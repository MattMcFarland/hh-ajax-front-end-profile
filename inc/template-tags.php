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

    add_action( 'wp_ajax_nopriv_hh_update_user_profile', 'hh_update_user_profile' );
    add_action( 'wp_ajax_hh_update_user_profile', 'hh_update_user_profile' );
    add_action( 'wp_ajax_nopriv_hh_ajax_load_profile_editor', 'hh_ajax_load_profile_editor' );
    add_action( 'wp_ajax_hh_ajax_load_profile_editor', 'hh_ajax_load_profile_editor' );
    add_action( 'wp_ajax_nopriv_hh_save_profile_pic', 'hh_save_profile_pic' );
    add_action( 'wp_ajax_hh_save_profile_pic', 'hh_save_profile_pic' );
}
add_action('plugins_loaded', 'hh_ajax_fe_profile_loaded');


/**
 * Update a profile field via ajax.
 */
function hh_update_user_profile() {

    header('Content-type: application/json');
    global $current_user;
    if ( !wp_verify_nonce( $_POST['nonce'], "hh_update_user_profile_nonce")) {
        exit("No naughty business please");
    }
    $meta_key = sanitize_text_field($_POST['meta_key']);
    $meta_value = sanitize_text_field($_POST['meta_value']);
    $meta_value = trim($meta_value);
    update_user_meta($current_user->ID,$meta_key,$meta_value);
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
 * Loads the profile editor via Ajax
 */
function hh_ajax_load_profile_editor() {
    global $current_user; ?>
    <div id = "hh_update_profile" class="container" data-processing-uri="<?php echo plugin_dir_url( __FILE__ ) . 'img/processing.gif '?>">

        <div class="row" style="padding:1em">
            <?php hh_fe_notify('Edit Profile','Click on whatever you wish to change'); ?>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <div id="hh_pfe-avatar" class="well well-inverse">
                    <div style="margin-bottom:1em">
                        <h2 style="display:inline-block;margin-right:0.4em;">Profile Photo</h2>
                            <span style="display:inline-block; border: thin dotted #999; padding:4px;">
                                <i class="fa fa-info-circle" style="color:#effe5c"> </i>
                                <em style="color:#aaa;"> Click on your photo to change it...</em>
                            </span>
                    </div>
                    <div>
                        <?php if (get_the_author_meta( 'profile_pic', $current_user->ID ) =="") hh_download_gravatar($current_user->ID ); ?>
                        <a type="button" style="display:inline-block;" id = "hh-change-profile-photo">
                            <img class="thumbnail" style="margin:0;display:inline-block; width:150px; height:150px;" src="<?php the_author_meta( 'profile_pic', $current_user->ID ); ?>"/>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div id="hh_pfe-name" class="well well-inverse">
                    <h2>Basic Info</h2><br/>
                    <?php hh_generate_text_field('Display Name','display_name'); ?>
                    <?php hh_generate_text_field('First Name','first_name'); ?>
                    <?php hh_generate_text_field('Last Name','last_name'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="hh_pfe-details" class="well well-inverse">
                    <h2>Details</h2><br/>
                    <?php hh_generate_text_field('Website','user_url'); ?>
                    <?php do_action('hh_add_form_fields') ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo hh_file_upload_ui();
    die();

}

/**
 * Save action after clicking save in cropping tool view.
 */
function hh_save_profile_pic() {
    header('Content-type: application/json');
    var_dump ($_POST);
    var_dump ($_FILES);


    global $current_user;
    if ( !wp_verify_nonce( $_POST['nonce'], "hh_save_profile_pic_nonce")) {
        exit("No naughty business please");
    }

    $valid_exts = array('jpeg', 'jpg', 'png', 'gif' ); // valid extensions
    $file = str_replace(' ', '_',$_FILES['fileUpload-file']['tmp_name']);
    // get uploaded file extension
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $max_size = 1048576;
    if (in_array($ext, $valid_exts) AND $_FILES['hvac-hacks-post-file']['size'] < $max_size)
    {
        $x1 = $_POST['x1'];
        $y1 = $_POST['y1'];
        $x2 = $_POST['x2'];
        $y2 = $_POST['y2'];

        $upload_dir =wp_upload_dir();

        $oldFile = $_FILES['fileUpload-file']['tmp_name'];
        $newFile = $upload_dir['path'].'/id_'.$current_user->ID.'_pic.jpg';

        exec('/usr/local/bin/convert -crop '.$x1.'x'.$y1.' +'.$x2.'+'.$y2.' -auto-orient -strip -interlace Plane -resize 150x150^ -gravity center -extent 150x150 -quality 62 -format jpg '.$oldFile.' '.$newFile.' 2>&1',$error);
        $meta_value = $upload_dir['url'].'/'.basename($newFile);
        $meta_key = 'profile_pic';
        update_user_meta($current_user->ID,$meta_key,$meta_value);
        wp_update_user( array ( 'ID' => $current_user->ID, $_POST['meta_key'] =>$_POST['meta_value']) ) ;
        $result['type'] = "success";
        $result['new_value'] = get_the_author_meta( $_POST['meta_key'], $current_user->ID);  //gets saved value from server
    } else {
        $result['type'] = "fail";
        $result['message'] = "Incorrect file extension or file size too big.";
    }

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
 * hh notify, requires bootstrap to look decent.
 * @param $title
 * @param $message
 * @param string $type
 */
function hh_fe_notify($title,$message,$type='info') {
    echo '
        <div style="margin:1em;" class="alert alert-'.$type.' alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <p>
        <strong>'.$title.'</strong> - <span>'.$message.'</span>
        </p>
        </div>
    ';
}

