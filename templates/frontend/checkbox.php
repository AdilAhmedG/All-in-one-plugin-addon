<?php
/**
 * A checkbox field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label = apply_filters( 'pewc_filter_field_option_name', wp_kses_post( $item['field_label'] ), $id, $item, $product );

$label_classes = array( 'pewc-checkbox-form-label' ); // label tag

$label .= '<span class="required"> &#42;</span>';

if( ! empty( $item['field_price'] ) && pewc_display_field_prices_product_page( $item ) ) {
	$field_price = apply_filters( 'pewc_filter_display_price_for_percentages', $field_price, $product, $item );
	$label .= apply_filters( 'pewc_option_price_separator', '+', $item ) . pewc_get_semi_formatted_raw_price( $field_price );
	$label = apply_filters( 'pewc_option_name', $label, $item, $product, $item['field_price'] );
}
$label = apply_filters( 'pewc_field_label_end', $label, $product, $item, $group_layout ); // tooltip can hook here

if( $group_layout == 'table' ) {
	$open_td = '<td colspan=2>';
}

printf(
	'%s<label class="%s" for="%s"><input type="checkbox" class="pewc-form-field" id="%s" name="%s" %s value="__checked__">&nbsp;<span>%s</span><span class="pewc-theme-element"></span></label>%s',
	$open_td, // Set in functions-single-product.php
	join( ' ', $label_classes ),
	esc_attr( $id ),
	esc_attr( $id ),
	esc_attr( $id ),
	checked( 1, $value, false ),
	$label,
	$close_td
); ?>
