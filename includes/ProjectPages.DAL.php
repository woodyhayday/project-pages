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


   function projectPages_statuses_all(){

    global $projectPageStatuses;
    return array_keys( $projectPageStatuses );
    
   }
   function projectPages_statuses_idea(){

    return array('idea');
    
   }
   function projectPages_statuses_active(){

    return array('inprogress');
    
   }
   function projectPages_statuses_archived_completed(){

    return array('completed','completedsuccess','completedfailure','shelved','archived','abandoned','evolved');
    
   }
   function projectPages_statuses_active_archived_completed(){

    return array(
        'inprogress',  
        'completed','completedsuccess','completedfailure','shelved','archived','abandoned','evolved'
      );
    
   }

   function projectPages_getProjectMeta($pID=-1){

   		if ($pID > 0) return get_post_meta($pID, 'whpp_meta', true);

   		return FALSE;

   }

   function projectPages_setProjectMeta($pID=-1,$metaArr=array()){

      if ($pID > 0 && is_array($metaArr)) return update_post_meta($pID, 'whpp_meta', $metaArr);

      return FALSE;

   }

   function projectPages_getProjectStatus($pID=-1){

      if ($pID > 0) return get_post_meta($pID, 'whpp_meta_status', true);

      return FALSE;

   }

   function projectPages_setProjectStatus($pID=-1,$status='idea'){

      if ($pID > 0) return update_post_meta($pID, 'whpp_meta_status', $status);

      return FALSE;

   }

   function projectPages_getProjectTags( $pID = -1 ){

    if ( $pID ) return wp_get_post_terms( $pID, 'projectpagetag' );

    return FALSE;

   }

   function projectPages_getProjectBody( $pID = -1 ){

    if ( $pID ) {

      $b = htmlspecialchars_decode( get_post_meta( $pID, 'whpp_project_body' , true ) );
      $b = apply_filters('the_content', $b); 
      return $b;

    }

    return FALSE;

   }

   function projectPages_getProjectSummary( $pID = -1 ){

    if ( $pID ) return nl2br(htmlspecialchars_decode( get_post_meta( $pID, 'whpp_project_summary' , true ) ) );

    return FALSE;

   }

   function projectPages_getProjectPermalink( $pID = -1 ){

    if ( $pID ) return get_post_permalink( $pID );

    return FALSE;

   }

   function projectPages_getProjectHeaderType( $pID = -1 ){

    if ( $pID ) return get_post_meta( $pID, 'noheader' , true );

    return FALSE;

   }

   function projectPages_getProjectFeatImg( $pID = -1 ){

    if ( $pID ){

      $projectFeaturedImage = '';
      $thumb_id = get_post_thumbnail_id( $pID );
      if ( isset( $thumb_id ) && !empty( $thumb_id ) ){
        $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
        if (isset($thumb_url_array) && is_array($thumb_url_array) && isset($thumb_url_array[0])) $projectFeaturedImage = $thumb_url_array[0];
      }

      return $projectFeaturedImage;

    }

    return FALSE;

   }


   // see: projectPages_getProjects_active post v2
   function projectPages_getQueryAllActive(){

      return new WP_Query(array('post_status'=>'publish','post_type'=>'projectpage',
        'meta_query' => array( 
          array(
            'key' => 'whpp_meta_status',
            'value' => array('inprogress'),
            'compare' => 'IN'
          )
        )
      ));

   }

   // see: projectPages_getProjects_archived post v2
   function projectPages_getQueryAllArchived(){

      return new WP_Query(array('post_status'=>'publish','post_type'=>'projectpage',
        'meta_query' => array( 
          array(
            'key' => 'whpp_meta_status',
            'value' => array('completed','completedsuccess','completedfailure','shelved','archived','abandoned','evolved'),
            'compare' => 'IN'
          )
        )
      ));

   }

   // see: projectPages_getProjects_ideas post v2
   function projectPages_getQueryAllIdeas(){

      return new WP_Query(array('post_status'=>'publish','post_type'=>'projectpage',
        'meta_query' => array( 
          array(
            'key' => 'whpp_meta_status',
            'value' => array('idea'),
            'compare' => 'IN'
          )
        )
      ));

   }


   // general retrieval func
   function projectPages_getProject( $project_id = -1, $wp_post = false, $tidy = true, $with_logs = true ){

      // got id?
      if ( $project_id <= 0 ) return false;

      // if not already got wp_post
      if ( !$wp_post ){

          // retrieve core post
          $wp_post = get_post( $project_id );

      }

      // if no post at this point, probably missing from db
      if ( !$wp_post ) return false;


      // hydrate, cache, return
      $project = projectPages_hydrate_project( $wp_post, $with_logs ); 


      // tidy?
      if ( $tidy ) $project = projectPages_tidyProject( $project );


      // return
      return $project;


   } 


  // Retrieves & caches a single project ($post), where available
  // re-calling does not re-retrieve details, unless $force_refresh is true
  function projectPages_get_single_project( $force_refresh = false ){

    global $post, $project_pages_single;

    if ( $post && $post->ID ){

      // got cached?
      if ( !$force_refresh && is_array( $project_pages_single ) ) return $project_pages_single;

      // no cache, or force refresh, retrieve:
      $project = projectPages_getProject( $post->ID, $post, true, true );

      // cache it
      $project_pages_single = $project;

      // return it
      return $project;

    }

    return false;

  }

   // general query loop func
   function projectPages_getProjects( 

      $status = '',
      $tag = '',
      $order_by = 'ID',
      $order_direction = 'DESC',
      $limit = 100,
      $full_details = false,
      $tidy = false,
      $meta_query = false,
      $status_array = false, // if status = '', and this is an array
      $search_query = ''

   ){

      $args = array( 
          'post_status' => 'publish',
          'post_type' => 'projectpage'   
        );

      // search
      if ( $search_query !== ''){

        $args['s'] = $search_query;

      }

      // status
      if ( $status !== '' ){
        
        $args['meta_query'] = array( 
            array(
              'key' => 'whpp_meta_status',
              'value' => array( $status ),
              'compare' => 'IN'
            )
        );

      } else {

        // statuses?
        if ( is_array( $status_array ) ){

          $args['meta_query'] = array( 
              array(
                'key' => 'whpp_meta_status',
                'value' => $status_array,
                'compare' => 'IN'
              )
          );

        }


      }

      // tagged
      if ( $tag !== '' ){

        $args['tax_query'] = array(
            array (
                'taxonomy' => 'projectpagetag',
                'field' => 'slug',
                'terms' => $tag
            )
        );


      } 

      // direct passing of meta query
      if ( is_array( $meta_query ) ){

        $args['meta_query'] = $meta_query;

      }

      // order
      $args['orderby'] = $order_by;
      $args['order'] = $order_direction;
      $args['posts_per_page'] = $limit;

      // le query
      $query = new WP_Query( $args );
      $return_posts = $query->posts;

      global $testq;
      $testq = $query;

      if ( is_array( $return_posts ) ){

        // fill details
        if ( $full_details ){

          $return_posts_full = array();

          foreach ( $return_posts as $post ){

            $return_posts_full[] = projectPages_hydrate_project( $post ); 

          }

          $return_posts = $return_posts_full; 
          unset( $return_posts_full );

        }


        // tidy?
        if ( $tidy ){

          $return_posts_tidy = array();

          foreach ( $return_posts as $post ){

            $return_posts_tidy[] = projectPages_tidyProject( $post ); 

          }

          $return_posts = $return_posts_tidy; 
          unset( $return_posts_tidy );

        }

      }

      // return
      return $return_posts;

   }


  // improved projectPages_getQueryAllIdeas
   function projectPages_getProjects_ideas(
    
      $status = '',
      $tag = '',
      $order_by = 'ID',
      $order_direction = 'DESC',
      $limit = 100,
      $full_details = false,
      $tidy = false

    ){

      $meta_query = array( 
          array(
            'key' => 'whpp_meta_status',
            'value' => array('idea'),
            'compare' => 'IN'
          )
        );

      return projectPages_getProjects( $status, $tag, $order_by, $order_direction, $limit, $full_details, $tidy, $meta_query );

   }

   // improved projectPages_getQueryAllActive
   function projectPages_getProjects_active(
    
      $status = '',
      $tag = '',
      $order_by = 'ID',
      $order_direction = 'DESC',
      $limit = 100,
      $full_details = false,
      $tidy = false

    ){

      $meta_query = array( 
          array(
            'key' => 'whpp_meta_status',
            'value' => array('inprogress'),
            'compare' => 'IN'
          )
        );

      return projectPages_getProjects( $status, $tag, $order_by, $order_direction, $limit, $full_details, $tidy, $meta_query );

   }

   // improved projectPages_getQueryAllArchived
   function projectPages_getProjects_archived(

      $status = '',
      $tag = '',
      $order_by = 'ID',
      $order_direction = 'DESC',
      $limit = 10,
      $full_details = false,
      $tidy = false

    ){

      $meta_query = array( 
          array(
            'key' => 'whpp_meta_status',
            'value' => array('completed','completedsuccess','completedfailure','shelved','archived','abandoned','evolved'),
            'compare' => 'IN'
          )
        );

      return projectPages_getProjects( $status, $tag, $order_by, $order_direction, $limit, $full_details, $tidy, $meta_query );

   }

   function projectPages_hydrate_project( $projectPostObj = false, $with_logs = true ){

      $n = false;

      if ( $projectPostObj ){

          $n = $projectPostObj;
          $n->status = projectPages_getProjectStatus( $projectPostObj->ID );
          $n->body = projectPages_getProjectBody( $projectPostObj->ID );          
          $n->summary = projectPages_getProjectSummary( $projectPostObj->ID );
          $n->permalink = projectPages_getProjectPermalink( $projectPostObj->ID );
          $n->meta = projectPages_getProjectMeta( $projectPostObj->ID );
          $n->tags = projectPages_getProjectTags( $projectPostObj->ID ); 
          $n->header_type = projectPages_getProjectHeaderType( $projectPostObj->ID );
          if ( $with_logs ){
            $n->logs = projectPages_getLogs( $projectPostObj->ID, true, 100 );
          }
          $n->feat_img = projectPages_getProjectFeatImg( $projectPostObj->ID );

      }

      return $n;

   }

   // get terms for project pages
   // not yet implemented: $order_by = 'user_date', $order_direction = 'DESC', $limit = -1
   function projectPages_getTags( $hide_empty = false, $tidy = false, $for_gutenberg = false, $specific_tags = '' ){

      $return_tags = get_terms([
          'taxonomy'  => 'projectpagetag',
          'hide_empty'    => $hide_empty
        ]);

      // for now return specific tags filtered via php, should move to query.
      if ( !empty( $specific_tags ) ){


        $tags_to_include = explode( ',', str_replace( ', ', ',', str_replace( ' ,', ',', $specific_tags ) ) );

        if ( !is_array( $tags_to_include ) || count( $tags_to_include ) == 0 ){

          // fail. just return the full list

        } else {

          $filtered_tags = array();
          foreach ( $return_tags as $tag ){
            
            if ( in_array( $tag->name,  $tags_to_include ) || in_array( $tag->slug, $tags_to_include ) ){

              $filtered_tags[] = $tag;

            }

          }

          $return_tags = $filtered_tags;

        }

      }

      // tidy
      if ( $tidy ){

        $tidy_tags = array();
        foreach ( $return_tags as $tag ){
          $tidy_tags[] = projectPages_tidyTags( $tag );

        }

        $return_tags = $tidy_tags;


      }

      // gut?
      if ( $for_gutenberg ){

        $gut_tags = array();
        foreach ( $return_tags as $tag ){
          $gut_tags[] = array(
            'label' => $tag->name,
            'value' => $tag->slug // id $tag->term_id
          );
        }

        $return_tags = $gut_tags;

      }

      return $return_tags;

   }


  function projectPages_getProjectCount(){

    $counts = wp_count_posts('projectpage');

    if (isset($counts) && isset($counts->publish)) return (int)$counts->publish;

    return 0;
  }

  // returns count of projects with active/completed status, optionally tagged x
  function projectPages_getProjectCount_filtered( $filter_by_status_array = false, $filter_by_tag_name = false ){

    global $wpdb;

    // default query
    $query = "SELECT COUNT(ID) FROM `wp_posts`
    INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id 
    WHERE
    {$wpdb->posts}.post_type = 'projectpage'
    AND
    {$wpdb->posts}.post_status = 'publish'";

    // if filter by status
    if ( is_array( $filter_by_status_array ) ){

        $query = "SELECT COUNT(ID) FROM `wp_posts`
        INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id 
        WHERE
        {$wpdb->posts}.post_type = 'projectpage'
        AND
        {$wpdb->posts}.post_status = 'publish'
        AND
        {$wpdb->postmeta}.meta_key = 'whpp_meta_status'
        AND 
        {$wpdb->postmeta}.meta_value IN (";

          $status_c = 0;
          foreach ( $filter_by_status_array as $status_str ){

            if ( $status_c > 0 ) $query .= ', ';

            $query .= "'" . $status_str . "'";

            $status_c++;

          }

        $query .= ")";
    }

    // if adding tag limiter - for now, requires status array, 
    // write a proper query layer later.
    if ( !empty( $filter_by_tag_name ) ){

      $query = "SELECT COUNT(ID) FROM `wp_posts`
      INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id 
      INNER JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id
      INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
      INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
      WHERE 
      {$wpdb->posts}.post_type = 'projectpage'
      AND
      {$wpdb->posts}.post_status = 'publish'
      AND
      {$wpdb->postmeta}.meta_key = 'whpp_meta_status'
      AND 
      {$wpdb->postmeta}.meta_value IN (";

          $status_c = 0;
          foreach ( $filter_by_status_array as $status_str ){

            if ( $status_c > 0 ) $query .= ', ';

            $query .= "'" . $status_str . "'";

            $status_c++;

          }

        $query .= ")
      AND
      tt.taxonomy = %s
      AND 
      t.name = %s";

      // Prepare the SQL query to fetch posts
      $query = $wpdb->prepare( $query, 'projectpagetag', $filter_by_tag_name);

    }


    return $wpdb->get_var( $query );

  }


  #} Minified get setting func
  function projectPages_getSetting($key){

    global $projectPages_Settings;
    return $projectPages_Settings->get($key);

  }

// retrieve logs
function projectPages_getLogs( $id = -1, $with_full_details = true, $perPage = 100, $page = 0, $tidied = true, $order_by = 'user_date', $order_direction = 'DESC' ){

  $args = array (
      'post_type'              => 'projectpagelog',
      'post_status'            => 'publish',
      'posts_per_page'         => $perPage,

      // KEY
         'meta_key'   => 'projectpageid',
         'meta_value' => $id
    );

  // order by
  if ( $id === -1 && $order_by == 'user_date' ){

    // hacky feeling wp query meta order solution. Doesn't allow for users who've manually entered bs
    $args['meta_key'] = 'whpp_project_log_datetime';
    unset( $args['meta_value'] );
    $args['orderby'] = 'meta_value_num'; // meta_value_num
    $args['order'] = $order_direction;

  } else {

    // wp post attr order ( bare in mind this'll be the LOG post not the parent project post )
    $args['orderby'] = $order_by;
    $args['order'] = $order_direction;

  }
    
    #} Add page if page... - dodgy meh
    $actualPage = $page-1; if ($actualPage < 0) $actualPage = 0;
    if ($actualPage > 0) $args['offset'] = $perPage*$actualPage;

    $ret = get_posts( $args );

    if ( $with_full_details ){

      // jam in meta
      $retArr = array();
      foreach ($ret as $r){
        $retArrA = $r;
        $retArrA->meta = array(

          'title' => get_post_meta( $r->ID, 'whpp_project_log_title', true ),
          'date' => get_post_meta( $r->ID, 'whpp_project_log_date', true ),
          //datetime is a db cached strtotime variant of date, we won't pass it here as tidy routine rebuilds it
          'icon' => get_post_meta( $r->ID, 'whpp_project_log_icon', true ),
          'body' => get_post_meta( $r->ID, 'whpp_project_log_body', true )
          
        );

        // if no projectpage id specified, retrieve
        if ( $id === -1 ) {
          
          $retArrA->meta['projectpageid'] = get_post_meta( $r->ID, 'projectpageid', true );

        } else {

          $retArrA->meta['projectpageid'] = $id;

        }

        // if no icon (e.g. older versions than 2.0, use default)
        if ( !$retArrA->meta['icon'] ){

          $retArrA->meta['icon'] = 'dashicons-arrow-right';

        }

        if ( $tidied ){

          $retArr[] = projectPages_tidyLog( $retArrA );

        } else {
          
          $retArr[] = $retArrA;

        }
      }

      $ret = $retArr;

    }

    return $ret;
}

// log count
function projectPages_getProjectLogCount(){

  $counts = wp_count_posts('projectpagelog');

  if (isset($counts) && isset($counts->publish)) return (int)$counts->publish;

  return 0;
}

// retrieve log
function projectPages_getLog( $log_post_id = -1, $with_full_details = true, $tidied = true ){

    $post = get_post( $log_post_id );

    if ( $post && $with_full_details ){

      // retrieve meta
      $post->meta = array(

          'projectpageid' => get_post_meta( $post->ID, 'projectpageid', true ),
          'title' => get_post_meta( $post->ID, 'whpp_project_log_title', true ),
          'date' => get_post_meta( $post->ID, 'whpp_project_log_date', true ),
          'icon' => get_post_meta( $post->ID, 'whpp_project_log_icon', true ),
          'body' => get_post_meta( $post->ID, 'whpp_project_log_body', true )
          
        );

      // if no icon (e.g. older versions than 2.0, use default)
      if ( !$post->meta['icon'] ){

        $post->meta['icon'] = 'dashicons-arrow-right';

      }

    }

    if ( $tidied ) {
      
      return projectPages_tidyLog( $post );

    }
    return $post;
}

// 2.0 -> Add log
function projectPages_add_log( $project_page_post_id = false, $log_data = false, $return_new_post_obj = false ){

  if ( $project_page_post_id && is_array( $log_data ) ){

      // create new
      $new_post_id = wp_insert_post( array( 'post_type' => 'projectpagelog', 'post_status' => 'publish' ) );


      // Save content (assumes clean)
      update_post_meta( $new_post_id, 'projectpageid', $project_page_post_id);
      update_post_meta( $new_post_id, 'whpp_project_log_title', $log_data['title'] );
      update_post_meta( $new_post_id, 'whpp_project_log_date', $log_data['date'] );
      update_post_meta( $new_post_id, 'whpp_project_log_datetime', strtotime( $log_data['date'] ) ); // allows meta querying
      update_post_meta( $new_post_id, 'whpp_project_log_icon', $log_data['icon'] );
      update_post_meta( $new_post_id, 'whpp_project_log_body', $log_data['body'] );

      // want the obj back?
      if ( $return_new_post_obj ){ return projectPages_getLog( $new_post_id, true, true ); }

      return $new_post_id;

  }

}

// 2.0 -> Update log
function projectPages_update_log( $log_post_id = false, $log_data = false, $return_new_post_obj = false ){

  if ( $log_post_id && is_array( $log_data ) ){

      // Overwrite content (assumes clean)
      update_post_meta( $log_post_id, 'whpp_project_log_title', $log_data['title'] );
      update_post_meta( $log_post_id, 'whpp_project_log_date', $log_data['date'] );
      update_post_meta( $log_post_id, 'whpp_project_log_datetime', strtotime( $log_data['date'] ) ); // allows meta querying
      update_post_meta( $log_post_id, 'whpp_project_log_icon', $log_data['icon'] );
      update_post_meta( $log_post_id, 'whpp_project_log_body', $log_data['body'] );

      // want the obj back?
      if ( $return_new_post_obj ){ return projectPages_getLog( $log_post_id, true, true ); }

      return $log_post_id;

  }

}

// 2.0 -> delete log
function projectPages_delete_log( $log_post_id = false ){

  if ( $log_post_id ){

    if (current_user_can( 'delete_post', $log_post_id ) ) { 

        return wp_delete_post( $log_post_id );

    }

  }

  return false;

}



// simplify return
// $project_post_obj may or may not be hydrated
function projectPages_tidyProject( $project_post_obj = false ){

    if ( $project_post_obj ){

      // core post stuff
      $project = array(

        'ID' => $project_post_obj->ID,
        'title' => $project_post_obj->post_title,
        'title_checked' => ( !empty( $project_post_obj->post_title ) ? $project_post_obj->post_title : __( 'Untitled Project #', 'projectpages' ) . $project_post_obj->ID ),
        'date' => date( 'F jS Y', strtotime( $project_post_obj->post_date ) ),
        'date_full' => $project_post_obj->post_date, // was post_modified pre v2.0
        'timestamp' => strtotime( $project_post_obj->post_date ) // set timestamp (as of yet no localisation)        

      );

      // currently meta (own DBT later)
      if ( isset( $project_post_obj->status ) ) { 
        
        $project['status'] = $project_post_obj->status; 
        
        // nicety, if got status, also add status colour :)
        global $projectPageStatuses;
        if ( isset( $projectPageStatuses[ $project['status'] ] ) ) {
          $project['status_label'] = $projectPageStatuses[ $project['status'] ][0];
          $project['status_colour'] = $projectPageStatuses[ $project['status'] ][1];
        }
      }
      if ( isset( $project_post_obj->body ) ) { $project['body'] = $project_post_obj->body; }
      if ( isset( $project_post_obj->summary ) ) { 
        $project['summary'] = $project_post_obj->summary; 
        // for ogmeta, often
        $project['summary_excerpt'] = strip_tags( $project['summary'] ); 
        $project['summary_excerpt'] = str_replace( '"', "'", $project['summary_excerpt'] );
      }
      if ( isset( $project_post_obj->permalink ) ) { $project['permalink'] = $project_post_obj->permalink; }
      if ( isset( $project_post_obj->meta ) ) { $project['meta'] = $project_post_obj->meta; }
      if ( isset( $project_post_obj->body ) ) { $project['body'] = $project_post_obj->body; }
      if ( isset( $project_post_obj->tags ) ) { $project['tags'] = $project_post_obj->tags; }
      if ( isset( $project_post_obj->header_type ) ) { $project['header_type'] = $project_post_obj->header_type; }
      if ( isset( $project_post_obj->logs ) ) { $project['logs'] = $project_post_obj->logs; }
      if ( isset( $project_post_obj->feat_img ) ) {
        $project['feat_img'] = $project_post_obj->feat_img;
        $project['feat_img_checked'] = $project['feat_img']; if ( empty( $project['feat_img'] ) ) $project['feat_img_checked'] = PROJECTPAGES_URL.'i/projectpages.png';
      }


      return $project;

    }

    return false;

}

// simplify return
function projectPages_tidyLog( $log_post_obj = false ){

    if ( $log_post_obj ){

      return array(

        'ID' => $log_post_obj->ID,
        'projectpageid' => $log_post_obj->meta['projectpageid'],
        'title' => $log_post_obj->meta['title'],
        'date' => $log_post_obj->meta['date'],
        'date_pretty' => date(  'F jS Y', strtotime( $log_post_obj->meta['date'] ) ),
        'timestamp' => strtotime( $log_post_obj->meta['date'] ), // set timestamp (as of yet no localisation)
        'icon' => $log_post_obj->meta['icon'],
        'body' => htmlspecialchars_decode($log_post_obj->meta['body'])

      );

    }

    return false;

}

// simplify return
function projectPages_tidyTags( $tag_post_obj = false ){

    if ( $tag_post_obj ){

      return array(

        'ID' => $tag_post_obj->term_id,
        'name' => $tag_post_obj->name,
        'slug' => $tag_post_obj->slug

      );

    }

    return false;

}


// simple hitcounter
// totally fallable and unreliable if caching (which you should be)
// but useful for welcome-wizard, and very low burdern on perf.
function projectPages_basic_hitcounter( $post_id = false ){

  if ( $post_id ) {

    $hits = (int)get_post_meta( $post_id, 'pp_hits', true );

    if ( !$hits ) $hits = 0;
    $hits++;

    update_post_meta( $post_id, 'pp_hits', $hits );

    return $hits;

  }

  return 0;

}

// fallable hitcounter total
function projectPages_getProjectPageHitsTotal(){

  global $wpdb;

  // SQL query to sum all meta values for 'pp_hits' where the value is greater than 0.
  $sql = "SELECT SUM(meta_value) as total_hits FROM $wpdb->postmeta WHERE meta_key = 'pp_hits' AND CAST(meta_value AS SIGNED) > 0";

  // Executing the SQL query.
  $total_hits = $wpdb->get_var($sql);

  // Check if we have a result and output it.
  if (!is_null($total_hits)) {
      return $total_hits;
  } 

  return 0;

}

// has user viewed settings?
function projectPages_hasViewedSettings(){

  return get_option( 'pp-has-viewed-settings' . get_current_user_id(), false );

}


// user has
function projectPages_setHasViewedSettings(){

  return update_option( 'pp-has-viewed-settings' . get_current_user_id(), time(), false );

}


// user joins community
function projectPages_joinCommunity( $email_address = '', $name = '', $solemnly_swear_to_make_cool_sht = false, $src = '' ){


    $data = array(
        'action'                   => 'pp_join',
        'return_url'               => home_url(),
        'email_address'            => $email_address,
        'name'                     => $name,
        'src'                      => $src,
        'solemnly'                 => $solemnly_swear_to_make_cool_sht
    );

    // call
    return wp_remote_post( ppurl( 'join' ), array(
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => $data,
        'cookies'     => array()
        )
    );

}

// compiles tidy project object from global $post
function projectPages_get_project_from_post( $squelch_404 = false, $use_cached_if_available = true ){


      // got post?
      global $post; if ( !$squelch_404 ) {

          if (!isset($post) || !isset($post->ID) || (isset($post->post_status) && $post->post_status != 'publish')) {
        
            // Send home - this'll probably never happen anyway.
            global $wp_query;   
              $wp_query->set_404();
              status_header( 404 );
              nocache_headers();
              exit();

          } 

      }

      // got cache?
      global $project_pages_cached_project;
      if ( $use_cached_if_available && isset( $project_pages_cached_project ) && is_array( $project_pages_cached_project ) ){

          return $project_pages_cached_project;

      }

      $project_complete = array();
      $project_complete['ID'] = $post->ID;
      $project_complete['title'] = $post->post_title; // $projectTitle
      $project_complete['title_checked'] = ( empty( $project_complete['title'] ) ) ? sprintf( __( 'Untitled Project #%d', 'projectpages' ), $post->ID ) : $project_complete['title']; // $projectTitleChecked
      $project_complete['url'] = get_post_permalink( $post->ID ); // $projectURL
      $project_complete['meta'] = projectPages_getProjectMeta( $post->ID ); // $projectMeta
      $project_complete['summary'] = nl2br( htmlspecialchars_decode( get_post_meta( $post->ID, 'whpp_project_summary' , true ) ) ); // $projectSummary
      $project_complete['summary_og'] = strip_tags( $project_complete['summary'] ); // $projectSummaryForOG
      $project_complete['summary'] = str_replace( '"', "'", $project_complete['summary'] );
      $project_complete['body'] = htmlspecialchars_decode( get_post_meta( $post->ID, 'whpp_project_body' , true ) ); // $projectBody
      $project_complete['body'] = apply_filters( 'the_content', $project_complete['body'] ); # http://wordpress.stackexchange.com/questions/21473/why-does-the-html-editor-not-wrap-my-code-in-paragraph-tags-when-i-press-enter
      $project_complete['tags'] = wp_get_post_terms( $post->ID, 'projectpagetag' ); // $projectTags
      $project_complete['post_date'] = date( 'F jS Y', strtotime( $post->post_modified ) ); // $projectDate
      $project_complete['header_type'] = get_post_meta( $post->ID, 'noheader', true ); // $projectHeaderType  
      $project_complete['logs'] = array(); if ( projectPages_getSetting('use_logs') == "1" ) $project_complete['logs'] = projectPages_getLogs( $post->ID, true, 100 ); // $projectLogs

      // feat img
      $project_complete['feat_img'] = ''; // $projectFeaturedImage
      $thumb_id = get_post_thumbnail_id();
      if ( isset( $thumb_id ) && !empty( $thumb_id ) ){
        $thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );
        if ( isset( $thumb_url_array ) && is_array( $thumb_url_array ) && isset( $thumb_url_array[0] ) ) $project_complete['feat_img'] = $thumb_url_array[0];
      }
      $project_complete['feat_img_checked'] = $project_complete['feat_img']; if ( empty( $project_complete['feat_img_checked'] ) ) $project_complete['feat_img_checked'] = PROJECTPAGES_URL.'i/projectpages.png'; // $projectFeaturedImageChecked
      $project_complete['feat_img_checked_empty'] = ''; if ( !empty( $project_complete['feat_img'] ) ) $project_complete['feat_img_checked_empty'] = $project_complete['feat_img'];

      // bg img      
      $project_complete['bg_img_url'] = ''; if ( isset( $project_complete['meta']['headerbg_imgurl'] ) && !empty( $project_complete['meta']['headerbg_imgurl'] ) ){
        $project_complete['bg_img_url'] = sanitize_url( $project_complete['meta']['headerbg_imgurl'] );
      } // $bg_img_url

      // bg gradient
      $project_complete['bg_gradient'] = ''; if ( isset( $project_complete['meta']['headerbg_gradient'] ) && !empty( $project_complete['meta']['headerbg_gradient'] ) ){
          $project_complete['bg_gradient'] = (int)$project_complete['meta']['headerbg_gradient'];
      } // $bg_gradient

      // bg vid
      $project_complete['bg_video_url'] = ''; if ( isset( $project_complete['meta']['headerbg_vidurl'] ) && !empty( $project_complete['meta']['headerbg_vidurl'] ) ){
        $project_complete['bg_video_url'] = sanitize_url( $project_complete['meta']['headerbg_vidurl'] );
      } // $bg_video_url


      // cache it
      $project_pages_cached_project = $project_complete;


      return $project_complete;

}

// dummy data (used in editor variants of blocks)
function projectPages_example_project(){

    $summary_html = '<p>Lorem ipsum, (' . __( 'example Project summary', 'projectpages' ) . '), dolor sit amet, consectetur adipiscing elit. Fusce in mi non quam posuere suscipit. Morbi viverra consequat imperdiet. Aliquam ullamcorper volutpat dui. Integer non erat vel dolor rutrum placerat. Sed tincidunt vitae massa sit amet imperdiet. Sed tempus felis sapien, vitae efficitur sapien fringilla ut. Cras vehicula dolor sit amet justo pulvinar finibus. Maecenas a eleifend elit, quis blandit lorem.</p>'
                  . '<p>Praesent eleifend vitae sapien ut sodales. Suspendisse potenti. Morbi elit quam, maximus nec neque nec, elementum ultrices velit.</p>';

    $body_html = '<p>Lorem ipsum, (' . __( 'example Project body content', 'projectpages' ) . '), dolor sit amet, consectetur adipiscing elit. Fusce in mi non quam posuere suscipit. Morbi viverra consequat imperdiet. Aliquam ullamcorper volutpat dui. Integer non erat vel dolor rutrum placerat. Sed tincidunt vitae massa sit amet imperdiet. Sed tempus felis sapien, vitae efficitur sapien fringilla ut. Cras vehicula dolor sit amet justo pulvinar finibus. Maecenas a eleifend elit, quis blandit lorem.</p>'
               . '<p>Praesent eleifend vitae sapien ut sodales. Suspendisse potenti. Morbi elit quam, maximus nec neque nec, elementum ultrices velit.</p>'
               // wp editor says no, lol . '<div class="project-pages-embed-container"><iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe></div>'
               . '<p><img src="' . PROJECTPAGES_URL.'i/projectpages.png' .'" /></p>'
               . '<p>Praesent eleifend vitae sapien ut sodales. Suspendisse potenti. Morbi elit quam, maximus nec neque nec, elementum ultrices velit.</p>'
               . '<p>Praesent eleifend vitae sapien ut sodales. Suspendisse potenti. Morbi elit quam, maximus nec neque nec, elementum ultrices velit.</p>';

    // process html so it's in same entity format as saved
    //$summary_html = htmlspecialchars( $summary_html );
    //$body_html = htmlspecialchars( $body_html );


    return array(
      'ID' => -1,
      'title' => __( 'Example Project', 'projectpages' ),
      'title_checked' => __( 'Example Project', 'projectpages' ),
      'date' => date( 'F jS Y', time() ),
      'date_full' => date( 'Y-m-d H:i:s', time() ),
      'timestamp' => time(),
      'status' => 'idea',
      'status_label' => 'Idea',
      'status_colour' => 'teal',
      'body' => $body_html,
      'summary' => $summary_html,
      'summary_excerpt' => $summary_html,
      'permalink' => get_home_url(),
      'meta' => array(
        'biline' => __( 'This is an example bi-line for a project, it gives a synopsis of what the project is all about.', 'projectpages' ),
        'status' => 'idea',
        'demourl' => '#example',
        'demolinktext' => __( 'Example Button', 'projectpages' ),
        'headerbg' => '#000000',
        'headerbg_imgurl' => '',
        'headerbg_vidurl' => '',
        'headerbg_gradient' => 1
      ),
      'tags' => [],
      'header_type' => 4, 
      'logs' => array(
        array(
          'ID' => -1,
          'projectpageid' => -1,
          'title' => __( 'Example Log', 'projectpages' ) . ' #1',
          'date' => date( 'F jS Y', time()-86400 ),
          'date_pretty' => date( 'F jS Y', time()-86400 ),
          'timestamp' => time()-86400,
          'icon' => 'dashicons-format-status',
          'body' => '<p>' . __( 'This is an example project log, it lets you talk about changes or events that occur during your project.', 'projectpages' ) . '</p>'
        ),
        array(
          'ID' => -1,
          'projectpageid' => -1,
          'title' => __( 'Example Log', 'projectpages' ) . ' #2',
          'date' => date( 'F jS Y', time()-(86400*2) ),
          'date_pretty' => date( 'F jS Y', time()-(86400*2) ),
          'timestamp' => time()-86400,
          'icon' => 'dashicons-admin-customizer',
          'body' => '<p>' . __( 'This is an example project log, it lets you talk about changes or events that occur during your project.', 'projectpages' ) . '</p>'
        )
      ),
      'feat_img' => '',
      'feat_img_checked' => PROJECTPAGES_URL.'i/creator-example.png'
    );

}


// inc
define('PROJECTPAGES_INC_DAL',true);