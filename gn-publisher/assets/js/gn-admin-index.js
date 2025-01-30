/**
 * Instant index related js
 * @since 1.5.19
 * */
jQuery(document).ready(function($){


	var $ufResponse = $('#gnpub-indexing-giapi-response-body');

    var logResponse = function( info, url ) {
		var d    = new Date();		
		var urls = $("#gnpub-giapi-url").val().split('\n').filter(Boolean);
		var urls_str = urls[0];
		var is_batch = false;
		var action = $(".gnpub-i-i-action").filter(':checked').val();

		if ( urls.length > 1 ) {
			urls_str = '(batch)';
			is_batch = true;
		}

		$ufResponse.removeClass('not-ready fail success').addClass('ready').find('.gnpub-indexing-response-id').html('<strong>' + action + '</strong>' + ' ' + urls_str);
		if ( ! is_batch ) {
			if ( typeof info.error !== 'undefined' ) {
				$ufResponse.addClass('fail').find('.gnpub-indexing-response-status').text(gnpub_index_localize_data.l10n_error+' '+info.error.code).siblings('.gnpub-indexing-response-message').text(info.error.message);
				$('.gnpub-indexing-response-status').css('color', '#bb2b2b');
			} else {
				var base = info;
				if ( typeof info.urlNotificationMetadata != 'undefined' ) {
					base = info.urlNotificationMetadata;
				}
				
				if ( typeof base.latestUpdate != 'undefined' ) {
					d = new Date(base.latestUpdate.notifyTime);
				}
				$ufResponse.addClass('success').find('.gnpub-indexing-response-status').text(gnpub_index_localize_data.l10n_success+' ').siblings('.gnpub-indexing-response-message').text(gnpub_index_localize_data.l10n_last_updated+' ' + d.toString());
				$('.gnpub-indexing-response-status').css('color', 'green');
			}
		} else {
			$ufResponse.addClass('success').find('.gnpub-indexing-response-status').text(gnpub_index_localize_data.l10n_success+' ');
			if ( typeof info.error !== 'undefined' ) {
				$ufResponse.addClass('fail').find('.gnpub-indexing-response-status').text(gnpub_index_localize_data.l10n_error+' '+info.error.code).siblings('.gnpub-indexing-response-message').text(info.error.message);
			} else {
				$.each(info, function(index, val) {
					if ( typeof val.error !== 'undefined' ) {
						var error_code = '';
						if ( typeof val.error.code !== 'undefined' ) {
							error_code = val.error.code;
						}
						var error_message = '';
						if ( typeof val.error.message !== 'undefined' ) {
							error_message = val.error.message;
						}
						$ufResponse.addClass('fail').find('.gnpub-indexing-response-status').text(gnpub_index_localize_data.l10n_error+' '+error_code).siblings('.gnpub-indexing-response-message').text(val.error.message);
					}
				});
			}
		}
		
	};

	$("#gnpub-instant-indexing-send").on("click", function(){
        
        var urls        = $("#gnpub-giapi-url").val();
        var api_action  = $(".gnpub-i-i-action").filter(':checked').val();
        var current     = $(this);

        current.addClass('updating-message'); 

        $.ajax({
            url : ajaxurl,
            method : "POST",
            dataType: 'json',
            data: { 
              action: "gnpub_ociaifs_giapi", 
              api_action: api_action,  
              url: urls,                      
              gnpub_index_security_nonce:gnpub_index_localize_data.gnpub_index_security_nonce
            },            
            success: function(result){   
                logResponse( result, urls );              
                current.removeClass('updating-message');                         
            },
            error: function(data){
              console.log("Failed Ajax Request");
              console.log(data);
            }
          }); 

    })

});