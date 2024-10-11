<?php 
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 05/08/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */


/* 

// Product hunt, only show 8th -> 9th Aug 24.
if ( time() > 1723136400 && time() < 1723276860 ){

	// PH announcement
	add_action('admin_notices', 'project_pages_announcement_ph');
	add_action('admin_enqueue_scripts', 'enqueue_announcement_ph_script');
	add_action('wp_ajax_dismiss_announcement_ph', 'dismiss_announcement_ph');

}

function project_pages_announcement_ph() {

    // Check if the user has dismissed the notification
    if (!get_user_meta(get_current_user_id(), 'announcement_ph_dismissed', true)) {

        ?>
        <div class="notice notice-warning is-dismissible">
            <p><strong><?php _e('Project Pages is launching on Product Hunt!', 'project-pages'); ?></strong></p>
            <p>You're now using Project Pages v2.0! I'm just about to launch this new version on Product Hunt, and it'd be amazing if you could take a look!</p>
           	<p><a href="https://www.producthunt.com/posts/project-pages?embed=true&utm_source=badge-featured&utm_medium=badge&utm_souce=badge-project&#0045;pages" target="_blank"><img src="https://api.producthunt.com/widgets/embed-image/v1/featured.svg?post_id=476780&theme=light" alt="Project&#0032;Pages - Showcase&#0032;the&#0032;things&#0032;you&#0032;make&#0033; | Product Hunt" style="width: 250px; height: 54px;" width="250" height="54" /></a></p>
        </div>
        <?php

    }

}


function dismiss_announcement_ph() {
    if (isset($_POST['dismiss']) && $_POST['dismiss'] === 'true') {
        // Set a user meta to mark the notification as dismissed
        update_user_meta(get_current_user_id(), 'announcement_ph_dismissed', true);
    }
    wp_die(); // This is required to terminate immediately and return a proper response
}


function enqueue_announcement_ph_script($hook) {
    // Only enqueue on admin pages
    wp_enqueue_script('ppv2-ph-notification', PROJECTPAGES_URL . 'js/ProjectPages.Announcements.js', array('jquery'), null, true);
    wp_localize_script('ppv2-ph-notification', 'adminNotification', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('dismiss_announcement_ph_nonce'),
    ));
}

*/