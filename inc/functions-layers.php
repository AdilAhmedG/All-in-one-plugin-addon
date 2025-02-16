<?php
/**
 * Functions for layered images
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if a field has layered images
 * @since 3.17.0
 */
function pewc_field_has_layers( $field ) {

	if( ! empty( $field['field_type'] ) && $field['field_type'] == 'image_swatch' && ! empty( $field['layered_images'] ) ) {
		return true;
	}
	return false;

}

/**
 * Get the URL of a swatch layer to use in the composite final image
 * @since 3.17.0
 */
function pewc_get_swatch_field_url( $field_value, $field ) {

	$field_options = ! empty( $field['field_options'] ) ?  $field['field_options'] : array();
	$field_image = false;
	
	foreach( $field_options as $option ) {
		if( ! empty( $option['value'] ) && $option['value'] == $field_value ) {
			// This is the attachment ID of the selected swatch
			$field_image = $option['image'];
			break;
		}
	}
	
	if( $field_image ) {
		// Return the URL of the selected swatch
		return wp_get_attachment_url( $field_image );
	}

	return false;

}

function pewc_create_composite_image( $cart_item_data, $groups ) {

	$swatch_urls = array();
	$file_name = array();

	// Iterate through each group and find fields that might add layers
	if( $groups ) {
		foreach( $groups as $group_id=>$group ) {

			if( ! empty( $group['items'] ) ) {
				// Now iterate through each field in the group
				foreach( $group['items'] as $field_id=>$field ) {
					
					$has_layers = pewc_field_has_layers( $field );
					if( ! $has_layers ) {
						continue;
					}
					
					$field_value = ! empty( $_POST['pewc_group_' . $group_id . '_' . $field_id ][0] ) ? $_POST['pewc_group_' . $group_id . '_' . $field_id ][0] : false;
					if( ! $field_value ) {
						continue;
					}
					
					$file_name[] = $field_id;
					$file_name[] = sanitize_key( $field_value );
					$swatch_urls[] = pewc_get_swatch_field_url( $field_value, $field );
					
				}
			}
		}
	}

	if( ! $swatch_urls ) {
		// If we don't have any layers, just return the data
		return $cart_item_data;
	}

	$upload_dir = trailingslashit( pewc_get_upload_dir() );
	$product_id = $_POST['pewc_product_id'];
	$product = wc_get_product( $product_id );
	$base_image = wp_get_attachment_url( $product->get_image_id() );

	$base_image = new Imagick( $base_image );
	$base_image->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
	$base_image->setImageArtifact('compose:args', "1,0,-0.5,0.5");
	foreach( $swatch_urls as $swatch_url ) {
		$swatch = new Imagick( $swatch_url );
		$base_image->compositeImage($swatch, Imagick::COMPOSITE_MATHEMATICS, 0, 0);
	}

	$layer_dir = $upload_dir . trailingslashit( pewc_get_upload_subdirs() );
	$layer_url = pewc_get_upload_url() .trailingslashit( pewc_get_upload_subdirs() );

	// Make a directory for layered images if one does not already exist
	if( ! file_exists( $layer_dir . 'index.php' ) ) {
		wp_mkdir_p( $layer_dir );
		@file_put_contents( $layer_dir . 'index.php', '<?php' . PHP_EOL . '// That whereof we cannot speak, thereof we must remain silent.' );
	}

	$slug = $product->get_slug();
	$filename = $slug . '-' . join( '-', $file_name ) . '-' . time() . '.png';
	// Create a unique file name with product and swatch data
	$composite_file_url = $layer_dir . $filename;
	$base_image->writeImage( $composite_file_url );

	$cart_item_data['composite_image'] = $layer_url . $filename;

	return $cart_item_data;

}
add_filter( 'pewc_after_add_cart_item_data', 'pewc_create_composite_image', 10, 2 );