# Photos for the live site. Do not install this through Plugins → Upload.
#
# Zip layout: safeguard/images/...
# Extract it inside wp-content/uploads/ so the result is:
#   wp-content/uploads/safeguard/images/...
#
# Upload with SFTP or the SSH tunnel. The site reads this folder before the plugin.

$ErrorActionPreference = 'Stop'

$pluginRoot = Split-Path -Parent $PSScriptRoot
$repoRoot   = (Resolve-Path (Join-Path $pluginRoot '..\..\..\..')).Path
$images     = Join-Path $pluginRoot 'assets\images'
$staging    = Join-Path $pluginRoot 'photos-zip-tmp'
$dest       = Join-Path $staging 'safeguard\images'
$zipPath    = Join-Path $repoRoot 'safeguard-photos.zip'

if (-not (Test-Path $images)) {
    throw "Missing assets/images at $images"
}

if (Test-Path $staging) {
    Remove-Item -Recurse -Force $staging
}

New-Item -ItemType Directory -Path $dest -Force | Out-Null
robocopy $images $dest /E /NFL /NDL /NJH /NJS /nc /ns /np | Out-Null
if ($LASTEXITCODE -ge 8) {
    throw "robocopy failed with exit code $LASTEXITCODE"
}

if (Test-Path $zipPath) {
    Remove-Item -Force $zipPath
}

Push-Location $staging
try {
    tar -a -cf $zipPath safeguard
} finally {
    Pop-Location
}

Remove-Item -Recurse -Force $staging

$entries = tar -tf $zipPath
$sample = $entries | Where-Object { $_ -like 'safeguard/images/*' } | Select-Object -First 1
if (-not $sample) {
    throw 'Zip verification failed: safeguard/images/ is missing.'
}

$sizeMb = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host ""
Write-Host "OK - photo pack ready ($sizeMb MB)" -ForegroundColor Green
Write-Host "  $zipPath"
Write-Host "  Extract inside wp-content/uploads/  (not inside the plugin)"
Write-Host ""
