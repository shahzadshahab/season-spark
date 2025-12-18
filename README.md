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

