var close_container = '.close-fileUpload-container';
var change_photo = '#hh-change-profile-photo';
var fileInput = '#fileUpload-file';
var errorMessage = '#fileUpload-errorMessage';
var jcrop_api;
var img_canvas = '#fileUpload-canvas';
var preview = '#fileUpload-preview';
var selectMode = '.fileUpload-select';
var previewMode = '.fileUpload-preview';
var form = '#fileUpload-control';


jQuery(document).ready(function($) {
    /**
     * When you click you click on the X button to close the container
     */
    $(document).on('click',close_container,function() {
        $('#fileUpload-container').hide();
    });

    /**
     * When you click on your profile picture in form.
     */
    $(document).on('click',change_photo,function() {
        $('#fileUpload-container').show();
    });

    /**
     *  When the input id fileUpload-file has changed.
     */
    $(document).on('change',fileInput, function() {

        var file = this.files[0];
        var valid_exts = ['jpeg', 'jpg', 'png', 'gif']; // valid extensions
        var ext = file.name.split('.').pop().toLowerCase();

        if (valid_exts.indexOf(ext) > -1) {
            if (file.size < 1048576) {
                /* html FileRender Api */
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById('fileUpload-file').files[0]);
                oFReader.onload = function (oFREvent) {
                    $(selectMode).hide();
                    $(previewMode).show();
                    var $editImage = $(preview);
                    $editImage.removeAttr('style');
                    $editImage.css('visibility', 'visible');
                    $editImage.attr('src', oFREvent.target.result).fadeIn();
                    var $canvas = $(img_canvas);
                    $editImage.Jcrop({
                        setSelect: [100,100,400,400],
                        minSize: [150,150],
                        aspectRatio: 1,
                        boxWidth: $canvas.width(),
                        onSelect: storeCoords
                    },function() {
                        jcrop_api = this;
                        $canvas.width(jcrop_api.getWidgetSize()[0]);
                        $canvas.height(jcrop_api.getWidgetSize()[1]);
                    });
                }
            } else {
                $(errorMessage).html('<span style="font-weight:bold;color:#FF0000;">Too big!</span><br/> (Your file must be less than 1MB in size)').fadeIn();
            }
        } else {
            $(errorMessage).html('<span style="font-weight:bold;color:#FF0000;">Invalid Filetype!</span><br/> (Only jpeg, jpg, png, gif are supported)').fadeIn();
        }
    });

    function storeCoords(c)
    {
        // variables can be accessed here as
        // c.x, c.y, c.x2, c.y2, c.w, c.h
        var $fileInput = $(fileInput);
        $fileInput.attr('data-x1',c.x);
        $fileInput.attr('data-y1',c.y);
        $fileInput.attr('data-x2',c.x2);
        $fileInput.attr('data-y2',c.y2);
        $fileInput.attr('data-w',c.w);
        $fileInput.attr('data-h',c.h);
    }

    $(document).on('submit',form,function(e) {
        e.preventDefault();

        var percent = $('#upload_percent');
        var status = $('#upload_status');
        var process = $('#upload_processing');
        var submit = $('.fileUpload-process');

        $('body').css('cursor', 'wait');

        $(this).ajaxForm({
            type : "post",
            dataType : "html",
            url : _hh_ajax_fileUploader.ajaxurl,
            data : {action: "hh_save_profile_pic"},
            async: true,

            /* reset before submitting */
            beforeSend: function() {
                $(selectMode).hide();
                $(previewMode).hide();
                submit.show();
                percent.html('0%').show();
                status.html('<b>Connecting...</b>')
            },

            /* progress bar call back*/
            uploadProgress: function(event, position, total, percentComplete) {

                var pVel = percentComplete + '%';
                percent.html(pVel);
                if (pVel === '100%') {
                    percent.hide();
                    $('#upload_processing').show();
                    status.html('<b>Processing...</b>');
                    $('#hh_upload_progress').hide();
                } else {
                    status.html('<b>Uploading...</b>')
                }

            },

            /* complete call back */
            complete: function(data) {
                submit.hide();
                process.hide();
                status.hide();
                $(selectMode).show();
                var response = data.responseText;
                console.log(response);
                $(close_container).trigger('click');
                $('body').css('cursor', 'auto');
            }
        });
    });
});