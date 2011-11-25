<?php
/*
Plugin Name: WP Advanced Page Style
Plugin URI: http://newage.pc.org.ua/wpaps
Description: A brief description of the Plugin.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Denis Derbenyov
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
?>
<?php 

add_action('init', 'codex_custom_init');
function codex_custom_init() 
{
  $labels = array(
    'name' => _x('Adv Page Style', 'post type general name'),
    'singular_name' => _x('Adv Page Style', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Adv Page Style'),
    'edit_item' => __('Edit Adv Page Style'),
    'new_item' => __('New Adv Page Style'),
    'all_items' => __('All Adv Page Style'),
    'view_item' => __('View Adv Page Style'),
    'search_items' => __('Search Adv Page Style'),
    'not_found' =>  __('No Adv Page Style found'),
    'not_found_in_trash' => __('No Adv Page Style found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Adv Page Style'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title','editor')
  ); 
  register_post_type( 'adv-page-style', $args );
}


/**
 * Add meta box
 */
add_action( 'add_meta_boxes', 'advps_add_custom_box' );
function advps_add_custom_box(){
    add_meta_box('advps_page_template', __('Page Template','advps'),'advps_inner_custom_box','adv-page-style','normal','default' );
}
function advps_inner_custom_box($post){
    $advps_datas = get_post_meta($post->ID, 'advps_datas',true);
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'advps_noncename' );
    echo '<input type="text" id="advps_field" name="advps_field" value="',$advps_datas,'" size="25" />';
}

/* Do something with the data entered */
add_action( 'save_post', 'advps_save_postdata' );
function advps_save_postdata($post_id){
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
    
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['advps_noncename'], plugin_basename( __FILE__ ) ) )
        return;
    
     // Check permissions
    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    }
    else{
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    $mydata = $_POST['advps_field'];
    update_post_meta($post_id, 'advps_datas', $mydata);
}