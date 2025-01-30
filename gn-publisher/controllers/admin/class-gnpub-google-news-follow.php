<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controllers handles google news follow feature
 * 
 * @since 1.5.19
 */
class GNPUB_News_Follow {

	public function __construct(){
		
		// add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_script' ) );	
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_script' ) );	
		add_action( 'admin_enqueue_scripts', array( $this, 'load_frontend_script' ) );	
		add_action( 'gnpub_render_google_news_follow', array( $this, 'google_news_follow' ) );
		add_shortcode( 'gnpub_google_news_follow', array( $this, 'render_shortocde' ) );
		add_action( 'admin_post_gnpub_save_gnfollow', array( $this, 'save_gnfollow' ) );
		add_action( 'wp_footer', array( $this, 'sticky_follow' ) );

	}

	/**
	 * Add modal window only on gn publisher settings page
	 * @since 	1.5.19
	 * */
	public function admin_init(){
		
		if ( ! empty( $_GET['page'] ) ) {
			
			$current_page 	=	sanitize_text_field( wp_unslash( $_GET['page'] ) );
			if ( $current_page == 'gn-publisher-settings' ) {
				$this->add_modal();
			}
		}

	}

	/**
	 * Enqueue google news follow related script
	 * @since 1.5.19
	 * */
	public function load_admin_script( $hook_suffix ) {

		if ( $hook_suffix == "settings_page_gn-publisher-settings" ) {

			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// wp_register_script( 'gn-admin-gnfpllow-script', GNPUB_URL . "/assets/js/gn-admin-gnfollow{$min}.js", array( 'jquery', 'wp-color-picker' ), GNPUB_VERSION, 'true' );
			
			// $current_screen = get_current_screen(); 		

			// $data = array(     
			// 	'ajax_url'                     		=> admin_url( 'admin-ajax.php' ),       
			// 	'uploader_title'            		=> esc_html__('Application Icon', 'gn-publisher'),
            // 	'uploader_button'           		=> esc_html__('Select Icon', 'gn-publisher'),      
			// 	'gnpub_gnfollow_security_nonce'  	=> wp_create_nonce('gnpub_gnfollow_ajax_check_nonce'),
			// );
							
			// $data = apply_filters('gnpub_localize_filter',$data,'gnpub_gnfollow_localize_data');		
			
			// wp_localize_script( 'gn-admin-gnfpllow-script', 'gnpub_gnfollow_localize_data', $data );

			// wp_enqueue_script( 'gn-admin-gnfpllow-script' );

			wp_enqueue_style( 'gn-admin-gnfollow-style', GNPUB_URL . "/assets/css/gn-admin-gnfollow{$min}.css", array(), GNPUB_VERSION, );
			// wp_enqueue_style( 'wp-color-picker' );
        	// wp_enqueue_script( 'wp-color-picker' );	
        	// wp_enqueue_media();
			
		}	

	}

	/**
	 * Enqueue google news follow related frontend script
	 * @since 1.5.19
	 * */
	public function load_frontend_script() {

		$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'gn-frontend-gnfollow-style', GNPUB_URL . "/assets/css/gn-frontend-gnfollow{$min}.css", array(), GNPUB_VERSION, );

	}

	/**
	 * Render Google news follow html
	 * @since 	1.5.19
	 * */
	public function google_news_follow(){

		$gnpub_options 		=	get_option( 'gnpub_new_options' );
		$news_follow 		=	isset( $gnpub_options['gnpub_enable_google_news_follow'] ) ? $gnpub_options['gnpub_enable_google_news_follow'] : false;
		$follow_us 			=	isset( $gnpub_options['gnpub_enable_google_news_follow_text'] ) ? $gnpub_options['gnpub_enable_google_news_follow_text'] : 'Follow us on';
		$follow_link 		=	isset( $gnpub_options['gnpub_enable_google_news_follow_link'] ) ? $gnpub_options['gnpub_enable_google_news_follow_link'] : '';
		$short_code 		=	'[gnpub_google_news_follow]';
		
		$opt_class 			=	'gnpub-d-none';
		if ( $news_follow ) {
			$opt_class 		=	'';
		}
	?>
		<tr>
	        <th><label for="gnpub_enable_google_news_follow" class="gnpub-hover-pointer"><?php echo esc_html__( 'Google News Follow Button', 'gn-publisher' ); ?></label></th>
	        <td>
	          <input type="checkbox" name="gnpub_enable_google_news_follow" id="gnpub_enable_google_news_follow" <?php checked( $news_follow, true ); ?> value="1" />
	          <label for="gnpub_enable_google_news_follow"><?php echo esc_html__( 'Add Google new follow button for your site', 'gn-publisher.' ); ?> &nbsp; <span class="gnpub-span-lrn-more"> <a target="_blank" style="text-decoration:none;" href="https://gnpublisher.com/docs/"><?php echo esc_html__( 'Learn More', 'gn-publisher' ); ?></a></span></label>
	          
	        </td>
	    </tr>
	    <tr class="gnpub-google-news-button-opts <?php echo esc_attr( $opt_class ); ?>">
	    	<th class="gnpub-child-set-options"><label><?php echo esc_html__( 'Google News Follow Text', 'gn-publisher' ); ?></label></th>
	    	<td>
	    		<input type="text" name="gnpub_enable_google_news_follow_text" id="gnpub_enable_google_news_follow_text" value="<?php echo esc_attr( $follow_us ); ?>" placeholder="Follow us on" size="60">
	    	</td>
	    </tr>
	    <tr class="gnpub-google-news-button-opts <?php echo esc_attr( $opt_class ); ?>">
	    	<th class="gnpub-child-set-options"><label><?php echo esc_html__( 'Google News Follow Link', 'gn-publisher' ); ?></label></th>
	    	<td>
	    		<input type="text" name="gnpub_enable_google_news_follow_link" id="gnpub_enable_google_news_follow_link" value="<?php echo esc_attr( $follow_link ); ?>" placeholder="Enter google news publisher link" size="60">
	    	</td>
	    </tr>
		<tr class="gnpub-google-news-button-opts <?php echo esc_attr( $opt_class ); ?>">
	        <th class="gnpub-child-set-options"><label><?php echo esc_html__( 'Shortcode', 'gn-publisher' ); ?></label></th>
	        <td>
	          <input type="text" class="gn-input" value="<?php echo esc_attr( $short_code ); ?>" id="gnpub-google-news-follow-code" size="60" readonly>
	          <div class="gn-tooltip">
	            <button id="gnpub-gnfollow-copy-btn" type="button" class="gn-btn" onclick="gn_copy('gnpub-google-news-follow-code')" onmouseout="gn_out('gnpub-google-news-follow-code')">
	              <span class="gn-tooltiptext" id="gnpub-google-news-follow-code-toolti"><?php echo esc_html__( 'Copy Shortcode', 'gn-publisher' ); ?></span>
	              <?php echo esc_html__( 'Copy', 'gn-publisher' ); ?>
	            </button>
	          </div>
	        </td>
	   </tr>
	   <tr class="gnpub-google-news-button-opts <?php echo esc_attr( $opt_class ); ?>">
	        <th class="gnpub-child-set-options"><label><?php echo esc_html__( 'Preview', 'gn-publisher' ); ?></label></th>
	        <td>
	          <?php echo do_shortcode( $short_code ); ?>
	        </td>
	   </tr>
	<?php

	}

	/**
	 * Add customize settings modal window
	 * @since 	1.5.19
	 * */
	public function add_modal() {

		$opt_value 				=	self::get_option_values();
		$wrapper_style 			=	"background-color: ".$opt_value['bg_color'];
		$txt_wrapper_style 		=	"color: ".$opt_value['txt_color'];

	?>
		<div  id="gnpub-modal-google-news-follow" class="gnpub-google-news-follow-modal" style="display: none;">
		  	<div id="gnpub-google-news-follow-modal-content">
		  		<form class="form-table" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
		  			<div>
						<div id="gnpub-google-news-follow-close" class="gnpub-google_news-follow-close ">&times;</div>
						<div id="gnpub-google-news-follow-header">
							<h3><?php echo esc_html__( 'Customize Settings' ); ?></h3>	
						</div>
						<div id="gnpub-gnfollow-design-wrapper">
							<h3><?php echo esc_html__( 'Design Appearance', 'gn-publisher' ); ?></h3>
							<table class="gnpub-gnfollow-field-table">
	                            <tbody>

	                                <tr class="gnpub-gnfollow-tr-row">
	                                    <th class="gnpub-gnfollow-td-headings"><?php echo esc_html__( 'Background Color', 'gn-publisher' ); ?></th>
	                                    <td class="gnpub-gnfollow-td-attributes gnpub-gnfollow-bg-color">
	                                        <input type="text" name="gnpub_gnfollow_background_color" id="gnpub-gnfollow-bg-color" class="gnpub-color-picker" value="<?php echo esc_attr( $opt_value['bg_color'] ); ?>" data-default-color="#000">
	                                    </td>
	                                </tr>

	                                <tr class="gnpub-gnfollow-tr-row">
	                                    <th class="gnpub-gnfollow-td-headings"><?php echo esc_html__( 'Text Color', 'gn-publisher' ); ?></th>
	                                    <td class="gnpub-gnfollow-td-attributes gnpub-gnfollow-text-color">
	                                        <input type="text" name="gnpub_gnfollow_text_color" id="gnpub-gnfollow-text-color" class="gnpub-color-picker" value="<?php echo esc_attr( $opt_value['txt_color'] ); ?>" data-default-color="#fff">
	                                    </td>
	                                </tr>

	                                <tr class="gnpub-gnfollow-tr-row">
	                                    <th class="gnpub-gnfollow-td-headings"><?php echo esc_html__( 'Background Image', 'gn-publisher' ); ?></th>
	                                    <td class="gnpub-gnfollow-td-attributes gnpub-gnfollow-icon-image">
	                                        <input type="text" name="gnpub_gnfollow_icon_image" id="gnpub-gnfollow-icon-image" value="<?php echo esc_attr( $opt_value['icon_image'] ); ?>">
	                                        <button type="button" class="button" id="gnpub-icon-upload" data-editor="content">
												<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'gn-publisher'); ?> 
											</button>
	                                    </td>
	                                </tr>

	                                <tr class="gnpub-gnfollow-tr-row">
	                                    <th class="gnpub-gnfollow-td-headings"><?php echo esc_html__( 'Redirect Link', 'gn-publisher' ); ?></th>
	                                    <td class="gnpub-gnfollow-td-attributes gnpub-gnfollow-redirect-link">
	                                        <input type="text" name="gnpub_gnfollow_redirect_link" id="gnpub-gnfollow-redirect-link" value="<?php echo esc_attr( $opt_value['link'] ); ?>">
	                                    </td>
	                                </tr>

	                                <tr class="gnpub-gnfollow-tr-row">
	                                    <th class="gnpub-gnfollow-td-headings"><?php echo esc_html__( 'Text One', 'gn-publisher' ); ?></th>
	                                    <td class="gnpub-gnfollow-td-attributes gnpub-gnfollow-text-one">
	                                        <input type="text" name="gnpub_gnfollow_text_one" id="gnpub-gnfollow-text-one" value="<?php echo esc_attr( $opt_value['text1'] ); ?>">
	                                    </td>
	                                </tr>

	                                <tr class="gnpub-gnfollow-tr-row">
	                                    <th class="gnpub-gnfollow-td-headings"><?php echo esc_html__( 'Text Two', 'gn-publisher' ); ?></th>
	                                    <td class="gnpub-gnfollow-td-attributes gnpub-gnfollow-text-two">
	                                        <input type="text" name="gnpub_gnfollow_text_two" id="gnpub-gnfollow-text-two" value="<?php echo esc_attr( $opt_value['text2'] ); ?>">
	                                    </td>
	                                </tr>

	                            </tbody>
                        	</table>
						</div> <!-- gnpub-gnfollow-field-wrapper div end -->

						<div id="gnpub-gnfollow-preview-wrapper">
							<h3><?php echo esc_html__( 'Preview', 'gn-publisher' ); ?></h3>
							<div id="gnpub-gnfollow-shortcode-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>">
								<a href="<?php echo esc_attr( $opt_value['link'] ); ?>" target="__blank">
									<div id="gnpub-gnfollow-shortcode-img-wrapper">
										<img src="<?php echo esc_attr( $opt_value['icon_image'] ); ?>" />
									</div>
									<div id="gnpub-gnfollow-shortcode-text-wrapper" style="<?php echo esc_attr( $txt_wrapper_style ); ?>">
										<div>
											<p id="gnpub-gnfollow-shortcode-follow-text"><?php echo esc_html( $opt_value['text1'] ); ?></p>
											<p id="gnpub-gnfollow-shortcode-news-text-wrapper">
												<span id="gnpub-gnfollow-shortcode-follow-google-txt"><?php echo esc_html( $opt_value['text2'] ); ?></span>
											</p>
										</div>
									</div>
								</a>
							</div>

						</div><!-- gnpub-gnfollow-preview-wrapper div end -->

						<div id="gnpub-gnfollow-btn-warpper">
							<?php wp_nonce_field( 'gnpub-gnfollow-settings', 'gnpub_gnfollow_nonce' ); ?>
							<input type="submit" name="save_gnpub_gnfollow_settings" id="submit" class="button button-primary" value="<?php echo esc_html__( 'Save Changes', 'gn-publisher' ); ?>" />
							<input type="hidden" name="action" value="gnpub_save_gnfollow">
						</div>
					</div>
				</form>
			</div> <!-- gnpub-google-news-follow-modal-content --> 
		</div> <!-- gnpub_modal_google_news_follow div end -->
	<?php

	}

	/**
	 * Define defaults option data
	 * @since 	1.5.19
	 * */
	public static function gnpub_gnfollow_defaults() {
		
		$defaults 	=	array(
			'gnpub_gnfollow_default_image'			=>	GNPUB_URL . '/assets/images/google-news-icon.jpg',
			'gnpub_gnfollow_icon_image'				=>	'',
			'gnpub_gnfollow_background_color'		=>	'#000',
			'gnpub_gnfollow_text_color' 			=>	'#fff',
			'gnpub_gnfollow_font_size' 				=>	'14px',
			'gnpub_gnfollow_redirect_link' 			=>	'#',
			'gnpub_gnfollow_text_one' 				=>	'Follow us on',
			'gnpub_gnfollow_text_two' 				=>	'Google News',
		);

		return $defaults;
	}

	public static function get_option_values(){
		
		$opt_value 					=	array();

		$shortcode_options 			=	get_option( 'gnpub_shortcode_options', self::gnpub_gnfollow_defaults() );
		$opt_value['icon_image'] 	=	! empty( $shortcode_options['gnpub_gnfollow_icon_image'] ) ? $shortcode_options['gnpub_gnfollow_icon_image'] : $shortcode_options['gnpub_gnfollow_default_image'];
		$opt_value['bg_color'] 		=	! empty( $shortcode_options['gnpub_gnfollow_background_color'] ) ? $shortcode_options['gnpub_gnfollow_background_color'] : '#000';
		$opt_value['txt_color'] 	=	! empty( $shortcode_options['gnpub_gnfollow_text_color'] ) ? $shortcode_options['gnpub_gnfollow_text_color'] : '#fff';
		$opt_value['link'] 			=	! empty( $shortcode_options['gnpub_gnfollow_redirect_link'] ) ? $shortcode_options['gnpub_gnfollow_redirect_link'] : '#';
		$opt_value['text1'] 		=	! empty( $shortcode_options['gnpub_gnfollow_text_one'] ) ? $shortcode_options['gnpub_gnfollow_text_one'] : 'Follow us on';
		$opt_value['text2'] 		=	! empty( $shortcode_options['gnpub_gnfollow_text_two'] ) ? $shortcode_options['gnpub_gnfollow_text_two'] : 'Google News';

		return $opt_value;

	}

	/**
	 * Render shortocde
	 * @since 	1.5.19
	 * */
	public function render_shortocde( $attributes, $content = null ) {
			
		$gnpub_options 		=	get_option( 'gnpub_new_options' );
		$news_follow 		=	isset( $gnpub_options['gnpub_enable_google_news_follow'] ) ? $gnpub_options['gnpub_enable_google_news_follow'] : false;
		$follow_us 			=	isset( $gnpub_options['gnpub_enable_google_news_follow_text'] ) ? $gnpub_options['gnpub_enable_google_news_follow_text'] : 'Follow us on';
		$follow_link 		=	isset( $gnpub_options['gnpub_enable_google_news_follow_link'] ) ? $gnpub_options['gnpub_enable_google_news_follow_link'] : '#';
		
		$icon 				=	GNPUB_URL . '/assets/images/google-news-icon.svg';
		$escape_html 		=	'';

		$escape_html 		.=	'<div id="gnpub-gnfollow-shortcode-wrapper">';
		$escape_html 		.=	'<a href="'.esc_attr( $follow_link ).'" target="__blank">';
		$escape_html 		.=	'<div>';
		$escape_html 		.=	'<span id="gnpub-gnfollow-shortcode-follow-text">' . esc_html( $follow_us ) . '</span>';
		$escape_html 		.=	'<img src="' . esc_attr( $icon ) . '" />';
		$escape_html 		.=	'</div>'; // gnpub-gnfollow-shortcode-img-wrapper div end
		$escape_html 		.=	'</a>';
		$escape_html 		.=	'</div>'; // gnpub-gnfollow-shortcode-wrapper div end
		

		return $escape_html;

	}

	/**
	 * Shortcode html data
	 * @since 	1.5.19
	 * */
	public static function shortcode_html(){

		$opt_value 			=	self::get_option_values();
		$wrapper_style 		=	"background-color: ".$opt_value['bg_color'];
		$txt_wrapper_style 	=	"color: ".$opt_value['txt_color'];

		$escape_html 		=	'';
		$escape_html 		.=	'<div id="gnpub-gnfollow-shortcode-wrapper" style="'.esc_attr( $wrapper_style ).'">';
		$escape_html 		.=	'<a href="'.esc_attr( $opt_value['link'] ).'" target="__blank">';
		$escape_html 		.=	'<div id="gnpub-gnfollow-shortcode-img-wrapper">';
		$escape_html 		.=	'<img src="' . esc_attr( $opt_value['icon_image'] ) . '" />';
		$escape_html 		.=	'</div>'; // gnpub-gnfollow-shortcode-img-wrapper div end
		$escape_html 		.=	'<div id="gnpub-gnfollow-shortcode-text-wrapper" style="'.esc_attr( $txt_wrapper_style ).'">'; 
		$escape_html 		.=	'<div>'; 
		$escape_html 		.=	'<p id="gnpub-gnfollow-shortcode-follow-text">'.esc_html( $opt_value['text1'] ).'</p>'; 
		$escape_html 		.=	'<p id="gnpub-gnfollow-shortcode-news-text-wrapper">';
		$escape_html 		.=	'<span id="gnpub-gnfollow-shortcode-follow-google-txt">'.esc_html( $opt_value['text2'] ).'</span>';
		// $escape_html 		.=	'<span id="gnpub-gnfollow-shortcode-follow-news-txt"> '.esc_html__( 'News', 'gn-publisher' ).'</span>';
		$escape_html 		.=	'</p>'; 
		$escape_html 		.=	'</div>';
		$escape_html 		.=	'</div>'; // gnpub-gnfollow-shortcode-text-wrapper div end
		$escape_html 		.=	'</a>';
		$escape_html 		.=	'</div>'; // gnpub-gnfollow-shortcode-wrapper div end

		return $escape_html;

	}

	public static function sticky_follow(){
		
		$gnpub_options 	=	get_option( 'gnpub_new_options' );
		$sticky_pos 	=	isset( $gnpub_options['gnpub_gnfollow_sticky'] ) ? esc_attr( $gnpub_options['gnpub_gnfollow_sticky'] ) : '';	

		if ( ! empty( $sticky_pos ) ) {

			$sticky_class 	= '';
			if ( $sticky_pos == 'top' ) {	
				$sticky_class 	=	'gnpub-gnfollow-sticky-top';		
			}else if( $sticky_pos == 'bottom' ) {
				$sticky_class 	=	'gnpub-gnfollow-sticky-bottom';
			}
			$follow_btn =	self::shortcode_html();
			?>
			<div id="gnpub-gnfollow-sticky" class="<?php echo esc_attr( $sticky_class ); ?>">
				<?php echo $follow_btn; ?>
			</div>
			<?php

		}

	}

	/**
	 * Save google news follow form data
	 * @since 	1.5.19
	 * */
	public function save_gnfollow(){
		
		$redirect_url 	=	admin_url('admin.php?page=gn-publisher-settings&tab=gn-features');
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_safe_redirect( $redirect_url );
		}

		if ( ! isset( $_POST['gnpub_gnfollow_nonce'] ) ) {
			wp_safe_redirect( $redirect_url );
		}

		if ( ! wp_verify_nonce( $_POST['gnpub_gnfollow_nonce'], 'gnpub-gnfollow-settings' ) ) {
			wp_safe_redirect( $redirect_url );
		}
		
		$shortcode_options 		=	get_option( 'gnpub_shortcode_options', self::gnpub_gnfollow_defaults() );
		if ( ! empty( $_POST['gnpub_gnfollow_background_color'] ) ) {
			$shortcode_options['gnpub_gnfollow_background_color'] 	=	sanitize_text_field( wp_unslash( $_POST['gnpub_gnfollow_background_color'] ) );	
		}
		if ( ! empty( $_POST['gnpub_gnfollow_text_color'] ) ) {
			$shortcode_options['gnpub_gnfollow_text_color'] 		=	sanitize_text_field( wp_unslash( $_POST['gnpub_gnfollow_text_color'] ) );	
		}
		if ( ! empty( $_POST['gnpub_gnfollow_redirect_link'] ) ) {
			$shortcode_options['gnpub_gnfollow_redirect_link'] 		=	sanitize_text_field( wp_unslash( $_POST['gnpub_gnfollow_redirect_link'] ) );	
		}
		if ( ! empty( $_POST['gnpub_gnfollow_icon_image'] ) ) {
			$shortcode_options['gnpub_gnfollow_icon_image'] 		=	sanitize_text_field( wp_unslash( $_POST['gnpub_gnfollow_icon_image'] ) );	
		}
		if ( ! empty( $_POST['gnpub_gnfollow_text_one'] ) ) {
			$shortcode_options['gnpub_gnfollow_text_one'] 			=	sanitize_text_field( wp_unslash( $_POST['gnpub_gnfollow_text_one'] ) );	
		}
		if ( ! empty( $_POST['gnpub_gnfollow_text_one'] ) ) {
			$shortcode_options['gnpub_gnfollow_text_one'] 			=	sanitize_text_field( wp_unslash( $_POST['gnpub_gnfollow_text_one'] ) );	
		}

	   	update_option( 'gnpub_shortcode_options', $shortcode_options );

	   	wp_safe_redirect( $redirect_url );

	}
}

new GNPUB_News_Follow();