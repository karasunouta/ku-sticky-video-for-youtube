=== KU Sticky Video for YouTube ===
Contributors: karasunouta
Donate link: https://karasunouta.com/
Tags: youtube, video, sticky, scroll, floating
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.5.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Make YouTube video player in posts follow the scroll position, showing in the corner of the page.

== Description ==

KU Sticky Video for YouTube is a lightweight WordPress plugin that automatically makes YouTube video players inside post content follow the scroll position, showing in the corner of the page when the original video scrolls out of view.

=== Features ===
* Float YouTube video when scrolled out of view.
* Customizable exclude CSS class to prevent specific videos from floating.
* Beautiful and clean admin settings page to configure options.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ku-sticky-video-for-youtube` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure the settings under Settings -> KU Sticky Video for YouTube.

== Frequently Asked Questions ==

=== How can I prevent a specific YouTube video from floating? ===
You can add the excluded CSS class (configured in the settings page, default: `no-sticky`) to the YouTube block or its parent container in the WordPress editor under "Advanced" -> "Additional CSS class(es)".

== Changelog ==

=== 1.5.0 ===
* Rename: Renamed plugin from "Sticky YouTube" to "KU Sticky Video for YouTube".
* Update: Updated version to 1.5.0.

=== 1.4.3 ===
* Fix: Compatibility headers and PHP version.
* Fix: Settings page security enhancements.
* Add: License declarations for WordPress.org submission.

=== 1.0.0 ===
* Initial release.
