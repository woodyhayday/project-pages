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


function projectPages_is_using_theme_template(){


	//if ( !is_child_theme() ){

    if ( projectPages_getSetting('template_mode') == "default" ){

		// default ( gutenberg templates )
		if ( file_exists( get_stylesheet_directory() . '/templates/single-projectpage.html' ) ) return true;
		if ( file_exists( get_stylesheet_directory() . '/templates/archive-projectpage.html' ) ) return true;
		if ( file_exists( get_stylesheet_directory() . '/templates/taxonomy-projectpagetag.html' ) ) return true;

	}

	return false;

}