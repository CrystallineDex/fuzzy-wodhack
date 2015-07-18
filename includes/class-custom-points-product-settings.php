<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Points_Product_Settings {

	public function __construct(){
        add_filter( 'product_type_selector' , array( $this, 'add_points_product_type' ) );
        add_action( 'woocommerce_product_options_sku', array( $this, 'points_add_custom_settings' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'points_save_custom_settings' ) );
    }

    public function add_points_product_type( $types ){
      $types[ 'points' ] = __( 'Points Product' );
      return $types;
    }

    // add the settings under ‘General’ sub-menu

    public function points_add_custom_settings() {
        global $woocommerce, $post;
        echo '<div class="options_group show-if-points">';

        // Commission Price pulled from WooCommerce Core
        woocommerce_wp_text_input( array( 'id' => '_regular_price', 'label' => __( 'Commission Price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')', 'data_type' => 'price' ) );

        // Create a number field, for example for UPC
        woocommerce_wp_text_input(
          array(
           'id'                => 'points_product_cost',
           'label'             => __( 'Points Cost', 'woocommerce' ),
           'placeholder'       => '',
           'desc_tip'    => 'true',
           'description'       => __( 'Enter the amount of points this product costs.', 'woocommerce' ),
           'type'              => 'number'
           ));

        // Individual product
        woocommerce_wp_checkbox( array( 'id' => '_sold_individually', 'label' => __( 'Sold Individually', 'woocommerce' ), 'description' => __( 'Enable this to only allow one of this item to be bought in a single order', 'woocommerce' ) ) );

        echo '</div>';

    }

    public function points_save_custom_settings( $post_id ){
        // save Point Cost field
        $points_product_cost = $_POST['points_product_cost'];
        if( !empty( $points_product_cost ) )
        update_post_meta( $post_id, 'points_product_cost', esc_attr( $points_product_cost) );
        }
    }
