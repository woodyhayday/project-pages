/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */
jQuery(document).ready(function(){

	// any additional work we want to do to settings page separate to the vue app.
	projectPages_bind_extras();

});


function projectPages_bind_extras(){

	// template explainers, init set:
	projectPages_display_template_explainers();

	// bind template mode changes
	jQuery('#ppsetting_template_mode').change(function() {

		// update
		projectPages_display_template_explainers();

	});

	// permalink root init set:
	projectPages_permalink_root_changes( true );

	// bind permalink root changes
	jQuery('#ppsetting_permalink_root').change(function() {

		// update
		projectPages_permalink_root_changes( false );

	});


}


function projectPages_display_template_explainers(){

	// toggle
	if ( jQuery('#ppsetting_template_mode').val() == 'legacy' ){

		jQuery('.project-pages-template-mode-hide-default').addClass('pp-hide');
		jQuery('.project-pages-template-mode-hide-legacy').removeClass('pp-hide');

	} else {

		jQuery('.project-pages-template-mode-hide-legacy').addClass('pp-hide');
		jQuery('.project-pages-template-mode-hide-default').removeClass('pp-hide');

	}

}

function projectPages_permalink_root_changes( initialising ){

	// to stop the infinite...
	if ( !window.projectPagesPermaAlreadyChanging ){

		window.projectPagesPermaAlreadyChanging = true;

		// get root
		var permalink_root = projectPages_make_slug( jQuery('#ppsetting_permalink_root').val() );
		if ( permalink_root == '' ) permalink_root = 'projects';

		// update the text input to (helps to force permalink slug)
		jQuery('#ppsetting_permalink_root').val( permalink_root );

		// fill in urls		
		jQuery('#project-page-urls-example').text( window.pp_wp_settings.blog_root_url + '/' + permalink_root + '/time-machine' );

		// on init we can link to page as it'll work (new ones need save + permalink flush)
		if ( initialising ){

			// replace
			jQuery('#project-page-urls-archive').text( window.pp_wp_settings.blog_root_url + '/' + permalink_root ).attr( 'href', window.pp_wp_settings.blog_root_url + '/' + permalink_root );
			

		} else {

			// make it a span again
			jQuery('#project-page-urls-archive').replaceWith( '<span id="project-page-urls-archive">' + window.pp_wp_settings.blog_root_url + '/' + permalink_root + '</span>');

		}

		window.projectPagesPermaAlreadyChanging = false;

	}

}

function projectPages_make_slug(str){
	str = str.replace(/^\s+|\s+$/g, ''); // trim
	str = str.toLowerCase();

	// remove accents, swap ñ for n, etc
	var from = "àáäâèéëêìíïîòóöôùúüûñçěščřžýúůďťň·/_,:;";
	var to   = "aaaaeeeeiiiioooouuuuncescrzyuudtn------";

	for (var i=0, l=from.length ; i<l ; i++)
	{
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}

	str = str.replace('.', '-') // replace a dot by a dash 
		.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		.replace(/\s+/g, '-') // collapse whitespace and replace by a dash
		.replace(/-+/g, '-') // collapse dashes
		.replace( /\//g, '' ); // collapse all forward-slashes

	return str;
}