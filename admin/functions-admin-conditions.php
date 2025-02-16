<?php
/**
 * Functions for conditions in the back end
 * @since 3.20.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return options for the true/false select field for Log In Status
 * @since 3.20.0
 */
function pewc_get_logged_in_status_options( $value ) {

	$field_options = array(
		'true'	=> __( 'True', 'pewc' ),
		'false'	=> __( 'False', 'pewc' ),
	);
	$options = array();
	foreach( $field_options as $key=>$label ) {
		$selected = selected( $value, $key, false );
		$options[] = sprintf(
			'<option %s value="%s">%s</option>',
			$selected,
			esc_attr( $key ),
			esc_attr( $label )
		);
		
	}

	return join( ' ', $options );
}