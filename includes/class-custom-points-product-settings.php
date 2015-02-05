<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Points_Product_Settings {

	public function __construct(){
        add_filter( 'product_type_selector' , array( $this, 'add_points_product_type' ) );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'points_add_custom_settings' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'points_save_custom_settings' ) );
    }

    public function add_points_product_type( $types ){
      $types[ 'points' ] = __( 'Points Product' );
      return $types;
    }

    // add the settings under ‘General’ sub-menu

    public function points_add_custom_settings() {
        global $woocommerce, $post;
        echo '<div class="options_group">';

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

        woocommerce_wp_text_input(
          array(
           'id'                => 'points_product_commission',
           'label'             => __( 'Product Commission', 'woocommerce' ),
           'placeholder'       => '',
           'desc_tip'    => 'true',
           'description'       => __( 'Enter the amount of money earned by the vendor.', 'woocommerce' ),
           'type'              => 'number'
           ));

        echo '</div>';
    }

    public function points_save_custom_settings( $post_id ){
        // save Point Cost field
        $points_product_cost = $_POST['points_product_cost'];
        if( !empty( $points_product_cost ) )
        update_post_meta( $post_id, 'points_product_cost', esc_attr( $points_product_cost) );

        $points_product_commission = $_POST['points_product_commission'];
        // save purchasable option
        if( !empty( $points_product_commission ) )
        update_post_meta( $post_id, 'points_product_commission', esc_attr( $points_product_commission ) );
    }
    }
