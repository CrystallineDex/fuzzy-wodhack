<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Points_Main_Admin {

	public function __construct(){
        // Remove all other dashboard widgets for ease of use
        add_action( 'admin_init', array( $this, 'remove_dashboard_meta' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_wod_dashboard_widget' ), 20 );
    }

    public function add_wod_dashboard_widget(){
        wp_add_dashboard_widget(
             'wod_dashboard_widget',         // Widget slug.
             'WodTicket',         // Title.
             array( $this, 'wod_dashboard_widget_function' ) // Display function.
        );	
    }
    
    public function wod_dashboard_widget_function() {
        echo "Initial dashboard widget.";
    }
    
    public function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
        remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal' );
        remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'normal' );
    }

}
