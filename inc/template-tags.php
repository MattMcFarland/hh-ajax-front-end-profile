<?php


/**
 * Perform the following actions/filters when plugins are loaded
 *
 * @since 0.1-alpha
 */
function hh_ajax_fe_profile_loaded(){
    global $current_user;
    if (!isset($current_user))
        $current_user = wp_get_current_user();

    if (empty($current_user))
        $current_user = wp_get_current_user();

    add_action( 'wp_ajax_inline_comments_add_comment', 'inline_comments_add_comment' );
    add_action( 'wp_ajax_nopriv_inline_comments_add_comment', 'inline_comments_add_comment' );
    add_action( 'wp_ajax_nopriv_inline_comments_load_template', 'inline_comments_load_template' );
    add_action( 'wp_ajax_inline_comments_load_template', 'inline_comments_load_template' );
    add_filter( 'template_redirect', 'inline_comments_template_redirect' );
}
add_action('plugins_loaded', 'hh_ajax_fe_profile_loaded');


/**
 * Load our JavaScript and Stylesheet on single page only
 *
 * @since 0.1-alpha
 */
function hh_ajax_fe_profile_redirect() {
    add_action( 'wp_enqueue_scripts', 'inline_comments_scripts');
    add_action( 'wp_head', 'inline_comments_head');
}


/**
 * Load our JavaScript and Stylesheet, we include the login-register script only if it is installed.
 *
 * @uses wp_enqueue_script()
 * @uses wp_enqueue_style()
 *
 * @since 0.1-alpha
 */
function hh_ajax_fe_profile_scripts(){
    wp_enqueue_script( 'hh_ajax_fe_profile-script' );
    wp_enqueue_style( 'hh_ajax_fe_profile-style' );
    wp_localize_script(
        'hh_ajax_fe_profile',
        '_hh_ajax_fe_profile',
        array(
            'ajaxurl' => admin_url("admin-ajax.php")
        )
    );
}



/**
 * Update a profile field via ajax.
 */
function hh_update_user_profile() {

    header('Content-type: application/json');
    global $current_user;
    if ( !wp_verify_nonce( $_POST['nonce'], "hh_update_user_profile_nonce")) {
        exit("No naughty business please");
    }
    wp_update_user( array ( 'ID' => $current_user->ID, $_POST['meta_key'] =>$_POST['meta_value']) ) ;
    $result['type'] = "success";
    $result['new_value'] = get_the_author_meta( $_POST['meta_key'], $current_user->ID);  //gets saved value from server

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    }
    else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }

die();

}

/**
 * Get profile data via ajax.
 */
function hh_get_profile_data() {

 die();
}

/**
 * hh notify, requires bootstrap to look decent.
 * @param $title
 * @param $message
 * @param string $type
 */
if (!function_exists('hh_notify')) {
    function hh_notify($title,$message,$type='info') {
        echo '
            <div style="margin:1em;" class="alert alert-'.$type.' alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p>
            <strong>'.$title.'</strong> - <span>'.$message.'</span>
            </p>
            </div>
        ';
    }
}

