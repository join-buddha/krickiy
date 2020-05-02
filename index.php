<?php
/**
 * The main template file.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

/**
 * This file chooses between two templates to use for blog layout.
 * If there's a page with a Horizontal Blog template, then the horizontal
 * layout will be used to display post lists. Otherwise if there's a page
 * with Vertical Blog template, then vertical layout will be used throughout
 * the website.
 */
$template = 'template-blog-horizontal.php';

$horizontal_blog = it_find_page_by_template( 'template-blog-horizontal.php', array( 'post_status' => 'publish' ) );

if ( ! $horizontal_blog ) {
    $vertical_blog = it_find_page_by_template( 'template-blog-vertical.php', array( 'post_status' => 'publish' ) );

    if ( $vertical_blog ) {
        $template = 'template-blog-vertical.php';
    }
}

$template = apply_filters( 'fluxus_blog_index_template', $template );

require_once dirname( __FILE__ ) . '/' . $template;