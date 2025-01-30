<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Indexing template
 * @since 1.5.19
 * */
?>
<header>
	<h1><?php echo esc_html__( 'Instant Indexing', 'gn-publisher' ); ?></h1>
	<p><?php echo esc_html__( 'Configure Google api key settings and choose your post types that needs to be instantly indexed when post is saved.', 'gn-publisher' ); ?></p>
</header>
<?php
$gnpub_options 		=	get_option( 'gnpub_new_options' );

GNPUB_Instant_Index::file_upload_form(); 
GNPUB_Instant_Index::automatic_indexing_form();

wp_nonce_field( 'gnpub_save_index_settings_nonce', 'gnpub_save_index_settings_nonce' );
?>

