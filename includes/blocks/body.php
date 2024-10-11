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

// Render single project body
function projectPages_blocks_render_body( $attributes, $content, $block ) {

  $html = '';

  $current_page = projectPages_get_current_page();

  switch ( $current_page ){

    case 'editor':

      // load example project
      $project = projectPages_example_project();

      break;

    case 'single':

      // load the project, if not loaded already (this'll autocache)
      $project = projectPages_get_single_project();

      break;

  }

  // output
  if ( isset( $project ) && is_array( $project ) && isset( $project['ID'] ) ){

    // output it
    $html = $project['body'];

  } else {

    // something's gone wrong.
    $html = '<p>' . __( 'There was an error displaying this project.', 'projectpages' ) . '</p>';

  }

  // discern width
  $content_width = 'narrow'; if ( $attributes['contentWidth'] === 'full' ) $content_width = 'full';

  // if not using logs, add powered by here
  if ( projectPages_getSetting('use_logs') != "1" ){
  
    $html .= projectPages_powered_by();

  }

  return '<div class="project-pages-body project-pages-wrapper-' . esc_attr( $content_width ) . ' project-pages-align-' . esc_attr( $attributes['align'] ) . '">' . $html . '</div>';

}