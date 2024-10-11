<?php
/*
Plugin Name: Project Pages
Plugin URI: https://projectpages.io
Description: Project Pages is the simplest way to share your projects beautifully.
Version: 2.0.4
Author: <a href="https://projectpages.io">Project Pages.io</a>
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 


// PHP Version check
if (version_compare(phpversion(), '5.4', '<')) {
    // php version isn't high enough
    echo '<div style="font-family: \'Open Sans\',sans-serif;">Project Pages Requires PHP Version 5.4 or above, please ask web hosting provider to update your PHP!</div>';
	exit();	

}

// Hooks

    // Install/uninstall
    register_activation_hook(__FILE__,'projectPages_install');
    register_deactivation_hook(__FILE__,'projectPages_uninstall');
    
    // general
	add_action('init', 			'projectPages_init');
    add_action('admin_menu', 	'projectPages_admin_menu'); 

// init vars
global 	$projectPages_db_version,$projectPages_version;
		$projectPages_db_version 			= "1.0";
		$projectPages_version 				= "2.0";

// Urls
global 	$projectPages_urls;
		$projectPages_urls['home'] 					= 'http://projectpages.io';
		$projectPages_urls['feedback']				= 'https://forms.gle/sAhUjTcNABN6ZgGLA'; // note this is also hardcoded in all blocks. change there if changed here.
		$projectPages_urls['subscribe']				= 'https://projectpages.io/join/';
		$projectPages_urls['get-pro']				= 'https://projectpages.io/pro/';
		$projectPages_urls['x']						= 'https://twitter.com/projectpagesio';
		$projectPages_urls['xwh']					= 'https://twitter.com/woodyhayday';
		$projectPages_urls['wh']					= 'https://woodyhayday.com';
		$projectPages_urls['join']					= 'https://projectpages.io/services/wp-join/wp-join.php';
		$projectPages_urls['review']				= 'https://wordpress.org/support/view/plugin-reviews/project-pages?filter=5#new-post';
		$projectPages_urls['kb-getting-started']  	= 'https://projectpages.io/docs/getting-started-with-project-pages/';
		$projectPages_urls['kb-templates']  		= 'https://projectpages.io/docs/templates/';
		$projectPages_urls['kb-permalink-root']  	= 'https://projectpages.io/docs/urls-permalink-root/';
		$projectPages_urls['kb-og-meta-generate']  	= 'https://projectpages.io/docs/generating-custom-sharing-images-og-meta-with-project-pages-pro/';
		$projectPages_urls['kb-imagemagick-req']  	= 'https://projectpages.io/docs/project-pages-pro-requires-imagemagick/';
		$projectPages_urls['kb-prompts']  			= 'https://projectpages.io/docs/using-prompts-for-better-project-updates-in-project-pages-pro/';
		$projectPages_urls['kb-not-block-based']  	= 'https://projectpages.io/docs/block-enabled-themes/';
		$projectPages_urls['publish-on-pp-io']		= 'https://projectpages.io/publish-to-projectpages/';

// Page slugs
global	$projectPages_slugs;
		$projectPages_slugs['home'] 		= "projectpages";
		$projectPages_slugs['settings'] 	= "projectpages-plugin-settings";

// defined
define( 'PROJECTPAGES_ROOTFILE', __FILE__ );
define( 'PROJECTPAGES_PATH', plugin_dir_path(__FILE__) );
define( 'PROJECTPAGES_ROOTDIR', basename(dirname(__FILE__)) ); // zero-bs-crm
define( 'PROJECTPAGES_URL', plugin_dir_url(__FILE__) );
define(	'PROJECTPAGES_ROOTPLUGIN', PROJECTPAGES_ROOTDIR.'/'.basename( PROJECTPAGES_ROOTFILE ) );
define( 'PROJECTPAGES_PLUGIN_TEMPLATE_PATH', PROJECTPAGES_PATH . 'templates');
define( 'PROJECTPAGES_THEME_TEMPLATE_PATH', get_stylesheet_directory().'/project-pages');

// Includes

// Pre-init
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.Pre.Init.php' );

// PRO VER
//require_once( PROJECTPAGES_PATH . 'pro/ProjectPages.PRO.php' );

// Init
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.Config.Init.php' );
require_once( PROJECTPAGES_PATH . 'includes/wh.config.lib.php' );
require_once( PROJECTPAGES_PATH . 'includes/wh.helpers.lib.php' );
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.DAL.php' );
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.MetaBoxes.php' );
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.AJAX.php' );
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.Blocks.php' );
// **EXPERIMENTAL** require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.BlockEditor.php' );
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.Templating.php' );
require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.OGMeta.php' );
# require_once( PROJECTPAGES_PATH . 'includes/ProjectPages.Announcements.php' );



// Init settings model (after init hook)
do_action( 'project_pages_pre_settings_hook' );
global $projectPages_Settings, $projectPages_Conf_Setup;
//echo '<pre>'.json_encode($projectPages_Conf_Setup).'</pre>'; exit();
if (!isset($projectPages_Settings)) $projectPages_Settings = new WH_WP_ConfigLib($projectPages_Conf_Setup);
// post settings hook
do_action( 'project_pages_post_settings_hook' );

#================== Init/Admin Enqueuing etc.

// Install function
function projectPages_install(){
	
	global $projectPages_version, $projectPages_db_version, $projectPages_Settings;

		// Initialising settings no happens via Settings Class
		// As of V1.0
		if (!is_array($projectPages_Settings->getAll())){
			
			add_action('admin_notices','projectPages__settingsfail');function projectPages__settingsfail(){echo '<div class="error"><p>Project Pages Plugin Could not create its options object!</p></div>';}
			
		} else {

			// check if theme is block-based
			projectPages_theme_check();

		}
}

// uninstall
function projectPages_uninstall(){

	// Skip deactivation feedback if it's a JSON/AJAX request or via WP-CLI
	if ( wp_is_json_request() || wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) || wp_is_xml_request() ) {
		return;
	}
        
    // got feedback? - currently not using this mark as fed-back feat.
    $has_given_feedback = get_option( 'has_given_projectpages_feedback' );

    // if php notice, (e.g. php ver to low, skip this)
    if ( $has_given_feedback == false && !defined('PROJECTPAGES_FEEDBACK_INPROG')){

        // Show feedback dialog + Deactivate
        // Define is to stop an infinite loop :)
        // (Won't get here the second time)
        define( 'PROJECTPAGES_FEEDBACK_INPROG', true );

        // ask for feedback
        try {

            // manually deactivate before exit
            deactivate_plugins( plugin_basename( PROJECTPAGES_ROOTFILE ) );

            // include feedback file
            require_once( PROJECTPAGES_PATH . 'pages/deactivation-feedback.php' ); exit();


        } catch (Exception $e){

            // Nada

        }

    }
	
}


// Install template(s)
// Switched this to an "optional override" like woocommerce :)
function projectPages_installProjectPageTemplates($quiet=false) {


	// Add dir if needed (copy perms as per wp theme protocol.)
	if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH)) mkdir(PROJECTPAGES_THEME_TEMPLATE_PATH,0707);

	// copy single template
	if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php')){
		copy(PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.php', PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php' );
		// Check
		if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php')){
			if (!$quiet) projectPages_html_msg(1,'Failed to install template into your theme directory! (single-projectpage.php)');
		} else {
			if (!$quiet) projectPages_html_msg(0,'Template successfully installed into your theme directory! (single-projectpage.php)');
		}
	} else {
		if (!$quiet) projectPages_html_msg(1,'Template already exists in your theme directory! (single-projectpage.php)');
	}


	// copy archive template
	if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php')){
		copy(PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.php', PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php' );
		// Check
		if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php')){
			if (!$quiet) projectPages_html_msg(1,'Failed to install template into your theme directory! (archive-projectpage.php)');
		} else {
			if (!$quiet) projectPages_html_msg(0,'Template successfully installed into your theme directory! (archive-projectpage.php)');
		}
	} else {
		if (!$quiet) projectPages_html_msg(1,'Template already exists in your theme directory! (archive-projectpage.php)');
	}


	// copy taxonomy template
	if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php')){
		copy(PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpage_DEFAULT.php', PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php' );
		// Check
		if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php')){
			if (!$quiet) projectPages_html_msg(1,'Failed to install template into your theme directory! (taxonomy-projectpagetag.php)');
		} else {
			if (!$quiet) projectPages_html_msg(0,'Template successfully installed into your theme directory! (taxonomy-projectpagetag.php)');
		}
	} else {
		if (!$quiet) projectPages_html_msg(1,'Template already exists in your theme directory! (taxonomy-projectpagetag.php)');
	}



	// copy taxonomy template
	if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/projectpages-footer.php')){
		copy(PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/projectpages-footer.php', PROJECTPAGES_THEME_TEMPLATE_PATH . '/projectpages-footer.php' );
		// Check
		if (!file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/projectpages-footer.php')){
			if (!$quiet) projectPages_html_msg(1,'Failed to install template into your theme directory! (projectpages-footer.php)');
		} else {
			if (!$quiet) projectPages_html_msg(0,'Template successfully installed into your theme directory! (projectpages-footer.php)');
		}
	} else {
		if (!$quiet) projectPages_html_msg(1,'Template already exists in your theme directory! (projectpages-footer.php)');
	}
	

}

function projectPages_renameProjectPageTemplates(){

	// Brutal.

		// rename single template
		if (file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php')){
			rename(PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php',PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage_replaced-'.time().'.php' );
		}

		// rename archive template
		if (file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php')){
			rename(PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php',PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage_replaced-'.time().'.php' );
		}


		// copy taxonomy template
		if (file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php')){
			rename(PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php',PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag_replaced-'.time().'.php' );
		}

		// copy taxonomy template
		if (file_exists(PROJECTPAGES_THEME_TEMPLATE_PATH . '/projectpages-footer.php')){
			rename(PROJECTPAGES_THEME_TEMPLATE_PATH . '/projectpages-footer.php',PROJECTPAGES_THEME_TEMPLATE_PATH . '/projectpages-footer_replaced-'.time().'.php' );
		}

}

// Props 2 http://wordpress.stackexchange.com/questions/2553/how-to-register-sidebar-without-messing-up-the-order
function projectPages_self_deprecating_sidebar_registration(){
  
	  if ( function_exists('register_sidebar') ){

		  register_sidebar(array(
		  	'id' => 'whpp-mid',
		    'name' => 'Project Pages Footer Mid',
		    'before_widget' => '<div class="three wide column"><div class="ppFooterWidgetWrap">',
		    'after_widget' => '</div></div>',
		    'before_title' => '<h4 class="ui inverted header">',
		    'after_title' => '</h4>',
		  )
		);

		  register_sidebar(array(
		  	'id' => 'whpp-right',
		    'name' => 'Project Pages Footer Right',
		    'before_widget' => '<div class="ppFooterWidgetWrap">',
		    'after_widget' => '</div>',
		    'before_title' => '<h4 class="ui inverted header">',
		    'after_title' => '</h4>',
		  )
		);

	}


}

add_action( 'wp_loaded', 'projectPages_self_deprecating_sidebar_registration' );


// Initialisation - enqueueing scripts/styles
function projectPages_init(){
  
	global $projectPages_slugs, $projectPages_Settings; #req

	// Retrieve settings
	$settings = $projectPages_Settings->getAll();

	// setup post types
	projectPages_setupPostTypes();

	// Load Lang	
  	load_plugin_textdomain( 'projectpages', false, dirname( plugin_basename( __FILE__ ) ) . '/translations' ); 
			
	// Admin & Public
	wp_enqueue_script("jquery");
	
	// Admin only	
	if (is_admin()){

		global $pagenow;

			// Hmmm not that efficient.
			$postTypeStr = ''; if (isset($_GET['post'])) $postTypeStr = get_post_type((int)$_GET['post']);

			if (
				(isset($_GET['post_type']) && $_GET['post_type'] == 'projectpage' && $pagenow !== 'edit.php' && $pagenow !== 'edit-tags.php') || 
				(!empty($postTypeStr) && $postTypeStr == 'projectpage')
				) {
				
				projectPages_enqueue_editor_page();

			}

			// gutenberg stuff
			if ( $pagenow == 'site-editor.php' ){

				projectPages_gutenberg_init();

			}



			// theme issue outstanding
			if ( get_user_meta(get_current_user_id(), 'pp_theme_issue', true)) {

			 	add_action('admin_notices','project_pages_theme_fail');

			 }

			 // theme opportunity outstanding
			if ( get_user_meta(get_current_user_id(), 'pp_theme_opportunity', true)) {

			 	add_action('admin_notices','project_pages_theme_opportunity');

			 }

	}

	// blocks	
	projectPages_init_blocks();

	// **EXPERIMENTAL**
	// Gutenberg editing (WIP)
	// setup block editor template (wp 6.1+) 
	// projectPages_register_block_template();
	
}

// check rewrite rules
function projectPages_rewrite_rules_check()
{
    $rules = get_option( 'rewrite_rules' );

    if ( ! isset( $rules['projects/?$'] ) ) {     	
        
        projectPages_rewrite_rules_flush();
        
    }		
}
add_action( 'wp_loaded','projectPages_rewrite_rules_check' );

// flush rewrite rules
function projectPages_rewrite_rules_flush()
{
   	global $wp_rewrite; $wp_rewrite->flush_rules();
    	
}

// redirect to homepage on activation
function projectPages_activated_plugin( $filename ) {

    // Skip the re-direction if it's a JSON/AJAX request or via WP-CLI
    if ( wp_is_json_request() || wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) || wp_is_xml_request() ) {
        return;
    }

    if ( $filename == PROJECTPAGES_ROOTPLUGIN ) {
		
		// send user to our home page        
        if ( wp_redirect( admin_url( 'options.php?page=projectpages' ) ) ) {
			exit;
		}
	}
}
add_action( 'activated_plugin', 'projectPages_activated_plugin' );

// Add le admin menu
function projectPages_admin_menu() {

	global $projectPages_slugs; 	

	// Welcome
	// as sub-item as hidden page, we then use projectPages_admin_header + ProjectPages.global.wp-admin.js to rewrite the url href on top level menu item to this
	$project_pages_hidden_menu_item = add_submenu_page( 'options.php', 'Project Pages', 'Welcome', 'edit_published_pages', $projectPages_slugs['home'], 'projectPages_pages_home', 1 );	
	add_action( "admin_print_styles-{$project_pages_hidden_menu_item}", 'projectPages_enqueue_home_page' );

	// settings
    $project_pages_settings_menu_item = add_submenu_page( 'edit.php?post_type=projectpage', 'Project Pages', 'Settings', 'manage_options', $projectPages_slugs['settings'], 'projectPages_pages_settings' );
	add_action( "admin_print_styles-{$project_pages_settings_menu_item}", 'projectPages_enqueue_settings_page' );

}

// put out any logic we need to admin header (menu rewrite url for js)
function projectPages_admin_header() {
	
	// edit.php?post_type=projectpage&page=projectpages
	?><script>

		// Tweak menu :/
		var projectPages_home_url = '<?php echo admin_url( 'options.php?page=projectpages' ); ?>';
		jQuery(function(){

			jQuery('li.menu-top .menu-icon-projectpage').attr( 'href', window.projectPages_home_url );

		});
		</script>

		<?php // gutenberg additions
		projectPages_gutenberg_scripts( false );

		// PRO hook
		do_action( 'project_pages_admin_header' );

}
add_action( 'admin_head', 'projectPages_admin_header' );

// home page css/js
function projectPages_enqueue_home_page() {

    wp_enqueue_style('pp_bootstrap', PROJECTPAGES_URL . 'css/bootstrap/bootstrap.min.css' );
    wp_enqueue_style('style', get_stylesheet_uri());
    wp_enqueue_script( 'bootstrap-js', PROJECTPAGES_URL . 'js/libs/bootstrap/bootstrap.bundle.min.js', array('jquery'), '3.3.4', true );

    wp_enqueue_style('pp_home', PROJECTPAGES_URL . 'css/ProjectPages.Home.min.css' );
	wp_enqueue_script( 'pp_home_js', PROJECTPAGES_URL . 'js/ProjectPages.Home.min.js' );
}

// settings page css/js
function projectPages_enqueue_settings_page() {

    wp_enqueue_style('pp_bootstrap', PROJECTPAGES_URL . 'css/bootstrap/bootstrap.min.css' );
    wp_enqueue_style('style', get_stylesheet_uri());
    wp_enqueue_script( 'bootstrap-js', PROJECTPAGES_URL . 'js/libs/bootstrap/bootstrap.bundle.min.js', array('jquery'), '3.3.4', true );

    // vue app (settings page)
    wp_enqueue_style('pp_settings_app', PROJECTPAGES_URL . 'css/ProjectPages.Settings.min.css' );
    wp_enqueue_script( 'pp_settings_app_js', PROJECTPAGES_URL . 'js/ProjectPages.Settings.min.js' );
    wp_enqueue_script( 'pp_settings_app_add_js', PROJECTPAGES_URL . 'js/ProjectPages.Settings.Additions.min.js' );
}

// editor page css/js
function projectPages_enqueue_editor_page() {

    wp_enqueue_style( 'pp_bootstrap', PROJECTPAGES_URL . 'css/bootstrap/bootstrap.min.css' );
    wp_enqueue_script( 'bootstrap-js', PROJECTPAGES_URL . 'js/libs/bootstrap/bootstrap.bundle.min.js', array('jquery'), '3.3.4', true );
    wp_enqueue_script( 'bootstrap-datepicker-js', PROJECTPAGES_URL . 'js/libs/bootstrap-datepicker.min.js', array('jquery') );

	wp_enqueue_style( 'projectpagesadmcss', 	plugins_url('/css/ProjectPages.Admin.min.css',__FILE__) );
	wp_enqueue_script( 'pp_editor_js', PROJECTPAGES_URL . 'js/ProjectPages.Admin.min.js' );
	//wp_enqueue_script( 'pp_editor_bs_select_js', PROJECTPAGES_URL . 'js/libs/bootstrap-select.js', array('jquery','bootstrap-js') );
	//wp_enqueue_style( 'pp_editor_bs_select_css', 	plugins_url('/css/bootstrap-select.min.css',__FILE__) );
	wp_enqueue_style( 'projectpagesspectrumcss', 	plugins_url('/css/libs/spectrum.css',__FILE__) );
	wp_enqueue_script( 'projectpagesspectrumjs', 	plugins_url('/js/libs/spectrum.js',__FILE__) );

	// dashicons picker
	wp_enqueue_style( 'dashicons-picker', plugin_dir_url( __FILE__ ) . 'css/libs/dashicons-picker.css', array( 'dashicons' ), '1.0' );
	wp_enqueue_script( 'dashicons-picker', plugin_dir_url( __FILE__ ) . 'js/libs/dashicons-picker.mod.js', array( 'jquery' ), '1.0' ); // modified version, added change monitoring

}

// front-end enqueuement
function projectPages_enqueue_project_pages_frontend(){

	if ( is_singular( 'projectpage' ) || is_post_type_archive( 'projectpage' ) || is_tax( 'projectpagetag' ) ) {

		// shared with some blocks
		projectPages_enqueue_frontend();

		// PRO hook
		do_action( 'project_pages_frontend_enqueuement' );

	}

} add_action( 'wp_enqueue_scripts', 'projectPages_enqueue_project_pages_frontend' );

// frontend styles
function projectPages_enqueue_frontend(){

	// wrecks stuff wp_enqueue_style( 'pp_bootstrap', PROJECTPAGES_URL . 'css/bootstrap/bootstrap.min.css' );
	wp_enqueue_style( 'pp_bootstrap', PROJECTPAGES_URL . 'css/bootstrap/bootstrap-grid.min.css' );
	wp_enqueue_style( 'project-pages-frontend', plugin_dir_url( __FILE__ ) . 'css/ProjectPages.Frontend.min.css' );

}


// the vue settings app code needs type=module.
add_filter('script_loader_tag', function($tag, $handle, $src) {
   if ( 'pp_settings_app_js' !== $handle ) {
      return $tag;
   }
   // change the script tag by adding type="module" and return it.
   $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
   return $tag;
} , 10, 3);

#================== / Init/Admin Enqueuing etc.



#================== Custom Post Types
function projectPages_setupPostTypes() {

	global $projectPagesTagsPermaRoot;

	$labels = array(
		'name'                       => _x( 'Project Tags', 'Project Tags', 'projectpages' ),
		'singular_name'              => _x( 'Project Tag', 'Project Tag', 'projectpages' ),
		'menu_name'                  => __( 'Project Tags', 'projectpages' ),
		'all_items'                  => __( 'All Tags', 'projectpages' ),
		'parent_item'                => __( 'Parent Tag', 'projectpages' ),
		'parent_item_colon'          => __( 'Parent Tag:', 'projectpages' ),
		'new_item_name'              => __( 'New Tag Name', 'projectpages' ),
		'add_new_item'               => __( 'Add Tag Item', 'projectpages' ),
		'edit_item'                  => __( 'Edit Tag', 'projectpages' ),
		'update_item'                => __( 'Tag Item', 'projectpages' ),
		'view_item'                  => __( 'View Tag', 'projectpages' ),
		'separate_items_with_commas' => __( 'Separate Tags with commas', 'projectpages' ),
		'add_or_remove_items'        => __( 'Add or remove Tags', 'projectpages' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'projectpages' ),
		'popular_items'              => __( 'Popular Tags', 'projectpages' ),
		'search_items'               => __( 'Search Tags', 'projectpages' ),
		'not_found'                  => __( 'Not Found', 'projectpages' ),
		'no_terms'                   => __( 'No Tags', 'projectpages' ),
		'items_list'                 => __( 'Tags list', 'projectpages' ),
		'items_list_navigation'      => __( 'Tags list navigation', 'projectpages' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
	    'capabilities' => array(
	      'manage_terms'=> 'manage_categories',
	      'edit_terms'=> 'manage_categories',
	      'delete_terms'=> 'manage_categories',
	      'assign_terms' => 'read'
	    ),
		'rewrite' 			  => array(

			'slug' => projectPages_permalink_root() . '/' . $projectPagesTagsPermaRoot,
			'with_front' => false

		)
	);
	register_taxonomy( 'projectpagetag', null, $args ); # set as null here, assigned below: array( 'projectpage' )

	$labels = array(
		'name'                => _x( 'Project Pages', 'Project Pages', 'projectpages' ),
		'singular_name'       => _x( 'Project Page', 'Project Page', 'projectpages' ),
		'menu_name'           => __( 'Project Pages', 'projectpages' ),
		'name_admin_bar'      => __( 'Project Pages', 'projectpages' ),
		'parent_item_colon'   => __( 'Parent Project Page:', 'projectpages' ),
		'all_items'           => __( 'All Project Pages', 'projectpages' ),
		'add_new_item'        => __( 'Add New Project Page', 'projectpages' ),
		'add_new'             => __( 'Add New', 'projectpages' ),
		'new_item'            => __( 'New Project Page', 'projectpages' ),
		'edit_item'           => __( 'Edit Project Page', 'projectpages' ),
		'update_item'         => __( 'Update Project Page', 'projectpages' ),
		'view_item'           => __( 'View Project Page', 'projectpages' ),
		'search_items'        => __( 'Search Project Pages', 'projectpages' ),
		'not_found'           => __( 'No Project Pages Found', 'projectpages' ),
		'not_found_in_trash'  => __( 'Project Page Not found in Trash', 'projectpages' ),
	);
	$args = array(
		'label'               => __( 'Project Page', 'projectpages' ),
		'description'         => __( 'Project Page', 'projectpages' ),
		'labels'              => $labels,
		'supports'            => array( 
			'title', 
			'thumbnail', 
			'taxonomies', 
			'custom-fields'

		),
		'hierarchical'        => false,
		'public'              => false, 
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-schedule',#plugins_url('/i/icon.png', __FILE__),//'dashicons-tickets',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => false,
		'has_archive'         => projectPages_permalink_root(),
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'taxonomies' 			=> array('projectpagetag'),
		'rewrite' 			  => array(

			'slug' => projectPages_permalink_root(),
			'with_front' => false

		)
	);

	// **EXPERIMENTAL**
	// Gutenberg editing (WIP)
	//$args['supports'][] = 'editor';
	//$args['show_in_rest'] = true;

	register_post_type( 'projectpage', $args );


		$labels = array(
			'name'                  => _x( 'Project Logs', 'Project Logs', 'zerobscrm' ),
			'singular_name'         => _x( 'Project Log', 'Project Log', 'zerobscrm' ),
			'menu_name'             => __( 'Project Logs', 'zerobscrm' ),
			'name_admin_bar'        => __( 'Project Log', 'zerobscrm' ),
			'archives'              => __( 'Project Log Archives', 'zerobscrm' ),
			'parent_item_colon'     => __( 'Project Log:', 'zerobscrm' ),
			'parent'    			 => __( 'Project Log', 'zerobscrm' ),
			'all_items'             => __( 'All Project Logs', 'zerobscrm' ),
			'add_new_item'          => __( 'Add New Project Log', 'zerobscrm' ),
			'add_new'               => __( 'Add New', 'zerobscrm' ),
			'new_item'              => __( 'New Project Log', 'zerobscrm' ),
			'edit_item'             => __( 'Edit Project Log', 'zerobscrm' ),
			'update_item'           => __( 'Update Project Log', 'zerobscrm' ),
			'view_item'             => __( 'View Project Log', 'zerobscrm' ),
			'search_items'          => __( 'Search Project Log', 'zerobscrm' ),
			'not_found'             => __( 'Not found', 'zerobscrm' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'zerobscrm' ),
			'featured_image'        => __( 'Project Log Image', 'zerobscrm' ),
			'set_featured_image'    => __( 'Set Project Log image', 'zerobscrm' ),
			'remove_featured_image' => __( 'Remove Project Log image', 'zerobscrm' ),
			'use_featured_image'    => __( 'Use as Project Log image', 'zerobscrm' ),
			'insert_into_item'      => __( 'Insert into Project Log', 'zerobscrm' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Project Log', 'zerobscrm' ),
			'items_list'            => __( 'Project Logs list', 'zerobscrm' ),
			'items_list_navigation' => __( 'Project Logs list navigation', 'zerobscrm' ),
			'filter_items_list'     => __( 'Filter Project Logs list', 'zerobscrm' ),
		);
		$args = array(
			'label'                 => __( 'Project Log', 'zerobscrm' ),
			'description'           => __( 'Project Page Log', 'zerobscrm' ),
			'labels'                => $labels,
			'supports'              => array(  'thumbnail', 'taxonomies'), #, 'page-attributes'
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false, #zeroBSCRM_getSetting('companylevelcustomers'), // Will be true if b2b on
			'public'                => true,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'menu_position'         => "5.1",
			'menu_icon'             => 'dashicons-admin-users',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => false,		
			'exclude_from_search'   => true, #false, # Exclude from front end
			'publicly_queryable'    => false, #true, , # Exclude from front end
			'capability_type'       => 'post',
			'taxonomies' 			=> array()
		);
		register_post_type( 'projectpagelog', $args );
	
}

#================== / Custom Post Types





#================== Templating

/* this is another way around it....
https://stackoverflow.com/questions/72171038/use-gutenberg-block-in-template-with-template-include

 function pp_manage_block_templates( $query_result, $query, $template_type ) {

 	// if template exists, mute this
    if ( !file_exists(get_stylesheet_directory().'/templates/single-projectpage.html') ){ 

    	// if overriding, don't bother
    	$use_override_php = projectPages_getSetting('enable_overwrite_templates');

    	if ( $use_override_php !== "1" ){

		    $theme = wp_get_theme();

		    $template_contents = file_get_contents( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.html' );
		    //$template_contents = str_replace( '~theme~', $theme->stylesheet, $template_contents );

		    $new_block                 = new WP_Block_Template();
		    $new_block->type           = 'wp_template';
		    $new_block->theme          = $theme->stylesheet;
		    $new_block->slug           = 'single-projectpage';
		    $new_block->id             = $theme->stylesheet . '//single-projectpage';
		    $new_block->title          = 'single-projectpage';
		    $new_block->description    = '';
		    $new_block->source         = 'custom';
		    $new_block->status         = 'publish';
		    $new_block->has_theme_file = true;
		    $new_block->is_custom      = true;
		    $new_block->content        = $template_contents;

		    $query_result[] = $new_block;

		}

	}

    return $query_result;
}
//add_filter( 'get_block_templates', 'pP_manage_block_templates', 10, 3 );
*/

# Thank you: http://wordpress.stackexchange.com/questions/17385/custom-post-type-templates-from-plugin-folder
// Note: Changes here need to be reflected in settings function `projectPages_settings_explainers_extend()`
function projectPages_singleProjectTemplate( $single ) {

    global $post;

    // First check for a post_type match: 
    if ( $post->post_type == "projectpage" ){

    	// #legacy template mode - use php variants
    	if ( projectPages_getSetting('template_mode') == "legacy" ){

	    	$legacy_template = projectPages_discern_single_template_legacy();

	    	if ( $legacy_template ) return $legacy_template;

	    }

	    // .... else, default: html gutenberg templates
    	
    	// If user already has a templated file, use that
        if ( file_exists( get_stylesheet_directory() . '/templates/single-projectpage.html' ) ) return $single;

    	// ... if no override, fallback to our file :)
        if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.html' ) ){

        	// ... as at 2/5/24 there is no legit way to hook in templates into gutenberg via plugin,
        	// ... so this is a workaround, and it's ugly.
        	global $_wp_current_template_content;
        	$_wp_current_template_content = file_get_contents( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.html' );

        }


    }

    // else return to single
    return $single;
}
add_filter('single_template', 'projectPages_singleProjectTemplate', 99);

// discerns which single #legacy template to use
function projectPages_discern_single_template_legacy(){

	// If match, then first check users theme/project-pages/* to see if they've made an override
    if ( file_exists( PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php' ) ) return PROJECTPAGES_THEME_TEMPLATE_PATH . '/single-projectpage.php';

	// ... if no override, fallback to our file :)
    if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.php' ) ) return PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.php';

    return false;

}

// Note: Changes here need to be reflected in settings function `projectPages_settings_explainers_extend()`
function projectPages_archiveProjectTemplate( $archive_template ) {

    global $post;

    // First check for a post_type match: 
    if ( 
    	// if posts
    	( $post && $post->post_type == "projectpage" ) 
    	||
    	// if empty
    	( wh_get_queried_post_type() == 'projectpage' )
    ){

    	// #legacy template mode - use php variants
    	if ( projectPages_getSetting('template_mode') == "legacy" ){
	  
	    	$legacy_template = projectPages_discern_archive_template_legacy();

	    	if ( $legacy_template ) return $legacy_template;

	    }

	    // .... else, default: html gutenberg templates

    	// If match, then first check users theme/project-pages/* to see if they've made an override
        if ( file_exists( get_stylesheet_directory() . '/templates/archive-projectpage.html' ) ) return $archive_template;

    	// ... if no override, fallback to our file :)
        if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.html' ) ){

        	// ... as at 2/5/24 there is no legit way to hook in templates into gutenberg via plugin,
        	// ... so this is a workaround, and it's ugly.
        	global $_wp_current_template_content;
        	$_wp_current_template_content = file_get_contents( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.html' );
        	
        }
    }

    // else return passed var
    return $archive_template;

}
add_filter('archive_template', 'projectPages_archiveProjectTemplate', 99);

// discerns which archive #legacy template to use
function projectPages_discern_archive_template_legacy(){

	// If match, then first check users theme/project-pages/* to see if they've made an override
	if ( file_exists( PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php' ) ) return PROJECTPAGES_THEME_TEMPLATE_PATH . '/archive-projectpage.php';

	// ... if no override, fallback to our file :)
	if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.php' ) ) return PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.php';

    return false;

}


// THIS NEEDS a higher priority than archive_template :) else that kicks in
// Note: Changes here need to be reflected in settings function `projectPages_settings_explainers_extend()`
function projectPages_taxonomyProjectTemplate( $taxonomy_template ) {
    
   	global $post;

    // Get the current term object. We will use get_queried_object
    $current_term = get_queried_object();

	// If the current term does not belong to advert post type, bail
	// could also use if ( get_query_var('taxonomy') == "projectpagetag" )
    if ( $current_term->taxonomy !== 'projectpagetag' ) return $taxonomy_template;

	// #legacy template mode - use php variants
	if ( projectPages_getSetting('template_mode') == "legacy" ){ 
	  
    	$legacy_template = projectPages_discern_taxonomy_template_legacy();

    	if ( $legacy_template ) return $legacy_template;

	}
	
	// .... else, default: html gutenberg templates

	// If match, then first check users theme/project-pages/* to see if they've made an override
    if ( file_exists( get_stylesheet_directory() . '/templates/taxonomy-projectpagetag.html' ) ) return $taxonomy_template;

		// ... if no override, fallback to our file :)
	if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpagetag_DEFAULT.html' ) ){

		// ... as at 2/5/24 there is no legit way to hook in templates into gutenberg via plugin,
		// ... so this is a workaround, and it's ugly.
		global $_wp_current_template_content;
		$_wp_current_template_content = file_get_contents( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpagetag_DEFAULT.html' );

	}

    // else return passed var
    return $taxonomy_template;

}
add_filter('taxonomy_template', 'projectPages_taxonomyProjectTemplate', 99);

// discerns which taxonomy #legacy template to use
function projectPages_discern_taxonomy_template_legacy(){

	// If match, then first check users theme/project-pages/* to see if they've made an override
    if ( file_exists( PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php' ) ) return PROJECTPAGES_THEME_TEMPLATE_PATH . '/taxonomy-projectpagetag.php';

	// ... if no override, fallback to our file :)
    if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpage_DEFAULT.php' ) ) return PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpage_DEFAULT.php';

    return false;

}

// Had to write this to do teh above for get_template_part
// ... as no filter to achieve this http://wordpress.stackexchange.com/questions/153004/is-it-possible-to-override-the-result-of-get-template-part
// #legay templating uses this.
function projectPages_get_template_part( $templateName='' ){

	// Check for template in our stores:

    	// First check users theme/project-pages/* to see if they've made an override
        if ( file_exists( PROJECTPAGES_THEME_TEMPLATE_PATH . '/'.$templateName.'.php')) { require(PROJECTPAGES_THEME_TEMPLATE_PATH . '/'.$templateName.'.php' ); return; }

    	// ... if no override, fallback to our file :)
        if ( file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/'.$templateName.'.php')) { require(PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/'.$templateName.'.php' ); return; }

    // No luck? then fallback to template part:
    get_template_part($templateName);
    
    return;

}


#================== / Templating




#================== Admin Pages

// Admin Page header
function projectPages_pages_header($subpage=''){

	global $wpdb, $projectPages_urls, $projectPages_version,$projectPages_Settings;	
	
	if (!current_user_can('edit_published_pages'))  { wp_die( __('You do not have sufficient permissions to access this page.','projectpages') ); }
    
    
?>
<div id="sgpBody">
    <div class="wrap"> 
	    <div id="icon-sb" class="icon32"><br /></div><h2>Project Pages<?php if (!empty($subpage)) echo ': '.$subpage; ?></h2> 
    </div>
    <div id="sgpHeader">
		<a href="<?php echo $projectPages_urls['home']; ?>" title="Project Pages" target="_blank">Project Pages</a> | 
	
        Version <?php echo $projectPages_version; ?>        
    </div>
    <div id="ProjectPagesAdminPage">
    <?php 	
	
	// Check for required upgrade
	#projectPages_checkForUpgrade();
	
}


// Admin Page footer
function projectPages_pages_footer(){
    
	?></div><?php 	
	
}



// welcome page
function projectPages_pages_home() {
	
	if ( !current_user_can( 'edit_published_pages' ) )  { wp_die( __( 'You do not have sufficient permissions to access this page.', 'projectpages' ) ); }

	if ( !function_exists( 'projectPages_page_welcome' ) ) require_once( PROJECTPAGES_PATH . 'pages/welcome.php' );
	
	// welcome
	projectPages_page_welcome();
	
}

// settings page
function projectPages_pages_settings() {
	
	if (!current_user_can('manage_options'))  { wp_die( __('You do not have sufficient permissions to access this page.','projectpages') ); }

	if(!function_exists('projectPages_page_settings' ) ) require_once( PROJECTPAGES_PATH . 'pages/settings.php' );


	// check if theme is block-based
	projectPages_theme_check();
	
	// Settings
	projectPages_page_settings();
	
}

// Main Config page
function projectPages_pages_settings_old() {
	
	global $wpdb, $projectPages_urls, $projectPages_version;	
	
	if (!current_user_can('manage_options'))  { wp_die( __('You do not have sufficient permissions to access this page.','projectpages') ); }
    
	// Header
    projectPages_pages_header('Settings');

	if(!function_exists('projectPages_page_settings' ) ) require_once( PROJECTPAGES_PATH . 'pages/settings.php' );
	
	// Settings
	projectPages_page_settings_old();
	
	// Footer
	projectPages_pages_footer();

?>
</div>
<?php 
}

#================== / Admin Pages



/* ======================================================
   Edit Post Messages (i.e. "Post Updated => Project Updated")
   ====================================================== */

add_filter( 'post_updated_messages', 'projectPages_post_updated_messages' );
function projectPages_post_updated_messages( $messages ) {

  $post             = get_post();
  $post_type        = get_post_type( $post );
  $post_type_object = get_post_type_object( $post_type );
  
  $messages['projectpage'] = array(
    0  => '', // Unused. Messages start at index 1.
    1  => sprintf( __( 'Project updated. <a href="%s" target="_blank">View Project</a>', 'projectpages' ), get_the_permalink( $post->ID ) ),
    2  => __( 'Custom field updated.', 'projectpages' ),
    3  => __( 'Custom field deleted.', 'projectpages' ),
    4  => __( 'Project updated.', 'projectpages' ),
    /* translators: %s: date and time of the revision */
    5  => isset( $_GET['revision'] ) ? sprintf( __( 'Project restored to revision from %s', 'projectpages' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6  => sprintf( __( 'Project saved. <a href="%s" target="_blank">View Project</a>', 'projectpages' ), get_the_permalink( $post->ID ) ),
    7  => sprintf( __( 'Project saved. <a href="%s" target="_blank">View Project</a>', 'projectpages' ), get_the_permalink( $post->ID ) ),
    8  => __( 'Project submitted.', 'projectpages' ),
    9  => sprintf(
      __( 'Project scheduled for: <strong>%1$s</strong>.' ),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
    ),
    10 => __( 'Project updated.' )
  );
      
  return $messages;

}

/* ======================================================
   / Edit Post Messages (i.e. "Post Updated => Project Updated")
   ====================================================== */

#================== Output HTML funcs

function projectPages_shareOut( $title='', $url='', $imgurl='', $return = false, $squelch_links = false, $taxonomy_archive_page = false ){

	// Output if setting?
	$shareMode = projectPages_getSetting('feat_share');

	if ( $shareMode == "true" ){

		// Retrieve fb app id + tw id
		// old $fbAppID = projectPages_getSetting('fbappid');
		// old $twVia = projectPages_getSetting('twvia');

		$share_fb = projectPages_getSetting('share_fb');
		$share_x = projectPages_getSetting('share_x');
		$share_li = projectPages_getSetting('share_li');
		$share_telegram = projectPages_getSetting('share_telegram');

		// use templates for single pages
		if ( !$taxonomy_archive_page ) {

			$x_template = projectPages_getSetting('share_x_template');
			$telegram_template = projectPages_getSetting('share_telegram_template');

		} else {

			$x_template = '{url} (cc @projectpagesio)';
			$telegram_template = '{url}';
			
		}

		// Only output if one or the other works :)
		if ( $share_fb || $share_x || $share_li || $share_telegram ){


			$html = '<div class="project-pages-share-wrap" data-sharable="true" data-ppurl="' . $url . '" data-ppdesc="' . $title . '" data-ppimg="' . $imgurl . '">'
				  . '<span class="project-pages-share-label">' . __( 'Share:', 'projectpages' ) . '</span> <span class="project-pages-share-icons">';			

			if ( $share_fb ){

				$html .= '<a href="' . wh_share_via_fb( $url ) . '" ';
				if ( $squelch_links ){
					$html .= 'onclick="return false;"';
				} else {
					$html .= 'target="_blank"';
				}
				$html .= ' title="' . sprintf ( __( 'Share %s on Facebook', 'projectpages' ), esc_attr( $title ) ) . '"><img src="' . PROJECTPAGES_URL . 'i/social-fb.png' . '" /></a>';

			}

			if ( $share_x ){

				// default tweet
				$tweet = $url;
				if ( isset( $x_template ) && !empty( $x_template ) ){

					$tweet = str_replace( '{title}', $title, str_replace( '{url}', $url, $x_template ) );

				}

				$html .= '<a href="' . wh_share_via_x( $tweet ) . '" ';
				if ( $squelch_links ){
					$html .= 'onclick="return false;"';
				} else {
					$html .= 'target="_blank"';
				}
				$html .= ' title="' . sprintf ( __( 'Share %s on X/Twitter', 'projectpages' ), esc_attr( $title ) ) . '"><img src="' . PROJECTPAGES_URL . 'i/social-x.png' . '" /></a>';

			}

			if ( $share_li ){

				$html .= '<a href="' . wh_share_via_li( $url ) . '" ';
				if ( $squelch_links ){
					$html .= 'onclick="return false;"';
				} else {
					$html .= 'target="_blank"';
				}
				$html .= ' title="' . sprintf ( __( 'Share %s on LinkedIn', 'projectpages' ), esc_attr( $title ) ) . '"><img src="' . PROJECTPAGES_URL . 'i/social-li.png' . '" /></a>';

			}

			if ( $share_telegram ){

				// default message
				$message = '';
				if ( isset( $telegram_template ) && !empty( $telegram_template ) ){

					$message = str_replace( '{title}', $title, str_replace( '{url}', $url, $telegram_template ) );

				}

				$html .= '<a href="' . wh_share_via_telegram( $url, $message ) . '" ';
				if ( $squelch_links ){
					$html .= 'onclick="return false;"';
				} else {
					$html .= 'target="_blank"';
				}
				$html .= ' title="' . sprintf ( __( 'Share %s via Telegram', 'projectpages' ), esc_attr( $title ) ) . '"><img src="' . PROJECTPAGES_URL . 'i/social-telegram.png' . '" /></a>';

			}


			$html .= '</span></div>';

			if ( $return ) return $html;

			echo $html;

		}


	}


}

#================== / Output HTML funcs


#================== Useful Functions

function projectPages_ifV($v){
	if (isset($v)) echo $v; 
}


// Outputs HTML message
function projectPages_html_msg($flag,$msg,$includeExclaim=false){
	
    if ($includeExclaim){ $msg = '<div id="sgExclaim">!</div>'.$msg.''; }

    if ($flag == -1){
		echo '<div class="fail wrap whAlert-box">'.$msg.'</div>';
	} 
	if ($flag == 0){
		echo '<div class="success wrap whAlert-box">'.$msg.'</div>';	
	}
	if ($flag == 1){
		echo '<div class="warn wrap whAlert-box">'.$msg.'</div>';	
	}
    if ($flag == 2){
        echo '<div class="info wrap whAlert-box">'.$msg.'</div>';
    }

    
}

// Ensures storage and return as UTF8 without slashes
function projectPages_textProcess($title){
	return htmlentities(stripslashes($title),ENT_QUOTES,'UTF-8');
} 
function projectPages_textExpose($title){
	return html_entity_decode($title,ENT_QUOTES,'UTF-8');
} 



function  projectPagesDefaultImage(){

	echo PROJECTPAGES_URL.'i/white-image.png';

}

// used for semantic ui numbering
function projectPagesReturnNoSimp($no=1){

	$noArr = array(  1                   => 'one',
    2                   => 'two',
    3                   => 'three',
    4                   => 'four',
    5                   => 'five',
    6                   => 'six',
    7                   => 'seven',
    8                   => 'eight',
    9                   => 'nine',
    10                  => 'ten',
    11                  => 'eleven',
    12                  => 'twelve',
    13                  => 'thirteen',
    14                  => 'fourteen',
    15                  => 'fifteen',
    16                  => 'sixteen',
    17                  => 'seventeen',
    18                  => 'eighteen',
    19                  => 'nineteen',
    20                  => 'twenty');

    if (isset($noArr[$no])) return $noArr[$no];

    return '';

}

#================== / Useful Functions 


// returns useful URLS
function ppurl( $key = '' ){

	global $projectPages_urls;

	if ( isset( $projectPages_urls[$key] ) ) return $projectPages_urls[$key];

	return $projectPages_urls['home'];

}

// return root projects url
function projectPages_projects_root_url(){

	return get_bloginfo('url') . '/' . projectPages_permalink_root();

}

function projectPages_get_current_page(){

 global $post, $wp_query; 

  // discern depth (home/elsewhere or projects archive or single project)
  // $_wp_current_template_id would be *//archive or *//single, but $wp_query is my fav
  $current_page = 'elsewhere';

  // cpt
  if ( $wp_query && isset( $wp_query->query['post_type'] ) && $wp_query->query['post_type'] == 'projectpage' ){

    // archive
    if ( $wp_query->is_archive ){
      
      $current_page = 'archive';

    }

    // single
    if ( $wp_query->is_single ){
      
      $current_page = 'single';

    }

    /* also works
    if ( $post && isset( $post->post_type ) && $post->post_type == 'projectpage' && $post->ID ){

      $current_page = 'single';

    } */

  }

  // taxonomy
  if ( is_tax( 'projectpagetag' ) ){

  	$current_page = 'taxonomy';

  }

  // catch editor callbacks
  // this is hacky, at best.
  if ( 
        $wp_query->query == NULL && 
        current_user_can( 'edit_published_pages' ) && 
        isset( $_GET['context'] ) && 
        $_GET['context'] == 'edit' 
      ){

      $current_page = 'editor';

  }
  
  return $current_page;

}


// hacked and burned version of paginate_links() from core
// ... workaround because this relies on $wp_query :/
// https://wordpress.stackexchange.com/questions/174907/how-to-use-the-posts-navigation-for-wp-query-and-get-posts
function pp_paginate_links( $args = '', $query = false ) {
	
	global $wp_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
		'format'             => $format, // ?page=%#% : %#% is replaced by the page number.
		'total'              => $total,
		'current'            => $current,
		'aria_current'       => 'page',
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => __( '&laquo; Previous' ),
		'next_text'          => __( 'Next &raquo;' ),
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => array(), // Array of query args to add.
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional query vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
		$format_query = isset( $format[1] ) ? $format[1] : '';
		wp_parse_str( $format_query, $format_args );

		// Find the query args of the requested URL.
		wp_parse_str( $url_parts[1], $url_query_args );

		// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
		foreach ( $format_args as $format_arg => $format_arg_value ) {
			unset( $url_query_args[ $format_arg ] );
		}

		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
	}

	// Who knows what else people pass in $args.
	$total = (int) $args['total'];
	if ( $total < 2 ) {
		return;
	}
	$current  = (int) $args['current'];
	$end_size = (int) $args['end_size']; // Out of bounds? Make it the default.
	if ( $end_size < 1 ) {
		$end_size = 1;
	}
	$mid_size = (int) $args['mid_size'];
	if ( $mid_size < 0 ) {
		$mid_size = 2;
	}

	$add_args   = $args['add_args'];
	$r          = '';
	$page_links = array();
	$dots       = false;

	if ( $args['prev_next'] && $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<a class="prev page-numbers" href="%s">%s</a>',
			/**
			 * Filters the paginated links for the given archive pages.
			 *
			 * @since 3.0.0
			 *
			 * @param string $link The paginated link URL.
			 */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['prev_text']
		);
	endif;

	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n == $current ) :
			$page_links[] = sprintf(
				'<span aria-current="%s" class="page-numbers current">%s</span>',
				esc_attr( $args['aria_current'] ),
				$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
			);

			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $add_args ) {
					$link = add_query_arg( $add_args, $link );
				}
				$link .= $args['add_fragment'];

				$page_links[] = sprintf(
					'<a class="page-numbers" href="%s">%s</a>',
					/** This filter is documented in wp-includes/general-template.php */
					esc_url( apply_filters( 'paginate_links', $link ) ),
					$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
				);

				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';

				$dots = false;
			endif;
		endif;
	endfor;

	if ( $args['prev_next'] && $current && $current < $total ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<a class="next page-numbers" href="%s">%s</a>',
			/** This filter is documented in wp-includes/general-template.php */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['next_text']
		);
	endif;

	switch ( $args['type'] ) {
		case 'array':
			return $page_links;

		case 'list':
			$r .= "<ul class='page-numbers'>\n\t<li>";
			$r .= implode( "</li>\n\t<li>", $page_links );
			$r .= "</li>\n</ul>\n";
			break;

		default:
			$r = implode( "\n", $page_links );
			break;
	}

	/**
	 * Filters the HTML output of paginated links for archives.
	 *
	 * @since 5.7.0
	 *
	 * @param string $r    HTML output.
	 * @param array  $args An array of arguments. See paginate_links()
	 *                     for information on accepted arguments.
	 */
	$r = apply_filters( 'paginate_links_output', $r, $args );

	return $r;
}


function projectPages_is_gutenberg_editor() {

	if ( ! function_exists( 'get_current_screen' ) ) {
	  return false;
	}

	$screen = get_current_screen();
	if ( isset( $screen->is_block_editor ) ) return $screen->is_block_editor;

	return false;
}


// retrieves permalink root (usually `projects`)
function projectPages_permalink_root(){

	$setting_permalink_root = projectPages_getSetting('permalink_root');

	if ( !empty( $setting_permalink_root ) ){

		return sanitize_title( $setting_permalink_root, 'projects' );
	}

	return 'projects';

}

function projectPages_is_pro(){

	return defined('PROJECTPAGES_PRO_PATH');
}

// check if using a block theme, if so, set template mode to legacy and notify :(
function projectPages_theme_check(){

	global $projectPages_Settings;

	if ( !wp_is_block_theme() ){

		// hide any notifications
        delete_user_meta( get_current_user_id(), 'pp_theme_opportunity' );

		// set setting
		$projectPages_Settings->update( 'template_mode', 'legacy' );

		// notify
        update_user_meta(get_current_user_id(), 'pp_theme_issue', true);		
		

	} else {

		// hide any notifications
        delete_user_meta( get_current_user_id(), 'pp_theme_issue' );

        // if user is in legacy mode, show the option to switch to modern block mode
        // but only once a week
        if ( $projectPages_Settings->get( 'template_mode') == 'legacy' ){

        	if ( !get_transient('pp_theme_opportunity') ){

	        	set_transient( 'pp_theme_opportunity', 1, 60*60*24*7 );

	        	update_user_meta(get_current_user_id(), 'pp_theme_opportunity', true);

	        }

        } else {

        	// everything good, hide all
       		delete_user_meta( get_current_user_id(), 'pp_theme_opportunity' );

        }

	}
}

// on switch theme: check if theme is block-based
add_action('switch_theme', 'projectPages_theme_check');



function project_pages_theme_fail() {

    // Check if the user has dismissed the notification
    if (get_user_meta(get_current_user_id(), 'pp_theme_issue', true)) {

        ?>
        <div class="notice notice-warning is-dismissible" data-pp-dismiss="pp_theme_issue">
            <p><strong><?php _e('Project Pages', 'project-pages'); ?></strong></p>
            <p>Project Pages has set its template mode to 'Legacy', this is because your theme is not block based. <a href="'.esc_url( ppurl('kb-not-block-based') ) . '" target="_blank" class="button button-primary">Read More</a></p>           	
        </div>
        <?php

    }

}

function project_pages_theme_opportunity() {

    // Check if the user has dismissed the notification
    if (get_user_meta(get_current_user_id(), 'pp_theme_opportunity', true)) {

        ?>
        <div class="notice notice-warning is-dismissible" data-pp-dismiss="pp_theme_opportunity">
            <p><strong><?php _e('Project Pages', 'project-pages'); ?></strong></p>
            <p>Did you know your theme is block based, this means you can use our new improved templates! <a href="'.esc_url( ppurl('kb-not-block-based') ) . '" target="_blank" class="button button-primary">Read More</a></p>
        </div>
        <?php

    }

}


function projectPages_dismiss_announcement() {
    if (isset($_POST['dismiss']) && $_POST['dismiss'] === 'true') {

    	// discern what is being dismissed
    	if ( isset( $_POST['to_dismiss']) && !empty( $_POST['to_dismiss'] ) ){

    		$to_dismiss = sanitize_text_field( $_POST['to_dismiss'] );

    		// hardtyped for security
    		$dismissable = array( 
    			'pp_theme_opportunity',
    			'pp_theme_issue'
    		);

    		if ( in_array( $to_dismiss, $dismissable ) ){
	        
		        // Delete user meta to mark the notification as dismissed
		        delete_user_meta(get_current_user_id(), $to_dismiss, true);

		    }

	    }
    }
    wp_die(); // This is required to terminate immediately and return a proper response
}
function projectPages_enqueue_announcement_script($hook) {
    // Only enqueue on admin pages
    wp_enqueue_script('ppv2-notifications', PROJECTPAGES_URL . 'js/ProjectPages.Announcements.js', array('jquery'), null, true);
    wp_localize_script('ppv2-notifications', 'adminNotification', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('dismiss_announcement_pp_nonce'),
        'dismiss_target' => 'dismiss_announcement_pp'
    ));
}
add_action('admin_enqueue_scripts', 'projectPages_enqueue_announcement_script');
add_action('wp_ajax_dismiss_announcement_pp', 'projectPages_dismiss_announcement');