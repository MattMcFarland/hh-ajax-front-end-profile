<?php


function hh_generate_text_field($label, $meta_key) {
    $nonce = wp_create_nonce('hh_update_user_profile_nonce');
    global $current_user;
    echo '
<form action="" method="post" data-meta_key="'.$meta_key.'" enctype="multipart/form-data" class="hh-update-profile" id="update-'.$meta_key.'">
    <div id = "main-'.$meta_key.'" data-nonce = "' . $nonce . '" data-meta_key="'.$meta_key.'" class="row edit-field">
        <div id="label-'.$meta_key.'" class="col-xs-5">
            <span class="pr-label pull-right">'.$label.'</span>
        </div>
        <div id="pad-'.$meta_key.'" class="col-xs-7 pr-field">
                <span id="swap-'.$meta_key.'">
                    <span id ="data-'.$meta_key.'">'. get_the_author_meta( $meta_key, $current_user->ID ).'</span>
                    <span class="pr-change"><i class="fa fa-pencil"></i></span>
                </span>
            <div class="pr-input-wrap" class="col-xs-12" id="'.$meta_key.'">
                <input id="input-'.$meta_key.'" name="input-'.$meta_key.'" type="text" value="'.get_the_author_meta( $meta_key, $current_user->ID ).'">
            </div>
        </div>
        <div class="pr-message col-xs-7" id ="message-'.$meta_key.'"></div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="pr-control"  id = "controls-'.$meta_key.'" >
                    <span class="pull-right">
                    <button type="submit" role="button" class="btn btn-success btn-sm pr-save"><i class="fa fa-check"></i> Save</a>
                        <button data-meta_key="'.$meta_key.'" type="button" role="button" id ="cancel-'.$meta_key.'" class="btn btn-danger btn-sm pr-cancel"><i class="fa fa-times-circle"></i> Cancel</button>
                    </span>
            </div>
        </div>
    </div>
</form>
';
}

/**
 * Loads the profile editor via Ajax
 */
function hh_ajax_load_profile_editor() {
    global $current_user; ?>
    <div id = "hh_update_profile" class="container">

        <div class="row" style="padding:1em">
            <?php hh_notify('Edit Profile','Click on whatever you wish to change'); ?>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <div id="hh_pfe-avatar" class="well well-inverse">
                    <h2>Profile Photo</h2><br/>
                    <div>
                        <?php if (get_the_author_meta( 'profile_pic', $current_user->ID ) =="") hh_download_gravatar($current_user->ID ); ?>
                        <img class="thumbnail" style="width:150px; height:150px;" src="<?php the_author_meta( 'profile_pic', $current_user->ID ); ?>"/>
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
    <?php die();
}


/**
* Shortcode to display the form.
* Usage: [display_profile_editor]
*
* Note: The real form is loaded by ajax.
* @return string - Form itself
*/
function hh_display_profile_editor() {
    if (!is_user_logged_in()) return '<h1>You must be logged in to view this page</h1>';
    if (!current_user_can('manage_options')) return 'Under Construction... -Matt';
    return '<div onload="hh_profile_edit_ajax_load_template()" id="profile-editor-container"></div>';
}

add_shortcode('display_profile_editor','hh_display_profile_editor');

