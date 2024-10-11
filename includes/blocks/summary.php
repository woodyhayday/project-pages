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

// Render project summary
function projectPages_blocks_render_summary( $attributes, $content, $block ) {

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

  $html = '<div class="project-pages-summary project-pages-align-' . esc_attr( $attributes['align'] ) . '">';

  // output
  if ( isset( $project ) && is_array( $project ) && isset( $project['ID'] ) ){

    // output it
    $html .= '<h2>' . __( 'Summary', 'projectpages' ) . '</h2>';
    $html .= $project['summary'];

  } else {

    // something's gone wrong.
    $html .= '<h2>' . __( 'Summary', 'projectpages' ) . '</h2>';
    $html .= '<p>' . __( 'There was an error displaying this project summary', 'projectpages' ) . '</p>';

  }

  $html .= '</div>';

  return $html;

}