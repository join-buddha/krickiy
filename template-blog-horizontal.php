<?php
/**
 * Template Name: Horizontal Blog
 *
 * @package fluxus
 * @since fluxus 1.2
 */

fluxus_add_html_class( 'horizontal-page horizontal-posts' );

get_header();

if ( is_page() ) {
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    query_posts( 'post_type=post&paged=' . $paged );
}

?>
<div id="main" class="site site-with-sidebar">
    <div id="content" class="site-content"><?php

        if ( have_posts() ) : ?>

            <div class="horizontal-content"><?php

                while ( have_posts() ) : the_post();
                    /**
                     * Include the Post-Format-specific template for the content.
                     */
                    ?>
                    <div class="horizontal-item">
                        <?php get_template_part( 'content', get_post_format() ); ?>
                    </div>
                    <?php

                endwhile;

                if ( $wp_query->max_num_pages > 1 ) :
                    fluxus_content_paging();
                endif;

                ?>
            </div><?php

        else :

            get_template_part( 'no-results', 'index' );

        endif; ?>
    </div>

    <?php get_sidebar( 'blog' ); ?>

</div>

<?php

get_footer();

