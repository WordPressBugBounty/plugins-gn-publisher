<?php
// namespace GNPUB\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controllers handles the onboarding process
 * @since 1.5.19
 */
class GNPUB_Setup_Wizard {

	protected $wizard_steps 	=	[];

	protected $active_tab 		=	'';

	/**
	 * Constructor
	 * @since 1.5.19
	 * */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu', array( $this, 'setup_wizard_menu' ) );
		add_action( 'admin_post_gnpub_save_setup_wizard', array( $this, 'save_step' ) );
		add_action( 'wp_ajax_gnpub_setup_wizard_checklist_ajax', array( $this, 'save_checklist_option' ) );	

		$this->wizard_steps();	

	}

	/**
	 * Enqueue admin scripts and styles
	 * @since 1.5.19
	 * */
	public function enqueue_scripts_and_styles( $hook_suffix ){
		
		if ( $hook_suffix == 'admin_page_gnpub-setup-wizard' ) {

			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'gn-admin-setup-wizard-script', GNPUB_URL . "/assets/js/gn-admin-setup-wizard{$min}.js", array('jquery'), GNPUB_VERSION, 'true' );		

			$data = array(     
				'ajax_url'                     			=>	admin_url( 'admin-ajax.php' ),             
				'gnpub_setup_wizard_security_nonce'   	=>	wp_create_nonce( 'gnpub_setup_wizard_nonce' )
			);
							
			$data = apply_filters('gnpub_localize_filter',$data,'gnpub_localize_data');		
			
			wp_localize_script( 'gn-admin-setup-wizard-script', 'gnpub_setup_wizard_localize_data', $data );

			wp_enqueue_script( 'gn-admin-setup-wizard-script' );

			wp_enqueue_style( 'gn-admin-setup-wizard-style', GNPUB_URL . "/assets/css/gn-admin-setup-wizard{$min}.css", array(), GNPUB_VERSION, );

		}

	}

	/**
	 * Wizard steps
	 * @since 1.5.19
	 * */
	public function wizard_steps() {
		
		$this->wizard_steps = array(
			'0'		=>	array(
				'id' 		=>	'key_features',
				'label' 	=>	'Key Features',
				'url' 		=>	admin_url('admin.php?page=gnpub-setup-wizard'),
			),
			'1'		=>	array(
				'id' 		=>	'site_map',
				'label' 	=>	'Site Map',
				'url' 		=>	admin_url('admin.php?page=gnpub-setup-wizard&wizard_step=site_map'),
			),
			'2'		=>	array(
				'id' 		=>	'indexing',
				'label' 	=>	'Indexing',
				'url' 		=>	admin_url('admin.php?page=gnpub-setup-wizard&wizard_step=indexing'),
			),
			'3'		=>	array(
				'id' 		=>	'general_status',
				'label' 	=>	'General Status',
				'url' 		=>	admin_url('admin.php?page=gnpub-setup-wizard&wizard_step=general_status'),
			),
			'4'		=>	array(
				'id' 		=>	'finish',
				'label' 	=>	'Finish',
				'url' 		=>	admin_url('admin.php?page=gnpub-setup-wizard&wizard_step=finish'),
			),
		);

	}

	/**
	 * Create setup wizard menu
	 * @since 1.5.19
	 * */
	public function setup_wizard_menu(){
		
		add_submenu_page(
			'',
			esc_html__( 'Setup Wizard', 'gn-publisher' ),
			esc_html__( 'Setup Wizard', 'gn-publisher' ),
			'manage_options',
			'gnpub-setup-wizard',
			array( $this, 'render_setup_wizard' )
		);

	}

	/**
	 * Render setup wizard page
	 * @since 1.5.19
	 * */
	public function render_setup_wizard(){

		// if ( ob_get_length() ) {
		// 	ob_end_clean();
		// }

		$this->header();
		$this->content();
		$this->footer();		

	}

	/**
	 * Render Header tabs of setup wizard
	 * @since 1.5.19
	 * */
	public function header() {
		
		$response 	=	$this->get_current_and_next_steps();

		?>
		<div id="gnpub-setup-wizard-wrapper">
			
			<div id="gnpub-setup-wizard-header">

				<div id="gnpub-setup-wizard-logo" class="gnpub-wizard-center">
					<a href="https://wordpress.org/plugins/gn-publisher/" target="_blank">
						<img src="<?php echo esc_url( GNPUB_URL . '/assets/images/logo.png' ); ?>" alt="GN Publisher Logo">
					</a>
				</div>

				<div id="gnpub-setup-wizard-navigation">
					<?php 

					$checklist_options 				=	get_option( 'gnpub_setup_wizard_checklist' );

					foreach ( $this->wizard_steps as $step_id => $step ) {

						$class 			=	'';
						if ( $response['current']['index'] ) {
							if ( $step_id  <= $response['current']['index'] ) {
								$class 	=	'gnpub-setup-wizard-step-done ';	
							}
						}

						$active_class 			=	'';
						if ( $step_id  == $response['current']['index'] ) {
							$class 		.=	'gnpub-setup-wizard-active-step ';	
						}

						if ( $step_id == 0 ){
							$class 		.=	'gnpub-setup-wizard-step-done gnpub-setup-wizard-origin';
						}

						$chk_response 		=	$this->gnpub_prepare_checklist_text( $step['id'] );
						$check_status 		=	$chk_response['text'];
						?>
							<a class="gnpub-setup-wizard-steps <?php echo esc_attr( $class ); ?>" title="<?php echo esc_html( $step['label'] ) . esc_attr( $check_status ); ?>" data-label="<?php echo esc_attr( $step['label'] ); ?>"><span></span></a>
						<?php
						// }
					}
					?>
				</div> <!-- gnpub-setup-wizard-navigation div end -->

			</div> <!-- gnpub-setup-wizard-header div end -->

		<?php

	}

	/**
	 * Render content of setup wizard
	 * @since 1.5.19
	 * */
	public function content() {

		$response 			=	$this->get_current_and_next_steps();

		$next_url 			=	'';
		$next_step_id 		=	'';
		$previous_url		=	'';
		$previous_step_id 	=	'';
		$btn_text 			=	'Save and Continue';
		if ( ! empty( $response['next'] ) ) {
			$next_url 		=	$response['next']['url'];	
			$next_step_id 	=	$response['next']['id'];	
		}

		if ( ! empty( $response['previous'] ) ) {
			$previous_url 		=	$response['previous']['url'];	
			$previous_step_id 	=	$response['previous']['id'];	
		}

		$current_step_id 	=	$response['current']['id'];
		$current_step_index =	$response['current']['index'];
		$step_count 		=	count( $this->wizard_steps );
		if ( $current_step_index == $step_count - 1 ){
			$btn_text		=	'Return to Dashboard';
		}
		
		?>
		<div class="gnpub-setup-wizard-content">
			<form enctype="multipart/form-data" class="gnpub-setup-wizard-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="gnpub_save_setup_wizard">
				<input type="hidden" name="next_step" value="<?php echo esc_attr( $next_step_id ); ?>">
				<input type="hidden" name="current_step" value="<?php echo esc_attr( $current_step_id ); ?>">
				<?php wp_nonce_field( 'gnpub-setup-wizard-nonce', 'security' ); ?>

				<?php $this->render_tab_body(); ?>

				<footer class="gnpub-setup-wizard-skip-next">
					<?php 
					if ( ! empty( $next_url ) ) {
					?>
						<a class="gnpub-setup-wizard-skip-btn" href="<?php echo esc_url( $previous_url ); ?>"><?php echo esc_html__( 'Back', 'gn-publisher' ); ?></a>
					<?php	
					}
					?>
					<button type="submit" name="gnpub_save_setup_wizard_settings" class="gnpub-setup-wizard-continue-btn"><?php echo esc_html( $btn_text ); ?></button>
				</footer>
			</form>

		</div> <!-- gnpub-setup-wizard-content div end -->
		<?php

	}

	/**
	 * Render tab body
	 * @since 1.5.19
	 * */
	public function render_tab_body() {

		switch( $this->active_tab ) {

			case 'site_map':

				require_once dirname( __FILE__ ) . '/../../templates/admin/setup-wizard/template-wizard-sitemap.php';

			break;

			case 'indexing':

				require_once dirname( __FILE__ ) . '/../../templates/admin/setup-wizard/template-wizard-indexing.php';

			break;

			case 'general_status':

				require_once dirname( __FILE__ ) . '/../../templates/admin/setup-wizard/template-wizard-feed.php';

			break;

			case 'finish':

				require_once dirname( __FILE__ ) . '/../../templates/admin/setup-wizard/template-wizard-finish.php';

			break;

			default:

				require_once dirname( __FILE__ ) . '/../../templates/admin/setup-wizard/template-key-features.php';

			break;

		}

	}

	/**
	 * Render footer of setup wizard
	 * @since 1.5.19
	 * */
	public function footer() {

		$dashboard_url 	=	admin_url('admin.php?page=gn-publisher-settings');

		$total_perc 	=	gnpub_setup_wizard_progress_perc();
		$class 			=	'';
		if ( $total_perc == 0 ){
			$class 		=	'gnpub-d-none';	
		}
		?>
			<div class="gnpub-setup-wizard-progress-container">
    			<div class="gnpub-setup-wizard-progress-bar" style="width: <?php echo esc_attr($total_perc) ?>%;">
    				<div class="gnpub-setup-wizard-progress-bar-text <?php echo esc_attr( $class ); ?>"><?php echo esc_html($total_perc) . esc_html__( '% Completed', 'gn-publisher' ); ?></div>
    			</div>
			</div>

			<div id="gnpub-setup-wizard-footer">
				<a href="<?php echo esc_url( $dashboard_url ); ?>"><?php echo esc_html__( 'Return to dashboard', 'gn-publisher' ); ?></a>	
			</div>
		</div> <!-- gnpub-setup-wizard-wrapper div end -->
		<?php
	}

	/**
	 * Get next step url
	 * @since 1.5.19
	 * */
	public function get_current_and_next_steps(){
		
		$response['next'] 		=	array();
		$response['current'] 	=	$this->wizard_steps[0];
		$response['current']['index'] 	=	0;

		$tab 					=	'';
		if ( isset( $_GET['wizard_step'] ) ) {
			$tab 				=	sanitize_text_field( wp_unslash( $_GET['wizard_step'] ) );	
			$this->active_tab 	=	$tab;	
		}

		$next 					=	'';
		if ( ! empty( $tab ) ) {
			
			foreach ( $this->wizard_steps as $step_key => $step ) {
				if ( $step['id'] == $tab && isset( $this->wizard_steps[$step_key + 1] ) ) {
					$response['next'] 		=	$this->wizard_steps[$step_key + 1];
				}
				if ( $step['id'] == $tab ) {
					$response['current'] 	=	$this->wizard_steps[$step_key];		
					$response['current']['index'] 	=	$step_key;
				}
				if ( $step['id'] == $tab && isset( $this->wizard_steps[$step_key - 1] ) ) {
					$response['previous'] 		=	$this->wizard_steps[$step_key - 1];
				}
			}	

		}

		return $response;

	}

	public function save_step() {

		$redirect_url 	=	admin_url('admin.php?page=gnpub-setup-wizard');
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_safe_redirect( $redirect_url );
		}

		if ( ! isset( $_POST['security'] ) ) {
			wp_safe_redirect( $redirect_url );
		}

		if ( ! wp_verify_nonce( $_POST['security'], 'gnpub-setup-wizard-nonce' ) ) {
			wp_safe_redirect( $redirect_url );
		}
		
		if ( isset( $_POST['next_step'] ) ) {

			$next_step 	=	sanitize_text_field( wp_unslash( $_POST['next_step'] ) );
			if ( isset( $_POST['current_step'] ) && $_POST['current_step'] == 'key_features' ) {
				$redirect_url 	=	admin_url( 'admin.php?page=gnpub-setup-wizard&wizard_step=site_map' );	
			}
			if ( ! empty( $next_step ) ) {
				$redirect_url 	=	admin_url( 'admin.php?page=gnpub-setup-wizard&wizard_step='.$next_step );
			}

		}

		if ( isset( $_POST['current_step'] ) ) {

			if ( isset( $_POST['current_step'] ) && $_POST['current_step'] == 'finish' ) {
				$redirect_url 	=	admin_url('admin.php?page=gn-publisher-settings');
			}

		}

		$gnpub_options 	=	get_option( 'gnpub_new_options' );

		// Save key features wizard options data
		if ( isset( $_POST['tab'] ) ) {

			$tab 		=	sanitize_text_field( wp_unslash( $_POST['tab'] ) );	


			// Save checklist options
			$wizard_checklist 		=	get_option( 'gnpub_setup_wizard_checklist', gnpub_default_checklist_options_data() );
			$post_checklist 		=	'';
			if ( isset( $_POST['gnpub_setup_wizard_checklist'] ) ) {

				$post_checklist 	=	wp_unslash( $_POST['gnpub_setup_wizard_checklist'] );

			}

			if ( $tab == 'key_features' ) {

				$option_update 		=	false;
				if ( isset( $_POST['gnpub_enable_news_article_schema'] ) ) {
					$gnpub_options['gnpub_enable_news_article_schema'] = true;
					$option_update 	=	true;
				}else{
					$gnpub_options['gnpub_enable_news_article_schema']= false;
				}

				if ( isset( $_POST['gnpub_show_info_featured_img'] ) ) {
					$gnpub_options['gnpub_show_info_featured_img'] = true;
					$option_update 	=	true;
				}else{
					$gnpub_options['gnpub_show_info_featured_img']= false;
				}

				if ( $option_update == true ){			
				  update_option( 'gnpub_new_options', $gnpub_options);	
				}

				if ( isset( $post_checklist['gnpub_enable_news_article_schema'] ) ) {
					$wizard_checklist['gnpub_enable_news_article_schema'] 	=	true;	
				}else{
					$wizard_checklist['gnpub_enable_news_article_schema'] 	=	false;	
				}
				if ( isset( $post_checklist['gnpub_show_info_featured_img'] ) ) {
					$wizard_checklist['gnpub_show_info_featured_img'] 		=	true;	
				}else{
					$wizard_checklist['gnpub_show_info_featured_img'] 		=	false;	
				}
				update_option( 'gnpub_setup_wizard_checklist', $wizard_checklist );

			}else if( $tab == 'site_map' ) {
		
				$option_update 	=	false;
				if ( isset( $_POST['gnpub_enable_gnsitemap'] ) ) {
					$gnpub_options['gnpub_enable_gnsitemap'] 	=	true;
				}else{
					$gnpub_options['gnpub_enable_gnsitemap'] 	=	false;
				}
				update_option( 'gnpub_new_options', $gnpub_options);
				
				if ( isset( $_POST['gnpub_enable_gnsitemap'] ) ){			

					if ( isset( $_POST['gnpub_news_sitmap'] ) && ! empty( $_POST['gnpub_news_sitmap'] ) ) {
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$data 					=	serialize( wp_unslash( $_POST['gnpub_news_sitmap'] ) );

						if ( ! empty( $data ) ) {
							update_option( 'gnpub_news_sitmap', $data );
						}
					}else{
						update_option( 'gnpub_news_sitmap', '' );
					}	
				}

				if ( isset( $post_checklist['gnpub_enable_gnsitemap'] ) ) {
					$wizard_checklist['gnpub_enable_gnsitemap'] 			=	true;	
				}else{
					$wizard_checklist['gnpub_enable_gnsitemap'] 			=	false;
				}
				update_option( 'gnpub_setup_wizard_checklist', $wizard_checklist );

			} 

		}

		wp_safe_redirect( $redirect_url );

	}

	/**
	 * Prepare checklist text and calculate percentage
	 * @param	$tabid 	string
	 * @return 	$data 	array
	 * @since 	1.5.19
	 * */
	public function gnpub_prepare_checklist_text( $tab_id ){
		
		$perc 						=	gnpub_setup_wizard_progress_perc();
		$checklist_options 			=	get_option( 'gnpub_setup_wizard_checklist', gnpub_default_checklist_options_data() );
		$check_status 				=	'';
		$label 						=	'';
		$completed_checks 			=	0;

		switch( $tab_id ){

			case 'key_features':

				$total_checks 		=	2;

				if ( ! empty( $checklist_options['gnpub_enable_news_article_schema'] ) ){
					$completed_checks++;	
				}
				if ( ! empty( $checklist_options['gnpub_show_info_featured_img'] ) ){
					$completed_checks++;	
				}

				$check_status 		=	' ('.$completed_checks.'/'.$total_checks.')';

			break;

			case 'site_map':

				$total_checks 		=	1;

				if ( ! empty( $checklist_options['gnpub_enable_gnsitemap'] ) ){
					$completed_checks++;	
				}
				
				$check_status 		=	' ('.$completed_checks.'/'.$total_checks.')';

			break;

			case 'general_status':

				$total_checks 		=	3;

				if ( ! empty( $checklist_options['gnpub_gn_status_robot'] ) ){
					$completed_checks++;	
				}
				if ( ! empty( $checklist_options['gnpub_gn_status_nas'] ) ){
					$completed_checks++;	
				}
				if ( ! empty( $checklist_options['gnpub_gn_status_byline'] ) ){
					$completed_checks++;	
				}
				
				$check_status 		=	' ('.$completed_checks.'/'.$total_checks.')';

			break;

		}


		$data['text'] 	=	$check_status;
		$data['perc']	=	$perc;

		return $data;

	}
	
	/**
	 * Ajax callback function to save checklist data
	 * @since 1.5.19
	 * */	
	public function save_checklist_option(){
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error('You are not authorized to perform this task');
		}
		if ( ! isset( $_POST['security'] ) ) {
			wp_send_json_error('You are not authorized to perform this task');
		}
		if ( ! wp_verify_nonce( $_POST['security'], 'gnpub_setup_wizard_nonce' ) ) {
			wp_send_json_error('You are not authorized to perform this task');
		}
		
		if ( isset( $_POST['name'] ) && isset( $_POST['value'] ) ) {

			$wizard_checklist 				=	get_option( 'gnpub_setup_wizard_checklist', gnpub_default_checklist_options_data() );
			$optname 						=	sanitize_text_field( wp_unslash( $_POST['name'] ) );
			$optvalue 						=	sanitize_text_field( wp_unslash( $_POST['value'] ) );
			
			if ( $optvalue == 'yes' ) {
				$wizard_checklist[$optname]	=	true;	
			}else{
				$wizard_checklist[$optname]	=	false;
			}

			$tab 							=	'';
			if ( ! empty( $_POST['tab'] ) ) {
				$tab 						=	sanitize_text_field( wp_unslash( $_POST['tab'] ) );
			}

			update_option( 'gnpub_setup_wizard_checklist', $wizard_checklist );

			$response 						=	$this->gnpub_prepare_checklist_text( $tab );

			wp_send_json_success( $response );

		}

		wp_die();
	}
}

new GNPUB_Setup_Wizard();