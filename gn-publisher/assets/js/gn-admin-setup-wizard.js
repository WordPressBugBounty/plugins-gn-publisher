/**
 * JS file for setup wizard
 * @since 1.5.19
 * */
jQuery(document).ready(function($){

	$(document).on( 'click', '#gnpub_enable_gnsitemap', function( e ) {

		if ( $(this).is(':checked') ) {
			$('#gnpub-sitemap-setting-wrapper').show();
			$('#gnpub-sitemap-setting-wrapper').removeClass('gnpub-d-none');
		}else{
			$('#gnpub-sitemap-setting-wrapper').hide();
			$('#gnpub-sitemap-setting-wrapper').removeClass('gnpub-show');
		}

	});

	// Change the opacity of the table row is checklist checkbox is selected
	$(document).on( 'click', '.gnpub-setup-wizard-chklist-chkbox', function(e) {
		let thisClass 	=	$(this).attr('data-dont-hide');

		if ( $(this).is(':checked') ){
			$(this).parent().addClass('gnpub-setup-wizard-chklist-tr-checked');
		
			$.each($('.gnpub-setup-wizard-add-opacity '), function( index, element ) {
				let siblings 	=	$(this).attr('data-hide');
				if ( thisClass == siblings ) {
					$(this).addClass('gnpub-setup-wizard-chklist-opacity');
				}
			});
			
		}else{
			$(this).parent().removeClass('gnpub-setup-wizard-chklist-tr-checked');
			
			$.each($('.gnpub-setup-wizard-add-opacity '), function( index, element ) {
				let siblings 	=	$(this).attr('data-hide');
				if ( thisClass == siblings ) {
					$(this).removeClass('gnpub-setup-wizard-chklist-opacity');
				}
			});
		}
	});

	// Save checklist data on change of a check box status
	$(document).on( 'change', '.gnpub-setup-wizard-chklist-chkbox', function(e) {

		e.preventDefault();

		let postKey 		=	$(this).attr('data-chk-opt-name');
		let activeTab 	=	$('#gnpub-active-tab').val();
		let isChecked 	=	'no';
		if ( $(this).is(':checked') ) {
			isChecked 		=	'yes';	
		}

		$.ajax({
			
            url : ajaxurl,
            method : 'POST',
            dataType: 'json',
            data: { 
              action: "gnpub_setup_wizard_checklist_ajax",   
              name: postKey,                     
              value: isChecked,                     
              tab: activeTab,                     
              security:gnpub_setup_wizard_localize_data.gnpub_setup_wizard_security_nonce
            },            
            success: function(result){   
            	
            	if ( result.success && typeof result.data.perc !== 'undefined' ) {

            		$('.gnpub-setup-wizard-progress-bar').css({"width": result.data.perc+"%"});
            		if ( result.data.perc > 0 ) {
            			$('.gnpub-setup-wizard-progress-bar-text').removeClass('gnpub-d-none');
            			$('.gnpub-setup-wizard-progress-bar-text').text( result.data.perc+'% '+'Completed' );
            		}else{
            			$('.gnpub-setup-wizard-progress-bar-text').addClass('gnpub-d-none');
            		}

            		let label = $('.gnpub-setup-wizard-active-step').attr('data-label');
            		$('.gnpub-setup-wizard-active-step').attr('title', label+' '+result.data.text);

            	}

            }

          });

	});

});