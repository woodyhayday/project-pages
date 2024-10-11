<?php
/**
 * Custom template for Project Page Footer (as used throughout templates)
 */

  #} localise
  global $projectPagesFooterInfo,$projectPages_urls;

  #} If a template exists, for additional footer stuff, include it :) (Let's users create auto-append things without template updates breaking them!)
  projectPages_get_template_part('projectpages-pre-footer');

?>
  <div class="ui inverted vertical footer segment">
    <div class="ui container">
      <div class="ui stackable inverted divided equal height stackable grid">
        <div class="eight wide column">
          <?php

            #} Just show 3 other projects here?
            $showRandomProjects = false;
            $projectFooterTitle = __('Trending Projects','projectpages'); # not really trending, basically random, probably :o

            #} if in single, find related posts
            if (!$showRandomProjects && isset($projectPagesFooterInfo['projecttags'])){

              #} Try and retrieve "related projects" (by tag)
              #} Note: by slug sucks here, so using id, (as per http://wordpress.stackexchange.com/questions/84607/custom-taxonomy-and-tax-query)

              if (count($projectPagesFooterInfo['projecttags']) > 0){

                  $taxonomy_list = wp_list_pluck( $projectPagesFooterInfo['projecttags'], 'term_id' );

                  $related_projects_args = array(
                                    'post_type'      => 'projectpage',
                                    'posts_per_page' => 3,
                                    'post_status'    => 'publish',
                                    'post__not_in'   => array( get_the_ID() ),
                                    'tax_query'      => array(
                                      array(
                                        'taxonomy' => 'projectpagetag',
                                        'fields'   => 'id',
                                        'terms'    => $taxonomy_list
                                      )
                                    )
                                  );


                  #} retrieve em...
                  $relatedProjects = new WP_Query( $related_projects_args );

                  if( !$relatedProjects->have_posts() ):

                    #} default to random others, as shown on other page footers.
                    $showRandomProjects = true;

                  else: 

                    #} output these (just pass the var ref for now)
                    $projectsOut = $relatedProjects;

                    #} and set title
                    $projectFooterTitle = __('Related Projects','projectpages');


                  endif;


              } 

            } else $showRandomProjects = true;



            if ($showRandomProjects){

                #} just show random projects!
                $projects_args = array(
                                  'post_type'      => 'projectpage',
                                  'posts_per_page' => 3,
                                  'post_status'    => 'publish',
                                  'post__not_in'   => array( get_the_ID() ),
                                  'orderby'        => 'rand'
                                );

                #} retrieve em...
                $projectsOut = new WP_Query( $projects_args );

            }


          if(isset($projectsOut) && $projectsOut->have_posts() ): ?>
          <h4 class="ui inverted header"><?php echo $projectFooterTitle; ?></h4>

          <div class="ui three cards">
                <?php while( $projectsOut->have_posts() ): $projectsOut->the_post(); 
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

                <?php endwhile; ?>
          </div>

          <?php endif; ?>
          
        </div>
        <?php 
        #} Any widgets?
        $widgetsPresentMid = true;
        if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Project Pages Footer Mid") ) : $widgetsPresentMid = false; ?>
        <?php endif; ?>

        <div class="<?php if ($widgetsPresentMid) echo 'three'; else echo 'five'; ?> wide column">
          <?php 
          #} Any widgets?
          $widgetsPresentRight = true;
          if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Project Pages Footer Right") ) : $widgetsPresentRight = false; ?>
          <?php endif; ?>

          <h4 class="ui inverted header"<?php if ($widgetsPresentRight) echo ' style="border-top: 1px solid #393939;padding-top: 16px;"'; ?>>
            <a href="<?php if (isset($projectPagesFooterInfo['projectsurl'])) echo $projectPagesFooterInfo['projectsurl']; else echo '#'; ?>"><?php if (isset($projectPagesFooterInfo['projectstitle'])) echo $projectPagesFooterInfo['projectstitle']; else _e('Projects','projectpages'); ?></a> 
            on 
            <a href="<?php if (isset($projectPagesFooterInfo['blogurl'])) echo $projectPagesFooterInfo['blogurl']; else echo '#'; ?>"><?php if (isset($projectPagesFooterInfo['blogtitle'])) echo $projectPagesFooterInfo['blogtitle']; ?></a> 
          </h4>
          <?php 

              #} Sharing love?
              $shareLove = projectPages_getSetting('poweredby');
              if ($shareLove == "1") { ?><p>Powered by <a href="<?php echo $projectPages_urls['home']; ?>" target="_blank">Project Pages</a></p><?php }

          ?>
        </div>
      </div>
    </div>
  </div>


  <?php

    #} Retrieve & set fb app id + tw id
    $fbAppID = projectPages_getSetting('fbappid');
    $twHandle = projectPages_getSetting('twvia');

    #} Pass fb app id + twitter handle, for sharing, if desired
    echo '<script type="text/javascript">var ppFBAppID = "'.$fbAppID.'";ppTwitterHandle = "'.$twHandle.'";</script>';

  ?>
  <script src="<?php echo PROJECTPAGES_URL.'js/ProjectPages.Legacy.min.js'; ?>"></script>