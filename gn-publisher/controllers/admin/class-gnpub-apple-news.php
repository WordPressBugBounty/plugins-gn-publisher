<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controller is responsible for handling apple news publish
 * 
 * @since 1.5.24
 */
class GNPUB_Apple_News {

	public $api_url 	=	"https://news-api.apple.com/";

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) ) ;
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'wp_ajax_gnpub_apple_news_publish', array( $this, 'gnpub_apple_news_publish_clbk' ) );
		add_action( 'gnpub_render_apple_news_compatibility', array( $this, 'render_apple_news' ) );

	}

	/**
	 * Generate apple news options
	 * @since 	1.5.24
	 * */
	public function render_apple_news() {
		$gnpub_options = get_option( 'gnpub_new_options' );
		$gnpub_apple_news = isset( $gnpub_options['gnpub_apple_news'] ) ? $gnpub_options['gnpub_apple_news']: false;
		$gnpub_apple_news_channel_id = isset( $gnpub_options['gnpub_apple_news_channel_id'] ) ? $gnpub_options['gnpub_apple_news_channel_id']: '';
		$gnpub_apple_news_api_key_id = isset( $gnpub_options['gnpub_apple_news_api_key_id'] ) ? $gnpub_options['gnpub_apple_news_api_key_id']: '';
		$gnpub_apple_news_api_key_secret = isset( $gnpub_options['gnpub_apple_news_api_key_secret'] ) ? $gnpub_options['gnpub_apple_news_api_key_secret']: '';
		$gnpub_apple_opt_class      = 'gnpub-d-none';
		if ( $gnpub_apple_news ) {
		  $gnpub_apple_opt_class    = '';
		}

		?>
		<tr>
	        <th><label for="gnpub-apple-news" class="gnpub-hover-pointer"><?php esc_html_e( 'Apple News', 'gn-publisher' ); ?></label></th>
	        <td>
	          <input type="checkbox" name="gnpub_apple_news" id="gnpub-apple-news" <?php checked( $gnpub_apple_news, true ) ?>/>
	        </td>
	    </tr>
	    <tr class="gnpub-apple-news-options <?php echo esc_attr( $gnpub_apple_opt_class ); ?>">
	        <th class="gnpub-child-set-options"><label><?php echo esc_html__( 'Channel ID', 'gn-publisher' ); ?></label></th>  
	        <td>
	          <input type="text" name="gnpub_apple_news_channel_id" id="gnpub-apple-news-channel-id" value="<?php echo esc_attr( $gnpub_apple_news_channel_id ); ?>" placeholder="Channel ID" size="60">
	        </td>
	    </tr>
	    <tr class="gnpub-apple-news-options <?php echo esc_attr( $gnpub_apple_opt_class ); ?>">
	        <th class="gnpub-child-set-options"><label><?php echo esc_html__( 'API Key ID', 'gn-publisher' ); ?></label></th>  
	        <td>
	          <input type="text" name="gnpub_apple_news_api_key_id" id="gnpub-apple-news-api-key-id" value="<?php echo esc_attr( $gnpub_apple_news_api_key_id ); ?>" placeholder="API Key ID" size="60">
	        </td>
	    </tr>
	    <tr class="gnpub-apple-news-options <?php echo esc_attr( $gnpub_apple_opt_class ); ?>">
	        <th class="gnpub-child-set-options"><label><?php echo esc_html__( 'API Key Secret', 'gn-publisher' ); ?></label></th>  
	        <td>
	          <input type="text" name="gnpub_apple_news_api_key_secret" id="gnpub-apple-news-api-key-secret" value="<?php echo esc_attr( $gnpub_apple_news_api_key_secret ); ?>" placeholder="API Key Secret" size="60">
	        </td>
	    </tr>
		<?php

	}

	/**
	 * Check if applenews is enabled and is active
	 * @since 	1.5.24
	 * */
	public function is_settings_valid() {
		
		$gn_options 	=	get_option( 'gnpub_new_options' );
		// Check if apple news is enabled and configured
		if ( ! empty( $gn_options['gnpub_apple_news'] ) && ! empty( $gn_options['gnpub_apple_news_channel_id'] ) && ! empty( $gn_options['gnpub_apple_news_api_key_id'] ) && ! empty( $gn_options['gnpub_apple_news_api_key_secret'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get apple news configuration credentials
	 * @since 	1.5.24
	 * */
	public function get_config() {
		
		$config 		=	array();
		$gn_options 	=	get_option( 'gnpub_new_options' );	
		
		$config['channel_id'] 		=	isset( $gn_options['gnpub_apple_news_channel_id'] ) ? $gn_options['gnpub_apple_news_channel_id'] : '';		
		$config['api_key_id'] 		=	isset( $gn_options['gnpub_apple_news_api_key_id'] ) ? $gn_options['gnpub_apple_news_api_key_id'] : '';		
		$config['api_key_secret'] 	=	isset( $gn_options['gnpub_apple_news_api_key_secret'] ) ? $gn_options['gnpub_apple_news_api_key_secret'] : '';		

		return $config;

	}

	/**
	 * Enqueue scripts and styles
	 * @param 	$hook_suffix 	string
	 * @since 	1.5.24
	 * */
	public function enqueue_scripts_and_styles( $hook_suffix ) {
		
		if ( $hook_suffix == "settings_page_gn-publisher-settings" || $hook_suffix == 'admin_page_gnpub-setup-wizard' || $hook_suffix == 'post.php' ) {
		
			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script('gn-admin-applenews-script', GNPUB_URL . "/assets/js/gn-admin-apple-news{$min}.js", array('jquery'), GNPUB_VERSION, true );
			wp_localize_script('gn-admin-applenews-script', 'gn_script_apple_news_vars', array(
				'gnpub_apple_news_security_nonce'	=> 	wp_create_nonce( 'gnpub_apple_news_check_nonce' ),
				'post_id' 							=>	isset( $_GET['post'] ) ? intval( $_GET['post'] ) : 0,	
				)
			);

			wp_enqueue_script( 'gn-admin-applenews-script' );

		}
	}

	/**
	 * Add meta boxes to the post types
	 * @since 1.5.24
	 * */
	public function add_meta_boxes() {
		
		$gn_options 	=	get_option( 'gnpub_new_options' );

		// Check if apple news is enabled and configured
		if ( $this->is_settings_valid() ) {
			
			$post_types = get_post_types( [ 'public' => true ], 'names' );

			if ( ! empty( $post_types ) ) {
				foreach ( $post_types as $key => $value ) {
					add_meta_box( 
		                'gnpub_apple_news_meta_options', 
		                esc_html__( 'Apple News', 'schema-and-structured-data-for-wp' ), 
		                array( $this, 'apple_news_meta_callback' ),
		                $value,
		                'side', 
		                'default' 
		            );
				}
	        }

		}

	}

	/**
	 * Callback function for meta box
	 * @param 	$post 	WP_Post
	 * @since 	1.5.24
	 * */
	public function apple_news_meta_callback( $post ) {
		?>
		<tr class="gnpub-apple-news-meta-wrapper" >
			<td>
				<button type="button" id="gnpub-apple-news-publish-btn" class="button button-primary"><?php echo esc_html__( 'Publish to Apple News', 'gn-publisher' ) ?></button>
				<div class="description gnpub-apple-news-note" style="margin-top: 15px;"><?php echo esc_html__( 'Click on publish apple news button to publish the article on apple news publisher platform', 'gn-publisher' ); ?></div>
			</td>
		</tr>
		<?php	
	}

	/**
	 * Process ajax request
	 * @since 	1.5.24
	 * */
	public function gnpub_apple_news_publish_clbk() {
		
		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['gnpub_apple_news_security_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['gnpub_apple_news_security_nonce'], 'gnpub_apple_news_check_nonce' ) ) {
			return;
		}

		if ( ! empty( $_POST['post_id'] ) && $_POST['post_id'] > 0 ) {
			$post_id 	=	intval( $_POST['post_id'] );
			$post 		=	get_post( $post_id );

			$api_data 	=	$this->gnpub_publish_apple_news( $post );
			wp_send_json( $api_data );
			wp_die();
		}

	}

	/**
	 * Publish apple news article
	 * @param 	$post 	WP_Post
	 * @since 	1.5.24
	 * */
	public function gnpub_publish_apple_news( $post ) {
		
		if ( is_object( $post ) && ! empty( $post->ID ) ) {
			
			$prepare_json 	=	$this->gnpub_prepare_anf_json( $post );
			$make_call 		=	$this->gnpub_call_api( 'POST', $prepare_json );
			
			return $make_call;
		}

	}

	/**
	 * Prepare anf request format
	 * @param 	$post 	WP_Post
	 * @return 	$anf 	array
	 * @since 	1.5.24
	 * 
	 * */
	public function gnpub_prepare_anf_json( $post ) {
		
		$post_id 			=	$post->ID;
		$anf 				=	array();
		$anf['version'] 	=	'1.11';	
		$anf['identifier'] 	=	'post-' . $post_id;	
		$anf['language'] 	=	apply_filters( 'gnpub_modify_anf_request_language', get_locale(), $post_id );
		$anf['title'] 		=	get_the_title( $post_id );


		$anf['components'][0]['role'] 	=	'title';
		$anf['components'][0]['text'] 	=	get_the_title( $post_id );

		$anf['components'][1]['role'] 	=	'body';
		$anf['components'][1]['text'] 	=	wp_strip_all_tags( apply_filters( 'the_content', $post->post_content ) );

		$post_thumbnail 				=	get_the_post_thumbnail_url( $post );
		if ( ! empty( $post_thumbnail ) ) {
			$anf['metadata']['thumbnailURL']=	get_the_post_thumbnail_url( $post );
		}
		$anf['metadata']['authors'] 	=	get_the_author_meta( 'display_name', $post->post_author );
		$anf['metadata']['datePublished'] =	get_the_date( 'c', $post );
		$anf['metadata']['dateModified'] =	get_the_modified_date( 'c', $post );
		$anf['metadata']['canonicalURL']=	get_permalink( $post );
		
		return $anf;

	}

	/**
	 * Get auth header
	 * @param 	$method 	string
	 * @param 	$path 		string
	 * @param 	$body 		string
	 * @return 	$header 	array
	 * @since 	1.5.24
	 * */
	public function gnpub_get_auth_header( $method, $path, $body = '' ) {
		
		$config = $this->get_config();

	    $api_key_id     = $config['api_key_id'];
	    $api_key_secret = $config['api_key_secret'];

	    $date = gmdate('Y-m-d\TH:i:s\Z');
	    $canonical_request = $method . "\n" . $path . "\n" . $date . "\n" . $body;
	    $signature = base64_encode(hash_hmac('sha256', $canonical_request, $api_key_secret, true));

	    return [
	        'Authorization' => "HHMAC; key=$api_key_id; signature=$signature; date=$date",
	        'Content-Type'  => 'application/json',
	    ];

	}

	/**
	 * Make a call to apple news publish api
	 * @param	$json 	array
	 * @since 	1.5.24 	
	 * */
	public function gnpub_call_api( $method, $json ) {
		
		$status 	=	true;
		$message 	=	'';

		$config 	=	$this->get_config();
		$path 		=	"channels/{$config['channel_id']}/articles";
    	$url  		=	$this->api_url . $path;
    	$json 		=	wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		$headers 	=	$this->gnpub_get_auth_header( $method, $url, $json );

		$response = wp_remote_post($url, [
	        'headers' =>	$headers,
	        'body'    =>	$json,
	        'timeout' =>	30,
	    ]);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$response = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $response ) && ! empty( $response['errors'] ) && ! empty( $response['errors'][0] ) ) {
				$message 	=	isset( $response['errors'][0]['code'] ) ? $response['errors'][0]['code'] : 'Due to technical issue request could not be processed'; 
			}
			$status 	=	false;
		}else{
			$response 	=	json_decode( wp_remote_retrieve_body( $response ) );
			$status 	=	true;
			
		}

		$return_data 	=	array( 'status' => $status, 'message' => $message, 'response' => $response );	
		return $return_data;

	}

}

new GNPUB_Apple_News();