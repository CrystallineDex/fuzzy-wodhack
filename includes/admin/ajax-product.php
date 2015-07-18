<?php 

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'wod-add-gym':
            create_gym_product();
            break;
    }
}

// Create a gym with all the necessary settings
function create_gym_product() {
    global $wpdb;

    $post = array(
         'post_title'   => "New Gym",
         'post_status'  => "draft",
         'post_type'    => "product"
     );

    $new_post_id = wp_insert_post( $post, $wp_error );
    wp_redirect( get_permalink( $new_post_id ) );
}
?>