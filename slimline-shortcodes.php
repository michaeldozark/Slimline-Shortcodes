<?php
/**
 * Plugin Name: Slimline Shortcodes
 * Plugin URI: http://www.michaeldozark.com/wordpress/plugins/slimline-shortcodes/
 * Description: Helper shortcodes. These were originally bundled with Slimline themes, but were removed for intruding on plugin territory.
 * Author: Michael Dozark
 * Author URI: http://www.michaeldozark.com/
 * Version: 0.1.0
 * Text Domain: slimline_shortcodes
 * Domain Path: /lang
 * License: GNU General Public License version 2.0
 * License URI: LICENSE
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2.0, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

/**
 * Fire the initialization function. This should be the only instance of add_action that
 * is not contained within a defined function.
 */
add_shortcode( 'init', 'slimline_shortcodes_init' );

/**
 * slimline_shortcodes_init function
 *
 * Initialize and configure the plugin.
 *
 * @since 0.1.0
 */
function slimline_shortcodes_init() {

	add_shortcode( 'slimline_google_map', 'slimline_shortcodes_google_map' ); // add a simple Google map to a post
	add_shortcode( 'slimline_mail', 'slimline_shortcodes_mail' ); // transparent name mangling to reduce spam
	add_shortcode( 'slimline_tel', 'slimline_shortcodes_tel' ); // transform telephone numbers into links on mobile devices
}

/**
 * slimline_shortcodes_google_map shortcode
 *
 * Creates a correctly marked Google map in the post content. Accepts the following attributes:
 * class  : CSS class or classes to add to the iframe
 * height : The height for the embedded iframe. Can be given in pixels, percent, or bare. Defaults to 350
 * id     : ID attribute for the iframe
 * src    : The URL of the map to embed.
 * text   : The text for the view larger map link. Defaults to "View Larger Map"
 * title  : The title attribute for the view larger map link. Defaults to value of Text.
 * width  : The width for the embedded iframe. Can be given in pixels, percent, or bare. Defaults to 425
 *
 * @param array $atts The shortcode attributes
 * @return string HTML string containing the embedded iframe or empty if no src attribute passed.
 * @since 0.1.0
 */
function slimline_shortcodes_google_map( $atts ) {

	extract(
		shortcode_atts(
			array(
				'class'  => '',
				'height' => 350,
				'id'     => '',
				'src'    => '',
				'text'   => __( 'View Larger Map', 'slimline_shortcodes' );
				'width'  => 425
			), $atts
		)
	);

	if ( empty( $src ) )
		return ''; // return empty string if no src given since there won't be a map to embed

	/**
	 * Clean up extracted attributes for use in the returned HTML string.
	 */

	// set class and id strings if any
	$class = ( $class ? "class='{$class}'}" : '' );
	$id = ( $class ? "id='{$id}'}" : '' );

	// strip px from height and width so we can use them for iframe attributes
	$height = str_replace( 'px', '', $height );
	$width = str_replace( 'px', '', $width );

	// add px in to height and width if they are not percent-based. Used for the iframe's inline CSS.
	$style_height = ( strpos( $height, '%' ) ? $height : "{$height}px" );
	$style_width = ( strpos( $width, '%' ) ? $width : "{$width}px" );

	// escape title text
	$title = esc_attr( ( $title ? $title : $text ) );

	/**
	 * Return HTML string
	 */
	return "
		<iframe {$class} frameborder='0' height='{$height}' {$id} marginheight='0' marginwidth='0' scrolling='no' src='{$src}&output=embed' style='height: {$style_height}; width: {$style_width};' width='{$width}'></iframe><br />
		<small><a href='{$src}' title='{$title}'>{$text}</a></small>
	";

}

/**
 * slimline_shortcodes_mail shortcode
 *
 * Shortcode for transparent name mangling on an email address. Accepted attibutes are:
 * class  : CSS class or classes to add to the anchor link
 * id     : ID attribute for the anchor link
 * title  : The title attribute for the link. Defaults to "{$content}"
 * 
 * @param array $atts Shortcode attributes
 * @param string $content Content enclosed between the [slimline_mail] shortcode tags
 * @return string HTML mailto link
 */
function slimline_shortcodes_mail( $atts, $content = null ) {

	$encoded_email = ''; // setup empty string so we don't produce PHP notices

	$strlen = strlen( $content );

	for ( $i = 0; $i < $strlen; $i++ )
		$encoded_email .= "&#" . ord( $content[ $i ] ) . ';';

	extract(
		shortcode_atts(
			array(
				'text'  => $encoded_email,
				'title' => $encoded_email
			), $atts
		)
	);

	// set class and id strings if any
	$class = ( $class ? "class='{$class}'}" : '' );
	$id = ( $class ? "id='{$id}'}" : '' );

	return "<a {$class} href='mailto:{$encoded_email}' {$id} title='{$title}'>{$text}</a>";

}

/**
 * slimline_shortcodes_tel_link shortcode
 *
 * Shortcode to produce tel links on mobile devices. Accepted attibutes are:
 * class  : CSS class or classes to add to the anchor link
 * id     : ID attribute for the anchor link
 * number : The href-formatted phone number. Defaults to a string-replaced version of $content
 * title  : The title attribute for the link. Defaults to "Dial {$content}"
 *
 * @param array $atts Shortcode attributes
 * @param string $content Content enclosed between the [slimline_tel] shortcode tags
 * @return string HTML tel link if a mobile device or $content if not a mobile device.
 * @uses is_mobile() to detect mobile device
 * @since 0.1.0
 */
function slimline_tel_link_shortcode( $atts, $content = '' ) {

	if ( ! is_mobile() )
		return $content; // tel links can produce errors in some desktop browsers

	extract(
		shortcode_atts(
			array(
				'class'  => '',
				'id'     => '',
				'number' => str_replace( array( '(', ')', '-', ' ' ), '', $content ),
				'title'  => __( sprintf( 'Dial %1$s', $content ), 'slimline' )
			), $atts
		)
	);

	// set class and id strings if any
	$class = ( $class ? "class='{$class}'}" : '' );
	$id = ( $class ? "id='{$id}'}" : '' );

	return "<a {$class} href='tel:{$number}' {$id} title='{$title}'>{$content}</a>";
}