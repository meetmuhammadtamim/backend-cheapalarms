#!/usr/bin/env python3
"""
Sync inc/mdi-icons.php with the official Material Design Icons package.

Icon paths must come from the package (@mdi/svg, by Pictogrammers), never be
typed by hand. To add or change an icon: edit MAP below, then run this script.

    python tools/sync-mdi-icons.py            report only
    python tools/sync-mdi-icons.py --write    rewrite the icon array in the PHP file

Browse icons: https://pictogrammers.com/library/mdi/
"""
import re
import sys
import urllib.error
import urllib.request
from pathlib import Path

VERSION = "7.4.47"
URL = "https://cdn.jsdelivr.net/npm/@mdi/svg@%s/svg/%s.svg"
PHP = Path(__file__).resolve().parent.parent / "inc" / "mdi-icons.php"

# site icon key -> MDI icon name
MAP = {
    "id-card": "card-account-details-outline", "fingerprint": "fingerprint", "award": "medal-outline",
    "clock": "clock-outline", "wrench": "wrench", "radio-tower": "radio-tower", "bell-ring": "bell-ring-outline",
    "bell": "bell-outline", "cctv": "cctv", "sun": "white-balance-sunny", "phone": "phone-outline",
    "mail": "email-outline", "router": "router-wireless", "smartphone": "cellphone",
    "shield-check": "shield-check-outline", "shield": "shield-outline", "eye": "eye-outline",
    "calendar-clock": "calendar-clock-outline", "calendar": "calendar-blank-outline",
    "clipboard-list": "clipboard-list-outline", "signal": "signal-cellular-3", "warehouse": "warehouse",
    "hard-hat": "hard-hat", "car": "car-outline", "building-2": "office-building-outline",
    "store": "store-outline", "building": "domain", "briefcase": "briefcase-outline",
    "heart-pulse": "heart-pulse", "chevron-right": "chevron-right", "chevron-down": "chevron-down",
    "chevron-left": "chevron-left", "arrow-right": "arrow-right", "arrow-top-right": "arrow-top-right", "wifi": "wifi", "cloud": "cloud-outline",
    "layout-grid": "view-grid-outline", "cable": "cable-data", "cpu": "chip", "radio": "antenna",
    "circle-dollar-sign": "currency-usd", "user-cog": "account-cog-outline",
    "lock": "lock-outline", "key": "key-outline", "mic": "microphone-outline", "map": "map-outline",
    "scan-eye": "motion-sensor", "cloud-rain": "weather-rainy", "map-pin": "map-marker-outline",
    "video": "video-outline", "headset": "headset", "moon": "weather-night", "house": "home-outline",
    "lock-smart": "lock-smart", "arrow-up-circle": "arrow-up-circle-outline", "pen-tool": "pencil-ruler",
    "clipboard-check": "clipboard-check-outline", "message-square": "message-outline",
    "camera": "camera-outline", "tag": "tag-outline", "user-check": "account-check-outline",
    "globe": "earth", "network": "lan", "cloud-download": "cloud-download-outline", "bar-chart-3": "chart-bar",
    "check": "check", "circle-check": "check-circle", "monitor": "monitor", "refresh-cw": "refresh",
    "badge-check": "check-decagram-outline", "calculator": "calculator", "file-text": "file-document-outline",
    "user": "account-outline", "users": "account-group-outline", "server": "server", "speaker": "speaker",
    "intercom": "doorbell-video", "settings": "cog-outline", "target": "target", "zap": "flash-outline",
    "battery-charging": "battery-charging-outline", "info": "information-outline", "tractor": "tractor",
    "fence": "fence", "x": "close", "send": "send-outline", "message-circle": "chat-outline",
    "star": "star-outline", "construction": "traffic-cone", "shopping-bag": "shopping-outline",
    "siren": "alarm-light-outline", "layers": "layers-outline", "graph": "graph-outline",
    "swap": "swap-horizontal", "lens": "camera-iris", "pulse": "pulse", "link": "link-variant",
    "linkedin": "linkedin", "twitter": "twitter", "facebook": "facebook", "instagram": "instagram",
    "youtube": "youtube", "alarm-panel": "alarm-panel-outline", "keypad": "dialpad", "search": "magnify",
    "plus": "plus", "minus": "minus", "arrow-left": "arrow-left", "external-link": "open-in-new",
    "copy": "content-copy", "play": "play", "whatsapp": "whatsapp", "menu": "menu",
}


def fetch_path(name):
    try:
        with urllib.request.urlopen(URL % (VERSION, name), timeout=20) as r:
            svg = r.read().decode("utf-8")
    except urllib.error.HTTPError:
        return None
    m = re.search(r'\bd="([^"]+)"', svg)
    return m.group(1) if m else None


def main():
    write = "--write" in sys.argv
    lines, missing = [], []
    for key, mdi in MAP.items():
        d = fetch_path(mdi)
        if d is None:
            missing.append("%s (mdi:%s)" % (key, mdi))
            continue
        lines.append("\t\t'%s' => '%s', // mdi:%s" % (key, d, mdi))
    print("icons fetched: %d of %d" % (len(lines), len(MAP)))
    if missing:
        print("NOT FOUND in @mdi/svg@%s: %s" % (VERSION, ", ".join(missing)))
        return 1
    if write:
        src = PHP.read_text(encoding="utf-8")
        pat = re.compile(r"(function site_blocks_icon_paths\(\): array \{\n\treturn array\(\n)(.*?)(\n\t\);\n\})", re.S)
        if not pat.search(src):
            print("could not find the icon array in", PHP)
            return 1
        PHP.write_text(pat.sub(lambda m: m.group(1) + "\n".join(lines) + m.group(3), src, count=1), encoding="utf-8")
        print("written:", PHP)
    return 0


if __name__ == "__main__":
    sys.exit(main())
