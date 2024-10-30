/* global kseo_cf */
(function ($) {
    $(document).ready(function ($) {
        $('.kseo-meta-color').wpColorPicker();
        var $ndConfig = {ajaxurl: '',ajax_kseo: ''};
        if (typeof kseo_cf !== 'undefined')
        {
            $ndConfig = $.extend(true, {}, $ndConfig, kseo_cf);
        }
        var listYesno = $('.kads-seo-button-yesno');
        if (listYesno.length)
        {
            $.each(listYesno, function () {
                var selection = $(this);
                $('.kads-seo-yesno .button', selection).on('click', function (event) {
                    event.preventDefault();
                    var yesno = $(this).closest('.kads-seo-yesno');
                    if (yesno.hasClass('selected'))
                        return;
                    $(yesno).find('input').prop("checked", true);
                    var container = yesno.closest('.kads-seo-button-yesno');

                    $('.kads-seo-yesno', container).removeClass('selected');
                    yesno.addClass('selected');
                });
            });
        }
        var formlabel = 0;
        var tgm_media_frame;
        $('body').on('click', '.kads-settings .kseo-upload-button', function () {
            formlabel = $(this).closest('.kads-settings');
            if (tgm_media_frame) {
                tgm_media_frame.open();
                return;
            }
            tgm_media_frame = wp.media({
                frame: 'select',
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            tgm_media_frame.on('select', function () {
                var media_attachment = tgm_media_frame.state().get('selection').first().toJSON();
                $('.kseo-img-placeholder',formlabel).css('background-image','url(' + media_attachment.url + ')');
                $('input.kseo-upload-input',formlabel).val(media_attachment.id);
            });
            tgm_media_frame.open();
        });
        $('body').on('keyup paste', '.kads-settings .kads-controls-count', function (e) {
            var elSetting = $(this).closest('.kads-settings');
            var maxchar = parseInt($(this).attr('maxlength'));
            var tval = $(this).val(),
                tlength = tval.length,
                set = maxchar,
                remain = parseInt(set - tlength);
            $('.maxchar-count',elSetting).html(remain);
            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                $(this).val((tval).substring(0, tlength - 1));
                return false;
            }
        });
        

        var kseo_gallery_frame;
        $('.kseo-list-images-container').each(function () {
            var listContainer = $(this);

            var $image_gallery_ids = $('.kseo-image-gallery', listContainer);

            var $kseo_images = $('.kseo-box-images', listContainer).find('ul.kseo-images');

            $('.add-kseo-images', listContainer).on('click', 'a', function (event) {
                var $el = $(this);

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if (kseo_gallery_frame) {
                    kseo_gallery_frame.open();
                    return;
                }

                // Create the media frame.
                kseo_gallery_frame = wp.media.frames.kseo_gallery = wp.media({
                    // Set the title of the modal.
                    title: $el.data('choose'),
                    button: {
                        text: $el.data('update')
                    },
                    states: [
                        new wp.media.controller.Library({
                            title: $el.data('choose'),
                            filterable: 'all',
                            multiple: true
                        })
                    ]
                });

                // When an image is selected, run a callback.
                kseo_gallery_frame.on('select', function () {
                    var selection = kseo_gallery_frame.state().get('selection');
                    var attachment_ids = $image_gallery_ids.val();

                    selection.map(function (attachment) {
                        attachment = attachment.toJSON();

                        if (attachment.id) {
                            attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                            var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                            $kseo_images.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li>');
                        }
                    });
                    $image_gallery_ids.val(attachment_ids);
                });

                // Finally, open the modal.
                kseo_gallery_frame.open();
            });

            // Image ordering.
            $kseo_images.sortable({
                items: 'li.image',
                cursor: 'move',
                scrollSensitivity: 40,
                forcePlaceholderSize: true,
                forceHelperSize: false,
                helper: 'clone',
                opacity: 0.65,
                placeholder: 'wc-metabox-sortable-placeholder',
                start: function (event, ui) {
                    ui.item.css('background-color', '#f6f6f6');
                },
                stop: function (event, ui) {
                    ui.item.removeAttr('style');
                },
                update: function () {
                    var attachment_ids = '';

                    $('.kseo-box-images', listContainer).find('ul li.image').css('cursor', 'default').each(function () {
                        var attachment_id = $(this).attr('data-attachment_id');
                        attachment_ids = attachment_ids + attachment_id + ',';
                    });

                    $image_gallery_ids.val(attachment_ids);
                }
            });

            // Remove images.
            $('.kseo-box-images', listContainer).on('click', 'a.delete', function () {
                $(this).closest('li.image').remove();

                var attachment_ids = '';

                $('.kseo-box-images', listContainer).find('ul li.image').css('cursor', 'default').each(function () {
                    var attachment_id = $(this).attr('data-attachment_id');
                    attachment_ids = attachment_ids + attachment_id + ',';
                });

                $image_gallery_ids.val(attachment_ids);
                return false;
            });
        });

        $('body').on('click', '.kseo-control-fileupload-button .kseo-fileupload-button', function () {
            var elid = $(this).attr('data-file');
            
            var percenttext = $(this).attr('data-info');
            
            
            var warp = $(this).closest('.kseo-upload-option-fileupload');
            var bar = $('.bar',warp);
            var percent = $('.percent',warp);
            var elinfo = $('.kseo-fileupload-info',warp);
            var nametext = $('#' + elid +'-name-html',warp);
            var elname = $('#' + elid +'-name',warp);
            
            var elbutton = $('.kseo-control-fileupload-button',warp);
            
            $('.delete',elinfo).on('click',function (e){
                nametext.html('');
                elname.val('');
                bar.width('0%');
                percent.html(percenttext);
                elbutton.fadeIn();
                elinfo.fadeOut();
                e.preventDefault();
            });
            var elem = document.getElementById('fileupload-kseo-file');
            if (elem && document.createEvent) {
                var evt = document.createEvent("MouseEvents");
                evt.initEvent("click", true, false);
                elem.dispatchEvent(evt);
                elem.onchange = function () {
                    $('#fileupload-kseo').ajaxForm({
                        beforeSend: function() {
                            var percentVal = '0%';
                            bar.width(percentVal);
                            percent.html(percentVal);
                        },
                        uploadProgress: function(event, position, total, percentComplete) {
                            var percentVal = percentComplete + '%';
                            bar.width(percentVal);
                            percent.html(percentVal);
                        },
                        success: function(rerult) {
                            if(rerult && rerult.success){
                                elname.val(rerult.data.name);
                                nametext.html(rerult.data.name);
                                elinfo.fadeIn();
                                elbutton.fadeOut();
                            }
                        }
                    });
                    $('#fileupload-kseo').submit();
                };
            }
        });
        
        if($('.kseo-fileupload-button').length){
            if($('#fileupload-kseo').length == 0)
            {
                $('body').append('<form id="fileupload-kseo" action="' + $ndConfig.ajaxurl +'" method="post" enctype="multipart/form-data">'
                        +'<input type="hidden" name="ajax_kseo_nonce" value="' + $ndConfig.ajax_kseo +'">'
                        +'<input type="hidden" name="action" value="kseo_add_data_file">'+
                        '<input id="fileupload-kseo-file" type="file" name="kseo-data-fileupload-file"></form>');
            }
        }
        
        $('body').on('click', '.kseo-features-option-set .kseo-features-button', function () {
            var elid = $(this).attr('data-input');
            var elicon = $(this).attr('data-icon');
            var addrow = '<tr class="kseo-features-table-group">' +
                        '<td><input type="text" name="' + elid + '[name][]" value="" /></td>' +
                        '<td><input type="text" name="' + elid + '[free][]" value="" /></td>' +
                        '<td><input type="text" name="' + elid + '[pro][]" value="" /></td>' +
                        '<td><a class="features-remove" href="#">' + elicon + '</a></td>' +
                    '</tr>';
            
            var $tbody = $('#' + elid + '-table tbody');
           $tbody.append(addrow);
        });
        $('body').on('click', '.kseo-features-option-set .features-remove', function (e) {
            $(this).closest('tr.kseo-features-table-group').remove();
            e.preventDefault();
        });
        
    });
})(jQuery);