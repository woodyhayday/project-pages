<?php 
/*!
 * Project Pages
 * http://www.woodyhayday.com/project/project-pages
 * V1.0
 *
 * Copyright 2017 and beyond, WoodyHayday.com, StormGate Ltd.
 *
 * Date: 17/01/17
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

	// asset loading

	// js
	wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'bootstrap-js', PROJECTPAGES_URL . 'js/libs/bootstrap/bootstrap.bundle.min.js', array('jquery'), '3.3.4', true );
	
	// css
	wp_enqueue_style( 'pp_bootstrap', PROJECTPAGES_URL . 'css/bootstrap/bootstrap.min.css' );
	wp_dequeue_style( 'admin-bar-css' );
	

?><!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php esc_html_e( 'Deactivation Feedback.', 'zero-bs-crm' ); ?></title>
	<?php @wp_print_styles(); ?>
	<style type="text/css">img.wp-smiley,img.emoji{display:inline !important;border:none !important;box-shadow:none !important;height:1em !important;width:1em !important;margin:0 .07em !important;vertical-align:-0.1em !important;background:none !important;padding:0 !important}#wc-logo img{max-width:20% !important}#feedbackPage{display:none}.wc-setup .wc-setup-actions .button-primary{background-color:#408bc9 !important;border-color:#408bc9 !important;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.25),0 1px 0 #408bc9 !important;box-shadow:inset 0 1px 0 rgba(255,255,255,.25),0 1px 0 #408bc9 !important;text-shadow:0 -1px 1px #408bc9,1px 0 1px #408bc9,0 1px 1px #408bc9,-1px 0 1px #408bc9 !important;float:right;margin:0;opacity:1}</style>	
	<style type="text/css">#wpadminbar { display:none !important; }</style>	
</head>
<body class="wc-setup wp-core-ui">

	<div class="px-4 py-5 my-5 text-center">
    <img src="<?php echo PROJECTPAGES_URL . 'i/project-pages-128.png'; ?>" alt="Project Pages" width="64" height="64">
    <h1 class="display-5 fw-bold text-body-emphasis"><?php _e( 'Before you go...', 'projectpages' ); ?></h1>
    <div class="col-lg-6 mx-auto">
      <p class="lead mb-4"><?php 
      _e( 'Thanks for giving Project Pages a try. I don\'t want to hold you up, but if you have any feedback at all I\'d really appreciate you dropping it below. I\'m actively making this for you, and take everything you say onboard 👍', 'projectpages' ); 
    	?></p>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
      	<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="btn btn-outline-secondary btn-lg px-4"><?php _e( 'Not right now', 'projectpages' ); ?></a>
        <a href="<?php echo ppurl( 'feedback' ); ?>" target="_blank" class="btn btn-primary btn-lg px-4 gap-3"><?php _e( 'Give Feedback', 'projectpages' ); ?></a>
      </div>
      <p class="text-center mt-3"><?php esc_html_e( 'Giving feedback won\'t close your tab, and it won\'t take more than a few minutes.', 'zero-bs-crm' ); ?></p>
    </div>
  </div>

</body></html>