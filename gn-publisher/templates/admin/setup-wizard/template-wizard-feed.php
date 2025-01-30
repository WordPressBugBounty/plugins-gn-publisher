<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Feed template
 * @since 1.5.19
 * */
?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Finish template
 * @since 1.5.19
 * */

$robots_url 				=	get_home_url().'/robots.txt';
$robots_response 			=	GNPUB_Status::get_remote_response( $robots_url );
$robot_status 				=	$robots_response['status'];
$status_class 				=	'dashicons dashicons-no-alt gnpub-fail-status';

if ( $robot_status == 'success' ) {
	$status_class 			=	'dashicons dashicons-yes gnpub-success-status';	
}

$news_article_response 	=	GNPUB_Status::check_for_news_schema();
$schema_status 			=	$news_article_response['status'];
$schema_class 				=	'dashicons dashicons-no-alt gnpub-fail-status';

if ( $schema_status == 'success' ) {
	$schema_class 			=	'dashicons dashicons-yes gnpub-success-status';	
}

$byline_class 				=	'dashicons dashicons-no-alt gnpub-fail-status';

if ( $schema_status == 'success' && $news_article_response['byline'] == 'yes' ) {
	$byline_class 			=	'dashicons dashicons-yes gnpub-success-status';	
}

$checklist_options 				=	get_option( 'gnpub_setup_wizard_checklist' );

$robot_chk_box 					=	isset( $checklist_options['gnpub_gn_status_robot'] ) ? $checklist_options['gnpub_gn_status_robot']: false;
$robot_chk_box_class 			=	'gnpub-setup-wizard-chklist-td ';
$robot_chk_box_op_class 		=	'gnpub-setup-wizard-add-opacity ';
if ( $robot_chk_box == true ) {
	$robot_chk_box_class 		.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$robot_chk_box_op_class 	.= 	'gnpub-setup-wizard-chklist-opacity';		
}

$news_chk_box 						=	isset( $checklist_options['gnpub_gn_status_nas'] ) ? $checklist_options['gnpub_gn_status_nas']: false;
$news_chk_box_class 				=	'gnpub-setup-wizard-chklist-td ';
$news_chk_box_op_class 			=	'gnpub-setup-wizard-add-opacity ';
if ( $news_chk_box == true ) {
	$news_chk_box_class 			.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$news_chk_box_op_class 		.= 	'gnpub-setup-wizard-chklist-opacity';		
}

$byline_chk_box 					=	isset( $checklist_options['gnpub_gn_status_byline'] ) ? $checklist_options['gnpub_gn_status_byline']: false;
$byline_chk_box_class 			=	'gnpub-setup-wizard-chklist-td ';
$byline_chk_box_op_class 		=	'gnpub-setup-wizard-add-opacity ';
if ( $byline_chk_box == true ) {
	$byline_chk_box_class 		.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$byline_chk_box_op_class 	.= 	'gnpub-setup-wizard-chklist-opacity';		
}

?>
<header>
	<h1><?php echo esc_html__( 'General Status', 'gn-publisher' ); ?></h1>
</header>
<div class="gnpub-setup-wizard-options-wrapper">
	<span id="gnpub-tick-to-comp"><?php echo esc_html__( 'Tick to Complete', 'gn-publisher' ); ?></span>

	<table class="form-table gnpub-setup-wizard-form-table">
		<tbody>

			<tr class="gnpub-setup-wizard-chklist-tr">
				<th class="<?php echo esc_attr( $robot_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_robot">
					<label for="gnpub_gn_status_robot" class="gnpub-hover-pointer"><?php echo esc_html__( 'Robots.txt', 'gn-publisher' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $robot_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_robot">
		          <span class="<?php echo esc_attr( $status_class ); ?>"></span>    
		      </td>
	        <td class="<?php echo esc_attr( $robot_chk_box_class ); ?>" style="float: right;">
	        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_gn_status_robot]" <?php checked( $robot_chk_box, true ); ?> value="1" data-dont-hide="gnpub_gn_status_robot" data-chk-opt-name="gnpub_gn_status_robot" />
	        </td>
			</tr>

			<tr class="gnpub-setup-wizard-chklist-tr">
				<th class="<?php echo esc_attr( $news_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_nas">
					<label for="gnpub_gn_status_nas" class="gnpub-hover-pointer"><?php echo esc_html__( 'News Article Schema', 'gn-publisher' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $news_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_nas">
		          <span class="<?php echo esc_attr( $schema_class ); ?>"></span>    
		      </td>
	        <td class="<?php echo esc_attr( $news_chk_box_class ); ?>" style="float: right;">
	        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_gn_status_nas]" <?php checked( $news_chk_box, true ); ?> value="1" data-dont-hide="gnpub_gn_status_nas" data-chk-opt-name="gnpub_gn_status_nas" />
	        </td>
			</tr>

			<tr class="gnpub-setup-wizard-chklist-tr">
				<th class="<?php echo esc_attr( $byline_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_byline">
					<label for="gnpub_gn_status_byline" class="gnpub-hover-pointer"><?php echo esc_html__( 'Byline', 'gn-publisher' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $byline_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_byline">
		          <span class="<?php echo esc_attr( $byline_class ); ?>"></span>    
		      </td>
	        <td class="<?php echo esc_attr( $byline_chk_box_class ); ?>" style="float: right;">
	        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_gn_status_byline]" <?php checked( $byline_chk_box, true ); ?> value="1" data-dont-hide="gnpub_gn_status_byline" data-chk-opt-name="gnpub_gn_status_byline" />
	        </td>
			</tr>

		</tbody>
	</table>
</div>
<input type="hidden" name="tab" id="gnpub-active-tab" value="general_status" />