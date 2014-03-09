
jQuery(document).ready(function($) {

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
        $('#fileUpload-container').hide();
        $('.edit-field').removeClass('disabled');
    });


    /**
     * Handle upload photo
     */
    $(document).on('submit','.hh-update-profile-photo',function(e) {

        //Show native file upload UI

        //ajax hh_upload_profile_pic() - which should return the cropping UI

        //Can you do both file upload UI and submit?

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