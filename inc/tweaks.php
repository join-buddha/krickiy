<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @since fluxus 1.0
 */


/**
 * Set excerpt length.
 */
function fluxus_excerpt_lenght( $length ) {
    return 50;
}
add_filter( 'excerpt_length', 'fluxus_excerpt_lenght', 1000 );


/**
 * Used to increase excerpt length on certain post types.
 */
function fluxus_increased_excerpt_lenght( $length ) {
    return 100;
}


function fluxus_continue_reading_link() {
    return '<div class="wrap-excerpt-more"><a class="excerpt-more" href="' . esc_url( get_permalink() ) . '">' . __( 'Continue reading', 'fluxus' ) . '</a></div>';
}


function fluxus_auto_excerpt_more( $more ) {
    return ' &hellip;';
}
add_filter( 'excerpt_more', 'fluxus_auto_excerpt_more' );


function fluxus_get_the_excerpt( $content ) {

    global $post;

    if ( ! is_attachment() && ( $post->post_type != 'fluxus_portfolio' ) ) {

        /**
         * Show continue reading only if content is not fully shown or there
         * is a n manually set excerpt.
         */
        if ( ( $post->post_content != $content ) || has_excerpt( $post->ID ) ) {

            $content .= fluxus_continue_reading_link();

        }

    }

    return $content;

}
add_filter( 'get_the_excerpt', 'fluxus_get_the_excerpt' );



/**
 * Modifies default [wp_caption] shortcode to remove
 * style="width: width+10" from output.
 */
function fluxus_image_caption( $foo, $attr, $content = null ) {

    extract(shortcode_atts(array(
        'id'    => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    ), $attr));

    if ( 1 > (int) $width || empty($caption) )
        return $content;

    if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

    return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '">'
    . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';

}
add_filter( 'img_caption_shortcode', 'fluxus_image_caption', 1, 3 );


function fluxus_add_image_link_class( $content ) {

    // find all links to images
    if ( preg_match_all( '/<a.*? href="(.*?)\.(png|jpg|jpeg|gif)">/i', $content, $matches ) ) {

        foreach ( $matches[0] as $match ) {

            if ( preg_match( '/class=".*?"/i', $match ) ) {
                $replacement = preg_replace( '/(<a.*? class=".*?)(".*?>)/', '$1 link-to-image$2', $match );
            } else {
                $replacement = preg_replace( '/(<a.*?)>/', '$1 class="link-to-image">', $match );
            }

            // replace them using links with classes
            $content = str_replace( $match, $replacement, $content );

        }

    }

    return $content;

}
add_filter( 'the_content', 'fluxus_add_image_link_class' );


/**
 * Filters that adds custom classes to comments paging navigation.
 */
function fluxus_comment_previous_page() {
    return ' class="button-minimal button-icon-right icon-right-open-big" ';
}
add_filter( 'previous_comments_link_attributes', 'fluxus_comment_previous_page' );


function fluxus_comment_next_page() {
    return ' class="button-minimal button-icon-left icon-left-open-big" ';
}
add_filter( 'next_comments_link_attributes', 'fluxus_comment_next_page' );


/**
 * Filters that adds a wrapping <span class="count" /> around item count in widgets.
 * Used for styling purposes.
 */
function fluxus_wp_list_categories_filter( $output ) {
    return preg_replace( '/\<\/a\>\s+?\((\d+)\)/', '</a><span class="count">[$1]</span>', $output );
}
add_filter( 'wp_list_categories', 'fluxus_wp_list_categories_filter' );


function fluxus_get_archives_link_filter( $output ) {
    return preg_replace( '/\<\/a\>(&nbsp;)?(\s+)?\((\d+)\)/', '</a><span class="count">[$3]</span>', $output );
}
add_filter( 'get_archives_link', 'fluxus_get_archives_link_filter' );


function fluxus_wp_list_bookmarks_filter( $output ) {
    return preg_replace( '/\<\/a\>(&nbsp;)?(\s+)?(\d+)/', '</a><span class="count">[$3]</span>', $output );
}
add_filter( 'wp_list_bookmarks', 'fluxus_wp_list_bookmarks_filter' );

/**
 * Comments should be turned off by default for newly created pages.
 *
 * @since fluxus 1.2.5
 */
function fluxus_default_content( $post_content, $post ) {
    if ( $post->post_type && ( $post->post_type == 'page' ) ) {
        $post->comment_status = 'closed';
        $post->ping_status = 'closed';
    }
    return $post_content;
}
add_filter( 'default_content', 'fluxus_default_content', 10, 2 );


/**
 * A filter that formats <title /> tag.
 */
function fluxus_wp_title( $title, $sep, $seplocation ) {
    global $page, $paged;

    // Don't affect in feeds.
    if ( is_feed() ) {
        return $title;
    }

    // Add the blog name
    if ( 'right' == $seplocation ) {
        $title .= get_bloginfo( 'name' );
    } else {
        $title = get_bloginfo( 'name' ) . $title;
    }

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );

    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title .= " {$sep} {$site_description}";
    }

    // Add a page number if necessary:
    if ( $paged >= 2 || $page >= 2 ) {
        $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
    }

    return $title;
}
add_filter( 'wp_title', 'fluxus_wp_title', 10, 3 );


/**
 * Remove rel=next and rel=prev from project pages for faster loading.
 */
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );