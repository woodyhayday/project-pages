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


/* ======================================================
  re-arrange metaboxes on editor page
    - Moves Featured Image above Tags
     (but respects a users mods to the view if they've overrided)
   ====================================================== */

add_filter( 'get_user_option_meta-box-order_projectpage', 'projectPages_metabox_order' );
function projectPages_metabox_order( $order ) {

    if ( empty ( $order ) ){

        // this may help a few folk on mobiles (better order):
        if ( wp_is_mobile() ){

            return array(
                'side' => 'whpp_meta,postimagediv,whpp_summary,whpp_body,projectpagetagdiv,whpp_logbody,_submitdiv,pp_feedback',
                'normal' => '',
                'advanced' => 'postcustom,slugdiv'
            );

        }

        // better full screen layout
        return array(
            'side' => '_submitdiv,postimagediv,projectpagetagdiv,pp_feedback',
            'normal' => 'whpp_meta,whpp_summary,whpp_body,whpp_logbody,postcustom,slugdiv',
            'advanced' => ''
        );

    }

    return $order;

}

/* ======================================================
  / re-arrange metaboxes on editor page
   ====================================================== */
/* ======================================================
  modified submit box (publish metabox removed + re-rigged)
  Inspired by https://wordpress.stackexchange.com/questions/354357/lock-draft-option-after-to-publish-my-custom-post
   ====================================================== */

// remove default
function projectPages_remove_submit_metabox(){    
    remove_meta_box( 'submitdiv', 'projectpage', 'side' ); 
}
add_action( 'add_meta_boxes', "projectPages_remove_submit_metabox", 10 );

// add own
function projectPages_add_submit_metabox(){ 
    add_meta_box( 
        "_submitdiv", 
        __( "Publish" ), 
        "projectPages_submit_metabox", 
        'projectpage', 
        'side', 
        'core', 
        [ 'show_draft_button' => false ] 
    );
}
add_action( 'add_meta_boxes', 'projectPages_add_submit_metabox' );

// modified `post_submit_meta_box()`
// https://developer.wordpress.org/reference/functions/post_submit_meta_box/
function projectPages_submit_metabox( $post, $args = array() ) {
    global $action;

    $post_id          = (int) $post->ID;
    $post_type        = $post->post_type;
    $post_type_object = get_post_type_object( $post_type );
    $can_publish      = current_user_can( $post_type_object->cap->publish_posts );
    ?>

<?php 
    // output post_id to js if there is one (for ajax log saving)
    if ( $post_id ){

        ?><script>var projectPages_post_id = <?php echo $post_id; ?>;</script><?php

    }
?>
<div class="submitbox" id="submitpost">

<div id="minor-publishing">

    <?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key. ?>
    <div style="display:none;">
        <?php submit_button( __( 'Save ME' ), '', 'save' ); ?>
    </div>

    <div id="minor-publishing-actions">
        <div id="save-action">
            <?php
            if ( ! in_array( $post->post_status, array( 'publish', 'future', 'pending' ), true ) ) {
                $private_style = '';
                if ( 'private' === $post->post_status ) {
                    $private_style = 'style="display:none"';
                }
                ?>
                <input <?php echo $private_style; ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e( 'Save Draft' ); ?>" class="button" />
                <span class="spinner"></span>
            <?php } elseif ( 'pending' === $post->post_status && $can_publish ) { ?>
                <input type="submit" name="save" id="save-post" value="<?php esc_attr_e( 'Save as Pending' ); ?>" class="button" />
                <span class="spinner"></span>
            <?php } ?>
        </div>

        <?php
        if ( is_post_type_viewable( $post_type_object ) ) :
            ?>
            <div id="preview-action">
                <?php
                $preview_link = esc_url( get_preview_post_link( $post ) );
                if ( 'publish' === $post->post_status ) {
                    $preview_button_text = __( 'Preview Changes' );
                } else {
                    $preview_button_text = __( 'Preview' );
                }

                $preview_button = sprintf(
                    '%1$s<span class="screen-reader-text"> %2$s</span>',
                    $preview_button_text,
                    /* translators: Hidden accessibility text. */
                    __( '(opens in a new tab)' )
                );
                ?>
                <a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview-<?php echo $post_id; ?>" id="post-preview"><?php echo $preview_button; ?></a>
                <input type="hidden" name="wp-preview" id="wp-preview" value="" />
            </div>
            <?php
        endif;

        /**
         * Fires after the Save Draft (or Save as Pending) and Preview (or Preview Changes) buttons
         * in the Publish meta box.
         *
         * @since 4.4.0
         *
         * @param WP_Post $post WP_Post object for the current post.
         */
        do_action( 'post_submitbox_minor_actions', $post );
        ?>
        <div class="clear"></div>
    </div>

    <div id="misc-publishing-actions">
        <div class="misc-pub-section misc-pub-post-status">
            <?php _e( 'Status:' ); ?>
            <span id="post-status-display">
                <?php
                switch ( $post->post_status ) {
                    case 'private':
                        _e( 'Privately Published' );
                        break;
                    case 'publish':
                        _e( 'Published' );
                        break;
                    case 'future':
                        _e( 'Scheduled' );
                        break;
                    case 'pending':
                        _e( 'Pending Review' );
                        break;
                    case 'draft':
                    case 'auto-draft':
                        _e( 'Draft' );
                        break;
                }
                ?>
            </span>

            <?php
            if ( 'publish' === $post->post_status || 'private' === $post->post_status || $can_publish ) {
                $private_style = '';
                if ( 'private' === $post->post_status ) {
                    $private_style = 'style="display:none"';
                }
                ?>
                <a href="#post_status" <?php echo $private_style; ?> class="edit-post-status hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text">
                    <?php
                    /* translators: Hidden accessibility text. */
                    _e( 'Edit status' );
                    ?>
                </span></a>

                <div id="post-status-select" class="hide-if-js">
                    <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ( 'auto-draft' === $post->post_status ) ? 'draft' : $post->post_status ); ?>" />
                    <label for="post_status" class="screen-reader-text">
                        <?php
                        /* translators: Hidden accessibility text. */
                        _e( 'Set status' );
                        ?>
                    </label>
                    <select name="post_status" id="post_status">
                        <?php if ( 'publish' === $post->post_status ) : ?>
                            <option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e( 'Published' ); ?></option>
                        <?php elseif ( 'private' === $post->post_status ) : ?>
                            <option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e( 'Privately Published' ); ?></option>
                        <?php elseif ( 'future' === $post->post_status ) : ?>
                            <option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e( 'Scheduled' ); ?></option>
                        <?php endif; ?>
                            <option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e( 'Pending Review' ); ?></option>
                        <?php if ( 'auto-draft' === $post->post_status ) : ?>
                            <option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php _e( 'Draft' ); ?></option>
                        <?php else : ?>
                            <option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e( 'Draft' ); ?></option>
                        <?php endif; ?>
                    </select>
                    <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e( 'OK' ); ?></a>
                    <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="misc-pub-section misc-pub-visibility" id="visibility">
            <?php _e( 'Visibility:' ); ?>
            <span id="post-visibility-display">
                <?php
                if ( 'private' === $post->post_status ) {
                    $post->post_password = '';
                    $visibility          = 'private';
                    $visibility_trans    = __( 'Private' );
                } elseif ( ! empty( $post->post_password ) ) {
                    $visibility       = 'password';
                    $visibility_trans = __( 'Password protected' );
                } elseif ( 'post' === $post_type && is_sticky( $post_id ) ) {
                    $visibility       = 'public';
                    $visibility_trans = __( 'Public, Sticky' );
                } else {
                    $visibility       = 'public';
                    $visibility_trans = __( 'Public' );
                }

                echo esc_html( $visibility_trans );
                ?>
            </span>

            <?php if ( $can_publish ) { ?>
                <a href="#visibility" class="edit-visibility hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text">
                    <?php
                    /* translators: Hidden accessibility text. */
                    _e( 'Edit visibility' );
                    ?>
                </span></a>

                <div id="post-visibility-select" class="hide-if-js">
                    <input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr( $post->post_password ); ?>" />
                    <?php if ( 'post' === $post_type ) : ?>
                        <input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky" <?php checked( is_sticky( $post_id ) ); ?> />
                    <?php endif; ?>

                    <input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />
                    <input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e( 'Public' ); ?></label><br />

                    <?php if ( 'post' === $post_type && current_user_can( 'edit_others_posts' ) ) : ?>
                        <span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post_id ) ); ?> /> <label for="sticky" class="selectit"><?php _e( 'Stick this post to the front page' ); ?></label><br /></span>
                    <?php endif; ?>

                    <input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e( 'Password protected' ); ?></label><br />
                    <span id="password-span"><label for="post_password"><?php _e( 'Password:' ); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr( $post->post_password ); ?>"  maxlength="255" /><br /></span>

                    <input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e( 'Private' ); ?></label><br />

                    <p>
                        <a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK' ); ?></a>
                        <a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
                    </p>
                </div>
            <?php } ?>
        </div>

        <?php
        /* translators: Publish box date string. 1: Date, 2: Time. See https://www.php.net/manual/datetime.format.php */
        $date_string = __( '%1$s at %2$s' );
        /* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
        $date_format = _x( 'M j, Y', 'publish box date format' );
        /* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
        $time_format = _x( 'H:i', 'publish box time format' );

        if ( 0 !== $post_id ) {
            if ( 'future' === $post->post_status ) { // Scheduled for publishing at a future date.
                /* translators: Post date information. %s: Date on which the post is currently scheduled to be published. */
                $stamp = __( 'Scheduled for: %s' );
            } elseif ( 'publish' === $post->post_status || 'private' === $post->post_status ) { // Already published.
                /* translators: Post date information. %s: Date on which the post was published. */
                $stamp = __( 'Live: %s' );
            } elseif ( '0000-00-00 00:00:00' === $post->post_date_gmt ) { // Draft, 1 or more saves, no date specified.
                $stamp = __( 'Publish <b>immediately</b>' );
            } elseif ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // Draft, 1 or more saves, future date specified.
                /* translators: Post date information. %s: Date on which the post is to be published. */
                $stamp = __( 'Schedule for: %s' );
            } else { // Draft, 1 or more saves, date specified.
                /* translators: Post date information. %s: Date on which the post is to be published. */
                $stamp = __( 'Publish on: %s' );
            }
            $date = sprintf(
                $date_string,
                date_i18n( $date_format, strtotime( $post->post_date ) ),
                date_i18n( $time_format, strtotime( $post->post_date ) )
            );
        } else { // Draft (no saves, and thus no date specified).
            $stamp = __( 'Publish <b>immediately</b>' );
            $date  = sprintf(
                $date_string,
                date_i18n( $date_format, strtotime( current_time( 'mysql' ) ) ),
                date_i18n( $time_format, strtotime( current_time( 'mysql' ) ) )
            );
        }

        if ( ! empty( $args['args']['revisions_count'] ) ) :
            ?>
            <div class="misc-pub-section misc-pub-revisions">
                <?php
                /* translators: Post revisions heading. %s: The number of available revisions. */
                printf( __( 'Revisions: %s' ), '<b>' . number_format_i18n( $args['args']['revisions_count'] ) . '</b>' );
                ?>
                <a class="hide-if-no-js" href="<?php echo esc_url( get_edit_post_link( $args['args']['revision_id'] ) ); ?>"><span aria-hidden="true"><?php _ex( 'Browse', 'revisions' ); ?></span> <span class="screen-reader-text">
                    <?php
                    /* translators: Hidden accessibility text. */
                    _e( 'Browse revisions' );
                    ?>
                </span></a>
            </div>
            <?php
        endif;

        if ( $can_publish ) : // Contributors don't get to choose the date of publish.
            ?>
            <div class="misc-pub-section curtime misc-pub-curtime">
                <span id="timestamp">
                    <?php printf( $stamp, '<b>' . $date . '</b>' ); ?>
                </span>
                <a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button">
                    <span aria-hidden="true"><?php _e( 'Edit' ); ?></span>
                    <span class="screen-reader-text">
                        <?php
                        /* translators: Hidden accessibility text. */
                        _e( 'Edit date and time' );
                        ?>
                    </span>
                </a>
                <fieldset id="timestampdiv" class="hide-if-js">
                    <legend class="screen-reader-text">
                        <?php
                        /* translators: Hidden accessibility text. */
                        _e( 'Date and time' );
                        ?>
                    </legend>
                    <?php touch_time( ( 'edit' === $action ), 1 ); ?>
                </fieldset>
            </div>
            <?php
        endif;


        // Project Pages additions: View link
        if ( 'publish' === $post->post_status ){
            ?>
            <div class="misc-pub-section misc-pub-view pp-submit-section">
                <span class="dashicons dashicons-admin-links"></span>
                <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" target="wp-view-<?php echo $post_id; ?>" id="post-view"><?php _e( 'View Project Page', 'projectpages' ); ?></a>
            </div>
            <?php
        }

        // Project Pages additions: Share
        if ( 'publish' === $post->post_status ){
            ?>
            <div class="misc-pub-section misc-pub-view pp-share-section">
                <span class="dashicons dashicons-share"></span> Share:
                <a href="<?php

                    echo wh_share_via_x( __( 'Check out my project: ', 'projectpages' ) . $post->post_title . '<br>' . esc_url( get_permalink( $post ) ) . '<br><br>@projectpagesio' );

                ?>" target="_blank"><img src="<?php echo PROJECTPAGES_URL . 'i/social-x.png'; ?>" alt="<? _e( 'Share on X', 'projectpages' ); ?>" /></a>
                <a href="<?php echo wh_share_via_fb( esc_url( get_permalink( $post ) ) ); ?>" target="_blank"><img src="<?php echo PROJECTPAGES_URL . 'i/social-fb.png'; ?>" alt="<? _e( 'Share on Facebook', 'projectpages' ); ?>" /></a>
                <a href="<?php echo wh_share_via_li( esc_url( get_permalink( $post ) ) ); ?>" target="_blank"><img src="<?php echo PROJECTPAGES_URL . 'i/social-li.png'; ?>" alt="<? _e( 'Share on LinkedIn', 'projectpages' ); ?>" /></a>
                <a href="<?php echo wh_share_via_telegram( esc_url( get_permalink( $post ) ), __( 'Check out my project: ', 'projectpages' ) . $post->post_title ); ?>" target="_blank"><img src="<?php echo PROJECTPAGES_URL . 'i/social-telegram.png'; ?>" alt="<? _e( 'Share on Telegram', 'projectpages' ); ?>" /></a>
            </div>
            <?php
        }

        // Project Pages additions: Publish on ProjectPages.io
        if ( 'publish' === $post->post_status ){
            ?>
            <div class="misc-pub-section misc-pub-view pp-promote-section">
                <span class="dashicons dashicons-admin-site-alt3"></span>
                <a href="<?php echo esc_url( ppurl('publish-on-pp-io') ); ?>" target="_blank"><?php _e( 'Publish on ProjectPages.io', 'projectpages' ); ?></a>
            </div>
            <?php
        }

        

        if ( 'draft' === $post->post_status && get_post_meta( $post_id, '_customize_changeset_uuid', true ) ) :
            $message = sprintf(
                /* translators: %s: URL to the Customizer. */
                __( 'This draft comes from your <a href="%s">unpublished customization changes</a>. You can edit, but there is no need to publish now. It will be published automatically with those changes.' ),
                esc_url(
                    add_query_arg(
                        'changeset_uuid',
                        rawurlencode( get_post_meta( $post_id, '_customize_changeset_uuid', true ) ),
                        admin_url( 'customize.php' )
                    )
                )
            );
            wp_admin_notice(
                $message,
                array(
                    'type'               => 'info',
                    'additional_classes' => array( 'notice-alt', 'inline' ),
                )
            );
        endif;

        /**
         * Fires after the post time/date setting in the Publish meta box.
         *
         * @since 2.9.0
         * @since 4.4.0 Added the `$post` parameter.
         *
         * @param WP_Post $post WP_Post object for the current post.
         */
        do_action( 'post_submitbox_misc_actions', $post );
        ?>
    </div>
    <div class="clear"></div>
</div>

<div id="major-publishing-actions">
    <?php
    /**
     * Fires at the beginning of the publishing actions section of the Publish meta box.
     *
     * @since 2.7.0
     * @since 4.9.0 Added the `$post` parameter.
     *
     * @param WP_Post|null $post WP_Post object for the current post on Edit Post screen,
     *                           null on Edit Link screen.
     */
    do_action( 'post_submitbox_start', $post );
    ?>
    <div id="delete-action">
        <?php
        if ( current_user_can( 'delete_post', $post_id ) ) {
            if ( ! EMPTY_TRASH_DAYS ) {
                $delete_text = __( 'Delete permanently' );
            } else {
                $delete_text = __( 'Move to Trash' );
            }
            ?>
            <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post_id ); ?>"><?php echo $delete_text; ?></a>
            <?php
        }
        ?>
    </div>

    <div id="publishing-action">
        <span class="spinner"></span>
        <?php
        if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ), true ) || 0 === $post_id ) {
            if ( $can_publish ) :
                if ( ! empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
                    ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php echo esc_attr_x( 'Schedule', 'post action/button label' ); ?>" />
                    <?php submit_button( _x( 'Schedule', 'post action/button label' ), 'primary large', 'publish', false ); ?>
                    <?php
                else :
                    ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish' ); ?>" />
                    <?php submit_button( __( 'Publish' ), 'primary large', 'publish', false ); ?>
                    <?php
                endif;
            else :
                ?>
                <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review' ); ?>" />
                <?php submit_button( __( 'Submit for Review' ), 'primary large', 'publish', false ); ?>
                <?php
            endif;
        } else {
            ?>
            <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); ?>" />
            <?php submit_button( __( 'Update' ), 'primary large', 'save', false, array( 'id' => 'publish' ) ); ?>
            <?php
        }
        ?>
    </div>
    <div class="clear"></div>
</div>

</div>
    <?php
}


/* ======================================================
  / modified submit box
   ====================================================== */

/* ======================================================
  Extra Details Metabox
   ====================================================== */

    class projectPages__MetaboxMetaDeets {

        static $instance;
        private $postType;

        public function __construct( $plugin_file ) {
            
            // lol.
            self::$instance = $this;
            $this->postType = 'projectpage';

            add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );
            add_filter( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );
        }

        public function create_meta_box() {

            add_meta_box(
                'whpp_meta',
                __('Project Details','projectpages'),
                array( $this, 'print_meta_box' ),
                $this->postType,
                'normal',
                'high'
            );
        }

        public function print_meta_box( $post, $metabox ) {

            global $projectPageStatuses;

            $projectPageMeta = projectPages_getProjectMeta($post->ID); 

            $headerimg = get_post_meta($post->ID, 'noheader', true);

            ?>

            <input type="hidden" name="meta_box_ids[]" value="<?php echo $metabox['id']; ?>" />
            <?php wp_nonce_field( 'save_' . $metabox['id'], $metabox['id'] . '_nonce' ); ?>
            <table class="form-table whppMetaBox" id="whppMetaBoxDeets">

                <!-- lol @ hardtyped width, works tho -->
                <tr class="wh-large"><th style="width:240px !important;"><label for="whpp_biline"><?php _e('Bi-line','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_biline" id="whpp_biline" class="form-control widetext" placeholder="e.g. An exploration into the dark side of the moon" value="<?php if (isset($projectPageMeta['biline'])) echo $projectPageMeta['biline']; ?>" />
                    </td>
                </tr>

                <tr class="wh-large"><th style="width:240px !important;"><label for="whpp_status"><?php _e('Status','projectpages'); ?>:</label></th>
                    <td>
                        <select name="whpp_status" id="" class="whpp_status">
                            <?php foreach ($projectPageStatuses as $statusKey => $status){
                                echo '<option value="'.$statusKey.'"';
                                if (isset($projectPageMeta['status']) && $projectPageMeta['status'] == $statusKey) echo ' selected="selected"';
                                echo '><div class="statusCircle '.$status[1].'"></div>'.$status[0].'</option>';
                            } ?>
                        </select>
                    </td>
                </tr>

                <tr class="wh-large"><th style="width:240px !important;"><label for="whpp_demolinktext"><?php _e('External Link Text','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_demolinktext" id="whpp_demolinktext" class="form-control widetext" placeholder="e.g. An exploration into the dark side of the moon" value="<?php if (isset($projectPageMeta['demolinktext'])) echo $projectPageMeta['demolinktext']; ?>" />
                    </td>
                </tr>

                <tr class="wh-large"><th style="width:240px !important;"><label for="whpp_demourl"><?php _e('External Link URL','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_demourl" id="whpp_demourl" class="form-control widetext" placeholder="e.g. http://yourdemo.com" value="<?php if (isset($projectPageMeta['demourl'])) echo $projectPageMeta['demourl']; ?>" />
                    </td>
                </tr>

                <tr class="wh-large"><th style="width:240px !important;"><label for="whpp_headerimg"><?php _e('Header Type','projectpages'); ?>:</label></th>
                    <td>
                        <select name="whpp_headerimg" id="whpp_headerimg" class="">
                            <option value="-1">Featured Image</option>
                            <option value="1" <?php if ($headerimg == "1") echo ' selected="selected";'; ?>>Colour</option>
                            <option value="2" <?php if ($headerimg == "2") echo ' selected="selected";'; ?>>Image from URL</option>
                            <option value="3" <?php if ($headerimg == "3") echo ' selected="selected";'; ?>>Video from URL</option>
                            <option value="4" <?php if ($headerimg == "4") echo ' selected="selected";'; ?>>Gradient</option>
                        </select>
                    </td>
                </tr>
                <tr class="wh-large wh-headerimg-cascade<?php if ($headerimg == "1") echo ' wh-cascade-show";'; ?>" id="headerbg"><th style="width:240px !important;"><label for="whpp_headerbg"><?php _e('Header Background Colour','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_headerbg" id="whpp_headerbg" class="" placeholder="e.g. #000000" value="<?php if (isset($projectPageMeta['headerbg'])) echo $projectPageMeta['headerbg']; else echo '#000000'; ?>" />
                    </td>
                </tr>

                <tr class="wh-large wh-headerimg-cascade<?php if ($headerimg == "2") echo ' wh-cascade-show";'; ?>" id="headerbg_imgurl"><th style="width:240px !important;"><label for="whpp_headerbg_imgurl"><?php _e('Header Image URL','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_headerbg_imgurl" id="whpp_headerbg_imgurl" class="form-control widetext" placeholder="e.g. https://yoursite.com/bg.jpg" value="<?php if (isset($projectPageMeta['headerbg_imgurl'])) echo $projectPageMeta['headerbg_imgurl']; ?>" />
                    </td>
                </tr>

                <tr class="wh-large wh-headerimg-cascade<?php if ($headerimg == "3") echo ' wh-cascade-show";'; ?>" id="headerbg_vidurl"><th style="width:240px !important;"><label for="whpp_headerbg_vidurl"><?php _e('Header Video URL','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_headerbg_vidurl" id="whpp_headerbg_vidurl" class="form-control widetext" placeholder="e.g. https://yoursite.com/vid.mp4" value="<?php if (isset($projectPageMeta['headerbg_vidurl'])) echo $projectPageMeta['headerbg_vidurl']; ?>" />
                    </td>
                </tr>

                <tr class="wh-large wh-headerimg-cascade<?php if ($headerimg == "4") echo ' wh-cascade-show";'; ?>" id="headerbg_gradient"><th style="width:240px !important;"><label for="whpp_headerbg_gradient"><?php _e('Header Gradient','projectpages'); ?>:</label></th>
                    <td>

                        <?php 

                        $gradient = 1;
                        if ( isset( $projectPageMeta['headerbg_gradient'] ) ){

                            $gradient = (int)$projectPageMeta['headerbg_gradient'];

                        }
                        ?>
                        <input type="hidden" name="whpp_headerbg_gradient" id="whpp_headerbg_gradient" value="<?php if (isset($projectPageMeta['headerbg_gradient'])) echo $projectPageMeta['headerbg_gradient']; ?>" />
                        
                        <div class="container">
                          <div class="row align-items-start">
                            <div class="col" style="max-width: 160px;padding-left: 0;">
                              
                                <div class="dropdown" id="pp_gradient_dropdown">
                                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select Gradient
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php for ( $i = 1; $i <= 10; $i++ ){ ?>
                                    <a class="dropdown-item pp-dropdown-item pp-gradient-<?php echo $i; ?>" href="#" data-pp-grad="<?php echo $i; ?>" onclick="projectPages_select_gradient(<?php echo $i; ?>);return false;"><?php echo sprintf(__("Gradient %s", 'projectpages'), $i) ?></a>
                                    <?php } ?>
                                  </div>
                                </div>


                            </div>
                            <div class="col">
                  
                                <div id="pp-gradient-example-wrap" class="d-flex align-items-center pp-gradient-<?php echo $gradient; ?>">
                                    <span id="pp-gradient-example"><?php echo sprintf(__("Gradient <span>%s</span>", 'projectpages'), $gradient) ?></span>
                                </div>

                            </div>
                          </div>
                        </div>                    

                    </td>
                </tr>

                <tr class="wh-large" id="headertextcolour"><th style="width:240px !important;"><label for="whpp_headertextcolour"><?php _e('Header H1 Text Colour','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_headertextcolour" id="whpp_headertextcolour" class="" placeholder="e.g. #000000" value="<?php if (isset($projectPageMeta['headertextcolour'])) echo $projectPageMeta['headertextcolour']; else echo '#FFFFFF'; ?>" />
                    </td>
                </tr>

                <tr class="wh-large" id="headeralttextcolour"><th style="width:240px !important;"><label for="whpp_headeralttextcolour"><?php _e('Bi-line Text Colour','projectpages'); ?>:</label></th>
                    <td>
                        <input type="text" name="whpp_headeralttextcolour" id="whpp_headeralttextcolour" class="" placeholder="e.g. #000000" value="<?php if (isset($projectPageMeta['headeralttextcolour'])) echo $projectPageMeta['headeralttextcolour']; else echo '#CCCCCC'; ?>" />
                    </td>
                </tr>
                
            </table>

            <style type="text/css">
                <?php 
                
                // brutal hide if not using logs (for v1.2)
                $usingLogging = projectPages_getSetting('use_logs');
                if ($usingLogging != "1") echo '#whpp_logbody { display:none!important;}';


                ?>

            </style>
            <script type="text/javascript">

                jQuery(document).ready(function(){
                    
                    jQuery("#whpp_headerbg").spectrum({
                        //color: "#000000",
                        preferredFormat: "hex"
                    });
                    jQuery("#whpp_headertextcolour").spectrum({
                        //color: "#000000",
                        preferredFormat: "hex"
                    });
                    jQuery("#whpp_headeralttextcolour").spectrum({
                        //color: "#000000",
                        preferredFormat: "hex"
                    });

                });


            </script><?php
        }

        public function save_meta_box( $post_id, $post ) {

            if( empty( $_POST['meta_box_ids'] ) ){ return; }
            foreach( $_POST['meta_box_ids'] as $metabox_id ){

                if( !isset($_POST[ $metabox_id . '_nonce' ]) || ! wp_verify_nonce( $_POST[ $metabox_id . '_nonce' ], 'save_' . $metabox_id ) ){ continue; }                
                if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ continue; }

                if( $metabox_id == 'whpp_meta'  && $post->post_type == $this->postType){

                    $projectPageMeta = array('biline' => '','status' => '');

                    if (isset($_POST['whpp_biline']) && !empty($_POST['whpp_biline'])) $projectPageMeta['biline'] = projectPages_textProcess($_POST['whpp_biline']);
                    if (isset($_POST['whpp_status']) && !empty($_POST['whpp_status'])) $projectPageMeta['status'] = sanitize_text_field($_POST['whpp_status']); # just trust that it's in the array here, lazy but will be fine
                    
                    // v1.1
                    if (isset($_POST['whpp_demourl']) && !empty($_POST['whpp_demourl'])) $projectPageMeta['demourl'] = projectPages_textProcess($_POST['whpp_demourl']);
                    if (isset($_POST['whpp_demolinktext']) && !empty($_POST['whpp_demolinktext'])) $projectPageMeta['demolinktext'] = projectPages_textProcess($_POST['whpp_demolinktext']);

                    // v1.2
                    if (isset($_POST['whpp_headerbg']) && !empty($_POST['whpp_headerbg'])) $projectPageMeta['headerbg'] = projectPages_textProcess($_POST['whpp_headerbg']);
                
                    // 2.0 save extras
                    $projectPageMeta['headerbg_imgurl'] = ''; if (isset($_POST['whpp_headerbg_imgurl']) && !empty($_POST['whpp_headerbg_imgurl'])) $projectPageMeta['headerbg_imgurl'] = projectPages_textProcess($_POST['whpp_headerbg_imgurl']);
                    $projectPageMeta['headerbg_vidurl'] = ''; if (isset($_POST['whpp_headerbg_vidurl']) && !empty($_POST['whpp_headerbg_vidurl'])) $projectPageMeta['headerbg_vidurl'] = projectPages_textProcess($_POST['whpp_headerbg_vidurl']);
                    $projectPageMeta['headerbg_gradient'] = 1; if (isset($_POST['whpp_headerbg_gradient']) && !empty($_POST['whpp_headerbg_gradient'])) $projectPageMeta['headerbg_gradient'] = (int)sanitize_text_field($_POST['whpp_headerbg_gradient']);                
                    if (isset($_POST['whpp_headertextcolour']) && !empty($_POST['whpp_headertextcolour'])) $projectPageMeta['headertextcolour'] = projectPages_textProcess($_POST['whpp_headertextcolour']);
                    if (isset($_POST['whpp_headeralttextcolour']) && !empty($_POST['whpp_headeralttextcolour'])) $projectPageMeta['headeralttextcolour'] = projectPages_textProcess($_POST['whpp_headeralttextcolour']);

                    // UPDATE!
                    projectPages_setProjectMeta($post_id, $projectPageMeta);

                    // v1.1 added status as separate field to simplify display, leaving prev stuff in place for quick dev
                    projectPages_setProjectStatus($post_id, $projectPageMeta['status']);

                    // V1.2 - whpp_headerimg
                    // v2.0 1 = color, -1 = featimg, 2 = imgurl, 3 = vidurl, 4 = gradient
                    $headerImg = -1; if (isset($_POST['whpp_headerimg']) && !empty($_POST['whpp_headerimg'])) $headerImg = (int)sanitize_text_field($_POST['whpp_headerimg']);
                    update_post_meta($post_id,'noheader',$headerImg);

                    // PRO hook 
                    do_action( 'project_pages_save_project', $post_id );

                }
            }

            return $post;
        }
    }

    $projectPages__MetaboxMetaDeets = new projectPages__MetaboxMetaDeets( __FILE__ );

/* ======================================================
  / Extra Details Metabox
   ====================================================== */




/* ======================================================
  Summary Metabox
   ====================================================== */

    class projectPages__MetaboxSummary {

        static $instance;
        private $postType;

        public function __construct( $plugin_file ) {
            
            // lol.
            self::$instance = $this;
            $this->postType = 'projectpage';

            add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );
            add_filter( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );
        }

        public function create_meta_box() {

            add_meta_box(
                'whpp_summary',
                __('Project Summary','projectpages'),
                array( $this, 'print_meta_box' ),
                $this->postType,
                'normal',
                'high'
            );
        }

        public function print_meta_box( $post, $metabox ) {

            ?><input type="hidden" name="meta_box_ids[]" value="<?php echo $metabox['id']; ?>" />
            <?php wp_nonce_field( 'save_' . $metabox['id'], $metabox['id'] . '_nonce' ); ?>
            <?php wp_editor( htmlspecialchars_decode(get_post_meta($post->ID, 'whpp_project_summary' , true )), 'whpp_project_summary', array('editor_height',320));

        }

        public function save_meta_box( $post_id, $post ) {

            if( empty( $_POST['meta_box_ids'] ) ){ return; }
            foreach( $_POST['meta_box_ids'] as $metabox_id ){

                if( !isset($_POST[ $metabox_id . '_nonce' ]) || ! wp_verify_nonce( $_POST[ $metabox_id . '_nonce' ], 'save_' . $metabox_id ) ){ continue; }                
                if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ continue; }

                if( $metabox_id == 'whpp_summary'  && $post->post_type == $this->postType){

                      if (isset($_POST['whpp_project_summary'])) {

                        // Save content
                        update_post_meta($post_id, 'whpp_project_summary', htmlspecialchars($_POST['whpp_project_summary']));


                      }                

                }
            }

            return $post;
        }
    }

    $projectPages__MetaboxSummary = new projectPages__MetaboxSummary( __FILE__ );

/* ======================================================
  / Summary Metabox
   ====================================================== */





/* ======================================================
  Body Metabox
   ====================================================== */

    class projectPages__MetaboxBody {

        static $instance;
        private $postType;

        public function __construct( $plugin_file ) {
            
            // lol.
            self::$instance = $this;
            $this->postType = 'projectpage';

            add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );
            add_filter( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );
        }

        public function create_meta_box() {

            add_meta_box(
                'whpp_body',
                __('Project Body','projectpages'),
                array( $this, 'print_meta_box' ),
                $this->postType,
                'normal',
                'high'
            );
        }

        public function print_meta_box( $post, $metabox ) {

            ?><input type="hidden" name="meta_box_ids[]" value="<?php echo $metabox['id']; ?>" />
            <?php wp_nonce_field( 'save_' . $metabox['id'], $metabox['id'] . '_nonce' ); ?>
            <?php // PRO hook
            do_action( 'project_pages_editor_body' ); ?>
            <?php wp_editor( htmlspecialchars_decode(get_post_meta($post->ID, 'whpp_project_body' , true )), 'whpp_project_body', array('editor_height',680));

        }

        public function save_meta_box( $post_id, $post ) {

            if( empty( $_POST['meta_box_ids'] ) ){ return; }
            foreach( $_POST['meta_box_ids'] as $metabox_id ){

                if( !isset($_POST[ $metabox_id . '_nonce' ]) || ! wp_verify_nonce( $_POST[ $metabox_id . '_nonce' ], 'save_' . $metabox_id ) ){ continue; }                
                if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ continue; }

                if( $metabox_id == 'whpp_body'  && $post->post_type == $this->postType){

                      if (isset($_POST['whpp_project_body'])) {

                        // Save content
                        update_post_meta($post_id, 'whpp_project_body', htmlspecialchars($_POST['whpp_project_body']));


                      }                

                }
            }

            return $post;
        }
    }

    $projectPages__MetaboxBody = new projectPages__MetaboxBody( __FILE__ );

/* ======================================================
  / Body Metabox
   ====================================================== */


/* ======================================================
  Logs Metabox
   ====================================================== */

    class projectPages__MetaboxLogs {

        static $instance;
        private $postType;

        public function __construct( $plugin_file ) {
            
            // lol.
            self::$instance = $this;
            $this->postType = 'projectpage';

            add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );
            add_filter( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );
        }

        public function create_meta_box() {

            add_meta_box(
                'whpp_logbody',
                __('Project Logs','projectpages'),
                array( $this, 'print_meta_box' ),
                $this->postType,
                'normal',
                'high'
            );
        }

        public function print_meta_box( $post, $metabox ) {

            ?><input type="hidden" name="meta_box_ids[]" value="<?php echo $metabox['id']; ?>" />
            <?php wp_nonce_field( 'save_' . $metabox['id'], $metabox['id'] . '_nonce' ); ?>

            <?php 

            $logs = array();
            $can_add_logs = false; 
            if ( isset( $post->ID ) ){ 
                $can_add_logs = true;
                $logs = projectPages_getLogs( $post->ID, true, 1000, 0 );
            }

            // output logs & nonce for js drawing
            ?><script>
                var projectPages_logs = <?php echo json_encode( $logs ); ?>;
                var projectPages_log_nonce = '<?php echo esc_js( wp_create_nonce( 'project-pages-log-nonce' ) ); ?>';
            </script><?php

            if ( count( $logs ) <= 0 ){

                // no logs yet
                ?><div class="container my-2" id="project-pages-no-logs">
                  <div class="p-5 text-center bg-body-tertiary rounded-3">
                    <h1 class="text-body-emphasis"><?php _e( 'No logs yet', 'projectpages' ); ?></h1>
                    <p class="col-lg-6 mx-auto mb-4">
                      <?php _e( 'Logs allow you to track the various stages or developments in your project.', 'projectpages' ); ?>
                    </p>
                    <?php if ( $can_add_logs ){ ?>
                    <p class="lead">
                        <button class="btn btn-primary px-5 mb-5" type="button" class="pp-add-log" data-bs-toggle="modal" data-bs-target="#project-pages-add-log-modal"><span class="dashicons dashicons-welcome-write-blog"></span> <?php _e( 'Add a log', 'projectpages' ); ?></button>
                    </p>
                    <?php } else { ?>
                    <p class="lead">
                        <?php _e( 'First save your project, then you can add logs.', 'projectpages' ); ?>
                    </p>
                    <?php } ?>
                  </div>
                </div><?php

            }

            ?>

            <div id="project-pages-log-outer-wrap">
                <div class="pp-add-log-top-button text-center">
                    <button class="btn btn-primary px-5 btn-sm pp-add-log" type="button" data-bs-toggle="modal" data-bs-target="#project-pages-add-log-modal"><span class="dashicons dashicons-welcome-write-blog"></span> <?php _e( 'Add a log', 'projectpages' ); ?></button>                    
                </div>
                <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-center justify-content-center">
                    <div class="list-group" id="project-pages-log-wrap"></div>
                </div>
            </div>


            <div class="modal fade modal-lg" id="project-pages-add-log-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="project-pages-add-log-modal-label" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="project-pages-add-log-modal-label"><?php _e( 'Add a log entry', 'projectpages' ); ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <div class="mb-3">
                        <label for="project-pages-add-log-title" class="form-label"><?php _e( 'Log Title:', 'projectpages' ); ?></label>
                        <input type="text" class="form-control" id="project-pages-add-log-title">
                      </div>
                      <div class="mb-3">

                            <div class="container mb-2" id="project-pages-add-log-meta-row">
                              <div class="row align-items-start">
                                <div class="col">
                                 
                                    <label for="project-pages-add-log-date" class="form-label"><?php _e( 'Log Date:', 'projectpages' ); ?></label>
                                    <div class="input-group date" id="project-pages-add-log-datepicker">
                                        <input type="text" class="form-control" id="project-pages-add-log-date"/>
                                        <span class="input-group-append">
                                          <span class="input-group-text bg-light d-block">
                                            <span class="dashicons dashicons-calendar-alt"></span>
                                          </span>
                                        </span>
                                    </div>

                                </div>
                                <div class="col">
                                    
                                    <label for="project-pages-add-log-dashicon" class="form-label"><?php _e( 'Log Icon:', 'projectpages' ); ?></label>
                                    <div class="input-group">
                                        <span id="project-pages-log-dashicon-icon" class="dashicons dashicons-arrow-right"></span>
                                        <input id="project-pages-log-dashicon" type="hidden" value="dashicons-arrow-right" />
                                        <input class="btn btn-secondary btn-sm dashicons-picker" type="button" value="<?php _e( 'Choose Icon', 'projectpages' ); ?>" data-target="#project-pages-log-dashicon" style="border-radius: 4px" />
                                    </div>

                                </div>
                              </div>
                            </div>
                      </div>                

                      <div class="mb-3">
                        <label class="form-label"><?php _e( 'Log:', 'projectpages' ); ?></label>
                        <?php wp_editor( '', 'project_page_add_log_body', array( 'height', 500 ) ); ?>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', 'projectpages' ); ?></button>
                    <button type="button" class="btn btn-primary" id="project-pages-add-log-submit"><?php _e( 'Add Log', 'projectpages' ); ?></button>
                  </div>
                </div>
              </div>
            </div>


            <?php // edit modal ?> 
            <div class="modal fade modal-lg" id="project-pages-edit-log-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="project-pages-edit-log-modal-label" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <input type="hidden" id="project-pages-edit-log-id" value="" />
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="project-pages-edit-log-modal-label"><?php _e( 'Edit log entry', 'projectpages' ); ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <div class="mb-3">
                        <label for="project-pages-edit-log-title" class="form-label"><?php _e( 'Log Title:', 'projectpages' ); ?></label>
                        <input type="text" class="form-control" id="project-pages-edit-log-title">
                      </div>
                      <div class="mb-3">

                            <div class="container mb-2" id="project-pages-edit-log-meta-row">
                              <div class="row align-items-start">
                                <div class="col">
                                 
                                    <label for="project-pages-edit-log-date" class="form-label"><?php _e( 'Log Date:', 'projectpages' ); ?></label>
                                    <div class="input-group date" id="project-pages-edit-log-datepicker">
                                        <input type="text" class="form-control" id="project-pages-edit-log-date"/>
                                        <span class="input-group-append">
                                          <span class="input-group-text bg-light d-block">
                                            <span class="dashicons dashicons-calendar-alt"></span>
                                          </span>
                                        </span>
                                    </div>

                                </div>
                                <div class="col">
                                    
                                    <label for="project-pages-edit-log-dashicon" class="form-label"><?php _e( 'Log Icon:', 'projectpages' ); ?></label>
                                    <div class="input-group">
                                        <span id="project-pages-edit-log-dashicon-icon" class="dashicons dashicons-arrow-right"></span>
                                        <input id="project-pages-edit-log-dashicon" type="hidden" value="dashicons-arrow-right" />
                                        <input class="btn btn-secondary btn-sm dashicons-picker" type="button" value="<?php _e( 'Choose Icon', 'projectpages' ); ?>" data-target="#project-pages-edit-log-dashicon" style="border-radius: 4px" />
                                    </div>

                                </div>
                              </div>
                            </div>
                      </div>                

                      <div class="mb-3">
                        <label class="form-label"><?php _e( 'Log:', 'projectpages' ); ?></label>
                        <?php wp_editor( '', 'project_page_edit_log_body', array( 'height', 500 ) ); ?>
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger float-start" id="project-pages-edit-log-delete"><?php _e( 'Delete Log', 'projectpages' ); ?></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', 'projectpages' ); ?></button>
                    <button type="button" class="btn btn-primary" id="project-pages-edit-log-submit"><?php _e( 'Update Log', 'projectpages' ); ?></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade" tabindex="-1" id="project-pages-delete-log">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><?php _e( 'Delete Log', 'projectpages' ); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p><?php _e( 'Are you sure you want to delete this log?', 'projectpages' ); ?></p>
                    <input type="hidden" id="project-pages-delete-this-log" value="" />
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="project-pages-delete-log-cancel"><?php _e( 'Cancel', 'projectpages' ); ?></button>
                    <button type="button" class="btn btn-warning" id="project-pages-delete-log-certain"><?php _e( 'Delete Log', 'projectpages' ); ?></button>
                  </div>
                </div>
              </div>
            </div>
            <?php


            if (isset($_GET['deletelog'])){

                // raw delete
                $delID = (int)sanitize_text_field($_GET['deletelog']);
                if (!empty($delID)){

                    if (current_user_can('delete_post', $delID)) { 

                        wp_delete_post($delID);

                        echo '<div class="whppMessage" id="whppDeleted">Project Log Deleted</div>';

                    }

                }
            }



        }

        public function save_meta_box( $post_id, $post ) {

            if( empty( $_POST['meta_box_ids'] ) ){ return; }
            foreach( $_POST['meta_box_ids'] as $metabox_id ){

                if( !isset($_POST[ $metabox_id . '_nonce' ]) || ! wp_verify_nonce( $_POST[ $metabox_id . '_nonce' ], 'save_' . $metabox_id ) ){ continue; }                
                if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){ continue; }

                if( $metabox_id == 'whpp_logbody'  && $post->post_type == $this->postType){

                      if (isset($_POST['whpp_project_log_body'])) {

                        $titleStr = sanitize_text_field($_POST['whpp_project_log_title']);
                        $bodyStr = $_POST['whpp_project_log_body'];

                        if (!empty($titleStr) && !empty($bodyStr)){

                            // Save new or update log
                            if (isset($_POST['whpp_project_log_editid'])){

                                // update
                                $updateID = (int)$_POST['whpp_project_log_editid'];

                                
                            } else {

                                // create new
                                $updateID = wp_insert_post(array('post_type'=>'projectpagelog','post_status'=>'publish'));

                            }



                            // Save content
                            update_post_meta($updateID, 'projectpageid', $post_id);
                            update_post_meta($updateID, 'whpp_project_log_title', $titleStr);
                            update_post_meta($updateID, 'whpp_project_log_date', sanitize_text_field($_POST['whpp_project_log_date']));
                            update_post_meta($updateID, 'whpp_project_log_body', htmlspecialchars($bodyStr));

                        }

                      }                

                }
            }

            return $post;
        }
    }

    $projectPages__MetaboxLogs = new projectPages__MetaboxLogs( __FILE__ );

/* ======================================================
  / Logs Metabox
   ====================================================== */




/* ======================================================
  Feedback Metabox
   ====================================================== */

    class projectPages__MetaboxFeedback {

        static $instance;
        private $postType;

        public function __construct( $plugin_file ) {
            
            // lol.
            self::$instance = $this;
            $this->postType = 'projectpage';

            // if transient not there, draw
            if ( ! get_transient( 'projectpages-hide-feedback-metabox' ) ){

                add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );

            }

        }

        public function create_meta_box() {

            add_meta_box(
                'pp_feedback',
                __('Give Feedback','projectpages'),
                array( $this, 'print_meta_box' ),
                $this->postType,
                'side',
                'low'
            );
        }

        public function print_meta_box( $post, $metabox ) {

            global $projectPages_urls;

            // output nonce for AJAX
            ?><script>
                var projectPages_feedback_nonce = '<?php echo esc_js( wp_create_nonce( 'project-pages-hide-feedback-nonce' ) ); ?>';
            </script>
            <div class="p-1 text-center bg-body-tertiary" id="project-pages-feedback-metabox">
                <div class="container py-1">
                  <button type="button" class="position-absolute top-1 btn-close bg-secondary bg-opacity-10 rounded-pill" aria-label="Close" id="project-pages-hide-feedback"></button>
                  <h1 class="text-body-emphasis"><?php _e( 'Do you like Project Pages?', 'projectpages' ); ?></h1>
                  <p class="col-lg-8 mx-auto lead">
                    <?php _e( 'Project Pages is a side project made for the ❤️ of making. It really helps me to hear what you think, so please do give feedback by clicking the button below:', 'projectpages' ); ?>
                  </p>
                  <a href="<?php echo $projectPages_urls['feedback']; ?>" target="_blank" class="btn btn-primary btn-sm mb-1" type="button"><?php _e( 'Give Feedback', 'projectpages' ); ?></a>

                </div>
              </div>
            <?php
        }

    }

    $projectPages__MetaboxFeedback = new projectPages__MetaboxFeedback( __FILE__ );

/* ======================================================
  / Feedback Metabox
   ====================================================== */
