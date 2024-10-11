<?php
/**
 * Custom template for Project Page Tag Archives (e.g. yourblog.com/projects/tagged)
 */

	#} NOTE: Adapted from : http://semantic-ui.com/examples/homepage.html
	#} <3 and props to Semantic UI

	#} For more on tag archives:
	#} https://developer.wordpress.org/themes/template-files-section/taxonomy-templates/

	#} Subtle efficiency drive.

		#} Post Archive specifics:
		$archiveTitle = get_the_archive_title();

			#} Personally don't like "Archives: Projects"
			$archiveTitle = str_replace('Project Tag: ','',$archiveTitle);

		$archiveTitleChecked = $archiveTitle; if (empty($archiveTitle)) $archiveTitleChecked = __('Projects','projectpages');

		#} Get url
		$archiveURL = get_permalink();
		
		#} Some basics:
		$blogTitle = get_bloginfo('name');
		$blogURL = get_bloginfo('url');
		$projectsTitle = __('Projects','projectpages');
    $projectsURL = projectPages_projects_root_url();

		#} Pretty up page title.
		$pageTitle = __('Projects','projectpages'); if (!empty($archiveTitle)) $pageTitle = $pageTitle .= ' '.__('Tagged','projectpages').' '.$archiveTitle.''; if (!empty($blogTitle)) $pageTitle .= ' | '.$blogTitle;

    #} Make a share ver... 
    $shareTitle = str_replace('"','',$pageTitle);

    #} Pass to footer
    global $projectPagesFooterInfo; $projectPagesFooterInfo = array('blogtitle' => $blogTitle,'blogurl'=>$blogURL,'projectstitle'=>$projectsTitle,'projectsurl'=>$projectsURL);

    #} Featured image (here only used for search)
    $ogImage = PROJECTPAGES_URL.'i/projectpages.png';

?><!DOCTYPE html>
<html>
<head>

  <!-- Standard Meta -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <!-- Site Properties -->
  <title><?php echo $pageTitle; ?></title>
  <link rel="stylesheet" type="text/css" href="<?php echo PROJECTPAGES_URL.'css/libs/semantic.min.css'; ?>">
  <?php
    
    #} Favicon?
    $faviconURL = projectPages_getSetting('favicon');
    if (!empty($faviconURL)) echo '<link rel="shortcut icon" href="'.$faviconURL.'" type="image/x-icon" />'; 

  ?>

  <!-- OG Meta -->
  <meta property="og:title" content="<?php echo $pageTitle; ?>" />
  <meta property="og:type" content="blog" />
  <meta property="og:url" content="<?php echo $archiveURL; ?>" />
  <meta property="og:image" content="<?php echo $ogImage; ?>" />
  <meta property="og:site_name" content="<?php echo $blogTitle; ?>" />
  <meta property="og:description" content="<?php echo $pageTitle; ?>" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:title" content="<?php echo $pageTitle; ?>" />
  <meta name="twitter:description" content="<?php echo $pageTitle; ?>" />
  <meta name="twitter:image" content="<?php echo $ogImage; ?>" />
  <meta itemprop="image" content="<?php echo $ogImage; ?>" />

  <style type="text/css">

    .hidden.menu {
      display: none;
    }

    .masthead.segment {
      min-height: 700px;
      padding: 1em 0em;
    }
    .masthead .logo.item img {
      margin-right: 1em;
    }
    .masthead .ui.menu .ui.button {
      margin-left: 0.5em;
    }
    .masthead h1.ui.header {
      margin-top: 3em;
      margin-bottom: 0em;
      font-size: 4em;
      font-weight: normal;
    }
    .masthead h2 {
      font-size: 1.7em;
      font-weight: normal;
    }

    .ui.vertical.stripe {
      padding: 8em 0em;
    }
    .ui.vertical.stripe h3 {
      font-size: 2em;
    }
    .ui.vertical.stripe .button + h3,
    .ui.vertical.stripe p + h3 {
      margin-top: 3em;
    }
    .ui.vertical.stripe .floated.image {
      clear: both;
    }
    .ui.vertical.stripe p {
      font-size: 1.33em;
    }
    .ui.vertical.stripe .horizontal.divider {
      margin: 3em 0em;
    }

    .quote.stripe.segment {
      padding: 0em;
    }
    .quote.stripe.segment .grid .column {
      padding-top: 5em;
      padding-bottom: 5em;
    }

    .footer.segment {
      padding: 5em 0em;
    }

    .secondary.pointing.menu .toc.item {
      display: none;
    }

    @media only screen and (max-width: 700px) {
      .ui.fixed.menu {
        display: none !important;
      }
      .secondary.pointing.menu .item,
      .secondary.pointing.menu .menu {
        display: none;
      }
      .secondary.pointing.menu .toc.item {
        display: block;
      }
      .masthead.segment {
        min-height: 350px;
      }
      .masthead h1.ui.header {
        font-size: 2em;
        margin-top: 1.5em;
      }
      .masthead h2 {
        margin-top: 0.5em;
        font-size: 1.5em;
      }
      .menubreadcrumb {
      	display:none !important;
      }
    }
    .project-pages-share-wrap span.project-pages-share-label {
        vertical-align: text-bottom;
    }
    .project-pages-share-wrap .project-pages-share-icons {
        background-color: #FFF;
        border-radius: 0.2em;
        display: inline-block;
        padding-top: 0.2em;
        padding-right: 0.3em;
    }
    .project-pages-share-wrap .project-pages-share-icons img {
        width: 1.5em;
        margin-left: 0.3em;
        margin-bottom: -0.2em;
    }

    /* few tweaks */
    .menubreadcrumb {
    	padding-top: 10px !important;
    }
    #fixedMenu .menubreadcrumb {
      padding-left: 4px;
    }
    .statuscard {
    	margin:0;
    	float:right;
    }
    .ui.secondary.inverted.pointing.menu {
    	border:0 !important;
    }
    .statuscard .description {
    	font-size:0.9em;
    }
    .ppFooterWidgetWrap {
      padding-bottom:4px;
    }
    .ppFooterWidgetWrap ul {

        list-style-type: none !important;    
        padding-left: 12px;
        font-size: 1.1em;
        line-height: 1.7em;
        
    }

    <?php if (!empty($projectFeaturedImage)){  #} Any feat img?  ?>
    #projectmasthead {
	    background-image: url("<?php echo $projectFeaturedImage; ?>");
	    background-size: cover;
	    background-repeat: no-repeat;
	    background-position: 50% 50%;
	}
    <?php } ?>


    /* Share bits */
    .whpp-sharewrap {

        font-size:18px;

    }
    .whpp-sharewrap .facebook, .whpp-sharewrap .twitter {
        
        margin-left: 4px;
        margin-right: 0;
        height: 30px;
        
    }
    .whpp-sharewrap .facebook:hover, .whpp-sharewrap .twitter:hover {

        cursor:pointer;
        border-bottom: 2px solid #FFF;

    }

    .ui.menu .item:before {
      background:none !important;
    }
  </style>
  <?php

    #} any override css?
    $cssOverride = projectPages_getSetting('css_override');
    if (!empty($cssOverride)) echo '<style type="text/css">'.projectPages_textProcess($cssOverride).'</style>';

    #} Print wp scripts to grab jquery, rather than using our own :)
    wp_print_head_scripts();

    /*<script src="<?php echo PROJECTPAGES_URL.'js/libs/jquery.min.js'; ?>"></script>*/
  ?>

  <script src="<?php echo PROJECTPAGES_URL.'js/libs/semantic/visibility.min.js'; ?>"></script>
  <script src="<?php echo PROJECTPAGES_URL.'js/libs/semantic/sidebar.min.js'; ?>"></script>
  <script src="<?php echo PROJECTPAGES_URL.'js/libs/semantic/transition.min.js'; ?>"></script>
  <script>
  jQuery(document)
    .ready(function() {

      // fix menu when passed
      jQuery('.masthead')
        .visibility({
          once: false,
          onBottomPassed: function() {
            jQuery('.fixed.menu').transition('fade in');
          },
          onBottomPassedReverse: function() {
            jQuery('.fixed.menu').transition('fade out');
          }
        })
      ;

      // create sidebar and attach to menu open
      jQuery('.ui.sidebar')
        .sidebar('attach events', '.toc.item')
      ;

    })
  ;
  </script>
</head>
<body>

<!-- Following Menu -->
<div class="ui large top fixed hidden menu" id="fixedMenu">
  <div class="ui container">
    <a class="item" href="<?php echo $blogURL; ?>"><?php echo $blogTitle; ?></a>
    <i class="right angle icon menubreadcrumb"></i> 
    <a class="item" href="<?php echo $projectsURL; ?>"><?php echo $projectsTitle; ?></a>
	<i class="right angle icon menubreadcrumb"></i> 
    <a class="item active" href="<?php echo $archiveURL; ?>"><?php echo $archiveTitle; ?></a>
  </div>
</div>

<!-- Sidebar Menu -->
<div class="ui vertical inverted sidebar menu">
  <a class="item" href="<?php echo $blogURL; ?>"><?php echo $blogTitle; ?></a> 
  <a class="item" href="<?php echo $projectsURL; ?>"><?php echo $projectsTitle; ?></a>
  <a class="item active" href="<?php echo $archiveURL; ?>"><?php echo $archiveTitle; ?></a>
</div>

<!-- Page Contents -->
<div class="pusher">
  <div class="ui inverted vertical masthead center aligned segment" id="projectmasthead">

    <div class="ui container">
      <div class="ui large secondary inverted pointing menu">
        <a class="toc item">
          <i class="sidebar icon"></i>
        </a>
    	<a class="item" href="<?php echo $blogURL; ?>"><?php echo $blogTitle; ?></a>
    	<i class="right angle icon menubreadcrumb"></i>  
	    <a class="item" href="<?php echo $projectsURL; ?>"><?php echo $projectsTitle; ?></a>
		<i class="right angle icon menubreadcrumb"></i> 
        <a class="item active" href="<?php echo $archiveURL; ?>"><?php echo $archiveTitle; ?></a>
      </div>
    </div>

	<?php if ( have_posts() ) : ?>

		<div class="ui text container">
	      <h1 class="ui inverted header">
	        <?php echo __('Projects Tagged','projectpages').' '.$archiveTitle; ?>
	      </h1>
	      <h2><?php 

	      #} count posts
	      $postCount = 0; global $wp_query; if (isset($wp_query->found_posts)) $postCount = $wp_query->found_posts;

	      printf( esc_html__( 'Browse the %1$s Projects tagged %2$s', 'projectpages'), $postCount, $archiveTitle ); ?></h2>
        <?php $shareImg = ''; if (isset($projectFeaturedImage) && !empty($projectFeaturedImage)) $shareImg = $projectFeaturedImage; projectPages_shareOut($shareTitle,$archiveURL,$shareImg); ?>
	    </div>

	  </div>

	  <?php #} Wrapper ?>
	  <div class="ui vertical stripe segment" id="project-page-body">
	    <div class="ui middle aligned stackable grid container">
	      <div class="row">
	        <div class="sixteen wide column">
            <div class="ui four cards centered">

		<?php
		// Start the Loop.
		while ( have_posts() ) : the_post();

			#} display each project as a card :)

				$projectDate = get_the_date('F jS Y');

				#} Featured image?
				$projectFeaturedImage = '';
				$thumb_id = get_post_thumbnail_id();
				if (isset($thumb_id) && !empty($thumb_id)){
					$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
					if (isset($thumb_url_array) && is_array($thumb_url_array) && isset($thumb_url_array[0])) $projectFeaturedImage = $thumb_url_array[0];
				}

			?>
				<div class="ui card">
				<?php if (!empty($projectFeaturedImage)){ ?>
					<a class="image" href="<?php echo get_permalink(); ?>">
						<img src="<?php echo $projectFeaturedImage; ?>" alt="<?php the_title(); ?>">
					</a>
				<?php } else { ?>
          <a class="image" href="<?php echo get_permalink(); ?>">
              <img alt="<?php the_title(); ?>" src="<?php projectPagesDefaultImage(); ?>">
          </a>
				<?php } ?>
				  <div class="content">
				    <a class="header" href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
				    <div class="meta">
				      <?php echo $projectDate; ?>
				    </div>
				  </div>
				</div>

			<?php

		// End the loop.
		endwhile;

		// Previous/next page navigation.
		the_posts_pagination( array(
			'prev_text'          => __( 'Previous page', 'projectpages' ),
			'next_text'          => __( 'Next page', 'projectpages' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'projectpages' ) . ' </span>',
		) );

	
		#} Close the wrap
		?>
            </div><!-- /cards -->
	        </div>
	      </div>
	    </div>
	  </div>
	  <?php

	// If no content, include the "No posts found" template.
	else :
		?>

		<div class="ui text container">
	      <h1 class="ui inverted header">
          <?php echo __('Projects Tagged','projectpages').' '.$archiveTitle; ?>
	      </h1>
	      <h2><?php printf( esc_html__( 'There are no Public Projects on %1$s', 'projectpages'), $blogTitle ); ?></h2>
	    </div>

	  </div><?php

	endif;
	?>
    

  <?php projectPages_get_template_part('projectpages-footer'); #get_template_part('projectpages-footer'); # Include our footer ?>


</div>

</body>

</html>