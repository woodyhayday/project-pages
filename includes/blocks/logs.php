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

// Render project logs
function projectPages_blocks_render_logs( $attributes, $content, $block ) { 
  
  if ( projectPages_getSetting('use_logs') == "1" ){

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
    if ( isset( $project ) && is_array( $project ) && isset( $project['ID'] ) && is_array( $project['logs'] ) ){

      if ( count( $project['logs'] ) > 0 ){

        $html = '<h2 id="project-logs">' . __( 'Project Log', 'projectpages' ) . '</h2>';

        foreach ( $project['logs'] as $log ){

            $html .= '<div class="project-pages-log project-pages-info-box">';

              if ( $log['date'] ){

                $html .= '<div class="project-pages-log-date">' . $log['date_pretty'] . '</div>';

              }

              $html .= '<div class="header"><span class="dashicons ' . esc_attr( $log['icon'] ) . '"></span> ' . esc_html( ( !empty( $log['title'] ) ? $log['title'] : __( 'Log', 'projectpages' ) ) ) . '</div>';

              $html .= '<div class="content">' . $log['body'] . '</div>';

            $html .= '</div>';

        }

      } else {

        // hide if no logs
        //$html = '<p>' . __( 'This project doesn\'t have any logs yet', 'projectpages' ) . '</p>';

      }



    } else {

      // something's gone wrong.
      $html = '<p>' . __( 'There was an error displaying this project.', 'projectpages' ) . '</p>';

    }

    // discern width
    $content_width = 'narrow'; if ( $attributes['contentWidth'] === 'full' ) $content_width = 'full';

    return '<div class="project-pages-logs project-pages-wrapper-' . esc_attr( $content_width ) . '">' . $html . '</div>' . projectPages_powered_by();

  } 

  return '';

}