<?php
/**
 * Plugin Name: Slimline Shortcodes
 * Plugin URI: http://www.michaeldozark.com/slimline/shortcodes/
 * Description: Helper shortcodes. These were originally bundled with Slimline themes, but were removed for intruding on plugin territory.
 * Author: Michael Dozark
 * Author URI: http://www.michaeldozark.com/
 * Version: 0.3.0
 * Text Domain: slimline-shortcodes
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
 *
 * @package Slimline Shortcodes
 * @subpackage Core
 * @version 0.3.0
 * @author Michael Dozark <michael@michaeldozark.com>
 * @copyright Copyright (c) 2014, Michael Dozark
 * @link http://www.michaeldozark.com/slimline/shortcodes/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Fire the initialization function. This should be the only instance of add_action that
 * is not contained within a defined function.
 */
add_action( 'init', 'slimline_shortcodes_init' );

/**
 * slimline_shortcodes_init function
 *
 * Initialize and configure the plugin.
 *
 * @since 0.1.0
 */
function slimline_shortcodes_init() {

	load_plugin_textdomain( 'slimline-shortcodes', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . trailingslashit( 'lang' ) );

	add_shortcode( 'slimline_google_map', 'slimline_shortcodes_google_map' ); // add a simple Google map to a post
	add_shortcode( 'slimline_mail', 'slimline_shortcodes_mail' ); // transparent name mangling to reduce spam
	add_shortcode( 'slimline_tel', 'slimline_shortcodes_tel' ); // transform telephone numbers into links on mobile devices
}

/**
 * slimline_shortcodes_check_units function
 *
 * Checks for any CSS measurement units in a given string.
 *
 * @param string $att The attribute to check
 * @return bool Returns true if a CSS unit is found, false if not.
 * @since 0.3.0
 */
function slimline_shortcodes_check_units( $att ) {

	return ( strpos( $att, '%' ) || strpos( $att, 'em' ) || strpos( $att, 'px' ) );
}

/**
 * slimline_shortcodes_google_map shortcode
 *
 * Creates a correctly marked Google map in the post content. Accepts the following attributes:
 * class       : CSS class or classes to add to the iframe
 * height      : The height for the embedded iframe. Can be given in em, pixels, percent, or bare. Defaults to 350
 * id          : ID attribute for the iframe
 * link_class  : CSS class or classes to add to the view larger map link
 * link_id     : ID attribute for the view larger map link
 * link_style  : Additional style properties for the view larger map link
 * link_target : Target for view larger map link
 * style       : Additional style properties for the iframe
 * text        : The text for the view larger map link. Defaults to "View Larger Map"
 * title       : The title attribute for the view larger map link. Defaults to value of Text.
 * width       : The width for the embedded iframe. Can be given in pixels, percent, or bare. Defaults to 425
 *
 * @param array $atts The shortcode attributes
 * @param string $content (Required) The URL of the map to embed. Should be enclosed between the [slimline_google_map] tags.
 * @return string HTML string containing the embedded iframe or empty if no content parameter passed.
 * @since 0.1.0
 * @uses slimline_shortcodes_check_units() to check height and width attributes for units
 * @uses slimline_shortcodes_process_atts() to sanitize attributes
 */
function slimline_shortcodes_google_map( $atts, $content = '' ) {

	if ( empty( $content ) )
		return ''; // return empty string if no URL given since there won't be a map to embed

	$atts = shortcode_atts(
		array(
			'class'       => '',
			'height'      => 350,
			'id'          => '',
			'link_class'  => '',
			'link_id'     => '',
			'link_style'  => '',
			'link_target' => '',
			'style'       => '',
			'text'        => __( 'View Larger Map', 'slimline-shortcodes' ),
			'title'       => '',
			'width'       => 425,
		), $atts
	);

	$content = esc_url( $content ); // sanitize url

	// add px in to height and width if no units given. Used for the iframe's inline CSS.
	$style_height = ( slimline_shortcodes_check_units( $atts[ 'height' ] ) ? $atts[ 'height' ] : "{$atts[ 'height' ]}px" );
	$style_width = ( slimline_shortcodes_check_units( $atts[ 'width' ] ) ? $atts[ 'width' ] : "{$atts[ 'width' ]}px" );

	// add css height and width to style attribute
	$atts[ 'style' ] = "height: {$style_height}; width: {$style_width};" . $atts[ 'style' ];

	extract( slimline_shortcodes_process_atts( $atts ) );

	// set class, id, style and target strings for the link if not empty
	$link_class = ( ! empty( $link_class ) : "class='{$link_class}'" : '' );
	$link_id = ( ! empty( $link_id ) : "id='{$link_id}'" : '' );
	$link_style = ( ! empty( $link_style ) : "style='{$link_style}'" : '' );
	$link_target = ( ! empty( $link_target ) : "target='{$link_target}'" : '' );

	// strip non-percentage units from height and width so we can use them for iframe attributes
	$height = preg_replace( '/[^0-9|\%]/', '', $height );
	$width = preg_replace( '/[^0-9|\%]/', '', $width );

	// default title if empty
	$title = ( ! empty( $title ) ? $title : $text );

	/**
	 * Return HTML string
	 */
	$return = "
		<iframe {$class} frameborder='0' height='{$height}' {$id} marginheight='0' marginwidth='0' scrolling='no' src='{$src}&output=embed' {$style} width='{$width}'></iframe><br />
		<small><a {$link_class} href='{$src}' {$link_id} {$link_style} {$link_target} title='{$title}'>{$text}</a></small>
	";

	return apply_filters( 'slimline_google_map', $return, $atts );
}

/**
 * slimline_shortcodes_mail shortcode
 *
 * Shortcode for transparent name mangling on an email address. Accepted attibutes are:
 * class : CSS class or classes to add to the anchor link
 * id    : ID attribute for the anchor link
 * style : Additional style properties for the link
 * text  : Text to use for link if not the email address. Defaults to "{$content}"
 * title : The title attribute for the link. Defaults to "{$content}"
 * 
 * @param array $atts Shortcode attributes
 * @param string $content (Required) Content enclosed between the [slimline_mail] shortcode tags. Should be the email address.
 * @return string HTML mailto link
 * @since 0.1.0
 * @uses slimline_shortcodes_process_atts() to sanitize attributes
 */
function slimline_shortcodes_mail( $atts, $content = '' ) {

	if ( ! $content )
		return $content; // no need to continue if no email address given.

	$encoded_email = ''; // setup empty string so we don't produce PHP notices

	$content = sanitize_email( $content ); // strip non-allowed characters

	$strlen = strlen( $content );

	for ( $i = 0; $i < $strlen; $i++ )
		$encoded_email .= "&#" . ord( $content[ $i ] ) . ';';

	$atts = shortcode_atts(
		array(
			'class' => '',
			'id'    => '',
			'style' => '',
			'text'  => $encoded_email,
			'title' => $encoded_email,
		), $atts
	);

	extract( slimline_shortcodes_process_atts( $atts ) );

	$return = "<a {$class} href='mailto:{$encoded_email}' {$id} title='{$title}'>{$text}</a>";

	return apply_filters( 'slimline_mail', $return, $atts );
}

/**
 * slimline_shortcodes_process_atts helper function
 *
 * Sanitize variables, set class, id and style strings if any.
 *
 * @param array $atts Shortcode atts to process
 * @since 0.1.1
 */
function slimline_shortcodes_process_atts( $atts ) {

	$process_atts = array_map( 'esc_attr', $atts ); // make sure to escape attribute variables

	// set class, id and style strings if not empty
	$process_atts[ 'class' ] = ( $process_atts[ 'class' ] ? "class='{$process_atts[ 'class' ]}'" : '' );
	$process_atts[ 'id' ] = ( $process_atts[ 'id' ] ? "id='{$process_atts[ 'id' ]}'" : '' );
	$process_atts[ 'style' ] = ( $process_atts[ 'style' ] ? "style='{$process_atts[ 'style' ]}'" : '' );

	return apply_filters( 'slimline_shortcodes_process_atts', $process_atts, $atts );
}

/**
 * slimline_shortcodes_tel shortcode
 *
 * Shortcode to produce tel links on mobile devices. Accepted attibutes are:
 * class  : CSS class or classes to add to the anchor link
 * id     : ID attribute for the anchor link
 * number : The href-formatted phone number. Defaults to $content
 * style  : Additional style properties for the link
 * title  : The title attribute for the link. Defaults to "Dial {$content}"
 *
 * @param array $atts Shortcode attributes
 * @param string $content Content enclosed between the [slimline_tel] shortcode tags
 * @return string HTML tel link if a mobile device or $content if not a mobile device.
 * @uses wp_is_mobile() to detect mobile device
 * @uses slimline_shortcodes_process_atts() to sanitize attributes
 * @since 0.1.0
 */
function slimline_shortcodes_tel( $atts, $content = '' ) {

	if ( ! wp_is_mobile() )
		return $content; // tel links can produce errors in some desktop browsers

	$atts = shortcode_atts(
		array(
			'class'  => '',
			'id'     => '',
			'number' => $content,
			'title'  => sprintf( __( 'Dial %1$s', 'slimline-shortcodes' ), $content ),
		), $atts
	);

	extract( slimline_shortcodes_process_atts( $atts ) );

	$number = preg_replace( '/[^\d+]/', '', $number ); // remove any non-standard characters from number

	if ( ! $number )
		return $content; // bail if no link href

	$return = "<a {$class} href='tel:{$number}' {$id} title='{$title}'>{$content}</a>";

	return apply_filters( 'slimline_tel', $return, $atts );
}