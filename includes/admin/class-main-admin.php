<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Points_Main_Admin {

	public function __construct(){
        // Remove all other dashboard widgets for ease of use
        add_action( 'admin_init', array( $this, 'remove_dashboard_meta' ) );
        wp_enqueue_style('wod_admin_style', plugins_url('styles/admin.css', __FILE__));
        add_action( 'wp_dashboard_setup', array( $this, 'add_wod_dashboard_widget' ), 20 );
    }

    public function add_wod_dashboard_widget(){
        wp_add_dashboard_widget(
             'wod_dashboard_widget',         // Widget slug.
             'WodTicket',         // Title.
             array( $this, 'wod_dashboard_widget_function' ) // Display function.
        );	
    }
    
    
    // Create the custom WOD dashboard widget for easy client usage
    public function wod_dashboard_widget_function() {
        
        $sections = $this->wod_dashboard_widget_builder();
        
        foreach($sections as $section){
            echo '<h4><strong>' . $section['title'] . '</strong></h4><hr>';
            echo '<ul class="actions">';
            
            $actions = $section['actions'];
            
            foreach($actions as $action){
                echo '<li><a class="button ' . $action['class'] . '" href="' . $action['url'] . '">'. $action['title'] . '</a></li>';
            }
            
            echo '</ul>';
        }
        
        $script = '<script>
                jQuery(".wod-add-gym").click( function(e){
                    e.preventDefault();
                    console.log("HI MOM");
                    var value = "wod-add-gym",
                        ajaxUrl = "' . plugins_url('ajax-product.php', __FILE__) . '",
                        data = { "action" : value };
                        
                        jQuery.post(ajaxUrl, data, function(response){
                            console.log("Response");
                        });
                    
                });
            </script>';
        
        echo $script;
    }
    
    // Populate data for custom WOD dashboard widget
    private function wod_dashboard_widget_builder(){
        
        $sections = array(
            array(
                'title' => 'Gyms',
                'actions' => array(
                    array(
                        'title' => 'View Gyms',
                        'url' => admin_url('edit.php?product_cat=cfgyms&post_type=product'),
                        'class' => ''
                    ),
                    array(
                        'title' => 'Add Gym',
                        'url' => '',
                        'class' => 'wod-add-gym'
                    )
                )
            ),
            array(
                'title' => 'Members',
                'actions' => array(
                    array(
                        'title' => 'Manage Points',
                        'url' => admin_url('admin.php?page=woocommerce-points-and-rewards'),
                        'class' => ''
                    ),
                    array(
                        'title' => 'View Points Log',
                        'url' => admin_url('admin.php?page=woocommerce-points-and-rewards&tab=log'),
                        'class' => ''
                    )
                )
            ),
            array(
                'title' => 'WOD',
                'actions' => array()
            ),  
        );
        
        return $sections;
    }
    
    
    // Removes a bunch of dashboard widgets
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
    
    
    
    // Create a gym with all the necessary settings
    public function create_gym_product() {
        global $wpdb;
        
        $post = array(
             'post_title'   => "New Gym",
             'post_status'  => "draft",
             'post_type'    => "product"
         );
        
        $new_post_id = wp_insert_post( $post, $wp_error );
        wp_redirect( get_permalink( $new_post_id ) );
    }

}
