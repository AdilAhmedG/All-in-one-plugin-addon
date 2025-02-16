<?php
/**
 * Functions for Text and Textarea
 * @since 3.11.3
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a character counter to text and textarea fields
 * Added advanced-preview on 3.11.6
 * @since 3.11.3
 */
function pewc_add_text_counter( $item, $id, $group_layout, $file ) {

	if ( empty( $item['field_type'] ) || ( 'text' != $item['field_type'] && 'textarea' != $item['field_type'] && 'advanced-preview' != $item['field_type'] ) ) {
		return; // do nothing
	}

	if ( empty( $item['show_char_counter'] ) ) {
		return; // do nothing
	}

	echo sprintf('<p class="pewc-text-counter-container %s"><small class="pewc-text-counter">', $id );
	echo '<span class="pewc-current-count">0</span>';

	if ( ! empty( $item['field_maxchars'] ) && apply_filters( 'pewc_text_counter_show_max', true, $item ) ) {
		echo apply_filters( 'pewc_text_counter_separator', ' / ', $item );
		echo sprintf('<span class="pewc-max-count">%d</span>', $item['field_maxchars'] );
	}

	echo '</small></p>';
}
add_action( 'pewc_after_include_frontend_template', 'pewc_add_text_counter', 10, 4);

/**
 * Add styles for character counter
 * @since 3.11.3
 */
function pewc_add_text_counter_styles() {
	$css = '
	/* Add-Ons Ultimate character counter */
	.pewc-text-counter-container {float:right; margin-top: 1em;}
	.pewc-text-counter-container .pewc-current-count.error { color:#ff0000; }';

	wp_add_inline_style( 'pewc-style', $css );
}
add_action( 'wp_enqueue_scripts', 'pewc_add_text_counter_styles', 9999 );
