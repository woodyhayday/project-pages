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

// Render project status card
function projectPages_blocks_render_status_card( $attributes, $content, $block ) {

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
    // Attr: showFeatImg, showTags, showLogs

    $html = '<div class="project-page-card">';

      // feat img?
      if ( $attributes['showFeatImg'] ){

        $html .= '<div class="project-page-card-image"><img src="' .esc_url( $project['feat_img_checked'] ) . '" alt="' . esc_attr( $project['title_checked'] ) . '" /></div>';

      }

      // card
      $html .= '<div class="project-pages-status-card project-pages-info-box">';

        $html .= '<div class="content">'
              .     '<span class="right floated circle project-pages-icon ' . esc_attr( $project['status_colour'] ) . '" title="' . __( 'Status: ', 'projectpages' ) . $project['status'] . '">●</span>'
              .     '<div class="header">' . __( 'Status', 'projectpages' ) . ': ' . esc_html( $project['status_label'] ) . '</div>'
              .     '<div class="meta">' . __( 'Updated', 'projectpages' ) . ': ' . esc_html( $project['date'] ) . '</div>'
              . '</div>';


        // tags?
        if ( $attributes['showTags'] ){

          $tag_list_html = '';

          // got tags even?
          if ( is_array( $project['tags'] ) && count( $project['tags'] ) > 0 ){

            // cycle through tags and link
            foreach ( $project['tags'] as $tag ){

              if ( !empty( $tag_list_html ) ){

                $tag_list_html .= ', ';

              }

              // add link
              $tag_url = get_term_link( $tag );
              $tag_list_html .= '<a href="' . esc_url( $tag_url ) .'" title="' . esc_attr( $tag->name . ' ' . __( 'Project Pages', 'projectpages' ) ) . '">' . $tag->name . '</a>';

            }


          } else {

            $tag_list_html = __( 'No Tags', 'projectpages' );

          }

          $html .= '<div class="content">'
                .     '<div class="description">'
                .       '<p><span class="dashicons dashicons-tag"></span> ' . __( 'Tagged', 'projectpages' ) . ': ' . $tag_list_html .'</p>'
                .     '</div>'
                . '</div>';

        }

        // Logs?
        if ( $attributes['showLogs'] && is_array( $project['logs'] ) && count( $project['logs'] ) > 0 ){


          $logs_html = '';

          // got logs even?
          if ( is_array( $project['logs'] ) ){

              $html .= '<div class="content">'
                    .     '<div class="description">'
                    .       '<p><span class="dashicons dashicons-format-status"></span> ' . sprintf( __( '%s x ', 'projectpages' ), count( $project['logs'] ) ) . ' <a href="#project-logs">' . __( 'Project Logs', 'projectpages' ) .'</a></p>'
                    .     '</div>'
                    . '</div>';

          } else {

              $html .= '<div class="content">'
                    .     '<div class="description">'
                    .       '<p><span class="dashicons dashicons-format-status"></span> ' . __( 'No Project Logs', 'projectpages' ) .'</p>'
                    .     '</div>'
                    . '</div>';

          }

        }

        // CTA
        if ( is_array( $project['meta']) && isset( $project['meta']['demourl'] ) && !empty( $project['meta']['demourl'] ) ){
            
            $cta_url = $project['meta']['demourl']; 
            $cta_text = __('View','projectpages'); 
            if ( isset( $project['meta']['demolinktext'] ) && !empty( $project['meta']['demolinktext'] ) ){
              
              $cta_text = $project['meta']['demolinktext'];

            }

            $html .= '<div class="content project-pages-cta">'
                    .     '<a href="' . esc_url( $cta_url ) . '" class="project-btn project-btn-primary project-btn-sm project-btn-black" target="_blank">' . esc_html( $cta_text ) . '</a>'
                    . '</div>';

        }


      $html .= '</div>'; // end card

    $html .= '</div>';

  } else {

    // something's gone wrong.
    $html = '<p>' . __( 'There was an error displaying this project card', 'projectpages' ) . '</p>';

  }


  return $html;

}
