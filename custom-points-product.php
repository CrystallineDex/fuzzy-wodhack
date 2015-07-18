<?php
/*
 * Plugin Name: WOD Engine
 * Version: 1.0
 * Plugin URI: http://www.crystalline-design.com
 * Description: Required features for WOD to work properly.
 * Author: Alex Ritchey
 * Author URI: http://www.crystalline-design.com
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wod-engine
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Alex Ritchey
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'woothemes_queue_update' ) || ! function_exists( 'is_woocommerce_active' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

define('plugin_dir_path', dirname(__FILE__) );
define( 'YOUR_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

class WC_Custom_Points {

  public function __construct() {
    // called just before the woocommerce template functions are included
    add_action( 'init', array( $this, 'include_template_functions' ), 20 );

    // called only after woocommerce has finished loading
    add_action( 'woocommerce_init', array( $this, 'woocommerce_loaded' ) );

    // called after all plugins have loaded
    add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

    // indicates we are running the admin
    if ( is_admin() ) {
      require_once( plugin_dir_path . '/includes/admin/class-main-admin.php' );
      $adminDashboard = new Custom_Points_Main_Admin();
    }
  }

  /**
   * Override any of the template functions from woocommerce/woocommerce-template.php
   * with our own template functions file
   */
  public function include_template_functions() {
  }

  /**
   * Take care of anything that needs woocommerce to be loaded.
   * For instance, if you need access to the $woocommerce global
   */
  public function woocommerce_loaded() {

  }

    /**
    * Add the Gateway to WooCommerce
    **/
    public function woocommerce_add_points_gateway($methods) {
        $methods[] = 'WC_Points_Payment';
        return $methods;
    }

  /**
   * Take care of anything that needs all plugins to be loaded
   */
  public function plugins_loaded() {

      // Load plugin class files
      require_once( plugin_dir_path . '/includes/class-custom-points-product.php' );
      require_once( plugin_dir_path . '/includes/class-custom-points-product-settings.php' );
      require_once( plugin_dir_path . '/includes/class-custom-points-order.php' );
      require_once( plugin_dir_path . '/includes/class-custom-points-payment-gateway.php' );

      $settings = new Custom_Points_Product_Settings();

      add_action('woocommerce_points_add_to_cart', array($this, 'add_to_cart'),30);
      add_filter('woocommerce_payment_gateways', array($this, 'woocommerce_add_points_gateway') );
  }


    public function add_to_cart() {
        wc_get_template( 'single-product/add-to-cart/points.php',$args = array(), $template_path = '', YOUR_TEMPLATE_PATH);
}

}

// finally instantiate our plugin class and add it to the set of globals

$GLOBALS['wc_custom_points'] = new WC_Custom_Points();
