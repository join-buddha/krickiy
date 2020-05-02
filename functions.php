<?php

/**
 * Fluxus functions and definitions.
 *
 * @package fluxus
 * @since fluxus 1.0
 */


/**
 * Define common constants.
 */
define( 'FLUXUS_URI',         get_template_directory_uri() );
define( 'FLUXUS_CSS_URI',     FLUXUS_URI . '/css' );
define( 'FLUXUS_JS_URI',      FLUXUS_URI . '/js' );
define( 'FLUXUS_IMAGES_URI',  FLUXUS_URI . '/images' );

define( 'FLUXUS_DIR',         get_template_directory() );
define( 'FLUXUS_LIB_DIR',     FLUXUS_DIR . '/lib' );
define( 'FLUXUS_INC_DIR',     FLUXUS_DIR . '/inc' );
define( 'FLUXUS_CSS_DIR',     FLUXUS_DIR . '/css' );
define( 'FLUXUS_JS_DIR',      FLUXUS_DIR . '/js' );
define( 'FLUXUS_WIDGETS_DIR', FLUXUS_INC_DIR . '/widgets' );


/**
 * Require various files.
 */
require_once FLUXUS_LIB_DIR . '/intheme-utils.php';                       // File contains a bunch of useful utilities.
require_once FLUXUS_LIB_DIR . '/intheme-menu-walker.php';                 // Custom menu walker.
require_once FLUXUS_LIB_DIR . '/appreciate.php';                          // Appreciate Post functionality.
require_once FLUXUS_INC_DIR . '/fluxus-theme.php';
require_once FLUXUS_INC_DIR . '/slider.php';                              // Full page slider functionality.
require_once FLUXUS_INC_DIR . '/portfolio/portfolio.php';                 // Portfolio functionality.
require_once FLUXUS_INC_DIR . '/contacts.php';                            // Contacts page functionality.
require_once FLUXUS_INC_DIR . '/background.php';                          // Page with background image functionality.
require_once FLUXUS_INC_DIR . '/template-tags.php';                       // Custom template tags for this theme.
require_once FLUXUS_INC_DIR . '/tweaks.php';                              // Various functionality tweaks.
require_once FLUXUS_INC_DIR . '/shortcodes.php';                          // Shortcodes.
require_once FLUXUS_INC_DIR . '/post-formats.php';                        // Post formats.
require_once FLUXUS_INC_DIR . '/widgets.php';                             // Widgets.
require_once FLUXUS_INC_DIR . '/plugin-support.php';                      // 3rd party plugin support.
require_once FLUXUS_INC_DIR . '/customize/class-fluxus-customize.php';    // Customize.
require_once FLUXUS_INC_DIR . '/upgrade.php';                             // All things regarding theme version update.

if ( is_admin() ) {
  require_once FLUXUS_LIB_DIR . '/class-tgm-plugin-activation.php';
}

/**
 * Initialize.
 */
function fluxus_init() {

  Fluxus_Customize::init();

  /**
   * Note, if you are changing the existing size dimensions,
   * then Wordpress will not automatically regenerate all the images.
   *
   * To do so, you could try using it_regenerate_wp_images() function.
   * Put it inside your admin_init hook, and visit admin section.
   * After waiting for usually a long time, all the images will be
   * available in a newly specified size.
   *
   * Custom image sizes:
   */

  add_image_size( 'fluxus-thumbnail', 583, 328, true );                // Used on: horizontal blog
  add_image_size( 'fluxus-thumbnail-2x', 1166, 656, true );
  add_image_size( 'fluxus-thumbnail-uncropped', 583, 328, false );     // Used on: vertical blog
  add_image_size( 'fluxus-thumbnail-uncropped-2x', 1166, 656, false );
  add_image_size( 'fluxus-gallery-thumbnail', 500, 500, true );        // Used on: content gallery
  add_image_size( 'fluxus-max', 1920, 1280, false );                   // Maximum image size displayed on site.

  add_filter( 'show_admin_bar' , '__return_false' );                  // Remove admin bar for everyone

}
add_action( 'init', 'fluxus_init', 1 );


/**
 * Initialize Admin.
 */
function fluxus_admin_init() {

  /**
   * General scripts and styles for admin area.
   */
  wp_enqueue_script( 'fluxus-wp-admin', FLUXUS_JS_URI . '/wp-admin/admin.js' );
  wp_enqueue_style( 'fluxus-wp-admin', FLUXUS_CSS_URI . '/wp-admin/admin.css' );

  add_editor_style( 'css/wp-admin/editor-styles.css' );

  global $wp_file_descriptions;
  $wp_file_descriptions['user.php'] = __( 'Use to add custom PHP code', 'fluxus' );
  $wp_file_descriptions['user.css'] = __( 'Use to add custom CSS code', 'fluxus' );

}
add_action( 'admin_init', 'fluxus_admin_init', 1 );



/**
 * Setup plugins.
 */
function fluxus_required_plugins() {

    $plugins = array(
      array(
        'name'      => 'Contact Form 7',
        'slug'      => 'contact-form-7',
        'required'  => false
      )
    );

    $config = array(
      'domain'            => 'fluxus',
      'parent_menu_slug'  => 'plugins.php',
      'parent_url_slug'   => 'plugins.php',
      'menu'              => 'install-required-plugins',
      'has_notices'       => true,
      'is_automatic'      => false,            // Automatically activate plugins after installation or not
      'message'           => '',               // Message to output right before the plugins table
      'strings'           => array(
        'menu_title' => __( 'Install Fluxus Compatible Plugins', 'fluxus' )
      )
    );

    tgmpa( $plugins, $config );

}
add_action( 'tgmpa_register', 'fluxus_required_plugins' );



/**
 * Specify the maximum content width.
 * This is based on CSS, when screen becomes big enough the content
 * area becomes fixed, so there is no need to have bigger images.
 */
if ( ! isset( $content_width ) ) {
  $content_width = 1021;
}


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since fluxus 1.0
 */
function fluxus_setup() {

  /**
   * Make theme available for translation
   * Translations can be filed in the /languages/ directory
   */
  load_theme_textdomain( 'fluxus', FLUXUS_DIR . '/languages' );

  /**
   * Enable theme support for standard features.
   */
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'automatic-feed-links' );

  /**
   * Register menus.
   */
  register_nav_menus( array(
    'header_primary'  => __( 'Header Primary Menu', 'fluxus' ),
    'header_secondary'  => __( 'Header Secondary Menu', 'fluxus' ),
    'footer_primary'  => __( 'Footer Primary Menu', 'fluxus' )
  ) );


  /**
   * Enable shortcodes for widgets.
   */
  add_filter( 'widget_text', 'do_shortcode' );


  /**
   * Initialize theme options.
   */
  require_once FLUXUS_INC_DIR . '/options.php';

  if ( !function_exists( 'optionsframework_init' ) ) {
    define( 'OPTIONS_FRAMEWORK_DIRECTORY', FLUXUS_URI . '/options-framework/' );
    require_once dirname(__FILE__) . '/options-framework/options-framework.php';
  }

}
add_action( 'after_setup_theme', 'fluxus_setup' );


/**
 * Enqueue scripts and styles.
 */
function fluxus_scripts_and_styles() {

  /**
   * CSS
   */
  wp_enqueue_style( 'fluxus-global',     FLUXUS_CSS_URI . '/global.css' );                         // Global CSS, should consist of tags only
  wp_enqueue_style( 'fluxus-grid',       FLUXUS_CSS_URI . '/grid.css' );                           // Fluid grid used in content columns
  wp_enqueue_style( 'fontello-icons',    FLUXUS_CSS_URI . '/fontello.css' );                       // Fontello font icon, use fonts/config.json to add more
  wp_enqueue_style( 'style',             get_stylesheet_uri() );                                   // Main stylesheet
  wp_enqueue_style( 'fluxus-responsive', FLUXUS_CSS_URI . '/responsive.css' );                     // Responsive rules

  if ( get_theme_mod( 'skin' ) ) {
    wp_enqueue_style( 'fluxus-skin',       FLUXUS_CSS_URI . '/skins/' . get_theme_mod( 'skin' ) ); // Skin
  }
  Fluxus_Customize::enqueue_style();

  // $color_css = of_get_option( 'fluxus_stylesheet' );
  // if ( $color_css ) {
  //   wp_enqueue_style( 'fluxus-skin', FLUXUS_CSS_URI . '/skins/' . (string) $color_css ); // Skin CSS file.
  // }

  if ( file_exists( FLUXUS_CSS_DIR . '/user.css' ) ) {
    // css/user.css file is depreciated. Use user.css file located in theme's root folder.
    // Having user.css file in root folder allows it to be edited via Appearance > Editor
    wp_enqueue_style( 'fluxus-user-depreciated', FLUXUS_CSS_URI . '/user.css' );
  }

  wp_enqueue_style( 'fluxus-user', FLUXUS_URI . '/user.css' ); // Custom CSS rules.

  /**
   * JS
   */
  wp_enqueue_script( 'underscore' );
  wp_enqueue_script( 'tinyscrollbar',   FLUXUS_JS_URI . '/jquery.tinyscrollbar.js', array( 'jquery' ), false, true ); // Scrollbar plugin
  wp_enqueue_script( 'sharrre',         FLUXUS_JS_URI . '/jquery.sharrre.js', array( 'jquery' ), false, true ); // Share count plugin
  wp_enqueue_script( 'jquery-transit',  FLUXUS_JS_URI . '/jquery.transit.js', array( 'jquery' ), false, true );       // CSS3 transition plugin
  wp_enqueue_script( 'fluxus-utils',    FLUXUS_JS_URI . '/utils.js', array( 'jquery' ), false, true );                // Other tiny plugins
  wp_enqueue_script( 'fluxus-grid',     FLUXUS_JS_URI . '/jquery.fluxus-grid.js', array( 'jquery' ), false, true );   // Grid portfolio layout plugin
  wp_enqueue_script( 'jquery-reveal',   FLUXUS_JS_URI . '/jquery.reveal.js', array( 'jquery' ), false, true );        // Modal box plugin
  wp_enqueue_script( 'fluxus-lightbox', FLUXUS_JS_URI . '/jquery.fluxus-lightbox.js', array( 'jquery', 'jquery-transit' ), false, true ); // Lightbox plugin
  wp_enqueue_script( 'iscroll',         FLUXUS_JS_URI . '/iscroll.js', array(), false, true );                        // iScroll 4
  wp_enqueue_script( 'fluxus-slider',   FLUXUS_JS_URI . '/jquery.fluxus-slider.js', array( 'jquery', 'jquery-transit', 'iscroll', 'underscore' ), false, true ); // Full Page Slider plugin
  wp_enqueue_script( 'fluxus',          FLUXUS_JS_URI . '/main.js', array( 'jquery', 'fluxus-utils', 'underscore' ), false, true ); // Main script
  wp_enqueue_script( 'fluxus-user',     FLUXUS_JS_URI . '/user.js', array( 'jquery', 'fluxus-utils' ), false, true ); // User custom javascript

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' ); // WP standard comment reply script
  }

}
add_action( 'wp_enqueue_scripts', 'fluxus_scripts_and_styles' );

include_once FLUXUS_INC_DIR . '/user.php'; // User modifications.
