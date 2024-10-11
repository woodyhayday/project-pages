/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */
jQuery(document).ready(function(){

	// modified submit box
    jQuery("#_submitdiv").attr( 'id', "submitdiv");

    // log date picker
  	jQuery('#project-pages-add-log-datepicker').datepicker({
		format: 'mm/dd/yyyy',
		orientation: 'bottom left',
		autoclose: true
	});
  	jQuery('#project-pages-edit-log-datepicker').datepicker({
		format: 'mm/dd/yyyy',
		orientation: 'bottom left',
		autoclose: true
	});

	// header type switch
	jQuery('#whpp_headerimg').on('change', function() {

	    jQuery('.wh-headerimg-cascade').hide();

		// v2.0 1 = color, -1 = featimg, 2 = imgurl, 3 = vidurl, 4 = gradient
	    switch ( parseInt( jQuery(this).find(":selected").val() ) ){

	    	// feat img
	    	case -1: 
   		
	    		break;

	    	// color
	    	case 1: 

	    		jQuery('#headerbg').show();
	    		break;

	    	// image url
	    	case 2: 

	    		jQuery('#headerbg_imgurl').show();
	    		break;

	    	// video url
	    	case 3: 

	    		jQuery('#headerbg_vidurl').show();
	    		break;

	    	// gradient
	    	case 4: 

	    		jQuery('#headerbg_gradient').show();
	    		break;

	    }

	});

	// add new log
	jQuery('#project-pages-add-log-submit').click(function(){

		projectPages_add_new_log();

	});

	// edit log
	jQuery('#project-pages-edit-log-submit').click(function(){

		projectPages_update_log();

	});

	// delete log
	jQuery('#project-pages-edit-log-delete').click(function(){

		projectPages_delete_log_prompt( jQuery( '#project-pages-edit-log-id' ).val() );

	});

	// delete log, yes I'm sure already
	jQuery('#project-pages-delete-log-certain').click(function(){

		projectPages_delete_log();

	});

	// delete log, backtrack
	jQuery('#project-pages-delete-log-cancel').click(function(){

		// hide delete modal
		window.projectPages_log_delete_prompt_modal.hide();

		// show edit modal
		window.projectPages_log_editor_modal.show();


	});

	// hide feedback box
	jQuery('#project-pages-hide-feedback').click(function(){

		projectPages_hide_feedback();

	});

	// render logs
	projectPages_render_logs();

	// log binds
	projectPages_log_editor_bind();

});


function projectPages_select_gradient( gradient ){

	// update hidden input
	jQuery('#whpp_headerbg_gradient').val(gradient);

	// update example
	jQuery('#pp-gradient-example-wrap').removeClass().addClass('d-flex align-items-center pp-gradient-' + gradient);
	jQuery('#pp-gradient-example span').text(gradient);

}

// draw any logs
function projectPages_render_logs(){

    if (!window.projectPages_render_blocker){

        // set blocker
        window.projectPages_render_blocker = true;

        if ( window.projectPages_logs && window.projectPages_logs.length ){

        	var ordered_logs = window.projectPages_logs;
        	ordered_logs.sort( projectPages_logs_sort_by_date );

        	var html = '';
        	
        	jQuery.each( window.projectPages_logs, function ( index, log ) {
			    
        		html 	+= '<a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3 project-pages-single-log" aria-current="true" data-logid="' + log.ID + '">'
                         + 		'<span class="dashicons ' + log.icon + '"></span>'
                         +		'<div class="d-flex gap-2 w-100 justify-content-between">'
                         +  		'<div>'
                         +    			'<h3>' + log.title + '</h3>'
                         +    			'<div class="mb-0 opacity-75 project-pages-3-lines">' + projectPages_decodeHtml( log.body ) + '</div>'
                         +  		'</div>'
                         +  		'<small class="opacity-50 text-nowrap">' + log.date + '</small>'
                         +		'</div>'
                         +	'</a>';

			});

			// hide no logs
			jQuery('#project-pages-no-logs').hide();

        	// set
        	jQuery('#project-pages-log-wrap').html( html )
        	jQuery('#project-pages-log-outer-wrap').show();

        	// rebind links to edit modals
        	setTimeout(function(){

        		jQuery('.project-pages-single-log').each( function ( index ){

        			var log_id = jQuery(this).attr('data-logid');
        			jQuery(this).click(function() {
				        projectPages_open_log_edit_modal( log_id );
				        return false;
				    });

        		});

        	},0);

        }

        // unset blocker
        window.projectPages_render_blocker = false;
    }

}


function projectPages_open_log_edit_modal( id ){
	
	// retrieve log
	var log = projectPages_get_log( id );

	if ( log ){

		// fill in data
		jQuery('#project-pages-edit-log-id').val( id );
		jQuery('#project-pages-edit-log-title').val( log.title );
		jQuery('#project-pages-edit-log-date').val( log.date );
		jQuery('#project-pages-edit-log-dashicon').val( log.icon );
		dashiconsPickerChange( '#project-pages-edit-log-dashicon', log.icon );
		//tinyMCE.activeEditor.setContent( projectPages_decodeHtml( log.body ) );
		tinyMCE.get('project_page_edit_log_body').setContent( projectPages_decodeHtml( log.body ) );



		window.projectPages_log_editor_modal.show();

	} else {

		// err

	}

}

function projectPages_get_log( id ){

	var the_log = false;

    jQuery.each( window.projectPages_logs, function ( index, log ) {

    	if ( log.ID == id ){

    		the_log = log;
    		return;

    	}


    });


	return the_log;

}

// process new log
function projectPages_add_new_log(){

	// vars
	var log_title = jQuery('#project-pages-add-log-title').val();
	var log_date = jQuery('#project-pages-add-log-date').val();
	var log_icon = jQuery('#project-pages-log-dashicon').val();
	var log_body = tinyMCE.get('project_page_add_log_body').getContent(); //tinyMCE.activeEditor.getContent();

	projectPages_add_new_log_post( {
		post_id: window.projectPages_post_id,
		title: log_title,
		date: log_date,
		body: log_body,
		icon: log_icon,
		nonce: window.projectPages_log_nonce
	}, function (r){

		// redraw logs
		projectPages_render_logs();

		// close modal
		var myModalEl = document.getElementById('project-pages-add-log-modal');
		var modal = bootstrap.Modal.getInstance(myModalEl); // Returns a Bootstrap modal instance
		modal.hide();

		// empty modal fields
		jQuery('#project-pages-add-log-title').val('');
		jQuery('#project-pages-add-log-date').val('');
		jQuery('#project-pages-add-log-dashicon').val('dashicons-arrow-right');
		dashiconsPickerChange( '#project-pages-log-dashicon', 'dashicons-arrow-right' );		
		tinyMCE.get('project_page_add_log_body').setContent(''); //tinyMCE.activeEditor.setContent('');

	}, function (r){


	});
	
}

// process update log
function projectPages_update_log(){

	// vars
	var log_id = jQuery('#project-pages-edit-log-id').val();
	var log_title = jQuery('#project-pages-edit-log-title').val();
	var log_date = jQuery('#project-pages-edit-log-date').val();
	var log_icon = jQuery('#project-pages-edit-log-dashicon').val();
	var log_body = tinyMCE.get('project_page_edit_log_body').getContent(); //tinyMCE.activeEditor.getContent();

	projectPages_update_log_post( {
		post_id: log_id,
		title: log_title,
		date: log_date,
		body: log_body,
		icon: log_icon,
		nonce: window.projectPages_log_nonce
	}, function (r){

		// redraw logs
		projectPages_render_logs();

		// close modal
		window.projectPages_log_editor_modal.hide();

		// empty modal fields
		jQuery('#project-pages-edit-log-id').val('');
		jQuery('#project-pages-edit-log-title').val('');
		jQuery('#project-pages-edit-log-date').val('');
		jQuery('#project-pages-edit-log-dashicon').val('dashicons-arrow-right');
		dashiconsPickerChange( '#project-pages-edit-log-dashicon', 'dashicons-arrow-right' );
		tinyMCE.get('project_page_edit_log_body').setContent(''); //tinyMCE.activeEditor.setContent('');

	}, function (r){


	});

}

// log delete warning
function projectPages_delete_log_prompt( log_post_id ){

	// set value
	jQuery('#project-pages-delete-this-log').val( log_post_id );

	// hide edit modal
	window.projectPages_log_editor_modal.hide();

	// show modal
	window.projectPages_log_delete_prompt_modal.show();

}

// delete the log, user has seen warning.
function projectPages_delete_log(){

	// vars
	var log_id = jQuery('#project-pages-delete-this-log').val();

	projectPages_delete_log_post( {
		post_id: log_id,
		nonce: window.projectPages_log_nonce
	}, function (r){

		// redraw logs
		projectPages_render_logs();

		// close modal
		window.projectPages_log_delete_prompt_modal.hide();

		// empty modal fields
		jQuery('#project-pages-delete-this-log').val('');

	}, function (r){


	});

}



// add the new log
function projectPages_add_new_log_post( postbag, successcb, errcb ){

    if (!window.projectPages_ajax_blocker){

        // set blocker
        window.projectPages_ajax_blocker = true;

            // postbag!
            var data = {
                'action': 'projectPagesAddLog'
            };
            jQuery.extend( postbag, data );

            // Send 
            jQuery.ajax({
                  type: "POST",
                  url: ajaxurl, // admin side is just ajaxurl not wptbpAJAX.ajaxurl,
                  "data": postbag,
                  dataType: 'json',
                  timeout: 20000,
                  success: function(response) {

                        // update the object
						window.projectPages_logs.push( response );

                        // any success callback?
                        if (typeof successcb == 'function') successcb(response);

                        // unset blocker
                        window.projectPages_ajax_blocker = false;

                  },
                  error: function(response){ 

                        // temp debug
                        console.error("Error: ",response);

                        // any error callback?
                        if (typeof errcb == 'function') errcb(response);

                        // unset blocker
                        window.projectPages_ajax_blocker = false;

                  }

            });


    } // / not blocked


}


// update the log
function projectPages_update_log_post( postbag, successcb, errcb ){

    if (!window.projectPages_ajax_blocker){

        // set blocker
        window.projectPages_ajax_blocker = true;

            // postbag!
            var data = {
                'action': 'projectPagesUpdateLog'
            };
            jQuery.extend( postbag, data );

            // Send 
            jQuery.ajax({
                  type: "POST",
                  url: ajaxurl, // admin side is just ajaxurl not wptbpAJAX.ajaxurl,
                  "data": postbag,
                  dataType: 'json',
                  timeout: 20000,
                  success: function(response) {

                        // update the object
                  		projectPages_logs_update_log_local( response );

                        // any success callback?
                        if (typeof successcb == 'function') successcb(response);

                        // unset blocker
                        window.projectPages_ajax_blocker = false;

                  },
                  error: function(response){ 

                        // temp debug
                        console.error("Error: ",response);

                        // any error callback?
                        if (typeof errcb == 'function') errcb(response);

                        // unset blocker
                        window.projectPages_ajax_blocker = false;

                  }

            });


    } // / not blocked


}

// delete the log
function projectPages_delete_log_post( postbag, successcb, errcb ){

    if (!window.projectPages_ajax_blocker){

        // set blocker
        window.projectPages_ajax_blocker = true;

            // postbag!
            var data = {
                'action': 'projectPagesDeleteLog'
            };
            jQuery.extend( postbag, data );

            // Send 
            jQuery.ajax({
                  type: "POST",
                  url: ajaxurl, // admin side is just ajaxurl not wptbpAJAX.ajaxurl,
                  "data": postbag,
                  dataType: 'json',
                  timeout: 20000,
                  success: function(response) {

                        // update the object
                  		projectPages_logs_remove_log_local( response );

                        // any success callback?
                        if (typeof successcb == 'function') successcb(response);

                        // unset blocker
                        window.projectPages_ajax_blocker = false;

                  },
                  error: function(response){ 

                        // temp debug
                        console.error("Error: ",response);

                        // any error callback?
                        if (typeof errcb == 'function') errcb(response);

                        // unset blocker
                        window.projectPages_ajax_blocker = false;

                  }

            });


    } // / not blocked


}



// hides feedback metabox for a week
function projectPages_hide_feedback( ){

    if (!window.projectPages_ajax_blocker){

        // set blocker
        window.projectPages_ajax_blocker = true;

        // postbag!
        var data = {
            'action': 'projectPagesHideFeedback',
			nonce: window.projectPages_feedback_nonce
        };

        // Send 
        jQuery.ajax({
              type: "POST",
              url: ajaxurl, // admin side is just ajaxurl not wptbpAJAX.ajaxurl,
              "data": data,
              dataType: 'json',
              timeout: 20000,
              success: function(response) {                  		

                    // unset blocker
                    window.projectPages_ajax_blocker = false;

              },
              error: function(response){ 

                    // unset blocker
                    window.projectPages_ajax_blocker = false;

              }

        });

    } // / not blocked

    // remove metabox
    jQuery('#pp_feedback').remove();


}


// updates a log which is already in the stack
function projectPages_logs_update_log_local( log_obj ){

	var new_stack = [];

    jQuery.each( window.projectPages_logs, function ( index, log ) {

    	if ( log.ID == log_obj.ID ){

    		new_stack.push( log_obj );

    	} else {

    		new_stack.push( log );

    	}

    });

    window.projectPages_logs = new_stack;

}

// removes a log from the stack
function projectPages_logs_remove_log_local( log_obj ){

	var new_stack = [];

    jQuery.each( window.projectPages_logs, function ( index, log ) {

    	if ( log.ID == log_obj.ID ){

    		// silence.

    	} else {

    		new_stack.push( log );

    	}

    });

    window.projectPages_logs = new_stack;

}

function projectPages_log_editor_bind(){

 	// initiate editor modal ahead of time
	window.projectPages_log_editor_modal = new bootstrap.Modal( document.getElementById( 'project-pages-edit-log-modal' ), {	  
	  keyboard: false
	});

 	// initiate prompt modal ahead of time
	window.projectPages_log_delete_prompt_modal = new bootstrap.Modal( document.getElementById( 'project-pages-delete-log' ), {	  
	  keyboard: false
	});

}

// catch for dashicons picker (via mod), updates example
function dashiconsPickerChange( target, new_ico_class ){

	if ( target == '#project-pages-log-dashicon'){
		
		// add
		jQuery('#project-pages-log-dashicon-icon').removeClass().addClass( 'dashicons ' + new_ico_class );

	} else if ( target == '#project-pages-edit-log-dashicon' ){
		
		// edit
		jQuery('#project-pages-edit-log-dashicon-icon').removeClass().addClass( 'dashicons ' + new_ico_class );

	}

}


function projectPages_decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

function projectPages_logs_sort_by_date( a, b ) {
  if ( a.timestamp < b.timestamp ){
    return -1;
  }
  if ( a.timestamp > b.timestamp ){
    return 1;
  }
  return 0;
}

function projectPages_date_toTimestamp(strDate){
   var datum = Date.parse(strDate);
   return datum/1000;
}