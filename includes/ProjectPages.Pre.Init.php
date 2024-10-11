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

// This fills out some helper info which needs processing just before the settings page loads
// e.g. state of templates
function projectPages_settings_explainers_extend(){

	global $projectPages_Conf_Setup;

		// permalink root
		$permalink_root = sanitize_title( $projectPages_Conf_Setup['conf_defaults']['permalink_root'] );
		$archive_url = get_bloginfo('url') .  '/' . $permalink_root;
		$example_url = get_bloginfo('url') .  '/' . $permalink_root . '/time-machine';
		$html = '<strong>' . __( 'Default:', 'projectpages' ) . '</strong> <span class="pp-label">`projects`</span>';
		$html .= '<strong>' . __( 'Example URLs:', 'projectpages' ) . '</strong><ul>';
		$html .= '<li><strong>' . __( 'Projects Archive:', 'projectpages' ) . '</strong> <a href="' . esc_url( $archive_url ) .'" target="_blank" id="project-page-urls-archive">' . $archive_url .'</a></li>';
		$html .= '<li><strong>' . __( 'Project Example:', 'projectpages' ) . '</strong> <span id="project-page-urls-example">' . $example_url .'</span></li>';
		$html .= '</ul>';

		// help guide info
		$html .= '<div><a href="' . ppurl( 'kb-permalink-root' ) . '" target="_blank" class="btn btn-primary btn-sm">' . __( 'View Permalink Guide', 'projectpages' ) .'</a></div>';

	
		// update the template mode eg
		$projectPages_Conf_Setup['setting_index']['permalink_root']['eg'] = $html;
		$html = ''; // safety.


		// Templating
		$html = '<div id="project-page-settings-template-info">';

			if ( is_child_theme() ){

				$html .= '<div><span class="dashicons dashicons-yes-alt success"></span> ' . __( 'Child theme ready for templates', 'projectpages' ) .'</div>';

			} else {

				$html .= '<div><span class="dashicons dashicons-marker"></span> ' . __( 'You are not using a child theme. If you install custom templates they may get overwritten when you update your theme.', 'projectpages' ) .'</div>';

			}

			// here we output 2 options, one of which will be hidden by js, depending on default/legacy option for `template_mode`

			// default ( gutenberg templates )
			$single_template = false; $single_template_source = __( 'Plugin', 'projectpages' );
			if ( file_exists( get_stylesheet_directory() . '/templates/single-projectpage.html' ) ) {
				$single_template = '/wp-content' . wh_str_after_x( get_stylesheet_directory() . '/templates/single-projectpage.html', 'wp-content' );
				$single_template_source = __( 'Theme', 'projectpages' );
			}
			if ( !$single_template && file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.html' ) ) $single_template = PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/single-projectpage_DEFAULT.html';
			
			$archive_template = false; $archive_template_source = __( 'Plugin', 'projectpages' );
			if ( file_exists( get_stylesheet_directory() . '/templates/archive-projectpage.html' ) ) {
				$archive_template = '/wp-content' . wh_str_after_x( get_stylesheet_directory() . '/templates/archive-projectpage.html', 'wp-content' );
				$archive_template_source = __( 'Theme', 'projectpages' );
			}
			if ( !$archive_template && file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.html' ) ) $archive_template = PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/archive-projectpage_DEFAULT.html';
			
			$taxonomy_template = false; $taxonomy_template_source = __( 'Plugin', 'projectpages' );
			if ( file_exists( get_stylesheet_directory() . '/templates/taxonomy-projectpagetag.html' ) ){
				$taxonomy_template = '/wp-content' . wh_str_after_x( get_stylesheet_directory() . '/templates/taxonomy-projectpagetag.html', 'wp-content' );
				$taxonomy_template_source = __( 'Theme', 'projectpages' );
			}
			if ( !$taxonomy_template && file_exists( PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpagetag_DEFAULT.html' ) ) $taxonomy_template = PROJECTPAGES_PLUGIN_TEMPLATE_PATH . '/taxonomy-projectpagetag_DEFAULT.html';

			$html .= '<hr><div class="project-pages-template-mode-hide-default pp-hide">';

				$html .= '<div><strong>' . __( 'Current Templates:', 'projectpages' ) . '</strong><br>';
				
				$html .= '<ul>';
					$html .= '<li><strong>' . __( 'Single:', 'projectpages' ) . '</strong> ' . ( $single_template ? $single_template . ' <span class="pp-label">' . $single_template_source . '</span>' : '<span class="dashicons dashicons-warning"></span> ' . __( 'None Available!', 'projectpages' ) ) . '</li>';
					$html .= '<li><strong>' . __( 'Archive:', 'projectpages' ) . '</strong> ' . ( $archive_template ? $archive_template . ' <span class="pp-label">' . $archive_template_source . '</span>' : '<span class="dashicons dashicons-warning"></span> ' . __( 'None Available!', 'projectpages' ) ) . '</li>';
					$html .= '<li><strong>' . __( 'Tags:', 'projectpages' ) . '</strong> ' . ( $taxonomy_template ? $taxonomy_template . ' <span class="pp-label">' . $taxonomy_template_source . '</span>' : '<span class="dashicons dashicons-warning"></span> ' . __( 'None Available!', 'projectpages' ) ) . '</li>';
				$html .= '</ul>';
				
				$html .= '</div>';

			$html .= '</div>';

			// legacy:
			$legacy_single_template = '/wp-content' . wh_str_after_x( projectPages_discern_single_template_legacy(), 'wp-content' );
			$legacy_archive_template = '/wp-content' . wh_str_after_x( projectPages_discern_archive_template_legacy(), 'wp-content' );
			$legacy_taxonomy_template = '/wp-content' . wh_str_after_x( projectPages_discern_taxonomy_template_legacy(), 'wp-content' );
			$html .= '<div class="project-pages-template-mode-hide-legacy pp-hide">';

				$html .= '<div><strong>' . __( 'Current Templates:', 'projectpages' ) . '</strong><br>';
				
				$html .= '<ul>';
					$html .= '<li><strong>' . __( 'Single:', 'projectpages' ) . '</strong> ' . ( $legacy_single_template ? $legacy_single_template : '<span class="dashicons dashicons-warning"></span> ' . __( 'None Available!', 'projectpages' ) ) . '</li>';
					$html .= '<li><strong>' . __( 'Archive:', 'projectpages' ) . '</strong> ' . ( $legacy_archive_template ? $legacy_archive_template : '<span class="dashicons dashicons-warning"></span> ' . __( 'None Available!', 'projectpages' ) ) . '</li>';
					$html .= '<li><strong>' . __( 'Tags:', 'projectpages' ) . '</strong> ' . ( $legacy_taxonomy_template ? $legacy_taxonomy_template : '<span class="dashicons dashicons-warning"></span> ' . __( 'None Available!', 'projectpages' ) ) . '</li>';
				$html .= '</ul>';
				
				$html .= '</div>';

			$html .= '</div>';

			// help guide info
			$html .= '<div><a href="' . ppurl( 'kb-templates' ) . '" target="_blank" class="btn btn-primary btn-sm">' . __( 'View Templates Guide', 'projectpages' ) .'</a></div>';

		$html .= '</div>';

		// update the template mode eg
		$projectPages_Conf_Setup['setting_index']['template_mode']['eg'] = $html;


		// add guide link to appearance settings too, for now - needs rethinking as to how to get gutenberg editor setup without child theme/easier for user
		// #75 may do away with this
		// Also be good to add per category explainers to the settings vue app #82
		$html = '<div id="project-page-settings-appearance-info" style="margin-top:6em">';

			// help guide info
			$html .= '<div>' . __( 'Note: For more appearance settings I recommend you install the Project Pages templates into your theme, then you can modify the look and feel of everything via the WordPress Editor (Gutenberg)', 'projectpages' ) .'</div>';
			$html .= '<div><a href="' . ppurl( 'kb-templates' ) . '" target="_blank" class="btn btn-primary btn-sm">' . __( 'View Templates Guide', 'projectpages' ) .'</a></div>';

		$html .= '</div>';

		// update the template mode eg
		$projectPages_Conf_Setup['setting_index']['display_showstatus']['eg'] = $html;

}
add_action( 'project_pages_pre_settings_hook', 'projectPages_settings_explainers_extend', 10, 2 );