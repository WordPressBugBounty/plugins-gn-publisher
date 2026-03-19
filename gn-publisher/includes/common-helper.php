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
						'gnpub_delete_data_on_uninstall' 		=>	false,
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

/**
 * Save compatibility options
 * @since 	1.5.27
 * */
add_action( 'wp_ajax_gnpub_save_com_options_ajax', 'gnpub_save_com_options_ajax' );

function gnpub_save_com_options_ajax() {

 if ( !current_user_can( 'manage_options' ) ) {
   return;
 }
 if ( empty( $_POST['security_nonce'] ) ) {
   return;
 }
 // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
 if ( ! wp_verify_nonce( wp_unslash( $_POST['security_nonce'] ), 'gn-admin-nonce' ) ) {
   return;
 }

 $option_name 		=	isset( $_POST['option_name'] ) ? sanitize_text_field( wp_unslash( $_POST['option_name'] ) ) : '';
 $option_value 		=	isset( $_POST['option_value'] ) ? rest_sanitize_boolean( $_POST['option_value'] ) : '';
 
 if ( ! empty( $option_name ) && isset( $option_value ) ) {
   $gnpub_options 	=	get_option( 'gnpub_new_options', false );
   if ( $gnpub_options ) {
     if ( in_array( $option_name, array( 'gnpub_yandex_turbo' ) ) ) {
       $gnpub_options[$option_name] = $option_value;
	   update_option( 'gnpub_new_options', $gnpub_options );
	   echo wp_json_encode( array( 'status' => 'success', 'msg' => 'option updated','options'=> $gnpub_options ) ) ;wp_die();
     }
   }
   else{
	$gnpub_options 		=	array();
	if ( in_array( $option_name, array( 'gnpub_yandex_turbo' ) ) ) {
		$gnpub_options[$option_name] = $option_value;
		update_option( 'gnpub_new_options', $gnpub_options );
		echo wp_json_encode( array( 'status' => 'success', 'msg' => 'option updated', 'options' => $gnpub_options ) ); wp_die();
	  }
   }
   echo wp_json_encode( array( 'status'=>'false','msg'=>'option does not exists' ) ); wp_die();
 }
 wp_die();
}