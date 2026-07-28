<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$options_data = get_option( 'gnpub_news_sitmap' );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$options = unserialize($options_data);
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$dgenre = '';
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if(!empty($options) && !empty($options['news_sitemap_default_genre'])){
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $dgenre = $options['news_sitemap_default_genre'];
} 
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$post_types = '';
if(!empty($options) && !empty($options['news_sitemap_include_post_types'])){
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $post_types = $options['news_sitemap_include_post_types'];
} 
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$get_categories = '';
if(!empty($options) && !empty($options['news_sitemap_exclude_terms'])){
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $get_categories = $options['news_sitemap_exclude_terms'];
} 
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$publication_name = get_bloginfo('name');
if(!empty($options) && !empty($options['publication_name'])){
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $publication_name = $options['publication_name']?$options['publication_name']:get_bloginfo('name');
} 
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$two_digit_langcode = get_bloginfo('language');
if($two_digit_langcode!='zh-cn' && $two_digit_langcode!='zh-tw'){
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $two_digit_langcode = explode('-',$two_digit_langcode);
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $two_digit_langcode= reset($two_digit_langcode);
}
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$post_list = array();
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$page_list = array();
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$attachment = array();

if(!empty($post_types)){
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $args = array();
    if(isset($post_types['post'])  && isset($post_types['page']) && $post_types['post'] == 'post' && $post_types['page'] == 'page'){
      // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $args = array(
          'post_type' => array('post','page'),
          'posts_per_page' => '10',
          'post_status'      => 'publish',
          'orderby'          => 'date',
          'order'            => 'DESC',
          );
    }
    
    if(!isset($post_types['page']) && isset($post_types['post']) && $post_types['post'] == 'post'){
      // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
      $args = array(
        'post_type' => 'post',
        'posts_per_page' => '10',
        'post_status'      => 'publish',
        'orderby'          => 'date',
        'order'            => 'DESC',
        );
    }

    if(!isset($post_types['post']) && isset($post_types['page']) && $post_types['page'] == 'page'){
      // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $args = array(
          'post_type' => 'page',
          'posts_per_page' => '10',
          'post_status'      => 'publish',
          'orderby'          => 'date',
          'order'            => 'DESC',
          );
    }
    if(!empty($get_categories)){
      // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
      foreach($get_categories as $categoryids){ 
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $category[] = $categoryids;
      }
      // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'category',
          'field'    => 'id',
          'terms'    => $category,
          'operator' => 'NOT IN',
        )
      );
    }
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $results = get_posts($args);
}
global $TRP_LANGUAGE;
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$active_languages = gnpub_get_active_language_slugs();
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$xsl_url =  GNPUB_URL . '/xml/gnpub-news-sitemap.xsl';
header('Content-Type: text/xml'); 
echo '<?xml version="1.0"  encoding="' . esc_attr( get_bloginfo('charset') ) . '" ?>'.PHP_EOL;
echo '<?xml-stylesheet type="text/xsl" href="'.esc_url( $xsl_url ).'" ?>'.PHP_EOL;
?>
<urlset  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  <?php if(!empty($results)){ 
      // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
      foreach($results as $result){ 
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        foreach($active_languages as $lang){
          // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
          $TRP_LANGUAGE = $lang;
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $tags     = get_the_terms( $result->ID, 'post_tag' );
  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
  $keywords=[];
  if ( $tags ){
    foreach ( $tags as $tag ){
      // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
      $keywords[] = $tag->name;
    }
   }
   // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
   $keywords = strtolower(  trim( implode( ', ', $keywords ), ', ' ) );
    ?> 
      <url>
        <loc><?php echo esc_url( apply_filters('gnpubpro_translated_url', get_permalink( $result->ID ) ) ); ?></loc>
          <news:news>
            <news:publication>
              <news:name>
                <?php 
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo htmlspecialchars($publication_name);
                ?>
                </news:name>
              <news:language>
                <?php 
                  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                  echo htmlspecialchars($two_digit_langcode);
                ?>
                </news:language>
            </news:publication>
            <news:publication_date>
              <?php 
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              echo get_date_from_gmt( $result->post_date_gmt, DATE_W3C ); 
              ?>
            </news:publication_date>
            <news:title>
              <?php 
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              echo htmlspecialchars( apply_filters( 'gnpubpro_translated_title', $result->post_title, $result->ID ) ); 
              ?>
            </news:title>
            <?php 
            if ( !empty( $keywords ) ){
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              echo "<news:keywords>" . htmlspecialchars( $keywords ) . '</news:keywords>'.PHP_EOL;
            }
            ?>
          </news:news>

          <?php 
          // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
          $images = array();
          if ( preg_match_all( '/<img [^>]+>/', $result->post_content, $matches ) ) {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            foreach ( $matches[0] as $img ) {
              if ( preg_match( '/src=("|\')([^"|\']+)("|\')/', $img, $match ) ) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                $src = $match[2];
                if ( strpos( $src, 'http' ) !== 0 ) {
                  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                  if ( $src[0] != '/' ){
                    continue;
                  }
                  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                  $src = get_bloginfo( 'url' ) . $src;
                }
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                if ( $src != esc_url( $src ) ){
                  continue;
                }
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                if ( isset( $url['images'][$src] ) ){
                  continue;
                }
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                $image = array();
                if ( preg_match( '/title=("|\')([^"\']+)("|\')/', $img, $match ) ){
                  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                  $image['title'] = str_replace( array( '-', '_' ), ' ', $match[2] );
                }

                if ( preg_match( '/alt=("|\')([^"\']+)("|\')/', $img, $match ) ){
                  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                  $image['alt'] = str_replace( array( '-', '_' ), ' ', $match[2] );
                }
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                $images[$src] = $image;
              }
            }
          }
        
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $post_thumbnail_id = get_post_thumbnail_id( $result->ID );

        if( $post_thumbnail_id ){
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            $post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            $images[$post_thumbnail_url] = $result->post_title;
         }
        if ( isset( $images ) && count( $images ) > 0 ) {
          // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
          foreach ( $images as $src => $img ) {
            echo "<image:image>\n";
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo "\t\t\t<image:loc>" . htmlspecialchars( $src ) . "</image:loc>\n";
            if ( isset( $img['title'] ) ){
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              echo "\t\t\t<image:title>" . htmlspecialchars( $img['title'] ) . "</image:title>\n";
            }
            if ( isset( $img['alt'] ) ){
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              echo  "\t\t\t<image:caption>" . htmlspecialchars( $img['alt'] ) . "</image:caption>\n";
            }
            echo  "\t\t</image:image>\n";
          }
        }
        ?>
      </url>
  <?php } } } 
    ?>
</urlset>
