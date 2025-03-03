<?php
/**
 * Functions for media
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set Upload Directory
 *
 * Credit EDD
 * @return Array
 */
if( ! function_exists( 'pewc_set_upload_dir' ) ) {

	function pewc_set_upload_dir( $upload ) {

		global $woocommerce;

		$subdirs = pewc_get_upload_subdirs();

		$upload['subdir'] = '/product-extras' . $subdirs;
		$upload['path']   = $upload['basedir'] . $upload['subdir'];
		$upload['url']    = $upload['baseurl'] . $upload['subdir'];

		// Used when moving uploads to order specific folder
		update_option( 'pewc_upload_url', $upload['baseurl'] . '/product-extras' );

		pewc_create_protection_files( $upload );

		return $upload;

	}

}

function pewc_get_upload_subdirs() {
	if ( isset(WC()->session) && ! WC()->session->has_session() && ! is_user_logged_in()) {
		// we need to initiate session now because if user is not signed in, the session keeps changing it looks like
		WC()->session->set_customer_session_cookie( true );
	}

	$subdirs = '/' . md5( WC()->session->get_customer_id() );

	// Override the year / month being based on the post publication date, if year/month organization is enabled
	if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
		// Generate the yearly and monthly dirs
		$time = current_time( 'mysql' );
		$y = substr( $time, 0, 4 );
		$m = substr( $time, 5, 2 );
		$subdirs .= "/$y/$m";
	}

	return $subdirs;

}

/**
 * Retrieve the absolute path to the file upload directory without the trailing slash
 *
 * @return String $path
 */
if( ! function_exists( 'pewc_get_upload_dir' ) ) {

	function pewc_get_upload_dir() {

		global $woocommerce;
		$wp_upload_dir = wp_upload_dir();
		wp_mkdir_p( $wp_upload_dir['basedir'] . '/product-extras' );
		$path = $wp_upload_dir['basedir'] . '/product-extras';

		return apply_filters( 'pewc_filter_get_upload_dir', $path );
	}

}

/**
 * Retrieve the URL to the file upload directory without the trailing slash
 * @return String $url
 * @since 3.7.0
 */
function pewc_get_upload_url() {

	$url = get_option( 'pewc_upload_url', false );
	return apply_filters( 'pewc_filter_get_upload_url', $url );

}

function pewc_create_protection_files( $upload_path=false ) {

	if( $upload_path ) {
		$upload_path = $upload_path['subdir'] . '/product-extras';
	} else {
		$upload_path = pewc_get_upload_dir();
	}

	// Make sure the /product-extras folder is created
	wp_mkdir_p( $upload_path );

	// Top level .htaccess file
	$rules = pewc_get_htaccess_rules();
	if( apply_filters( 'pewc_overwrite_htaccess_file', true ) ) {
		@file_put_contents( $upload_path . '/.htaccess', $rules );
	}

	// Top level blank index.php
	@file_put_contents( $upload_path . '/index.php', '<?php' . PHP_EOL . '// That whereof we cannot speak, thereof we must remain silent.' );

	// Now place index.php files in all sub folders
	$folders = pewc_scan_folders( $upload_path );

}
// Changed to weekly 3.7.7
// add_action( 'admin_init', 'pewc_create_protection_files' );
add_action( 'wp_site_health_scheduled_check', 'pewc_create_protection_files' );

/**
 * Scans all folders inside /uploads/product-extras
 *
 * @since 1.0.0
 * @return Array $return List of folders inside directory
 */
function pewc_scan_folders( $path = '', $return = array() ) {
	$path = $path == ''? dirname( __FILE__ ) : $path;
	$lists = @scandir( $path );

	if ( ! empty( $lists ) ) {
		foreach ( $lists as $f ) {
			if ( is_dir( $path . DIRECTORY_SEPARATOR . $f ) && $f != "." && $f != ".." ) {
				if ( ! in_array( $path . DIRECTORY_SEPARATOR . $f, $return ) ) {
					$folder = trailingslashit( $path . DIRECTORY_SEPARATOR . $f );
					if ( ! file_exists( $folder . 'index.php' ) && wp_is_writable( $folder ) ) {
						@file_put_contents( $folder . 'index.php', '<?php' . PHP_EOL . '// That whereof we cannot speak, thereof we must remain silent.' );
					}
					$return[] = trailingslashit( $path . DIRECTORY_SEPARATOR . $f );
				}
				pewc_scan_folders( $path . DIRECTORY_SEPARATOR . $f, $return);
			}
		}
	}

	return $return;
}

/**
 * Retrieve the .htaccess rules to wp-content/uploads/product-extras/
 *
 * @since 1.6
 *
 * @param bool $method
 * @return mixed|void The htaccess rules
 */
function pewc_get_htaccess_rules() {

	// Prevent directory browsing and direct access to specified files
	$restricted_filetypes = apply_filters( 'pewc_protected_directory_allowed_filetypes', array( 'jpg', 'jpeg', 'png', 'gif' ) );
	$filetypes = '';
	if( ! empty( $restricted_filetypes ) && is_array( $restricted_filetypes ) ) {
		$filetypes = join( '|', $restricted_filetypes );
	}
	$rules = "Options -Indexes\n"; // Prevent directory browsing
	$rules .= "<Files ~ '.*\..*'>\n";
	$rules .= "Order Allow,Deny\n";
	$rules .= "Deny from all\n";
	$rules .= "</Files>\n";
	$rules .= "<FilesMatch '\.(" . $filetypes . ")$'>\n";
	$rules .= "Order Deny,Allow\n";
	$rules .= "Allow from all\n";
	$rules .= "</FilesMatch>";

	return $rules;

}


/**
 * Do the file upload - standard HTML file input method only
 * @param  String $file
 * @return Mixed
 */
function pewc_handle_upload( $file ) {

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	include_once( ABSPATH . 'wp-admin/includes/media.php' );

	if( ! isset( $_POST['pewc_file_upload'] ) || ! wp_verify_nonce( $_POST['pewc_file_upload'], 'pewc_file_upload' ) ) {
		wc_add_notice( apply_filters( 'pewc_file_not_valid_message', __( 'File not valid.', 'pewc' ) ), 'error' );
		return false;
		// wp_die( __( 'File not valid', 'pewc' ) );
	}
	$mime_types = pewc_get_permitted_mimes();
	// Use wp_check_filetype for additional security
	$file_info = wp_check_filetype( basename( $file['name'] ), $mime_types );

	if( ! empty( $file_info['type'] ) ) {

		$image_mimes = pewc_get_image_mimes();
		if( is_array( $image_mimes ) && in_array( $file_info['type'], $image_mimes ) ) {
			// Check image size for valid image
			if( ! @getimagesize( $file['tmp_name'] ) ) {
				wc_add_notice( apply_filters( 'pewc_file_not_valid_message', __( 'File not valid.', 'pewc' ) ), 'error' );
				return false;
				// wp_die( __( 'File not valid', 'pewc' ) );
			}
		}

		add_filter( 'upload_dir', 'pewc_set_upload_dir' );
		if ( ! has_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file' ) ) {
			add_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file2' ); // 3.13.7. The original filter conflicts with Divi, so allow users to remove that filter. This will be used instead.
		}

		$upload = wp_handle_upload(
			$file,
			array(
				'test_form' => false,
				'mimes'		=> $mime_types
			)
		);

		remove_filter( 'upload_dir', 'pewc_set_upload_dir' );
		if ( has_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file2' ) ) {
			remove_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file2' ); // 3.13.7
		}

		return $upload;

	} else {

		wc_add_notice( apply_filters( 'pewc_file_not_valid_message', __( 'File not valid.', 'pewc' ) ), 'error' );
		return false;
		// wp_die( __( 'File not valid', 'pewc' ) );

	}

	return false;

}

/**
 * Return the permitted file types
 *
 * @return 	Array
 */
function pewc_get_permitted_mimes() {

	$permitted_mimes = array(
		'jpg|jpeg|jpe'	=> 'image/jpeg', // Updated from image/jpg in 2.4.4
		'png'						=> 'image/png',
		'gif'						=> 'image/gif'
	);

	$permitted_mimes = apply_filters( 'pewc_permitted_mimes', $permitted_mimes );

	return $permitted_mimes;

}

/**
 * Retrieve selected MIME types from user options
 * @since 3.20.0
 */ 
function pewc_get_selected_mime_types() {
    // Retrieve the selected MIME types saved in the pewc_upload_mime_types option
    $selected_mime_types = get_option('pewc_upload_mime_types', array());

    // If PDF uploads are enabled, add 'pdf' to the array of selected MIME types
    if( pewc_enable_pdf_uploads() === 'yes' ) {
        $selected_mime_types[] = 'pdf';
    }

    // If there are selected MIME types, return them
    if (!empty($selected_mime_types)) {
        return $selected_mime_types;
    } else {
        // If no selected MIME types, return the default MIME types
        $default_mime_types = array( 'jpg|jpeg|jpe', 'png', 'gif' );
        // If PDF uploads are enabled, add 'pdf' to the default MIME types
        if (pewc_enable_pdf_uploads() === 'yes') {
            $default_mime_types[] = 'pdf';
        }
        return $default_mime_types;
    }
}

/**
 * Add selected MIME types into permitted MIME types
 * @since 3.20.0
 */ 
function pewc_selected_permitted_mimes($permitted_mimes) {
    
	// Retrieve selected MIME types
    $selected_mime_types = pewc_get_selected_mime_types();

    // Retrieve full MIME types list
    $mime_types = wp_get_mime_types();

    // Initialize array for user-selected permitted MIME types
    $user_permitted_mimes = array();

    // Iterate through selected MIME types and add them to the user_permitted_mimes array
    foreach ($selected_mime_types as $mime_type) {
        if (isset($mime_types[$mime_type])) {
            $file_extension = $mime_types[$mime_type];
            $user_permitted_mimes[$mime_type] = $file_extension;
        }
    }

    // Merge user-selected permitted MIME types with existing permitted MIME types
    //$permitted_mimes = array_merge($permitted_mimes, $user_permitted_mimes);
	$permitted_mimes = $user_permitted_mimes;

    return $permitted_mimes;
}
add_filter('pewc_permitted_mimes', 'pewc_selected_permitted_mimes');

/**
 * Return the standard image file types
 * Used to validate uploads
 * @since 3.8.10
 *
 * @return 	Array
 */
function pewc_get_image_mimes() {

	$image_mimes = array(
		'image/jpeg', 'image/jpg', 'image/jpe', 'image/png', 'image/gif'
	);

	$image_mimes = apply_filters( 'pewc_image_mimes', $image_mimes );

	return $image_mimes;

}

/**
 * Return the permitted file types by extension only
 *
 * @param 	$existing_mimes
 * @return 	Array
 */
function pewc_get_pretty_permitted_mimes() {
	$pretty_files = '';
	$file_types = pewc_get_permitted_mimes();
	if( $file_types ) {
		foreach( $file_types as $key=>$value ) {
			$pretty_files .= $key . ' ';
		}
	}
	$pretty_files = str_replace( '|', ' ', $pretty_files );
	return trim( $pretty_files );
}

/**
 * Filter the upload_dir.
 *
 * @param $pathdata
 * @return Array
 */
/*
function pewc_upload_dir( $pathdata ) {
	global $woocommerce;

	$dir = '/product-extras';

	$pathdata['path']   = $pathdata['path'] . '/product-extras/' . md5( $woocommerce->session->get_customer_id() );
	$pathdata['url']    = $pathdata['url']. '/product-extras/' . md5( $woocommerce->session->get_customer_id() );
	$pathdata['subdir'] = '/product-extras/' . md5( $woocommerce->session->get_customer_id() );

	return $pathdata;
}
*/

/**
 * Upload files via AJAX
 * @since 3.2.1
 */
function pewc_dropzone_upload() {

	if( ! isset( $_REQUEST['pewc_file_upload'] ) || ! wp_verify_nonce( $_REQUEST['pewc_file_upload'], 'pewc_file_upload' ) ) {
		wp_send_json_error( array( 'nonce_fail' ) );
	}

	add_filter( 'upload_dir', 'pewc_set_upload_dir' );
	if ( ! has_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file' ) ) {
		add_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file2' ); // 3.13.7. The original filter conflicts with Divi, so allow users to remove that filter. This will be used instead.
	}

	$uploaded_files = array();

	if( ! empty( $_FILES ) ) {

		$existing_file_data = $_REQUEST['file_data'];
		$existing_files = array();
		if( $existing_file_data ) {
			$existing_files = json_decode( stripslashes( $existing_file_data ) );
		}

		$base_url = get_site_url();
		// Truncate the path as well
		$base_dir = pewc_get_upload_dir();

		foreach( $_FILES['file']['name'] as $index=>$file ) {

			$error = 0;

			$uploaded_bits = wp_upload_bits(
				$_FILES['file']['name'][$index],
				null, //deprecated
				file_get_contents( $_FILES['file']['tmp_name'][$index] )
			);
			if ( false !== $uploaded_bits['error'] ) {
				$error = 1;
			}

			// Remove the base URL from the uploaded file name
			// Prevent users inserting cross domain URLs
			$truncated_url = str_replace( $base_url, '', $uploaded_bits['url'] );

			// Does the URL still contain http? Something fishy could be happening, so reject it
			if( strpos( $truncated_url, 'http' ) !== false || strpos( $truncated_url, ':' ) !== false ) {
				error_log( 'AOU: fail. truncated_url:'.$truncated_url );
			}

			$file_type = $_FILES['file']['type'][$index];
			if ( 'application/pdf' === $file_type && ! empty( $uploaded_bits ) && empty( $uploaded_bits['error'] ) ) {
				$pdf_thumb = pewc_generate_pdf_thumb( $uploaded_bits );
			} else {
				$pdf_thumb = false;
			}

			// Validate any images
			$image_mimes = pewc_get_image_mimes();
			if( is_array( $image_mimes ) && in_array( $_FILES['file']['type'][$index], $image_mimes ) ) {

				// Check image size for valid image
				if( ! @getimagesize( $_FILES['file']['tmp_name'][$index] ) ) {
					wc_add_notice(
						apply_filters(
							'pewc_ajax_file_not_valid_message',
							sprintf(
								'%s: %s',
								__( 'File not valid', 'pewc' ),
								$_FILES['file']['name'][$index]
							)
						),
						'error'
					);
					continue;
				}

			}

			$uploaded_files[$index] = array(
				'file'     	=> $uploaded_bits['file'],
				'size'			=> $_FILES['file']['size'][$index],
				'url'      	=> $truncated_url,
				'type'			=> $_FILES['file']['type'][$index],
				'filetype' 	=> wp_check_filetype( basename( $uploaded_bits['file'] ), null ),
				'name'			=> $_FILES['file']['name'][$index],
				'tmp_name'	=> $_FILES['file']['tmp_name'][$index],
				'error'			=> $error,
				'name_encoded'	=> $_REQUEST['filename_encoded'][$index] // Safari has issues with special characters, backup fix
			);

			if ( $pdf_thumb ) {
				$uploaded_files[$index]['pdf_thumb'] = $pdf_thumb;
			}

			if ( apply_filters( 'pewc_use_original_image_for_thumbnail', false ) ) {
				$uploaded_files[$index]['use_original'] = 'yes';
			}

		}

		if ( isset( $_POST['action'] ) && 'pewc_dropzone_upload' === $_POST['action'] ) {
			// apply filters to uploaded files... only ensure this is run if $_POST is set
			$uploaded_files = apply_filters( 'pewc_after_ajax_upload', $uploaded_files, $_POST );
		}

	}

	$uploaded_files = array_merge( $existing_files, $uploaded_files );

	remove_filter( 'upload_dir', 'pewc_set_upload_dir' );
	if ( has_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file2' ) ) {
		remove_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file2' ); // 3.13.7
	}

	wp_send_json_success( array( 'files' => $uploaded_files, 'count' => count( $uploaded_files ) ) );

	die();

}
add_action( 'wp_ajax_nopriv_pewc_dropzone_upload', 'pewc_dropzone_upload' ); //allow on front-end
add_action( 'wp_ajax_pewc_dropzone_upload', 'pewc_dropzone_upload' );

/**
 * Remove files via AJAX
 * @since 3.2.1
 */
function pewc_dropzone_remove() {

	if( ! isset( $_REQUEST['pewc_file_upload'] ) || ! wp_verify_nonce( $_REQUEST['pewc_file_upload'], 'pewc_file_upload' ) ) {
		wp_send_json_error( array( 'nonce_fail' ) );
	}

	add_filter( 'upload_dir', 'pewc_set_upload_dir' );

	$remove_file_name = $_REQUEST['file'];
	$existing_file_data = $_REQUEST['file_data'];
	$existing_files = array();
	if( $existing_file_data ) {
		$existing_files = json_decode( stripslashes( $existing_file_data ) );
	}

	$base_url = get_site_url();

	// Does the URL still contain http? Something fishy could be happening, so reject it

	// Find our file
	if( $existing_files ) {
		foreach( $existing_files as $index=>$existing_file ) {
			if( $existing_file->name == $remove_file_name ) {
				// Remove it from the array and from the server
				$filepath = $existing_file->file;
				if ( is_file( $filepath ) ) {
					do_action( 'pewc_dropzone_remove_before_unlink', $filepath ); // 3.18.2
					// delete the file
					unlink( $filepath );
				}
				if ( ! empty( $existing_file->pdf_thumb ) && is_file( $filepath . '.jpg' ) ) {
					// also delete PDF thumb
					unlink( $filepath . '.jpg' );
				}
				unset( $existing_files[$index] );
				// Reset the array
				$existing_files = array_values( $existing_files );
			}
		}
	}

	remove_filter( 'upload_dir', 'pewc_set_upload_dir' );

	wp_send_json_success( array( 'files' => $existing_files, 'count' => count( $existing_files ) ) );

	die();

}
add_action( 'wp_ajax_nopriv_pewc_dropzone_remove', 'pewc_dropzone_remove' ); //allow on front-end
add_action( 'wp_ajax_pewc_dropzone_remove', 'pewc_dropzone_remove' );

function pewc_dropzone_template() {
	if( pewc_enable_ajax_upload() != 'yes' ) {
		return;
	} ?>
	<div id="tpl" style="display: none; visibility: hidden;">
		<table class="dz-preview dz-file-preview">
			<tbody>
				<tr>
					<td class="pewc-dz-image-wrapper"><img data-dz-thumbnail /></td>
					<td class="dz-details">
						<div class="dz-filename"><span data-dz-name></span></div>
						<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
						<div class="dz-success-mark"><span><?php echo apply_filters( 'pewc_filter_upload_success_message', __( 'Uploaded', 'pewc' ) ); ?></span></div>
						<div class="dz-error-mark"><span><?php echo apply_filters( 'pewc_filter_upload_failed_message', __( 'Failed', 'pewc' ) );; ?></span></div>
						<div class="dz-error-message"><span data-dz-errormessage></span></div>
						<div class="dz-size" data-dz-size></div>
					</td>
					<?php do_action( 'pewc_dz_tpl_td' ); ?>
					<td class="pewc-dz-remove-wrapper"><img src="<?php echo esc_url( PEWC_PLUGIN_URL . 'assets/images/remove.png' )?>" data-dz-remove></td>
				</tr>
			</tbody>
		</table>
	</div>
<?php }
add_action( 'wp_footer', 'pewc_dropzone_template' );

/**
 * Rename uploaded files
 * @since 3.7.0
 */
function pewc_rename_uploaded_file( $filename ) {

	// Lots of reasons we don't want to rename this particular file
	if( ! pewc_get_rename_uploads() || empty( $_POST ) || ! isset( $_POST['pewc_product_id'] ) ) {
		return $filename;
	}

	if( $filename == 'wc-aelia-foundation-classes' ) {
		return $filename;
	}

	// Now get the updated file name
	$product_id = isset( $_POST['pewc_product_id'] ) ? absint( $_POST['pewc_product_id'] ) : '';
	$new_name = pewc_get_renamed_uploaded_file( $filename, $product_id );

	return $new_name;

}
add_filter( 'sanitize_file_name', 'pewc_rename_uploaded_file' );

/**
 * Rename uploaded files 2.0. The original filter above interferes with Divi theme, so allow users to not use that filter (remove_filter) but use the one found inside the upload functions instead
 * @since 3.13.7
 */
function pewc_rename_uploaded_file2( $filename ) {
	return pewc_rename_uploaded_file( $filename );
}

/**
 * Get the renamed file name
 * @since 3.7.0
 */
function pewc_get_renamed_uploaded_file( $filename, $product_id ) {

	// Just check this filter isn't getting called some other way, before WooCommerce is ready
	if( did_action( 'woocommerce_after_register_taxonomy' ) < 1 ) {
		return $filename;
	}

	if( ! function_exists( 'wc_get_product' ) || 'product' != get_post_type( $product_id ) ) {
		return $filename;
	}

	$info = pathinfo( $filename );
	$ext  = isset( $info['extension'] ) && ! empty( $info['extension'] ) ? '.'. $info['extension'] : '';
	$name = basename( $filename, $ext );

	// We need to parse the tags in the setting
	$new_name = pewc_get_rename_uploads();
	$new_name = str_replace( '{product_id}', $product_id, $new_name );

	$product = wc_get_product( $product_id );
	$sku = $product->get_sku();
	if( $sku ) {
		$new_name = str_replace( '{product_sku}', $sku, $new_name );
	} else {
		$new_name = str_replace( '{product_sku}', 'product_sku', $new_name );
	}

	$format = apply_filters( 'pewc_uploaded_file_date_format', 'Y-m-d' );
	$date = date( $format);
	$new_name = str_replace( '{date}', $date, $new_name );

	$new_name = str_replace( '{original_file_name}', $name, $new_name );

	// Prepare
	$new_name = str_replace( '{product_sku}', 'xxproduct_skuxx', $new_name );
	$new_name = str_replace( '{order_number}', 'xxorder_numberxx', $new_name );
	$new_name = str_replace( '{group_id}', 'xxgroup_idxx', $new_name );
	$new_name = str_replace( '{field_id}', 'xxfield_idxx', $new_name );
	$new_name = str_replace( '{quantity}', 'xxquantityxx', $new_name );

	return $new_name . $ext;

}

function pewc_add_pdf_permitted_mimes( $permitted_mimes ) {

	if( pewc_enable_pdf_uploads() != 'yes' ) {
		return $permitted_mimes;
	}

  // Add PDF to the list of permitted mime types
  $permitted_mimes['pdf'] = "application/pdf";
  return $permitted_mimes;

}
add_filter( 'pewc_permitted_mimes', 'pewc_add_pdf_permitted_mimes' );

// Add PDF to the list of restricted filetypes
function pewc_add_pdf_allowed_filetypes( $restricted_filetypes ) {

	if( pewc_enable_pdf_uploads() != 'yes' ) {
		return $restricted_filetypes;
	}

  $restricted_filetypes[] = 'pdf';
  return $restricted_filetypes;

}
add_filter( 'pewc_protected_directory_allowed_filetypes', 'pewc_add_pdf_allowed_filetypes' );
