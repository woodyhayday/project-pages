<?php
/**
 * Custom template for Project Pages
 * v2.0
 */

	#} Got post?
	global $post; if (!isset($post) || !isset($post->ID) || (isset($post->post_status) && $post->post_status != 'publish')) {
		
		#} Send home - this'll probably never happen anyway.
		global $wp_query;		
	    $wp_query->set_404();
	    status_header( 404 );
	    nocache_headers();
	    exit();

	} 

	#} Subtle efficiency drive.

		#} Post specifics:
		$projectTitle = $post->post_title;
		$projectTitleChecked = $projectTitle; if (empty($projectTitle)) $projectTitleChecked = 'Untitled Project #'.$post->ID;
		$projectURL = get_post_permalink($post->ID);
		$projectMeta = projectPages_getProjectMeta($post->ID);
		$projectSummary = nl2br(htmlspecialchars_decode(get_post_meta($post->ID, 'whpp_project_summary' , true )));
    $projectSummaryForOG = strip_tags($projectSummary); $projectSummaryForOG = str_replace('"',"'",$projectSummaryForOG);
		$projectBody = htmlspecialchars_decode(get_post_meta($post->ID, 'whpp_project_body' , true ));
    $projectBody = apply_filters('the_content', $projectBody); # http://wordpress.stackexchange.com/questions/21473/why-does-the-html-editor-not-wrap-my-code-in-paragraph-tags-when-i-press-enter
    $projectTags = wp_get_post_terms($post->ID,'projectpagetag');
		$projectDate = date('F jS Y',strtotime($post->post_modified));
    $projectHeaderType = get_post_meta($post->ID, 'noheader' , true );
    $usingLogging = projectPages_getSetting('use_logs');
    $projectLogs = array(); if ($usingLogging == "1") $projectLogs = projectPages_getLogs($post->ID,true,100);


			#} Featured image?
			$projectFeaturedImage = '';
			$thumb_id = get_post_thumbnail_id();
			if (isset($thumb_id) && !empty($thumb_id)){
				$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
				if (isset($thumb_url_array) && is_array($thumb_url_array) && isset($thumb_url_array[0])) $projectFeaturedImage = $thumb_url_array[0];
			}

      #} if Required, use og fallback
      $projectFeaturedImageChecked = $projectFeaturedImage; if (empty($projectFeaturedImageChecked)) $projectFeaturedImageChecked = PROJECTPAGES_URL.'i/projectpages.png';

      #} Project Logs?


		#} Some basics:
		$blogTitle = get_bloginfo('name');
		$blogURL = get_bloginfo('url');
		$projectsTitle = __('Projects','projectpages');
    $projectsURL = projectPages_projects_root_url();

		#} Pretty up page title.
		$pageTitle = __('Project','projectpages'); if (!empty($projectTitle)) $pageTitle = $projectTitle; if (!empty($blogTitle)) $pageTitle .= ' | '.$blogTitle;

    #} Pass to footer
    global $projectPagesFooterInfo; $projectPagesFooterInfo = array('blogtitle' => $blogTitle,'blogurl'=>$blogURL,'projectstitle'=>$projectsTitle,'projectsurl'=>$projectsURL,'projecttags'=>$projectTags);

    // simple hitcounter
    $hits = projectPages_basic_hitcounter( $post->ID );

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
  <link rel='stylesheet' id='dashicons-css' href='<?php echo includes_url(); ?>/css/dashicons.min.css?ver=6.5.2' media='all' />   
  <?php
    
    #} Favicon?
    $faviconURL = projectPages_getSetting('favicon');
    if (!empty($faviconURL)) echo '<link rel="shortcut icon" href="'.$faviconURL.'" type="image/x-icon" />'; 

  ?>

  <!-- OG Meta -->
  <meta property="og:title" content="<?php echo $pageTitle; ?>" />
  <meta property="og:type" content="blog" />
  <meta property="og:url" content="<?php echo $projectURL; ?>" />
  <meta property="og:image" content="<?php echo $projectFeaturedImageChecked; ?>" />
  <meta property="og:site_name" content="<?php echo $blogTitle; ?>" />
  <meta property="og:description" content="<?php echo $projectSummaryForOG; ?>" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:title" content="<?php echo $pageTitle; ?>" />
  <meta name="twitter:description" content="<?php echo $projectSummaryForOG; ?>" />
  <meta name="twitter:image" content="<?php echo $projectFeaturedImageChecked; ?>" />
  <meta itemprop="image" content="<?php echo $projectFeaturedImageChecked; ?>" />


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

    /* few tweaks */
    #project-page-summary {
      padding: 5em 0em;
    }
    #project-page-summary .statuscard {
      margin-top: 1em;  
    }
    #project-page-summary-content {
      text-align:justify;
      font-size: 1.3em;
      line-height: 1.5em;
    }
    #project-page-summary-content h2 {
          margin-top: 0.6em !important;
    }
    #project-page-summary-img {      
      text-align: center;
      width: 290px;
      max-width: 290px;
      float: right;
    }
    #project-page-summary-img img {
      max-width:50%;
    }
    #project-page-demo-link {
      margin-top: 1em;  
      text-align:center; 
      width: 290px;
      max-width: 290px;
      float: right;
    }
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


    /* WP Content helper styles */
    .wp-body {
      font-size:1.2em; /* Preference, here, really */
    }
    .wp-body .caption {

      font-size: 1.4em;
      text-align: center;
      border-bottom: 2px solid #64b0ed;
      margin-top: 3em;

    }
    .wp-body .aligncenter {

        margin-left: auto;
        margin-right: auto;
        display: block;

    }
    .wp-body img.size-full {

        max-width: 80%;
        width: 80%;
        height: auto;
        margin-left: auto;
        margin-right: auto;
        display: block;

    }
    .wp-body p {
      margin-bottom:1.3em !important;
    }
    .wp-body ul, .wp-body ol {        
      font-size: 1.2em;
      line-height: 1.3em;
      margin-top: 2em;
      margin-bottom: 2em;
    }
    .wp-body ul li, .wp-body ol li {  
      margin-bottom: 0.8em;
    }
    .wp-body h2, .wp-body h3 {
      margin-top: 1.7em !important;
    }
    #project-page-body {
      padding-top:3em !important;
    }

    .wp-body p iframe {

      max-width: 100%;

    }

    #whppLogs .whppMessage .header {

      margin-top: 0.2em !important;
      margin-bottom: 0.6em;

    }
    #whppLogs .whppMessage p {
      font-size: 1em;
    }
    #whppLogs .whppMessage .dashicons {

      margin-right: 0.7em;
      margin-top: 0.2em;

    }

    .ui.menu .item:before {
      background:none !important;
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
    #project-page-body img {
        max-width: 100%;
        width: 100%;
        height: auto;
    }
    <?php

    // Header backgrounds :)
    // v2.0 1 = color, -1 = featimg, 2 = imgurl, 3 = vidurl, 4 = gradient
    switch ( $projectHeaderType ){

      // -1 = Featured Image
      case -1:

        if ( !empty( $projectFeaturedImage ) ){ ?>
          #projectmasthead {
            background-image: url("<?php echo $projectFeaturedImage; ?>");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 50% 50%;
          }
        <?php 

        }

        break;


      // 1 = background colour
      case 1:

        echo '#projectmasthead { background-color:'.$projectMeta['headerbg'].' !important; }';

        break;

      // 2 = image by URL
      case 2:

        // got img?
        $bg_img_url = ''; if ( isset( $projectMeta['headerbg_imgurl'] ) && !empty( $projectMeta['headerbg_imgurl'] ) ){
          $bg_img_url = sanitize_url( $projectMeta['headerbg_imgurl'] );
        }

        if ( $bg_img_url ){
          ?>
            #projectmasthead {
              background-image: url("<?php echo $bg_img_url; ?>");
              background-size: cover;
              background-repeat: no-repeat;
              background-position: 50% 50%;
            }
          <?php 
        }
        break;

      // 3 = video by URL
      case 3:

        // got video? - set this, caught below
        $bg_video_url = ''; if ( isset( $projectMeta['headerbg_vidurl'] ) && !empty( $projectMeta['headerbg_vidurl'] ) ){
          $bg_video_url = sanitize_url( $projectMeta['headerbg_vidurl'] );

          ?>
            #projectmasthead {
              overflow: hidden;
            }
            #projectmasthead .ui.container {
              position: relative;
            }
          <?php 
        }

        break;

      // 4 = gradient (prescribed)
      case 4:

        // got gradient? - set this, caught below
        $bg_gradient = ''; if ( isset( $projectMeta['headerbg_gradient'] ) && !empty( $projectMeta['headerbg_gradient'] ) ){
          $bg_gradient = (int)$projectMeta['headerbg_gradient'];
        }

        break;

    }
?>
    .pp-video-bg {
      position: absolute;
      top: 50%;
      left: 50%;
      /*object-fit: cover;*/
      transform: translate(-50%, -50%);
      min-width: 100%;
      min-height: 100%;
      width: auto;
      height: auto;
    }    
    .pp-gradient-1 {
      
      color: #FFF !important;
      background-image: radial-gradient( circle farthest-corner at 10% 20%,  rgba(100,43,115,1) 0%, rgba(4,0,4,1) 90% ) !important;

    }
    .pp-gradient-1 span {
      color: #FFF !important;
    }
    .pp-gradient-2 {
      
      color: #000 !important;
      background-color: #85FFBD !important;
      background-image: linear-gradient(45deg, #85FFBD 0%, #FFFB7D 100%) !important;

    }
    .pp-gradient-2 h1,
    .pp-gradient-2 h2,
    .pp-gradient-2 a,
    .pp-gradient-2 .whpp-sharewrap,
    .pp-gradient-2 .ui.secondary.inverted.pointing.menu .item {      
      color: #000 !important;
      border:0 !important;
    }
    .pp-gradient-2 .ui.secondary.inverted.pointing.menu .active.item {     
      border-color: #000 !important;
      border-bottom:2px solid #000 !important;
    }
    .pp-gradient-3 {
      
      background-color: #FBAB7E !important;
      background-image: linear-gradient(62deg, #FBAB7E 0%, #F7CE68 100%) !important;

    }
    .pp-gradient-3 h1,
    .pp-gradient-3 h2,
    .pp-gradient-3 a,
    .pp-gradient-3 .whpp-sharewrap,
    .pp-gradient-3 .ui.secondary.inverted.pointing.menu .item {      
      color: #000 !important;
      border:0 !important;
    }
    .pp-gradient-3 .ui.secondary.inverted.pointing.menu .active.item {     
      border-color: #000 !important;
      border-bottom:2px solid #000 !important;
    }
    .pp-gradient-4 {
      
      color: #FFF !important;
      background-color: #FF9A8B !important;
      background-image: linear-gradient(90deg, #FF9A8B 0%, #FF6A88 55%, #FF99AC 100%) !important;

    }
    .pp-gradient-4 span {
      color: #FFF !important;
    }
    .pp-gradient-5 {
      
      color: #FFF !important;
      background-color: #FFE53B !important;
      background-image: linear-gradient(147deg, #FFE53B 0%, #FF2525 74%) !important;

    }
    .pp-gradient-5 span {
      color: #FFF !important;
    }
    .pp-gradient-6 {
      
      background-color: #52ACFF !important;
      background-image: linear-gradient(180deg, #52ACFF 25%, #FFE32C 100%) !important;

    }
    .pp-gradient-6 h1,
    .pp-gradient-6 h2,
    .pp-gradient-6 a,
    .pp-gradient-6 .whpp-sharewrap,
    .pp-gradient-6 .ui.secondary.inverted.pointing.menu .item {      
      color: #000 !important;
      border:0 !important;
    }
    .pp-gradient-6 .ui.secondary.inverted.pointing.menu .active.item {     
      border-color: #000 !important;
      border-bottom:2px solid #000 !important;
    }
    .pp-gradient-7 {
      
      background-color: #FFDEE9 !important;
      background-image: linear-gradient(0deg, #FFDEE9 0%, #B5FFFC 100%) !important;

    }
    .pp-gradient-7 h1,
    .pp-gradient-7 h2,
    .pp-gradient-7 a,
    .pp-gradient-7 .whpp-sharewrap,
    .pp-gradient-7 .ui.secondary.inverted.pointing.menu .item {      
      color: #000 !important;
      border:0 !important;
    }
    .pp-gradient-7 .ui.secondary.inverted.pointing.menu .active.item {     
      border-color: #000 !important;
      border-bottom:2px solid #000 !important;
    }
    .pp-gradient-8 {
      
      color: #FFF !important;
      background-color: #21D4FD !important;
      background-image: linear-gradient(19deg, #21D4FD 0%, #B721FF 100%) !important;

    }
    .pp-gradient-8 span {
      color: #FFF !important;
    }
    .pp-gradient-9 {
      
      color: #FFF !important;
      background-color: #F4D03F !important;
      background-image: linear-gradient(132deg, #F4D03F 0%, #16A085 100%) !important;

    }
    .pp-gradient-9 span {
      color: #FFF !important;
    }
    .pp-gradient-10 {

      background-color: #0093E9 !important;
      background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%) !important;

    }
    .pp-gradient-10 h1,
    .pp-gradient-10 h2,
    .pp-gradient-10 a,
    .pp-gradient-10 .whpp-sharewrap,
    .pp-gradient-10 .ui.secondary.inverted.pointing.menu .item {      
      color: #000 !important;
      border:0 !important;
    }
    .pp-gradient-10 .ui.secondary.inverted.pointing.menu .active.item {     
      border-color: #000 !important;
      border-bottom:2px solid #000 !important;
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

<?php while ( have_posts() ) : the_post(); #} Loop me! ?>

<!-- Following Menu -->
<div class="ui large top fixed hidden menu" id="fixedMenu">
  <div class="ui container">
    <a class="item" href="<?php echo $blogURL; ?>"><?php echo $blogTitle; ?></a>
    <i class="right angle icon menubreadcrumb"></i> 
    <a class="item" href="<?php echo $projectsURL; ?>"><?php echo $projectsTitle; ?></a>
    <i class="right angle icon menubreadcrumb"></i> 
    <a class="active item" href="<?php echo $projectURL; ?>"><?php echo $projectTitleChecked; ?></a>
  </div>
</div>

<!-- Sidebar Menu -->
<div class="ui vertical inverted sidebar menu">
  <a class="item" href="<?php echo $blogURL; ?>"><?php echo $blogTitle; ?></a>
  <a class="item" href="<?php echo $projectsURL; ?>"><?php echo $projectsTitle; ?></a>
  <a class="active item" href="<?php echo $projectURL; ?>"><?php echo $projectTitleChecked; ?></a>
</div>

<!-- Page Contents -->
<div class="pusher">
  <div class="ui inverted vertical masthead center aligned segment<?php if ( isset( $bg_gradient ) && $bg_gradient > 0 ){ echo ' pp-gradient-' . $bg_gradient; } ?>" id="projectmasthead"> 
  <?php

    // if using a video cover, inject it here
    if ( isset( $bg_video_url ) && !empty( $bg_video_url ) ){

      ?>      
      <video autoplay muted loop class="pp-video-bg">
          <source src="<?php echo $bg_video_url; ?>" type="video/mp4">
          Your browser does not support HTML5 video.
      </video>    
      <?php

    }

  ?>

    <div class="ui container">
      <div class="ui large secondary inverted pointing menu">
        <a class="toc item">
          <i class="sidebar icon"></i>
        </a>
    	<a class="item" href="<?php echo $blogURL; ?>"><?php echo $blogTitle; ?></a>
    	<i class="right angle icon menubreadcrumb"></i> 
        <a class="item" href="<?php echo $projectsURL; ?>"><?php echo $projectsTitle; ?></a>
    	<i class="right angle icon menubreadcrumb"></i> 
    	<a class="active item" href="<?php echo $projectURL; ?>"><?php echo $projectTitleChecked; ?></a>
      </div>
    </div>

    <div class="ui text container">
      <h1 class="ui inverted header">
        <?php echo $projectTitleChecked; ?>
      </h1>
      <?php if (is_array($projectMeta) && isset($projectMeta['biline'])) echo '<h2>'.projectPages_textExpose($projectMeta['biline']).'</h2>'; ?>
      <?php $shareImg = ''; if (isset($projectFeaturedImage) && !empty($projectFeaturedImage)) $shareImg = $projectFeaturedImage; projectPages_shareOut($projectTitleChecked.' on '.$blogTitle,$projectURL,$shareImg); ?>
    </div>

  </div>

  <?php if (isset($projectSummary) && !empty($projectSummary)){ ?>
  <div class="ui vertical stripe segment" id="project-page-summary">
    <div class="ui aligned stackable grid container">
      <div class="row">
        <div class="ten wide column wp-body" id="project-page-summary-content">
          <h2><?php _e('Summary','projectpages'); ?></h2>
          <?php echo $projectSummary; ?>
        </div>
        <div class="one wide column"><div style="clear">&nbsp;</div></div>
        <div class="five wide column">
          <?php


              #} Featured image?
              $projectFeaturedImage = '';
              $thumb_id = get_post_thumbnail_id();
              if (isset($thumb_id) && !empty($thumb_id)){
                $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
                if (isset($thumb_url_array) && is_array($thumb_url_array) && isset($thumb_url_array[0])) $projectFeaturedImage = $thumb_url_array[0];
              }

              if (!empty($projectFeaturedImage)){ ?><div id="project-page-summary-img"><img src="<?php echo $projectFeaturedImage; ?>" alt="<?php the_title(); ?>" /></div><?php }

          ?>
          <div class="ui card statuscard">
      		  <?php 

      		  	#} Status
      		  	if (is_array($projectMeta) && isset($projectMeta['status'])) {

      		  		global $projectPageStatuses;
      		  		$statusStr = ''; $statusColourClass = ''; if (isset($projectPageStatuses[$projectMeta['status']])) {
      		  			$statusStr = $projectPageStatuses[$projectMeta['status']][0];
      		  			$statusColourClass = $projectPageStatuses[$projectMeta['status']][1];
      		  		}
      		  		
      		  		?>
              <div class="content">
		              <i class="right floated circle icon <?php echo $statusColourClass; ?>"></i>
		              <div class="header"><?php _e('Status','projectpages'); echo ': '.$statusStr; ?></div>
		              <div class="meta"><?php echo __('Updated','projectpages').': '.$projectDate; ?></div>
	            </div>
              		<?php

      		  	}

              #} Tags?
              if (is_array($projectTags) && count($projectTags) > 0){

                ?>
                <div class="content">
                  <div class="description">
                    <p><i class="tags icon"></i> <?php _e('Tagged','projectpages'); ?>: <?php $tagIndx = 0; foreach ($projectTags as $tag){ if ($tagIndx > 0) echo ', '; ?><a href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo $tag->name; ?></a><?php $tagIndx++; } ?></p>
                  </div>
                </div>
                <?php

              }

              #} Logs?
              if (is_array($projectLogs) && count($projectLogs) > 0){

                ?>
                <div class="content">
                  <div class="description">
                    <p><i class="remove bookmark icon"></i> <?php echo count($projectLogs).' x <a href="#project-logs">'.__('Project Logs','projectpages'); ?></a></p>
                  </div>
                </div>
                <?php

              }

      		  ?>
          </div>
          <?php 

            #} Any demo url?
            $demoUrl = ''; if (is_array($projectMeta) && isset($projectMeta['demourl']) && !empty($projectMeta['demourl'])) $demoUrl = $projectMeta['demourl'];
            $demoText = __('View','projectpages'); if (is_array($projectMeta) && isset($projectMeta['demolinktext']) && !empty($projectMeta['demolinktext'])) $demoText = $projectMeta['demolinktext'];

            if (!empty($demoUrl)){

              ?><div id="project-page-demo-link"><a href="<?php echo $demoUrl; ?>" class="ui primary button" target="_blank"><?php echo $demoText; ?></a></div><?php

            }

          ?>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>


  <?php if (isset($projectBody) && !empty($projectBody)){ ?>
  <div class="ui vertical stripe segment" id="project-page-body">
    <div class="ui middle aligned stackable grid container">
      <div class="row">
  <?php
    
    #} useThinLayout?
    $useThinLayout = projectPages_getSetting('thin_column');
    if ( $useThinLayout && $useThinLayout == "1") {
      ?><div class="three wide column">&nbsp;</div>
      <div class="ten wide column wp-body"><?php
    } else {
      ?><div class="sixteen wide column wp-body"><?php
    } ?>
          <?php echo $projectBody; ?>
        </div>
    <?php 
    if ( $useThinLayout && $useThinLayout == "1") {
      ?><div class="three wide column">&nbsp;</div><?php
    } ?>
      </div>
    </div>
  </div>
  <?php } ?>

  <?php 

        if (count($projectLogs) > 0) {

          ?><div class="ui vertical stripe segment" id="whppLogs">
            <div class="ui middle aligned stackable grid container">
              <div class="row">
                <?php

                if ( isset( $useThinLayout ) && $useThinLayout == "1") {
                    ?><div class="three wide column">&nbsp;</div>
                    <div class="ten wide column wp-body"><?php
                  } else {
                    ?><div class="sixteen wide column wp-body"><?php
                  } 

                    echo '<h2 class="ui header huge" style="margin-top: 0 !important;text-align:center;" id="project-logs">'.__('Project Log','projectpages').'</h2>';
                  
                    foreach ($projectLogs as $log){

                      ?><div class="ui info message whppMessage">
                      <?php if ( $log['date'] ) echo '<div class="ui label top right attached">' . $log['date'] . '</div>'; ?>
                          <div class="header">
                            <?php

                              if ( $log['icon'] ){
                                
                                ?><span class="dashicons <?php echo $log['icon']; ?>"></span><?php 
                              
                              } else {

                                ?><span class="dashicons  dashicons-arrow-right"></span><?php

                              }                              

                              if ( $log['title'] ){
                                
                                echo $log['title'];
                              
                              } else {

                                _e( 'Log', 'projectpages' );

                              }

                            ?>
                          </div>
                          <?php 

                              if ( $log['body'] ){
                                
                                echo  htmlspecialchars_decode( $log['body'] );
                              
                              } else {

                                // silence.

                              }

                          ?>
                        </div><?php

                    }

                  ?></div><?php
                   
                  if ( isset( $useThinLayout ) && $useThinLayout == "1") {
                    ?><div class="three wide column">&nbsp;</div><?php
                  } 

          ?> </div>  
            </div>
          </div><?php

        }


  ?>


<?php endwhile; #} End of the loop. ?>
    

  <?php projectPages_get_template_part('projectpages-footer'); #get_template_part('projectpages-footer'); # Include our footer ?>


  </div>
</div>

</body>

</html>