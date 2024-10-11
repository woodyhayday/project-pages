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

// archives/tags hero, contains breadcrumb block (embedded)
function projectPages_blocks_render_hero_archives_tags( $attributes, $content, $block ) {

  $html = '';

  // get screen
  $current_page = projectPages_get_current_page();

  // archive or tag?
  $breadcrumb_template = 'archive';
  if ( is_tax( 'projectpagetag' ) ) {
    $breadcrumb_template = 'taxonomy';
  }

  // build breadcrumb html
  // (This uses the breadcrumb block, passing down the same attr)
  $breadcrumb_html = '';
  if ( $attributes['showBreadcrumbs'] ) {
    $breadcrumb_html = projectPages_blocks_render_breadcrumb( $attributes, $content, $block, $breadcrumb_template );
  }

  // localise settings 
  $bg_override_single = ( isset( $attributes['bgOverrideProject'] ) && $attributes['bgOverrideProject'] ) ? true : false;
  $bg_img_url = ''; if ( isset( $attributes['bgImgUrl'] ) ){
    $bg_img_url = $attributes['bgImgUrl'];
  }
  $bg_vid_url = ''; if ( isset( $attributes['bgVidUrl'] ) ){
    $bg_vid_url = $attributes['bgVidUrl'];
  }

  // generally used
  $blog_title = get_bloginfo('name');
  $blog_url = get_bloginfo('url');

  // Archive or tax page
  if ( $current_page == 'archive' || $current_page == 'taxonomy' ){

    // retrieve term if taxonomy
    // {"term_id":3,"name":"new ideas","slug":"new-ideas","term_group":0,"term_taxonomy_id":3,"taxonomy":"projectpagetag","description":"","parent":0,"count":2,"filter":"raw"}
    if ( is_tax( 'projectpagetag' ) ) {
      $term = get_queried_object();
    }

    // Generate CSS (this should probably be JS based)    
    $override_css = '';

    // Header colours
    if ( isset( $attributes['bgColour'] ) && !empty( $attributes['bgColour'] ) ){

        $override_css = '#projectmasthead {
            background-color:' . $attributes['bgColour'] . ' !important;
          }';
        
    }

    // Header backgrounds :)
    if ( isset( $attributes['bgImgUrl'] ) && !empty( $attributes['bgImgUrl'] ) ){

        $override_css = '#projectmasthead {
            background-image: url("' . $attributes['bgImgUrl'] . '");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 50% 50%;
          }';
        
    }
    if ( isset( $attributes['bgVidUrl'] ) && !empty( $attributes['bgVidUrl'] ) ){

        $bg_video_fallback = $attributes['bgVidUrl'];
        $override_css = '#projectmasthead {
            overflow: hidden;
            position: relative;
          }
          #projectmasthead .ui.container {
            position: relative;
          }';
        
    }

    // Likely this'll always fire :)
    $hero_text_color = '#FFFFFF';
    if ( isset( $attributes['heroTextColour'] ) && !empty( $attributes['heroTextColour'] ) ){
      $hero_text_color = $attributes['heroTextColour'];
    }

    $override_css .= '#projectmasthead .project-pages-hero-h1 h1, 
    body div.project-pages-breadcrumb-wrap a,
    body div.project-pages-breadcrumb-wrap span.project-pages-separator
    {color:' . $hero_text_color . ';-webkit-text-fill-color:' . $hero_text_color . ';}';
  
    $hero_text_alt_color = '#CCCCCC';
    if ( isset( $attributes['heroTextAltColour'] ) && !empty( $attributes['heroTextAltColour'] ) ){
      $hero_text_alt_color = $attributes['heroTextAltColour'];
    }

    $override_css .= '#projectmasthead h2,
    #projectmasthead .project-pages-hero-h1 .project-pages-share-wrap span,
    body div.project-pages-breadcrumb-wrap a.active-page
    {color:' . $hero_text_alt_color . ';-webkit-text-fill-color:' . $hero_text_alt_color . ';}';

    if ( $override_css ){

      // dislike putting it out inline, but needs must.
      $html = '<style type="text/css">' . $override_css . '</style>';

    }


    // Generate HTML

    $html .= '<div class="ui inverted vertical masthead center aligned segment" id="projectmasthead">';
      

        // if using a video cover, via project, or fallback, inject it here
        if ( 
              isset ( $bg_video_fallback ) 
            ){

            $vid_url = $bg_video_fallback;
            $html .= '<video autoplay muted loop class="pp-video-bg"><source src="' . $vid_url . '" type="video/mp4">Your browser does not support HTML5 video.</video>';

        }

    $html .= $breadcrumb_html;

    // hmmm will depend on users choice for loop setting really. This seems most honest
    $project_count = projectPages_getProjectCount_filtered( projectPages_statuses_active_archived_completed() );

    $hero_title = ( !empty( $attributes['archiveTitle'] ) ? $attributes['archiveTitle'] : __( 'Project Pages', 'projectpages' ) );
    $biline = sprintf( __( 'Browse the %1$s Projects on %2$s', 'projectpages'), $project_count, $blog_title );
    
    // tags override the title, etc.
    if ( is_tax( 'projectpagetag' ) ) {

      // retrieve tagged count
      $tagged_count = projectPages_getProjectCount_filtered( projectPages_statuses_active_archived_completed(), $term->name );

      $hero_title = sprintf( __( 'Projects tagged %s', 'projectpages' ), ( !empty( $term->name ) ? $term->name : __( 'Tag', 'projectpages' ) ) );
      $biline = sprintf( __( 'Browse %s Projects tagged "%s"', 'projectpages' ), $tagged_count, ( !empty( $term->name ) ? $term->name : __( 'Tag', 'projectpages' ) ) );

    }

    $html .= '<div class="project-pages-hero-h1 wp-block-group has-global-padding is-layout-constrained wp-container-core-group-is-layout-6 wp-block-group-is-layout-constrained">
                <h1 class="wp-block-heading has-text-align-center has-x-large-font-size">
            ' . $hero_title . '
          </h1>';
        

        $html .= '<h2 class="wp-block-heading has-text-align-center">' . $biline . '</h2>';    
        $html .= projectPages_shareOut( esc_html( $biline ), projectPages_projects_root_url(), PROJECTPAGES_URL.'i/projectpages.png', true, false, true ); 
    $html .= '</div>
      </div>';

  } // / archive


  // editor
  if ( $current_page == 'editor' ){


    $blog_title = get_bloginfo('name');
    $blog_url = get_bloginfo('url');

    // Generate CSS (this should probably be JS based)
    
    $override_css = '';    

    // Header backgrounds :)
    // here we're in editor, so we just use the overrides if they're present
    // v2.0 1 = color, -1 = featimg, 2 = imgurl, 3 = vidurl, 4 = gradient
    // if there's no given header (e.g. featimg but no img specified, default to theme setting if present)

    // theme got settings?
    if ( isset( $attributes['bgColour'] ) && !empty( $attributes['bgColour'] ) ){

        $override_css = '#projectmasthead {
            background-color:' . $attributes['bgColour'] . ' !important;
          }';
        
    }
    if ( isset( $attributes['bgImgUrl'] ) && !empty( $attributes['bgImgUrl'] ) ){

        $override_css = '#projectmasthead {
            background-image: url("' . $attributes['bgImgUrl'] . '");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 50% 50%;
          }';
        
    }
    if ( isset( $attributes['bgVidUrl'] ) && !empty( $attributes['bgVidUrl'] ) ){

        $bg_video_fallback = $attributes['bgVidUrl'];
        $override_css = '#projectmasthead {
            overflow: hidden;
          }
          #projectmasthead .ui.container {
            position: relative;
          }';
        
    }

    // Likely this'll always fire :)
    $hero_text_color = '#FFFFFF';
    if ( isset( $attributes['heroTextColour'] ) && !empty( $attributes['heroTextColour'] ) ){
      $hero_text_color = $attributes['heroTextColour'];
    }

    $override_css .= '#projectmasthead .project-pages-hero-h1 h1, 
    body div.project-pages-breadcrumb-wrap a,
    body div.project-pages-breadcrumb-wrap span.project-pages-separator
    {color:' . $hero_text_color . ';-webkit-text-fill-color:' . $hero_text_color . ';}';
  
    $hero_text_alt_color = '#CCCCCC';
    if ( isset( $attributes['heroTextAltColour'] ) && !empty( $attributes['heroTextAltColour'] ) ){
      $hero_text_alt_color = $attributes['heroTextAltColour'];
    }

    $override_css .= '#projectmasthead h2,
    #projectmasthead .project-pages-hero-h1 .project-pages-share-wrap span,
    body div.project-pages-breadcrumb-wrap a.active-page
    {color:' . $hero_text_alt_color . ';-webkit-text-fill-color:' . $hero_text_alt_color . ';}';

    if ( $override_css ){

      // dislike putting it out inline, but needs must.
      $html = '<style type="text/css">' . $override_css . '</style>';

    }

    // Generate HTML

    $html .= '<div class="ui inverted vertical masthead center aligned segment" id="projectmasthead">';
          
        // if using a video cover, via project, or fallback, inject it here
        if ( isset ( $bg_video_fallback ) ){ 
          $html .= '<video autoplay muted loop class="pp-video-bg"><source src="' . $bg_video_fallback . '" type="video/mp4">Your browser does not support HTML5 video.</video>';
        }
    $html .= $breadcrumb_html;

    $html .= '<div class="project-pages-hero-h1 wp-block-group has-global-padding is-layout-constrained wp-container-core-group-is-layout-6 wp-block-group-is-layout-constrained">
                <h1 class="wp-block-heading has-text-align-center has-x-large-font-size">
            ' . ( !empty( $attributes['archiveTitle'] ) ? $attributes['archiveTitle'] : __( 'Project Pages', 'projectpages' ) ) . '
          </h1>';
        
        $html .= '<h2 class="wp-block-heading has-text-align-center">' . sprintf( esc_html__( 'Browse the %1$s Projects on %2$s', 'projectpages'), projectPages_getProjectCount(), $blog_title ) . '</h2>';    
        $html .= projectPages_shareOut( __( 'Projects Share', 'projectpages' ), '#', '', true, true ); 
    $html .= '</div>
      </div>';

  } // / editor

  return $html;

}