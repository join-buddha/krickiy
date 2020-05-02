<?php
/**
 * File contains funcionality used for Portfolio.
 *
 * @since fluxus 1.0
 */

// Load framework classes
require_once dirname( __FILE__ ) . '/class-fluxus-page.php';
require_once dirname( __FILE__ ) . '/class-fluxus-admin-page.php';

require_once dirname( __FILE__ ) . '/class-portfolio-project.php';          // Project
require_once dirname( __FILE__ ) . '/class-portfolio-media.php';            // Project Media
require_once dirname( __FILE__ ) . '/class-grid-portfolio.php';             // Grid Portfolio

// WP Admin Pages
require_once dirname( __FILE__ ) . '/class-portfolio-project-admin.php';    // Project Admin
require_once dirname( __FILE__ ) . '/class-grid-portfolio-admin.php';       // Grid Portfolio Admin

require_once dirname( __FILE__ ) . '/project-type.php';


/**
 * Initialize Portolio
 */
function fluxus_portfolio_init() {

    add_image_size( 'fluxus-portfolio-thumbnail', 90, 90, true );

    /**
     * First we register taxonomy, then custom post type.
     * The order is important, because of rewrite rules.
     */
    $args = array(
        'label' => __( 'Project Types', 'fluxus' ),
        'singular_label' => __( 'Project Type', 'fluxus' ),
        'public' => true,
        'show_tagcloud' => false,
        'hierarchical' => true,
        'rewrite' => false
    );
    register_taxonomy( 'fluxus-project-type', 'fluxus_portfolio',  $args );


    /**
     * Register portfolio_project custom post type.
     */
    $args = array(
        'label' => __( 'Portfolio', 'fluxus' ),
        'labels' => array(
          'singular_label' => __( 'Project', 'fluxus' ),
          'all_items' => __( 'Projects', 'fluxus' ),
        ),
        'public' => true,
        'capability_type' => 'page',
        'rewrite' => false,
        'taxonomy' => 'fluxus-project-type',
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array( 'title', 'editor', 'excerpt', 'page-attributes' )
       );
    register_post_type( 'fluxus_portfolio' , $args );


    /**
     * Register portfolio Project Media File
     */
    $args = array(
        'label' => __(' Project Media', 'fluxus' ),
        'singular_label' => __( 'Project Media File', 'fluxus' ),
        'public' => false,
        'supports' => array( 'title' )
       );
    register_post_type( 'fluxus_portfolio_project_media' , $args );


    /**
     * Permalink structure
     */
    add_rewrite_tag( '%fluxus-project-type%', '([^&/]+)', 'fluxus-project-type=' );
    add_rewrite_tag( '%fluxus_portfolio%', '([^&/]+)', 'fluxus_portfolio=' );

    if ( fluxus_wpml_active() ) {
        $languages = array_keys( icl_get_languages('skip_missing=0') );
        $bases = array();
        foreach ( $languages as $language ) {
            $bases[$language] = fluxus_get_default_portfolio_slug( $language );
        }
        $bases = array_unique( $bases );

        foreach ( $bases as $language => $base ) {
            add_permastruct( 'fluxus-project-type-' . $language, $base . "/%fluxus-project-type%", false );
            add_permastruct( 'fluxus_portfolio-' . $language, $base . '/%fluxus-project-type%/%fluxus_portfolio%', false );
        }
    }

    $base = fluxus_get_default_portfolio_slug();
    add_permastruct( 'fluxus-project-type', $base . '/%fluxus-project-type%', false );
    add_permastruct( 'fluxus_portfolio', $base . '/%fluxus-project-type%/%fluxus_portfolio%', false );
    add_permastruct( 'fluxus-project-type-default', 'portfolio/%fluxus-project-type%', false );
    add_permastruct( 'fluxus_portfolio-default', 'portfolio/%fluxus-project-type%/%fluxus_portfolio%', false );

    if ( isset( $_GET['fluxus-action'] ) && ( $_GET['fluxus-action'] == 'flush' ) && is_admin() ) {
        it_flush_rewrite_rules();
    }

    PortfolioMedia::register( PortfolioMedia::POST_TYPE );

    /**
     * Grid image layout customization.
     */
    if ( isset( $_GET['customize-layout'] ) && is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
        add_action( 'before', 'fluxus_customize_grid_layout_init' );
    }

}
add_action( 'init', 'fluxus_portfolio_init', 1 );


function fluxus_portfolio_pre_get_posts( $query ) {

    if ( ! is_admin() ) {

        if ( ( isset( $query->query_vars['fluxus-project-type'] ) && $query->query_vars['fluxus-project-type'] ) ||
             ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'fluxus_portfolio' ) ) {

            // Disable project title altering for password protected and private projects.
            add_filter( 'protected_title_format', 'fluxus_post_title_formatting' );
            add_filter( 'private_title_format', 'fluxus_post_title_formatting' );

        }

    }

}

add_action( 'pre_get_posts', 'fluxus_portfolio_pre_get_posts' );

/**
 * Initialize Portfolio Admin
 */
function fluxus_portfolio_admin_init() {

    global $pagenow;

    $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

    if ( $post_id = it_get_post_id() ) {
        $post = get_post( $post_id );
        $post_type = $post->post_type;
    }

    if ( $post_type == 'fluxus_portfolio' ) {

        // Project List Page
        if ( 'edit.php' == $pagenow ) {

            // Set correct order of projects in admin.
            add_filter( 'pre_get_posts', 'fluxus_portfolio_admin_project_order' );

            // Custom columns in Project List
            add_filter( 'manage_edit-fluxus_portfolio_columns', 'fluxus_portfolio_admin_project_list_columns' );
            add_action( 'manage_posts_custom_column', 'fluxus_portfolio_project_list_column_data' );

        }

        // Post Edit or Post New Page
        if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin-ajax.php' ) ) ) {
            new PortfolioProjectAdmin( $post_id );
        }

    }

    if ( $post_id ) {

        if ( it_is_template( $post_id, 'template-portfolio-grid.php' ) ) {
            new GridPortfolioAdmin( $post_id );
        }

    }

}
add_action( 'admin_init', 'fluxus_portfolio_admin_init' );


/**
 * Set correct order of projects in admin.
 */
function fluxus_portfolio_admin_project_order( $wp_query ) {
    if ( $wp_query->query['post_type'] == 'fluxus_portfolio' ) {
        $wp_query->set( 'orderby', 'menu_order ID' );
        $wp_query->set( 'order', 'ASC DESC' );
    }
}


/**
 * Add additional columns in project list table.
 */
function fluxus_portfolio_admin_project_list_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => 'Project',
        'description' => 'Description',
        'link' => 'Link',
        'type' => 'Type of Project',
    );

    return $columns;

}


/**
 * Populate added columns with data.
 */
function fluxus_portfolio_project_list_column_data( $column ) {
    global $post;

    $project = new PortfolioProject( $post->ID );

    switch ( $column ) {
        case 'description':
            the_excerpt();
        break;

        case 'link':
            echo $project->meta_link;
        break;

        case 'type':
            echo get_the_term_list( $post->ID, 'fluxus-project-type', '', ', ', '' );
        break;
    }

}


/**
 * Query portfolio items.
 */
function fluxus_query_portfolio( $args = array() ) {

    add_filter( 'posts_orderby_request', 'fluxus_portfolio_orderby_filter' );

    $defaults = array(
            'post_type'          => 'fluxus_portfolio',
            'posts_per_page'     => -1,
            'orderby'            => 'menu_order ID',
            'post_status'        => 'publish',
            'order'              => 'ASC DESC'
        );

    $args = array_merge( $defaults, $args );

    $result = query_posts( apply_filters( 'fluxus_query_portfolio_args', $args ) );

    remove_filter( 'posts_orderby_request', 'fluxus_portfolio_orderby_filter' );

    return $result;

}


/**
 * Orders project by menu_order ASC and ID desc.
 */
function fluxus_portfolio_orderby_filter( $orderby ) {

    /**
     * Limit the use for a very specific case.
     */
    if ( 'wp_posts.menu_order,wp_posts.ID DESC' == $orderby ) {
        return 'wp_posts.menu_order ASC, wp_posts.ID DESC';
    }

    return $orderby;

}


/**
 * Returns next project according to the specified order.
 */
function fluxus_portfolio_get_next_project( $current_project ) {
    return fluxus_portfolio_get_adjacent_project( $current_project, 'next' );
}


/**
 * Returns previous project according to the specified order.
 */
function fluxus_portfolio_get_previous_project( $current_project ) {
    return fluxus_portfolio_get_adjacent_project( $current_project, 'previous' );
}


/**
 * Get next/previous project while ordering by menu_order DESC and id DESC.
 * That is newer items with same menu_order goes first.
 */
function fluxus_portfolio_get_adjacent_project( $current_project, $sibling = 'next' ) {
    global $wpdb;

    if ( !is_object($current_project) ) {
        return false;
    }

    $compare_id = 'next' === $sibling ? '<' : '>';

    /**
     * Select next post with the same menu_order but lower ID.
     */
    $where = $wpdb->prepare("WHERE
                                p.id $compare_id %d AND
                                p.menu_order = %d AND
                                p.post_type = 'fluxus_portfolio' AND
                                p.post_status = 'publish'",
                            $current_project->ID, $current_project->menu_order );

    if ( 'next' === $sibling ) {
        $sort  = "ORDER BY p.id DESC LIMIT 1";
    } else {
        $sort  = "ORDER BY p.id ASC LIMIT 1";
    }

    $query = "SELECT p.* FROM $wpdb->posts AS p $where $sort";

    $result = $wpdb->get_row( $query );

    if ( null === $result ) {

        /**
         * No project with the same menu order found. Now select
         * a project with a lower menu order.
         */

        if ( 'next' === $sibling ) {
            $sort  = "ORDER BY p.menu_order ASC, p.id DESC LIMIT 1";
            $compare_menu_order = '>';
        } else {
            $sort  = "ORDER BY p.menu_order DESC, p.id ASC LIMIT 1";
            $compare_menu_order = '<';
        }

        $where = $wpdb->prepare("WHERE
                                p.menu_order $compare_menu_order %d AND
                                p.post_type = 'fluxus_portfolio' AND
                                p.post_status = 'publish'",
                            $current_project->menu_order );

        $query = "SELECT p.* FROM $wpdb->posts AS p $where $sort";

        $result = $wpdb->get_row( $query );

    }

    return $result;

}


function fluxus_wpml_active() {
    global $sitepress;
    return defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE && $sitepress;
}


function fluxus_wpml_get_element_language( $id, $element_type ) {
    global $sitepress;
    return $sitepress->get_language_for_element( $id, $element_type );
}


function fluxus_get_save_post_cache_key( $cache_key ) {
    $namespace = wp_cache_get( 'fluxus_save_post_cache_key' );
    if ( $namespace === false ) {
        $namespace = 1;
        wp_cache_set( 'fluxus_save_post_cache_key', $namespace );
    }

    return $cache_key . ':' . $namespace;
}


function fluxus_clear_save_post_cache( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }
    it_flush_rewrite_rules();
    wp_cache_incr( 'fluxus_save_post_cache_key' );
}
add_action( 'save_post', 'fluxus_clear_save_post_cache' );


function fluxus_compare_by_id($page_1, $page_2) {
    return $page_1->ID > $page_2->ID;
}


/**
 * Returns a default portfolio page, which should be used when generating URLs.
 * If WPML plugin is used, then it will automatically return a page for current language.
 *
 * @param string $language Return default portfolio page for a specific language.
 * @return mixed returns page object or FALSE if a page couldn't be found.
 */
function fluxus_get_default_portfolio_page( $language = '' ) {
    $cache_key = fluxus_get_save_post_cache_key( 'portfolio-base-' . $language );
    $found = false;
    $cached_data = wp_cache_get( $cache_key, 'fluxus', false, $found );

    if ( $found ) {
        return $cached_data;
    }

    $horizontal_portfolios = it_find_page_by_template( 'template-portfolio.php' );
    $horizontal_portfolios = empty( $horizontal_portfolios ) ? array() : $horizontal_portfolios;

    $grid_portfolios = it_find_page_by_template( 'template-portfolio-grid.php' );
    $grid_portfolios = empty( $grid_portfolios ) ? array() : $grid_portfolios;

    $portfolios = array_merge( $horizontal_portfolios, $grid_portfolios );
    usort( $portfolios, 'fluxus_compare_by_id' );

    $result = false;

    if ( fluxus_wpml_active() && $language ) {
        foreach ( $portfolios as $portfolio ) {
            if ( $language == fluxus_wpml_get_element_language( $portfolio->ID, 'post_page' ) ) {
                $result = $portfolio;
                break;
            }
        }
    } else {
        if ( ! empty( $portfolios ) ) {
            $result = $portfolios[0];
        }
    }

    wp_cache_set( $cache_key, $result, 'fluxus' );

    return $result;
}


function fluxus_get_default_portfolio_permalink() {
    if ( fluxus_wpml_active() ) {
        $language = ICL_LANGUAGE_CODE;
    } else {
        $language = '';
    }

    $portfolio_page = fluxus_get_default_portfolio_page( $language );

    if ( $portfolio_page ) {
        $link = get_permalink( $portfolio_page->ID );
    } else {
        $link = '';
    }

    return $link;
}


/**
 * Returns slug of a page that has a 'Horizontal Portfolio' template assigned.
 * If no such page can be found, then 'portfolio' is returned.
 */
function fluxus_get_default_portfolio_slug( $language = '' ) {
    $portfolio_page = fluxus_get_default_portfolio_page( $language );

    if ( $portfolio_page ) {
        $slug = $portfolio_page->post_name;
    } else {
        $slug = 'portfolio';
    }

    return apply_filters( 'fluxus_portfolio_base_slug', $slug );
}


/**
 * Generate correct project type links.
 */
function fluxus_project_type_permalink( $termlink, $term, $taxonomy ) {
    /**
     * Don't replace anything if it's not a fluxus-project-type taxonomy,
     * if there was an error or we are not using fancy links.
     */
    if ( is_wp_error( $term ) || 'fluxus-project-type' != $term->taxonomy || empty( $termlink ) ) {
        return $termlink;
    }

    if ( fluxus_wpml_active() ) {
        $project_type_language = fluxus_wpml_get_element_language( $term->term_id, 'tax_fluxus-project-type' );
        $default_base = fluxus_get_default_portfolio_slug();
        $correct_base = fluxus_get_default_portfolio_slug( $project_type_language );
        $termlink = str_replace( $default_base . '/' . $term->slug, $correct_base . '/' . $term->slug, $termlink );
    }

    return $termlink;
}
add_filter( 'term_link', 'fluxus_project_type_permalink', 10, 3 );


/**
 * Generate correct portfolio links.
 */
function fluxus_portfolio_permalink( $permalink, $post, $leavename ) {
    /**
     * If there's an error with post, or this is not fluxus_portfolio
     * or we are not using fancy links.
     */
    if ( is_wp_error( $post ) || 'fluxus_portfolio' != $post->post_type || empty( $permalink ) ) {
        return $permalink;
    }

    /**
     * Find out project type.
     */
    $project_type = '';

    if ( strpos( $permalink, '%fluxus-project-type%') !== false ) {

        $terms = get_the_terms( $post->ID, 'fluxus-project-type' );

        if ( $terms ) {
            // sort terms by ID.
            usort( $terms, '_usort_terms_by_ID' );
            $project_type = $terms[0]->slug;
        } else {
            $project_type = 'uncategorized';
        }

    }

    $rewrite_codes = array(
        '%fluxus-project-type%',
        $leavename ? '' : '%fluxus_portfolio%'
    );

    if ( fluxus_wpml_active() ) {
        $project_language = fluxus_wpml_get_element_language( $post->ID, 'post_fluxus_portfolio' );
        $default_base = fluxus_get_default_portfolio_slug();
        $correct_base = fluxus_get_default_portfolio_slug( $project_language );
        $permalink = str_replace( $default_base . '/%fluxus-project-type%/', $correct_base . '/%fluxus-project-type%/', $permalink );
    }

    $rewrite_replace = array(
        $project_type,
        $post->post_name
    );

    $permalink = str_replace( $rewrite_codes, $rewrite_replace, $permalink );

    return $permalink;

}
add_filter( 'post_type_link', 'fluxus_portfolio_permalink' , 10, 3 );


/**
 * Exclude password protected posts.
 */
function fluxus_portfolio_posts_where( $where, $query ) {

    global $wpdb;

    if ( ! is_admin() ) {

        $vars = $query->query_vars;

        $post_type_portfolio = isset( $vars['post_type'] ) && ( $vars['post_type'] == 'fluxus_portfolio' );
        $is_portfolio_single = isset( $vars['fluxus_portfolio'] ) && ( $vars['fluxus_portfolio'] != '' );
        $project_type_set = isset( $vars['fluxus-project-type'] ) && $vars['fluxus-project-type'];

        // Identify pages that lists portfolios
        if ( ( $post_type_portfolio && !$is_portfolio_single ) || // All pages querying fluxus_portfolio except single
             $project_type_set ) { // Project types pages

            // Remove password protected projects from portfolio lists if needed.
            if ( of_get_option( 'fluxus_hide_password_protected_projects' ) ) {
                $where .= " AND {$wpdb->posts}.post_password = '' ";
            }
        }

    }

    return $where;

}

add_filter( 'posts_where', 'fluxus_portfolio_posts_where', 10, 2 );


/**
 * Removes "Protected: %s" title formatting.
 */
function fluxus_post_title_formatting( $title ) {
    return '%s';
}


/**
 * Include required scripts for grid layout customization.
 */
function fluxus_customize_grid_layout_init() {

    wp_enqueue_script( 'fluxus-wp-admin-grid-image-sizes', get_template_directory_uri() . '/js/wp-admin/grid-image-sizes.js', array('jquery', 'json2') );
    wp_enqueue_style( 'fluxus-wp-admin-grid-image-sizes', get_template_directory_uri() . '/css/wp-admin/grid-image-sizes.css' );

    $wp_vars = array(
      'clickToChangeSize' => __( 'click to change size', 'fluxus' )
    );
    wp_localize_script( 'fluxus-wp-admin-grid-image-sizes', 'wpVars', $wp_vars );

    ?>
    <div class="fluxus-customize-note">
        <p>
            <?php _e( 'Click on any image to change it\'s size.', 'fluxus' ); ?>
        </p>
        <a href="#" class="btn-cancel"><?php _e( 'Cancel', 'fluxus'); ?></a>
        <a href="#" class="btn-save"><?php _e( 'Done', 'fluxus'); ?></a>
    </div><?php

}
