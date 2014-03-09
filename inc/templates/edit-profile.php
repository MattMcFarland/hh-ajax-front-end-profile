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
* Shortcode to display the form.
* Usage: [display_profile_editor]
*
* Note: The real form is loaded by ajax.
* @return string - a div that tells the js to load the form via ajax.
*/
function hh_display_profile_editor() {
    if (!is_user_logged_in()) return '<h1>You must be logged in to view this page</h1>';
    if (!current_user_can('manage_options')) return 'Under Construction... -Matt';

    return '<img id="profile-editor-preloader" src="'.plugin_dir_url( __FILE__ ).'../img/loading-form.gif" ></img><div id="profile-editor-container"></div>';
}

add_shortcode('display_profile_editor','hh_display_profile_editor');



function hh_file_upload_ui() {
    ob_start() ?>
    <style>
        #fileUpload-container {
            display:none;
        }
        #fileUpload-bg {
            position:absolute;
            background-color:rgba(0,0,0,0.8);
            width:100%;
            height:100%;
        }
        #fileUpload-inner {
            width:95%;
            height:95%;
            margin-top:1em;
        }
        #fileUpload-preview {

        }
        #fileUpload-btn {
            width:100%;
            height:100%;
        }
        #fileUpload-info {

        }
    </style>

    <div id="fileUpload-container">
        <div id="fileUpload-bg">
            <div id="fileUpload-inner" class="container">
                <div class="row">
                    <div id = "fileUpload-preview" class="col-xs-12">
                        <a id="fileUpload-btn" onclick="fileUploadSelect()"></i></a>
                        <div id="fileUpload-info"></div>
                        <input style="display:none;" type="file" name = "fileUpload-file" id="fileUpload-file">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $result = ob_get_clean();
    return $result;
}
