<?php 
/*!
 * Project Pages
 * http://www.woodyhayday.com/project/project-pages
 * V2.0
 *
 * Date: 24/04/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */


// Welcome
function projectPages_page_welcome(){

	global $wpdb, $projectPages_Settings, $projectPages_db_version, $projectPages_version, $projectPages_urls, $projectPages_slugs; 

	// get counts
	$project_page_count = projectPages_getProjectCount();
	$project_page_hit_count = projectPages_getProjectPageHitsTotal();
	$project_page_log_count = projectPages_getProjectLogCount();
	$project_page_terms = projectPages_getTags( true );
	$has_viewed_settings = projectPages_hasViewedSettings();
	$using_pp_block = false;
	$project_page_random_url = wh_get_random_published_url( 'projectpage' );	
	$using_child_theme_templates = projectPages_is_using_theme_template();

	$welcome_steps = array(

			'make_project_page' => array(

					'title' => __( 'Make a Project Page', 'projectpages' ),
					'desc' => __( 'Make your first project page.', 'projectpages' ),
					'url' => get_admin_url( null, 'post-new.php?post_type=projectpage' ),
					'link_title' => __( 'Add a Project Page', 'projectpages' ),
					'state' => ( $project_page_count > 0 ),
					'success_msg' => sprintf( __( 'Great, you\'ve made %s Product pages, you\'re awesome!', 'projectpages' ), $project_page_count )

			),
			'view_project_page' => array(

					'title' => __( 'View a Project Page', 'projectpages' ),
					'desc' => __( 'Check yourself out! View a project page.', 'projectpages' ),
					'url' => $project_page_random_url,
					'link_title' => __( 'View a (random) Project Page', 'projectpages' ),
					'state' => ( $project_page_hit_count > 0 ),
					'success_msg' => __( 'Awesome, looks like your Project Page has been viewed.', 'projectpages' )

			),
			'add_project_log' => array(

					'title' => __( 'Add a Project Log', 'projectpages' ),
					'desc' => __( 'Add a log entry to a Project Page.', 'projectpages' ),
					'url' => get_admin_url( null, 'edit.php?post_type=projectpage' ),
					'link_title' => __( 'Edit a Project', 'projectpages' ),
					'state' => ( $project_page_log_count > 0 ),
					'success_msg' => sprintf( __( 'You\'ve got %s logs, well done. I hope you like this feature!', 'projectpages' ), $project_page_log_count )

			),
			'add_project_tag' => array(

					'title' => __( 'Add a Project Tag', 'projectpages' ),
					'desc' => __( 'Add a tag to a Project Page.', 'projectpages' ),
					'url' => get_admin_url( null, 'edit.php?post_type=projectpage' ),
					'link_title' => __( 'Edit a Project', 'projectpages' ),
					'state' => ( count( $project_page_terms ) > 0 ),
					'success_msg' => __( 'Tags aplenty, good work.', 'projectpages' )

			),
			/*
			removed for v2.0 launch, readdress
			'use_pp_block' => array(

					'title' => __( 'Use a Project Page block', 'projectpages' ),
					'desc' => __( 'Try using a Project Page block in one your pages. The log summary one is good!', 'projectpages' ),
					'url' => get_admin_url( null, 'edit.php?post_type=page' ),
					'link_title' => __( 'Edit a page', 'projectpages' ),
					'state' => ( $using_pp_block ),
					'success_msg' => __( 'Ah sweet, I can see you\'ve got a Project Page block in there. Good job.', 'projectpages' )

			), */
			'check_settings' => array(

					'title' => __( 'Check the settings', 'projectpages' ),
					'desc' => __( 'Check through the settings and make sure everything fits what you\'re using Project Pages for.', 'projectpages' ),
					'url' => get_admin_url( null, 'edit.php?post_type=projectpage&page=projectpages-plugin-settings' ),
					'link_title' => __( 'View Settings', 'projectpages' ),
					'state' => $has_viewed_settings,
					'success_msg' => __( 'Nice to know you\'ve checked them over.', 'projectpages' )

			),
			'use_templates' => array(

					'title' => __( 'Use Templates', 'projectpages' ),
					'desc' => __( 'You can modify the templates in Project Pages by copying them to your child theme', 'projectpages' ),
					'url' => get_admin_url( null, 'edit.php?post_type=projectpage&page=projectpages-plugin-settings' ),
					'link_title' => __( 'View Template Settings', 'projectpages' ),
					'state' => $using_child_theme_templates,
					'success_msg' => __( 'Great, you\'re using custom templates!', 'projectpages' )

			)


	);

$bonus_steps = array(

			'get_pro' => array(

					'title' => __( 'Get PRO', 'projectpages' ),
					'desc' => __( '<strong>Pay what you want</strong> to get the upgraded version of Project Pages.', 'projectpages' ),
					'url' => ppurl( 'get-pro' ),
					'link_title' => __( 'Upgrade', 'projectpages' )

			),

			'get_updates' => array(

					'title' => __( 'Join the Community', 'projectpages' ),
					'desc' => __( 'Enter your email here to get updates on Project Pages and join the community.', 'projectpages' ),
					'url' => ppurl( 'subscribe' ),
					'link_title' => __( 'Get Updates', 'projectpages' )

			),

			'give_feedback' => array(

					'title' => __( 'Give Feedback', 'projectpages' ),
					'desc' => __( 'This plugin is made for those who make! I\'d love to hear how you\'re using it, or would like to use it. Please do give feedback, it helps this get better!', 'projectpages' ),
					'url' => ppurl( 'feedback' ),
					'link_title' => __( 'Give Feedback ❤️', 'projectpages' )

			),

			'leave_review' => array(

					'title' => __( 'Leave a Review', 'projectpages' ),
					'desc' => __( 'Your review really matters, I\'d love to keep working on this for you, and your positive review helps that happen!', 'projectpages' ),
					'url' => ppurl( 'review' ),
					'link_title' => __( 'Review Project Pages', 'projectpages' )

			),

			'follow_pp' => array(

					'title' => __( 'Follow @ProjectPagesio', 'projectpages' ),
					'desc' => __( 'Follow Project Pages on X and watch as we grow!', 'projectpages' ),
					'url' => ppurl( 'x' ),
					'link_title' => __( 'Follow @projectpagesio', 'projectpages' )

			),

			'follow_wh' => array(

					'title' => __( 'Follow @woodyhayday', 'projectpages' ),
					'desc' => __( '👋 I\'m Woody Hayday and I made this for you! Follow me on X :)', 'projectpages' ),
					'url' => ppurl( 'xwh' ),
					'link_title' => __( 'Follow @woodyhayday', 'projectpages' )

			),

		);

    // output nonce for AJAX
    ?><script>
        var projectPages_join_nonce 	= '<?php echo esc_js( wp_create_nonce( 'project-pages-join-nonce' ) ); ?>';
        var projectPages_joined_html 	= '<div class="p-3 text-center"><span style="font-size:3em">🥳</span>'
        															+ '<p class="lead"><?php _e( 'Great to have you! Please check your email to confirm', 'projectpages' ); ?></p></div>';
        var projectPages_labels				= <?php echo json_encode(

        	array(

        		'joining' => __( 'Adding you...', 'projectpages' )

        	)

        	); ?>;
    </script>

	<div class="p-3 mb-3 border-bottom">
	  <div class="container">
	    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
	      <div class="position-relative"> <?php _e( 'Project Pages', 'projectpages' ); ?> <?php if ( defined( 'PROJECTPAGES_PRO_PATH' ) ){ echo 'Pro '; } ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">v<?php echo $projectPages_version; ?></span>
	      </div>
	      <div class="position-relative me-auto ms-4"> / <?php _e( 'Welcome', 'projectpages' ); ?> </div>
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

<div class="container col-xl-10 col-xxl-8 px-4">
    <div class="row align-items-center g-lg-5 py-5">
      <div class="col-lg-7 text-center text-lg-start">
      	<img src="<?php echo PROJECTPAGES_URL . 'i/project-pages-128.png'; ?>" alt="Project Pages" width="64" height="64">
        <h1 class="display-4 fw-bold lh-1 text-body-emphasis mb-3" style="font-size:2em"><?php _e( 'Welcome to Project Pages', 'projectpages'); ?></h1>
        <p class="col-lg-10 fs-5"><?php _e( 'It\'s great to have you here! I made this plugin for those of us who make stuff and want to share what we\'ve made with the world. I want Project Pages to help you document your creator journey, and empowers you to make even cooler projects in the future.', 'projectpages' ); ?></p>        
        <p class="" style="padding-left:3em">
        	<a href="<?php echo $projectPages_urls['feedback']; ?>" target="_blank" class="btn btn-primary px-5 mb-5" type="button"><?php _e( 'Give Feedback', 'projectpages' ); ?></a>
        	<?php if ( !projectPages_is_pro() ){ ?><a href="<?php echo ppurl('get-pro'); ?>" target="_blank" class="btn btn-success px-5 mb-5" type="button"><?php _e( 'Upgrade', 'projectpages' ); ?></a><?php } ?>
        </p>
      </div>
      <div class="col-md-10 mx-auto col-lg-5">
        <form class="p-4 p-md-5 border rounded-3 bg-body-tertiary" id="project-pages-hero-sub-wrap">
        	<p class="col-lg-10 fs-6" style="font-weight: bold;text-align: center;width: 100%;"><?php _e( 'Join the Project Pages community:', 'projectpages' ); ?></p>
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="project-pages-hero-sub" placeholder="name@example.com">
            <label for="project-pages-hero-sub"><?php _e( 'Email Address', 'projectpages' ); ?></label>
            <div class="invalid-feedback">
			        <?php _e( 'Please use a valid email', 'projectpages' ); ?>
			      </div>
          </div>
          <div class="checkbox mb-3">
            <label>
              <input type="checkbox" value="solemnly-swear" id="project-pages-hero-sub-solemnly"> <?php _e( 'I solemnly swear to keep making awesome stuff and sharing it with others.', 'projectpages' ); ?>
            </label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="button" id="project-pages-hero-sub-go"><?php _e( 'Join Project Makers!', 'projectpages' ); ?></button>
          <div class="project-pages-failed-form" id="project-pages-hero-sub-failed">
			        <?php echo sprintf( __( 'There was a problem joining the community. Please try again, or <a href="%s" target="_blank">Click here</a>', 'projectpages' ), ppurl('subscribe') ); ?>
			    </div>
			    <hr class="my-4">
          <a href="<?php echo ppurl( 'wh' ); ?>" target="_blank"><img src="https://woodyhayday.com/assets/woody-hayday-round.jpeg" alt="<?php _e( 'Created by @woodyhayday', 'projectpages'); ?>" style="max-width: 64px;float: right;border-radius: 50%;margin-top: -1em;" /></a>
          <small class="text-body-secondary"><?php _e( 'I promise to never spam you.', 'projectpages' ); ?></small>
        </form>
      </div>
    </div>
    <div class="row align-items-center text-center" id="project-pages-suggest-down">
    	<a href="#project-pages-get-started"><span class="dashicons dashicons-arrow-down-alt2" style="font-size: 3em;"></span></a>
    </div>
  </div>

<hr>

	<h1 class="text-center pt-3"><?php echo sprintf( __( '%s Steps to Project Pages Mastery', 'projectpages' ), count( $welcome_steps ) ); ?></h1>
	<p class="text-center" id="project-pages-get-started"><a href="<?php echo ppurl('kb-getting-started'); ?>" target="_blank"><?php _e( 'Read the Getting Started Guide', 'projectpages' ); ?></a></p>
	<div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-3 align-items-center justify-content-center" id="project-pages-welcome-steps">
			<div class="list-group">

				<?php

					$failed_steps = 0;

					foreach ( $welcome_steps as $step_key => $step ){

						if ( !$step['state'] ) $failed_steps++;

						?>
					  <div class="list-group-item list-group-item-action">
					  	<?php if ( $step['state'] ){ ?>
					  	<span class="dashicons dashicons-yes-alt success"></span>
					  	<?php } else { ?>
					  	<span class="dashicons dashicons-marker"></span>
					  	<?php } ?>
					  	<div class="project-pages-step-info">
						    <div class="d-flex w-100 justify-content-between">
						      <h5 class="mb-1"><?php echo $step['title']; ?></h5>
						    </div>
						    <p class="mb-1 project-pages-step-desc"><?php echo $step['desc']; ?></p>
						    <?php if ( $step['state'] ) { ?>
						    	<small><?php echo $step['success_msg']; ?></small>
						    <?php } else { 

						    	if ( $step['url'] ) { ?>
						    	<a href="<?php echo $step['url']; ?>" class="btn btn-primary px-5" type="button"><?php echo $step['link_title']; ?></a>    
						    	<?php }
						    } ?>
						  </div>
					  </div>
						<?php


					}

				?>
		</div>
	</div>
	<?php

		if ( $failed_steps === 1 ){

			?><div class="px-4 py-5 my-5 text-center">
    <span style="font-size:3em">🥳</span>
    <h1 class="display-6 fw-bold text-body-emphasis"><?php _e( 'You\'re Awesome!', 'projectpages' ); ?></h1>
    <div class="col-lg-6 mx-auto">
      <p class="lead"><?php _e( 'Thanks for using my little plugin to share the things you make with the world!', 'projectpages' ); ?></p>
      <p class="lead mb-4"><?php _e( 'There are some bonus steps below if you\'re into that. Otherwise, let\'s connect:', 'projectpages' ); ?></p>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
        <a href="<?php ppurl( 'xwh' ); ?>" class="btn btn-primary btn-lg px-4 gap-3">@woodyhayday</a>
      </div>
    </div>
  </div>

			<?php

		}


	?>

	<h1 class="text-center pt-3"><?php _e( 'Bonus Steps', 'projectpages' ); ?></h1>
	<div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-center justify-content-center" id="project-pages-bonus-steps">
			<div class="list-group">

				<?php

					foreach ( $bonus_steps as $step_key => $step ){

						?>
					  <div class="list-group-item list-group-item-action">
					  	<span class="dashicons dashicons-star-filled"></span>
					  	<div class="project-pages-step-info">
						    <div class="d-flex w-100 justify-content-between">
						      <h5 class="mb-1"><?php echo $step['title']; ?></h5>
						    </div>
						    <p class="mb-1 project-pages-step-desc"><?php echo $step['desc']; ?></p>
						    <?php
						    	if ( $step_key == 'get_updates' ){ ?>
						    		<div id="project-pages-bonus-sub-wrap">
						        	<div class="form-floating mb-3">
						            <input type="email" class="form-control" id="project-pages-bonus-sub" placeholder="name@example.com">
						            <label for="project-pages-bonus-sub"><?php _e( 'Email Address', 'projectpages' ); ?></label>
						            <div class="invalid-feedback">
									        <?php _e( 'Please use a valid email', 'projectpages' ); ?>
									      </div>
						          </div>
						          <div class="checkbox mb-3">
						            <label>
						              <input type="checkbox" value="solemnly-swear" id="project-pages-bonus-sub-solemnly"> <?php _e( 'I solemnly swear to keep making awesome stuff and sharing it with others.', 'projectpages' ); ?>
						            </label>
						          </div>
						          <button class="btn btn-primary px-5" type="button" id="project-pages-bonus-sub-go"><?php _e( 'Join Project Makers!', 'projectpages' ); ?></button>
						          <div class="project-pages-failed-form" id="project-pages-bonus-sub-failed">
									        <?php echo sprintf( __( 'There was a problem joining the community. Please try again, or <a href="%s" target="_blank">Click here</a>', 'projectpages' ), ppurl('subscribe') ); ?>
									    </div>
										</div>
						    	<?php } else { ?>
						    		<a href="<?php echo $step['url']; ?>" target="_blank" class="btn btn-primary px-5" type="button"><?php echo $step['link_title']; ?></a>    
						    	<?php } ?>
						   </div>
					  </div>
						<?php


					}
				?>
		</div>
	<?php


}