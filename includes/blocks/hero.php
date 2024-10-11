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


// single hero, contains breadcrumb block (embedded)
function projectPages_blocks_render_hero_single( $attributes, $content, $block ) {

  $html = '';

  // get screen
  $current_page = projectPages_get_current_page();

  // build breadcrumb html
  // (This uses the breadcrumb block, passing down the same attr)
  $breadcrumb_html = '';
  if ( $attributes['showBreadcrumbs'] ) {
    $breadcrumb_html = projectPages_blocks_render_breadcrumb( $attributes, $content, $block );
  }

  // localise settings 
  $bg_override_single = ( isset( $attributes['bgOverrideProject'] ) && $attributes['bgOverrideProject'] ) ? true : false;
  $bg_img_url = ''; if ( isset( $attributes['bgImgUrl'] ) ){
    $bg_img_url = $attributes['bgImgUrl'];
  }
  $bg_vid_url = ''; if ( isset( $attributes['bgVidUrl'] ) ){
    $bg_vid_url = $attributes['bgVidUrl'];
  }


  // if single, load the post deets
  if ( $current_page == 'single' ){

    $project = projectPages_get_project_from_post( false, true );

    // if no project, 4oh4
    if ( !is_array( $project ) || count( $project ) <= 0 || !isset( $project['ID'] ) ){

      return __( 'There was an error loading this Project', 'projectpages' );

    }

    $blog_title = get_bloginfo('name');
    $blog_url = get_bloginfo('url');

    // simple hitcounter
    $hits = projectPages_basic_hitcounter( $project['ID'] );

    // Generate CSS (this should probably be JS based)
    
    $override_css = '';

    // Header backgrounds :)
    // v2.0 1 = color, -1 = featimg, 2 = imgurl, 3 = vidurl, 4 = gradient
    // if there's no given header (e.g. featimg but no img specified, default to theme setting if present)
    if ( 
          $project['header_type'] == -1 && empty( $project['feat_img'] ) ||
          $project['header_type'] == 2 && empty( $project['bg_img_url'] ) ||
          $project['header_type'] == 3 && empty( $project['bg_video_url'] ) 
        ){

        // empty header, assume default

        // theme got settings?
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

    }

    switch ( $project['header_type'] ){

      // -1 = Featured Image
      case -1:

        if ( !empty( $project['feat_img'] ) ){ 

          $override_css = '#projectmasthead {
            background-image: url("' . $project['feat_img'] . '");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 50% 50%;
          }'; 

        }

        break;


      // 1 = background colour
      case 1:

        $override_css = '#projectmasthead { background-color:' . $project['meta']['headerbg'] . ' !important; }';

        break;

      // 2 = image by URL
      case 2:

        if ( $project['bg_img_url'] ){
        
          $override_css = '#projectmasthead {
              background-image: url("' . $project['bg_img_url'] . '");
              background-size: cover;
              background-repeat: no-repeat;
              background-position: 50% 50%;
            }';
        
        }

        break;

      // 3 = video by URL
      case 3:

        if ( $project['bg_video_url'] ){

          $override_css = '#projectmasthead {
              overflow: hidden;
              position: relative;
            }
            #projectmasthead .ui.container {
              position: relative;
            }';

        }

        break;

      // 4 = gradient (prescribed)
      case 4:
        break;

    }


    // hero text colour
    // priority = per project page setting
    // next = per theme template
    // last = fallback
    $hero_text_color = '#FFFFFF';
    // got it as a project page setting?
    if ( isset( $project['meta']['headertextcolour'] ) ){
      $hero_text_color = $project['meta']['headertextcolour'];
    } else {
      
      if ( isset( $attributes['heroTextColour'] ) && !empty( $attributes['heroTextColour'] ) ){
        $hero_text_color = $attributes['heroTextColour'];
      }

    }

    $override_css .= '#projectmasthead .project-pages-hero-h1 h1, 
    body div.project-pages-breadcrumb-wrap a,
    body div.project-pages-breadcrumb-wrap span.project-pages-separator
    {color:' . $hero_text_color . ';-webkit-text-fill-color:' . $hero_text_color . ';}';
  
    // hero alt text colour
    // priority = per project page setting
    // next = per theme template
    // last = fallback
    $hero_text_alt_color = '#CCCCCC';
    // got it as a project page setting?
    if ( isset( $project['meta']['headeralttextcolour'] ) ){
      $hero_text_alt_color = $project['meta']['headeralttextcolour'];
    } else {
      
      if ( isset( $attributes['heroTextAltColour'] ) && !empty( $attributes['heroTextAltColour'] ) ){
        $hero_text_alt_color = $attributes['heroTextAltColour'];
      }

    }

    $override_css .= '#projectmasthead h2,
    #projectmasthead .project-pages-hero-h1 .project-pages-share-wrap span,
    body div.project-pages-breadcrumb-wrap a.active-page
    {color:' . $hero_text_alt_color . ';-webkit-text-fill-color:' . $hero_text_alt_color . ';}';

    if ( $override_css ){

      // dislike putting it out inline, but needs must.
      $html = '<style type="text/css" id="project-pages-override-css">' . $override_css . '</style>';

    }


    // Generate HTML

    $html .= '<div class="ui inverted vertical masthead center aligned segment';
        if ( $project['header_type'] == 4 && isset( $project['bg_gradient'] ) && $project['bg_gradient'] > 0 ){ $html .= ' pp-gradient-' . $project['bg_gradient']; }
    $html .= '" id="projectmasthead">';
          
        // if using a video cover, via project, or fallback, inject it here
        if ( 
              $project['header_type'] == 3 
              &&
              (
                (
                  isset( $project['bg_video_url'] ) && !empty( $project['bg_video_url'] )
                )
                ||
                isset ( $bg_video_fallback ) 

              )

            ){

          $vid_url = $project['bg_video_url'];
          if ( empty( $vid_url ) && isset( $bg_video_fallback ) ){
            $vid_url = $bg_video_fallback;
          }
          $html .= '<video autoplay muted loop class="pp-video-bg"><source src="' . $vid_url . '" type="video/mp4">Your browser does not support HTML5 video.</video>';

        }

    $html .= $breadcrumb_html;

    $html .= '<div class="project-pages-hero-h1 wp-block-group has-global-padding is-layout-constrained wp-container-core-group-is-layout-6 wp-block-group-is-layout-constrained">
                <h1 class="wp-block-heading has-text-align-center has-x-large-font-size">
            ' . $project['title_checked'] . '
          </h1>';
        
        if ( is_array( $project['meta'] ) && isset( $project['meta']['biline'] ) ) $html .= '<h2 class="wp-block-heading has-text-align-center">' . projectPages_textExpose( $project['meta']['biline'] ) . '</h2>';    
        $html .= projectPages_shareOut( $project['title_checked'].' on '.$blog_title, $project['url'], $project['feat_img_checked_empty'], true ); 
    $html .= '</div>
      </div>';

  } // / single


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


    // hero text colour
    // priority = per project page setting
    // next = per theme template
    // last = fallback
    $hero_text_color = '#FFFFFF';
    // got it as a project page setting?
    if ( isset( $project['meta']['headertextcolour'] ) ){
      $hero_text_color = $project['meta']['headertextcolour'];
    } else {
      
      if ( isset( $attributes['heroTextColour'] ) && !empty( $attributes['heroTextColour'] ) ){
        $hero_text_color = $attributes['heroTextColour'];
      }

    }

    $override_css .= '#projectmasthead .project-pages-hero-h1 h1, 
    body div.project-pages-breadcrumb-wrap a,
    body div.project-pages-breadcrumb-wrap span.project-pages-separator
    {color:' . $hero_text_color . ';-webkit-text-fill-color:' . $hero_text_color . ';}';
  
    // hero alt text colour
    // priority = per project page setting
    // next = per theme template
    // last = fallback
    $hero_text_alt_color = '#CCCCCC';
    // got it as a project page setting?
    if ( isset( $project['meta']['headeralttextcolour'] ) ){
      $hero_text_alt_color = $project['meta']['headeralttextcolour'];
    } else {
      
      if ( isset( $attributes['heroTextAltColour'] ) && !empty( $attributes['heroTextAltColour'] ) ){
        $hero_text_alt_color = $attributes['heroTextAltColour'];
      }

    }

    $override_css .= '#projectmasthead h2,
    #projectmasthead .project-pages-hero-h1 .project-pages-share-wrap span,
    body div.project-pages-breadcrumb-wrap a.active-page
    {color:' . $hero_text_alt_color . ';-webkit-text-fill-color:' . $hero_text_alt_color . ';}';

    if ( $override_css ){

      // dislike putting it out inline, but needs must.
      $html = '<style type="text/css" id="project-pages-override-css">' . $override_css . '</style>';

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
            ' . __( 'Project Title Example', 'projectpages' ) . '
          </h1>';
        
        $html .= '<h2 class="wp-block-heading has-text-align-center">' . __( 'Project Bi-line here....', 'projectpages' ) . '</h2>';    
        $html .= projectPages_shareOut( __( 'Example Project Share', 'projectpages' ), '#', '', true, true ); 
    $html .= '</div>
      </div>';

  } // / editor

  return $html;

}