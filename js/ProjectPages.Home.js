/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */
jQuery(document).ready(function(){


    // hero click join
    jQuery('#project-pages-hero-sub-go').click(function(){

        // in prog
        jQuery('#project-pages-hero-sub-go').attr('disabled','disabled');
        jQuery('#project-pages-hero-sub-failed').hide();

        var email = jQuery('#project-pages-hero-sub').val();
        var solemnly_check = false;
        if ( jQuery('#project-pages-hero-sub-solemnly').is(':checked')) {
            solemnly_check = true;
        }

        // if valid email, go
        if ( projectPages_verify_email( email ) ){

            var data = {
                email_address: email,
                solemnly: solemnly_check,
                src: 'wp-plugin-hero'
            };

            jQuery('#project-pages-hero-sub-go').text( window.projectPages_labels.joining );
        
            projectPages_join( data, function(r){

                jQuery('#project-pages-hero-sub-wrap').html( window.projectPages_joined_html );
                jQuery('#project-pages-bonus-sub-wrap').html( window.projectPages_joined_html );
                jQuery('#project-pages-hero-sub-go').removeAttr('disabled');

            }, function(r){

                jQuery('#project-pages-hero-sub-failed').show();
                jQuery('#project-pages-hero-sub-go').removeAttr('disabled');

            });

        } else {

            // nope
            jQuery('#project-pages-hero-sub').addClass('is-invalid');
            jQuery('#project-pages-hero-sub-go').removeAttr('disabled');

        }

    });

    // bonus click join
    jQuery('#project-pages-bonus-sub-go').click(function(){

        jQuery('#project-pages-bonus-sub-go').attr('disabled','disabled');
        jQuery('#project-pages-bonus-sub-failed').hide();

        var email = jQuery('#project-pages-bonus-sub').val();
        var solemnly_check = false;
        if ( jQuery('#project-pages-bonus-sub-solemnly').is(':checked')) {
            solemnly_check = true;
        }

        // if valid email, go
        if ( projectPages_verify_email( email ) ){

            var data = {
                email_address: email,
                solemnly: solemnly_check,
                src: 'wp-plugin-bonus'
            };

            jQuery('#project-pages-bonus-sub-go').text( window.projectPages_labels.joining );

            projectPages_join( data, function(r){

                jQuery('#project-pages-hero-sub-wrap').html( window.projectPages_joined_html );
                jQuery('#project-pages-bonus-sub-wrap').html( window.projectPages_joined_html );
                jQuery('#project-pages-bonus-sub-go').removeAttr('disabled');

            }, function(r){

                jQuery('#project-pages-bonus-sub-failed').show();
            jQuery('#project-pages-bonus-sub-go').removeAttr('disabled');

            });

        } else {

            // nope
            jQuery('#project-pages-bonus-sub').addClass('is-invalid');
            jQuery('#project-pages-bonus-sub-go').removeAttr('disabled');

        }

    });

    // watch for validity
    jQuery('#project-pages-hero-sub, #project-pages-bonus-sub').change(function(){

        if ( jQuery(this).hasClass('is-invalid') ){

            if ( projectPages_verify_email( jQuery(this).val() ) ){
                jQuery(this).removeClass('is-invalid');
            }

        }

    });

});



// join
function projectPages_join( postbag, successcb, errcb ){

    if (!window.projectPages_ajax_blocker){

        // set blocker
        window.projectPages_ajax_blocker = true;

            // postbag!
            var data = {
                'action': 'projectPagesJoin',
                'nonce': window.projectPages_join_nonce
            };
            jQuery.extend( postbag, data );

            // Send 
            jQuery.ajax({
                  type: "POST",
                  url: ajaxurl,
                  "data": postbag,
                  dataType: 'json',
                  timeout: 20000,
                  success: function(response) {

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

// verify email
function projectPages_verify_email( email ) {
    var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test( email );
}