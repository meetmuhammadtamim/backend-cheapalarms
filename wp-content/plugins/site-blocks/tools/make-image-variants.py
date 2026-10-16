"""Make the smaller image variants the homepage serves to phones and small slots.

Source images are never edited. Each variant is a NEW file next to its source:
resized (aspect ratio kept) and re-encoded as WebP with the alpha channel kept.
Run from the plugin folder:  python tools/make-image-variants.py

Variants:
- hero/home-rack-{480,800,1100}.webp  responsive hero (srcset)
- why/safeguard-3d-400.webp, why/seal-400.webp   Why-card art is shown at ~200px
- brand/safeguard-logo.webp, safeguard-logo-footer.webp, safeguard-logo-mark.webp
  (the asset helper prefers a sibling .webp over the .png of the same name)
- {physical-security,enterprise}/hub-hero-640.webp   mega menu feature image
- {alarm,cctv,access-control,intercom}/hero-thumb.webp, monitoring/virtual-patrol-hero-thumb.webp
  mega menu 50px thumbnails
- brands/hanwha-vision.webp, brands/nx-witness.webp  (PNG logos, WebP siblings)
"""

import os
import sys

from PIL import Image

ROOT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "assets", "images")


def save(src_rel, dst_rel, width=None, height=None, quality=80, alpha_quality=90):
    src = os.path.join(ROOT, src_rel)
    dst = os.path.join(ROOT, dst_rel)
    im = Image.open(src)
    has_alpha = im.mode in ("RGBA", "LA") or "transparency" in im.info
    im = im.convert("RGBA" if has_alpha else "RGB")
    w, h = im.size
    if width:
        height = max(1, round(h * width / w))
    elif height:
        width = max(1, round(w * height / h))
    else:
        width, height = w, h
    if (width, height) != (w, h):
        im = im.resize((width, height), Image.LANCZOS)
    kwargs = dict(format="WEBP", quality=quality, method=6)
    if has_alpha:
        kwargs["alpha_quality"] = alpha_quality
    im.save(dst, **kwargs)
    print("%-52s %5dx%-5d %6.1f KB" % (dst_rel, width, height, os.path.getsize(dst) / 1024))


# Hero: the 1500px file stays the largest candidate.
for w in (480, 800, 1100):
    save("hero/home-rack.webp", "hero/home-rack-%d.webp" % w, width=w, quality=78, alpha_quality=88)

# Why cards (shown at about 112 to 200 CSS px).
save("why/safeguard-3d.webp", "why/safeguard-3d-400.webp", width=400, quality=82)
save("why/seal.webp", "why/seal-400.webp", width=400, quality=82)

# Logos: shown at 160 to 190 CSS px wide, so 2x is enough.
save("brand/safeguard-logo.png", "brand/safeguard-logo.webp", width=440, quality=80, alpha_quality=80)
save("brand/safeguard-logo-footer.png", "brand/safeguard-logo-footer.webp", width=438, quality=80, alpha_quality=80)
save("brand/safeguard-logo-mark.png", "brand/safeguard-logo-mark.webp", width=288, quality=80, alpha_quality=80)

# Mega menu: every image the header menu config names gets a 640px feature
# file and a 160px-high thumbnail (inc/safeguard-chrome.php picks the right one).
import re

chrome = open(os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "inc", "safeguard-chrome.php"), encoding="utf-8").read()
mega = sorted(set(re.findall(r"'image'\s*=>\s*'images/([^']+\.webp)'", chrome)))
for rel in mega:
    if rel.endswith("-640.webp") or rel.endswith("-thumb.webp"):
        continue
    if not os.path.exists(os.path.join(ROOT, rel)):
        print("skip (missing)", rel)
        continue
    save(rel, rel[:-5] + "-640.webp", width=640, quality=76)
    save(rel, rel[:-5] + "-thumb.webp", height=160, quality=76)

# PNG logos get WebP siblings (the helper serves the sibling automatically).
save("brands/hanwha-vision.png", "brands/hanwha-vision.webp", width=440, quality=80, alpha_quality=80)
save("brands/nx-witness.png", "brands/nx-witness.webp", width=256, quality=80, alpha_quality=80)
