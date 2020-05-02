<?php
/*
Template Name: Grid Portfolio
*/

$columns     = false;
$rows        = false;
$image_sizes = array();

if ( have_posts() ) :

    if ( is_page() ) {

        $grid_portfolio = new GridPortfolio( get_the_ID() );

        // Sets: $orientation, $aspect_ratio, $grid_size, $image_sizes, $columns, $rows
        extract( $grid_portfolio->get_options() );

    } else {

        $default_grid_portfolio = it_find_page_by_template( 'template-portfolio-grid.php', array( 'post_status' => 'publish' ) );

        if ( $default_grid_portfolio ) {
            $grid_portfolio = new GridPortfolio( $default_grid_portfolio[0]->ID );
            extract( $grid_portfolio->get_options() );
        }

        /**
         * We are on a Project Type page,
         * thus we need to check if there is any overriden options.
         *
         * Sets: $orientation, $aspect_ratio, $grid_size, $image_sizes, $columns, $rows
         */
        extract( fluxus_project_type_grid_options( get_queried_object()->term_id ) );

    }

    if ( ! is_numeric( $columns ) ) {
        $columns = 4;
    }

    if ( ! is_numeric( $rows ) ) {
        $rows = 3;
    }

    $max_size = $orientation == 'horizontal' ? $rows : $columns;

    fluxus_add_html_class( 'layout-portfolio-grid layout-portfolio-grid-' . $orientation );

    if ( $orientation != 'vertical' ) {
        fluxus_add_html_class( 'horizontal-page' );
    }

    get_header();

    fluxus_query_portfolio( array(
        'fluxus-project-type' => get_query_var( 'fluxus-project-type' )
    ) );

    ?>
    <div id="main" class="site">

        <div class="portfolio-grid" data-aspect-ratio="<?php esc_attr_e( $aspect_ratio ); ?>" data-orientation="<?php esc_attr_e( $orientation ); ?>" data-columns="<?php echo $columns; ?>" data-rows="<?php echo $rows; ?>"><?php

            while ( have_posts() ) :

                the_post();

                $project = new PortfolioProject( get_the_ID() );
                $featured = $project->get_featured_media();

                if ( ! $featured ) continue; // We have no media on this project, nothing to show.

                $thumbnail = FLUXUS_IMAGES_URI . '/no-portfolio-thumbnail.png';
                $thumbnail_2x = '';

                $thumbnail_data = $featured->get_thumbnail( 'fluxus-thumbnail' );
                $thumbnail_data_2x = $featured->get_thumbnail( 'fluxus-thumbnail-2x', 'fluxus-thumbnail' );

                if ( $thumbnail_data ) {
                    $thumbnail = $thumbnail_data['src'];
                }

                if ( $thumbnail_data_2x ) {
                    $thumbnail_2x = $thumbnail_data_2x['src'];
                    if ( $thumbnail_2x == $thumbnail ) {
                        $thumbnail_2x = '';
                    }
                }

                if ( isset( $image_sizes[get_the_ID()] ) ) {
                    $size = $image_sizes[get_the_ID()];
                    $size = $size > $max_size ? $max_size : $size;
                } else {
                    $size = 1;
                }

                ?>
                <article class="grid-project size-<?php echo $size; ?>" data-size="<?php echo $size; ?>" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
                    <a href="<?php the_permalink(); ?>" class="preview" style="background-image: url(<?php echo esc_url( $thumbnail ); ?>);" data-hd-image="<?php echo esc_url( $thumbnail_2x ); ?>">
                        <span class="hover-box">
                            <span class="inner"><?php
                                if ( $project->meta_subtitle ) : ?>
                                    <i><?php echo $project->meta_subtitle; ?></i><?php
                                endif;

                                ?>
                                <b><?php the_title(); ?></b>
                                <?php if ( post_password_required() ) : ?>
                                    <span class="password-required">
                                        <?php _e( 'Password required', 'fluxus' ); ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                        </span>
                        <?php
                        /**
                         * Add <img /> tag, so that they can be found
                         * by search engines and social sharing widgets.
                         */
                        ?>
                        <img class="hide" src="<?php echo esc_url( ! empty( $thumbnail_2x ) ? $thumbnail_2x : $thumbnail ); ?>" alt="<?php esc_attr_e( get_the_title() ); ?>" />
                    </a>
                </article><?php

            endwhile; ?>

        </div>

    </div>

<?php

else:

    get_header();

endif;

wp_reset_query();

get_footer();
