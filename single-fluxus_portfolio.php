<?php
/**
 * Portfolio single project template.
 */

fluxus_add_html_class( 'horizontal-page' );

the_post();

$project = new PortfolioProject( get_the_ID() );
$project_media = $project->get_media();
$featured = $project->get_featured_media();

if ( $featured ) {
    global $fluxus_theme;

    // Use featured image as page thumbnail.
    $image = $featured->get_thumbnail( 'fluxus-max' );
    if ( $image ) {
        $fluxus_theme->set_image( $image['src'] );
    }

    // Use project excerpt as page description.
    $excerpt = get_the_excerpt();
    if ( $excerpt ) {
        $fluxus_theme->set_description( $excerpt );
    }
}

get_header();

?>
<div id="main" class="site site-with-sidebar"><?php

    if ( ! post_password_required() ) :

        $lazy_loading = of_get_option( 'fluxus_lazy_loading' );
        $on_click_action = of_get_option( 'fluxus_project_image_click', 'disabled' );

        $portfolio_settings = array(
                'class' => 'portfolio-single horizontal-content',
                'data-loading' => __( 'Please wait...', 'fluxus' ),
                'data-lazy' => of_get_option( 'fluxus_lazy_loading' )
            );

        if ( $on_click_action != 'disabled' ) {
            $portfolio_settings['data-onclick-action'] = $on_click_action;
        }

        if ( $lazy_loading ) {
            $portfolio_settings['class'] .= ' lazy-loading';
        }

        $portfolio_settings = it_array_to_attributes( $portfolio_settings );

        ?>
        <div id="content" class="site-content">

            <article<?php echo $portfolio_settings; ?>><?php

                $index = 0;

                foreach ( $project_media as $media_item ) :

                    if ( ! $media_item->meta_published ) continue;

                    $index++;

                    if ( $media_item->is_image() ) :

                        $image = $media_item->get_thumbnail( 'fluxus-max' );

                        if ( ! $image ) continue;

                        ?>
                        <div class="horizontal-item project-image">
                            <figure><?php
                                if ( $on_click_action != 'disabled' ) : ?>
                                    <a href="<?php echo esc_url( $image['src'] ); ?>" class="project-image-link"><?php
                                endif;

                                    if ( $lazy_loading ) : ?>
                                        <div class="image lazy-image" data-width="<?php echo $image['width']; ?>" data-height="<?php echo $image['height']; ?>" data-src="<?php echo esc_url( $image['src'] ); ?>">
                                            <div class="loading"><?php _e( 'Please Wait', 'fluxus' ); ?></div>
                                        </div><?php
                                    else: ?>
                                        <img class="image" src="<?php echo esc_url( $image['src'] ); ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" alt="<?php echo esc_attr( $media_item->get_alt() ); ?>" /><?php
                                    endif;

                                if ( $on_click_action != 'disabled' ) : ?>
                                    </a><?php
                                endif;

                                if ( $media_item->meta_description ) : ?>
                                    <figcaption><?php echo nl2br( $media_item->meta_description ); ?></figcaption><?php
                                endif;

                                ?>
                            </figure>
                        </div><?php

                    else:

                        if ( ! $media_item->meta_embed ) continue;

                        $video_size = $media_item->get_video_size();

                        // Don't show video, if there is no width and height set
                        if ( ! $video_size['width'] || ! $video_size['height'] ) {
                            continue;
                        }

                        ?>
                        <div class="horizontal-item project-video">
                            <?php echo $media_item->meta_embed; ?>
                        </div><?php

                    endif;

                endforeach;


                /**
                 * Portfolio navigation & sharing.
                 */

                $disable_like_this_project = of_get_option( 'fluxus_disable_like_this_project' );
                $disable_other_projects = of_get_option( 'fluxus_disable_other_projects' );

                if ( ! ( $disable_like_this_project && $disable_other_projects ) ) : ?>

                    <nav class="portfolio-navigation"><?php

                        if ( ! $disable_like_this_project ) : ?>

                            <header<?php if ( $disable_other_projects ) { echo ' class="other-projects-disabled"'; } ?>>
                                <h3><?php _e( 'Like this project?', 'fluxus' ); ?></h3>
                                <div class="feedback-buttons"><?php

                                    $args = array(
                                            'class' => 'btn-appreciate',
                                            'title' => __( 'Appreciate', 'fluxus' ),
                                            'title_after' => __( 'Appreciated', 'fluxus' )
                                        );
                                    fluxus_appreciate( $post->ID, $args );

                                    $args = array(
                                            'id' => 'sharrre-project',
                                            'data-buttons-title' => array(
                                                    __( 'Share this project', 'fluxus' )
                                                )
                                        );

                                    $sharrre = fluxus_get_social_share( $args );

                                    if ( $sharrre ) : ?>
                                        <span class="choice"><span><?php _e( 'Or', 'fluxus' ); ?></span></span><?php
                                        echo $sharrre;
                                    endif;

                                ?>
                                </div>
                            </header><?php

                        endif; // end of Like this project?

                        if ( ! $disable_other_projects ) : ?>

                            <div class="navigation">
                                <div class="other-projects">

                                    <h3><?php _e( 'Other projects', 'fluxus' ); ?></h3>
                                    <?php

                                    $previous_project = false;
                                    $next_project = false;
                                    $related_projects = $project->get_other_projects();

                                    $index = 0;

                                    foreach ( $related_projects as $key => $related_project ) :

                                        $index++;
                                        $current = $project->post->ID == $related_project->post->ID;

                                        if ( $current ) {

                                            if ( $key && isset( $related_projects[$key - 1] ) ) {
                                                $previous_project = $related_projects[$key - 1];
                                            }

                                            if ( isset( $related_projects[$key + 1] ) ) {
                                                $next_project = $related_projects[$key + 1];
                                            }

                                        }

                                        $featured_media = $related_project->get_featured_media();

                                        $attr = array(
                                                'href' => array(
                                                        esc_url( get_permalink( $related_project->post->ID ) )
                                                    ),
                                                'class' => array(
                                                        'item-' . $index,
                                                        $index % 4 == 0 ? 'fourth' : '',
                                                        $current ? 'active' : ''
                                                    )
                                            );

                                        $image = array( 'src' => get_template_directory_uri() . '/images/no-portfolio-thumbnail.png' );

                                        if ( $featured_media ) {
                                            $featured_image = $featured_media->get_thumbnail( 'fluxus-gallery-thumbnail' );
                                            if ( $featured_image ) {
                                                $image = $featured_image;
                                            }
                                        }

                                        $attr['style'] = array(
                                                'background-image: url(' . $image['src'] . ')'
                                            );

                                        ?>
                                        <a<?php echo it_array_to_attributes( $attr ); ?>>
                                            <?php echo $related_project->post->post_title; ?>
                                            <span class="hover"><?php
                                                if ( $current ) {
                                                    _e( 'Current', 'fluxus' );
                                                } else {
                                                    _e( 'View', 'fluxus' );
                                                }
                                                ?>
                                            </span>
                                        </a><?php

                                    endforeach; ?>
                                </div><?php

                                /**
                                 * Next / Previous / Back to portfolio buttons.
                                 */

                                if ( $previous_project ) : ?>
                                    <a href="<?php echo esc_url( get_permalink( $previous_project->post->ID ) ); ?>" class="button-minimal prev-project button-icon-left icon-left-open-big"><?php _e( 'Previous', 'fluxus' ); ?></a><?php
                                endif;

                                if ( $next_project ) : ?>
                                    <a href="<?php echo esc_url( get_permalink( $next_project->post->ID ) ); ?>" class="button-minimal next-project button-icon-right icon-right-open-big"><?php _e( 'Next', 'fluxus' ); ?></a><?php
                                endif;

                                $back_title = $project->meta_back_to_link ? __( 'Back to projects', 'fluxus' ) : __( 'Back to portfolio', 'fluxus' );

                                ?>
                                <a href="<?php echo esc_url( $project->get_back_link() ); ?>" class="button-minimal back-portfolio"><?php echo $back_title ?></a>
                            </div><?php

                        endif; // end of Other Proejcts

                        ?>
                    </nav><?php

                endif;

                ?>
            </article>

        </div><?php

    endif; // end of !post_password_required()

    get_sidebar( 'portfolio-single' );

    ?>
</div><?php // end of #main

get_footer();