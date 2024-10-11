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

// Add a log
function projectPages_AJAX_add_log(){

	// Check nonce
	check_ajax_referer( 'project-pages-log-nonce', 'nonce' );

	// retrieve data
	$post_id = false; if ( isset( $_POST['post_id'] ) ){ $post_id = (int)sanitize_text_field( $_POST['post_id'] ); }
	$log = array();
	$log['title'] = ''; if ( isset( $_POST['title'] ) ){ $log['title'] = sanitize_text_field( $_POST['title'] ); }
	$log['date'] = ''; if ( isset( $_POST['date'] ) ){ $log['date'] = sanitize_text_field( $_POST['date'] ); }
	$log['icon'] = ''; if ( isset( $_POST['icon'] ) ){ $log['icon'] = sanitize_text_field( $_POST['icon'] ); }
	$log['body'] = ''; if ( isset( $_POST['body'] ) ){ $log['body'] = htmlspecialchars( $_POST['body'] ); }

	$new_log = projectPages_add_log( $post_id, $log, true );

	if ( $new_log ){
		
		projectPages_AJAX_success( $new_log );

	}

	projectPages_AJAX_error( array( 'msg' => __( 'Failed to add log', 'projectpages' ) ) );

} add_action( 'wp_ajax_projectPagesAddLog', 'projectPages_AJAX_add_log' );

// Update a log
function projectPages_AJAX_update_log(){

	// Check nonce
	check_ajax_referer( 'project-pages-log-nonce', 'nonce' );

	// retrieve data
	$log_post_id = false; if ( isset( $_POST['post_id'] ) ){ $log_post_id = (int)sanitize_text_field( $_POST['post_id'] ); }
	$log = array();
	$log['title'] = ''; if ( isset( $_POST['title'] ) ){ $log['title'] = sanitize_text_field( $_POST['title'] ); }
	$log['date'] = ''; if ( isset( $_POST['date'] ) ){ $log['date'] = sanitize_text_field( $_POST['date'] ); }
	$log['icon'] = ''; if ( isset( $_POST['icon'] ) ){ $log['icon'] = sanitize_text_field( $_POST['icon'] ); }
	$log['body'] = ''; if ( isset( $_POST['body'] ) ){ $log['body'] = htmlspecialchars( $_POST['body'] ); }

	// check log exists
	$log_post = projectPages_getLog( $log_post_id, false, false );

	if ( $log_post ){

		$updated_log = projectPages_update_log( $log_post_id, $log, true );

		if ( $updated_log ){
			
			projectPages_AJAX_success( $updated_log );

		}

	}

	projectPages_AJAX_error( array( 'msg' => __( 'Failed to update log', 'projectpages' ) ) );

} add_action( 'wp_ajax_projectPagesUpdateLog', 'projectPages_AJAX_update_log' );

// Delete a log
function projectPages_AJAX_delete_log(){

	// Check nonce
	check_ajax_referer( 'project-pages-log-nonce', 'nonce' );

	// retrieve data
	$log_post_id = false; if ( isset( $_POST['post_id'] ) ){ $log_post_id = (int)sanitize_text_field( $_POST['post_id'] ); }

	// check log exists
	$log_post = projectPages_getLog( $log_post_id, false, false );

	if ( $log_post ){

		$deleted_log = projectPages_delete_log( $log_post_id );

		if ( $deleted_log ){
			
			projectPages_AJAX_success( $deleted_log );

		}

	}

	projectPages_AJAX_error( array( 'msg' => __( 'Failed to delete log', 'projectpages' ) ) );

} add_action( 'wp_ajax_projectPagesDeleteLog', 'projectPages_AJAX_delete_log' );


// Hide feedback
function projectPages_AJAX_hide_feedback(){

	// Check nonce
	check_ajax_referer( 'project-pages-hide-feedback-nonce', 'nonce' );

	// set transient for a week
	set_transient( 'projectpages-hide-feedback-metabox', true, 604800 );
			
	// return
	projectPages_AJAX_success( array( 'hidden' => true ) );	

} add_action( 'wp_ajax_projectPagesHideFeedback', 'projectPages_AJAX_hide_feedback' );


// Join
function projectPages_AJAX_join(){

	// Check nonce
	check_ajax_referer( 'project-pages-join-nonce', 'nonce' );

	// retrieve data
	$email_address = ''; if ( isset( $_POST['email_address'] ) ){ $email_address = sanitize_email( $_POST['email_address'] ); }
	$name = ''; if ( isset( $_POST['name'] ) ){ $name = sanitize_text_field( $_POST['name'] ); }
	$src = ''; if ( isset( $_POST['src'] ) ){ $src = sanitize_text_field( $_POST['src'] ); }
	$solemnly_swear_to_make_cool_sht = false; if ( isset( $_POST['solemnly'] ) ){ $solemnly_swear_to_make_cool_sht = true; }

	if ( wh_verify_email( $email_address ) ){

		// attempt to join
		$join_request = projectPages_joinCommunity( $email_address, $name, $solemnly_swear_to_make_cool_sht, $src );

		if ( !is_wp_error( $join_request ) && wp_remote_retrieve_response_code( $join_request ) == 200 ) {

			$response = json_decode( wp_remote_retrieve_body( $join_request ) );

			if ( $response && $response->success ){

				// return
				projectPages_AJAX_success( array( 'joined' => true ) );	

			}

		}

	}

	// fail
	projectPages_AJAX_error( array( 'failed' => true ) );

} add_action( 'wp_ajax_projectPagesJoin', 'projectPages_AJAX_join' );


// send error response
function projectPages_AJAX_error( $error_object = '', $status_code = 500 ){
	wp_send_json_error( $error_object, $status_code );
}

// send success response
function projectPages_AJAX_success( $success_object = '' ){

	header( 'Content-Type: application/json' );
	echo json_encode( $success_object, true );
	exit();

}