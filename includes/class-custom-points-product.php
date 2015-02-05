<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * External Product Class
 *
 * External products cannot be bought; they link offsite. Extends simple products.
 *
 * @class 		WC_Product_Points
 * @version		2.0.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */

class WC_Product_Points extends WC_Product {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $product
	 */
	public function __construct( $product ) {
		$this->product_type = 'points';
		parent::__construct( $product );

	}

    public function is_purchasable() {
		return apply_filters( 'woocommerce_is_purchasable', false, $this );
    }

    private function deduct_points( $post_id ){

        global $wpdb;
        $points_to_deduct = get_post_meta($post_id, 'points_product_cost');

        // remove points
        WC_Points_Rewards_Manager::decrease_points( get_current_user_id(), $points_to_deduct[0], 'point-product-reward');

    }

    public function process_points( $post_id ){
        $this->deduct_points( $post_id );



    }
}
