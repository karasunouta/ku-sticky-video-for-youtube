=== KU Sticky Video for YouTube ===
Contributors: karasunouta
Donate link: https://karasunouta.com/
Tags: sticky video, floating video, picture in picture, youtube scroll, youtube
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.8.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically float YouTube videos on scroll as a sticky video. Features picture in picture floating video and smooth youtube scroll follow.

== Description ==

Create a seamless and engaging viewing experience with **KU Sticky Video for YouTube**. This lightweight plugin automatically turns your YouTube embeds into a **floating video** (a **sticky video** in **picture in picture** style) that follows the reader as they browse your content.

When a user scrolls past a playing video, the player smoothly glides to the corner of the page so they can keep watching. This prevents interruptions, keeping users engaged with your content while they read. (You can also configure it to float always on scroll.)

Whether you want to implement **youtube scroll** tracking or a simple **floating video** widget, this plugin delivers high performance with zero configuration needed.

This plugin contains minified JavaScript for performance. The complete, unminified source code is publicly available on GitHub:
https://github.com/karasunouta/ku-sticky-video-for-youtube

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

== Frequently Asked Questions ==

=== How can I prevent a specific YouTube video from floating? ===
You can add the excluded CSS class (configured in the settings page, default: `no-sticky`) to the YouTube block or its parent container in the WordPress editor under "Advanced" -> "Additional CSS class(es)".

=== Will this plugin slow down my website? ===
No, it is built with performance in mind. The plugin is extremely lightweight (minified vanilla JS, no jQuery) and utilizes conditional loading. This means scripts and styles are only loaded on pages or posts that actually contain YouTube video embeds. Other pages will remain completely untouched.

=== How do I fix the floating player overlapping with my header menu or chat widget? ===
You can adjust the Z-index setting in the plugin options (Settings -> KU Sticky Video for YouTube). Increasing the Z-index will bring the player forward, while decreasing it can place it behind sticky menus. You can also switch the display position between Bottom Right and Bottom Left.

=== Can I disable the sticky effect on mobile devices? ===
Yes. There is a setting to automatically disable the sticky player on narrow viewports (mobile devices). This ensures a clean and user-friendly experience on smaller screens where screen space is limited.

=== Does it work with the default WordPress Gutenberg editor? ===
Yes, it works out of the box with the default WordPress YouTube Embed blocks, as well as classic editor embeds and raw iframe codes within post content.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ku-sticky-video-for-youtube` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure the settings under Settings -> KU Sticky Video for YouTube.

=== Build Instructions ===
To rebuild the minified assets, run the following commands in the plugin directory:
1. `npm install` (to install build dependencies)
2. `npm run build` (to compile the source files using `@wordpress/scripts`)

== Screenshots ==

1. The upper part of the plugin settings page (General Settings and Trigger options).
2. The lower part of the plugin settings page (Layout, Width, Z-index, and Mobile settings).
3. A YouTube video player loaded in a WordPress post on the frontend.
4. The YouTube video player showing in a sticky (fixed position) state in the bottom-right corner when scrolling down the WordPress post.

== Changelog ==

=== 1.8.3 ===
* Add: Settings link on plugins list page.

=== 1.8.2 ===
* Fix: Resolved an issue where sticky borders and box-sizing styles would leak and remain on the original video player after dismissing the sticky state.

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
