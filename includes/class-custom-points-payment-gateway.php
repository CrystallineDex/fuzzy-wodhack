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

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'pointspayment';
		$this->method_title       = __( 'Points Payment', 'custom-points-product' );
		$this->method_description = __( 'This gateway takes points as a currency.', 'custom-points-product' );
		$this->supports           = array(
			'products'
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		$this->title        = $this->settings['title'];

		$description = sprintf( __( "Available balance: %s", 'custom-points-product'), WC_Points_Rewards_Manager::get_users_points(get_current_user_id()) );

		$this->description = $description;

		// Subscriptons
		add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 3 );
		add_filter( 'woocommerce_my_subscriptions_recurring_payment_method', array( $this, 'subscription_payment_method_name' ), 10, 3 );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Check if the gateway is available for use
	 *
	 * @return bool
	 */
	public function is_available() {
		$is_available = ( 'yes' === $this->enabled ) ? true : false;

		if ( WC_Points_Rewards_Manager::get_users_points(get_current_user_id()) <= 0 ) {
			$is_available = false;
		}

		return $is_available;
	}

	/**
	 * Settings
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woothemes' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable', 'woothemes' ),
				'default' => 'yes'
			),
			'title' => array(
				'title'       => __( 'Title', 'woothemes' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woothemes' ),
				'default'     => __( 'Points Payment', 'custom-points-product' )
			)
		);
	}

	/**
	 * Process a payment
	 */
	public function process_payment( $order_id ) {
		$order  = wc_get_order( $order_id );

		if ( ! is_user_logged_in() ) {
			wc_add_notice( __( 'Payment error:', 'custom-points-product' ) . ' ' . __( 'You must be logged in to use this payment method', 'custom-points-product' ), 'error' );
			return;
		}
        
        $user_id = $order->get_user_id();
        $available_points = WC_Points_Rewards_Manager::get_users_points( $user_id );
        $total_points = Custom_Points_Order::get_order_points_cost_total( $order );

		if ( $available_points < $total_points ) {
			wc_add_notice( __( 'Payment error:', 'custom-points-product' ) . ' ' . __( 'Insufficient points in your account.', 'custom-points-product' ), 'error' );
			return;
		}

		// deduct points from account
		WC_Points_Rewards_Manager::decrease_points( $user_id, $total_points, 'custom-points-gateway' );
        
		$order->set_total( 0 );

		// Payment complete
		$order->payment_complete();

		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'    => 'success',
			'redirect'  => $this->get_return_url( $order )
		);
	}
}
