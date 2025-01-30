<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controllers handles sitemap feature
 * @since 1.5.19
 */
class GNPUB_Sitemap {

	public function __construct() {
		
		add_action( 'gnpub_sitemap_form', array( $this, 'sitemap_form_render') );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_sitemap_script' ) );
		add_action( 'wp_ajax_gnpub_save_options', array( $this, 'save_options_ajax' ) );
		add_action( 'init', array( $this, 'sitemap_init' ) );
		add_filter( 'template_include', array( $this,'include_sitemap_templates' ) );

	}

	/**
	 * Enqueue sitemap related script
	 * @since 1.5.19
	 * */
	public function load_sitemap_script( $hook_suffix ) {

		if ( $hook_suffix == "settings_page_gn-publisher-settings" ) {

			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'gn-admin-sitemap-script', GNPUB_URL . "/assets/js/gn-admin-sitemap{$min}.js", array('jquery'), GNPUB_VERSION, 'true' );
			
			$current_screen = get_current_screen(); 		

			$data = array(     
				'ajax_url'                     	=> admin_url( 'admin-ajax.php' ),             
				'gnpub_sitemap_security_nonce'  => wp_create_nonce('gnpub_sitemap_ajax_check_nonce'),
			);
							
			$data = apply_filters('gnpub_localize_filter',$data,'gnpub_localize_data');		
			
			wp_localize_script( 'gn-admin-sitemap-script', 'gnpub_sitemap_localize_data', $data );

			wp_enqueue_script( 'gn-admin-sitemap-script' );

			wp_enqueue_style( 'gn-admin-sitemap-style', GNPUB_URL . "/assets/css/gn-admin-sitemap{$min}.css", array(), GNPUB_VERSION, );
			
		}	
	}

	/**
	 * Render sitemap form
	 * @since 1.5.19
	 * */
	public function sitemap_form_render() {

		$options_data 			=	get_option( 'gnpub_news_sitmap' );
		$options 				=	unserialize( $options_data );

		$default_options 		=	array('gnpub_enable_gnsitemap' => false );

		$gnpub_options 			=	get_option( 'gnpub_new_options', $default_options );
		$gnpub_enable_gnsitemap =	isset($gnpub_options['gnpub_enable_gnsitemap'])?$gnpub_options['gnpub_enable_gnsitemap']:false;

		$post_types 			=	'';
		if ( !empty( $options ) && ! empty( $options['news_sitemap_include_post_types'] ) ) {
			$post_types 		= $options['news_sitemap_include_post_types'];
		} 

		$get_categories 		= '';
		if ( ! empty( $options ) && ! empty( $options['news_sitemap_exclude_terms'] ) ) {
			$get_categories 	= $options['news_sitemap_exclude_terms'];
		} 
		?>	

		<tr>
	        <th><label for="gnpub_enable_gnsitemap" class="gnpub-hover-pointer"><?php echo esc_html__( 'Google News Sitemap', 'gn-publisher' ); ?></label></th>
	        <td>
			<input <?php if(!$gnpub_enable_gnsitemap){ echo 'type="checkbox"';}else{echo 'type="hidden"';}?> name="gnpub_enable_gnsitemap" id="gnpub_enable_gnsitemap" <?php checked( $gnpub_enable_gnsitemap, true ); ?> value="1" />
			<label for="gnpub_enable_gnsitemap" id="gnpub_gnsitemap_label" data-checked="<?php _e( "<b class='gnpub-green'>Enabled</b>", 'gn-publisher' ); ?>" data-unchecked="<?php echo esc_html__( 'You will generally need a News Sitemap when your website is included in Google News.', 'gn-publisher' ); ?>"><?php if($gnpub_enable_gnsitemap){  _e( "<b class='gnpub-green'>Enabled</b>", 'gn-publisher' );}else{ echo esc_html__( 'You will generally need a News Sitemap when your website is included in Google News.', 'gn-publisher' );} ?></label> 
			&nbsp; <a id="gnpub_gnsitemap_config" class="gnpub_config_button" <?php if(!$gnpub_enable_gnsitemap){ echo 'style="display:none";';}?>><?php echo esc_html__( 'Configure Settings', 'gn-publisher' ); ?></a>
			 <a class="gnpub-disbale-sitemap-link" id="gnpub_disable_link_2" data-id="gnpub_enable_gnsitemap" <?php if(!$gnpub_enable_gnsitemap){ echo 'style="display:none";';}?>><?php echo esc_html__( 'disable', 'gn-publisher' ); ?></a>
	    	</td>  
		</tr>

		<div  id="gnpub_modal_gnsitemap" class="gnpub-sitemap-modal">
		  	<div class="gnpub-sitemap-modal-content">
				<span id="gnpub_gnsitemap_close" class="gnpub-sitemap-close ">&times;</span>
				<p>
					<p><?php echo esc_html__( 'You will generally only need a News Sitemap when your website is included in Google News.', 'gn-publisher' ); ?></p>
					<p><?php echo esc_html__( 'Click here', 'gn-publisher' ); ?> <?php echo apply_filters('gnpubpro_sitemap_links','<a href="'.get_site_url().'/gn_sitemap.xml" target="_blank">View your News Sitemap.</a>');?></p>
					<small><?php echo esc_html__( 'Note : If the above link is not working then update wordpress permalinks.', 'gn-publisher' ); ?> </small>

					<h3><?php echo esc_html__( 'General settings', 'gn-publisher'); ?></h3>

					<h4><?php echo esc_html__( 'Post Types to include in News Sitemap', 'gn-publisher' ); ?></h4>
					<input class="gnpub_checkbox double" id="gnpub_news_sitemap_include_post_types_post" type="checkbox" name="gnpub_news_sitmap[news_sitemap_include_post_types][post]" <?php if(!empty($post_types) && isset($post_types['post']) && $post_types['post'] == 'post'){ echo 'checked="checked"'; } ?>  value="post" />
					<label class="gnpub_checkbox" for="gnpub_news_sitemap_include_post_types_post"><?php echo esc_html__( 'Posts (post)', 'gn-publisher' ); ?></label>
				
					<input class="gnpub_checkbox" id="gnpub_news_sitemap_include_post_types_page" type="checkbox" name="gnpub_news_sitmap[news_sitemap_include_post_types][page]" <?php if(!empty($post_types) && isset($post_types['page']) && $post_types['page'] == 'page'){ echo 'checked="checked"'; } ?> value="page" />
					<label class="gnpub_checkbox" for="gnpub_news_sitemap_include_post_types_page"><?php echo esc_html__( 'Pages (page)', 'gn-publisher' ); ?></label>
				
					<input class="gnpub_checkbox" id="gnpub_news_sitemap_include_post_types_attachment" type="checkbox" name="gnpub_news_sitmap[news_sitemap_include_post_types][attachment]" <?php if(!empty($post_types) && isset($post_types['attachment']) && $post_types['attachment'] == 'attachment'){ echo 'checked="checked"'; } ?> value="attachment" />
					<label class="gnpub_checkbox" for="gnpub_news_sitemap_include_post_types_attachment"><?php echo esc_html__( 'Media (attachment)', 'gn-publisher' ); ?></label>
			
					<h4><?php esc_html__( 'Categories to exclude', 'gn-publisher' ); ?></h4>
					<?php 
					$categories = get_categories();
				
					if ( ! empty( $categories ) ) {
						$c 		= '0';
						foreach ( $categories as $category ) {

							$c++;
							$checked = '';
							if ( ! empty( $get_categories ) ) {
								foreach ( $get_categories as $categoryids ) { 
									if ( $categoryids == $category->term_id ) {
										$checked = $category->term_id;
									}
								}
							} 
							$label_class 		=	$category->name.''.$c.'_for_post';
							$input_field_name 	=	'gnpub_news_sitmap[news_sitemap_exclude_terms][category_'.$category->name.''.$c.'_for_post]';
							$input_field_id 	=	'gnpub_news_sitemap_exclude_terms_category_'.$category->name.''.$c.'_for_post';
							?>
						 	<div class="gnpub_float_left">
								<input class="gnpub_checkbox" <?php if(!empty($checked) && $checked == $category->term_id){ echo 'checked="checked"'; } ?> id="<?php echo esc_attr( $input_field_id ); ?>" type="checkbox" name="<?php echo esc_attr( $input_field_name ); ?>"  value="<?php echo esc_attr( $category->term_id ); ?>">
								<label class="gnpub_checkbox" for="gnpub_news_sitemap_exclude_terms_category_<?php echo esc_attr($label_class); ?>"><?php echo esc_html( $category->name ); ?></label>
							</div>
							<?php
						}

					}else{ ?>
						<p><?php echo esc_html__( 'There is no category.', 'gn-publisher' ); ?></p>
					<?php } 
					?>
				</p>
				<input type="submit" name="save_gnpub_settings" id="submit" class="button button-primary" value="Update Settings">
			</div>
		</div>

	<?php
	}

	/**
	 * Save sitemap option
	 * @since 1.5.19
	 * */
	public function save_options_ajax(){
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_POST['security_nonce'] ) ) {
		   return;
		}
		if ( ! wp_verify_nonce( $_POST['security_nonce'], 'gnpub_sitemap_ajax_check_nonce' ) ) {
		   return;
		}

		$option_name 			=	isset( $_POST['option_name'] ) ? sanitize_text_field( wp_unslash( $_POST['option_name'] ) ) : '';
		$option_value 			=	isset($_POST['option_value'] ) ? sanitize_text_field( wp_unslash( $_POST['option_value'] ) ) : '';
		 
		if ( ! empty( $option_name ) && ! empty( $option_value ) ) {

		   	$gnpub_options		= get_option( 'gnpub_new_options', false);

		   	if ( $gnpub_options ) {

		    	if ( in_array( $option_name, array( 'gnpub_enable_gnsitemap' ) ) ) {

				   $option_value 	=  ( $option_value ==true ) ? true : false;
			       $gnpub_options[$option_name] = $option_value;
				   update_option( 'gnpub_new_options', $gnpub_options );
				   echo wp_json_encode( array( 'status'=>'success','msg'=>'option updated','options'=>$gnpub_options ) );
				   wp_die();

		    	}

		   	}
		   	else{

				$gnpub_options		=	array();
				if ( in_array( $option_name, array( 'gnpub_enable_gnsitemap' ) ) ) {
					$option_value 	= ( $option_value == true ) ? true : false;
					$gnpub_options[$option_name] = $option_value;
					update_option( 'gnpub_new_options', $gnpub_options );
					echo wp_json_encode( array ( 'status'=>'success', 'msg'=>'option updated','options'=> $gnpub_options) );
					wp_die();
				}

		   }
		   echo wp_json_encode(array('status'=>'false','msg'=>'option does not exists'));wp_die();

		 }
		 wp_die();

	}

	/**
	 * Add sitemap rewrite rules
	 * @since 1.5.19
	 * */
	public function sitemap_init() {
		  
		add_rewrite_rule( 
			'^gn_sitemap\.xml/?$',
			'index.php?gn_sitemap=1',
			 'top' 
		);

		add_rewrite_rule(
			'^([a-z]{2}(?:_[a-z]{2})?)\/gn_sitemap_single\.xml$',
			'index.php?gn_sitemap_ln=1&lang=$matches[1]',
			'top'
		);
		
		add_filter( 'query_vars', array( $this, 'add_sitemap_query_vars' ) ); 

	}

	/**
	 * Add query vars
	 * @since 1.5.19
	 * */
	public function add_sitemap_query_vars( $vars ) {
		
		$vars[] = 'gn_sitemap';
		$vars[] = 'gn_sitemap_ln';

		return $vars;

	}

	/**
	 * Include sitemap templates
	 * @param 	$template 	string
	 * @return 	$template 	string
	 * @since 	1.5.19
	 * */
	public function include_sitemap_templates( $template ) {
		
	    // Check if the 'gn_sitemap_ln' query variable is set
	    if ( get_query_var( 'gn_sitemap_ln' ) ) {
	        // Return the path to the single sitemap template
	        return dirname( __FILE__ ) . '/../../xml/gnpub-news-sitemap-single.php';
	    }
	    
	    // Check if 'gn_sitemap' query variable is not set or is empty
	    if ( ! get_query_var( 'gn_sitemap' ) ) {
	        return $template;
	    }

	    // Return the path to the general sitemap template
	    return dirname( __FILE__ ) . '/../../xml/gnpub-news-sitemap.php';

	}

}

new GNPUB_Sitemap();