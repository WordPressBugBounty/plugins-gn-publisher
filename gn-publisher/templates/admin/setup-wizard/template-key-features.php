<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Wizard key features template
 * @since 1.5.19
 * */

$gnpub_options 					=	get_option( 'gnpub_new_options' );
$gnpub_news_schema 				=	isset( $gnpub_options['gnpub_enable_news_article_schema'] ) ? $gnpub_options['gnpub_enable_news_article_schema']: false;

$checklist_options 				=	get_option( 'gnpub_setup_wizard_checklist' );

$news_chk_box 					=	isset( $checklist_options['gnpub_enable_news_article_schema'] ) ? $checklist_options['gnpub_enable_news_article_schema']: false;
$news_chk_box_class 			=	'gnpub-setup-wizard-chklist-td ';
$news_chk_box_op_class 			=	'gnpub-setup-wizard-add-opacity ';
if ( $news_chk_box == true ) {
	$news_chk_box_class 		.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$news_chk_box_op_class 		.= 	'gnpub-setup-wizard-chklist-opacity';		
}

$fimage_chk_box 				=	isset( $checklist_options['gnpub_show_info_featured_img'] ) ? $checklist_options['gnpub_show_info_featured_img']: false;
$fimage_chk_box_class 			=	'gnpub-setup-wizard-chklist-td ';
$fimage_chk_box_op_class 		=	'gnpub-setup-wizard-add-opacity ';
if ( $fimage_chk_box == true ) {
	$fimage_chk_box_class 		.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$fimage_chk_box_op_class 	.= 	'gnpub-setup-wizard-chklist-opacity';		
}
?>
<header>
	<h1><?php echo esc_html__( 'Key Features', 'gn-publisher' ); ?></h1>
	<p><?php echo esc_html__( 'Choose your features which you want to activate or deactivate.', 'gn-publisher' ); ?></p>
</header>

<div class="gnpub-setup-wizard-options-wrapper">
	<span id="gnpub-tick-to-comp"><?php echo esc_html__( 'Tick to Complete', 'gn-publisher' ); ?></span>
	<table class="form-table gnpub-setup-wizard-form-table">
		<tbody>
			<tr class="gnpub-setup-wizard-chklist-tr">
				<th class="<?php echo esc_attr( $news_chk_box_op_class ); ?>" data-hide="gnpub_enable_news_article_schema">
					<label for="gnpub_enable_news_article_schema" class="gnpub-hover-pointer"><?php echo esc_html__( 'News Article Schema', 'gn-publisher'); ?></label>
				</th>
				<td class="<?php echo esc_attr( $news_chk_box_op_class ); ?>" data-hide="gnpub_enable_news_article_schema">
		          <input type="checkbox" name="gnpub_enable_news_article_schema" id="gnpub_enable_news_article_schema" <?php checked( $gnpub_news_schema, true ); ?> value="1" />
		          <label for="gnpub_enable_news_article_schema"><?php echo esc_html__( 'Add NewsArticle schema on every post', 'gn-publisher.' ); ?> &nbsp; <span class="gnpub-span-lrn-more"> <a target="_blank" style="text-decoration:none;" href="https://gnpublisher.com/docs/"><?php echo esc_html__( 'Learn More', 'gn-publisher' ); ?></a></span></label>
		          
		        </td>
		        <td class="<?php echo esc_attr( $news_chk_box_class ); ?>">
		        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_enable_news_article_schema]" <?php checked( $news_chk_box, true ); ?> value="1" data-dont-hide="gnpub_enable_news_article_schema" data-chk-opt-name="gnpub_enable_news_article_schema" />
		        </td>
			</tr>
			<tr class="gnpub-setup-wizard-chklist-tr">
        		<th class="<?php echo esc_attr( $fimage_chk_box_op_class ); ?>" data-hide="gnpub_show_info_featured_img">
        			<label for="gnpub_show_info_featured_img" class="gnpub-hover-pointer"><?php echo esc_html__( 'Show info for Featured Image', 'gn-publisher' ); ?></label>
        		</th>
        		<td class="<?php echo esc_attr( $fimage_chk_box_op_class ); ?>" data-hide="gnpub_show_info_featured_img">
		        	<input type="checkbox" name="gnpub_show_info_featured_img" id="gnpub_show_info_featured_img" class="gnpub_show_info_featured_img" value="1" />
		        	<label for="gnpub_show_info_featured_img"><?php echo esc_html__( 'This will show additional data for featured image like caption , alt text , description etc (if available)', 'gn-publisher.' ); ?></label>
		        </td>
		        <td class="<?php echo esc_attr( $fimage_chk_box_class ); ?>">
		        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_show_info_featured_img]" <?php checked( $fimage_chk_box, true ); ?> value="1" data-dont-hide="gnpub_show_info_featured_img" data-chk-opt-name="gnpub_show_info_featured_img" />
		        </td>
        	</tr>
		</tbody>
		<input type="hidden" name="tab" id="gnpub-active-tab" value="key_features" />
	</table>
</div>