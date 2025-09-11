jQuery(document).ready(function($) {
	/** Apple news publisher code */
	$(document).on('change', '#gnpub-apple-news', function(e){
		if( $(this).is(':checked') ) {
		  $('.gnpub-apple-news-options').show();
		}else{
		  $('.gnpub-apple-news-options').hide();
		}
	});

	// On click of publish button process this
	$(document).on('click', '#gnpub-apple-news-publish-btn', function(e){
		$(this).addClass('updating-message');

		$.ajax({
            url : ajaxurl,
            method : "POST",
            dataType: 'json',
            data: { 
              action: "gnpub_apple_news_publish",           
              post_id: gn_script_apple_news_vars.post_id,
              gnpub_apple_news_security_nonce:gn_script_apple_news_vars.gnpub_apple_news_security_nonce
            },            
            success: function(result){   
                $('#gnpub-apple-news-publish-btn').removeClass('updating-message');
                
                if ( result.status === false && result.message) {
                  alert(result.message);
                }else{
                  location.reload();
                }

            },
            error: function(data){
              console.log("Failed Ajax Request");
              console.log(data);
            }
        }); 
	});
});