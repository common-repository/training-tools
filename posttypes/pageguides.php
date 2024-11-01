<?php
/** 
 * WP Training Tools Custom Post Type: Page Guides
 * 
 * @package Training Tools
 * @author Ryan Bayne
 * 
 * Each post represents a guide for a specific page (can be used for per view
 * where different views are used within a page) on a plugin or theme. Eventually
 * multiple page guides will be used to build tutorials or simply enhance
 * the information available within a tutorial by referencing the guides
 * to pages shown in the tutorial. 
 * 
 * The plan is for these things to happen automatically/intelligently. 
 * 
 * Main post content is optional. Not using it will force the custom post type
 * template to use a default structure relying on meta. Using it will avoid
 * the default structure unless user has opted to use both i.e. a very short
 * tutorial followed by the default page structure that may include more
 * technical information. 
 * 
 * @todo add ability to collect technical form data from any plugin and paste it 
 * into the form guide builder view. This can be achieved using the form 
 * registration system be capturing the form and input IDs, names and validation. 
 */

// load in WordPress only
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

add_action( 'init', 'trainingtools_init_posttype_pageguides' );
add_action( 'add_meta_boxes', 'trainingtools_add_meta_boxes_pageguides' );
add_action( 'save_post', 'trainingtools_save_meta_boxes_pageguides',10,2 );

/**
* Must be called using add_action( 'init', 'trainingtools_register_customposttype_contentdesigns' )
* Registers custom post type for content only
*/
function trainingtools_init_posttype_pageguides() {
    $labels = array(
        'name' => _x( 'TT Page Guides', 'post type general name' ),
        'singular_name' => _x( 'TT Page Guides', 'post type singular name' ),
        'add_new' => _x( 'Create', 'wtgcsvcontent' ),
        'add_new_item' => __( 'Create Page Guide' ),
        'edit_item' => __( 'Edit Page Guide' ),
        'new_item' => __( 'Create Page Guide' ),
        'all_items' => __( 'All Page Guides' ),
        'view_item' => __( 'View Page Guides' ),
        'search_items' => __( 'Search Page Guides' ),
        'not_found' =>  __( 'No page guides found' ),
        'not_found_in_trash' => __( 'No page guides found in Trash' ), 
        'parent_item_colon' => '',
        'menu_name' => 'Page Guides'
    );
    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => true, 
        'hierarchical' => true,
        'menu_position' => 100,
        'supports' => array( 'title', 'editor', 'custom-fields' )
    );   

    register_post_type( 'ttpageguides', $args );
}

/**
* Adds meta boxes for post posts type 
*/
function trainingtools_add_meta_boxes_pageguides() {
    //global $trainingtools_settings;
    // author adsense (allows WordPress authors or users with publish_posts ability to add their own adsense snippet)
    //if( $trainingtools_settings['monetizesection']['adsense']['authoradsense']['switch'] == 'enabled' && current_user_can( 'publish_posts' ) ){
    //    add_meta_box(
    //        'posts-meta-authoradsense',
    //        esc_html__( 'Author AdSense' ),
    //        'trainingtools_metabox_authoradsense',
    //        'post',
    //        'side',
    //        'default'
    //    );
    //}
}

/**
* Tickets Meta Box: authoradsense  
*/
function trainingtools_metabox_authoradsense( $object, $box ) { 
    //wp_nonce_field( basename( __FILE__ ), 'postnonce' );
    //$value = get_post_meta( $object->ID, '_trainingtools_authoradsense', true );
    //echo '<textarea id="c2pauthoradsense" name="trainingtools_post_authoradsense" rows="5" cols="30">'.$value.'</textarea>'; 
} 

/**
* Save meta box's for post posts type
*/
function trainingtools_save_meta_boxes_guides( $post_id, $post ) {    
    global $wpdb, $trainingtools_settings;
    
    $flagmeta_array = array(
        'authoradsense'
    );
    
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['postnonce'] ) || !wp_verify_nonce( $_POST['postnonce'], basename( __FILE__ ) ) )    
        return $post_id;
        
    // check autosave
    if (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {
        return $post_id;
    }
 
    // check permissions
    if ( (key_exists( 'post_type', $post) ) && ( 'post' == $post->post_type) ) {
        if (!current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } elseif (!current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }        

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ){
        return $post_id;
    }
 
    // loop through our terms and meta functions
    foreach( $flagmeta_array as $key => $term)
    {  
        $new_meta_value = '';
        $sent_emails = false;
        $email_recipients_string = '';// comma seperated email address
         
        /* Get the meta key. */
        $meta_key = '_trainingtools_' . $term;
                                            
        if( isset( $_POST['trainingtools_post_'.$term] ) )
        {
            $new_meta_value = $_POST['trainingtools_post_'.$term];    
        }

        /* Get the meta value of the custom field key. */
        $old_meta_value = get_post_meta( $post_id, $meta_key, true );

        if ( $new_meta_value && '' == $old_meta_value )
        {
            add_post_meta( $post_id, $meta_key, $new_meta_value, true );# new meta value was added and there was no previous value
        }
        elseif ( $new_meta_value && $new_meta_value != $old_meta_value )
        {
            update_post_meta( $post_id, $meta_key, $new_meta_value );# new meta value does not match the old value, update it
        }
        elseif ( '' == $new_meta_value && $old_meta_value )
        {
            delete_post_meta( $post_id, $meta_key, $old_meta_value );# no new meta value but an old value exists, delete it
        }
    }
}  
?>
