"""Build the homepage stylesheet bundle.

Joins the self-hosted font rules and the homepage's plugin stylesheets, in the
order WordPress prints them today, into one minified file:

    assets/css/safeguard-home.bundle.css

The homepage then makes one stylesheet request instead of fifteen (see
inc/performance.php). The source files stay as they are and still load on
other pages. Re-run after editing any source stylesheet:

    python tools/build-home-css.py

Requires: pip install rcssmin
"""

import os
import re
import sys

import rcssmin

PLUGIN = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..")
CSS = os.path.join(PLUGIN, "assets", "css")

# Same order WordPress prints these handles on the homepage.
SOURCES = [
    "../fonts/safeguard-fonts.css",
    "main.css",
    "safeguard-home.css",
    "safeguard-process-flow.css",
    "safeguard-why.css",
    "safeguard-details.css",
    "safeguard-coverage.css",
    "safeguard-quality.css",
    "safeguard-connected.css",
    "safeguard-blog.css",
    "safeguard-process.css",
    "safeguard-brands.css",
    "safeguard-cases.css",
    "safeguard-motion.css",
    "safeguard-signature-icons.css",
    "safeguard-logo-marquee.css",
]


def version():
    main = open(os.path.join(PLUGIN, "site-blocks.php"), encoding="utf-8").read()
    m = re.search(r"define\(\s*'SITE_BLOCKS_VERSION',\s*'([^']+)'", main)
    return m.group(1) if m else "unknown"


def main():
    parts = []
    raw = 0
    for name in SOURCES:
        path = os.path.normpath(os.path.join(CSS, name))
        text = open(path, encoding="utf-8", errors="strict").read()
        raw += len(text.encode("utf-8"))
        parts.append(text)
        print("  %-34s %6.1f KB" % (os.path.basename(path), len(text.encode("utf-8")) / 1024))
    joined = "\n".join(parts)
    # Font URLs are relative to assets/css/, where the bundle lives.
    out = "/*! Safeguard homepage bundle %s (built by tools/build-home-css.py) */\n" % version() + rcssmin.cssmin(joined) + "\n"
    dst = os.path.join(CSS, "safeguard-home.bundle.css")
    open(dst, "w", encoding="utf-8", newline="\n").write(out)
    print("bundle: %.1f KB from %.1f KB" % (len(out.encode("utf-8")) / 1024, raw / 1024))


if __name__ == "__main__":
    sys.exit(main())
