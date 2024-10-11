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

// tag cloud
function projectPages_blocks_render_tag_cloud( $attributes, $content, $block ) { 

  $html = '';

  // attributes
  $specific_tags = ''; if ( isset( $attributes['specifyTags'] ) ){

    $specific_tags = $attributes['specifyTags'];

  }
  $hide_empty = true; if ( isset( $attributes['hideEmpty'] ) ){

    $hide_empty = $attributes['hideEmpty'];

  }
  

  // get tags
  $tags = projectPages_getTags( $hide_empty, true, false, $specific_tags );

  if ( is_array( $tags ) && count( $tags ) > 0 ){

    $html = '<div class="project-pages-tag-cloud project-pages-align-' . esc_attr( $attributes['align'] ) . '">';

      // got label?
      if ( isset( $attributes['label'] ) && !empty( $attributes['label'] ) ){

        $html .= '<span>' . esc_html( $attributes['label'] ) . '</span>';

        $tag_html = '';

        foreach ( $tags as $tag ){

            if ( !empty( $tag_html ) ){
              
              $tag_html .= ', ';

            }

            $tag_html .= '<a href="' . get_term_link( $tag['ID'] ) . '">' . $tag['name'] . '</a>';

            
        }

        $html .= $tag_html;

      }

    $html .= '</div>';

  } else {

    $html = projectPages_no_projects_no_cry();
    
  }

  return $html;

}