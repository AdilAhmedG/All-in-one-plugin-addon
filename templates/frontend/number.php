<?php
/**
 * A number field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$min = isset( $item['field_minval'] ) ? $item['field_minval'] : '0';
$max = isset( $item['field_maxval'] ) ? $item['field_maxval'] : '';
$decimals = ! empty ( $item['field_step'] ) ? $item['field_step'] : false;
if( ! $decimals ) {
	$step = '1';
} else {
	$step = pow( 10, 0 - absint( $decimals ) );
}

$product_id = $product->get_id();

$input_type = apply_filters( 'pewc_number_field_input_type', 'number', $product_id, $item );
$number_span = '';
if( $input_type == 'range' ) {
	$number_span = '<span class="pewc-range-value"></span>';
}

$other_data = '';

$require_required = apply_filters( 'pewc_only_validate_number_field_value_if_field_required', false, $product_id, $item );
if ( $require_required ) {
	$other_data .= 'data-require-required="yes" ';
} else {
	$other_data .= 'data-require-required="no" ';
}

$classes = array(
	'pewc-form-field',
	'pewc-number-field',
);
$classes[] = sprintf(
	'pewc-number-field-%s',
	esc_attr( $item['field_id'] )
);
if( $input_type == 'range' ) {
	$classes[] = 'pewc-range-slider';
}

printf(
	'%s<input type="%s" class="%s" id="%s" name="%s" value="%s" min="%s" max="%s" step="%s" autocomplete="%s" %s>%s%s',
	$open_td, // Set in functions-single-product.php
	$input_type,
	join( ' ', $classes ),
	esc_attr( $id ),
	esc_attr( $id ),
	esc_attr( $value ),
	esc_attr( $min ),
	esc_attr( $max ),
	apply_filters( 'pewc_number_field_step', $step, $item ),
	apply_filters( 'pewc_autocomplete', 'off', $item ),
	apply_filters( 'pewc_number_field_other_data', $other_data, $product_id, $item ),
	$number_span,
	$close_td
);
