<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Points Payment Gateway Class
 *
 * @class 		WC_Product_Points
 * @version		2.0.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */

/**
 * Gateway class
 */
class WC_Points_Payment extends WC_Payment_Gateway {

    public function __construct(){
        $this->id = 'points_payment';
        $this->method_title = 'Points Payment';
        $this->method_description = 'Processing for using points as a currency.';

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option( 'title' );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function init_form_fields(){
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'woocommerce' ),
                'type' => 'checkbox',
                'label' => __( 'Enable Points Payment', 'woocommerce' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __( 'Title', 'woocommerce' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default' => __( 'Points Payment', 'woocommerce' ),
                'desc_tip'      => true,
            )
        );
    }

    function process_payment( $order_id ) {
        global $woocommerce;
        $order = new WC_Order( $order_id );

        // Mark as on-hold (we're awaiting the cheque)
        $order->update_status('on-hold', __( 'Awaiting cheque payment', 'woocommerce' ));

        // Reduce stock levels
        $order->reduce_order_stock();

        // Remove cart
        $woocommerce->cart->empty_cart();

        // Return thankyou redirect
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }

}
