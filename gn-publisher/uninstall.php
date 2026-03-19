<?php
/**
 * GN Publishre Uninstall
 *
 * Uninstalling  Gn Publisher deletes options and data.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$gn_options = get_option( 'gnpub_new_options' );

if ( ! empty( $gn_options ) &&  is_array( $gn_options ) ) {
	if ( ! empty( $gn_options['gnpub_delete_data_on_uninstall'] ) ) {

		delete_option( 'gnpub_include_featured_image' );
		delete_option( 'gnpub_is_default_feed' );
		delete_option( 'gnpub_installed_version' );
		delete_option( 'gnpub_last_activation' );
		delete_option( 'gnpub_new_options' );
		delete_option( 'gnpub_news_sitmap' );
		delete_option( 'gnpub_feed_list' );
		delete_option( 'gnpub_websub_last_ping' );
		delete_option( 'gnpub_google_last_fetch' );
		delete_option( 'gnpub_shortcode_options' );
		delete_option( 'gnpub_google_index_api_settings' );
		delete_option( 'gnpub_giapi_requests' );
		delete_option( 'gnpub_setup_wizard_checklist' );

	}
}