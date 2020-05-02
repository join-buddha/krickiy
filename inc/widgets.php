<?php
/**
 * Widgets. Contains functionality related to theme's widgets.
 *
 * @since fluxus 1.0
 */


/**
 * Include custom widgets.
 */
require_once FLUXUS_WIDGETS_DIR . '/widget-project-types.php';


/**
 * Returns theme's default widget params.
 *
 * @since fluxus 1.0
 */
function fluxus_get_default_widget_params() {
    return array(
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<div class="decoration"></div><h1 class="widget-title">',
            'after_title' => '</h1>'
        );
}


/**
 * Register widgetized area and update sidebar with default widgets.
 *
 * @since fluxus 1.0
 */
function fluxus_widgets_init() {

    /**
     * Register sidebars.
     */

    $sidebar_main = array_merge(
            array( 'name' => __( 'General Sidebar', 'fluxus' ), 'id' => 'sidebar-main' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_main );

    $sidebar_post = array_merge(
            array( 'name' => __( 'Blog Sidebar', 'fluxus' ), 'id' => 'sidebar-blog' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_post );


    $sidebar_post = array_merge(
            array( 'name' => __( 'Blog Post Sidebar', 'fluxus' ), 'id' => 'sidebar-post' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_post );


    $sidebar_portfolio = array_merge(
            array( 'name' => __( 'Portfolio Sidebar', 'fluxus' ), 'id' => 'sidebar-portfolio' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_portfolio );


    $sidebar_portfolio_single = array_merge(
            array( 'name' => __( 'Portfolio Project Sidebar', 'fluxus' ), 'id' => 'sidebar-portfolio-single' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_portfolio_single );


    // Removes the default styles that are packaged with the Recent Comments widget.
    add_filter( 'show_recent_comments_widget_style', '__return_false' );

}
add_action( 'widgets_init', 'fluxus_widgets_init' );


/**
 *  Adds a widget specified by ID to one of the registered sidebars.
 */
function intheme_add_widget_to_sidebar( $widget_id, $widget_args, $widget_sidebar_id ) {

    $sidebars_widgets = get_option( 'sidebars_widgets', array() );

    /**
     * Count widget instances.
     */
    $existing_count = 0;

    if ( $sidebars_widgets ) {

        // Walk through all sidebars and widget sets
        foreach ( $sidebars_widgets as $sidebar_id => $sidebar_widgets ) {

            // Exclude helpers
            if ( ! in_array( $sidebar_id, array( 'wp_inactive_widgets', 'array_version' ) ) && is_array( $sidebar_widgets ) ) {

                // Walk through widgets
                foreach ( $sidebar_widgets as $widget ) {

                    // Get widget name and count
                    $parsed_widget_name = explode( '-' , $widget );

                    if ( count( $parsed_widget_name ) > 1 ) {
                        $parsed_count = $parsed_widget_name[count($parsed_widget_name) - 1];
                        unset( $parsed_widget_name[count( $parsed_widget_name ) - 1] );
                        $parsed_name = implode( '-', $parsed_widget_name );

                        if ( ( $parsed_name == $widget_id ) && ( $parsed_count > $existing_count ) ) {
                            $existing_count = $parsed_count;
                        }
                    }
                }
            }
        }
    }

    $instance_count = $existing_count + 1;

    // Add to Sidebar
    $sidebars_widgets[ $widget_sidebar_id ][] = $widget_id . '-' . $instance_count;
    update_option( 'sidebars_widgets', $sidebars_widgets );

    // Apply arguments
    $widget_contents = get_option( 'widget_' . $widget_id, array() );
    $widget_contents[$instance_count] = $widget_args;
    update_option( 'widget_' . $widget_id, $widget_contents );

}

