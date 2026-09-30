# Etu Blocks

**Interactive, accessible blocks for the WordPress editor.**

Lightweight Gutenberg blocks powered by the [Interactivity API](https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/) and built to WCAG 2.2 AA. No page-builder lock-in.

- **WordPress.org:** https://wordpress.org/plugins/etu-blocks/
- **Requires WordPress:** 6.7+ · **Requires PHP:** 7.4+
- **License:** GPL-2.0-or-later

## Blocks

Banner, Before / After, Comparison Table, Countdown, Counter, Call to Action, Data Table, FAQ, Icon List, Info Box, Lottie, Google Map, Modal, Notice, Post Filter, Post Grid, Pricing Table, Progress Bar, Slides, Social Share, Star Rating, Steps, Tabs, Taxonomy, Team Member, Testimonial, Timeline, Table of Contents, Video Popup.

## Development

The unminified source lives in `src/`; the compiled runtime in `build/` is committed so it ships as-is.

```bash
npm install      # install dev dependencies
npm run build    # rebuild build/ from src/
npm run start    # watch mode
```

## Releasing

Deployment to WordPress.org is automated via GitHub Actions (see `.github/workflows/`):

- **Assets** — pushing changes under `.wordpress-org/` (icon, banner, screenshots) updates the WordPress.org listing art automatically. No version change, so users are not prompted to update.
- **Code** — publishing a GitHub **release** deploys that version to WordPress.org (a new SVN tag + trunk). Bump the `Stable tag` in `readme.txt` and `Version` in `etu-blocks.php` first. The workflow skips the deploy if the version already exists on SVN.

Both workflows require the `SVN_USERNAME` and `SVN_PASSWORD` repository secrets (a WordPress.org account with commit access).

## Directory assets

`.wordpress-org/` holds the WordPress.org plugin-directory art:

| File | Purpose |
|------|---------|
| `icon.svg` / `icon-256x256.png` / `icon-128x128.png` | Plugin icon (animated SVG + PNG fallbacks) |
| `banner-1544x500.png` / `banner-772x250.png` | Plugin banner (retina + standard) |
| `screenshot-1.png` … `screenshot-10.png` | Screenshots, in the order of the `== Screenshots ==` list in `readme.txt` |
