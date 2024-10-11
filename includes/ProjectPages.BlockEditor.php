<?php 
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 17/06/24
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'PROJECTPAGES_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

// Register a block template for Gutenberg editing.
// **EXPERIMENTAL**
function projectPages_register_block_template() {

	$block_template = array(
        array( 'core/group', array(
            'tagName' => 'main',
            'align' => 'full'
        ), array(
            array( 'core/group', array(
                'style' => array(
                    'spacing' => array(
                        'margin' => array(
                            'top' => 'var:preset|spacing|40'
                        ),
                        'padding' => array(
                            'bottom' => 'var:preset|spacing|50'
                        )
                    )
                ),
                'layout' => array(
                    'type' => 'default'
                )
            ), array(
                array( 'project-pages-blocks/hero', array(
                    'heroTextColour' => '#ffffff',
                    'heroTextAltColour' => '#4b73d7'
                ) ),
                array( 'core/columns', array(
                    'style' => array(
                        'spacing' => array(
                            'padding' => array(
                                'top' => 'var:preset|spacing|40',
                                'right' => 'var:preset|spacing|30',
                                'bottom' => 'var:preset|spacing|40',
                                'left' => 'var:preset|spacing|30'
                            )
                        )
                    )
                ), array(
                    array( 'core/column', array(
                        'width' => '66.66%',
                        'style' => array(
                            'spacing' => array(
                                'padding' => array(
                                    'top' => 0,
                                    'right' => 0,
                                    'bottom' => 0,
                                    'left' => 0
                                )
                            )
                        )
                    ), array(
                        array( 'project-pages-blocks/summary' )
                    ) ),
                    array( 'core/column', array(
                        'width' => '33.33%'
                    ), array(
                        array( 'project-pages-blocks/status-card' )
                    ) )
                ) ),
                array( 'core/separator', array(
                    'className' => 'is-style-wide'
                ) ),
                array( 'project-pages-blocks/body' ),
                array( 'project-pages-blocks/logs' )
            ) )
        ) )
    );


	/*
	    // Register the template
	    register_block_pattern(
	        'project-pages/project-page-template',
	        array(
	            'title'       => __( 'Project Page Template', 'text-domain' ),
	            'description' => _x( 'A template for project pages.', 'Block pattern description', 'text-domain' ),
	            'content'     => serialize_blocks( $block_template ),
	        )
	    );
	*/


	$post_type_object = get_post_type_object( 'projectpage' );
	$post_type_object->template = $block_template;
	$post_type_object->template_lock = 'all';

}