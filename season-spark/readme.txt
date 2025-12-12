=== Season Spark ===
Contributors: Shahzad Shahab
Tags: holiday, snow, confetti, particles, fireworks, seasonal
Requires at least: 5.4
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Season Spark adds lightweight site-wide visual holiday effects (snow, hearts, fireworks, leaves, etc.) with accessible controls and a modern admin UI.

== Description ==
Season Spark (Seasonal Sparkle) is a lightweight WordPress plugin that adds tasteful, performant holiday visual effects across your site.
- Snowfall for Christmas
- Ghosts & pumpkins for Halloween
- Hearts & confetti for Valentine's Day
- Fireworks for New Year & Independence Day
- Eggs & bunnies for Easter
- Falling leaves for Thanksgiving
- Diwali lights & rangoli sparks
- Menorah candles for Hanukkah
- Generic modes: rain, stars, bubbles

Admin UI allows enabling/disabling effects, scheduling by date, customizing colors, density and speed. Accessibility support for prefers-reduced-motion and per-user toggle.

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
= 1.0.0 =
* Initial release.

== Upgrade Notice ==
N/A
