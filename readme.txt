=== Slimline Shortcodes ===
Contributors: michaeldozark
Tags: google maps, mailto, shortcodes, tel
Requires at least: 2.5 or higher
Tested up to: 3.8
Stable tag: 0.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helper shortcodes for inserting mailto links, tel links and Google Maps.

== Description ==

== Installation ==

1. Upload the 'slimline-shortcodes' folder to the '/wp-content/plugins/' directory
2. Activate the 'Slimline Shortcodes' plugin through the 'Plugins' menu in WordPress
3. Insert links or maps into content using [slimline_google_map]{google maps url}[/slimline_google_map], [slimline_mail]{email address}[/slimline_mail] and [slimline_tel]{telephone number}[/slimline_tel]

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 0.4.0 =
* Change plugin initialization hook from 'init' to 'plugins_loaded' to support the 'load_plugin_textdomain()' call
* Add shortcode-specific filters to 'slimline_shortcodes_process_atts()'
* Add '$text' attribute to 'slimline_tel'
* Update PHPDoc comments