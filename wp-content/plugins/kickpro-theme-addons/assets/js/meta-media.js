jQuery(document).ready(function ($) {

    var meta_image_frame, btn, meta_image_preview, meta_image, media_attachment;

    $('.image-upload').click(function (e) {
        
        e.preventDefault();
        
        btn = $(this);

        if (meta_image_frame) {
            meta_image_frame.open();
            return;
        }

        meta_image_frame = wp.media.frames.meta_image_frame = wp.media();

        meta_image_frame.on('select', function () {

            meta_image_preview = btn.parents('.aw-uploader').find('.image-preview');
            meta_image = btn.parents('.aw-uploader').find('.meta-image');
           
            media_attachment = meta_image_frame.state().get('selection').first().toJSON();
          
            meta_image.val(media_attachment.url);
			let $img = meta_image_preview.find('img');
			
			if ($img.length) {
                $img.attr('src', media_attachment.url).show();
            } else {
                $img = $('<img>', {
					id: 'post_page_icon_preview',
                    src: media_attachment.url,
                    css: { width: '200px', display: 'block' }
                });
                meta_image_preview.append($img);
            }
			$("#remove_post_page_icon").show();

        });

        meta_image_frame.open();

    });
	
	$('#remove_post_page_icon').click(function(e){
		$("#post_page_icon_hidden").val('');
		$("#post_page_icon_preview").attr('src', '');
		$("#post_page_icon_preview").attr('srcset', '');
		$("#remove_post_page_icon").hide();
	});
});
