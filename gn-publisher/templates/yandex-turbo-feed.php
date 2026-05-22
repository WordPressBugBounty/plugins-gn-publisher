<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$last_deactivation = get_option( 'gnpub_last_deactivation', 0 );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$last_activation = get_option( 'gnpub_last_activation', 0 );

/**
 * RSS2 Feed Template for displaying RSS2 Posts feed specifically for Google News Publisher.
 * 
 * This template is based on wp-includes/feed-rss2.php
 */

header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
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
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
do_action( 'rss_tag_pre', 'rss2' );
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:yandex="http://news.yandex.ru"
	xmlns:media="http://search.yahoo.com/mrss/"
	xmlns:turbo="http://turbo.yandex.ru"
<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	do_action( 'rss2_ns' );
	echo '>';
	?> 

	<channel>
		<turbo:cms_plugin>gn-publisher</turbo:cms_plugin>
		<title><?php gnpub_wp_title_rss(); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php gnpub_feed_channel_link(); ?></link>
		<description><?php gnpub_bloginfo_rss( 'description' ); ?></description>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<generator><?php echo esc_html( 'GN Publisher: Google News Compatible RSS Feeds v' . GNPUB_VERSION . ' https://wordpress.org/plugins/gn-publisher/' ); ?></generator>
<?php
	while ( have_posts() ) :
		the_post();
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$mod_counter = intval( get_post_meta( get_the_ID(), 'gnpub_modified_count', true ) );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$last_modified = get_post_modified_time( 'U', true );
		if ( $last_modified > $last_deactivation && $last_modified < $last_activation ) {
			$mod_counter++;
		}

		if ( $mod_counter ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			$pub_date_object = new DateTime;
			$pub_date_object->setTimestamp( get_post_time( 'U', true ) );
			$pub_date_object->modify( '+' . $mod_counter . ' seconds' );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			$pub_date = gmdate( 'D, d M Y H:i:s +0000', $pub_date_object->getTimestamp() );
		} else {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			 $pub_date = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false );

		}
		
		
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$category = get_the_category(get_the_ID());
		if ( ! empty( $category ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			$namees = array();
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			foreach ($category  as $nameCategory) {
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
				$namees[] = $nameCategory->name;   
			  }                                      
		 }
		 ?>

		<item turbo="true">
			<title><?php gnpub_the_title_rss(); ?></title>
			<link><?php gnpub_feed_post_link(get_the_permalink()); ?></link>
			<turbo:topic><?php gnpub_the_title_rss(); ?></turbo:topic>
			<turbo:source><?php gnpub_feed_post_link(get_the_permalink()); ?></turbo:source>
			<guid isPermaLink="false"><?php the_guid(); ?></guid>
			<pubDate><?php echo esc_attr( $pub_date ); ?></pubDate>
			<?php $gnpub_authors_escaped = '<dc:creator><![CDATA['. esc_html( get_the_author() ) .']]></dc:creator>'; ?>
				<?php $gnpub_authors_escaped = apply_filters('gnpub_pp_authors_compat',$gnpub_authors_escaped );
					  $gnpub_authors_escaped = apply_filters('gnpub_molongui_authors_compat',$gnpub_authors_escaped );
					  echo $gnpub_authors_escaped; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --reason: already escaped
				?>
			
<?php 
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$content = get_the_content_feed( GNPUB_Feed::FEED_ID );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$content = gnpub_remove_potentially_dangerous_tags( $content );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$thumb_id  = get_post_thumbnail_id( get_the_ID() );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$thumb_url = wp_get_attachment_url( $thumb_id );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$caption   = wp_get_attachment_caption( $thumb_id );
// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$related_posts = get_posts(['category__in' => wp_get_post_categories( get_the_ID() ), 'post__not_in' => [get_the_ID()], 'posts_per_page' => 5 ]);

if( function_exists( 'gnpub_pp_translate' ) )
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$content = gnpub_pp_translate( $content );
 if ( $content && strlen( $content ) > 0 ) : 
?>
			<description><![CDATA[<?php echo wp_trim_words($content,15,'...'); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>]]></description>

			<content:encoded><![CDATA[<?php echo $content; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>]]></content:encoded>
<?php 		else : ?>
			<content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
<?php 		endif; ?>
			<turbo:content><![CDATA[
			<header>
				<h1><?php gnpub_the_title_rss(); ?></h1>
				<?php if ( $thumb_url ){ ?>
				<figure>
				<img src="<?php echo esc_url( $thumb_url ); ?>">
				<?php if ( $caption ) { ?>
				<figcaption><?php echo esc_html( $caption ); ?></figcaption>
				<?php } ?>
				</figure>
			<?php } ?>
			</header>
			<?php 
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $content; 
			?>
			]]></turbo:content>
			<yandex:related>
			<?php 
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			foreach( $related_posts as $related ) { ?>
			<link url="<?php echo esc_url( get_permalink( $related->ID ) ); ?>">
			<?php echo esc_html( get_the_title( $related->ID ) ); ?>
			</link>
			<?php } ?>
			</yandex:related>
<?php 		rss_enclosure(); ?>
		</item>
<?php 	endwhile; ?>
	</channel>
</rss>