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

// Init Blocks
function projectPages_init_blocks(){

  // global includes
  require_once PROJECTPAGES_PATH . '/includes/blocks/_project-card.php';
  require_once PROJECTPAGES_PATH . '/includes/blocks/_powered-by.php';

  // blocks to register
  $blocks = array(

    // generally useful
    'recent-project-logs' => 'projectPages_blocks_render_recent_logs',

    // for template (archive & single)
    'breadcrumb' => 'projectPages_blocks_render_breadcrumb',
    'tag-cloud' => 'projectPages_blocks_render_tag_cloud',

    // single
    'summary' => 'projectPages_blocks_render_summary',
    'status-card' => 'projectPages_blocks_render_status_card',
    'body' => 'projectPages_blocks_render_body',
    'logs' => 'projectPages_blocks_render_logs',
    'hero' => 'projectPages_blocks_render_hero_single',

    // archive template
    'query-loop' => 'projectPages_blocks_render_query_loop',
    'hero-archives-tags' => 'projectPages_blocks_render_hero_archives_tags',
    'archive-body' => 'projectPages_blocks_render_archives_tags_body'
    
  );

  foreach ( $blocks as $dir => $render_callback ) {
    $args = array();
    if ( ! empty( $render_callback ) ) {
      $args['render_callback'] = $render_callback;
    }

    // include when needed
    if ( !function_exists( $render_callback ) ){

      if ( file_exists( PROJECTPAGES_PATH . 'includes/blocks/' . $dir . '.php' ) ){
        
        require_once PROJECTPAGES_PATH . 'includes/blocks/' . $dir . '.php';

      }

    }

    register_block_type( PROJECTPAGES_PATH . 'blocks/build/' . $dir, $args );
  }
  
}


// due to dashicons not loading into block editor
// https://github.com/WordPress/gutenberg/issues/53528
add_action('enqueue_block_assets', function (): void {
    wp_enqueue_style('dashicons');
});

// bootstrap in gut:
// https://stackoverflow.com/questions/72364803/create-wordpress-blocks-based-on-bootstrap
function projectPages_add_editor_styles() {

    add_theme_support('editor-styles');

    // enqueue our public styles, and bootstrap
    add_editor_style([
        'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css',
        plugins_url('/css/ProjectPages.Frontend.min.css', PROJECTPAGES_ROOTFILE )
    ]);

} add_action('after_setup_theme', 'projectPages_add_editor_styles');


// admin init, if gutenberg editor
function projectPages_gutenberg_init(){

    // custom endpoints for gut options SelectControl's etc.
    //projectPages_declare_custom_gut_rest_endpoints();

}

// required for query-loop block attribute choices
// replaces #legacyinject
function projectPages_gutenberg_scripts( $return = true ){

    if ( projectPages_is_gutenberg_editor() && current_user_can( 'edit_posts' ) ){

        global $projectPageStatuses;

        // remap slightly
        $projectPageStatuses_for_gut = array();
        foreach ( $projectPageStatuses as $status_key => $status_obj ){
          $projectPageStatuses_for_gut[] = array(
            'label' => $status_obj[0],
            'value' => $status_key
          );
        }

        // build a simple global
        $project_pages_gutenberg_store = array(

          'tags' => projectPages_getTags( false, false, true ),
          'status' => $projectPageStatuses_for_gut

        );

        $script = '<script>var project_pages = ' . json_encode( $project_pages_gutenberg_store ) . ';</script>';

        if ( !$return ){

            echo $script;
            return true;

        }

        // return
        return $script;

    }

    return '';

}

// Add our category to blocks catalog
function projectPages_new_block_category( $block_categories ) {

  $block_categories[] = array(
    'slug' => 'project-pages-blocks',
    'title' => 'Project Page Blocks'
  );

  return $block_categories;
  
}
add_filter( 'block_categories_all', 'projectPages_new_block_category' );


/*
  Returns html to render a timeline view of $projects
*/
function projectPages_timeline_html( $projects, $attributes ){

  $html = '';

  // got projects?
  if ( !is_array( $projects ) || count( $projects ) < 1 ) return '';

    // project-pages-timeline view

    $show_years = ( isset( $attributes['timelineShowYears'] ) && $attributes['timelineShowYears'] );
    $year_cursor = false;

    $html .= '<div class="project-pages-timeline">
                <div class="container">
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="project-pages-timeline-container">
                        <div class="project-pages-timeline-end">
                          <p>' . esc_html( isset( $attributes['timelineTopText'] ) ? $attributes['timelineTopText'] : '' ) .'</p>
                        </div>';

    // these should be in order by date anyhow (publish_date):

    // if show years, first we output the year of the first project in arr
    if ( $show_years ){

      // default
      $first_year = __( 'Latest', 'projectpages' );

      if ( isset( $projects[0]['timestamp'] ) ){

        $first_year = date( 'Y', $projects[0]['timestamp'] );
        $year_cursor = $first_year;

      }

      $html .= '        <div class="row">
                          <div class="col-12">
                            <div class="project-pages-timeline-year">
                              <p>' . esc_html( $first_year ) . '</p>
                            </div>
                          </div>
                        </div>';
    }

    $html .= '          <div class="project-pages-timeline-continue">';

    // cycle through projects, outputting
    $side_cursor = 'right';
    foreach ( $projects as $project ){

      // if years, and this ones a new year, output year header
      if ( $show_years ){

        if ( isset( $project['timestamp'] ) ){

          $this_year = date( 'Y', $project['timestamp'] );
          if ( $this_year !== $year_cursor ){

            // fresh year            

            // set cursor
            $year_cursor = $this_year;

            // output break
            $html .= '  </div>          

                        <div class="row">
                          <div class="col-12">
                            <div class="project-pages-timeline-year">
                              <p>' . esc_html( $year_cursor ) . '</p>
                            </div>
                          </div>
                        </div>

                        <div class="project-pages-timeline-continue">';

          }

        }

      }


      $html .= '<div class="row project-pages-timeline-' . esc_attr( $side_cursor ) .'">';

      // img?
      $optional_img = '';
      if ( isset( $attributes['timelineShowImages'] ) && $attributes['timelineShowImages'] ){

        $optional_img = '<a href="' . esc_url( $project['permalink'] ) . '"><img class="project-pages-timeline-image" src="' . esc_url( $project['feat_img_checked'] ) . '" alt="' . esc_attr( $project['title'] ) . '" /></a>';

      }

      // flip flop columns
      $cols = array(
                          '<div class="col-md-6 project-pages-relative">
                            <p class="project-pages-timeline-date">
                              ' . esc_html( date( 'F Y', $project['timestamp'] ) ) . '
                            </p>
                          </div>',
                          
                          '<div class="col-md-6 project-pages-relative">
                            <div class="project-pages-timeline-box">                                
                              <div class="project-pages-timeline-text">'
                                . $optional_img . '
                                <a href="' . esc_url( $project['permalink'] ) . '"><h3>' . esc_html( $project['title'] ) . '</h3></a>
                                <p>' . $project['summary'] . '</p>
                                <div class="project-pages-timeline-status project-pages-status ' . esc_html( $project['status_colour'] ) . '">
                                  ' . esc_html( $project['status_label'] ) . '
                                </div>
                              </div>
                            </div>
                          </div>'
                    );

      // switch
      if ( $side_cursor == 'left' ){

        $cols = array_reverse( $cols );

      }

      $html .= $cols[0] . $cols[1];

      $html .= '</div>';


        // flip. flop.
        if ( $side_cursor == 'right' ){

          $side_cursor = 'left';

        } else {

          $side_cursor = 'right';

        }

      }

    // closing
    $html .= '
                    </div>

                      <div class="project-pages-timeline-start">
                        <p>' . esc_html( isset( $attributes['timelineBaseText'] ) ? $attributes['timelineBaseText'] : '' ) .'</p>
                      </div>
                    
                  </div>
                </div>
              </div>
            </div>';


    return $html;

}

// no projects msg
function projectPages_no_projects_no_cry(){

  return '<p class="project-pages-4oh4">' . __( 'There are currently no projects to display.', 'projectpages' ) . '</p>';

}