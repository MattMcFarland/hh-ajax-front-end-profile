<?php

/**
 * Plugin Name: Ajax Front End Profile
 * Description: Adds a Front End Profile / Account / Custom Avatar to wordpress.
 * Version: 0.1-alpha
 * Author: Matthew A McFarland
 * Email: mmcfarland@hvac-hacks.com
 * Dependencies: Twitter Bootstrap 3.0.3, jQuery 10x, jQuery UI,
 * License: MIT
 */


/**
 * From the WordPress plugin headers above we derive the version number, and plugin name
 */
$plugin_headers = get_file_data( __FILE__, array( 'Version' => 'Version', 'Name' => 'Plugin Name' ) );


/**
 * We store our plugin data in the following global array.
 * $my_unique_name with your unique name
 */
global $hh_ajax_front_end_profile_name;
$hh_ajax_front_end_profile_name = array();
$hh_ajax_front_end_profile_name['version_key'] = strtolower( str_replace( ' ', '_', $plugin_headers['Name'] ) ) . '_version';
$hh_ajax_front_end_profile_name['version_value'] = $plugin_headers['Version'];


/**
 * When the user activates the plugin we add the version number to the
 * options table as "my_plugin_name_version" only if this is a newer version.
 */
function hh_ajax_front_end_profile_activation(){

    global $hh_ajax_front_end_profile_name;

    if ( get_option( $hh_ajax_front_end_profile_name['version_key'] ) && get_option( $hh_ajax_front_end_profile_name['version_key'] ) > $hh_ajax_front_end_profile_name['version_value'] )
        return;

    update_option( $hh_ajax_front_end_profile_name['version_key'], $hh_ajax_front_end_profile_name['version_value'] );

}
register_activation_hook( __FILE__, 'hh_ajax_front_end_profile_activation' );


/**
 * Delete our version number from the database when the plugin is activated.
 */
function hh_ajax_front_end_profile_deactivate(){
    global $hh_ajax_front_end_profile_name;
    delete_option( $hh_ajax_front_end_profile_name['version_key'] );
}
register_deactivation_hook( __FILE__, 'hh_ajax_front_end_profile_deactivate' );


if ( is_admin() )
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-tags.php';

/**
 * Theme only functions
 */

    require_once plugin_dir_path( __FILE__ ) . 'inc/template-tags.php';
    require_once plugin_dir_path( __FILE__ ) . 'inc/templates/profile-pic.php';
    require_once plugin_dir_path( __FILE__ ) . 'inc/templates/edit-profile.php';


function hh_profile_editor_enqueue_scripts(){

    wp_register_style( 'hh_ajax_fe_profile-style', plugin_dir_url( __FILE__ ) . 'inc/css/profile-edit.min.css' );
    wp_enqueue_style( 'hh_ajax_fe_fileUpload-style', plugin_dir_url( __FILE__ ) . 'inc/css/fileUpload.css' );
    wp_register_script( 'hh_ajax_fe_profile-script', plugin_dir_url( __FILE__ ) . 'inc/js/profile-edit.js', array('jquery'),'b10');
    if ( !is_admin() && is_page('edit-profile') ) {
        wp_enqueue_script( 'hh_ajax_fe_profile-script' );
        wp_enqueue_style( 'hh_ajax_fe_profile-style' );
        wp_localize_script(
            'hh_ajax_fe_profile-script',
            '_hh_ajax_fe_profile',
            array(
                'ajaxurl' => admin_url("admin-ajax.php")
            )
        );
    }

}
add_action('wp_enqueue_scripts', 'hh_profile_editor_enqueue_scripts');


function hh_init_fileUploader_scripts(){
    wp_register_script( 'hh_ajax_fileUploader-script', plugin_dir_url( __FILE__ ) . 'inc/js/fileUploader.js', array('jquery'),'a05');
    if ( !is_admin() && is_page('edit-profile') ) {
        wp_enqueue_script( 'hh_ajax_fileUploader-script' );
        wp_localize_script(
            'hh_ajax_fileUploader-script',
            '_hh_ajax_fileUploader',
            array(
                'ajaxurl' => admin_url("admin-ajax.php")
            )
        );
    }

}
add_action('wp_enqueue_scripts', 'hh_init_fileUploader_scripts');


function hh_init_Jcrop_plugin(){
    $JcropPath = plugin_dir_url( __FILE__ ) . 'inc/vendor/Jcrop/';
    wp_register_style( 'hh_ajax_jcrop-style',  $JcropPath . 'css/jquery.Jcrop.min.css' );
    wp_register_script( 'hh_ajax_jcrop-script',  $JcropPath . 'js/jquery.Jcrop.min.js', array('jquery'),'a05');
    if ( !is_admin() && is_page('edit-profile') ) {
        wp_enqueue_script( 'hh_ajax_jcrop-script' );
        wp_enqueue_style( 'hh_ajax_jcrop-style' );
    }
}
add_action('wp_enqueue_scripts', 'hh_init_Jcrop_plugin');