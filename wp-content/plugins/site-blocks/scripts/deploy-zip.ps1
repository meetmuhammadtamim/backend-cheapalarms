# WordPress plugin upload zip — layout MUST be: site-blocks/site-blocks.php
# Do NOT zip the inner files only (that breaks WP and causes "Plugin file does not exist").
#
# Usage:
#   .\deploy-zip.ps1 -CodeOnly    # live upload. PHP/CSS/JS only. This is the zip for WP Admin.
#   .\deploy-zip.ps1              # full package — do not upload this in WP Admin
#   .\photos-zip.ps1              # photos only, for wp-content/uploads/safeguard/
#
# WP Admin plugin upload copies the zip into uploads, deletes the plugin folder, then
# extracts. A zip with photos fills the disk and the install hangs or fails.
# Photos go by SFTP into wp-content/uploads/safeguard/ (see photos-zip.ps1).
# The site serves that folder first, so a later code-only plugin upload does not remove them.

param(
    [switch]$CodeOnly
)

$ErrorActionPreference = 'Stop'

$pluginRoot = Split-Path -Parent $PSScriptRoot
$pluginsDir = Split-Path -Parent $pluginRoot
# Repo root (headless-cheapalarms) — always overwrite this path so the zip lands in the same place every time
$repoRoot     = (Resolve-Path (Join-Path $pluginRoot '..\..\..\..')).Path
$staging      = Join-Path $pluginsDir 'site-blocks-zip-tmp'
$wrapDir      = Join-Path $staging 'site-blocks'
$zipPath      = Join-Path $pluginRoot 'deploy\site-blocks-wp-upload.zip'
$zipCopy      = Join-Path $pluginsDir 'site-blocks.zip'
$zipCanonical = Join-Path $repoRoot 'site-blocks.zip'

if (Test-Path $staging) {
    Remove-Item -Recurse -Force $staging
}

New-Item -ItemType Directory -Path $wrapDir -Force | Out-Null
New-Item -ItemType Directory -Path (Split-Path $zipPath) -Force | Out-Null

$excludeDirs = @(
    'node_modules', 'site-blocks-zip-tmp', 'site-blocks-deploy', 'deploy', 'scripts', 'src'
)
if ($CodeOnly) {
    $excludeDirs += 'images'
}

$xdArgs = @()
foreach ($d in $excludeDirs) {
    $xdArgs += '/XD'
    $xdArgs += $d
}

$xfArgs = @(
    '/XF', 'site-blocks.zip', '*.log', 'package.json', 'package-lock.json',
    'postcss.config.js', 'tailwind.config.js', '.gitignore',
    'solar-use-cases-banner.png', 'solar-use-cases-split.png', 'hub-commercial.webp'
)

& robocopy $pluginRoot $wrapDir /E @xdArgs @xfArgs /NFL /NDL /NJH /NJS /nc /ns /np

if ($LASTEXITCODE -ge 8) {
    throw "robocopy failed with exit code $LASTEXITCODE"
}

if ($CodeOnly) {
    $imagesInZip = Join-Path $wrapDir 'assets\images'
    if (Test-Path $imagesInZip) {
        Remove-Item -Recurse -Force $imagesInZip
    }
    New-Item -ItemType Directory -Path $imagesInZip -Force | Out-Null
}

$mainFile = Join-Path $wrapDir 'site-blocks.php'
if (-not (Test-Path $mainFile)) {
    throw "Missing site-blocks.php in staging folder - zip would be invalid for WordPress."
}

if (Test-Path $zipPath) {
    Remove-Item -Force $zipPath
}

Push-Location $staging
try {
    tar -a -cf $zipPath site-blocks
} finally {
    Pop-Location
}

Remove-Item -Recurse -Force $staging

$entries = tar -tf $zipPath
$mainInZip = $entries | Where-Object { $_ -eq 'site-blocks/site-blocks.php' }
if (-not $mainInZip) {
    throw "Zip verification failed: site-blocks/site-blocks.php not found at archive root."
}

$nested = $entries | Where-Object { $_ -match '^site-blocks/site-blocks/' }
if ($nested) {
    throw "Zip verification failed: double-nested site-blocks/site-blocks/ detected."
}

Copy-Item -Force $zipPath $zipCopy
Copy-Item -Force $zipPath $zipCanonical

$sizeMb = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
$mode   = if ($CodeOnly) { 'CODE-ONLY (no images)' } else { 'FULL (code + images)' }

Write-Host ""
Write-Host "OK - WordPress plugin zip ready ($sizeMb MB) [$mode]" -ForegroundColor Green
Write-Host "  Canonical (same every time): $zipCanonical"
Write-Host "  Also: $zipPath"
Write-Host "  Also: $zipCopy"
Write-Host ""
Write-Host "Prefer SFTP/rsync to Coolify plugins bind-mount over WP Admin upload." -ForegroundColor Yellow
if ($CodeOnly) {
    Write-Host "Photos are not in this zip. They belong in wp-content/uploads/safeguard/ (safeguard-photos.zip)." -ForegroundColor Yellow
}
