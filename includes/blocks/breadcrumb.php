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

// breadcrumbs block
function projectPages_blocks_render_breadcrumb( $attributes, $content, $block, $template_type = 'single', $override_single_project = false ) {

  global $post;

    $current_page = projectPages_get_current_page();

    $html = '';
    $showHomeLevel = false;
    if ( isset( $attributes['showHomeLevel'] ) && $attributes['showHomeLevel'] ){

      $showHomeLevel = true;

    }

    $home_label = get_bloginfo( 'name' );
    if ( isset( $attributes['homeLabel'] ) && !empty( $attributes['homeLabel'] ) ){

      $home_label = $attributes['homeLabel'];

    }
    $projects_label = __( 'Projects', 'projectpages' );
    if ( isset( $attributes['projectsLabel'] ) && !empty( $attributes['projectsLabel'] ) ){

      $projects_label = $attributes['projectsLabel'];

    }
    $separator = '<span class="project-pages-separator dashicons dashicons-arrow-right-alt2"></span>';
    if ( isset( $attributes['separator'] ) && !empty( $attributes['separator'] ) ){

      $separator = '<span class="project-pages-separator">' . $attributes['separator'] . '</span>';

    }

    // here we override current_page if $template_type = 'archive' (this lets us use the fact we have a split hero block for archive let us show archive breadcrumbs in editor)
    if ( $template_type == 'archive' ) $current_page = 'archive';
    if ( $template_type == 'taxonomy' ) $current_page = 'taxonomy';

    // here we allow hard override
    if ( $override_single_project ) $current_page = 'single_override';

    switch ( $current_page ) {

        // single
        case 'single':

          // get urls
          $project_url = get_permalink( $post );
          $project_title = $post->post_title;

          $html = '<div class="project-pages-breadcrumb-wrap">';

          if ( $showHomeLevel ){
          
            $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', get_home_url(), $home_label )
                . $separator;
          
          }

          $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', projectPages_projects_root_url(), $projects_label )
                . $separator
                . sprintf( '<a href="%1$s" class="active-page">%2$s</a>', $project_url, $project_title )
                . '</div>';

          break;

        // single (override) - e.g. featured project block
        case 'single_override':

          // get urls
          $project_url = $override_single_project['permalink'];
          $project_title = $override_single_project['title'];

          $html = '<div class="project-pages-breadcrumb-wrap">';

          if ( $showHomeLevel ){
          
            $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', get_home_url(), $home_label )
                . $separator;
          
          }

          $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', projectPages_projects_root_url(), $projects_label )
                . $separator
                . sprintf( '<a href="%1$s" class="active-page">%2$s</a>', $project_url, $project_title )
                . '</div>';

          break;
        // archive
        case 'archive':

          $html = '<div class="project-pages-breadcrumb-wrap">';

          if ( $showHomeLevel ){
          
            $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', get_home_url(), $home_label )
                . $separator;
          
          }
                
          $html .= sprintf( '<a href="%1$s" class="active-page">%2$s</a>', projectPages_projects_root_url(), $projects_label )
                . '</div>';

          break;

        // taxonomy
        case 'taxonomy':

          $html = '<div class="project-pages-breadcrumb-wrap">';

          if ( $showHomeLevel ){
          
            $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', get_home_url(), $home_label )
                . $separator;
          
          }
                

          // if we have term, add it
          if ( is_tax( 'projectpagetag' ) ) {

              $html .= sprintf( '<a href="%1$s">%2$s</a>', projectPages_projects_root_url(), $projects_label );

              $term = get_queried_object();
              if ( isset( $term->name ) ){
                
                $html .= $separator
                . sprintf( '<a href="%1$s" class="active-page">%2$s</a>', get_term_link( $term->term_id ), sprintf( __( 'Tagged "%s"', 'projectpages' ), $term->name ) );                

              }

          } else {

            // just proj root
            $html .= sprintf( '<a href="%1$s" class="active-page">%2$s</a>', projectPages_projects_root_url(), $projects_label );

          }

          $html .= '</div>';

          break;

        // editor example
        case 'editor':

          $html = '<div class="project-pages-breadcrumb-wrap">';

          if ( $showHomeLevel ){
          
            $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', get_home_url(), $home_label )
                . $separator;
          
          }
                
          $html .= sprintf( '<a href="%1$s" class="">%2$s</a>', projectPages_projects_root_url(), $projects_label )
                . $separator
                . sprintf( '<a href="%1$s" class="active-page">%2$s</a>', '#', __( 'Example', 'projectpages' ) )
                . '</div>';

          break;

        // non project page
        case 'elsewhere':
        default:

          $html = __( 'Project Pages Breadcrumb: This block is not intended for use in pages outside of the Project Pages ecosystem.', 'projectpages' );

          break;

    }

  return $html;

}