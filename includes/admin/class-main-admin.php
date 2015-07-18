<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Points_Main_Admin {

	public function __construct(){
        add_action( 'wp_dashboard_setup', array( $this, 'add_wod_dashboard_widget' ), 20 );
    }

    public function add_wod_dashboard_widget(){
        wp_add_dashboard_widget(
                 'wod_dashboard_widget',         // Widget slug.
                 'WodTicket',         // Title.
                 'example_dashboard_widget_function' // Display function.
        );	
    }
    
    function example_dashboard_widget_function() {
        echo "Initial dashboard widget.";
    }
}
