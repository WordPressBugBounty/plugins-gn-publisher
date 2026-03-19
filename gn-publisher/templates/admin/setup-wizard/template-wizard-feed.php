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

$gnpub_robots_url 				=	get_home_url().'/robots.txt';
$gnpub_robots_response 			=	GNPUB_Status::get_remote_response( $gnpub_robots_url );
$gnpub_robot_status 			=	$gnpub_robots_response['status'];
$gnpub_status_class 			=	'dashicons dashicons-no-alt gnpub-fail-status';

if ( $gnpub_robot_status == 'success' ) {
	$gnpub_status_class 		=	'dashicons dashicons-yes gnpub-success-status';	
}

$gnpub_news_article_response 	=	GNPUB_Status::check_for_news_schema();
$gnpub_schema_status 			=	$gnpub_news_article_response['status'];
$gnpub_schema_class 			=	'dashicons dashicons-no-alt gnpub-fail-status';

if ( $gnpub_schema_status == 'success' ) {
	$gnpub_schema_class 		=	'dashicons dashicons-yes gnpub-success-status';	
}

$gnpub_byline_class 			=	'dashicons dashicons-no-alt gnpub-fail-status';

if ( $gnpub_schema_status == 'success' && $gnpub_news_article_response['byline'] == 'yes' ) {
	$gnpub_byline_class 		=	'dashicons dashicons-yes gnpub-success-status';	
}

$gnpub_checklist_options 		=	get_option( 'gnpub_setup_wizard_checklist' );

$gnpub_robot_chk_box 			=	isset( $gnpub_checklist_options['gnpub_gn_status_robot'] ) ? $gnpub_checklist_options['gnpub_gn_status_robot']: false;
$gnpub_robot_chk_box_class 			=	'gnpub-setup-wizard-chklist-td ';
$gnpub_robot_chk_box_op_class 		=	'gnpub-setup-wizard-add-opacity ';
if ( $gnpub_robot_chk_box == true ) {
	$gnpub_robot_chk_box_class 		.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$gnpub_robot_chk_box_op_class 	.= 	'gnpub-setup-wizard-chklist-opacity';		
}

$gnpub_news_chk_box 					=	isset( $gnpub_checklist_options['gnpub_gn_status_nas'] ) ? $gnpub_checklist_options['gnpub_gn_status_nas']: false;
$gnpub_news_chk_box_class 				=	'gnpub-setup-wizard-chklist-td ';
$gnpub_news_chk_box_op_class 			=	'gnpub-setup-wizard-add-opacity ';
if ( $gnpub_news_chk_box == true ) {
	$gnpub_news_chk_box_class 			.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$gnpub_news_chk_box_op_class 		.= 	'gnpub-setup-wizard-chklist-opacity';		
}

$gnpub_byline_chk_box 					=	isset( $gnpub_checklist_options['gnpub_gn_status_byline'] ) ? $gnpub_checklist_options['gnpub_gn_status_byline']: false;
$gnpub_byline_chk_box_class 			=	'gnpub-setup-wizard-chklist-td ';
$gnpub_byline_chk_box_op_class 		=	'gnpub-setup-wizard-add-opacity ';
if ( $gnpub_byline_chk_box == true ) {
	$gnpub_byline_chk_box_class 		.= 	'gnpub-setup-wizard-chklist-tr-checked';		
	$gnpub_byline_chk_box_op_class 	.= 	'gnpub-setup-wizard-chklist-opacity';		
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
				<th class="<?php echo esc_attr( $gnpub_robot_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_robot">
					<label for="gnpub_gn_status_robot" class="gnpub-hover-pointer"><?php echo esc_html__( 'Robots.txt', 'gn-publisher' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $gnpub_robot_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_robot">
		          <span class="<?php echo esc_attr( $gnpub_status_class ); ?>"></span>    
		      </td>
	        <td class="<?php echo esc_attr( $gnpub_robot_chk_box_class ); ?>" style="float: right;">
	        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_gn_status_robot]" <?php checked( $gnpub_robot_chk_box, true ); ?> value="1" data-dont-hide="gnpub_gn_status_robot" data-chk-opt-name="gnpub_gn_status_robot" />
	        </td>
			</tr>

			<tr class="gnpub-setup-wizard-chklist-tr">
				<th class="<?php echo esc_attr( $gnpub_news_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_nas">
					<label for="gnpub_gn_status_nas" class="gnpub-hover-pointer"><?php echo esc_html__( 'News Article Schema', 'gn-publisher' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $gnpub_news_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_nas">
		          <span class="<?php echo esc_attr( $gnpub_schema_class ); ?>"></span>    
		      </td>
	        <td class="<?php echo esc_attr( $gnpub_news_chk_box_class ); ?>" style="float: right;">
	        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_gn_status_nas]" <?php checked( $gnpub_news_chk_box, true ); ?> value="1" data-dont-hide="gnpub_gn_status_nas" data-chk-opt-name="gnpub_gn_status_nas" />
	        </td>
			</tr>

			<tr class="gnpub-setup-wizard-chklist-tr">
				<th class="<?php echo esc_attr( $gnpub_byline_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_byline">
					<label for="gnpub_gn_status_byline" class="gnpub-hover-pointer"><?php echo esc_html__( 'Byline', 'gn-publisher' ); ?></label>
				</th>
				<td class="<?php echo esc_attr( $gnpub_byline_chk_box_op_class ); ?>" data-hide="gnpub_gn_status_byline">
		          <span class="<?php echo esc_attr( $gnpub_byline_class ); ?>"></span>    
		      </td>
	        <td class="<?php echo esc_attr( $gnpub_byline_chk_box_class ); ?>" style="float: right;">
	        	<input class="gnpub-setup-wizard-chklist-chkbox" type="checkbox" name="gnpub_setup_wizard_checklist[gnpub_gn_status_byline]" <?php checked( $gnpub_byline_chk_box, true ); ?> value="1" data-dont-hide="gnpub_gn_status_byline" data-chk-opt-name="gnpub_gn_status_byline" />
	        </td>
			</tr>

		</tbody>
	</table>
</div>
<input type="hidden" name="tab" id="gnpub-active-tab" value="general_status" />