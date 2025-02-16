<?php
/**
 * A name your price template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

// echo pewc_field_label( $item, $id );

$min = isset( $item['field_minval'] ) ? $item['field_minval'] : 0;
$max = isset( $item['field_maxval'] ) ? $item['field_maxval'] : '';
$step = apply_filters( 'pewc_name_your_price_step', 0.01, $item, $id );

$product_id = $product->get_id();

$other_data = '';

$require_required = apply_filters( 'pewc_only_validate_number_field_value_if_field_required', false, $product_id, $item );
if ( $require_required )
	$other_data .= 'data-require-required="yes" ';
else
	$other_data .= 'data-require-required="no" ';

printf(
	'%s<input type="number" class="pewc-form-field" id="%s" name="%s" value="%s" step="%s" min="%s" max="%s" %s>%s',
	$open_td, // Set in functions-single-product.php
	esc_attr( $id ),
	esc_attr( $id ),
	esc_attr( $value ),
	esc_attr( $step ),
	esc_attr( $min ),
	esc_attr( $max ),
	apply_filters( 'pewc_name_price_field_other_data', $other_data, $product_id, $item),
	$close_td
); ?>
