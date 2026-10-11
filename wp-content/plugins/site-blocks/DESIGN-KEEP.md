# Safeguard button and surface map

This file is the reference for the pass that follows. The Next.js portal is a separate product and is not in this pass. `assets/css/main.css` is the old package sheet and is not in this pass.

## The set

- Page button: 48px pill, Montserrat 15px, no shadow.
- Card button: 40px pill, Montserrat 14px, no shadow. Class: `sg-btn sg-btn--sm`.
- Orange fill (`sg-btn--orange`): the sale. Quote, send a message, contact.
- Blue fill (`sg-btn--primary`): the other real action. See a system, open a product, open a case study.
- Navy outline (`sg-btn--secondary`): the phone, on a light background.
- White outline (`sg-btn--ghost`): the phone or a quiet action, on navy or a photo.
- Text button: a link you can skip. Not used for the phone or the quote.
- Cards and photos: 12px corners.
- Flat surface: hairline, no shadow.
- Raised card: `0 10px 30px rgb(12 45 68 / 8%)` via `--sg-shadow`.
- Overlay: menus, chat, and the sticky mobile bar. `0 16px 40px rgb(12 45 68 / 16%)` via `--sg-shadow-overlay`.
- Retired: soft-blue gradient, orange button glow, navy filled buttons, 8px / 14px / 0.85rem / 1.15rem corners, the black `--sg-shadow`.

## Tokens

`assets/css/safeguard-home.css` `:root`

- `--sg-radius` and `--sg-radius-lg` become 12px.
- `--sg-btn-radius` becomes 999px.
- `--sg-btn-height` becomes 48px. Small size is 40px.
- `--sg-shadow` becomes the raised-card shadow.
- `--sg-shadow-overlay` is added.

## Shared button CSS

`assets/css/safeguard-home.css`

- `.sg-btn`: pill, 48px, no shadow.
- `.sg-btn--sm`: 40px.
- `.sg-btn--orange` and `.sg-cta-phone`: drop the orange glow. Hover is a colour change.
- `.sg-btn--soft-blue`: same paint as solid blue, so any leftover gradient button cannot ship.
- `.sg-btn--secondary`: navy outline on white.
- Dark bands keep the existing white-outline override.
- `.sg-cta-card`: 12px corners. The tight breakpoint stops forcing 12px type.
- `.sg-header__mega-panel`: 12px corners, overlay shadow.
- `.sg-mobile-bar`: overlay shadow.

## Button roles

Quote and send become orange:

- `inc/hero-variants.php` hero primaries
- `inc/safeguard-ajax-hero.php` hero primary
- `inc/pillar-hero.php`, `inc/safeguard-stage-hero.php` already orange or switched with the hero primaries
- `blocks/manpower-hero/render.php`
- `blocks/electronic-security-hero/render.php`
- `blocks/ajax-alarm-systems-products/render.php`
- `blocks/ajax-alarm-systems-property-fit/render.php`
- `blocks/ajax-calculator-hero/render.php`
- `inc/akuvox-render.php`, `inc/paradox-m25-render.php` product CTAs
- `inc/safeguard-homepage-markup.php` hero and in-page quote buttons
- `inc/safeguard-footer.php` sticky bar quote
- `inc/safeguard-chrome.php` header Quote
- `blocks/contact-form/render.php` submit
- `inc/enterprise-render.php` quote buttons
- `inc/monitoring-render.php` quote button
- `templates/single-enterprise_insight.php`
- `inc/safeguard-sections.php` options-card quote
- `inc/about-page.php` hero quote
- `inc/hero-variants.php` contact hero Send a message
- `templates/single-case-study.php` Start a similar job (40px, it sits in the card)
- `templates/single-post.php` Send a message (40px)
- `blocks/cctv-spotlight/render.php` quote, swapped off the ghost style

Blue, the other action:

- `inc/safeguard-ajax-card.php` Explore Ajax
- `inc/safeguard-homepage-markup.php` the Ajax page button beside the quote

Navy outline, the phone on white:

- Sticky bar Call
- Enterprise quote phone on the white band
- CCTV spotlight phone
- About hero phone
- Contact hero Call us
- Existing `sg-btn--secondary` phone buttons pick this up from the shared CSS

White outline, left as ghost because the band is navy or a photo:

- Navy CTA card phone
- Dark homepage hero Help Me Choose
- Monitoring quote phone on the blue band
- Ajax quote panel phone

Help Me Choose stays an outline. It is not promoted to a filled blue button.

## Surfaces

12px corners, and the raised shadow where a card already has a shadow:

- `assets/css/about.css` proof card
- `assets/css/blog-post.css` image, contents, sidebar card
- `assets/css/case-study.css` photo, facts, brief
- `assets/css/safeguard-contact.css` facts card
- `assets/css/safeguard-ajax-hero.css` photo, caption, trust bar
- `assets/css/safeguard-home.css` homepage cards that used 0.7rem or the token
- Service stylesheets where a card, photo, or panel is square or uses 10px, 0.85rem, 1rem, or 1.15rem: Ajax, CCTV, alarms, access, monitoring, manpower, intercom, electronic security, Akuvox, Paradox, enterprise, related services

Left square on purpose: comparison tables, icon resets, nav underlines, 2px rules.

## Chat

`assets/css/safeguard-chat.css`

- Panel uses the overlay shadow.
- The launcher floats, so it keeps a shadow. It uses the same overlay shadow. It is the one button that is not flat, because it sits over the page.
