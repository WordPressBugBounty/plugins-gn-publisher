<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if NewsArticle option is enabled and prepare markup
 * @since 1.5.19
 * */
function gnpub_prepare_newsartile_schema_markup(  ){

	global $post;

	$category_detail 				= get_the_category(get_the_ID());//$post->ID
    $article_section 				= '';

    if ( $category_detail ) {

        foreach ( $category_detail as $cd ){

            if ( is_object($cd ) ) {
                $article_section 	=  $cd->cat_name;
            }                                        

        }

    }

    $thumbnail_url 					= 	'';
    $image 							=	array();
    $image_details 	 				= 	wp_get_attachment_image_src( get_post_thumbnail_id() );
    if ( is_array( $image_details ) ) {

    	if ( isset( $image_details[0] ) ) {

    		$thumbnail_url 			=	esc_url( $image_details[0] );

    		$image['@type']			=	'ImageObject';	
    		$image['url']			=	esc_url( $image_details[0] );
    		$image['width']			=	isset( $image_details[1] ) ? $image_details[1]:'';
    		$image['height']		=	isset( $image_details[2] ) ? $image_details[2]:'';
    	}
    }


    $content 						= 	wp_strip_all_tags( get_the_content() );	
    $reading_details 				=	gnpub_reading_time_and_word_count();
    $word_count 					=	'';
    $time_required 					=	'';

    if ( is_array( $reading_details ) ) {
    	if ( isset( $reading_details['word_count'] ) ) {
    		$word_count 			=	intval( $reading_details['word_count'] );	
    	}
    	if ( isset( $reading_details['timerequired'] ) ) {
    		$time_required 			=	esc_html( $reading_details['timerequired'] );	
    	}
    }

	$input 		=	array();

	$input['@context']				=	gnpub_schema_context_url();
	$input['@type']					=	'NewsArticle';
	$input['@id']					=	get_permalink().'#newsarticle';
	$input['url']					=	get_permalink();
	if ( ! empty( $image ) ) {
		$input['image']				=	$image;
	}
	$input['headline']				=	get_the_title();
	$input['mainEntityOfPage']		=	get_permalink();
	$input['datePublished']			=	get_the_date("c");
	$input['dateModified']			=	get_the_modified_date("c");
	$input['description']			=	gnpub_get_the_excerpt();
	$input['articleSection']		=	$article_section;
	$input['articleBody']           = 	$content;
	$input['keywords']           	= 	gnpub_get_the_tags();
	$input['name']           		= 	get_the_title();
	$input['thumbnailUrl']          = 	$thumbnail_url;
	$input['wordCount']          	= 	$word_count;
	$input['timeRequired']          = 	$time_required;
	$input['mainEntity']          	= 	array(
                                            '@type' => 'WebPage',
                                            '@id'   => get_permalink(),
                                    	);
	$input['author']				=	gnpub_get_author_details();
	$input['editor']				=	gnpub_get_author_details();
	
	return $input;

}

/**
 * Get schema context URL
 * @since 1.5.19
 * */
function gnpub_schema_context_url() {
    
    $url 		= 'http://schema.org/';
    
    if ( is_ssl() ) {
        $url 	= 'https://schema.org/';
    }
    
    return $url;
}

/**
 * Get Post excerpt
 * @since 1.5.19
 * */
function gnpub_get_the_excerpt(){

	global $post;
	$excerpt 				= '';

	if ( is_object( $post ) ) {

        $excerpt 		 	= $post->post_excerpt;

        if ( empty ( $excerpt ) ) {

            $post_content 	= wp_strip_all_tags( strip_shortcodes( $post->post_content ) ); 
            $post_content 	= preg_replace( '/\[.*?\]/','', $post_content );

            $excerpt_length = apply_filters( 'excerpt_length', 55 );                        
            $excerpt_more 	= '';
            $excerpt      	= wp_trim_words( $post_content, $excerpt_length, $excerpt_more );
        }

        if ( strpos( $excerpt, "<p>" ) !==false ) {

            $regex = '/<p>(.*?)<\/p>/';
            preg_match_all( $regex, $excerpt, $matches );

            if ( is_array( $matches[1] ) ) {
                $excerpt = implode( " ", $matches[1] ); 
            }

        }
    }

    return $excerpt;

}

/**
 * Get Post tags
 * @since 1.5.19
 * */
function gnpub_get_the_tags() {

	global $post;

	$tags_str 	=	'';
    if ( is_object ( $post ) ) {
        
      $tags = get_the_tags( $post->ID );
      
      if ( $tags ) {
          foreach( $tags as $tag ) {
            $tags_str .= $tag->name.', ';  
          }
      }
    }

    return $tags_str;

}

/**
 * Get Post word count and reading time
 * @since 1.5.19
 * */
function gnpub_reading_time_and_word_count() {
    
    global $post;
    // Predefined words-per-minute rate.
    $words_per_minute 	=	225;
    $words_per_second 	=	$words_per_minute / 60;

    // Count the words in the content.
    $word_count      	= 	0;
    $text            	= trim( wp_strip_all_tags( @get_the_content() ) );
    
    if ( ! $text && is_object( $post ) ) {
        $text 			= $post->post_content;
    }    
    $word_count      	= substr_count( " $text ", ' ' );
    // How many seconds (total)?
    $seconds 			= floor( $word_count / $words_per_second );
    
    $timereq 			= '';

    if ( $seconds > 60 ) {

        $minutes      	= floor($seconds/60);        
        $seconds_left 	= $seconds % 60;
        
        $timereq 		= 'PT'.$minutes.'M'.$seconds_left.'S';

    }else{
        $timereq 		= 'PT'.$seconds.'S';
    }

    return array( 'word_count' => esc_attr( $word_count ), 'timerequired' => esc_attr( $timereq ) );
}

/**
 * Get Post author details
 * @since 1.5.19
 * */
function gnpub_get_author_details() {

	global $post;
	$author_details 	=	array();

    $author_id          =	get_the_author_meta('ID');
    $author_name 	    =	get_the_author();
    $author_desc        =	get_the_author_meta( 'user_description' );     

    if ( ! $author_name && is_object( $post ) ) {
        $author_id    	=	get_post_field ( 'post_author', $post->ID );
        $author_name  	=	get_the_author_meta( 'display_name' , $author_id );             
    }

    $author_meta 		=	get_user_meta($author_id);

    $author_url   		=	get_author_posts_url( $author_id ); 
    $same_as      		=	array();

    $social_links 		=	array('url', 'facebook', 'twitter', 'instagram', 'linkedin', 'myspace', 'pinterest', 'soundcloud', 'tumblr', 'youtube', 'wikipedia', 'jabber', 'yim', 'aim', 'threads', 'mastodon');

    foreach ( $social_links as $links ) {

        $url  = get_the_author_meta($links, $author_id );

        if ( $url ) {
            $same_as[] = $url;
        }

    }
                    
    $author_image = array();
    
    if ( function_exists( 'get_avatar_data') &&  ! empty( get_option( 'show_avatars' ) ) ) {
        $author_image	= get_avatar_data( $author_id );
    }
            
    $author_details['@type']           = 'Person';
    $author_details['name']            = esc_attr( $author_name );
    if ( ! empty( $author_desc) ) {
        $author_details['description'] = wp_strip_all_tags( strip_shortcodes( $author_desc ) ); 
    }else{
        if ( ! empty( $author_meta['author_bio'][0] ) ) {
            $author_details['description'] =   $author_meta['author_bio'][0];
        }
    }
    $author_details['url']             = esc_url( $author_url );
    $author_details['sameAs']          = $same_as;

    if ( ! empty( $author_meta['knowsabout'][0]) ) {
        $author_details['knowsAbout'] 	=   explode( ',', $author_meta['knowsabout'][0] );
    }

    if ( ! empty( $author_meta['honorificsuffix'][0] ) ) {
        $author_details['honorificSuffix'] =  $author_meta['honorificsuffix'][0];
    }

    if ( ! empty( $author_meta['alumniof'][0] ) ) {
        $str =  $author_meta['alumniof'][0];
        $itemlist = explode( ",", $str );
        if ( ! empty( $itemlist ) ) {
            foreach ( $itemlist as $key => $list ){
                $vnewarr['@type'] = 'Organization';
                $vnewarr['Name']   = $list;   
                $author_details['alumniOf'][] = $vnewarr;
            }
        }
        
    }

    if ( ! empty( $author_meta['author_image'][0]) ) {
        $author_image =  wp_get_attachment_image_src($author_meta['author_image'][0]);
        if ( ! empty( $author_image) ) {
            $author_details['image']['@type']  = 'ImageObject';
            $author_details['image']['url']    = $author_image[0];
            $author_details['image']['height'] = $author_image[1];
            $author_details['image']['width']  = $author_image[2];
        }
    }elseif ( isset( $author_image['url']) && isset($author_image['height']) && isset($author_image['width']) ) {

        $author_details['image']['@type']  = 'ImageObject';
        $author_details['image']['url']    = $author_image['url'];
        $author_details['image']['height'] = $author_image['height'];
        $author_details['image']['width']  = $author_image['width'];
    }
     
    return $author_details; 
}

/**
 * Add schema markup to post content
 * @since 1.5.19
 * */
add_filter( 'wp', 'gnpub_output_schema_markup_on_posts', 999 );
function gnpub_output_schema_markup_on_posts( ){

	if ( ! is_admin() && is_singular( 'post' ) ) { 
		ob_start( 'gnpub_generate_and_add_markup_to_content' );
	}

}

/**
 * Generate schea markup
 * @since 1.5.19
 * */
function gnpub_generate_and_add_markup_to_content( $content ){

	global $post;
	$gnpub_options		= 	get_option( 'gnpub_new_options' );

	if ( ! empty( $gnpub_options['gnpub_enable_news_article_schema'] ) ) {

		$markup 		=	gnpub_prepare_newsartile_schema_markup();

		if ( ! empty( $markup ) ) {
			
			$markup 	=	wp_json_encode( $markup );

			$response_html =	'';
			$response_html =	"\n";
			$response_html.= '<script type="application/ld+json" class="gnpub-schema-markup-output">'; 
            $response_html.= "\n";       
            $response_html.= $markup;       
            $response_html.= "\n";
            $response_html.= '</script>';
            $response_html.= "\n";

            $content = str_replace( '</head>', $response_html.'</head>', $content );

		}

	}
	
	return $content;
}