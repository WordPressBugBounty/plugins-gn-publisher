<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Wizard sitemap template
 * @since 1.5.19
 * */

$gnpub_options 				=	get_option( 'gnpub_new_options' );
$gnpub_enable_gnsitemap 	=	isset( $gnpub_options['gnpub_enable_gnsitemap'] ) ? $gnpub_options['gnpub_enable_gnsitemap'] : false;

$gnpub_sitemap_options 		=	get_option( 'gnpub_news_sitmap' );
$gnpub_sitemap_options 		=	unserialize( $gnpub_sitemap_options );

$gnpub_checklist_options 	=	get_option( 'gnpub_setup_wizard_checklist' );
$gnpub_site_chk_box 		=	isset( $gnpub_checklist_options['gnpub_enable_gnsitemap'] ) ? $gnpub_checklist_options['gnpub_enable_gnsitemap']: false;
$gnpub_site_chk_box_class 	=	'gnpub-setup-wizard-chklist-td ';
$gnpub_site_chk_box_op_class 		=	'gnpub-setup-wizard-add-opacity ';
if ( $gnpub_site_chk_box == true ) {
	$gnpub_site_chk_box_class 	.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$gnpub_site_chk_box_op_class 	.= 	'gnpub-setup-wizard-chklist-opacity';		
}

$gnpub_post_types 			=	'';
if ( !empty( $gnpub_sitemap_options ) && ! empty( $gnpub_sitemap_options['news_sitemap_include_post_types'] ) ) {
	$gnpub_post_types 		= $gnpub_sitemap_options['news_sitemap_include_post_types'];
} 

$gnpub_get_categories 		=	'';
if ( ! empty( $gnpub_sitemap_options ) && ! empty( $gnpub_sitemap_options['news_sitemap_exclude_terms'] ) ) {
	$gnpub_get_categories 	=	$gnpub_sitemap_options['news_sitemap_exclude_terms'];
} 
?>
<header>
	<h1><?php echo esc_html__( 'Sitemap', 'gn-publisher' ); ?></h1>
	<p><?php echo esc_html__( 'You will generally need a News Sitemap when your website is included in Google News.', 'gn-publisher' ); ?></p>
</header>
<table class="form-table gnpub-setup-wizard-form-table">
	<span id="gnpub-tick-to-comp"><?php echo esc_html__( 'Tick to Complete', 'gn-publisher' ); ?></span>
	<tr class="gnpub-setup-wizard-chklist-tr">
        <th class="<?php echo esc_attr( $gnpub_site_chk_box_op_class ); ?>">
        	<label for="gnpub_enable_gnsitemap" class="gnpub-hover-pointer"><?php echo esc_html__( 'Google News Sitemap', 'gn-publisher' ); ?></label>
        </th>
        <td class="<?php echo esc_attr( $gnpub_site_chk_box_op_class ); ?>">
			<input type="checkbox" name="gnpub_enable_gnsitemap" id="gnpub_enable_gnsitemap" <?php checked( $gnpub_enable_gnsitemap, true ); ?> value="1" />
			<label for="gnpub_enable_gnsitemap" id="gnpub_gnsitemap_label"><?php echo esc_html__( 'You will generally need a News Sitemap when your website is included in Google News.', 'gn-publisher' ); ?></label>
    	</td>
    	<td class="<?php echo esc_attr( $gnpub_site_chk_box_class ); ?>">
		    <input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_enable_gnsitemap]" <?php checked( $gnpub_site_chk_box, true ); ?> value="1" data-chk-opt-name="gnpub_enable_gnsitemap" />
		</td>  
	</tr>
	<?php //do_action('gnpub_sitemap_form');  ?>
</table>
	
<?php
$gnpub_c_class 	=	'gnpub-d-none'; 
if ( $gnpub_enable_gnsitemap == true ) {
	$gnpub_c_class 	=	'gnpub-show';	
} 
$gnpub_c_class 	.=	' '.$gnpub_site_chk_box_op_class;	
?>
<div id="gnpub-sitemap-setting-wrapper" class="<?php echo esc_attr( $gnpub_c_class ); ?>">

	<table class="form-table">
		<tbody>
			<tr>
				<th>
					<label class="gnpub-index-tab-heading"><?php echo esc_html__( 'Post Types To Include', 'gn-publisher' ); ?></label>
				</th>
				<td>
					<table class="gnpub-setup-wizard-sitemap-post">
						<tr>
							<td>
								<input id="gnpub_news_sitemap_include_post_types_post" type="checkbox" name="gnpub_news_sitmap[news_sitemap_include_post_types][post]" <?php if(!empty($gnpub_post_types) && isset($gnpub_post_types['post']) && $gnpub_post_types['post'] == 'post'){ echo 'checked="checked"'; } ?>  value="post" />
								<label class="gnpub_checkbox gnpub-hover-pointer" for="gnpub_news_sitemap_include_post_types_page"><?php echo esc_html__( 'Posts (post)', 'gn-publisher' ); ?></label>
							</td>
						</tr>
						<tr>
							<td>
								<input id="gnpub_news_sitemap_include_post_types_page" type="checkbox" name="gnpub_news_sitmap[news_sitemap_include_post_types][page]" <?php if(!empty($gnpub_post_types) && isset($gnpub_post_types['page']) && $gnpub_post_types['page'] == 'page'){ echo 'checked="checked"'; } ?> value="page" />
								<label class="gnpub_checkbox gnpub-hover-pointer" for="gnpub_news_sitemap_include_post_types_page"><?php echo esc_html__( 'Pages (page)', 'gn-publisher' ); ?></label>
							</td>
						</tr>
						<tr>
							<td>
								<input id="gnpub_news_sitemap_include_post_types_attachment" type="checkbox" name="gnpub_news_sitmap[news_sitemap_include_post_types][attachment]" <?php if(!empty($gnpub_post_types) && isset($gnpub_post_types['attachment']) && $gnpub_post_types['attachment'] == 'attachment'){ echo 'checked="checked"'; } ?> value="attachment" />
								<label class="gnpub_checkbox gnpub-hover-pointer" for="gnpub_news_sitemap_include_post_types_attachment"><?php echo esc_html__( 'Media (attachment)', 'gn-publisher' ); ?></label>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<th>
					<label class="gnpub-index-tab-heading"><?php echo esc_html__( 'Categories To Exclude', 'gn-publisher' ); ?></label>
				</th>
				<td>
					<table class="gnpub-setup-wizard-sitemap-post">
						<?php 
						$gnpub_categories = get_categories();
						if ( ! empty( $gnpub_categories ) ) {
							$gnpub_c 		= '0';
							foreach ( $gnpub_categories as $gnpub_category ) {

								$gnpub_c++;
								$gnpub_checked = '';
								if ( ! empty( $gnpub_get_categories ) ) {
									foreach ( $gnpub_get_categories as $gnpub_categoryids ) { 
										if ( $gnpub_categoryids == $gnpub_category->term_id ) {
											$gnpub_checked = $gnpub_category->term_id;
										}
									}
								} 
								$gnpub_label_class 		=	$gnpub_category->name.''.$gnpub_c.'_for_post';
								$gnpub_input_field_name 	=	'gnpub_news_sitmap[news_sitemap_exclude_terms][category_'.$gnpub_category->name.''.$gnpub_c.'_for_post]';
								$gnpub_input_field_id 	=	'gnpub_news_sitemap_exclude_terms_category_'.$gnpub_category->name.''.$gnpub_c.'_for_post';
								?>
							 	<tr>
							 		<td>
										<input <?php if(!empty($gnpub_checked) && $gnpub_checked == $gnpub_category->term_id){ echo 'checked="checked"'; } ?> id="<?php echo esc_attr( $gnpub_input_field_id ); ?>" type="checkbox" name="<?php echo esc_attr( $gnpub_input_field_name ); ?>"  value="<?php echo esc_attr( $gnpub_category->term_id ); ?>">
										<label class="gnpub-hover-pointer" for="gnpub_news_sitemap_exclude_terms_category_<?php echo esc_attr($gnpub_label_class); ?>"><?php echo esc_html( $gnpub_category->name ); ?></label>
									</td>
								</tr>
								<?php
							}
						}else{
						?>
							<tr>
								<td><?php echo esc_html__( 'There is no category.', 'gn-publisher' ); ?></td>
							</tr>
						<?php
						}
						?>
					</table>
				</td>
			</tr>
    	</tbody>
	</table>

</div><!-- gnpub-sitemap-setting-wrapper div end -->
<input type="hidden" name="tab" id="gnpub-active-tab" value="site_map" />