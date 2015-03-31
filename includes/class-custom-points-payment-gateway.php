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
		$wcaf_settings      = get_option( 'wcaf_settings' );

		$description = sprintf( __( "Available balance: %s", 'woocommerce-account-funds'), WC_Account_Funds::get_account_funds() );

		if ( 'yes' === get_option( 'account_funds_give_discount' ) ) {
			$amount      = floatval( get_option( 'account_funds_discount_amount' ) );
			$amount      = 'fixed' === get_option( 'account_funds_discount_type' ) ? wc_price( $amount ) : $amount . '%';
			$description .= '<br/><em>' . sprintf( __( 'Use your account funds and get a %s discount on your order.', 'woocommerce-account-funds' ), $amount ) . '</em>';
		}

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

		if ( WC_Account_Funds_Cart_Manager::cart_contains_deposit() || WC_Account_Funds_Cart_Manager::using_funds() ) {
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
				'default'     => __( 'Account Funds', 'woocommerce-account-funds' )
			)
		);
	}

	/**
	 * Process a payment
	 */
	public function process_payment( $order_id ) {
		$order  = wc_get_order( $order_id );

		if ( ! is_user_logged_in() ) {
			wc_add_notice( __( 'Payment error:', 'woocommerce-account-funds' ) . ' ' . __( 'You must be logged in to use this payment method', 'woocommerce-account-funds' ), 'error' );
			return;
		}

		$available_funds = WC_Account_Funds::get_account_funds( $order->get_user_id(), false, $order_id );

		if ( $available_funds < $order->get_total() ) {
			wc_add_notice( __( 'Payment error:', 'woocommerce-account-funds' ) . ' ' . __( 'Insufficient account balance', 'woocommerce-account-funds' ), 'error' );
			return;
		}

		// deduct amount from account funds
		WC_Account_Funds::remove_funds( $order->get_user_id(), $order->get_total() );
		update_post_meta( $order_id, '_funds_used', $order->get_total() );
		update_post_meta( $order_id, '_funds_removed', 1 );
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

	/**
	 * @param float $amount
	 * @param WC_Order $order
	 * @param int $product_id
	 * @return bool|WP_Error
	 */
	public function scheduled_subscription_payment( $amount, $order, $product_id ) {
		$order_items        = $order->get_items();
		$product            = $order->get_product_from_item( array_shift( $order_items ) );
		$subscription_name  = sprintf( __( 'Subscription for "%s"', 'woocommerce-account-funds' ), $product->get_title() ) . ' ' . sprintf( __( '(Order %s)', 'woocommerce-account-funds' ), $order->get_order_number() );
		$user_id            = $order->get_user_id();
		$error              = false;

		if ( ! $user_id ) {
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
			return new WP_Error( 'accountfunds', __( 'Customer not found', 'woocommerce-account-funds' ) );
		}

		$funds = WC_Account_Funds::get_account_funds( $user_id );

		if ( $amount > $funds ) {
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
			return new WP_Error( 'accountfunds', __( 'Insufficient funds', 'woocommerce-account-funds' ) );
		}

		WC_Account_Funds::remove_funds( $order->get_user_id(), $amount );
		WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );

		$order->add_order_note( __( 'Account Funds subscription payment completed', 'woocommerce-account-funds' ) );

		return true;
	}

	/**
	 * Payment method name
	 */
	public function subscription_payment_method_name( $payment_method_to_display, $subscription_details, $order ) {
		if ( $this->id !== $order->recurring_payment_method || ! $order->customer_user ) {
			return $payment_method_to_display;
		}
		return sprintf( __( 'Via %s', 'woocommerce-account-funds' ), $this->method_title );
	}
}
