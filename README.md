Slimline Shortcodes
======

WordPress helper shortcodes. These were originally bundled with Slimline themes, but were removed for intruding on plugin territory.

== Description ==

== Installation ==

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 0.4.0 =
* Change plugin initialization hook from `init` to `plugins_loaded` to support the `load_plugin_textdomain()` call
* Add shortcode-specific filters to `slimline_shortcodes_process_atts()`
* Add `$text` attribute to `slimline_tel`
* Update PHPDoc comments