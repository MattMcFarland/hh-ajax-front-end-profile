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
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-tags.inc';

/**
 * Theme only functions
 */
if ( !is_admin() )
    require_once plugin_dir_path( __FILE__ ) . 'inc/template-tags.inc';


function inline_comments_enqueue_scripts(){

    wp_register_style( 'hh_profile_editor-style', plugin_dir_url( __FILE__ ) . 'inc/css/profile-edit.min.css' );
    wp_register_script( 'hh_profile_editor-script', plugin_dir_url( __FILE__ ) . 'inc/js/profile-edit.js', array('jquery'),'a01');

}
add_action('wp_enqueue_scripts', 'hh_profile_editor_enqueue_scripts', 2);