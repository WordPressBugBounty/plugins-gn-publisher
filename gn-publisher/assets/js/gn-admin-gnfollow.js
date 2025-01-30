jQuery(document).ready(function($){

	$(".gnpub-color-picker").wpColorPicker(); // Color picker

	$("#gnpub-icon-upload").click(function(e) {  // Application Icon upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: gnpub_gnfollow_localize_data.uploader_title,
            button: {
                text: gnpub_gnfollow_localize_data.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            $("#gnpub-gnfollow-icon-image").val(attachment.url);
            $("#gnpub-gnfollow-shortcode-img-wrapper img").attr('src', attachment.url);
         })
        .open();
    });

    $('#gnpub-gnfollow-bg-color').wpColorPicker({
	    change: function (event, ui) {
	        var element = event.target;
	        var color = ui.color.toString();
	        $('#gnpub-gnfollow-shortcode-wrapper').css('background', color);
	    },
	});

	$('#gnpub-gnfollow-bg-color').wpColorPicker({
	    change: function (event, ui) {
	        var element = event.target;
	        var color = ui.color.toString();
	        $('#gnpub-gnfollow-shortcode-wrapper').css('background', color);
	    },
	});

	$('#gnpub-gnfollow-text-color').wpColorPicker({
	    change: function (event, ui) {
	        var element = event.target;
	        var color = ui.color.toString();
	        $('#gnpub-gnfollow-shortcode-text-wrapper').css('color', color);
	    },
	});

	$(document).on( 'keyup', '#gnpub-gnfollow-text-one', function(e) {
		let textOne 	=	$(this).val();
		$('#gnpub-gnfollow-shortcode-follow-text').text(textOne);
	});

	$(document).on( 'keyup', '#gnpub-gnfollow-text-two', function(e) {
		let texttwo 	=	$(this).val();
		$('#gnpub-gnfollow-shortcode-follow-google-txt').text(texttwo);
	});

	$(document).on('change', '#gnpub_gnfollow_sticky', function(e){
		e.preventDefault();
		if ( $(this).is(':checked') ) {
			$('.gnpub-gnfollow-sticky-opts').show();
			$('.gnpub-gnfollow-sticky-opts').css('display', 'table-row');
		}else{
			$('.gnpub-gnfollow-sticky-opts').hide();
		}	
	});
	gnpub_init_modal( 'gnpub-modal-google-news-follow','gnpub-customize-news-follow','gnpub-google-news-follow-close' );

});