<?php 
/**
 * Newsletter class
 *
 * @author   Magazine3
 * @category Admin
 * @path     controllers/admin/newsletter
 * @Version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class GNPUB_Newsletter {
        
	public function __construct () {

                add_filter( 'gnpub_localize_filter',array($this,'gnpub_add_localize_footer_data'),10,2);
                add_action('wp_ajax_gnpub_subscribe_to_news_letter', array($this, 'gnpub_subscribe_to_news_letter'));

        }
        
        public function gnpub_subscribe_to_news_letter(){

                if ( ! current_user_can( 'manage_options' ) ) {
                    return; 
                }
                if ( ! isset( $_POST['gnpub_security_nonce'] ) ){
                    return; 
                }
                //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                if ( ! wp_verify_nonce( $_POST['gnpub_security_nonce'], 'gnpub_ajax_check_nonce' ) ){
                   return;  
                }
                                
	            $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
                $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
                $website = isset( $_POST['website'] ) ? sanitize_url( wp_unslash( $_POST['website'] ) ) : '';
                
                if ( $email ) {
                        
                    $api_url = 'http://magazine3.company/wp-json/api/central/email/subscribe';

		    $api_params = array(
		        'name'    => $name,
		        'email'   => $email,
		        'website' => $website,
		        'type'    => 'gnpub'
                    );
                    
		    $response = wp_remote_post( $api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
            $response = wp_remote_retrieve_body( $response );                    
		    $response = json_decode( $response, true );
		    echo wp_json_encode( array( 'response' => $response['response'] ) );

            }else{

                echo wp_json_encode( array( 'response' => esc_html__( 'Email id required', 'gn-publisher' ) ) );

            }                        

            wp_die();
                
        }
	        
        public function gnpub_add_localize_footer_data( $object, $object_name ) {
            
        $dismissed = explode ( ',', get_user_meta( wp_get_current_user()->ID, 'dismissed_wp_pointers', true ) );
        $do_tour   = ! in_array ( 'gnpub_subscribe_pointer', $dismissed );
        
        if ( $do_tour ) {

                wp_enqueue_style ('wp-pointer');
                wp_enqueue_script ('wp-pointer');						

	    }
                        
        if ( $object_name == 'gnpub_localize_data' ) {
                        
                global $current_user;
		        $tour     = array ();
                //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: Nonce verification is not required here.
                $tab      = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
                
                if ( ! array_key_exists( $tab, $tour ) ) {
			                                           			            	
                        $object['do_tour']            = $do_tour;        
                        $object['get_home_url']       = get_home_url();                
                        $object['current_user_email'] = $current_user->user_email;                
                        $object['current_user_name']  = $current_user->display_name;        
			            $object['displayID']          = '#menu-settings';                        
                        $object['button1']            = esc_html__( 'No Thanks', 'gn-publisher' );
                        $object['button2']            = false;
                        $object['function_name']      = '';                        
		}
		                                                                                                                                                    
        }
        return $object;
         
    }
       
}

$gnpub_newsletter = new GNPUB_Newsletter();