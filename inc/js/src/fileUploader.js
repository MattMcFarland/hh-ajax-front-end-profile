var close_container = '.close-fileUpload-container';
var change_photo = '#hh-change-profile-photo';
var fileInput = '#fileUpload-file';
var errorMessage = '#fuErrorMessageText';
var jcrop_api;
var img_canvas = '#fileUpload-canvas';
var preview = '#fileUpload-preview';
var selectMode = '.fileUpload-select';
var previewMode = '.fileUpload-preview';
var errorMode = '.fileUpload-error';
var form = '#fileUpload-control';
var uploading = false;

jQuery(document).ready(function($) {
    /**
     * When you click you click on the X button to close the container
     */
    $(document).on('click',close_container,function() {
        location.reload();
    });

    /**
     * When you click on your profile picture in form.
     */
    $(document).on('click',change_photo,function() {
        $('#fileUpload-container').show();
        $('.edit-field').addClass('disabled');
    });

    /**
     *  When the input id fileUpload-file has changed.
     */
    $(document).on('change',fileInput, function() {

        var file = this.files[0];
        var valid_exts = ['jpeg', 'jpg', 'png', 'gif']; // valid extensions
        var ext = file.name.split('.').pop().toLowerCase();

        if (valid_exts.indexOf(ext) > -1) {
            if (file.size < 6291456) {
                /* html FileRender Api */
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById('fileUpload-file').files[0]);
                oFReader.onload = function (oFREvent) {
                    $(selectMode).hide();
                    $(previewMode).show();
                    var $editImage = $(preview);
                    $editImage.css('visibility', 'visible');
                    $editImage.attr('src', oFREvent.target.result).fadeIn();
                    $editImage.show();
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
                throwError('<span style="font-weight:bold;color:#FF0000;">Too big!</span><br/> (Filesize must be less than 6 Megabytes)');
            }
        } else {
            throwError('<span style="font-weight:bold;color:#FF0000;">Invalid Filetype!</span><br/> (Only jpeg, jpg, png, gif are supported)');
        }
    });

    function throwError($msg) {
        $(selectMode).hide();
        $(errorMessage).html($msg);
        $(errorMode).fadeIn();
    }

    function hideErrorStuff() {

    }

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
        $fileInput.attr('data-cw',$(img_canvas).width());
        $fileInput.attr('data-ch',$(img_canvas).height());
    }

    $(document).on('submit',form,function(e) {
        e.preventDefault();
        var container = $('#fuProcessContainer');
        var percent = $('#fuProcessPercent');
        var status = $('#fuProcessStatus');
        var image = $('#fuProcessImage');
        nonce = $(this).attr('data-nonce');
        $('body').css('cursor', 'wait');
        $(selectMode).hide();
        //hide preview mode
        $(previewMode).hide();
        $(preview).hide();
        $('.jcrop-holder').hide();
        $('#fileUpload-file').hide();

        container.show();
        percent.html('0%').show();
        status.html('<b>Please Wait...</b>')

        $(this).ajaxForm({
            type : "post",
            dataType : "html",
            url : _hh_ajax_fileUploader.ajaxurl,
            data : {action: "hh_save_profile_pic",
                nonce:  nonce,
                x1:     $(fileInput).attr('data-x1'),
                y1:     $(fileInput).attr('data-y1'),
                x2:     $(fileInput).attr('data-x2'),
                y2:     $(fileInput).attr('data-y2'),
                w:     $(fileInput).attr('data-w'),
                h:     $(fileInput).attr('data-h'),
                cw:     $(fileInput).attr('data-cw'),
                ch:     $(fileInput).attr('data-ch')
            },
            async: true,

            /* reset before submitting */
            beforeSend: function() {
                uploading = true;
                percent.html('1%');
                status.html('<b>Connecting...</b>')
            },

            /* progress bar call back*/
            uploadProgress: function(event, position, total, percentComplete) {

                var pVel = percentComplete + '%';
                percent.html(pVel);
                if (pVel === '100%') {
                    percent.hide();
                    image.show();
                    status.html('<b>Processing...</b>');
                } else {
                    status.html('<b>Uploading...</b>')
                }

            },

            /* complete call back */
            complete: function(data) {
                $(close_container).trigger('click');
            }
        });
        if (uploading===false) $(form).submit();
    });
});