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


// show 'powered by'
function projectPages_powered_by(){

  $html = '';

  // to stop this showing twice on any page, we set a global
  global $project_pages_powered_by;

  if ( !isset( $project_pages_powered_by ) ){

    $shareLove = projectPages_getSetting('poweredby');

    if ( $shareLove == "1" ){

      $html = '<div class="project-pages-power">Powered by <a href="' . ppurl('home') . '" target="_blank" title="Project Pages - Showcase your projects"><span>Project</span> Pages</a></div>';

      $project_pages_powered_by = true;

    }

  } 

  return $html;

}