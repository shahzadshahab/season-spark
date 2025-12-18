=== Season Spark ===
Contributors: designsbyshahzad
Stable tag: 1.0.0
Author: Shahzad Shahab
Author URI: https://profiles.wordpress.org/designsbyshahzad/
Donate link: https://creatingbee.com
Tags: holiday, snow, particles, fireworks, seasonal
Requires at least: 5.4
Tested up to: 6.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Season Spark adds lightweight, accessible site-wide holiday visual effects with a modern admin UI.

== Description ==
Season Spark is a lightweight WordPress plugin that adds tasteful, performant holiday visual effects across your site. Key highlights:

- Snowfall, ghosts & pumpkins, hearts & confetti, fireworks, egg drops, falling leaves, Diwali lights, menorah sparkles and more.
- Custom Graphics mode: upload background and cursor images via the WP Media modal for a custom animated background.
- Per-effect scheduling, density, speed and optional color controls for effects that support color.
- Per-effect `Custom Cursor` support (uses SVGs or uploaded cursor images).
- Accessibility: respects `prefers-reduced-motion` and offers a per-user motion toggle.

Admin UI and developer features:

- Effects / Settings / For Devs admin pages with a modern, glassy UI.
- Per-effect controls, WP Media-based image picker for Custom Graphics, and helpful inline tips.
- Developer filters: `kss_get_registered_effects`, `kss_settings_for_js`, `kss_images_for_js`.

Notes:

- Default activation: only `valentines` enabled by default.
- All effect assets (JS and SVGs) are bundled under `assets/` for WP.org friendliness.

== Installation ==
1. Upload `season-spark` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure at the Season Spark menu in the admin sidebar.

== Frequently Asked Questions ==
= Will these effects affect my SEO? =
No. Effects are decorative (aria-hidden) and rendered as canvas layers. They do not modify the page's semantic content.

= How can I add a new effect? =
Use the filter `kss_get_registered_effects` to register a new key and add a corresponding JS file under `assets/js/effects/` that calls `kssRegisterEffect('yourkey', fn)`.

== Changelog ==
= 1.2.0 =
* Dec 2025
* Custom Graphics: WP Media picker for background and cursor images; lightweight animated background overlay
* Improved fireworks (New Year, Independence): multicolor, larger, and proper fade/destroy
* Halloween: subtle fog overlay
* Easter: egg drops + occasional large egg animation
* Thanksgiving / Diwali / Hanukkah: occasional large popups (turkey, diya, menorah)
* Custom cursor hotspot adjusted to top of image for intuitive clicking
* Canvas pointer-events fix so page links remain clickable at high particle speeds
* Admin: color picker limited to effects that use color; media picker for Custom Graphics
* Security: escaped admin outputs and sanitized inputs for WP.org review

= 1.1.0 =
* Admin UI refresh: tabs, glass buttons, inline controls.
* Per-effect custom cursor support and SVG mapping.
* Schedule toggle and improved date UI.
* Developer documentation tab and filters.

== Upgrade Notice ==
N/A
