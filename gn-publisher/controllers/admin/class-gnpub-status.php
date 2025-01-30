<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controllers handles status tab related operations
 * 
 * @since 1.5.19
 */
class GNPUB_Status {

	/**
	 * Render status related html content
	 * @since 1.5.19
	 * */
	public static function gnpub_render_status_tab_html() {

		$robots_url 			=	get_home_url().'/robots.txt';
		$robots_response 		=	self::get_remote_response( $robots_url );
		$robot_status 			=	$robots_response['status'];
		$status_class 			=	'dashicons dashicons-no-alt gnpub-fail-status';

		if ( $robot_status == 'success' ) {
			$status_class 		=	'dashicons dashicons-yes gnpub-success-status';	
		}

		$news_article_response 	=	self::check_for_news_schema();
		$schema_status 			=	$news_article_response['status'];
		$schema_class 			=	'dashicons dashicons-no-alt gnpub-fail-status';

		if ( $schema_status == 'success' ) {
			$schema_class 		=	'dashicons dashicons-yes gnpub-success-status';	
		}

		$byline_class 			=	'dashicons dashicons-no-alt gnpub-fail-status';

		if ( $schema_status == 'success' && $news_article_response['byline'] == 'yes' ) {
			$byline_class 		=	'dashicons dashicons-yes gnpub-success-status';	
		}
		
	?>
		<div class="gnpub-status-tab-wrapper">
			<div class="gnpub-index-tab-settings-list">

				<table class="form-table">
					<tbody>

						<tr>
							<th>
								<label class="gnpub-status-label"><?php echo esc_html__( 'Robots.txt', 'gn-publisher' ); ?></label>
							</th>
							<td>
								<span class="<?php echo esc_attr( $status_class ); ?>"></span>
							</td>
						</tr>

						<tr>
							<th>
								<label class="gnpub-status-label"><?php echo esc_html__( 'News Article Schema', 'gn-publisher' ); ?></label>
							</th>
							<td>
								<span class="<?php echo esc_attr( $schema_class ); ?>"></span>
							</td>
						</tr>

						<tr>
							<th>
								<label class="gnpub-status-label"><?php echo esc_html__( 'Byline', 'gn-publisher' ); ?></label>
							</th>
							<td>
								<span class="<?php echo esc_attr( $byline_class ); ?>"></span>
							</td>
						</tr>

					</tbody>
				</table>

			</div>
		</div> <!-- gnpub-status-tab-wrapper div end -->
	<?php

	}

	/**
	 * Check the URL and return status
	 * @since 1.5.19
	 * */
	public static function get_remote_response( $url, $method = 'get', $args = array() ){
		
		$remote_response 		=	array();
		$response 				=	array();
		$response['status'] 	=	'failure';
		$response['body'] 		=	'';

		if ( $method == 'get' ) {
			$remote_response 	=	wp_remote_get( $url, $args );
		}else{
			$remote_response 	=	wp_remote_post( $url, $args );
		}

		if ( ( ! is_wp_error( $remote_response )) && ( 200 === wp_remote_retrieve_response_code( $remote_response ) ) ) {

			$response['body'] 	=	wp_remote_retrieve_body( $remote_response );
			$response['status'] =	'success';

		}
		
		return $response;
	}

	/**
	 * Check if News Article schema option is enabled or
	 * any of the post contains News Article schema
	 * @since 	1.5.19
	 * */
	public static function check_for_news_schema(){
		
		$response 				=	array();
		$response['status'] 	=	'failure'; 		
		$response['body'] 		=	''; 		
		$response['byline'] 	=	'no'; 		

		$gnpub_options			= 	get_option( 'gnpub_new_options' );

		if ( ! empty( $gnpub_options['gnpub_enable_news_article_schema'] ) ) {

			$response['status'] =	'success';
			$response['byline'] =	'yes';

		}

		// If option is not enabled then check if post contains News Article schema
		if ( $response['status'] == 'failure' ) {

			$permalink 			=	'';

			$args = array(
			    'post_type'      => 'post',       // Post type
			    'post_status'    => 'publish',    // Only published posts
			    'posts_per_page' => 1,            // Limit to 1 post
			    'orderby'        => 'date',       // Order by post date
			    'order'          => 'DESC',       // Latest post first
			);

			// Execute the query
			$query 				=	new WP_Query( $args );

			// Check if there are posts
			if ( $query->have_posts() ) { 
				
				$query->the_post();

				// Get the permalink of the current post
    			$permalink 		=	get_permalink();

			}
			wp_reset_postdata();

			if ( ! empty( $permalink ) ) {

				$post_response 	=	self::get_remote_response( $permalink );
				if ( ! empty( $post_response['body'] ) ) {

					$pattern 	= '/<script type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is';
					preg_match_all( $pattern, $post_response['body'], $schema_match );

					if ( ! empty( $schema_match ) && ! empty( $schema_match[1] ) && is_array( $schema_match[1] ) ) {

						foreach ( $schema_match[1] as $schema_key => $schema ) {

							if ( ! empty( $schema ) && is_string( $schema ) ) {

								$decode_schema 	=	json_decode( $schema, true );
								if ( is_array( $decode_schema ) ) {

									foreach ( $decode_schema as $key => $value ) {

										if ( is_array( $value ) && ! empty( $value['@type'] ) ) {
											if ( $value['@type'] == 'NewsArticle' ) {

												if ( isset( $value['datePublished'] ) && isset( $value['dateModified'] ) ) {
													$response['byline'] =	'yes';
												}

												$response['status'] 	=	'success';
												break;
											}

										}
									} // $decode_schema foreach end

								} // $decode_schema if end

							} // $schema if end

						} // $schema_match[1] foreach end
				
					} // $schema_match if end

				} // $post_response['body'] if end 
				
			} // $permalink if end

		} // $response['status'] if end
		
		return $response;
	}

}