<?php 
/*!
 * Project Pages
 * http://www.woodyhayday.com/project/project-pages
 * V2.0
 *
 * Date: 15/04/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

// converter
function projectPages_vue_form_checkbox_val( $form_key = '', $return_as_int = true ){

	if ( $form_key && isset( $_POST[ $form_key ] ) ){

		if ( sanitize_text_field( $_POST[ $form_key ] ) == 'on' ){

			if ( $return_as_int ) return 1;

			return true;

		}

	}

	if ( $return_as_int ) return 0;

	return false;

}

// Settings
function projectPages_page_settings(){

	global $wpdb, $projectPages_Settings, $projectPages_db_version, $projectPages_version, $projectPages_urls, $projectPages_slugs; 
	
	// for welcome wizard
	projectPages_setHasViewedSettings();

	$confirmAct = false;
	$settings = $projectPages_Settings->getAll();
											

	// Act on any edits!
	if ( isset( $_POST['pp_settings_nonce'] ) ){

		// Verify nonce with sanitizing as per WPCS
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'pp_settings_nonce' ] ) ), 'pp-settings-edit' ) ) {
		  return;
		}


		// Retrieve
		$updatedSettings = array();
		$updatedSettings['permalink_root'] = 'projects'; if (isset($_POST['pp_permalink_root'])){

			// blacklist checks
			$proposed_permalink_root = sanitize_title($_POST['pp_permalink_root']);

			$permalink_root_blacklist = array(

				'blog',
				'home',
				'wp-admin'

			);

			if ( !in_array( $proposed_permalink_root, $permalink_root_blacklist ) ){
				
				$updatedSettings['permalink_root'] = $proposed_permalink_root;

			} else {

				// ideally output a dialog

			}

		}
		$updatedSettings['feat_share'] = false; if (isset($_POST['pp_feat_share']) && $_POST['pp_feat_share'] == "true" ) $updatedSettings['feat_share'] = true;	
		$updatedSettings['poweredby'] = projectPages_vue_form_checkbox_val( 'pp_poweredby' );
		$updatedSettings['favicon'] = ''; if (isset($_POST['pp_favicon'])) $updatedSettings['favicon'] = sanitize_text_field($_POST['pp_favicon']);		
		$updatedSettings['css_override'] = ''; if (isset($_POST['pp_css_override'])) $updatedSettings['css_override'] = projectPages_textProcess($_POST['pp_css_override']);
		$updatedSettings['menu_type'] = ''; if (isset($_POST['pp_menu_type'])) $updatedSettings['menu_type'] = sanitize_text_field($_POST['pp_menu_type']);		
		$updatedSettings['menu_tags'] = ''; if (isset($_POST['pp_menu_tags'])) $updatedSettings['menu_tags'] = sanitize_text_field($_POST['pp_menu_tags']);		
		$updatedSettings['display_type'] = ''; if (isset($_POST['pp_display_type'])) $updatedSettings['display_type'] = sanitize_text_field($_POST['pp_display_type']);		
		$updatedSettings['display_showstatus'] = projectPages_vue_form_checkbox_val( 'pp_display_showstatus' );
		$updatedSettings['display_showbiline'] = projectPages_vue_form_checkbox_val( 'pp_display_showbiline' );
		$updatedSettings['thin_column'] = projectPages_vue_form_checkbox_val( 'pp_thin_column' );
		$updatedSettings['use_logs'] = projectPages_vue_form_checkbox_val( 'pp_use_logs' );
		$updatedSettings['template_mode'] = 'default'; if ( isset( $_POST['pp_template_mode'] ) && $_POST['pp_template_mode'] == 'legacy' ) $updatedSettings['template_mode'] = 'legacy';
		$updatedSettings['share_fb'] = projectPages_vue_form_checkbox_val( 'pp_share_fb' );
		$updatedSettings['share_x'] = projectPages_vue_form_checkbox_val( 'pp_share_x' );
		$updatedSettings['share_li'] = projectPages_vue_form_checkbox_val( 'pp_share_li' );
		$updatedSettings['share_telegram'] = projectPages_vue_form_checkbox_val( 'pp_share_telegram' );
		$updatedSettings['share_x_template'] = ''; if (isset($_POST['pp_share_x_template'])) $updatedSettings['share_x_template'] = sanitize_text_field($_POST['pp_share_x_template']);		
		$updatedSettings['share_telegram_template'] = ''; if (isset($_POST['pp_share_telegram_template'])) $updatedSettings['share_telegram_template'] = sanitize_text_field($_POST['pp_share_telegram_template']);		
		$updatedSettings['add_og_meta'] = projectPages_vue_form_checkbox_val( 'pp_add_og_meta' );

		// PRO hook
		$updatedSettings = apply_filters( 'project_pages_save_settings', $updatedSettings );

		// Brutal update
		foreach ($updatedSettings as $k => $v) $projectPages_Settings->update($k,$v);

		// $msg out!
		$sbupdated = true;

		// Catch permalink root changes...
		$existing_permalink_root = get_option( 'pp_permalink_root', '' );
		if ( $updatedSettings['permalink_root'] !== $existing_permalink_root ){

			// flush the rules
			projectPages_rewrite_rules_flush();

			// update the option
			update_option( 'pp_permalink_root', $updatedSettings['permalink_root'], false );

		}
		
		// check if theme is block-based
		projectPages_theme_check();
			
	}

	/*
	// catch resets.
	if ( isset( $_GET['resetsettings'] ) ) if ( $_GET['resetsettings'] == 1 ){


		if (!isset($_GET['imsure'])){

				#} Needs to confirm!	
				$confirmAct = true;
				$actionStr 				= 'resetsettings';
				$actionButtonStr 		= __('Reset Settings to Defaults?','projectpages');
				$confirmActStr 			= __('Reset All Project Pages Settings?','projectpages');
				$confirmActStrShort 	= __('Are you sure you want to reset these settings to the defaults?','projectpages');
				$confirmActStrLong 		= __('Once you reset these settings you cannot retrieve your previous settings.','projectpages');

			} else {


				if (wp_verify_nonce( $_GET['_wpnonce'], 'resetclearprojectpages' ) ){

						#} Reset
						$projectPages_Settings->resetToDefaults();

						#} Reload
						$settings = $projectPages_Settings->getAll();

						#} Msg out!
						$sbreset = true;

				}

			}

	} 

	// catch template installs.
	if (isset($_GET['installtemplates'])) if ($_GET['installtemplates']==1){


		if (!isset($_GET['imsure'])){

				#} Needs to confirm!	
				$confirmAct = true;
				$actionStr 				= 'installtemplates';
				$actionButtonStr 		= __('Install Template Examples?','projectpages');
				$confirmActStr 			= __('Install Project Page Templates into your Theme directory (in the subdirectory /project-pages)?','projectpages');
				$confirmActStrShort 	= __('Are you sure you want to install these default templates?','projectpages');
				$confirmActStrLong 		= __('(Any existing templates will be renamed with a prefix _replaced-*timestamp*.php)','projectpages');

			} else {


				if (wp_verify_nonce( $_GET['_wpnonce'], 'resetclearprojectpages' ) ){

						#} Rename exisitng
						projectPages_renameProjectPageTemplates();

						#} Copy in
						projectPages_installProjectPageTemplates(true);

						#} Msg out!
						$installedTemplates = true;

				}

			}

	} */




	// retrieve settings
	$settings = $projectPages_Settings->getAll();
	$settingsIndex = $projectPages_Settings->getValuesIndex( true, true );

	// pro flag
	$pro_flag = apply_filters( 'project_pages_pro_flag', false );

	// Vue App out

	?>
	
	<script>
		var pp_settings = <?php echo json_encode($settingsIndex); ?>;
		var pp_wp_settings = <?php echo json_encode(
			array(
				'version' => $projectPages_version,
				'settings_slug' => $projectPages_slugs['settings'],
				'settings_url' => get_admin_url( null, 'edit.php?post_type=projectpage&amp;page=projectpages-plugin-settings' ),
				'home_url' => ppurl(),
				'blog_root_url' => get_bloginfo('url'),
				'feedback_url' => ppurl('feedback'),
				'assets_url' => PROJECTPAGES_URL,
				'upgrade_url' => ppurl('get-pro'),
				'post_url' => 'edit.php?post_type=projectpage&page=' . $projectPages_slugs['settings'],
				'new_project_url' => get_admin_url( null, 'post-new.php?post_type=projectpage' ),
				'welcome_url' => get_admin_url( null, 'options.php?page=projectpages' ),
				'nonce' => wp_create_nonce( 'pp-settings-edit' ),
				'pro_flag' => $pro_flag
			)
		); ?>;
	</script>

	<div id="pp-settings-app"></div>

	<?php

}