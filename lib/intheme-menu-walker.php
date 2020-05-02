<?php

/**
 * Extend native walker class with extra functionality.
 */
class Intheme_Menu_Walker extends Walker_Nav_Menu {

  function is_item_portfolio_index( $element ) {

    if ( $element->object != 'page' ) {
      return false;
    }

    return it_is_template( $element->object_id, 'template-portfolio.php' ) ||
         it_is_template( $element->object_id, 'template-portfolio-grid.php' );

  }

  function is_item_blog_index( $element ) {

    if ( $element->object != 'page' ) {
      return false;
    }

    return it_is_template( $element->object_id, 'template-blog-horizontal.php' ) ||
         it_is_template( $element->object_id, 'template-blog-vertical.php' );

  }

  function has_current( $elements ) {

    foreach ( $elements as $element ) {

      if ( $element->current || $element->current_item_parent || $element->current_item_ancestor ) {
        return true;
      }

    }

    return false;

  }

  function walk( $elements, $max_depth ) {

    // We have to take 3rd argument dynamically to avoid Strict Standards: Declaration of ' ' should be compatible with ' '
    $args = array_slice(func_get_args(), 2);
    $args = $args[0];

    global $post;

    // We are on a project page
    if ( $post && ( $post->post_type == 'fluxus_portfolio' ) && is_single() ) {

      // If there's a current element, then let's not do anything
      $found = $this->has_current( $elements );

      if ( ! $found ) {

        foreach ( $elements as $key => $element ) {

          // Search only root items first
          if ( 0 == $element->menu_item_parent ) {

            // If our current menu item is a Page with Portfolio Template
            if ( $this->is_item_portfolio_index( $element ) ) {
              $elements[$key]->classes[] = 'active';
              $found = true;
              break;
            }

          }

        }

        // We were unable to find Portfolio on the root, then activate any item that has
        // template-portfolio.php or template-portfolio-grid.php
        if ( ! $found ) {

          foreach ( $elements as $key => $element ) {

            if ( $this->is_item_portfolio_index( $element ) ) {
              $elements[$key]->classes[] = 'active';
              break;
            }

          }

        }

      }

    }

    // We are on blog post page
    if ( $post && ( $post->post_type == 'post' ) && is_single() ) {

      $found = $this->has_current( $elements );

      if ( ! $found ) {

        foreach ( $elements as $key => $element ) {

          // Search only root items first
          if ( 0 == $element->menu_item_parent ) {

            if ( $this->is_item_blog_index( $element ) ) {
              $elements[$key]->classes[] = 'active';
              $found = true;
              break;
            }

          }

        }

      }

    }

    return parent::walk( $elements, $max_depth, $args );

  }

  function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

    if ( !$element ) {
      return;
    }

    global $post;

    /**
     * Adjust menu if our current post_type is fluxus_portfolio
     */
    if ( $post && $post->post_type == 'fluxus_portfolio' ) {

      // If our current menu item is a Page with Portfolio Template
      if ( $this->is_item_portfolio_index( $element ) ) {

        if ( isset( $children_elements[$element->ID] ) ) {

          // Check if this Portfolio menu item has children that are
          // currently active Project Type terms. This also means
          // that our current page is Project Type archive.

          foreach ( $children_elements[$element->ID] as $child ) {
            if ( in_array( 'current-fluxus-project-type-ancestor', $child->classes ) ) {
              $element->classes[] = 'active';
            }
          }

        }


      }

    }

    $id_field = $this->db_fields['id'];

    /**
     * Adds the "has-children" class to the current item if it has children.
     */
    if ( ! empty( $children_elements[$element->$id_field] ) ) {
      array_push( $element->classes, 'has-children' );
    }

    /**
     * That's it, now call the default function to do the rest.
     */
    return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
  }

}