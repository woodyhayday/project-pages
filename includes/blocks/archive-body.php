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

// Render project archives
function projectPages_blocks_render_archives_tags_body( $attributes, $content, $block ) {

  $html = '';
  $projects_by_area = array();
  $tag_str = '';
  $is_timeline_view = false;

  // retrieve term if taxonomy
  // {"term_id":3,"name":"new ideas","slug":"new-ideas","term_group":0,"term_taxonomy_id":3,"taxonomy":"projectpagetag","description":"","parent":0,"count":2,"filter":"raw"}
  if ( is_tax( 'projectpagetag' ) ) {
    
    $term = get_queried_object();
    $tag_str = $term->name;

  }

  // orderby
  $order_by = 'ID';
  $order_direction = 'DESC';
  if ( isset( $attributes['orderBy'] ) ){
    $order_by = $attributes['orderBy'];
  }
  if ( isset( $attributes['orderDirection'] ) ){
    $order_direction = $attributes['orderDirection'];
  }

  // output
  switch ($attributes['displayType']){

      case 'all':

        $projects_by_area['all'] = projectPages_getProjects( '', $tag_str, $order_by, $order_direction, -1, true, true );

        break;

      case 'active':

        $projects_by_area['active'] = projectPages_getProjects_active( '', $tag_str, $order_by, $order_direction, -1, true, true );

        break;

      case 'timeline':

        $is_timeline_view = true;
        $projects_by_area['timeline'] = projectPages_getProjects( '', $tag_str, 'publish_date', 'DESC', -1, true, true, false, projectPages_statuses_active_archived_completed() );

        break;

      case 'active_archived':
      default: 

        $projects_by_area['active'] = projectPages_getProjects_active( '', $tag_str, $order_by, $order_direction, -1, true, true );        
        $projects_by_area['archived'] = projectPages_getProjects_archived( '', $tag_str, $order_by, $order_direction, -1, true, true );

        break;

  }

  // quick count
  $projects_count = 0;
  foreach( $projects_by_area as $project_area ){
    $projects_count += count( $project_area );
  }

  // output
  if ( $projects_count > 0 ){

    if ( $is_timeline_view ){

      // build timeline html (shared with query-loop block)
      $html = projectPages_timeline_html ( $projects_by_area['timeline'], $attributes );

    } else {

      // != project-pages-timeline, normal out

      // specify order.
      $project_order = array('active','archived','all');

      // output
      foreach ( $project_order as $area_key ){

          // only output if any to output
          if ( isset( $projects_by_area[ $area_key ] ) && count( $projects_by_area[ $area_key ] ) > 0 ){

              $html .= '<div class="project-pages-archive project-pages-archives-' . esc_attr( $area_key ) . '">';

              switch ( $area_key ){
                
                case 'all':

                  $html .= '<h2>' . __( 'All Projects', 'projectpages' ) . '</h2>';

                  break;
                
                case 'active':

                  $html .= '<h2>' . __( 'Active Projects', 'projectpages' ) . '</h2>';

                  break;
                
                case 'archived':

                  $html .= '<h2>' . __( 'Completed/Archived Projects', 'projectpages' ) . '</h2>';

                  break;


              }

              $html .= '<div class="row justify-content-md-center project-pages-bs-row">';

              // output projects 
              foreach ( $projects_by_area[ $area_key ] as $project ){

                // project card
                $html .= projectPages_projectCardOut( $project );

              }     

              // always close the grid.
              $html .= '</div></div>';

          }

      }

    }

  } else {

    // something's gone wrong.
    $html = projectPages_no_projects_no_cry();

  }

  // discern width
  $content_width = 'narrow'; if ( $attributes['contentWidth'] === 'full' ) $content_width = 'full';

  return '<div class="project-pages-archives project-pages-wrapper-' . esc_attr( $content_width ) . ' project-pages-wrapper-card-list">' . $html . projectPages_powered_by() . '</div>';

}