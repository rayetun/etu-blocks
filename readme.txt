=== Etu Blocks – Accessible Blocks & Page Builder for the Block Editor ===
Contributors:      rayetun
Donate link:       https://wise.com/pay/me/mdrayhanu2
Tags:              page builder, blocks, gutenberg blocks, block editor, accessibility
Requires at least: 6.7
Tested up to:      7.1
Requires PHP:      7.4
Stable tag:        1.0.1
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Build pages everyone can use. Accessible, interactive page builder blocks for the block editor, built to WCAG 2.2 AA.

== Description ==

🧱 **Etu Blocks is a focused, accessible block library — and all of it is free.**

**1. Accessible by construction.** Every interactive block is built keyboard-operable and screen-reader-correct *from the first line of code*, against **WCAG 2.2 Level AA** — real dialog semantics, real range inputs, real focus management, real ARIA state that can't drift from what you see. Accessibility is a merge gate, not a polish pass.

**2. Interactive without bloat.** Front-end behaviour runs on the native **WordPress Interactivity API** — no jQuery, no framework runtime, no build system on your visitors' pages. Each block's script loads only where it's used, and the content is server-rendered first, so it works even before that script loads — or if it never does.

Etu Blocks does not reinvent core blocks — it builds only what core has no equal for, held to a premium bar: shared design tokens, a schema-driven Inspector every block inherits, and multiple presets per block.

**This plugin is free, in full.**

No locked panels, no "upgrade to unlock", no nag screens, no paywall — every block and every control described here works for everyone.

An **Etu Blocks Pro** add-on with additional premium blocks may follow, but nothing in this free plugin will move behind a paywall.

= How Etu Blocks is organised =

* **Accessible by default** — WCAG 2.2 AA is the definition of done
* **Interactivity API only** — server-rendered first, no framework runtime on visitors' pages
* **Privacy first** — video and map embeds use a click-to-load facade; no telemetry, no phone-home

Etu Blocks ships 29 blocks across Content, Marketing, Interactive, Data, and Media groups. Every block is browsable in the editor inserter, with a full description and preview shown inline as you insert it.

---

= 🧱 The Blocks =

**Content**

* ❓ **FAQ / Accordion** — the disclosure pattern, done right: each header is a real `<button>` inside a heading, `aria-expanded` reflects state, the panel is a `<region>` labelled by its button, and closed panels are `hidden` (not just clipped). Multiple presets, and an optional FAQPage JSON-LD schema for SEO.
* 🗂️ **Tabs** — a full **ARIA Tabs** implementation: `role="tablist"` / `role="tab"` / `role="tabpanel"`, single-tab-stop keyboard navigation with arrow keys, Home/End, and roving `tabindex`. Built to the pattern so a future core Tabs block is a trivial transform away.
* 💡 **Info Box** — icon + heading + text callout with premium presets. The icon is decorative (`aria-hidden`) when text carries the meaning, so screen readers aren't read a decorative glyph.
* 🎯 **Banner** — a premium hero: background colour or image with a colour overlay, headline, text, and up to two buttons, with height, alignment, and content-width controls. Buttons render as real `<a>` links when a URL is set, or focusable `<button>` elements when not — never inert spans.
* 📢 **Call to Action** — heading, text, and buttons in a bold section, with the same real-link-or-real-button rule as Banner.
* 💬 **Testimonial** — quote card with avatar and citation, on semantic `<figure>` / `<blockquote>` / `<figcaption>`. Multiple presets.
* 👥 **Team Member** — profile card with Card, Overlay, Horizontal, and Minimal presets. Social links are real `<a>` elements with accessible names, never icon-only.
* 📋 **Icon List** — feature or checklist list with per-item icons. Real `<ul>` semantics — a screen reader announces "list, 5 items".

**Marketing**

* 💳 **Pricing Table** — premium plan cards with a featured plan, feature lists, and an optional monthly/annual billing toggle. The toggle is a keyboard-operable radio group (not a fake switch), and the recommended plan is marked with a text badge — never colour alone.
* ⚖️ **Comparison Table** — compare plans or products side-by-side, with a highlighted column, check/cross/text cells, and a real `<table>` with `<th scope>` headers so the row/column relationships are announced correctly.
* 🖼️ **Post Grid** — display posts as a grid, masonry, or carousel with full query controls (post type, categories, order, offset, exclude-current), premium card presets (Elevated, Bordered, Minimal, Overlay), an optional category filter bar, and carousel controls that respect reduced motion.
* 🏷️ **Taxonomy** — a visual term browser for *any* public taxonomy: image-card grid, list, or pill layout. Each card uses the term's own image or the latest post's featured image as a universal fallback, cached in a transient so it isn't re-queried on every page load.

**Interactive**

* 🪟 **Modal / Popup** — an accessible dialog: `role="dialog"` + `aria-modal`, focus trapped while open and restored to the trigger on close, Esc closes, backdrop dismiss is opt-in, body scroll is locked, and the background is made `inert` so assistive tech and keyboard focus can only reach the dialog. Opens on click, on page load, or after a delay; holds any blocks.
* 🎬 **Video Popup** — a video lightbox on the same accessible dialog recipe, with a **privacy-first click-to-load facade**: the YouTube (via `youtube-nocookie.com`), Vimeo (with `dnt=1`), or self-hosted video is requested **only when the visitor opens it** — no third-party requests, cookies, or autoplay on page view — and closing actually stops playback. Focus is moved in and returned.
* 🎠 **Slides / Carousel** — put any blocks in each slide; a genuine keyboard-scrollable region (nothing hidden from screen readers), with real prev/next and dot controls, arrows, opt-in autoplay that ships a real pause button and never runs under reduced motion.
* 🔀 **Before / After** — an image comparison you can operate with the **keyboard**, not just the mouse. Built on a real `<input type="range">`, so arrow keys move the divider, dragging works, and the position is announced as a percentage via `aria-valuetext`. Horizontal or vertical.
* ⏳ **Countdown** — a countdown to a fixed date or an evergreen per-visitor duration. The ticking digits are **hidden from screen readers**, so a screen-reader user isn't read a new number every second, while a `role="timer"` container carries a descriptive label and the end is announced once via a polite live region.
* 🔍 **Post Filter** — a filterable post grid: a search field, taxonomy dropdowns, and a reset button. Filtering happens live over a REST endpoint (`etu/v1/posts`) — but the bar is a real GET `<form>` and Load-more is a real link, so **everything works with JavaScript disabled**. Card and Overlay presets, with an inline SVG placeholder for posts that have no featured image.
* 🧭 **Table of Contents** — auto-built from your headings, as a real `<nav>` landmark. Multiple presets, an optional collapse, sticky positioning, smooth scroll with reduced-motion respect, and active-section highlighting as you scroll.
* 🔗 **Social Share** — accessible share buttons (social networks, copy link, native device share) on real `<a>` / `<button>` elements with accessible names, powered by the Interactivity API.
* 🔔 **Notice** — dismissible callout, inline or as a fixed top/bottom announcement bar. Persistent dismissal via `localStorage` so it doesn't reappear on the next page load.
* 🔢 **Counter** — an animated number that counts up when it scrolls into view. The final value is present in the DOM immediately for search engines and no-JS visitors; under reduced motion the animation is skipped and the final value stays put.
* 📈 **Progress Bar** — labelled progress meter with `role="progressbar"` and `aria-valuenow`, animated to its target when scrolled into view, still under reduced motion.
* ⭐ **Star Rating** — an accessible rating display, exposed to screen readers as "Rated 4.5 out of 5" rather than five unlabelled symbols.
* 👣 **Steps** — numbered process list.
* 📅 **Timeline** — dated vertical timeline.

**Data**

* 📊 **Data Table** — a real, optionally sortable `<table>` with a `<caption>`, `<th scope="col">` headers, sort changes announced via `aria-sort`, live search with a polite result count, sticky header, striped/bordered/compact styles, and a focusable scroll region for narrow screens (or a stack-to-cards mode).

**Media & integrations**

* 📍 **Google Map** — a **keyless** Google Maps embed (no API key to configure or leak) with a privacy-first click-to-load facade, a floating info card with title + address, and a keyless "Get directions" link. Road / Satellite / Hybrid / Terrain, aspect ratios, and an optional colour-on-hover grayscale.
* 🎞️ **Lottie** — plays a Lottie animation with autoplay, on-scroll, hover/focus, or click triggers. Supports both `.json` and `.lottie` (dotLottie) — the archive is fetched and unzipped in the browser. Player is **bundled**, not loaded from a CDN. Reduced motion suppresses auto triggers and leaves a still first frame; hover and click stay available. Uploads: a small helper class lets logged-in editors upload `.json` and `.lottie` files to the Media Library (safely — a non-JSON file smuggled as `.json` is rejected).

Every dot, arrow, close button, and toggle meets the WCAG 2.2 24×24px target-size minimum.

---

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/etu-blocks/`, or install via **Plugins → Add New**.
2. Activate Etu Blocks through the **Plugins** screen.
3. Add any Etu block from the inserter — look for the **Etu Blocks** category.
4. Open the **Etu Blocks** menu item in wp-admin to enable/disable individual blocks or check usage.

No configuration is required to get started.

== Frequently Asked Questions ==

= ♿ Are the blocks really accessible, or is that a marketing line? =

Really. Every interactive block is built to a documented ARIA recipe from the start — real dialog semantics, real range inputs, real ARIA state bound to the Interactivity store so what you see cannot drift from what a screen reader hears. Accessibility is a merge gate: a block that doesn't meet WCAG 2.2 AA and its pattern's keyboard behaviour does not ship.

= 📏 Which WCAG version do you target? =

WCAG 2.2 Level AA — the version cited by EN 301 549 (European Accessibility Act) as it converges on 2.2, and a superset of the WCAG 2.1 AA cited by ADA Title II rules.

= ⚡ Is it fast? What is it built on? =

Built the modern WordPress way, on official core APIs: the **Interactivity API** for front-end behaviour, the **Block API v3** (`block.json`), and **server-side rendering**. So there is no jQuery and no extra framework runtime on your visitors' pages, and each block's script module loads only where it is actually used. Every block is rendered on the server and only *enhanced* by a small view module, so the content works even before that script loads — or if it never does.

= 🎨 Does it work with my theme? =

Yes. Blocks inherit your theme's colours, spacing, and typography via `theme.json` / global styles before adding their own controls. Block themes and classic themes are both supported.

= 🔒 Does Etu Blocks send my data anywhere? =

No. The plugin itself sends nothing from your server. The only third-party requests come from the Video Popup and Google Map blocks — and only in a visitor's browser, and only when they click Load. See the **External Services** section below for the full list.

= 🎯 Why unregister a block when I disable it, instead of just hiding it? =

So the choice is deliberate. If a disabled block still rendered on pages that use it, you could ship a page relying on a block that "isn't installed" and not realise. Unregistering makes the impact visible: any existing usage shows the standard "block not found" placeholder, and the Dashboard shows the usage count next to the toggle *before* you flip it — so you can see who's affected first.

= 🛒 Do any of the blocks need WooCommerce or another plugin? =

No. All 29 blocks work on a stock WordPress install with no other plugins required.

= 🧩 Is there a Pro version? =

An Etu Blocks Pro add-on is planned but not yet released — it would add premium blocks (subscribe form, feeds, WooCommerce components, template kits) on top of what's here, never move existing features behind a paywall. This free plugin is fully functional on its own.

= ♻️ What happens if I uninstall Etu Blocks? =

The plugin removes its own options (the disabled-blocks list, the usage-count cache) on uninstall. Your posts, pages, and media are never touched. Existing content that used Etu blocks will show the standard "block not found" placeholder until the plugin is re-activated.

= 💬 Get Support =

Post in the [WordPress.org support forum](https://wordpress.org/support/plugin/etu-blocks/).

== Screenshots ==

1. Dashboard — search, filter by type, enable/disable per block with usage counts and a light/dark toggle.
2. Post Filter — live search + taxonomy filter over a responsive card grid, with the Overlay preset.
3. Post Grid — masonry, carousel, and premium card presets (Elevated, Bordered, Minimal, Overlay).
4. Video Popup — the privacy-first facade before load, and the accessible dialog after.
5. Google Map — the click-to-load facade with the info card and directions button.
6. Before / After — the image comparison slider with keyboard focus on the handle.
7. Modal / Popup — the accessible dialog with focus trap, Esc-to-close, and backdrop dismiss.
8. Tabs — the ARIA Tabs pattern with roving keyboard focus.
9. Pricing Table — plan cards with the monthly / annual radio toggle.
10. Data Table — sortable columns, live search, sticky header, and responsive stacking.

== Changelog ==

= 1.0.1 =

Listing and metadata update. No changes to block behaviour, markup or output.

* Change: Plugin name now states what the plugin does — "Accessible Blocks & Page Builder for the Block Editor".
* Change: Readme tags and short description rewritten for clarity.
* Fix: A repository-only config file (`.gitattributes`) is no longer included in the distributed plugin.

= 1.0.0 =

Initial release. 29 accessible blocks, powered by the WordPress Interactivity API.


**Framework & dashboard**

* New: Schema-driven Inspector — every block declares one PHP schema that drives its controls, its attributes, and the CSS it generates. One source, no drift.
* New: Live CSS preview in the editor — the same generator that renders on the front end also renders as you edit.
* New: Responsive controls — every length control can be split per device (desktop / tablet / mobile).
* New: SaaS-style dashboard — search, filter by type, enable/disable per block with usage counts, light/dark theme toggle, support banner.
* New: Full internationalization — every user-facing string translatable via the standard `__()` / `_x()` / `_n()` calls; `.pot` shipped in `languages/`.
* New: REST endpoint `etu/v1/posts` — powers the Post Filter block's live filtering, with strict parameter clamping (post type must be public, per-page ≤ 24, taxonomies must belong to the post type, slugs sanitised). Only ever exposes published posts of public post types.
* New: Privacy-first facades for Google Maps and Video Popup — no third-party request until the visitor opts in.

== Upgrade Notice ==

= 1.0.1 =
Listing and metadata update only — no changes to blocks or output.

= 1.0.0 =
First public release of Etu Blocks.

== External Services ==

Etu Blocks itself sends no data anywhere. There is no telemetry, no account, and no phone-home. However, several blocks *can* embed third-party content — and only then, and only in a visitor's browser:

**Google Maps — Google Map block (only when the map is loaded)**
When a page contains an Etu Google Map block, the visitor's browser can load a map from Google Maps (`https://maps.google.com/maps`) and, when the "Get directions" button is used, open Google Maps' directions (`https://www.google.com/maps/dir/`). The block ships a **privacy-first click-to-load facade** by default, so no request is made to Google until the visitor clicks "Load map". A site owner can turn this facade off per block to load the map on page view instead.
Data sent: the location (address or place name) the site owner typed into the block, plus what the visitor's browser normally sends to any embedded iframe (IP address, user agent, cookies).
Sent when: only when the visitor clicks "Load map" (default), or on page view if the site owner disabled the facade. A page with no Map block contacts no external service.
Service URL: https://maps.google.com
Terms of Service: https://cloud.google.com/maps-platform/terms
Privacy Policy: https://policies.google.com/privacy

**YouTube — Video Popup block (only when a visitor plays a YouTube video)**
When a Video Popup block points to a YouTube URL, the video is loaded from the privacy-enhanced host `https://www.youtube-nocookie.com/` — but **only when the visitor clicks Play**, never on page load. Closing the popup clears the iframe so playback stops entirely.
Data sent: the video ID the site owner entered, plus what the visitor's browser normally sends to an embedded iframe.
Sent when: only when a visitor opens a Video Popup that points at a YouTube URL.
Service URL: https://www.youtube.com
Terms of Service: https://www.youtube.com/t/terms
Privacy Policy: https://policies.google.com/privacy

**Vimeo — Video Popup block (only when a visitor plays a Vimeo video)**
When a Video Popup block points to a Vimeo URL, the video is loaded from `https://player.vimeo.com/` with the "do not track" (`dnt=1`) flag set — but **only when the visitor clicks Play**, never on page load.
Data sent: the video ID the site owner entered, plus what the visitor's browser normally sends to an embedded iframe.
Sent when: only when a visitor opens a Video Popup that points at a Vimeo URL. A self-hosted video file, or a popup no visitor opens, contacts no external service.
Service URL: https://vimeo.com
Terms of Service: https://vimeo.com/terms
Privacy Policy: https://vimeo.com/privacy

**Author-supplied Lottie animation URLs — Lottie block (only when the site owner uses a remote URL)**
If a site owner enters a Lottie animation URL from any host (for example `https://lottie.host/`), the visitor's browser fetches that `.json` or `.lottie` file directly from that host to render the animation. Uploading a `.json` or `.lottie` file to the site's Media Library instead avoids any third-party request.
Data sent: nothing beyond the URL request itself and normal browser headers.
Sent when: only when a page contains a Lottie block configured with a remote URL, and only for that URL. Local uploads contact no external service.
Service URL: whichever host the site owner chose (e.g. https://lottie.host)
Terms of Service and Privacy Policy: those of the chosen host.

**Social share destinations — Social Share block (only when a visitor clicks a share button)**
The Social Share block renders a row of buttons as **plain outbound links** to each network's public share URL. Nothing is sent unless a visitor clicks one — the block does not embed any third-party content or make any request on page load. Which networks appear is controlled by the site owner.
Data sent: the URL and title of the page being shared, in the destination network's public share URL scheme.
Sent when: only when a visitor clicks the corresponding button, in their own browser, opening the network's share dialog in a new tab.
Networks and their share endpoints:
* Facebook — https://www.facebook.com/sharer/sharer.php — https://www.facebook.com/terms.php — https://www.facebook.com/privacy/policy/
* X (Twitter) — https://twitter.com/intent/tweet — https://x.com/en/tos — https://x.com/en/privacy
* LinkedIn — https://www.linkedin.com/sharing/share-offsite/ — https://www.linkedin.com/legal/user-agreement — https://www.linkedin.com/legal/privacy-policy
* Pinterest — https://pinterest.com/pin/create/button/ — https://policy.pinterest.com/en/terms-of-service — https://policy.pinterest.com/en/privacy-policy
* Reddit — https://www.reddit.com/submit — https://www.redditinc.com/policies/user-agreement — https://www.reddit.com/policies/privacy-policy
* WhatsApp — https://api.whatsapp.com/send — https://www.whatsapp.com/legal/terms-of-service — https://www.whatsapp.com/legal/privacy-policy
* Telegram — https://t.me/share/url — https://telegram.org/tos — https://telegram.org/privacy

