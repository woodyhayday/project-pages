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

// query loop render
function projectPages_blocks_render_query_loop( $attributes, $content, $block ) {

  // enqueue frontend scripts
  projectPages_enqueue_frontend();

  // build html

  $html = '';

  // localise
  $filter = 'none'; if ( isset( $attributes['filter'] ) ){ $filter = $attributes['filter']; }
  $order_by = 'ID'; if ( isset( $attributes['order'] ) ){ $order_by = $attributes['order']; }
  $order_direction = 'DESC'; if ( isset( $attributes['orderDirection'] ) ){ $order_direction = $attributes['orderDirection']; }
  $limit = -1;  // pagination in future version. $limit = 10; if ( isset( $attributes['limit'] ) ){ $limit = $attributes['limit']; }

  $tag = ''; if ( isset( $attributes['tag'] ) ){ $tag = $attributes['tag']; }
  $status = ''; if ( isset( $attributes['status'] ) ){ $status = $attributes['status']; }
  $is_timeline_view = false;

  // we only want what's set by the filter (e.g. not status + tag)
  switch ( $filter ){

    case 'tag':
      $status = '';

      break;
    case 'status':
      $tag = '';

      break;
    case 'none':
      $tag = '';
      $status = '';

      break;

  }

  // output
  switch ($attributes['displayType']){


      case 'timeline':

        $is_timeline_view = true;
        $projects = projectPages_getProjects( $status, $tag, 'publish_date', $order_direction, -1, true, true, false, projectPages_statuses_active_archived_completed() );

        break;

      case 'all':
      default:

        $projects = projectPages_getProjects( $status, $tag, $order_by, $order_direction, -1, true, true );

        break;

  }

  if ( count( $projects ) > 0 ){

    if ( $is_timeline_view ){

        $html = projectPages_timeline_html ( $projects, $attributes );

    } else {

      // standard view
    
        // start the grid
        $html .= '<div class="project-pages-archive"><div class="row justify-content-md-center project-pages-bs-row">';

        // cycle through them, making bootstrap rows
        foreach ( $projects as $project ){

          // project added
          $html .= projectPages_projectCardOut( $project );

        }

        // always close the grid.
        $html .= '</div></div>';


    }

    // pagination #todo



  } else {

    // something's gone wrong.
    $html = projectPages_no_projects_no_cry();

  }
  
  // discern width
  $content_width = 'narrow'; if ( $attributes['contentWidth'] === 'full' ) $content_width = 'full';

  return '<div class="project-pages-archives project-pages-wrapper-' . esc_attr( $content_width ) . ' project-pages-wrapper-card-list">' . $html . projectPages_powered_by() . '</div>';

}