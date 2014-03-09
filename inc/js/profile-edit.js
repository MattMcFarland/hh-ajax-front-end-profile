
jQuery(document).ready(function($) {
    var jcrop_api;


    $.ajaxSetup({
        type: "POST",
        url: _hh_ajax_fe_profile.ajaxurl
    });


    if ( $('#profile-editor-container').length ) hh_profile_edit_ajax_load_template();


    $(document).on('submit','.hh-update-profile',function(e) {
        e.preventDefault();

        var formfield = $(this).attr('data-meta_key');
        var $formfield = $('#main-'+formfield);
        var meta_key = $formfield.attr('data-meta_key');  console.log('meta_key: '+meta_key);
        var nonce = $formfield.attr('data-nonce');  console.log('nonce: '+nonce);
        var meta_value = $('#input-'+meta_key).val();  console.log('meta_value: '+meta_value);

        $.ajax({
            dataType : "json",
            data : {action: "hh_update_user_profile", meta_key : meta_key, meta_value : meta_value, nonce: nonce},
            beforeSend: function() {
                edit_stop(meta_key);
                edit_alert_show(meta_key,'info','Submitting Request...',true);
                $('.edit-field').addClass('disabled');
            },
            complete: function() {
                edit_stop(meta_key);
                $('#message-'+meta_key).hide();
            },
            success: function(response) {
                var $data = $('#data-'+meta_key);
                $data.html(response.new_value);
                $('<span class="label label-success fadeOut">Updated</span>').insertAfter('#data-'+meta_key);
                $('.fadeOut').fadeOut(1600,function(){
                    $(this).remove();
                    $('.edit-field').removeClass('disabled');
                });
            },
            error: function () {
                var $data = $('#data-'+meta_key);
                $('<span class="label label-danger fadeOut">Failed!!</span>').insertAfter('#data-'+meta_key);
                $('.fadeOut').fadeOut(2600,function(){
                    $(this).remove();
                    $('.edit-field').removeClass('disabled');
                });
            }
        });
    });

    $(document).on('click','.edit-field', function() {
        if ( $(this).hasClass('disabled') ) return;
        edit_start($(this).attr('data-meta_key'));
    });

    $(document).on('click','.pr-cancel', function() {
        edit_stop($(this).attr('data-meta_key'));
    });

    function edit_start(meta_key) {
        $('#current_meta_key').val(meta_key);
        $('#'+meta_key).show();
        $('#main-'+meta_key).addClass('edit-highlight');
        $('#swap-'+meta_key).hide();
        $('#pad-'+meta_key).addClass('field-highlight').css('padding','0');
        $('#controls-'+meta_key).show().css('height','auto');
        $('#input-'+meta_key).focus();
    }
    function edit_stop(meta_key) {
        $('#'+meta_key).removeAttr('style');
        $('#input-'+meta_key).val($('#data-'+meta_key).text());
        $('#main-'+meta_key).removeClass('edit-highlight').removeAttr('style');
        $('#pad-'+meta_key).removeClass('field-highlight').removeAttr('style');
        $('#swap-'+meta_key).removeAttr('style');
        $('#controls-'+meta_key).removeAttr('style');
    }

    function edit_alert_show(meta_key,type,message,show_process) {
        $('#main-'+meta_key).addClass('disabled');
        $('#pad-'+meta_key).hide();
        var processing_img = $('#hh_update_profile').attr('data-processing-uri');
        var msg  = '<div class="alert alert-'+type+'">';
        msg  += '<p>'+message+'</p>';
        if (show_process) {
            msg += '<img src="'+processing_img+'" />';
        }
        msg += '</div>';
        $('#message-'+meta_key).html(msg).show();
    }

    $(document).on('click','#hh-change-profile-photo',function(e) {
        e.preventDefault();
        $('#fileUpload-container').show();
        $('.edit-field').addClass('disabled');
    });

    $(document).on('click','.close-fileUpload-container',function() {
        var previewFields = $('.fileUpload-preview');
        var selectFields = $('.fileUpload-select');
        var errorFields = $('.fileUpload-error');
        var cropFields = $('.fileUpload-crop');
        selectFields.hide();
        cropFields.hide();
        $('#fileUpload-canvas').removeAttr('style');
        previewFields.hide().removeAttr('style');
        errorFields.hide();
        selectFields.show();
        jcrop_api.destroy();
        $('#fileUpload-container').hide();
        $('.edit-field').removeClass('disabled');
    });


    $(document).on('click','.reset-fileUpload-container',function() {
        var previewFields = $('.fileUpload-preview');
        var selectFields = $('.fileUpload-select');
        var errorFields = $('.fileUpload-error');
        var cropFields = $('.fileUpload-crop');
        selectFields.hide();
        cropFields.hide();
        previewFields.hide().removeAttr('style');
        $('#fileUpload-canvas').removeAttr('style');
        errorFields.hide();
        selectFields.show();
        if (jcrop_api) {
            jcrop_api.destroy();
        }

    });

    $(document).on('change','#fileUpload-file', function() {
        if (jcrop_api) {
            jcrop_api.destroy();
        }
        var fileData = $('#fileUpload-fileData');
        var filePreview = $('#fileUpload-preview');
        var previewFields = $('.fileUpload-preview');
        var selectFields = $('.fileUpload-select');
        var errorFields = $('.fileUpload-error');
        var cropFields = $('.fileUpload-crop');
        selectFields.hide();
        cropFields.hide();
        previewFields.hide();
        errorFields.hide();
        filePreview.removeAttr('style');

        var file = this.files[0];
        var valid_exts = ['jpeg', 'jpg', 'png', 'gif']; // valid extensions
        var ext = file.name.split('.').pop().toLowerCase();
        if (valid_exts.indexOf(ext) > -1) {
            if (file.size < 1048576) {
                fileData.html(file.name+' ('+bytesToSize(file.size)+')');
                /* html FileRender Api */
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById("fileUpload-file").files[0]);
                oFReader.onload = function (oFREvent) {
                    previewFields.show();
                    filePreview.css('visibility', 'visible');
                    filePreview.attr('src', oFREvent.target.result).fadeIn();
                    cropFields.show();
                    $editImage = $('#fileUpload-preview');
                    $canvas = $('#fileUpload-canvas');
                    $editImage.Jcrop({
                        setSelect: [100,100,400,400],
                        minSize: [150,150],
                        aspectRatio: 1,
                        boxWidth: $editImage.width()
                    },function() {
                        jcrop_api = this;
                        $canvas.width(jcrop_api.getWidgetSize()[0]/2);
                        $canvas.height(jcrop_api.getWidgetSize()[1]/2);
                    });


                }
            } else {
                errorFields.fadeIn();
                $('#fileUpload-errorMessage').html('<span style="font-weight:bold;color:#FF0000;">Too big!</span><br/> (Your file must be less than 1MB in size)');
            }
        } else {
            errorFields.fadeIn();
            $('#fileUpload-errorMessage').html('<span style="font-weight:bold;color:#FF0000;">Invalid Filetype!</span><br/> (Only jpeg, jpg, png, gif are supported)');
        }
    });



    /**
     * Save Cropped Photo
     */
    $(document).on('submit','.hh-update-profile-photo',function(e) {

        //close the cropping tool.

        //ajax save the cropped file - hh_save_profile_pic()

        //ajax return the new file to the form

    });




});

window.hh_profile_edit_ajax_load_template = function () {
    jQuery(document).ready(function($) {
        $.ajax({
            dataType : "html",
            data: { action: "hh_ajax_load_profile_editor" },
            success: function (data) {
                $('#profile-editor-preloader').fadeOut('fast');
                $('#profile-editor-container').fadeIn('slow').html(data);
            }
        });

    });
};

window.fileUpload_select = function () {
    jQuery.trigger('#fileUpload-file','click')
}