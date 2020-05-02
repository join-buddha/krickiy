<?php

/**
 * Contains methods for theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since Fluxus 1.3
 */
class Fluxus_Customize {

  const CSS_FILENAME = 'fluxus-customize.css';
  static public $css_file_path = '';
  static public $css_file_url = '';


  static public function init() {

    $uploads = wp_upload_dir();
    self::$css_file_path = $uploads['basedir'] . '/' . self::CSS_FILENAME;
    self::$css_file_url = $uploads['baseurl'] . '/' . self::CSS_FILENAME;

    add_action( 'customize_register', array( 'Fluxus_Customize' , 'register' ) );
    add_action( 'customize_save_after', array( 'Fluxus_Customize', 'after_save' ) );
    add_action( 'customize_controls_print_footer_scripts', array( 'Fluxus_Customize', 'footer_scripts' ) );

  }


  /**
   * Checks if destination CSS file is writable.
   */
  public static function check_permissions() {
    $uploads = wp_upload_dir();
    return ( file_exists( self::$css_file_path ) && is_writable( self::$css_file_path ) ) ||
           ( ! file_exists( self::$css_file_path ) && is_writable( $uploads['basedir'] ) );
  }


  /**
   * Used to output error message, when destination CSS file is not writable.
   */
  public static function footer_scripts() {
    if ( ! self::check_permissions() ) {
      $error_messages = array(
        'badPermissions' => "Warning. File '" . self::$css_file_path . "' is not writable. Color customization settings will have no effect."
      );
      wp_enqueue_script( 'customizer-permission-error', FLUXUS_JS_URI . '/wp-admin/customizer-permission-error.js' );
      wp_localize_script( 'customizer-permission-error', 'errors', $error_messages );
    }
  }

  /**
   * This hooks into 'customize_register' (available as of WP 3.4) and allows
   * you to add new sections and controls to the Theme Customize screen.
   *
   * Note: To enable instant preview, we have to actually write a bit of custom
   * javascript. See live_preview() for more.
   *
   * @see add_action('customize_register',$func)
   * @param \WP_Customize_Manager $wp_customize
   * @since Fluxus 1.3
   */
  public static function register( $wp_customize ) {
    require dirname( __FILE__ ) . '/class-fluxus-customize-image-control.php';

    // Enqueue live preview javascript in Theme Customizer admin screen
    add_action( 'customize_preview_init' , array( 'Fluxus_Customize' , 'live_preview' ) );


    /**
     * ----------------------------------------------------
     * 'Logos' section.
     * ----------------------------------------------------
     */

    $wp_customize->add_section( 'logos',
      array(
        'title'       => __( 'Logos', 'fluxus' ),
        'capability'  => 'edit_theme_options',
        'priority'    => 30
      )
    );

    /**
     * 'Logos' section's settings.
     */

    $wp_customize->add_setting( 'logo',
      array(
        'default' => '',
        'type' => 'theme_mod',
        'transport' => 'postMessage',
      )
    );

    $wp_customize->add_setting( 'logo_retina',
      array(
        'default' => '',
        'type' => 'theme_mod',
        'transport' => 'postMessage',
      )
    );

    $wp_customize->add_setting( 'favicon',
      array(
        'default' => '',
        'type' => 'theme_mod',
        'transport' => 'postMessage',
      )
    );

    $wp_customize->add_setting( 'facebook_icon',
      array(
        'default' => '',
        'type' => 'theme_mod',
        'transport' => 'postMessage',
      )
    );


    /**
     * 'Logos' section's controls.
     */

    $wp_customize->add_control(
      new Fluxus_Customize_Image_Control(
        $wp_customize,
       'fluxus_control_logo',
        array(
          'label'       => __( 'Logo', 'fluxus' ),
          'description' => __( 'Maximum width is 190px. Recommended height is under 40px.', 'fluxus' ),
          'section'     => 'logos',
          'settings'    => 'logo',
          'priority'    => 5
        )
      )
    );

    $wp_customize->add_control(
      new Fluxus_Customize_Image_Control(
        $wp_customize,
       'fluxus_control_logo_retina',
        array(
          'label'       => __( 'Retina Logo', 'fluxus' ),
          'description' => __( 'A high definition version of your logo. It should be 2 times bigger. It will be displayed on high resolution devices (eg. iPad, iPhone).', 'fluxus' ),
          'section'     => 'logos',
          'settings'    => 'logo_retina',
          'priority'    => 6
        )
      )
    );

    $wp_customize->add_control(
      new Fluxus_Customize_Image_Control(
        $wp_customize,
       'fluxus_control_favicon',
        array(
          'label'       => __( 'Favicon', 'fluxus' ),
          'description' => __( 'Upload a 32x32 sized PNG or GIF image that will be used as a favicon.', 'fluxus' ),
          'section'     => 'logos',
          'settings'    => 'favicon',
          'priority'    => 6
        )
      )
    );

    $wp_customize->add_control(
      new Fluxus_Customize_Image_Control(
        $wp_customize,
       'fluxus_control_facebook',
        array(
          'label'       => __( 'Facebook Icon', 'fluxus' ),
          'description' => __( 'Image used on Facebook timeline when someone likes the website. If visitor likes a content page (blog post / gallery) then image will be taken automatically from content. Should be at least 200x200 in size (preferably 1500x1500).', 'fluxus' ),
          'section'     => 'logos',
          'settings'    => 'facebook_icon',
          'priority'    => 6
        )
      )
    );


    /**
     * ----------------------------------------------------
     * 'Colors' section.
     * ----------------------------------------------------
     */

    $wp_customize->add_section( 'colors',
      array(
        'title'       => __( 'Colors', 'fluxus' ),
        'capability'  => 'edit_theme_options',
        'priority'    => 35,
        'description' => __( 'Choose a predefined skin or tune each color one by one.', 'fluxus' )
      )
    );

    /**
     * 'Colors' section's settings.
     */

    $wp_customize->add_setting( 'skin',
      array(
        'default' => 'light.css',
        'type' => 'theme_mod',
        'transport' => 'postMessage',
      )
    );

    $wp_customize->add_setting( 'css_background_color',
      array(
        'default'   => '#ffffff',
        'type'      => 'theme_mod',
        'transport' => 'postMessage',
       )
    );

    $wp_customize->add_setting( 'css_accent_color',
      array(
        'default'   => '#FFF200',
        'type'      => 'theme_mod',   // Is this an 'option' or a 'theme_mod'?
        'transport' => 'postMessage', // What triggers a refresh of the setting? 'refresh' or 'postMessage'?
       )
    );

    $wp_customize->add_setting( 'css_accent_alt_color',
      array(
        'default'   => '#111111',
        'type'      => 'theme_mod',   // Is this an 'option' or a 'theme_mod'?
        'transport' => 'postMessage', // What triggers a refresh of the setting? 'refresh' or 'postMessage'?
       )
    );

    /**
     * 'Colors' section's controls.
     */

    // Select Skin
    $wp_customize->add_control( new WP_Customize_Control(
      $wp_customize,          // Pass the $wp_customize object (required)
      'fluxus_control_skin',  // Set a unique ID for the control
      array(
        'label' => __( 'Skin', 'fluxus' ), // Admin-visible name of the control
        'type' => 'select',
        'choices' => array(
          'light.css' => 'Light (default)',
          'dark.css'  => 'Dark'
        ),
        'section' => 'colors',         // ID of the section this control should render in (can be one of yours, or a WordPress default section)
        'settings' => 'skin',          // Which setting to load and manipulate (serialized is okay)
        'priority' => 5
      )
    ));

    // Accent Color
    $wp_customize->add_control( new WP_Customize_Color_Control(
      $wp_customize,
      'fluxus_control_css_accent_color',
      array(
        'label' => __( 'Accent Color', 'fluxus' ),
        'section' => 'colors',
        'settings' => 'css_accent_color'
      )
    ));

    // Alt Accent Color
    $wp_customize->add_control( new WP_Customize_Color_Control(
      $wp_customize,
      'fluxus_control_css_accent_alt_color',
      array(
        'label' => __( 'Alternative Accent Color (contrasting)', 'fluxus' ),
        'section' => 'colors',
        'settings' => 'css_accent_alt_color'
      )
    ));


    /**
     * Modify built-in settings.
     */
    $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

  }


  /**
   * Compiles customize.less file into CSS output using theme's CSS related customizations.
   */
  public static function theme_mods_to_css() {

    require_once( FLUXUS_LIB_DIR . '/lessc.inc.php' );

    $less = new lessc;
    $less_vars = array();
    $mods = get_theme_mods();

    foreach ( $mods as $id => $value ) {

      if ( 'css_' === substr( $id, 0, 4) ) {
        $less_vars[$id] = $value;
      }

    }

    $less->setVariables( $less_vars );

    return $less->compileFile( FLUXUS_CSS_DIR . '/less/customize.less' );

  }


  /**
   * Enqueues a script responsible for live change previewing.
   *
   * @see add_action('customize_preview_init',$func)
   * @since Fluxus 1.3
   */
  public static function live_preview() {

    wp_enqueue_script( 'css-parser', FLUXUS_JS_URI . '/wp-admin/cssParser.js' );

    wp_enqueue_script(
      'fluxus-customizer-preview',
      FLUXUS_JS_URI . '/wp-admin/customizer-live-preview.js',
      array( 'jquery', 'customize-preview' ),
      '',
      true
    );

    $wp_vars = array(
      'templateUrl' => FLUXUS_CSS_URI . '/skins',
      'cssTemplate' => file_get_contents( FLUXUS_CSS_DIR . '/less/customize.less' )
    );
    wp_localize_script( 'fluxus-customizer-preview', 'customizerVars', $wp_vars );

  }


  /**
   * Actions to execute, when customizations settings were saved.
   */
  public static function after_save( $wp_customize ) {

    // Delete logo size cache.
    delete_transient( 'fluxus_option_fluxus_logo' );
    delete_transient( 'fluxus_option_fluxus_logo_retina' );

    // Generate a CSS file.
    if ( self::check_permissions() ) {

      $css = self::theme_mods_to_css();
      file_put_contents( self::$css_file_path, $css );
      chmod( self::$css_file_path, 0777 );

      update_option( 'fluxus_customize_css_version', date('YmdHis') );

    }

  }


  public static function enqueue_style() {

    $css_version = get_option( 'fluxus_customize_css_version' );
    if ( $css_version ) {
      wp_enqueue_style( 'fluxus-customizer', self::$css_file_url, array(), $css_version );
    }

  }

}