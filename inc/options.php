<?php
/**
 * File contains definition and implementation of Theme Options.
 *
 * @since fluxus 1.0
 */

/**
 * Returns all supported Social Networks.
 */
function fluxus_get_social_networks() {

    $networks = array(
            '500px' => '500px',
            'Behance' => 'behance',
            'Dribbble' => 'dribbble',
            'Facebook' => 'facebook',
            'Flickr' => 'flickr',
            'Instagram' => 'instagram',
            'Google Plus' => 'gplus',
            'Linkedin' => 'linkedin',
            'Pinterest' => 'pinterest',
            'Skype' => 'skype',
            'Tumblr' => 'tumblr',
            'Twitter' => 'twitter',
            'Vimeo' => 'vimeo',
            'YouTube' => 'youtube',
        );

    return $networks;

}


/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 */
function optionsframework_options() {

    /**
     * Options page: General Settings
     */
    $options = array();

    $options[] = array(
            'name' => __( 'General Settings', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Footer Copyright', 'fluxus' ),
            'desc' => __( 'Copyright displayed in the bottom. You can use HTML tags.' , 'fluxus' ),
            'id' => 'fluxus_copyright_text',
            'std' => '&copy; Fluxus Wordpress Theme',
            'type' => 'text',
            'html' => true
        );

    $options[] = array(
            'name' => __( 'Site Description', 'fluxus' ),
            'desc' => __( 'Give a short description of your website. This is visible in search results and when sharing your website. The description will not be used on content pages like blog post or portfolio project.' , 'fluxus' ),
            'id' => 'fluxus_site_description',
            'type' => 'textarea'
        );

    $options[] = array(
            'name' => __( 'Disable Fluxus Meta Tags', 'fluxus' ),
            'desc' => __( 'By default Fluxus theme will create page description and page thumbnail meta tags. If you use any SEO plugin, then you should disable Fluxus meta tag generation.' , 'fluxus' ),
            'id' => 'fluxus_disable_meta_tags',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Tracking code', 'fluxus' ),
            'id' => 'fluxus_tracking_code',
            'desc' => sprintf(
                __( 'Paste your Google Analytics or any other tracking JavaScript code. The code output goes in the footer and <b>&lt;script&gt;&lt;/script&gt;</b> tags are automatically added.' , 'fluxus' ),
                '<br />'
            ),
            'type' => 'textarea'
        );

    $options[] = array(
            'name' => __( 'Custom CSS', 'fluxus' ),
            'id' => 'fluxus_custom_css',
            'skip_sanitize' => true,
            'desc' => sprintf(
                __( "Add your custom CSS rules here. \n<b>Note:</b> You can also add rules to the user.css file, which is better for performance. <a href='%s'>Click here to edit</a> user.css file." , 'fluxus' ),
                esc_url( 'theme-editor.php?file=' . urlencode( 'user.css' ) . '&theme=' . urlencode( get_stylesheet() ) )
            ),
            'type' => 'textarea'
        );



    /**
     * Options page: Portfolio
     */
    $options[] = array(
            'name' => __( 'Portfolio', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Clicking on project image', 'fluxus' ),
            'id' => 'fluxus_project_image_click',
            'std' => 'lightbox',
            'class' => 'medium',
            'options' => array(
                    'disabled' => 'Does nothing',
                    'lightbox' => 'Opens Lightbox',
                    'scroll' => 'Scrolls to the next image'
                ),
            'type' => 'select'
        );

    $options[] = array(
            'name' => __( 'Hide password protected projects', 'fluxus' ),
            'desc' => __( 'Check to hide password protected projects from appearing inside portfolios. Password protected posts will be still accessible by using direct URL.' , 'fluxus' ),
            'id' => 'fluxus_hide_password_protected_projects',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Lazy load project images', 'fluxus' ),
            'desc' => __( 'Load project images once they are visible in the user viewport.' , 'fluxus' ),
            'id' => 'fluxus_lazy_loading',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Disable &quot;Like this Project?&quot;', 'fluxus' ),
            'desc' => __( 'Check to hide project sharing widget at the end of each project.' , 'fluxus' ),
            'id' => 'fluxus_disable_like_this_project',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Disable &quot;Other Projects&quot; navigation', 'fluxus' ),
            'desc' => __( 'Check to hide navigation at the end of each project.' , 'fluxus' ),
            'id' => 'fluxus_disable_other_projects',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Hide project types', 'fluxus' ),
            'desc' => __( 'Hide project types that are displayed under project titles on horizontal portfolio. Note: to remove project types from sidebar go to Appearance > Widgets.' , 'fluxus' ),
            'id' => 'fluxus_hide_project_types_under_titles',
            'std' => '0',
            'type' => 'checkbox'
        );



    /**
     * Options page: Social
     */
    $options[] = array(
            'name' => __( 'Social', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Enable share buttons', 'fluxus' ),
            'desc' => __( 'Show social sharing buttons in the footer.' , 'fluxus' ),
            'id' => 'fluxus_share_enabled',
            'std' => '1',
            'type' => 'checkbox'
        );

    $social_networks = array(
            'facebook' => 'Facebook Like',
            'facebookshare' => 'Facebook Share',
            'twitter' => 'Twitter',
            'googleplus' => 'Google+',
            'pinterest' => 'Pinterest',
            'linkedin' => 'LinkedIn',
            'digg' => 'Digg',
            'delicious' => 'Delicious',
            'stumbleupon' => 'StumbleUpon'
        );

    $social_networks_defaults = array(
            'facebook' => 1,
            'twitter' => 1,
            'googleplus' => 1
        );

    $options[] = array(
            'name' => __( 'Sharing Networks', 'fluxus' ),
            'desc' => __( 'Select social networks on which you want to share your website.' , 'fluxus' ),
            'id' => 'fluxus_share_services',
            'std' => false,
            'type' => 'multicheck',
            'options' => $social_networks,
            'str' => $social_networks_defaults
        );

    $options[] = array(
            'name' => __( 'Enable social networks', 'fluxus' ),
            'desc' => __( 'Show social network links in the footer.' , 'fluxus' ),
            'id' => 'fluxus_social_enabled',
            'std' => '0',
            'type' => 'checkbox'
        );

    foreach ( fluxus_get_social_networks() as $label => $network) {

        $options[] = array(
                'name' => $label . ' ' . __( 'URL', 'fluxus' ),
                'id'   => 'fluxus_' . $network . '_url',
                'type' => 'text'
            );

        if ( $network == 'skype' ) {
            $options[count($options) - 1]['name'] = __( 'Skype name', 'fluxus' );
        }

    }



    /**
     * Options page: Style
     */
    $options[] = array(
            'name' => __( 'Visual', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'type' => 'info',
            'desc' => sprintf(
                __( 'All visual settings can be configured by going to <a href="%s">Appearance > Customize</a>.', 'fluxus' ),
                esc_url( admin_url( 'customize.php' ) )
            )
        );



    /**
     * Options page: Misc
     */
    $options[] = array(
            'name' => __( 'Misc', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Upscale images and videos to fit screen height', 'fluxus' ),
            'desc' => __( 'Allow image and video upscaling in order to make them fill available screen height while maintaining aspect ratio. This affects horizontal portfolio, projects, blog posts and vertical blog.' , 'fluxus' ),
            'id' => 'fluxus_allow_image_upscaling',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Show navigation arrows on Full Page Slider', 'fluxus' ),
            'desc' => __( 'Check to always show navigation arrows on Full Page Slider. If not checked arrows and titles will be only shown when mouse is hovering slide image.' , 'fluxus' ),
            'id' => 'fluxus_show_slider_arrows',
            'std' => '0',
            'type' => 'checkbox'
        );

    return $options;
}


/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */
function optionsframework_option_name() {

    // This gets the theme name from the stylesheet
    $themename = get_option( 'stylesheet' );
    $themename = preg_replace("/\W/", "_", strtolower($themename) );

    $optionsframework_settings = get_option( 'optionsframework' );
    $optionsframework_settings['id'] = $themename;
    update_option( 'optionsframework', $optionsframework_settings );

}


/**
 * ------------------------------------------------------------------------------------
 * The functions below implement Theme Options.
 * ------------------------------------------------------------------------------------
 */


/**
 * Tracking code
 */
function fluxus_tracking_code() {
    $option = of_get_option( 'fluxus_tracking_code' );
    if ( ! empty( $option ) ) {
        echo '<script>' . $option . '</script>';
    }
}

if ( ! is_admin() && ! is_preview() ) {
    add_action( 'wp_footer', 'fluxus_tracking_code', 1000 );
}


/**
 * Custom CSS
 */
function fluxus_custom_css() {
    $option = of_get_option( 'fluxus_custom_css' );
    if ( ! empty( $option ) ) {
        echo "<style>\n" . $option . "\n</style>\n";
    }
}

if ( ! is_admin() ) {
    add_action( 'wp_head', 'fluxus_custom_css' );
}


/**
 * Favicon
 */
function fluxus_favicon() {
    $favicon = get_theme_mod('favicon');

    if ( ! empty( $favicon ) ) {
        echo "<link rel='shortcut icon' href='" . esc_url( $favicon ) . "' />\n";
    }
}
add_action( 'wp_head', 'fluxus_favicon' );


function fluxus_options_init() {

   global $fluxus_theme;

    $disable_meta = of_get_option( 'fluxus_disable_meta_tags' );
    if ( $disable_meta ) {

        $fluxus_theme->options['enable_meta_description'] = false;
        $fluxus_theme->options['enable_meta_image'] = false;

    } else {
        $facebook_image = get_theme_mod( 'facebook_icon' );

        if ( $facebook_image ) {
            $fluxus_theme->set_image( $facebook_image );
        }

        // Page Description
        $fluxus_theme->set_description( of_get_option( 'fluxus_site_description' ) );

    }

    if ( of_get_option( 'fluxus_allow_image_upscaling' ) ) {
        fluxus_add_html_class( 'upscale' );
    }

}
add_action( 'init', 'fluxus_options_init' );


function fluxus_full_page_slider_options( $attributes ) {
    $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] : array();
    $attributes['class'][] = of_get_option( 'fluxus_show_slider_arrows' ) ? 'show-arrows-always' : 'show-arrows-on-hover';
    return $attributes;
}
add_filter( 'fluxus_slider_attributes', 'fluxus_full_page_slider_options' );


/**
 * Share widget
 */
function fluxus_get_social_share( $args = array() ) {

    if ( is_404() ) {
        return false;
    }

    $option = of_get_option( 'fluxus_share_enabled' );

    if ( $option && $option == '1' ) {

        $share_services = of_get_option( 'fluxus_share_services' );
        $data_services = array();

        if ( is_array( $share_services ) && count( $share_services ) ) {

            foreach ( $share_services as $key => $service ) {
                if ( $service ) {
                    $key = $key == 'googleplus' ? 'googlePlus' : $key;
                    $key = $key == 'facebookshare' ? 'facebookShare' : $key;
                    $data_services[] = $key;
                }
            }

        }

        if ( count( $data_services ) == 0 ) {
            return false;
        }

        $defaults = array(
                'data-url' => array(
                        esc_url( get_permalink() )
                    ),
                'data-curl' => array(
                        esc_url( get_template_directory_uri() . '/lib/sharrre.php' )
                    ),
                'data-services' => array(
                        join( ',', $data_services )
                    ),
                'data-title' => array(
                        __( 'Share', 'fluxus' )
                    ),
                'class' => array(
                        'sharrre'
                    )
            );

        $args = array_merge( $defaults, $args );

        $html = '<div' . it_array_to_attributes( $args ) . '></div>';

        return $html;

    } else {

        return false;

    }

}


/**
 * Share widget that is located in the footer.
 */
function fluxus_footer_social_share() {
    $args = array(
            'id' => 'sharrre-footer',
            'data-buttons-title' => __( 'Share this page', 'fluxus' )
        );
    $html = fluxus_get_social_share( $args );
    if ( $html ) {
        echo $html;
    }
}

if ( !is_admin() && !is_404() ) {
    add_action( 'footer_social', 'fluxus_footer_social_share' );
}


/**
 * Social networks
 */
function fluxus_social_networks() {

    $option = of_get_option( 'fluxus_social_enabled' );

    if ( $option && $option == '1' ) {

        $html = '';

        foreach ( fluxus_get_social_networks() as $network) {

            $option = of_get_option( 'fluxus_' . $network . '_url' );
            $title = esc_attr( sprintf( __( 'Connect on %s', 'fluxus' ), ucfirst( $network ) ) );

            if ( !empty( $option ) ) {
                if ( $network == 'skype' ) {
                    $href = 'skype:' . $option . '?call';
                } else {
                    $href = esc_url ( $option );
                }
                $html .= '<a class="icon-social icon-' . $network . '-circled" href="' . $href . '" target="_blank" title="' . $title . '" rel="nofollow"></a>';
            }

        }

        if ( !empty( $html ) ) : ?>
            <div class="social-networks"><?php echo $html; ?></div><?php
        endif;

    }

}

if ( ! is_admin() ) {
    add_action( 'footer_social', 'fluxus_social_networks' );
}


/**
 * This function is called when saving custom logo.
 * It will try to retrieve logo size and save it for later use.
 */
function of_update_option_fluxus_logo( $value, $id ) {
    return of_update_image_option( $value, $id );
}


/**
 * This function is called when saving RETINA custom logo.
 * It will try to retrieve logo size and save it for later use.
 */
function of_update_option_fluxus_logo_retina( $value, $id ) {
    return of_update_image_option( $value, $id );
}


/**
 * Retrieves image size and saves it as transient.
 */
function of_update_image_option( $value, $id ) {

    if ( $value && !is_numeric( $value ) ) {

        $size = getimagesize( $value );

        if ( is_array( $size ) && isset( $size[0] ) && isset( $size[1] ) &&
             is_numeric( $size[0] ) && is_numeric( $size[1] ) &&
             $size[0] && $size[1] ) {

            set_transient( 'fluxus_option_' . $id, array( $size[0], $size[1] ) );

        } else {

            /**
             * If we are unable to set image data, then store the error.
             */
            set_transient( 'fluxus_option_' . $id, 'unable to get image data' );

        }

    } else {

        delete_transient( 'fluxus_option_' . $id );

    }

    return $value;

}


/**
 * Returns an array with logo information: url, size.
 */
function fluxus_get_logo() {

    $logo = get_theme_mod( 'logo' );

    $output = array();

    if ( is_numeric( $logo ) ) {

        $output = wp_get_attachment_image_src( $logo, false );

        if ( $output ) {
            $output[3] = 'width="' . esc_attr( $output[1] ) . '"';
            $output[4] = 'height="' . esc_attr( $output[2] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';
        }

    } else if ( ! empty( $logo ) ) {

        $output[0] = $logo;

        $size = get_transient( 'fluxus_option_fluxus_logo' );

        /**
         * If transient does not exist, then let's try to set it.
         */
        if ( $size === false ) {
            of_update_image_option( $output[0], 'fluxus_logo' );
            $size = get_transient( 'fluxus_option_fluxus_logo' );
        }

        if ( is_array( $size ) ) {

            $output[1] = $size[0];
            $output[2] = $size[1];
            $output[3] = 'width="' . esc_attr( $size[0] ) . '"';
            $output[4] = 'height="' . esc_attr( $size[1] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';

        } else { // We have an image set, but unable to retrieve size

            $output[1] = '';
            $output[2] = '';
            $output[3] = '';
            $output[4] = '';
            $output['size'] = '';

        }

    }

    return $output;

}


/**
 * Returns an array with RETINA logo information: url, size.
 */
function fluxus_get_logo_retina() {

    $logo = get_theme_mod( 'logo_retina' );

    $output = array();

    if ( is_numeric( $logo ) ) {

        $output = wp_get_attachment_image_src( $logo, false );

        if ( $output ) {
            $output[3] = 'width="' . esc_attr( round($output[1] / 2) ) . '"';
            $output[4] = 'height="' . esc_attr( round($output[2] / 2) ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';
        }

    } else if ( ! empty( $logo ) ) {

        $output[0] = $logo;

        $size = get_transient( 'fluxus_option_fluxus_logo_retina' );

        /**
         * If transient does not exist, then let's try to set it.
         */
        if ( $size === false ) {
            of_update_image_option( $output[0], 'fluxus_logo_retina' );
            $size = get_transient( 'fluxus_option_fluxus_logo_retina' );
        }

        if ( is_array( $size ) ) {

            $output[1] = round( $size[0] / 2 );
            $output[2] = round( $size[1] / 2 );
            $output[3] = 'width="' . esc_attr( $output[1] ) . '"';
            $output[4] = 'height="' . esc_attr( $output[2] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';

        } else { // We have an image set, but unable to retrieve size

            $output[1] = '';
            $output[2] = '';
            $output[3] = '';
            $output[4] = '';
            $output['size'] = '';

        }

    }

    return $output;

}
