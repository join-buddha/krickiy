<?php
/*
Template Name: Full Page Slider
*/

fluxus_add_html_class( 'horizontal-page no-scroll' );

if ( have_posts() ) : the_post();

    $slides = fluxus_slider_get_published_slides( $post->ID );
    $slides = apply_filters( 'fluxus_before_slider', $slides );

    $options = fluxus_slider_get_options( $post->ID );

    $slider_attr = array();

    if ( $options['slideshow'] ) {
        $slider_attr['data-slideshow'] = '1';
        $slider_attr['data-slideshow-duration'] = $options['slideshow_interval'];
    }

    $slider_attr['data-animation'] = $options['slideshow_animation'];

    global $fluxus_theme;
    if ( ( ! is_front_page() || ! $fluxus_theme->get_image() ) && $slides && $slides[0] ) {
        $data = fluxus_slider_get_slide_data( $slides[0]->ID );
        if ( !is_front_page() && $data['description'] ) {
            $fluxus_theme->set_description( $data['description'] );
        }
        if ( $data['image'] ) {
            $fluxus_theme->set_image( $data['image'] );
        }
    }

    get_header();

    if ( $slides ) :

        $container_attributes = array( 'class' => array( 'site' ) );

        ?>
        <div id="main" <?php echo it_array_to_attributes( apply_filters( 'fluxus_slider_attributes', $container_attributes ) ); ?>>

            <a href="#" class="slider-arrow-left"><?php _e( 'Previous', 'fluxus' ); ?></a>
            <a href="#" class="slider-arrow-right"><?php _e( 'Next', 'fluxus' ); ?></a>

            <div class="slider"<?php echo it_array_to_attributes( $slider_attr ); ?>><?php

                $index = 0;
                $images = array();

                /**
                 * Slide loop.
                 */
                foreach ( $slides as $slide ) : $index++;

                    // get slide data
                    $data = fluxus_slider_get_slide_data( $slide->ID );

                    // slide dom element attributes
                    $slide_attr = array(
                            'class' => array( 'slide' ),
                            'id'    => 'slide-' . $slide->ID,
                            'data-image' => $data['image']
                        );

                    $images[] = $data;

                    if ( $data['background_position'] ) {

                        $slide_attr['class'][] = 'image-' . str_replace( ' ', '-', $data['background_position'] );

                    }

                    // slide info box dom element attributes
                    $slide_info_attr = array(
                            'class' => array(
                                    'info',
                                    'style-default',
                                    $data['info_box_text_color']
                                ),
                            'style' => array()
                        );

                    $position = false;

                    $slide_info_attr['data-position'] = $data['info_box_position'];

                    if ( $data['info_box_position'] == 'custom' ) {

                        $slide_info_attr['data-left'][] = $data['info_box_left'];
                        $slide_info_attr['data-top'][] = $data['info_box_top'];

                    }

                    if ( $data['dim_background'] == '1' ) {

                        $slide_info_attr['class'][] = 'dim-background';

                    }

                    ?>
                    <article<?php echo it_array_to_attributes( $slide_attr ); ?>><?php

                        if ( $data['subtitle'] ||
                             $data['title'] ||
                             $data['description'] ||
                             $data['link'] ||
                             $data['link_portfolio'] ) : ?>

                                <div<?php echo it_array_to_attributes( $slide_info_attr ); ?>>
                                    <div class="viewport">
                                        <div class="animate-1"><?php

                                            if ( $data['subtitle'] ) : ?>
                                                <p class="slide-subtitle"><?php echo $data['subtitle']; ?></p><?php
                                            endif;

                                            if ( $data['title'] ) : ?>
                                                <h2 class="slide-title"><?php echo $data['title']; ?></h2><?php
                                            endif;

                                            ?>
                                        </div><?php

                                        if ( $data['description'] || $data['link'] || $data['link_portfolio'] ) : ?>
                                            <div class="decoration"></div>
                                            <div class="animate-2"><?php

                                                $title = !empty( $data['link_title'] ) ? $data['link_title'] : __( 'View', 'fluxus' );

                                                if ( $data['description'] ) : ?>
                                                    <div class="description"><p><?php echo nl2br( $data['description'] ); ?></p></div><?php
                                                endif;

                                                if ( $data['link'] ) : ?>
                                                    <a href="<?php echo esc_url( $data['link'] ); ?>" class="button"><?php echo $title; ?></a><?php
                                                endif;

                                                if ( $data['link_portfolio'] && !$data['link'] ) : ?>
                                                    <a href="<?php echo get_permalink( $data['link_portfolio'] ); ?>" class="button"><?php echo $title; ?></a><?php
                                                endif;

                                                ?>
                                            </div><?php
                                        endif; ?>
                                    </div>
                                </div><?php

                        endif; ?>

                    </article><?php

                endforeach;

                ?>
                <div class="hide">
                    <?php foreach ( $images as $image_data ) : ?>
                        <img src="<?php echo esc_url( $image_data['image'] ); ?>" alt="<?php echo esc_attr( $image_data['title'] ); ?>" />
                    <?php endforeach; ?>
                </div>
            </div>

        </div><?php // end of div#main

    endif;

else:

    get_header();

endif; // have_posts()

get_footer();