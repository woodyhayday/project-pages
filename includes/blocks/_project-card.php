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


// Project card
function projectPages_projectCardOut( $project = array() ){

  if ( !is_array( $project ) ) return '';

  $show_biline = projectPages_getSetting( 'display_showbiline' );
  $show_status = projectPages_getSetting( 'display_showstatus' );

  $html = '<div class="project-card">';

  // feat img
  if ( $project['feat_img_checked'] ){

    $html .= '<a class="project-card-img-top" href="' . esc_url( $project['permalink'] ) . '"><img src="' . esc_url( $project['feat_img_checked'] ) . '" alt="' . esc_attr( $project['title'] ) . '"></a>';

  }

  // body
  $html .=  '<div class="project-card-body">';
  $html .=    '<a href="' . esc_url( $project['permalink'] ) . '" class="project-page-h5"><h5 class="project-card-title">' . esc_html( $project['title'] ) . '</h5></a>';
  if ( $show_biline ){

    $html .=    '<p class="project-card-text has-contrast-2-color has-small-font-size">';

    if ( $project['meta']['biline'] ){
      
      $html .= esc_html( $project['meta']['biline'] );

    } else {

      $html .= __( 'Updated', 'projectpages' ) . ' ' . $project['date'];

    }

    $html .=     '</p>';

  }
  $html .=  '</div>';

  // footer
  $html .=  '<div class="project-card-footer">
              <div class="project-card-footer-row">';
  
  // status
  if ( $show_status ){

    //project-pages-archive-status project-pages-status
    $html .=  '<div class="project-card-footer-column">
                  <div class="project-pages-ribbon ' . esc_html( $project['status_colour'] ) . '">
                    ' . esc_html( $project['status_label'] ) . '
                  </div>
              </div>';

  }

  // view
  $html .=    '<div class="project-card-footer-column">
                <a href="' . esc_url( $project['permalink'] ) . '" class="project-btn project-btn-primary project-btn-sm project-btn-black">' . __( 'View Project', 'projectpages' ) . '</a>
              </div>';
  

  $html .=  ' </div>
            </div>';

  $html .= '</div>';

  return $html;

}