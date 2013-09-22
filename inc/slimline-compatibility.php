<?php
/**
 * Slimline Compatibility
 *
 * This file allows plugins to include Slimline's contextual actions and filters without
 * needing the Slimline framework present. It includes pluggable functions to replace 
 * the ones contained in Slimline themes.
 *
 * @package Slimline
 * @subpackage Compatibility
 * @version 0.1.0
 */

/**
 * slimline_apply_filters function
 *
 * Converts slimline_apply_filters to a standar apply_filters call. Useful since we 
 * will not access to Slimline context variables.
 *
 * @param string $tag The base name of the filter hook.
 * @param mixed $value The value which the filters hooked to $tag may modify.
 * @param mixed $args All additional arguments for the filter.
 * @return mixed The result after the filter hook has been applied to $value
 * @since 0.1.0
 */
if ( ! function_exists( 'slimline_apply_filters' ) {

	function slimline_apply_filters( $tag, $value = '', $args = '' ) {

		// retrieve the filter arguments, minus the $tag
		$args = func_get_args();
		$args = array_splice( $args, 0, 1 );

		return apply_filters_ref_array( $tag, $args ); // only apply the generic filter

	}

}

/**
 * slimline_class template tag
 *
 * Gives miscellaneous objects a filterable class.
 *
 * @param string $element The element identifier. Also serves as the intial class
 * @param array|string $classes (Optional). An array or space-separated string of additional classes to apply to the element.
 * @return string HTML class attribute
 * @since 0.1.0
 */
if ( ! function_exists( 'slimline_class' ) ) {

	function slimline_class( $element = '', $classes = '' ) {

		echo slimline_get_class( $element, $classes );
	}
}

/**
 * slimline_do_action function
 *
 * Converts slimline_do_action to a standar do_action call. Useful since we will 
 * not have access to Slimline context variables.
 *
 * @param string $tag The base name of the action hook.
 * @param mixed $args All additional arguments for the action.
 * @since 0.1.0
 */
if ( ! function_exists( 'function slimline_do_action' ) ) {

	function slimline_do_action( $tag, $args = '' ) {

		// retrieve the action arguments, minus the $tag
		$args = func_get_args();
		$args = array_splice( $args, 0, 1 );

		do_action_ref_array( $tag, $args ); // do only the generic action

	}

}

/**
 * slimline_get_class template tag
 *
 * Generates a filterable class for miscellaneous elements.
 *
 * @param string $element The element identifier. Also serves as the intial class
 * @param array|string $classes (Optional). An array or space-separated string of additional classes to apply to the element.
 * @return string HTML class attribute
 * @since 0.1.0
 */
if ( ! function_exists( 'slimline_class' ) ) {

	function slimline_class( $element = '', $classes = '' ) {

		if ( ! $element )
			return ''; // stop processing if no arguments passed

		if ( is_string( $classes ) ) // get an array for easier filtering
			$classes = explode( ' ', $classes );

		// no need to use slimline_apply_filters since we would only convert that to a regular apply_filters anyway
		$classes = apply_filters( 'slimline_class', $classes, $element );

		$classes = apply_filters( "slimline_class-{$element}", $classes, $element );

		$classes = array_unshift( $element, $classes );

		return implode( ' ', $classes );
	}
}
