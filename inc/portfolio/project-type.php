<?php

/**
 * --------------------------------------------------------------------
 * Project Type WP Admin functions
 * --------------------------------------------------------------------
 */


class FluxusProjectTypeOptions {
    protected $id;
    protected $options = array(
            'layout' => '',
            'grid_orientation' => '',
            'grid_aspect_ratio' => '',
            'grid_size' => '',
            'grid_image_sizes' => '',
        );

    function __construct( $project_type_term_id ) {
        $this->id = $project_type_term_id;
        return $this;
    }

    function __set( $name, $value ) {
        if ( isset( $this->options[ $name ] ) ) {
            $this->options[ $name ] = $value;
            update_option( 'project_type_' . $name . '_' . $this->id, $value );
            return $this;
        }
    }

    function __get( $name ) {
        if ( isset( $this->options[ $name ] ) ) {
            return get_option( 'project_type_' . $name . '_' . $this->id );
        }
    }

    function delete() {
        delete_option( 'project_type_grid_size_' . $this->id );
        delete_option( 'project_type_layout_' . $this->id );
    }
}


class ProjectTypeGridOptions extends GridPortfolioAdmin {
    protected $term_id;
    protected $term;
    protected $options;

    function __construct( $term_id ) {
        parent::__construct( 0 );

        $this->term_id = $term_id;
        $this->term = get_term( $term_id, 'fluxus-project-type' );
        $this->options = new FluxusProjectTypeOptions( $term_id );
    }

    function get_orientation_options() {
        return array_merge(
            array( '' => __( 'Default', 'fluxus' ) ),
            parent::get_orientation_options()
        );
    }

    function get_aspect_ratio_options() {
        return array_merge(
            array( '' => __( 'Default', 'fluxus' ) ),
            parent::get_aspect_ratio_options()
        );
    }

    function get_grid_size_options() {
        return array_merge(
            array( '' => __( 'Default', 'fluxus' ) ),
            parent::get_grid_size_options()
        );
    }

    function get_grid_options() {
        $url = get_term_link( $this->term );

        $options = array(
            'orientation'   => $this->options->grid_orientation,
            'aspect_ratio'  => $this->options->grid_aspect_ratio,
            'grid_size'     => $this->options->grid_size,
            'image_sizes_serialized' => $this->options->grid_image_sizes,
            'customize_url' => add_query_arg( 'customize-layout', 1, $url )
        );

        $options['image_sizes'] = GridPortfolio::parse_image_sizes( $options['image_sizes_serialized'] );

        return $options;
    }

}


function fluxus_portfolio_layouts() {

    return array(
        ''           => __( 'Default', 'fluxus' ),
        'horizontal' => __( 'Horizontal', 'fluxus' ),
        'grid'       => __( 'Grid', 'fluxus' )
    );

}


function fluxus_project_type_option( $project_type_id, $option_name ) {

    $options = new FluxusProjectTypeOptions( $project_type_id );
    $value = $options->$option_name;

    if ( $value === false ) {
        $project_type = get_term_by( 'id', $project_type_id, 'fluxus-project-type' );

        if ( $project_type->parent ) {
            return fluxus_project_type_option( $project_type->parent, $option_name );
        }
    }

    return $value;

}


/**
 * Recursively checks if project type has any overridden options.
 * Returns an array with overriden options, so it can be used with extract().
 */
function fluxus_project_type_grid_options( $project_type_id ) {

    $vars = array();

    $grid_size = fluxus_project_type_option( $project_type_id, 'grid_size' );
    $grid_size = explode( ' ', $grid_size );
    if ( is_array( $grid_size ) && count( $grid_size ) == 2 ) {
        $vars['columns'] = $grid_size[0];
        $vars['rows'] = $grid_size[1];
    }

    $grid_orientation = fluxus_project_type_option( $project_type_id, 'grid_orientation' );
    if ( $grid_orientation ) {
        $vars['orientation'] = $grid_orientation;
    }

    $grid_aspect_ratio = fluxus_project_type_option( $project_type_id, 'grid_aspect_ratio' );
    if ( $grid_aspect_ratio ) {
        $vars['aspect_ratio'] = $grid_aspect_ratio;
    }

    $grid_image_sizes = fluxus_project_type_option( $project_type_id, 'grid_image_sizes' );
    if ( $grid_image_sizes ) {
        $vars['image_sizes_serialized'] = $grid_image_sizes;
        $vars['image_sizes'] = GridPortfolio::parse_image_sizes( $grid_image_sizes );
    } else {
        $vars['image_sizes_serialized'] = '';
        $vars['image_sizes'] = array();
    }

    return $vars;

}



function fluxus_project_type_grid_size( $project_type_id ) {

    $options = new FluxusProjectTypeOptions( $project_type_id );

    $grid_size = $options->grid_size;

    if ( ! $grid_size ) {
        $project_type = get_term_by( 'id', $project_type_id, 'fluxus-project-type' );

        if ( $project_type->parent ) {
            for ( $i = 0; $i <= 10; $i++ ) {
                $parent = get_term_by( 'id', $project_type->parent, 'fluxus-project-type' );

                $parent_options = new FluxusProjectTypeOptions( $parent->term_id );
                $grid_size = $parent_options->grid_size;

                if ( $grid_size || ( $parent->parent == 0 ) ) {
                    break;
                }
            }
        }
    }

    return $grid_size ? explode( ' ', $grid_size ) : false;

}


function fluxus_project_type_layout( $project_type_id ) {

    $options = new FluxusProjectTypeOptions( $project_type_id );

    $template = $options->layout;

    if ( ! $template ) {
        $project_type = get_term_by( 'id', $project_type_id, 'fluxus-project-type' );

        if ( $project_type->parent ) {
            for ( $i = 0; $i <= 10; $i++ ) {
                $parent = get_term_by( 'id', $project_type->parent, 'fluxus-project-type' );

                $parent_options = new FluxusProjectTypeOptions( $parent->term_id );
                $template = $parent_options->layout;

                if ( $template || ( $parent->parent == 0 ) ) {
                    break;
                }
            }
        }
    }

    switch ( $template ) {

        case 'grid':
            $template = 'template-portfolio-grid.php';
            break;

        case 'horizontal':
            $template = 'template-portfolio.php';
            break;

        default:

            /*
             * Template is not set using Project Type options, so let's determinate it by ourselves
             * using following logic:
             *   1. If page with horizontal portfolio template is found, then use horizontal.
             *   2. If page with grid portfolio template is found, then use grid.
             *   3. If no page is found, then use horizontal.
             */

            $template = 'template-portfolio.php';

            $horizontal_portfolio = it_find_page_by_template( 'template-portfolio.php', array( 'post_status' => 'publish' ) );

            if ( $horizontal_portfolio ) {
                $template = 'template-portfolio.php';
            } else {
                $grid_portfolio = it_find_page_by_template( 'template-portfolio-grid.php', array( 'post_status' => 'publish' ) );

                if ( $grid_portfolio ) {
                    $template = 'template-portfolio-grid.php';
                }
            }

    }

    return $template;

}

/**
 * Project Type Edit Form
 */
function fluxus_project_type_edit_form( $project_type ) {

    $grid_options = new ProjectTypeGridOptions( $project_type->term_id );
    $options = new FluxusProjectTypeOptions( $project_type->term_id );

    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="project-type-layout"><?php _e( 'Layout', 'fluxus' ); ?></label>
        </th>
        <td>
            <?php
                it_select_tag( array(
                    'name' => 'project-type-layout',
                    'id' => 'project-type-layout'
                ), fluxus_portfolio_layouts(), $options->layout );
            ?>
            <br />
            <span class="description"><?php _e( 'Portfolio layout that will be used to display the project type.', 'fluxus' ); ?></span>
        </td>
    </tr>
    <tr id="project-type-grid-portfolio-options">
        <th></th>
        <td>
            <div id="poststuff">
                <div class="postbox">
                    <h3 class="hndle">
                        <span>
                            <?php _e( 'Grid Options', 'fluxus' ); ?>
                        </span>
                    </h3>
                    <div class="inside">
                        <?php echo $grid_options->admin_options_content(); ?>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <?php
        /* This is needed for WPML plugin, it clones the last <tr />,
         * where WPML shows it's UI. Our tr:last has display: none, what also hides WPML's user UI */
    ?>
    <tr>
        <th></th>
        <td></td>
    </tr>
    <?php

}
add_action( 'fluxus-project-type_edit_form_fields', 'fluxus_project_type_edit_form' );


/**
 * Project Type Create Form
 */
function fluxus_project_type_create_form() {
    ?>
    <div class="form-field">
        <label for="project-type-layout"><?php _e( 'Layout', 'fluxus' ); ?></label>
        <select name="project-type-layout" id="project-type-layout" class="postform">
            <?php echo it_array_to_select_options( fluxus_portfolio_layouts() ); ?>
        </select>
        <br>
        <span class="description"><?php _e( 'Portfolio layout that will be used to display the project type.', 'fluxus' ); ?></span>
    </div>
    <div id="project-type-layout-option" class="form-field" style="display: none">
        <label for="project-type-grid-size"><?php _e( 'Grid Size', 'fluxus' ); ?></label>
        <select name="project-type-grid-size" id="project-type-grid-size">
            <?php echo it_array_to_select_options( GridPortfolioAdmin::get_grid_size_options(), GridPortfolio::DEFAULT_GRID_SIZE ); ?>
        </select>
    </div>
    <?php
}
add_action( 'fluxus-project-type_add_form_fields', 'fluxus_project_type_create_form' );


/**
 * Saves project type additional options: layout and grid size.
 */
function fluxus_project_type_update( $term_id ) {

    if ( isset( $_POST['project-type-layout'] ) ) {

        // Since we use the same function for adding/updating terms, check for different nonces.
        if ( isset( $_POST['_wpnonce_add-tag'] ) ) {
            check_admin_referer( 'add-tag', '_wpnonce_add-tag' );
        } else {
            check_admin_referer( 'update-tag_' . $term_id );
        }

        $tax = get_taxonomy( 'fluxus-project-type' );

        if ( current_user_can( $tax->cap->edit_terms ) ) {

            $options = new FluxusProjectTypeOptions( $term_id );

            $options->layout = $_POST['project-type-layout'];

            $whitelist = array(
                'grid_orientation',
                'grid_aspect_ratio',
                'grid_size',
                'grid_image_sizes',
            );

            foreach ( $whitelist as $key ) {
                if ( isset( $_POST['fluxus_portfolio_' . $key] ) ) {
                    $options->$key = stripslashes( $_POST['fluxus_portfolio_' . $key] );
                }
            }

        }
    }

}
add_action( 'edit_fluxus-project-type', 'fluxus_project_type_update' );
add_action( 'create_fluxus-project-type', 'fluxus_project_type_update' );


/**
 * On Project Type term deletion deletes associated options.
 */
function fluxus_project_type_delete( $term_id ) {

    // Same action for bulk and single deletion
    if ( isset( $_POST['delete-tag'] ) ) {
        check_admin_referer( 'delete-tag_' . $term_id );
    } else {
        check_admin_referer( 'bulk-tags' );
    }

    $tax = get_taxonomy( 'fluxus-project-type' );
    if ( current_user_can( $tax->cap->delete_terms ) ) {
        $options = new FluxusProjectTypeOptions( $term_id );
        $options->delete();
    }

}

add_action( 'delete_fluxus-project-type', 'fluxus_project_type_delete' );