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

// recent logs block
function projectPages_blocks_render_recent_logs( $attributes, $content, $block ) {

  $classes = array( 'project-pages-recent-logs', 'project-pages-block-text-' . $attributes['align'] );  
  $wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
  
  // count
  $count = 0; if ( isset( $attributes['numberOfLogs'] ) ) $count = (int)$attributes['numberOfLogs'];

  // retrieve
  $recent_logs = projectPages_getLogs( -1, true, $count, 0, true, 'user_date', 'DESC' );

  $li_html = '';

  foreach ( $recent_logs as $log ) {

    $project_page_url = esc_url( get_permalink( $log['projectpageid'] ) );

    $project_page_title = get_the_title( $log['projectpageid']  );

    if ( ! $project_page_title ) {
      $project_page_title = __( '(Untitled Project)', 'dynamic-block' );
    }

    $li_html .= '<li>';

    if ( $log['icon'] ){

      $li_html .= '<span class="dashicons ' . $log['icon'] . '"></span>';

    } else {

      $li_html .= '<span class="dashicons  dashicons-arrow-right"></span>';

    }   

    if ( isset( $attributes['showDate'] ) && $attributes['showDate'] ){

      $li_html .= '<span class="project-pages-block-date">' . $log['date'] .'</span>';
    }

    if ( $log['title'] ){

      $li_html .= $log['title'];

    } else {

      $li_html .= __( 'Log', 'projectpages' );

    }

    $li_html .= sprintf(
      ' (<a class="dynamic-block-recent-posts__post-title" href="%1$s" title="%2$s">%2$s</a>)',
      esc_url( $project_page_url ),
      $project_page_title
    );

    /* if ( $log['body'] ){

    echo  htmlspecialchars_decode( $log['body'] );

    } else {

    // silence.

    } */


    $li_html .= '</li>';

  }

  $heading = ( isset( $attributes['showHeading'] ) && $attributes['showHeading'] ) ? '<h2>' . $attributes['heading'] . '</h2>' : '';

  $see_all_projects = '';
  if ( isset( $attributes['showSeeAll'] ) && $attributes['showSeeAll'] ){

    $see_all_projects_label = __( 'View All Projects', 'projectpages' );

    if ( isset( $attributes['seeAllLabel'] ) ){
      $see_all_projects_label = $attributes['seeAllLabel'];
    }

    $see_all_projects = sprintf( 
      '<div class="project-pages-view-all"><a href="%1$s" title="%2$s" class="wp-block-button__link wp-element-button">%3$s</a></div>',
      projectPages_projects_root_url(),
      $see_all_projects_label,
      $see_all_projects_label
    );

  }

  return sprintf(
    '<div %2$s">%1$s<ul>%3$s</ul>%4$s</div>',
    $heading,
    $wrapper_attributes,
    $li_html,
    $see_all_projects
  );
}