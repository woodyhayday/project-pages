<?php 
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */


/* ======================================================
  General storage :D
   ====================================================== */
    global $projectPageStatuses;
    $projectPageStatuses = array(

    	// Hard typed for fun.
    	'idea' => array(__('Idea','projectpages'),'teal'),
    	'planning' => array( __('Planning','projectpages'), 'yellow'),
    	'inprogress' => array( __('In Progress','projectpages'), 'olive'),
    	'completed' => array( __('Completed','projectpages'), 'green'),
    	'completedsuccess' => array( __('Success','projectpages'), 'olive'),
    	'completedfailure' => array( __('Fail','projectpages'), 'teal'),
    	'shelved' => array( __('Shelved','projectpages'), 'blue'),
    	'archived' => array( __('Archived','projectpages'), 'blue'),
    	'abandoned' => array( __('Abandoned','projectpages'), 'pink'),
    	'evolved' => array(__('Evolved','projectpages'), 'violet')

    );

    // permalink root structure: e.g. yourblog.com/projects/PROJECT
    // superceded by setting, see: projectPages_permalink_root()
    // global $projectPagesPermaRoot; $projectPagesPermaRoot = 'projects';
    // ... and this is tags: e.g. yourblog.com/projects/tagged/TAG
    global $projectPagesTagsPermaRoot; $projectPagesTagsPermaRoot = 'tagged'; 
/* ======================================================
  / General storage :D
   ====================================================== */




	// ================================================================================

		// Define the key the config model will use to store the config in wp options

	// ================================================================================

		global $projectPages_Conf_Setup,$projectPages_db_version,$projectPages_version;
		$projectPages_Conf_Setup = array(

			// Define the key the config model will use to store the config in wp options
			'conf_key' => 'whppsettings', 

			// Define the version of config (update as this file updated - any string)
			'conf_ver' => 'v1.0//11.01.17', 

			// Define the plugin name, ver and db ver (meta data stored in option)
			'conf_plugin' => 'ProjectPages', 
			'conf_pluginver' => $projectPages_version, 		
			'conf_plugindbver' => $projectPages_db_version,

			// Added DMZ Config (this stores all dmz config settings)
			'conf_dmzkey' => 'pp_dmz',

			// Protected conf settings, these don't get flushed when restoring defaults
			// NOTE: They can still be edited via usual edit funcs
			'conf_protected' => array(
				'whlang',
				'customfields',
				'customisedfields',
				#'customviews'
			),

			// store's language labels, types, etc. which
			// allow auto-buildout of settings page
			'setting_index' => array(
			
				'permalink_root' =>  array(

						'type' 						=> 'text',
						'title' 					=> 'Permalink Root',
						'description' 		=> 'Choose the root permalink string',
						'category'				=> 'URLs',
						'eg'							=> '<strong>Default:</strong> <span class="pp-label">"projects"</span>'

				),

				'template_mode' =>  array(

						'type' 						=> 'select',
						'title' 					=> 'Template mode',
						'description' 		=> 'Choose which page templates Project Pages should use',
						'options'					=> array(

								'default'			=> 'Default (Theme template -> Plugin template)',
								'legacy'			=> 'Legacy PHP templates'

						),
						'eg'							=> '', // later in the stack we fill this with html relevant to the state of templates
						'category'				=> 'Templates'

				),



				'feat_share' =>  array(

						'type' 						=> 'select',
						'title' 					=> 'Enable Sharing Icons',
						'description' 		=> 'Display social sharing icons on Project Pages',
						'category'				=> 'Social Sharing',
						'options'					=> array(

								'true'				=> 'Enabled',
								'false'			=> 'Disabled'

						)

				),
				/*
				'fbappid' => array(

						'type' 						=> 'text',
						'title' 					=> 'Facebook App ID',
						'description' 		=> 'Enter your Facebook App ID for this domain to enhance social sharing via FB',
						'placeholder'			=> 'e.g. 547813598571889',
						'category'				=> 'Social Sharing'

				),
				'twvia' => array(

						'type' 						=> 'text',
						'title' 					=> 'X Handle',
						'description' 		=> 'Your Twitter/X handle (Tweet sharing will append this to the tweet when someone shares your Project Page)',
						'eg'							=> '<a href="https://twitter.com/woodyhayday" target="_blank">@woodyhayday</a>',
						'category'				=> 'Social Sharing'

				), */
				'share_fb' => array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Share via FB',
						'description' 		=> 'Show "Share via Facebook" on Project Pages',
						'category'				=> 'Social Sharing',
						'conditional'			=> array(

								'feat_share' 	=> true

						)

				),
				'share_x' => array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Share via X/Twitter',
						'description' 		=> 'Show "Share via X" on Project Pages',
						'category'				=> 'Social Sharing',
						'conditional'			=> array(

								'feat_share' 	=> true

						)

				),
				'share_li' => array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Share via LinkedIn',
						'description' 		=> 'Show "Share via LinkedIn" on Project Pages',
						'category'				=> 'Social Sharing',
						'conditional'			=> array(

								'feat_share' 	=> true

						)

				),
				'share_telegram' => array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Share via Telegram',
						'description' 		=> 'Show "Share via Telegram" on Project Pages',
						'category'				=> 'Social Sharing',
						'conditional'			=> array(

								'feat_share' 	=> true

						)

				),
				'share_x_template' => array(

						'type' 						=> 'text',
						'title' 					=> 'X Tweet template',
						'description' 		=> 'The template tweet you want to load when people share your Project. Use parameters like "{url}" or "{title}".',
						'eg'							=> 'Check this project by @yourhandle out! {url} (cc @projectpagesio)',
						'category'				=> 'Social Sharing',
						'conditional'			=> array(

								'feat_share' 	=> true

						)

				),
				'share_telegram_template' => array(

						'type' 						=> 'text',
						'title' 					=> 'Telegram message template',
						'description' 		=> 'The template message you want to load when people send your Project. Use parameters like "{url}" or "{title}".',
						'eg'							=> 'Check this project out! {url}',
						'category'				=> 'Social Sharing',
						'conditional'			=> array(

								'feat_share' 	=> true

						)

				),
				'add_og_meta' => array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Add OG Meta Tags',
						'description' 		=> 'Automatically add OG Meta Tags to Project Pages',
						'category'				=> 'Social Sharing'

				),
				'poweredby' => array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Share Love:',
						'description' 		=> 'Show small "Powered By" logo',
						'category'				=> 'Appearance'

				),
				'favicon' => array(

						'type' 						=> 'text',
						'title' 					=> 'Favicon URL',
						'description' 		=> '',
						'category'				=> 'Legacy Template Settings',
						'conditional'			=> array(

								'template_mode' 	=> 'legacy'

						)

				),
				'css_override' => array(

						'type' 						=> 'textarea',
						'title' 					=> 'Override CSS',
						'description' 		=> '',
						'category'				=> 'Legacy Template Settings',
						'conditional'			=> array(

								'template_mode' 	=> 'legacy'

						)

				),
				'menu_type' => array(

						'type' 						=> 'select',
						'title' 					=> 'Menu Type',
						'description' 		=> 'Choose what type of menu to display on your Project Pages',
						'options'					=> array(

								'none'				=> 'No Menu',
								'alltags'			=> 'All Tags',
								'sometags'		=> 'Some Tags',

						),
						'category'				=> 'Legacy Template Settings',
						'conditional'			=> array(

								'template_mode' 	=> 'legacy'

						)

				),
				'menu_tags' => array(

						'type' 						=> 'text',
						'title' 					=> 'Tags to Show',
						'description' 		=> 'Provide a comma seperated list of tags to show',
						'eg'							=> '1, 2, 3',
						'conditional'			=> array(

								'menu_type' 	=> 'sometags'

						),
						'category'				=> 'Legacy Template Settings',
						'conditional'			=> array(

								'template_mode' 	=> 'legacy'

						)

				),
				'display_type' => array(

						'type' 						=> 'select',
						'title' 					=> 'Project Pages Archive Type',
						'description' 		=> 'Choose how to display your Project Pages on the PP Archive page (usually "/projects")',
						'options'					=> array(

								'none'						=> 'All Projects',
								'currentarchive'	=> 'Current | Archive/Completed (Do not show "Ideas")',
								'currentonly'			=> 'Current (In Progress)',

						),
						'category'				=> 'Legacy Template Settings',
						'conditional'			=> array(

								'template_mode' 	=> 'legacy'

						)

				),
				'display_showbiline' =>  array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Show Bi-line on Project Cards',
						'description' 		=> 'Display the bi-line on single Project Pages',
						'category'				=> 'Appearance'

				),
				'display_showstatus' =>  array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Show Status on Project Cards',
						'description' 		=> 'Display the project status on single Project Pages',
						'category'				=> 'Appearance'

				),
				'thin_column' =>  array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Use Thin Content Columns',
						'description' 		=> '',
						'category'				=> 'Legacy Template Settings',
						'conditional'			=> array(

								'template_mode' 	=> 'legacy'

						)

				),
				'use_logs' =>  array(

						'type' 						=> 'checkbox',
						'title' 					=> 'Use Project Logs',
						'description' 		=> '',
						'category'				=> 'Modules'

				)

			)

		);

	// ================================================================================	


	// ================================================================================

		// Define default config model that will be loaded on every new init of settings
		// ... or when a user resets their settings

	// ================================================================================

		// Only declared here, then get's shuttled into $projectPages_Conf_Setup
		// ... left seperate for ease of reading 
		// global $projectPages_Conf_Def;
		$projectPages_Conf_Def = array( 

											'permalink_root' => 'projects',
											'feat_share' =>  true,
											'twvia' => '',
											'poweredby' => 1,
											'favicon' => '',
											'css_override' => '',
											'menu_type' => 'none',
											'menu_tags' => 'all',
											'display_type' => 'currentarchive',
											'display_showbiline' => "1",
											'display_showstatus' => "1",
											'thin_column' => 1,
											'use_logs' => 1,
											'template_mode' => 'default',
											'share_fb' => 1,
											'share_x' => 1,
											'share_li' => 1,
											'share_telegram' => 1,
											'share_x_template' => 'Check this project by @yourhandle out! {url} (cc @projectpagesio)',
											'share_telegram_template' => 'Check this project out! {url}',
											'add_og_meta' => 1,

											// ======= Migrations - this stores which have been run!
											'migrations' => array()


			);




	
	// Move defaults arr into main config
	$projectPages_Conf_Setup['conf_defaults'] = $projectPages_Conf_Def;


	// Mark as included :)
	define('PROJECTPAGES_INC_CONFINIT',true);