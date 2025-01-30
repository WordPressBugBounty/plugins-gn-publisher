<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Finish template
 * @since 1.5.19
 * */
?>
<header>
	<h1><?php echo esc_html__( 'Thank You for using GN Publisher.', 'gn-publisher' ) ?></h1>
</header>
<div id="gnpub-setup-wizard-finish-wrapper">
<p class="gnpub-thankyou-note"><?php echo esc_html__( 'We strive to create the best GN Publisher solution in WordPress. Our dedicated development team does continuous development and innovation to make sure we are able to meet your demand.', 'gn-publisher' ) ?></p>
<?php 
if ( ! defined( 'GNPUB_PRO_VERSION' ) ){ 
	?> 
	<p class="gnpub-thankyou-note-pro"><?php echo esc_html__( 'Please support us by Upgrading to Premium version.', 'gn-publisher' ) ?></p>
	<h3><?php echo esc_html__( 'Premium Features', 'gn-publisher' ); ?></h3>
	<table id="gnpub-setup-features-list-wrapper">
    <tbody>
      <tr>
        <td>
            <p><span class="dashicons dashicons-yes gnpub-success-status"></span><?php echo esc_html__( 'Feed Content Protection', 'gn-publisher' ); ?></p>
            <p><span class="dashicons dashicons-yes gnpub-success-status"></span><?php echo esc_html__( 'Exclude Categories From Main Feed', 'gn-publisher' ); ?></p>
        </td>
        <td>
            <p><span class="dashicons dashicons-yes gnpub-success-status"></span><?php echo esc_html__( 'Compatible with Flipboard.com', 'gn-publisher' ); ?></p>
            <p><span class="dashicons dashicons-yes gnpub-success-status"></span><?php echo esc_html__( 'Compatible with PublishPress Authors', 'gn-publisher' ); ?></p>
        </td>
        <td>  
            <p><span class="dashicons dashicons-yes gnpub-success-status"></span><?php echo esc_html__( 'Compatible with Molongui Authorship', 'gn-publisher' ); ?></p>
            <p><span class="dashicons dashicons-yes gnpub-success-status"></span><?php echo esc_html__( 'Compatible with Translate Press', 'gn-publisher' ); ?></p>
        </td>
      </tr>
    </tbody>
  </table>
	<a target="_blank" href="https://gnpublisher.com/pricing/">
	    <button class="gnpub-setup-wizard-upgrade-btn">
	        <span><?php esc_html_e( 'YES! I want to Support by UPGRADING.', 'gn-publisher' ) ?></span>
	    </button>
    </a>
    <a href="<?php echo esc_url( add_query_arg( 'page', 'gn-publisher-settings', admin_url( 'options-general.php' ) ) ); ?>"
       style="text-decoration: none;">
        <button class="button-gnp-ugrade1"
                style="display: block;text-align: center;border: 0;margin: 0 auto;background: none;">
            <span style="cursor: pointer;"><?php esc_html_e( 'No Thanks, I will stick with FREE version for now.', 'gn-publisher' ) ?></span>
        </button>
    </a> 
	<?php
}
?>
</div>
