<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Points_Order {

	public function __construct(){

    }

    public static function get_order_points_cost_total( $order ){

        $points_total = 0;

        foreach( $order->get_items() as $item ){
            $cost = get_post_meta($item['product_id'], 'points_product_cost');
            $points_total += $cost[0];
        }

        return $points_total;
    }
    
    public static function get_cart_points_cost_total(){

        $points_total = 0;

        foreach( WC()->cart->get_cart() as $item ){
            $product = $item['data'];
            $cost = get_post_meta( $product->id, 'points_product_cost' );
            $points_total += $cost[0];
        }

        return $points_total;
    }
}
