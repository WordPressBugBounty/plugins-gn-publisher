<?php
// namespace GNPUB\Controllers\Admin;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controllers handles instant index related actions.
 * 
 * @since 1.5.19
 */
class GNPUB_Instant_Index {

	public function __construct(){

		add_action( 'admin_enqueue_scripts', array( $this, 'gnpub_admin_instant_index_script' ) );
		add_action( 'admin_init', array( $this, 'gnpub_save_index_settings_data' ) );
		add_action( 'wp_ajax_gnpub_ociaifs_giapi', array( $this, 'gnpub_ociaifs_giapi' ) );	

		$this->add_auto_indexing();
		add_action( 'trashed_post', array( $this, 'delete_post' ) );

	}

	/**
	 * Render Index tab html
	 * @since 1.5.19
	 * */
	public static function gnpub_render_index_tab_html() {
		?>
		
	    <form enctype="multipart/form-data" method="post" action="">

	    	<?php 
	    	self::file_upload_form(); 
	    	self::automatic_indexing_form(); 
	    	self::instant_indexing_form(); 
	    	?>

	    	<?php wp_nonce_field( 'gnpub_save_index_settings_nonce', 'gnpub_save_index_settings_nonce' ); ?>
	    	<div id="gnpub-index-btn-wrapper" style="margin-top:40px;">
	      		<input type="submit" name="gnpub_save_index_settings" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'gn-publisher' ); ?>" />
	      	</div>
		</form>

		<div id="gnpub-indexing-giapi-response-body" class="not-ready">
		    <br>
		    <hr>
		    <div class="gnpub-indexing-response-box">
		        <code class="gnpub-indexing-response-id"></code>
		        <h4 class="gnpub-indexing-response-status"></h4>
		        <p class="gnpub-indexing-response-message"></p>
		    </div>		
		</div>
		<?php
	}

	/**
	 * Render google key file upload template
	 * @since 1.5.19
	 * */
	public static function file_upload_form() {
		?>
		<div class="gnpub-index-tab-settings-list">
    		<ul>
    			<li>
                    <div class="gnpub-index-tab-label" style="width: 300px;">
                        <p class="gnpub-index-tab-heading"><?php echo esc_html__( 'Google JSON Key:', 'gn-publisher' ); ?></p><br>
                        <small><?php echo esc_html__( 'Upload the Service Account JSON key file you obtained from Google API Console or paste its contents in the field.', 'gn-publisher' ); ?> <a target="_blank" href="https://gnpublisher.com/docs/"><?php echo esc_html__( 'Learn more', 'gn-publisher' ); ?></a></small>
                    </div> 
                    <div class="gnpub-index-tab-field">
                    	<?php 
                    	$gapi_settings 	=	get_option( 'gnpub_google_index_api_settings' );
                    	?>
                    	<textarea name="gnpub_index_json_key" class="regular-text code" style="min-width: 600px;" rows="6"><?php echo esc_textarea( $gapi_settings ); ?></textarea>
                    	<br>
                    	<label>
						<?php echo esc_html__( 'Or upload JSON file: ', 'gn-publisher' ); ?>
						<input type="file" name="gnpub_index_json_file" />
					</label>
                    </div>                                             
                </li>
            </ul>
    	</div>
		<?php
	}

	/**
	 * Render automatic indexing template
	 * @since 1.5.19
	 * */
	public static function automatic_indexing_form(){

		$gnpub_options 		=	get_option( 'gnpub_new_options' );
		$post_types 		= 	get_post_types( array( 'public' => true ), 'objects' ); 

		?>
		<div class="gnpub-index-tab-settings-list">
		    <div>
				<h2><?php echo esc_html__('Enable On ( Automatic )', 'gn-publisher' ); ?></h2>
			</div>
		    <p> 
		    	<?php echo esc_html__('To instant indexing from these post types automatically in the background when a post is published, edited, or deleted', 'gn-publisher' ); ?>
		    	<a target="_blank" href="https://gnpublisher.com/docs/"><?php echo esc_html__( 'Learn more', 'gn-publisher' ); ?></a>
		    </p>
    		<ul>
    			<li>
    				<div class="gnpub-index-tab-label">
                        <label class="gnpub-index-tab-heading"><?php echo esc_html__( 'Post Types', 'gn-publisher' ); ?></label>
                    </div>
                    <div class="gnpub-index-tab-field">
		                <?php
		                    if( ! empty( $post_types ) ) {

		                        unset( $post_types['attachment'], $post_types['saswp'], $post_types['saswp_reviews'], $post_types['saswp-collections'], $post_types['saswp_template'], $post_types['saswp_reviews_server'] );
		                        
		                        foreach ( $post_types as $post_type ) {

		                        	$chk_field_id 		=	'gnpub-instant-index-'.$post_type->name;
		                        	$chk_field_name 	=	'gnpub_instant_indexing['.$post_type->name.']';
		                        	$chk_field_val 		=	false;

		                        	if ( isset( $gnpub_options['gnpub_instant_indexing'] ) && isset( $gnpub_options['gnpub_instant_indexing'][ $post_type->name ] ) ) {
		                        		$chk_field_val 		=	$gnpub_options['gnpub_instant_indexing'][ $post_type->name ];
		                        	}
		                        ?>

		                        	<input id="<?php echo esc_attr( $chk_field_id ); ?>" type="checkbox" value="1" name="<?php echo esc_attr( $chk_field_name ); ?>" <?php checked( $chk_field_val, true ); ?> /> 
		                        	<label for="<?php echo esc_attr( $chk_field_id ); ?>" class="gnpub-hover-pointer"><?php echo esc_html( $post_type->label ); ?></label> <br>
		                        <?php

		                        }

		                    }
		                                
		                ?>
                    </div>
    			</li>
    		</ul>
    	</div>
    	<?php
	}

	public static function instant_indexing_form() {

		$indexing_action 	= 	isset( $gnpub_options['gnpub_instant_index_action'] ) ? $gnpub_options['gnpub_instant_index_action'] : false; 
		?>
		<div class="gnpub-index-tab-heading">
	    	<h2><?php echo esc_html__( 'Instant Console ( Manual )', 'gn-publisher' ); ?></h2>
	    </div>

    	<div class="gnpub-index-tab-settings-list">
    		<ul>
    			<li>
                    <div class="gnpub-index-tab-label">
                        <label for="giapi-url" class="gnpub-index-tab-manual-label" ><?php echo esc_html__('URLs (one per line, up to 50):', 'gn-publisher' ); ?></label>
                    </div>    
                    <div class="gnpub-index-tab-field">
                    	<textarea name="url" id="gnpub-giapi-url" class="regular-text code" style="min-width: 600px;" rows="6"></textarea>
                    </div>                                          
                </li>
                <li>
                	<div class="gnpub-index-tab-label">
                        <label class="gnpub-index-tab-manual-label"><?php echo esc_html__('Action:', 'gn-publisher' ); ?></label>

                        <input type="radio" class="gnpub-i-i-action" id="gnpub-index-tab-update-action" name="gnpub_instant_index_action" value="update"  <?php echo checked($indexing_action, 'update') ?>   /> <label for="gnpub-index-tab-update-action" class="gnpub-hover-pointer"><?php echo esc_html__('Publish/Update', 'gn-publisher' ); ?></label> <br>
                        <input type="radio" class="gnpub-i-i-action" id="gnpub-index-tab-remove-action" name="gnpub_instant_index_action" value="remove"   <?php echo checked($indexing_action, 'remove') ?> /> <label for="gnpub-index-tab-remove-action" class="gnpub-hover-pointer"><?php echo esc_html__('Remove', 'gn-publisher' ); ?> </label> <br>
                        <input type="radio" class="gnpub-i-i-action" id="gnpub-index-tab-status-action" name="gnpub_instant_index_action" value="getstatus" <?php echo checked($indexing_action, 'getstatus') ?> /> <label for="gnpub-index-tab-status-action" class="gnpub-hover-pointer"><?php echo esc_html__('Get status', 'gn-publisher' ); ?> </label>
                        <br>
                        <br>
                        <a class="button button-default" id="gnpub-instant-indexing-send"><?php echo esc_html__('Send For Indexing', 'gn-publisher' ); ?></a>
                    </div>
                </li>
    		</ul>
    	</div>
		<?php
	}

	/**
	 * Enqueue instant index related script
	 * @since 1.5.19
	 * */
	public function gnpub_admin_instant_index_script( $hook_suffix ) {

		if ( $hook_suffix == "settings_page_gn-publisher-settings" || $hook_suffix == 'admin_page_gnpub-setup-wizard' ) {

			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'gn-admin-instant-index-script', GNPUB_URL . "/assets/js/gn-admin-index{$min}.js", array('jquery'), GNPUB_VERSION, 'true' );
			
			$current_screen = get_current_screen(); 		

			$data = array(     
				'ajax_url'                     	=> admin_url( 'admin-ajax.php' ),             
				'gnpub_index_security_nonce'   	=> wp_create_nonce('gnpub_index_ajax_check_nonce'),
				'l10n_success'      			=> esc_html__( 'Success', 'gn-publisher' ),
            	'l10n_error'        			=> esc_html__( 'Error', 'gn-publisher' ),
            	'l10n_last_updated' 			=> esc_html__( 'Last updated ', 'gn-publisher' ),
            	'l10n_see_response' 			=> esc_html__( 'See response for details.', 'gn-publisher' )
			);
							
			$data = apply_filters('gnpub_localize_filter',$data,'gnpub_localize_data');		
			
			wp_localize_script( 'gn-admin-instant-index-script', 'gnpub_index_localize_data', $data );

			wp_enqueue_script( 'gn-admin-instant-index-script' );
			
		}	
	}

	/**
	 * Save index form settings
	 * @since 1.5.19
	 * */
	public function gnpub_save_index_settings_data() {
	
		if ( ( isset( $_POST['gnpub_save_index_settings'] ) || isset( $_POST['gnpub_save_setup_wizard_settings'] ) ) && current_user_can( 'manage_options' ) ) {

			if ( ! isset( $_POST['gnpub_save_index_settings_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['gnpub_save_index_settings_nonce'], 'gnpub_save_index_settings_nonce' ) ) {
				return;
			}

			$gnpub_options 		=	get_option( 'gnpub_new_options' );
			$update 			=	0;

			if ( isset( $_POST['gnpub_instant_indexing'] ) ) {
				$gnpub_options['gnpub_instant_indexing'] 		=	array_map( 'intval', $_POST['gnpub_instant_indexing'] );
				$update 	=	1;
			}

			if ( isset( $_POST['gnpub_instant_index_action'] ) ) {
				$gnpub_options['gnpub_instant_index_action']	=	sanitize_text_field( wp_unslash( $_POST['gnpub_instant_index_action'] ) );	
				$update 	=	1;
			}

			if ( $update ==	1 ) {
				update_option( 'gnpub_new_options', $gnpub_options);
			}
			
			if ( isset( $_FILES['gnpub_index_json_file'] ) && ! empty( $_FILES['gnpub_index_json_file']['tmp_name'] ) ) {
				if ( isset( $_FILES['gnpub_index_json_file']['type'] ) &&  $_FILES['gnpub_index_json_file']['type'] == 'application/json' ) {
					$file_contents 	=	sanitize_textarea_field( file_get_contents( $_FILES['gnpub_index_json_file']['tmp_name'] ) );
					update_option( 'gnpub_google_index_api_settings', $file_contents );
				}

			}else{
				if ( isset( $_POST['gnpub_index_json_key'] ) ) {
					$api_settings 	=	sanitize_textarea_field( wp_unslash( $_POST['gnpub_index_json_key'] ) );
					update_option( 'gnpub_google_index_api_settings', $api_settings );	
				}
			}

		}

	}

	/**
	 * Handle static URL instant indexing
	 * @since 1.5.19
	 * */
	public function gnpub_ociaifs_giapi() {
	
		if ( ! isset( $_POST['gnpub_index_security_nonce'] ) && ! isset( $_POST['url'] ) && ! isset( $_POST['api_action'] ) ) {
			return; 
		}
		if ( ! wp_verify_nonce( $_POST['gnpub_index_security_nonce'], 'gnpub_index_ajax_check_nonce' ) ) {
			return;  
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;  
		}


		$url_input = array_values( array_filter( array_map( 'trim', explode( "\n", sanitize_textarea_field( wp_unslash( $_POST['url'] ) ) ) ) ) );
		$action    = sanitize_title( wp_unslash( $_POST['api_action'] ) );
		header( 'Content-type: application/json' );		
		$result = $this->send_to_indexing_api( $url_input, $action );
		wp_send_json( $result );
		exit();

	}

	/**
	 * Handle indexing api
	 * @since 1.5.19
	 * */
	public function send_to_indexing_api( $url_input, $action ) {
	    $url_input = (array) $url_input;
	    $data      = array();

	    // Validate action
	    if ( ! in_array( $action, [ 'update', 'delete', 'getstatus' ], true ) ) {
	        return [ 'error' => 'Invalid action. Use "update", "delete", or "getstatus".' ];
	    }

	    // Ensure autoloader is loaded
	    if ( ! class_exists( \Google\Client::class ) ) {
	        $autoload = GNPUB_PATH . 'vendor/autoload.php';
	        if ( file_exists( $autoload ) ) {
	            require_once $autoload;
	        } else {
	            return [ 'error' => 'Google API autoloader not found. Run "composer install".' ];
	        }
	    }

	    // Get service account key
	    $api_settings = get_option( 'gnpub_google_index_api_settings', '' );
	    if ( empty( $api_settings ) || ! is_string( $api_settings ) ) {
	        return [ 'error' => 'API settings not configured.' ];
	    }

	    $json_key = json_decode( $api_settings, true );
	    if ( json_last_error() !== JSON_ERROR_NONE || empty( $json_key ) ) {
	        return [ 'error' => 'Invalid service account JSON key.' ];
	    }

	    try {
	        // ✅ Use \Google\Client (modern class)
	        $client = new \Google\Client();
	        $client->setAuthConfig( $json_key );
	        $client->addScope( \Google\Service\Indexing::INDEXING );
	        $client->setAccessType( 'offline' );

	        // Enable batch mode
	        $client->setUseBatch( true );
	        $service = new \Google\Service\Indexing( $client );

	        // ✅ Use $service->createBatch() — recommended way
	        $batch = $service->createBatch();

	        foreach ( $url_input as $i => $url ) {
	            $url = esc_url_raw( $url );
	            if ( ! $url ) {
	                $data[ $i ] = [ 'error' => 'Invalid URL provided.' ];
	                continue;
	            }

	            if ( $action === 'getstatus' ) {
	                $request = $service->urlNotifications->getMetadata( [ 'url' => $url ] );
	            } else {
	                $body = new \Google\Service\Indexing\UrlNotification();
	                $body->setUrl( $url );
	                $body->setType( $action === 'update' ? 'URL_UPDATED' : 'URL_DELETED' );
	                $request = $service->urlNotifications->publish( $body );
	            }

	            $batch->add( $request, "url-{$i}" );
	        }

	        // Execute batch
	        $results = $batch->execute();
	        $res_count = count( $results );

	        foreach ( $results as $id => $response ) {
	            $local_id = substr( $id, 9 ); // "response-url-X" → "url-X"

	            if ( is_a( $response, \Google\Service\Exception::class ) ) {
	                $error = json_decode( $response->getMessage(), true );
	                $data[ $local_id ] = $error ?: [ 'error' => 'Google API error.' ];
	            } else {
	                $data[ $local_id ] = $response->toSimpleObject();
	            }
	        }

	        // Return single result if only one
	        if ( $res_count === 1 ) {
	            $data = array_values( $data )[0];
	        }

	    } catch ( Exception $e ) {
	        return [
	            'error' => 'Request failed: ' . $e->getMessage()
	        ];
	    } catch ( Error $e ) {
	        return [
	            'error' => 'Internal error: ' . $e->getMessage()
	        ];
	    }

	    $this->log_request( $action );

	    return $data;
	}

	/**
	 * Store request log in option
	 * @since 1.5.19
	 * */
	public function log_request( $type ) {
		$requests_log            = get_option(
			'gnpub_giapi_requests',
			array(
				'update'    => array(),
				'delete'    => array(),
				'getstatus' => array(),
			)
		);
		$requests_log[ $type ][] = time();
		if ( count( $requests_log[ $type ] ) > 600 ) {
			$requests_log[ $type ] = array_slice( $requests_log[ $type ], -600, 600, true );
		}
		update_option( 'gnpub_giapi_requests', $requests_log );
	}


	/**
	 * Auto indexing when instant indexing is enabled
	 * @since 1.5.19
	 * */
	public function add_auto_indexing() {
		
		$gnpub_options 		=	get_option( 'gnpub_new_options' );
		
		if ( isset( $gnpub_options['gnpub_instant_indexing'] ) && is_array( $gnpub_options['gnpub_instant_indexing'] ) ) {

			foreach ( $gnpub_options['gnpub_instant_indexing'] as $post_type => $enabled ) {
				if ( empty( $enabled ) ) {
					continue;
				}
				add_action( 'save_post_' . $post_type, array( $this, 'publish_post' ), 10, 2 );				
			}
		}

	}

	/**
	 * Auto indexing when instant indexing is enabled
	 * @since 1.5.19
	 * */
	public function publish_post( $post_id ) {

		$post = get_post( $post_id );
		
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$send_url = get_permalink( $post );
		
		if ( ! $send_url ) {
			return;
		}

		if ( $post->post_status === 'publish' ) {
			$this->send_to_indexing_api( $send_url, 'update' );			
		}

	}

	/**
	 * Delete indexing for post type
	 * @since 1.5.19
	 * */
	public function delete_post( $post_id ) {

		global $sd_data;				
		$post       = get_post( $post_id );
		if ( empty( $sd_data['instant_indexing'][ $post->post_type ] ) ) {
			return;
		}

		$send_url = get_permalink( $post );
		
		if ( ! $send_url ) {
			return;
		}

		$this->send_to_indexing_api( $send_url, 'delete' );

	}

}

new GNPUB_Instant_Index();