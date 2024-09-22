<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$last_deactivation = get_option( 'gnpub_last_deactivation', 0 );
$last_activation = get_option( 'gnpub_last_activation', 0 );

/**
 * RSS2 Feed Template for displaying RSS2 Posts feed specifically for Google News Publisher.
 * 
 * This template is based on wp-includes/feed-rss2.php
 */

header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . esc_attr( get_option( 'blog_charset' ) ), true );
$more = 1;

///////////////
// Disable caching @since 1.0.2 -ca
//////////////
header('Expires: Wed, 01 Jan 2014 00:00:00 GMT');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

define( 'DONOTCACHEPAGE', true);

echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?' . '>';
/**
 * Fires between the xml and rss tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action( 'rss_tag_pre', 'rss2' );
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/" <?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	echo '>';
	?> 

	<channel>
		<title><?php gnpub_wp_title_rss(); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php gnpub_feed_channel_link(); ?></link>
		<description><?php gnpub_bloginfo_rss( 'description' ); ?></description>
		<lastBuildDate><?php
			$date = get_lastpostmodified( 'GMT' );
			echo $date ? esc_html( mysql2date( 'D, d M Y H:i:s +0000', $date, false ) ) : esc_html( gmdate( 'r' ) );
		?></lastBuildDate>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<sy:updatePeriod> <?php $duration = 'hourly'; echo esc_html( apply_filters( 'rss_update_period', $duration ) );?> </sy:updatePeriod>
		<sy:updateFrequency> <?php $frequency = '1'; echo esc_html( apply_filters( 'rss_update_frequency', $frequency ) );?> </sy:updateFrequency>
		<atom:link rel="hub" href="https://pubsubhubbub.appspot.com/" />
		<generator>GN Publisher v<?php echo esc_html(GNPUB_VERSION);?> https://wordpress.org/plugins/gn-publisher/</generator>
<?php
	$feed_support_flag = apply_filters('gnpub_enable_feed_support_filter', 0); // create selected post type feeds
	if($feed_support_flag == 0){
		while ( have_posts() ) :
			the_post();
			$post_id = get_the_ID();
			$mod_counter = intval( get_post_meta( $post_id, 'gnpub_modified_count', true ) );
			$last_modified = get_post_modified_time( 'U', true );
			if ( $last_modified > $last_deactivation && $last_modified < $last_activation ) {
				$mod_counter++;
			}

			if ( $mod_counter ) {
				$pub_date_object = new DateTime;
				$pub_date_object->setTimestamp( get_post_time( 'U', true ) );
				$pub_date_object->modify( '+' . $mod_counter . ' seconds' );

				$pub_date = gmdate( 'D, d M Y H:i:s +0000', $pub_date_object->getTimestamp() );
			} else {
				 $pub_date = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false );

			}

			?>

			<item>
				<title><?php gnpub_the_title_rss(); ?></title>
				<link><?php gnpub_feed_post_link(get_the_permalink()); ?></link>
				<pubDate><?php echo esc_attr( $pub_date ); ?></pubDate>
				<?php $gnpub_authors_escaped = '<dc:creator><![CDATA['. esc_html( get_the_author() ) .']]></dc:creator>'; ?>
				<?php $gnpub_authors_escaped = apply_filters('gnpub_pp_authors_compat',$gnpub_authors_escaped );
					  $gnpub_authors_escaped = apply_filters('gnpub_molongui_authors_compat',$gnpub_authors_escaped );
					  echo $gnpub_authors_escaped; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --reason: already escaped
				?>
				<guid isPermaLink="false"><?php the_guid() ?></guid>
	<?php 
	$content = get_the_content_feed( GNPUB_Feed::FEED_ID );
	$content = gnpub_remove_potentially_dangerous_tags($content);

	if( function_exists( 'gnpub_pp_translate' ) )
		$content = gnpub_pp_translate( $content );
	 if ( $content && strlen( $content ) > 0 ) : 
	?>
				<description><![CDATA[<?php echo wp_trim_words($content,15,'...'); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>]]></description>

				<content:encoded><![CDATA[<?php echo $content; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>]]></content:encoded>
	<?php 		else : ?>
				<content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
	<?php 		endif; ?>
	<?php 		gnpub_rss_enclosure(); 
			do_action( 'rss2_item',  $post_id);
	?>
			</item>
	<?php 	endwhile; 
	}
	?>
	</channel>
</rss>
<!-- last GN Pub feeds fetch (not specifically this feed): <?php echo (get_option( 'gnpub_google_last_fetch', null )) ? esc_html(date_i18n( 'Y-m-d H:i:s', get_option( 'gnpub_google_last_fetch' ) )) : esc_html__( 'has not fetched' , 'gn-publisher' ); ?> -->
