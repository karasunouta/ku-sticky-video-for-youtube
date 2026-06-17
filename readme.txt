=== KU Sticky Video for YouTube ===
Contributors: karasunouta
Donate link: https://karasunouta.com/
Tags: youtube, video, sticky, scroll, floating
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Make YouTube video player in posts follow the scroll position, showing in the corner of the page.

== Description ==

KU Sticky Video for YouTube is a lightweight WordPress plugin that automatically makes YouTube video players inside post content follow the scroll position, showing in the corner of the page when the original video scrolls out of view.

This plugin contains minified JavaScript for performance. The complete, unminified source code is publicly available on GitHub:
https://github.com/karasunouta/ku-sticky-video-for-youtube

=== Build Instructions ===
To rebuild the minified assets, run the following commands in the plugin directory:
1. `npm install` (to install build dependencies)
2. `npm run build` (to compile the source files using `@wordpress/scripts`)

=== Features ===
* Float YouTube video player automatically when it scrolls out of view.
* Choose display position: Bottom Right or Bottom Left.
* Flexible trigger options: float only when playing or float always (scroll follow).
* Hide the floating player when scrolling above the original video position.
* Customize video width using either pixel (px) or percentage (%) units.
* Set maximum width limits for percentage-based sizing (keep within original video width or set custom pixel threshold).
* Adjust Z-index to prevent overlapping with theme header menus, widgets, or popups.
* Automatically limits sticky video height to 50% of viewport to prevent blocking screen space.
* Protect mobile user experience by disabling the sticky video player on narrow viewports automatically.
* Exclude specific videos using a customizable CSS class.
* Beautiful and clean admin settings page.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ku-sticky-video-for-youtube` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure the settings under Settings -> KU Sticky Video for YouTube.

== Frequently Asked Questions ==

=== How can I prevent a specific YouTube video from floating? ===
You can add the excluded CSS class (configured in the settings page, default: `no-sticky`) to the YouTube block or its parent container in the WordPress editor under "Advanced" -> "Additional CSS class(es)".

== Screenshots ==

1. The upper part of the plugin settings page (General Settings and Trigger options).
2. The lower part of the plugin settings page (Layout, Width, Z-index, and Mobile settings).
3. A YouTube video player loaded in a WordPress post on the frontend.
4. The YouTube video player showing in a sticky (fixed position) state in the bottom-right corner when scrolling down the WordPress post.

== Changelog ==

=== 1.8.0 ===
* Improve: Migrated scroll tracking to IntersectionObserver API to eliminate layout reflows (getBoundingClientRect) for a much smoother scroll experience.
* Improve: Implemented conditional asset loading on PHP side to skip loading JS/CSS on singular pages that do not contain any YouTube video embeds.

=== 1.7.0 ===
* Add: Support for percentage (%) and pixel (px) width units for the floating player.
* Add: Max-width constraints for percentage-based sizes to prevent exceeding original video width or a custom pixel limit.
* Add: Configurable Z-index option to resolve overlapping with sticky header menus and chat widgets.
* Add: Option to hide the floating player when scrolling above the original video's position.
* Improve: Auto-limit the floating player's height to 50% of the viewport.
* Fix: Resolved issue where the sticky player was not dismissed when playing another video while a sticky video is paused.
* Fix: Refined YouTube API iframe loading and initialization timing.

=== 1.6.1 ===
* Fix: Adjusted Japanese translation spacing and punctuation to align with the WordPress translation style guide.

=== 1.6.0 ===
* Add: Option to select display position (Bottom Right or Bottom Left).
* Add: Option to automatically disable sticky video on narrow (mobile) viewports.
* Improve: Enhance internal hooks and architecture to support future extensions.

=== 1.5.0 ===
* Rename: Renamed plugin from "Sticky YouTube" to "KU Sticky Video for YouTube".
* Update: Updated version to 1.5.0.

=== 1.4.3 ===
* Fix: Compatibility headers and PHP version.
* Fix: Settings page security enhancements.
* Add: License declarations for WordPress.org submission.

=== 1.0.0 ===
* Initial release.

