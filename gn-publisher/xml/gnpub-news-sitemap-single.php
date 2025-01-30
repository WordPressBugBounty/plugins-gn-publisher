<?php
$options_data = get_option( 'gnpub_news_sitmap' );
$options = unserialize($options_data);
$dgenre = '';
if(!empty($options) && !empty($options['news_sitemap_default_genre'])){
  $dgenre = $options['news_sitemap_default_genre'];
} 
$post_types = '';
if(!empty($options) && !empty($options['news_sitemap_include_post_types'])){
  $post_types = $options['news_sitemap_include_post_types'];
} 

$get_categories = '';
if(!empty($options) && !empty($options['news_sitemap_exclude_terms'])){
  $get_categories = $options['news_sitemap_exclude_terms'];
} 

$publication_name = get_bloginfo('name');
if(!empty($options) && !empty($options['publication_name'])){
  $publication_name = $options['publication_name']?$options['publication_name']:get_bloginfo('name');
} 

$two_digit_langcode = get_bloginfo('language');
if($two_digit_langcode!='zh-cn' && $two_digit_langcode!='zh-tw'){
  $two_digit_langcode = explode('-',$two_digit_langcode);
  $two_digit_langcode= reset($two_digit_langcode);
}
$post_list = array();
$page_list = array();
$attachment = array();

if(!empty($post_types)){
  
    $args = array();
    if(isset($post_types['post'])  && isset($post_types['page']) && $post_types['post'] == 'post' && $post_types['page'] == 'page'){
        $args = array(
          'post_type' => array('post','page'),
          'posts_per_page' => '10',
          'post_status'      => 'publish',
          'orderby'          => 'date',
          'order'            => 'DESC',
          );
    }
    
    if(!isset($post_types['page']) && isset($post_types['post']) && $post_types['post'] == 'post'){
      $args = array(
        'post_type' => 'post',
        'posts_per_page' => '10',
        'post_status'      => 'publish',
        'orderby'          => 'date',
        'order'            => 'DESC',
        );
    }

    if(!isset($post_types['post']) && isset($post_types['page']) && $post_types['page'] == 'page'){
        $args = array(
          'post_type' => 'page',
          'posts_per_page' => '10',
          'post_status'      => 'publish',
          'orderby'          => 'date',
          'order'            => 'DESC',
          );
    }
    if(!empty($get_categories)){
      foreach($get_categories as $categoryids){ 
        $category[] = $categoryids;
      }
      $args['tax_query'] = array(
        array(
          'taxonomy' => 'category',
          'field'    => 'id',
          'terms'    => $category,
          'operator' => 'NOT IN',
        )
      );
    }
    $results = get_posts($args);
    if(isset($post_types['attachment']) && $post_types['attachment'] == 'attachment'){
      $args1 = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 10,
        'orderby'          => 'date',
        'order'          => 'DESC'
        );
      $attachment = new WP_Query($args1);
      $attachment_list = get_posts($attachment);
      
    }
}
$xsl_url = plugin_dir_url( __FILE__ ).'gnpub-news-sitemap.xsl';
header('Content-Type: text/xml'); 
echo '<?xml version="1.0"  encoding="' . get_bloginfo('charset') . '" ?>'.PHP_EOL;
echo '<?xml-stylesheet type="text/xsl" href="'.$xsl_url.'" ?>'.PHP_EOL;
?>
<urlset  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  <?php if(!empty($results)){ 
  foreach($results as $result){ 
  $tags     = get_the_terms( $result->ID, 'post_tag' );
  $keywords=[];
  if ( $tags ){
    foreach ( $tags as $tag )
      $keywords[] = $tag->name;
   }
   $keywords = strtolower(  trim( implode( ', ', $keywords ), ', ' ) );
    ?> 
      <url>
        <loc><?php echo apply_filters('gnpubpro_translated_url', get_permalink( $result->ID ) ); ?></loc>
          <news:news>
            <news:publication>
              <news:name><?php echo htmlspecialchars($publication_name);?></news:name>
              <news:language><?php echo htmlspecialchars($two_digit_langcode);?></news:language>
            </news:publication>
            <news:publication_date><?php echo get_date_from_gmt( $result->post_date_gmt, DATE_W3C ); ?></news:publication_date>
            <news:title><?php echo htmlspecialchars( apply_filters( 'gnpubpro_translated_title', $result->post_title, $result->ID ) ); ?></news:title>
            <?php 
            if ( !empty( $keywords ) ){
              echo "<news:keywords>" . htmlspecialchars( $keywords ) . '</news:keywords>'.PHP_EOL;
            }
            ?>
          </news:news>

          <?php $images = array();
          if ( preg_match_all( '/<img [^>]+>/', $result->post_content, $matches ) ) {
            foreach ( $matches[0] as $img ) {
              if ( preg_match( '/src=("|\')([^"|\']+)("|\')/', $img, $match ) ) {
                $src = $match[2];
                if ( strpos( $src, 'http' ) !== 0 ) {
                  if ( $src[0] != '/' )
                    continue;
                  $src = get_bloginfo( 'url' ) . $src;
                }

                if ( $src != esc_url( $src ) )
                  continue;

                if ( isset( $url['images'][$src] ) )
                  continue;

                $image = array();
                if ( preg_match( '/title=("|\')([^"\']+)("|\')/', $img, $match ) )
                  $image['title'] = str_replace( array( '-', '_' ), ' ', $match[2] );

                if ( preg_match( '/alt=("|\')([^"\']+)("|\')/', $img, $match ) )
                  $image['alt'] = str_replace( array( '-', '_' ), ' ', $match[2] );

                $images[$src] = $image;
              }
            }
          }
        
        // Also check if the featured image value is set. 
        $post_thumbnail_id = get_post_thumbnail_id( $result->ID );

        if( $post_thumbnail_id ){
            $post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
            $images[$post_thumbnail_url] = $result->post_title;
         }
        if ( isset( $images ) && count( $images ) > 0 ) {
          foreach ( $images as $src => $img ) {
            echo "<image:image>\n";
            echo "\t\t\t<image:loc>" . htmlspecialchars( $src ) . "</image:loc>\n";
            if ( isset( $img['title'] ) ){
              echo "\t\t\t<image:title>" . htmlspecialchars( $img['title'] ) . "</image:title>\n";
            }
            if ( isset( $img['alt'] ) ){
              echo  "\t\t\t<image:caption>" . htmlspecialchars( $img['alt'] ) . "</image:caption>\n";
            }
            echo  "\t\t</image:image>\n";
          }
        }
        ?>
      </url>
  <?php } }  ?>
</urlset>