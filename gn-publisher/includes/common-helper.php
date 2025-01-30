<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get translatepress active languages
 * @since 1.5.19
 * */
function gnpub_get_active_language_slugs() {

	// Ensure TranslatePress class exists
	if ( function_exists('gnpub_pro_is_translatepress_enabled') && gnpub_pro_is_translatepress_enabled() ) {

	  	$trp 					=	TRP_Translate_Press::get_trp_instance();
		$trp_settings 			=	$trp->get_component( 'settings' );
		$trp_settings 			=	$trp_settings->get_settings();
		$available_languages 	=	$trp_settings['translation-languages'];

		return $available_languages;

	}

	return array('en');

}

/**
 * Assign default checklist data
 * @since 1.5.19
 * */
function gnpub_default_checklist_options_data(){
	
	$default 	=	array(
						'gnpub_enable_news_article_schema' 		=>	false,
						'gnpub_show_info_featured_img' 			=>	false,
						'gnpub_enable_gnsitemap' 				=>	false,
						'gnpub_gn_status_robot' 				=>	false,
						'gnpub_gn_status_nas' 					=>	false,
						'gnpub_gn_status_byline' 				=>	false,
					);
	return $default;
}

/**
 * Calculate setup wizard progress percentage
 * @since 1.5.19
 * */
function gnpub_setup_wizard_progress_perc() {
	
	$wizard_checklist  =  get_option( 'gnpub_setup_wizard_checklist', gnpub_default_checklist_options_data() );
	$total_options     =  0;
	$chklist_completed =  0;
	$total_perc        =  0;

	if ( ! empty( $wizard_checklist ) ) {
	$total_options   =  count( $wizard_checklist );
	foreach ( $wizard_checklist as $wz_key => $chk_list ) {
	  if ( ! empty( $chk_list ) ) {
	    $chklist_completed++;
	  }  
	}
	}
	if ( $total_options > 0 && $chklist_completed > 0 ) {
		$total_perc      =  ceil( ( $chklist_completed / $total_options ) * 100 );
	}

	return $total_perc;

}