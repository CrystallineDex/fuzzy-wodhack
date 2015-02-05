<?php
/*
 * Plugin Name: Custom Points Product
 * Version: 1.0
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: custom-points-product
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
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
      // ...
    }

    // indicates we are being served over ssl
    if ( is_ssl() ) {
      // ...
    }

    // take care of anything else that needs to be done immediately upon plugin instantiation, here in the constructor
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
   * Take care of anything that needs all plugins to be loaded
   */
  public function plugins_loaded() {

      // Load plugin class files
      require_once( plugin_dir_path . '/includes/class-custom-points-product.php' );
      require_once( plugin_dir_path . '/includes/class-custom-points-product-settings.php' );

      $settings = new Custom_Points_Product_Settings();

      add_action('woocommerce_points_add_to_cart', array($this, 'add_to_cart'),30);

  }


    public function add_to_cart() {
        wc_get_template( 'single-product/add-to-cart/points.php',$args = array(), $template_path = '', YOUR_TEMPLATE_PATH);
}
}

// finally instantiate our plugin class and add it to the set of globals

$GLOBALS['wc_custom_points'] = new WC_Custom_Points();