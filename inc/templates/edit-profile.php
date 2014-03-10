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
    <div id="fileUpload-container">
        <div id="fileUpload-bg">
            <div id="fileUpload-inner">
                <header class="row">
                    <div class="col-xs-10"><h1>Upload Profile Photo</h1></div>
                    <h1 class="col-xs-2"><a type="button" class="close-fileUpload-container" id="fileUpload-X-button"><i class="fa fa-times"></i></a></h1>
                </header>

                <section class="row fileUpload-body">
                    <!--- Above Canvas --->
                    <div class="fileUpload-Title fileUpload-error" id="fileUpload-previewTitle">Please Try Again</div>
                    <div class="fileUpload-Title fileUpload-preview" id="fileUpload-previewTitle">Crop Photo</div>

                    <!--- Canvas Area --->
                    <canvas id = "fileUpload-canvas">

                        <!--- Select Stage --->
                        <a class="fileUpload-select fileUpload-canvasInner" id="fileUpload-btn" onclick="jQuery('#fileUpload-file').trigger('click')">
                            <div>
                                <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/upload-cloud.gif'?>" class="fileUpload-img">
                                <div class="fileUpload-Title">Tap here to browse your photos</div>
                            </div>
                        </a>
                        <input style="display:none;" type="file" name = "fileUpload-file" id="fileUpload-file">

                        <!--- Preview Stage --->
                        <div class="fileUpload-canvasInner fileUpload-preview" id="fileUpload-previewCanvas">
                            <img src="" class="thumbnail " id="fileUpload-preview"/>
                        </div>

                        <!--- Error Stage --->
                        <div class="fileUpload-error fileUpload-canvasInner" id="fileUpload-previewCanvas">
                            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/error.gif'?>" class="fileUpload-img"/>
                        </div>

                    </canvas>

                </section>

                <!--- Below Canvas --->
                <footer class="row fileUpload-belowCanvas">
                    <div class="fileUpload-preview">
                        <div class= "fileUpload-message" id="Message"><div id="fileUpload-fileData"></div></div>
                        <button type="button" class="btn btn-success btn-large" ><i class="fa fa-check"></i> Save</button>
                        <button type="button" class="btn btn-danger btn-large reset-fileUpload-container"><i class="fa fa-ban "></i> Cancel</button>
                    </div>
                    <div class="fileUpload-select">
                        <div class="fileUpload-message">Max Filesize: 1MB (jpg, gif, png only)</div>
                    </div>

                    <!--- Error Stage --->
                    <div class="fileUpload-error">
                        <div class= "fileUpload-message" id="fileUpload-errorMessage"></div>
                        <button type="button" class="btn btn-success btn-large" onclick="jQuery('#fileUpload-file').trigger('click')" ><i class="fa fa-check"></i> Try Again</button>
                        <button type="button" class="btn btn-danger btn-large close-fileUpload-container"><i class="fa fa-ban "></i> Nevermind</button>
                    </div>


                </footer>
            </div>
        </div>
    </div>

    <?php $result = ob_get_clean();
    return $result;
}
