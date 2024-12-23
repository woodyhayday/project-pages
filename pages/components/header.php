<?php
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 19/12/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

   global $projectPages_Settings, $projectPages_db_version, $projectPages_version, $projectPages_urls, $projectPages_slugs; 

   global $pp_component_args;
?>
   <div class="p-3 mb-3 border-bottom">
	  <div class="container">
	    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
	      <div class="position-relative"> <?php _e( 'Project Pages', 'projectpages' ); ?> <?php if ( defined( 'PROJECTPAGES_PRO_PATH' ) ){ echo 'Pro '; } ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">v<?php echo $projectPages_version; ?></span>
	      </div>
	      <div class="position-relative me-auto ms-4"> / <?php if ( !empty($pp_component_args['page']) ){ echo $pp_component_args['page']; } else { _e( 'Welcome', 'projectpages' ); } ?> </div>
	      <div class="nav">
	        <div class="dropdown text-end">
	          <a href="<?php echo ppurl(); ?>" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
	            <img src="<?php echo PROJECTPAGES_URL; ?>i/project-pages-128.png" alt="<?php _e( 'Project Pages', 'projectpages' ); ?>" width="32" height="32" class="rounded-circle">
	          </a>
	          <ul class="dropdown-menu text-small">
	            <li>
	              <a class="dropdown-item" href="<?php echo get_admin_url( null, 'post-new.php?post_type=projectpage' ); ?>"><?php _e( 'New Project Page', 'projectpages' ); ?></a>
	            </li>
	            <li>
	              <a class="dropdown-item" href="<?php echo get_admin_url( null, 'options.php?page=projectpages' ); ?>"><?php _e( 'Welcome', 'projectpages' ); ?></a>
	            </li>
	            <li>
	              <a class="dropdown-item" href="<?php echo get_admin_url( null, 'edit.php?post_type=projectpage&amp;page=projectpages-plugin-settings' ); ?>"><?php _e( 'Settings', 'projectpages' ); ?></a>
	            </li>
	            <?php if ( projectPages_is_pro() ){ ?><li>
	              <a class="dropdown-item" href="<?php echo get_admin_url( null, 'edit.php?post_type=projectpage&amp;page=projectpages-plugin-settings&pp_tab=statuses' ); ?>"><?php _e( 'Custom Statuses', 'projectpages' ); ?></a>
	            </li><?php } ?>
	            <?php if ( !projectPages_is_pro() ){ ?><li>
	              <a class="dropdown-item" href="<?php echo ppurl('get-pro'); ?>" target="_blank"><?php _e( 'Upgrade to PRO', 'projectpages' ); ?></a>
	            </li><?php } ?>
	            <li>
	              <a class="dropdown-item" href="<?php echo ppurl('feedback'); ?>" target="_blank"><?php _e( 'Give Feedback', 'projectpages' ); ?></a>
	            </li>
	            <li>
	              <a class="dropdown-item" href="<?php echo ppurl(); ?>">ProjectPages.io</a>
	            </li>
	          </ul>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>