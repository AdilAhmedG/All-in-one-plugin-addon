<?php
/**
 * Include the Customizer Library
 * @since 2.3.3
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Certain themes throw a JavaScript error related to the color picker
function pewc_enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker');
}
add_action( 'admin_enqueue_scripts', 'pewc_enqueue_color_picker' );

add_action( 'customize_register', 'pewc_add_product_extras_section' );

function pewc_add_customizer_section( $wp_customize ) {
  pewc_add_product_extras_section( $wp_customize );
}

function pewc_add_product_extras_section( $wp_customize ) {

  $wp_customize->add_panel( 'pewc_panel', array(
    'priority'       => 201,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Product Add-Ons Ultimate', 'pewc' )
  ) );

  $wp_customize->add_section(
    'pewc_section',
    array(
      'title'    => __( 'Labels', 'pewc' ),
      'priority' => 10,
      'panel'    => 'pewc_panel'
    )
  );

  $wp_customize->add_section(
    'pewc_styles_section',
    array(
      'title'    => __( 'Styles', 'pewc' ),
      'priority' => 20,
      'panel'    => 'pewc_panel',
    )
  );

  $wp_customize->add_section(
    'pewc_swatches_section',
    array(
      'title'    => __( 'Swatches', 'pewc' ),
      'priority' => 30,
      'panel'    => 'pewc_panel',
    )
  );

  $wp_customize->add_setting(
    'pewc_preset_style',
    array(
      'default'     => 'simple',
      'type'        => 'option',
      'capability'  => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_preset_style',
    array(
      'label'    => __( 'Preset styles', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_preset_style',
      'type'        => 'select',
      // 'description' => __( '(Beta only - subject to change)', 'pewc' ),
      'choices'     => array(
        'inherit'		=> __( 'Inherit from theme', 'pewc' ),
        // 'colour'		=> __( 'Colour', 'pewc' ),
        // 'rounded'		=> __( 'Rounded', 'pewc' ),
        // 'shadow'		=> __( 'Shadow', 'pewc' ),
        // 'minimal'		=> __( 'Minimal', 'pewc' ),
        'simple'		=> __( 'Simple', 'pewc' )
      ),
    )
  );

  $wp_customize->add_setting(
    'pewc_preset_accent_colour',
    array(
      'default'     => '#2196F3',
      'type'        => 'option',
      'capability'  => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_preset_accent_colour',
    array(
      'label'           => __( 'Accent colour', 'pewc' ),
      'section'         => 'pewc_styles_section',
      'settings'        => 'pewc_preset_accent_colour',
      'type'            => 'color',
      'active_callback' => 'pewc_use_preset_style'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_margin_left',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '0'
    )
  );

  $wp_customize->add_control(
    'pewc_list_margin_left',
    array(
      'label'    => __( 'Fields Wrapper Margin Left', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_margin_left',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_margin_bottom',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '0'
    )
  );

  $wp_customize->add_control(
    'pewc_list_margin_bottom',
    array(
      'label'    => __( 'Fields Wrapper Margin Bottom', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_margin_bottom',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_padding',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_list_padding',
    array(
      'label'    => __( 'Fields Wrapper Padding', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_padding',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_list_background',
    array(
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_list_background',
    array(
      'label'    => __( 'Fields Wrapper Background Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_list_background',
      'type'     => 'color'
    )
  );

  // Individual fields
  $wp_customize->add_setting(
    'pewc_field_margin_left',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '0'
    )
  );

  $wp_customize->add_control(
    'pewc_field_margin_left',
    array(
      'label'    => __( 'Field Margin Left', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_margin_left',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_margin_bottom',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '16'
    )
  );

  $wp_customize->add_control(
    'pewc_field_margin_bottom',
    array(
      'label'    => __( 'Field Margin Bottom', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_margin_bottom',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_padding_top',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_field_padding_top',
    array(
      'label'    => __( 'Field Padding (Top and Bottom)', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_padding_top',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_padding_left',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_field_padding_left',
    array(
      'label'    => __( 'Field Padding (Left and Right)', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_padding_left',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_field_background',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_field_background',
    array(
      'label'    => __( 'Field Background Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_field_background',
      'type'     => 'color'
    )
  );

  $wp_customize->add_setting(
    'pewc_text_colour',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_text_colour',
    array(
      'label'    => __( 'Text Colour', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_text_colour',
      'type'     => 'color'
    )
  );

  $wp_customize->add_setting(
    'pewc_text_width',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_text_width',
    array(
      'label'    => __( 'Full Width Text Fields ', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_text_width',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_number_width',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_number_width',
    array(
      'label'    => __( 'Full Width Number Fields ', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_number_width',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_textarea_height',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_textarea_height',
    array(
      'label'    => __( 'Textarea Height', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_textarea_height',
      'type'     => 'range',
      'input_attrs' => array(
        'min' => 2,
        'max' => 20,
        'step' => 1,
      )
    )
  );

  $wp_customize->add_setting(
    'pewc_select_width',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_select_width',
    array(
      'label'    => __( 'Full Width Select Fields ', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_select_width',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_block_label',
    array(
      'default'              => true,
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce'
    )
  );

  $wp_customize->add_control(
    'pewc_block_label',
    array(
      'label'    => __( 'Display Label on Own Line', 'pewc' ),
      'section'  => 'pewc_styles_section',
      'settings' => 'pewc_block_label',
      'type'     => 'checkbox'
    )
  );

  /**
   * General panel
   */

  $wp_customize->add_setting(
    'pewc_enable_summary_panel',
    array(
      'default'              => '',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_price_label',
    array(
      'default'              => '',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_price_display',
    array(
      'default'              => 'before',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_show_totals',
    array(
      'default'              => 'all',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_product_total_label',
    array(
      'default'              => __( 'Product total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_options_total_label',
    array(
      'default'              => __( 'Options total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_flatrate_total_label',
    array(
      'default'              => __( 'Flat rate total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_setting(
    'pewc_grand_total_label',
    array(
      'default'              => __( 'Grand total', 'pewc' ),
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      // 'sanitize_callback'    => 'wc_bool_to_string',
      // 'sanitize_js_callback' => 'wc_string_to_bool',
    )
  );

  $wp_customize->add_control(
    'pewc_enable_summary_panel',
    array(
      'label'    => __( 'Enable summary panel', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_enable_summary_panel',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_control(
    'pewc_price_label',
    array(
      'label'    => __( 'Price label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_price_label',
      'type'     => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_price_display',
    array(
      'label'    => __( 'Price label display', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_price_display',
      'type'        => 'select',
      'choices'     => array(
        'before'			=> __( 'Before price', 'pewc' ),
        'after'				=> __( 'After price', 'pewc' ),
        'hide'				=> __( 'Hide price', 'pewc' )
      ),
    )
  );

  $wp_customize->add_control(
    'pewc_show_totals',
    array(
      'label'    => __( 'Display totals fields', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_show_totals',
      'type'        => 'select',
      'choices'     => array(
        'all'           => __( 'Show totals', 'pewc' ),
        'none'          => __( 'Hide totals', 'pewc' ),
        'total'         => __( 'Total only', 'pewc' ),
      ),
    )
  );

  $wp_customize->add_control(
    'pewc_product_total_label',
    array(
      'label'    => __( 'Product total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_product_total_label',
      'type'        => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_options_total_label',
    array(
      'label'    => __( 'Options total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_options_total_label',
      'type'        => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_flatrate_total_label',
    array(
      'label'    => __( 'Flat rate total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_flatrate_total_label',
      'type'        => 'text'
    )
  );

  $wp_customize->add_control(
    'pewc_grand_total_label',
    array(
      'label'    => __( 'Grand total label', 'pewc' ),
      'section'  => 'pewc_section',
      'settings' => 'pewc_grand_total_label',
      'type'        => 'text'
    )
  );

  // Swatches panel

  // $wp_customize->add_setting(
  //   'pewc_swatch_wrapper',
  //   array(
  //     'default'              => '',
  //     'type'                 => 'option',
  //     'capability'           => 'manage_woocommerce',
  //     'default'              =>  ''
  //   )
  // );
  // $wp_customize->add_control(
  //   'pewc_swatch_wrapper',
  //   array(
  //     'label'    => __( 'Swatch Highlight Colour', 'pewc' ),
  //     'section'  => 'pewc_swatches_section',
  //     'settings' => 'pewc_swatch_wrapper',
  //     'type'     => 'color'
  //   )
  // );

  $wp_customize->add_setting(
    'pewc_show_inputs',
    array(
      'default'       => false,
      'type'          => 'theme_mod',
      'capability'    => 'manage_woocommerce',
      'transport'     => 'refresh'
    )
  );

  $wp_customize->add_control(
    'pewc_show_inputs',
    array(
      'label'    => __( 'Show Inputs on Products Field', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_show_inputs',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_products_border',
    array(
      'default'              => '',
      'type'                 => 'theme_mod',
      'capability'           => 'manage_woocommerce',
      'default'              =>  ''
    )
  );

  $wp_customize->add_control(
    'pewc_products_border',
    array(
      'label'    => __( 'Products Highlight Colour', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_products_border',
      'type'     => 'color'
    )
  );

  $wp_customize->add_setting(
    'pewc_swatch_border_width',
    array(
      'default'              => '',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '4'
    )
  );

  $wp_customize->add_control(
    'pewc_swatch_border_width',
    array(
      'label'    => __( 'Swatch Border Width', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_swatch_border_width',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_swatch_border_padding',
    array(
      'default'              => '',
      'type'                 => 'option',
      'capability'           => 'manage_woocommerce',
      'default'              =>  '8'
    )
  );

  $wp_customize->add_control(
    'pewc_swatch_border_padding',
    array(
      'label'    => __( 'Swatch Border Padding', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_swatch_border_padding',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_circular_swatches',
    array(
      'default'       => '',
      'type'          => 'option',
      'capability'    => 'manage_woocommerce',
      'transport'     => 'refresh'
    )
  );
  $wp_customize->add_control(
    'pewc_circular_swatches',
    array(
      'label'    => __( 'Enable Circular Swatches', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_circular_swatches',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_color_swatch_width',
    array(
      'default'       => '60',
      'type'          => 'option',
      'capability'    => 'manage_woocommerce',
      'transport'     => 'refresh'
    )
  );

  $wp_customize->add_control(
    'pewc_color_swatch_width',
    array(
      'label'    => __( 'Color Swatch Width', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_color_swatch_width',
      'type'     => 'number'
    )
  );

  $wp_customize->add_setting(
    'pewc_color_swatch_height',
    array(
      'default'       => '60',
      'type'          => 'option',
      'capability'    => 'manage_woocommerce',
      'transport'     => 'refresh'
    )
  );

  $wp_customize->add_control(
    'pewc_color_swatch_height',
    array(
      'label'    => __( 'Color Swatch Height', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_color_swatch_height',
      'type'     => 'number'
    )
  );
  
  $wp_customize->add_setting(
    'pewc_swatch_grid',
    array(
      'default'       => '',
      'type'          => 'option',
      'capability'    => 'manage_woocommerce',
      'transport'     => 'refresh'
    )
  );
  $wp_customize->add_control(
    'pewc_swatch_grid',
    array(
      'label'    => __( 'Use Grid Layout', 'pewc' ),
      'section'  => 'pewc_swatches_section',
      'settings' => 'pewc_swatch_grid',
      'type'     => 'checkbox'
    )
  );

  $wp_customize->add_setting(
    'pewc_swatch_image_size',
    array(
      'default'       => 'thumbnail',
      'type'          => 'option',
      'capability'    => 'manage_woocommerce',
      'transport'     => 'refresh'
    )
  );
  $wp_customize->add_control(
    'pewc_swatch_image_size',
    array(
      'label'         => __( 'Swatch Image Size', 'pewc' ),
      'section'       => 'pewc_swatches_section',
      'settings'      => 'pewc_swatch_image_size',
      'type'          => 'select',
      'choices'       => pewc_get_image_sizes()
    )
  );

}

/**
 * Have we enabled grid layout for swatches?
 * @since 3.20.0
 */
function pewc_get_swatch_grid() {
  return get_option( 'pewc_swatch_grid', false );
}

/**
 * Get image sizes for swatch fields
 * @since 3.20.0
 */
function pewc_get_image_sizes() {
  $sizes = wp_get_registered_image_subsizes();
  // $wcvs_sizes = array(
  //     'default'    => sprintf(
  //         '%s (50 x 50)',
  //         __( 'Default', 'woocommerce-variation-swatches' )
  //     ),
  //     'custom'    => __( 'Custom', 'woocommerce-variation-swatches' )
  // );
  $wcvs_sizes = array();
  if( $sizes ) {
      foreach( $sizes as $label=>$data ) {
          $name = str_replace( '_', ' ', $label );
          $name = str_replace( 'woocommerce', 'WooCommerce', $name );
          $name = ucwords( $name );
          $wcvs_sizes[$label] = sprintf(
              '%s (%s x %s)',
              $name,
              $data['width'],
              $data['height']
          );
      }
  }

  return $wcvs_sizes;
}

function pewc_customize_css() { ?>
  <style type="text/css">
    .pewc-group-content-wrapper {
      background-color: <?php echo get_theme_mod( 'pewc_list_background' ); ?> !important;
    }
    ul.pewc-product-extra-groups {
      margin-left: <?php echo get_theme_mod( 'pewc_list_margin_left' ); ?>px;
      margin-bottom: <?php echo get_theme_mod( 'pewc_list_margin_bottom' ); ?>px;
      padding: <?php echo get_theme_mod( 'pewc_list_padding' ); ?>px;
      background-color: <?php echo get_theme_mod( 'pewc_list_background' ); ?>;
    }
    .pewc-product-extra-groups > li {
      margin-left: <?php echo get_theme_mod( 'pewc_field_margin_left' ); ?>px;
      margin-bottom: <?php echo get_theme_mod( 'pewc_field_margin_bottom' ); ?>px;
      padding-top: <?php echo get_theme_mod( 'pewc_field_padding_top' ); ?>px;
      padding-bottom: <?php echo get_theme_mod( 'pewc_field_padding_top' ); ?>px;
      padding-left: <?php echo get_theme_mod( 'pewc_field_padding_left' ); ?>px;
      padding-right: <?php echo get_theme_mod( 'pewc_field_padding_left' ); ?>px;
      background-color: <?php echo get_theme_mod( 'pewc_field_background' ); ?>;
      color: <?php echo get_theme_mod( 'pewc_text_colour', 0 ); ?>;
    }
    <?php if( get_theme_mod( 'pewc_text_width' ) ) { ?>
      input[type="text"].pewc-form-field,
      textarea.pewc-form-field {
        width: 100% !important
      }
    <?php } ?>
    <?php if( get_theme_mod( 'pewc_number_width' ) ) { ?>
      .pewc-item-name_price input[type="number"].pewc-form-field,
      .pewc-item-number input[type="number"].pewc-form-field {
        width: 100% !important
      }
    <?php } ?>
    <?php if( get_theme_mod( 'pewc_select_width' ) ) { ?>
      select.pewc-form-field {
        width: 100% !important
      }
    <?php } ?>
    textarea.pewc-form-field {
      height: <?php echo get_theme_mod( 'pewc_textarea_height', false ); ?>em;
    }
    <?php if( get_theme_mod( 'pewc_block_label' ) ) { ?>
      ul.pewc-product-extra-groups .pewc-item:not(.pewc-item-checkbox):not(.pewc-item-products-radio-list) label {
        display: block !important
      }
    <?php }
    if( pewc_get_swatch_border_width() ) { ?>
      .pewc-preset-style.pewc-show-inputs .pewc-radio-image-wrapper,
      .pewc-preset-style.pewc-show-inputs .pewc-checkbox-image-wrapper,
      .pewc-hex {
        border-width: <?php echo absint( pewc_get_swatch_border_width() ); ?>px
      }
    <?php }
    if( pewc_get_swatch_width() ) { ?>
      .pewc-item-image_swatch .pewc-radio-image-wrapper label,
      .pewc-item-image_swatch .pewc-checkbox-image-wrapper label {
        width: <?php echo absint( pewc_get_swatch_width() ); ?>px
      }
      .pewc-hex {
        width: <?php echo absint( pewc_get_swatch_width() ); ?>px
      }
      .pewc-circular-swatches .pewc-hex {
        height: <?php echo absint( pewc_get_swatch_width() ); ?>px
      }
    <?php }
    $swatch_color_width = pewc_get_color_swatch_width();
    if( $swatch_color_width ) { ?>
      .pewc-has-hex .pewc-radio-images-wrapper[class*=" pewc-columns-"] .pewc-radio-image-wrapper {
        width: <?php echo absint( $swatch_color_width ); ?>px;
      }
      <?php
      // If the swatch is circular, the height needs to be the same as the width
      if( pewc_get_circular_swatches()) { ?>
        .pewc-hex {
          height: <?php echo absint( $swatch_color_width ); ?>px;
        }
      <?php } ?>
    <?php }
    $swatch_color_height = pewc_get_color_swatch_height();
    if( $swatch_color_height && ! pewc_get_circular_swatches() ) { ?>
      .pewc-hex {
        height: <?php echo absint( $swatch_color_height ); ?>px;
      }
    <?php }
    if( get_theme_mod( 'pewc_products_border', '#2196f3' ) ) { ?>
      .pewc-preset-style .pewc-radio-image-wrapper.checked,
      .pewc-preset-style .pewc-checkbox-image-wrapper.checked,
      .pewc-preset-style .checked .pewc-hex,
      .pewc-preset-style .pewc-radio-image-wrapper:hover,
      .pewc-preset-style .pewc-checkbox-image-wrapper:hover,
      .pewc-preset-style .pewc-radio-image-wrapper:hover .pewc-hex {
        border-color:  <?php echo get_theme_mod( 'pewc_products_border', '#2196f3' ); ?>;
      }
    <?php }
    // if( get_theme_mod( 'pewc_swatch_border_padding', '8' ) ) { ?>
      .pewc-preset-style .pewc-radio-image-wrapper,
      .pewc-preset-style .pewc-checkbox-image-wrapper {
        padding: <?php echo get_option( 'pewc_swatch_border_padding', '8' ); ?>px
      }
    <?php // } ?>
  </style>
  <?php
}
add_action( 'wp_head', 'pewc_customize_css');
