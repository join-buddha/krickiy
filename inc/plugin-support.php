<?php

/**
 * ------------------------------------------------------------------------
 * WPML Plugin
 * ------------------------------------------------------------------------
 */

// Don't load CSS or JS files on front-end.
define( 'ICL_DONT_LOAD_NAVIGATION_CSS', true );
define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true );
define( 'ICL_DONT_LOAD_LANGUAGES_JS', true );

// Move footer widget from wp_footer to footer_links_right
global $icl_language_switcher;

if ( $icl_language_switcher && is_object( $icl_language_switcher ) ) {
  remove_action( 'wp_footer', array( $icl_language_switcher, 'language_selector_footer' ), 19 );
  add_action( 'footer_links_right', array( $icl_language_switcher, 'language_selector_footer' ) );
}
