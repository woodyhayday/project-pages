<?php 
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */

// helper, retrieves queried post type, regardless of page
// e.g. works on empty archive pages.
function wh_get_queried_post_type(){

	global $wp_query;
	$query_var = ( array ) $wp_query->get( 'post_type' );
	
	if ( is_array( $query_var ) && count( $query_var ) == 1 ){

		return $query_var[0];
	}

	return false;
	
}

// gets a random published url of a cpt
function wh_get_random_published_url( $post_type = 'post' ){

      $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'orderby' => 'rand', 
        'posts_per_page' => '1'
      );

      $post_query = new WP_Query( $args );
      $posts = array(); if ( $post_query->posts ) $posts = $post_query->posts;

      if ( is_array( $posts ) && count( $posts ) > 0 ){

        return esc_url( get_permalink( $posts[0] ) );

      }

      return false;
}

// checks an email
function wh_verify_email( $email_address = '' ){

    if ( filter_var( $email_address, FILTER_VALIDATE_EMAIL ) ) {
     
        return true;
    
    }

    return false;

}

// returns a string after x
function wh_str_after_x( $str = '', $x = '' ){

    if ( !strpos($str, $x ) ) return $str;

    $pos = strpos( $str, $x );

    return substr( $str, $pos + strlen( $x ) );

}

// social shares via link
function wh_share_via_fb ( $url ){
	return esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $url ) );
}

function wh_share_via_x ( $tweet ){
	return str_replace( '%3Cbr%3E', '%0A', esc_url( 'https://twitter.com/intent/tweet?text=' . urlencode( $tweet ) ) );
}

function wh_share_via_li ( $url ){
	return esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( $url ) );
}

function wh_share_via_telegram ( $url, $message = '' ){
	return esc_url( 'https://t.me/share/url?url=' . urlencode( $url ) . '&text=' . urlencode( $message ) );
}

function wh_dashicons_arr(){

	return array(
        'dashicons-menu',
        'dashicons-dashboard',
        'dashicons-admin-site',
        'dashicons-admin-media',
        'dashicons-admin-page',
        'dashicons-admin-comments',
        'dashicons-admin-appearance',
        'dashicons-admin-plugins',
        'dashicons-admin-users',
        'dashicons-admin-tools',
        'dashicons-admin-settings',
        'dashicons-admin-network',
        'dashicons-admin-generic',
        'dashicons-admin-home',
        'dashicons-admin-collapse',
        'dashicons-admin-links',
        'dashicons-admin-post',
        'dashicons-format-standard',
        'dashicons-format-image',
        'dashicons-format-gallery',
        'dashicons-format-audio',
        'dashicons-format-video',
        'dashicons-format-links',
        'dashicons-format-chat',
        'dashicons-format-status',
        'dashicons-format-aside',
        'dashicons-format-quote',
        'dashicons-welcome-write-blog',
        'dashicons-welcome-edit-page',
        'dashicons-welcome-add-page',
        'dashicons-welcome-view-site',
        'dashicons-welcome-widgets-menus',
        'dashicons-welcome-comments',
        'dashicons-welcome-learn-more',
        'dashicons-image-crop',
        'dashicons-image-rotate-left',
        'dashicons-image-rotate-right',
        'dashicons-image-flip-vertical',
        'dashicons-image-flip-horizontal',
        'dashicons-undo',
        'dashicons-redo',
        'dashicons-editor-bold',
        'dashicons-editor-italic',
        'dashicons-editor-ul',
        'dashicons-editor-ol',
        'dashicons-editor-quote',
        'dashicons-editor-alignleft',
        'dashicons-editor-aligncenter',
        'dashicons-editor-alignright',
        'dashicons-editor-insertmore',
        'dashicons-editor-spellcheck',
        'dashicons-editor-distractionfree',
        'dashicons-editor-expand',
        'dashicons-editor-contract',
        'dashicons-editor-kitchensink',
        'dashicons-editor-underline',
        'dashicons-editor-justify',
        'dashicons-editor-textcolor',
        'dashicons-editor-paste-word',
        'dashicons-editor-paste-text',
        'dashicons-editor-removeformatting',
        'dashicons-editor-video',
        'dashicons-editor-customchar',
        'dashicons-editor-outdent',
        'dashicons-editor-indent',
        'dashicons-editor-help',
        'dashicons-editor-strikethrough',
        'dashicons-editor-unlink',
        'dashicons-editor-rtl',
        'dashicons-editor-break',
        'dashicons-editor-code',
        'dashicons-editor-paragraph',
        'dashicons-align-left',
        'dashicons-align-right',
        'dashicons-align-center',
        'dashicons-align-none',
        'dashicons-lock',
        'dashicons-calendar',
        'dashicons-visibility',
        'dashicons-post-status',
        'dashicons-edit',
        'dashicons-post-trash',
        'dashicons-trash',
        'dashicons-external',
        'dashicons-arrow-up',
        'dashicons-arrow-down',
        'dashicons-arrow-left',
        'dashicons-arrow-right',
        'dashicons-arrow-up-alt',
        'dashicons-arrow-down-alt',
        'dashicons-arrow-left-alt',
        'dashicons-arrow-right-alt',
        'dashicons-arrow-up-alt2',
        'dashicons-arrow-down-alt2',
        'dashicons-arrow-left-alt2',
        'dashicons-arrow-right-alt2',
        'dashicons-leftright',
        'dashicons-sort',
        'dashicons-randomize',
        'dashicons-list-view',
        'dashicons-exerpt-view',
        'dashicons-hammer',
        'dashicons-art',
        'dashicons-migrate',
        'dashicons-performance',
        'dashicons-universal-access',
        'dashicons-universal-access-alt',
        'dashicons-tickets',
        'dashicons-nametag',
        'dashicons-clipboard',
        'dashicons-heart',
        'dashicons-megaphone',
        'dashicons-schedule',
        'dashicons-wordpress',
        'dashicons-wordpress-alt',
        'dashicons-pressthis,',
        'dashicons-update,',
        'dashicons-screenoptions',
        'dashicons-info',
        'dashicons-cart',
        'dashicons-feedback',
        'dashicons-cloud',
        'dashicons-translation',
        'dashicons-tag',
        'dashicons-category',
        'dashicons-archive',
        'dashicons-tagcloud',
        'dashicons-text',
        'dashicons-media-archive',
        'dashicons-media-audio',
        'dashicons-media-code',
        'dashicons-media-default',
        'dashicons-media-document',
        'dashicons-media-interactive',
        'dashicons-media-spreadsheet',
        'dashicons-media-text',
        'dashicons-media-video',
        'dashicons-playlist-audio',
        'dashicons-playlist-video',
        'dashicons-yes',
        'dashicons-no',
        'dashicons-no-alt',
        'dashicons-plus',
        'dashicons-plus-alt',
        'dashicons-minus',
        'dashicons-dismiss',
        'dashicons-marker',
        'dashicons-star-filled',
        'dashicons-star-half',
        'dashicons-star-empty',
        'dashicons-flag',
        'dashicons-share',
        'dashicons-share1',
        'dashicons-share-alt',
        'dashicons-share-alt2',
        'dashicons-twitter',
        'dashicons-rss',
        'dashicons-email',
        'dashicons-email-alt',
        'dashicons-facebook',
        'dashicons-facebook-alt',
        'dashicons-networking',
        'dashicons-googleplus',
        'dashicons-location',
        'dashicons-location-alt',
        'dashicons-camera',
        'dashicons-images-alt',
        'dashicons-images-alt2',
        'dashicons-video-alt',
        'dashicons-video-alt2',
        'dashicons-video-alt3',
        'dashicons-vault',
        'dashicons-shield',
        'dashicons-shield-alt',
        'dashicons-sos',
        'dashicons-search',
        'dashicons-slides',
        'dashicons-analytics',
        'dashicons-chart-pie',
        'dashicons-chart-bar',
        'dashicons-chart-line',
        'dashicons-chart-area',
        'dashicons-groups',
        'dashicons-businessman',
        'dashicons-id',
        'dashicons-id-alt',
        'dashicons-products',
        'dashicons-awards',
        'dashicons-forms',
        'dashicons-testimonial',
        'dashicons-portfolio',
        'dashicons-book',
        'dashicons-book-alt',
        'dashicons-download',
        'dashicons-upload',
        'dashicons-backup',
        'dashicons-clock',
        'dashicons-lightbulb',
        'dashicons-microphone',
        'dashicons-desktop',
        'dashicons-tablet',
        'dashicons-smartphone',
        'dashicons-smiley'
    );
}

// will this always work? :thinking-face:
function wh_get_current_wp_url(){
    
    global $wp;
    return add_query_arg( $wp->query_vars, home_url( $wp->request ) );
    
}


// url param check
function wh_has_get_parameters( $backend_check = true, $pageArr = array(), $param_keypairs = array(), $params_present = false ) {

    global $pagenow;

    // are we on the back end?
    if ( $backend_check && ! is_admin() ) {
        return false;
    }

    // check in array (like  array( 'post.php', 'post-new.php' ))
    if ( in_array( $pagenow, $pageArr ) ) {
            
        if ( is_array( $param_keypairs ) ){

            // check param key value pairs - return false if any not present
            foreach ( $param_keypairs as $k => $v ) {
                if ( ! isset( $_GET[ $k ] ) || $_GET[ $k ] !== $v ) {
                    return false;
                }
            }

        }

        if ( is_array( $params_present ) ){

            // check params - return false if any not present
            foreach ( $params_present as $p ) {
                if ( ! isset( $_GET[ $p ] ) ) {
                    return false;
                }
            }

        }

        // has all params
        return true;

    }

    return false;
}