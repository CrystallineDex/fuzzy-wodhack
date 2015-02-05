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

    public function process_points( $post_id ){
        $points_to_deduct = get_post_meta($post_id, 'points_product_cost');

        // Add conditional for if user has 0 points or less.
        $this->deduct_points( $post_id, $points_to_deduct[0] );

        $order = $this->create_order_info( $post_id, $points_to_deduct );

    }

    private function create_order_info( $product_id, $points ){
        $order = wc_create_order( array( 'customer_id' => get_current_user_id() ) );

        $order->add_product( get_product( $product_id ), 1 );

        return $order;
    }

    private function create_code(){
        $unique_code = $this->randomNumber(6);

        return $unique_code;
    }

    private function deduct_points( $post_id, $points ){
        // remove points
        WC_Points_Rewards_Manager::decrease_points( get_current_user_id(), $points, 'point-product-reward');
    }

    private function get_vendor_id( $product_id ){
        $author = WCV_Vendors::get_vendor_from_product( $product_id );

        return $author;
    }

    private function randomNumber($length) {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }
}
