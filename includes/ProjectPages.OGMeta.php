<?php 
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 05/07/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */


add_action('wp_head', 'projectPages_og_meta_intercept', 1);
function projectPages_og_meta_intercept(){

    $add_og_meta = projectPages_getSetting('add_og_meta');

    // PRO hook
    $add_og_meta = apply_filters( 'project_pages_add_og_meta', $add_og_meta );

    if ( $add_og_meta &&
          ( is_singular( 'projectpage' ) || is_post_type_archive( 'projectpage' ) || is_tax( 'projectpagetag' ) ) 
        ) {

        $blog_title = get_bloginfo('name');

        // simple meta output
        $extra_meta_output = '';

        // single project page
        if ( is_singular( 'projectpage' ) ){

          // description
          $description = projectPages_getProjectSummary( get_the_ID() ) . ' ' . sprintf( __( '(Project on %s)', 'projectpages'), $blog_title );

          // image (use the featured image)
          $image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );


        }

        // project page archive
        if ( is_post_type_archive( 'projectpage' ) ){

          // description
          // hmmm will depend on users choice for loop setting really. This seems most honest
          $project_count = projectPages_getProjectCount_filtered( projectPages_statuses_active_archived_completed() );
          $description = sprintf( __( 'Browse the %1$s Projects on %2$s', 'projectpages'), $project_count, $blog_title );

          // image (use the site logo where available) 
          $image_url = projectPages_get_custom_logo_url();
          
        }

        // taxonomy
        if ( is_tax( 'projectpagetag' ) ){

            // retrieve term
            $term = get_queried_object();

            // description
            $tagged_count = projectPages_getProjectCount_filtered( projectPages_statuses_active_archived_completed(), $term->name );
            $description = sprintf( __( 'Browse %s Projects tagged "%s" on %s', 'projectpages' ), $tagged_count, ( !empty( $term->name ) ? $term->name : __( 'Tag', 'projectpages' ) ), $blog_title );
          
            // image (use the site logo where available) 
            $image_url = projectPages_get_custom_logo_url();
        } 

        // image
        // The recommended image ratio for an og:image is 1.91:1. The optimal size would be 1200 x 630.
        // PRO Version has auto-image gen for nicer images :)
        if ( empty( $image_url ) ){

          $image_url = PROJECTPAGES_URL . 'i/project-pages-social-meta.png';

        }
          
        $extra_meta_output .= '<meta property="og:image" content="'. esc_url( $image_url )   .'" />';

        // description
        if ( empty( $description ) ){

          $description = sprintf( __( 'Project Pages on %s', 'projectpages'), $blog_title );

        }

        // description should be between 55 and 200 characters long, with a maximum of 300
        $extra_meta_output .= '<meta property="og:description" content="'. esc_attr( $description )   .'" />';

        // filter + dump
        echo apply_filters( 'project_pages_og_meta', $extra_meta_output );
        
    }
}

// returns site logo where available
function projectPages_get_custom_logo_url()
{
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );

    if ( is_array( $image ) && isset( $image[0] ) ){
      
      return $image[0];

    } 

    return '';
}