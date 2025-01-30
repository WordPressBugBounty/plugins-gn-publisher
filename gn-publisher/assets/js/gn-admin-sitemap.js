/**
 * Sitemap related js code
 * @since 1.5.19
 * */
function gnpub_init_modal( model_id = null, button_id = null,close_id = null ) {

		if ( ! model_id || ! button_id || ! close_id ) {
			return '';
		}

		// Get the modal
		var modal = document.getElementById( model_id );

		// Get the button that opens the modal
		var btn = document.getElementById( button_id );

		// Get the <span> element that closes the modal
		var span = document.getElementById( close_id );

		// When the user clicks the button, open the modal
		if ( btn ) {
			btn.onclick = function() {
	  		modal.style.display = "block";
			}
		}

		if ( span ) {
			// When the user clicks on <span> (x), close the modal
			span.onclick = function() {
	  		modal.style.display = "none";
			}
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
	  if ( event.target == modal) {
	    modal.style.display = "none";
	  }
	}

}

gnpub_init_modal( 'gnpub_modal_gnsitemap','gnpub_gnsitemap_config','gnpub_gnsitemap_close' );

var gnpub_sitemap_checkbox		=	document.getElementById('gnpub_enable_gnsitemap');
if ( gnpub_sitemap_checkbox ) {
	gnpub_sitemap_checkbox.addEventListener( 'click',function(e) {

		jQuery.ajax({
	        url: ajaxurl,
	        method: 'post',
					dataType: "json",
	        data: {
	        		action: 'gnpub_save_options',
							option_name:'gnpub_enable_gnsitemap',
							option_value:true,
	        		security_nonce:gnpub_sitemap_localize_data.gnpub_sitemap_security_nonce
	        },
	        success: function(response){
						if(response.status=='success'){
			        		if(gnpub_sitemap_checkbox.checked == true){
								gnpub_sitemap_checkbox.type="hidden";
								document.getElementById('gnpub_gnsitemap_label').innerHTML=document.getElementById('gnpub_gnsitemap_label').getAttribute('data-checked');
								document.getElementById('gnpub_gnsitemap_config').style.display="inline-block";
								document.getElementById('gnpub_disable_link_2').style.display="inline-block";
							}
						}
						else{
							alert('options not saved , try again later');
						}
	        },
					error:function(error){
						alert(error);
					}
	    });
	});
}

var gnpub_disbale_link_2 	=	document.getElementById('gnpub_disable_link_2');
if ( gnpub_disbale_link_2 ) {
	gnpub_disbale_link_2.addEventListener('click',function(e){
		const dis_btn = jQuery(this);
		dis_btn.text('Please Wait...');
		jQuery.ajax({
	        url: ajaxurl,
	        method: 'post',
					dataType: "json",
	        data: {
	        		action: 'gnpub_save_options',
							option_name:'gnpub_enable_gnsitemap',
							option_value:false,
	        		security_nonce:gnpub_sitemap_localize_data.gnpub_sitemap_security_nonce
	        },
	        success: function(response){
						if(response.status=='success'){
			        	var gnpub_flip_checkbox 	= document.getElementById('gnpub_enable_gnsitemap');
								gnpub_flip_checkbox.type 	=	"checkbox";
								gnpub_flip_checkbox.checked=false;
								document.getElementById('gnpub_gnsitemap_label').innerHTML=document.getElementById('gnpub_gnsitemap_label').getAttribute('data-unchecked');
								document.getElementById('gnpub_gnsitemap_config').style.display="none";
								document.getElementById('gnpub_disable_link_2').style.display="none";
								gnpub_flip_checkbox.style.display="inline-block";
								dis_btn.text('Disable');	
						}
						else{
							alert('options not saved , try again later');
							dis_btn.text('Disable');	
						}
	        },
					error:function(error){
						alert(error);
						dis_btn.text('Disable');
					}
	    });
		
	});
}