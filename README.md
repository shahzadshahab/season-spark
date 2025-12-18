<!-- Banner: referenced image under assets/images/banner.png -->
[https://github.com/shahzadshahab/season-spark/blob/main/season-spark/assets/images/banner.png](https://github.com/shahzadshahab/season-spark/blob/main/season-spark/assets/images/banner.png)

# ✨ Season Spark

> Lightweight, accessible, and performance-minded site-wide holiday visual effects for WordPress.

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/)
[![License: GPLv2+](https://img.shields.io/badge/license-GPLv2%2B-brightgreen.svg)](https://www.gnu.org/licenses/)

---

## What it does

Season Spark provides decorative, site-wide particle effects and lightweight visual overlays (images/popups) for holidays and special occasions. Effects are rendered in aria-hidden canvas/overlay containers and never modify page semantics. The plugin is built to be modular: only enabled effects load their JavaScript to keep front-end impact minimal.

## Included effects

- 🎄 `christmas` — Snowfall & subtle twinkles
- 🎃 `halloween` — Ghosts & pumpkins (image-based) with a subtle fog overlay
- ❤️ `valentines` — Floating hearts + confetti
- 🎆 `newyear` — Occasional fireworks bursts (multicolor)
- 🐣 `easter` — Eggs & bunnies, plus occasional large egg drop popup
- 🍂 `thanksgiving` — Falling leaves + occasional turkey popup
- 🇺🇸 `independence` — Fireworks + occasional spark/flag bursts
- 🪔 `diwali` — Small lights/diya sparkles + occasional diya popup
- ✡️ `hanukkah` — Menorah sparkles + occasional menorah popup
- 🖼️ `generic` — Custom Graphics (rain/stars/bubbles modes or an uploaded animated background)

> Default activation on first install: only `valentines` is enabled.

## Key features (exact behavior implemented)

- Per-effect controls: `enabled`, `schedule` (optional `start` / `end` dates), `density` (0–200), `speed` (0.0–10.0), and `color` for effects that support color.
- `generic` (Custom Graphics): accepts `custom_bg` (background image URL) and `custom_cursor_image` via WP Media selectors; when `custom_bg` is provided, the plugin creates a lightweight animated background element in place of particle initialization.
- Custom Cursor: front-end creates an overlay cursor if any enabled effect has `custom_cursor` enabled; it prefers `custom_cursor_image`, then falls back to mapped bundled SVGs.
- Accessibility: respects `prefers-reduced-motion`, honors a site-level `motion_reduced` admin toggle, and exposes a per-user toggle (stored in `localStorage`) to disable motion.
- Performance: `tsparticles` is shipped in `assets/vendor/tsparticles/` and per-effect JS files under `assets/js/effects/` are enqueued only when an effect is enabled.
- Admin: Modern admin UI with `Effects`, `Settings`, and `For Devs` pages; media picker support for `generic` custom graphics; color picker shown only for effects that accept color.

## Installation

1. Upload the `season-spark` folder to `/wp-content/plugins/`.
2. Activate the plugin via the WordPress Plugins screen.
3. Configure under the Season Spark menu (Effects / Settings / For Devs).

## Usage examples

Enable an effect and optionally set `density`, `speed` or `color` on the Effects admin page. For scheduled effects, enable `Schedule` and set `start`/`end` (YYYY-MM-DD) to limit when the effect runs.

### Register a custom effect (PHP)

```php
add_filter('kss_get_registered_effects', function($effects){
    $effects['myeffect'] = 'My Effect';
    return $effects;
});
```

Then add `assets/js/effects/myeffect.js` that calls:

```javascript
window.kssRegisterEffect('myeffect', function(ts, id, cfg){
    // ts.load(id, {...}) — initialize particles for container id
});
```

### Filter settings or images sent to front-end

- `kss_settings_for_js` — filter the localized settings object (`kssSettings`).
- `kss_images_for_js` — filter the image map localized as `kssImages` (used for effect images and default cursor images).

## Front-end data shape

The front-end receives `kssSettings` with `global` and `effects` keys. Each effect object includes: `enabled`, `schedule` (0|1), `start`, `end`, `color`, `density`, `speed`, `custom_cursor` (0|1), `custom_bg`, and `custom_cursor_image` where applicable.

## Files of interest

- `season-spark.php` — plugin bootstrap & activation defaults
- `includes/class-kss-assets.php` — enqueues scripts/styles; localizes `kssSettings` and `kssImages`
- `includes/admin/class-kss-admin.php` — admin UI, settings registration, sanitization handlers
- `includes/public/class-kss-public.php` — prints aria-hidden effect containers and per-user motion toggle
- `assets/js/kss-public.js` — front-end initializer and `kssRegisterEffect` registry
- `assets/js/effects/*` — per-effect initializers (one file per effect)

## Banner image

Banner is included at the top of this README and is located at `assets/images/banner.png` in the plugin.

## Changelog

- 1.0.0 — Initial release: core effects, admin UI, per-effect options, accessibility-aware front-end initialization.

## License

This plugin is licensed under GPLv2 or later. See the `License` header in `season-spark.php` for details.

---

*Made with ✨ by the Season Spark team.*
# Season Spark

Season Spark adds lightweight site-wide visual holiday effects (snow, hearts, particles, fireworks, leaves, and more) with accessible controls and a modern admin UI. The plugin is intentionally lightweight — assets are bundled and only enabled effects load on the front-end.

## Features

- Christmas: snowfall with blue-tinted default flakes (now visible on light backgrounds)
- Halloween: ghosts and pumpkins with a subtle fog overlay for atmosphere
- Valentine's Day: floating hearts and confetti
- New Year & Independence Day: larger, multicolored fireworks that fade after bursting
- Easter: egg drops (uses `egg.svg`) with occasional larger drop animations
- Thanksgiving: falling leaves and occasional turkey popup
- Diwali: floating lights and occasional diya popup
- Hanukkah: menorah sparkles and occasional menorah popup
- Custom Graphics: upload background graphics and optional cursor images via the WP Media modal
- Per-effect controls: enable, density (0–200), speed (0.0–10.0), optional color (effects that support color), per-effect schedule (start/end dates)
- Per-effect `Custom Cursor` (uses SVGs mapped per effect or a custom uploaded cursor image)
- Accessibility: respects `prefers-reduced-motion` and provides a per-user motion toggle
- Developer-friendly filters: `kss_get_registered_effects`, `kss_settings_for_js`, `kss_images_for_js`

Cursor images (default mapping):

- `christmas` → `snowflake.svg`
- `halloween` → `ghost.svg`
- `valentines` → `heart.svg`
- `newyear` → `star.svg`
- `easter` → `bunny.svg`
- `thanksgiving` → `turkey.svg`
- `independence` → `spark.svg`
- `diwali` → `diya.svg`
- `hanukkah` → `menorah.svg`
- `generic` → `leaf.svg`

The front-end initializer (`kss-public.js`) picks the first enabled effect with `custom_cursor` enabled, prefers any per-effect uploaded cursor URL, then falls back to the mapped SVG.

## Custom Graphics (Generic)

The `Custom Graphics` effect lets you upload a background image and an optional custom cursor via the WordPress Media Library. These images are stored as URLs in the plugin settings and used to render a lightweight animated background overlay without adding heavy libraries.

## Installation

1. Upload the `season-spark` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure settings under the Season Spark menu in the admin sidebar.

Notes:

- Default activation: only `valentines` is enabled by default.
- All effect assets (JS and SVGs) are bundled under `assets/` to keep the plugin self-contained and WP.org-friendly.

## Developer Notes

- Register new effects using `kss_get_registered_effects` and provide a matching file under `assets/js/effects/` calling `kssRegisterEffect('yourkey', fn)`.
- Use `kss_images_for_js` and `kss_settings_for_js` filters to customize the image map and settings sent to the front-end.
- Front-end receives a `kssSettings` object localized by PHP. Effects should read their per-effect config from that object.

## Security & Performance

- The plugin avoids inline unescaped output in admin pages and sanitizes inputs stored in options.
- Particle canvases are set to `pointer-events: none` to avoid intercepting clicks on page elements.
- No external libraries are loaded unless explicitly enabled; `tsparticles` is bundled locally by default.

## FAQ

Will these effects affect my SEO?

No. Effects are decorative (aria-hidden) and rendered as canvas layers. They do not modify the page's semantic content.

How can I add a new effect?

Use the `kss_get_registered_effects` filter and add a JS file under `assets/js/effects/` that calls `kssRegisterEffect('yourkey', fn)`.

## Changelog

- 1.2.0 — Dec 2025
	- Custom Graphics: WP Media picker for background and cursor images, lightweight animated background overlay
	- Improved fireworks (New Year, Independence): multicolor, larger, and proper fade/destroy
	- Halloween: added subtle fog overlay
	- Easter: egg drop animation + larger occasional drops
	- Thanksgiving / Diwali / Hanukkah: occasional large popup elements (turkey, diya, menorah)
	- Custom cursor hotspot set to image top for more intuitive clicking
	- Canvas pointer-events fix so page links remain clickable at high particle speeds
	- Admin: color picker only shown for effects that use color; media picker for Custom Graphics
	- Security: escaping and sanitized admin outputs for WP.org review

- 1.1.0 — earlier
	- Admin UI refresh, per-effect custom cursors, schedule UI, image mapping, and developer docs

