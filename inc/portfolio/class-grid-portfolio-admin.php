<?php

class GridPortfolioAdmin extends FluxusAdminPage {

    public $scripts = array(
        array( 'fluxus-wp-admin-grid', 'grid.js', array( 'jquery', 'backbone' ) )
    );


    function __construct( $post_id ) {

        parent::__construct( $post_id );

        // Add options meta box
        add_meta_box( 'fluxus-portfolio-grid-meta', __( 'Grid Options', 'fluxus' ),
                      array( $this, 'admin_options_content' ), 'page', 'normal', 'low' );

        add_action( 'save_post', array( $this, 'admin_options_save' ), 2 );

    }


    function get_orientation_options() {

        return array(
            'horizontal' => __( 'Horizontal', 'fluxus' ),
            'vertical'   => __( 'Vertical', 'fluxus' )
        );

    }


    function get_aspect_ratio_options() {

        $options = array(
            'auto'   => __( 'Auto', 'fluxus' ),
            '1:1'    => __( '1:1 (square)', 'fluxus' ),
            '4:3'    => '4:3',
            '2:1'    => '2:1',
            '16:9'   => '16:9',
            '9:16'   => '9:16',
        );

        return apply_filters( 'fluxus_portfolio_aspect_ratio', $options );

    }


    function get_grid_size_options() {

        $size_options = array(
                        '5 4' => __( '5 columns, 4 rows', 'fluxus' ),
                        '5 3' => __( '5 columns, 3 rows', 'fluxus' ),
                        '4 3' => __( '4 columns, 3 rows', 'fluxus' ),
                        '3 3' => __( '3 columns, 3 rows', 'fluxus' ),
                        '3 2' => __( '3 columns, 2 rows', 'fluxus' )
                    );

        return apply_filters( 'fluxus_portfolio_grid_sizes', $size_options );

    }


    function get_grid_options() {

        $grid = new GridPortfolio( $this->post_id );

        return array_merge(
            $grid->get_options(),
            array( 'customize_url' => add_query_arg( 'customize-layout', 1, get_permalink( $this->post_id ) ) )
        );

    }


    function admin_options_content() {

        global $post;

        extract( $this->get_grid_options() );

        ?>
        <div class="fluxus-meta-field">
            <label for="fluxus_portfolio_grid_orientation"><?php _e( 'Orientation', 'fluxus' ); ?></label>
            <div class="field"><?php

                it_select_tag(
                    array( 'name' => 'fluxus_portfolio_grid_orientation' ),
                    $this->get_orientation_options(),
                    $orientation
                );

                ?>
            </div>
        </div>
        <div class="fluxus-meta-field fluxus-meta-field-aspect-ratio">
            <label for="fluxus_portfolio_grid_aspect_ratio"><?php _e( 'Aspect ratio', 'fluxus' ); ?></label>
            <div class="field"><?php

                it_select_tag(
                    array( 'name' => 'fluxus_portfolio_grid_aspect_ratio' ),
                    $this->get_aspect_ratio_options(),
                    $aspect_ratio
                );

                ?>
            </div>
            <div class="notes">
                <?php _e( 'When set to <b>auto</b> aspect ratio will be chosen depending on the grid size.', 'fluxus' ); ?>
            </div>
        </div>
        <div class="fluxus-meta-field">
            <label for="fluxus_portfolio_grid_size"><?php _e( 'Grid size', 'fluxus' ); ?></label>
            <div class="field"><?php

                it_select_tag(
                    array( 'name' => 'fluxus_portfolio_grid_size' ),
                    $this->get_grid_size_options(),
                    $grid_size
                );

                ?>
            </div>
        </div>
        <div class="fluxus-meta-field">
            <label for="fluxus_portfolio_grid_size"><?php _e( 'Image sizes', 'fluxus' ); ?></label>
            <div class="field">
                <a href="<?php echo esc_url( $customize_url ); ?>" class="button button-grid-layout" style="margin-right: 10px"><?php _e( 'Customize', 'fluxus' ); ?></a>
                <a href="#" class="button button-grid-layout-reset" data-confirm="<?php esc_attr_e( __( 'This will set image sizes to the default values. Are you sure you want to continue?', 'fluxus' ) ); ?>"><?php _e( 'Reset', 'fluxus' ); ?></a>

                <input type="hidden" name="fluxus_portfolio_grid_image_sizes" value="<?php esc_attr_e( $image_sizes_serialized ); ?>">
            </div>
            <div class="notes">
                <?php _e( 'Allows to increase thumbnail sizes for chosen projects.', 'fluxus' ); ?>
            </div>
        </div>
        <div class="fluxus-meta-field-note">
            <b><?php _e( 'Note:', 'fluxus' ); ?></b>
            <?php
                _e( 'on smaller screens (eg. iPad, iPhone) grid settings are automatically changed to best fit the viewer\'s screen.', 'fluxus' );
            ?>
        </div>
        <?php

    }


    function admin_options_save( $post_id ) {

        if ( ! it_check_save_action( $post_id, 'page' ) ) {
            return $post_id;
        }

        $grid = new GridPortfolio( $this->post_id );
        $grid->update_from_array( stripslashes_deep( $_POST ) )->save();

    }

}
