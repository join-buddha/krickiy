<?php
/**
 * Standard Post Format
 *
 * @since fluxus 1.2.4
 */


function fluxus_standard_post_get_data( $post_id ) {

    $show_featured_image = get_post_meta( $post_id, 'fluxus_show_featured_image', true );
    $show_text_over_featured_image = get_post_meta( $post_id, 'fluxus_show_text_over_featured_image', true );

    if ( $show_featured_image == '' ) {
        $show_featured_image = 1; // default value is true
    }

    if ( $show_text_over_featured_image == '' ) {
        $show_text_over_featured_image = 1; // default value is true
    }

    return array(
        'show_featured_image' => $show_featured_image,
        'show_text_over_featured_image' => $show_text_over_featured_image
    );

}


/**
 * Meta box contents.
 */
function fluxus_standard_post_meta_box_contents() {
    global $post;

    if ( ! $post) {
        return;
    }

    $data = fluxus_standard_post_get_data( $post->ID );

    ?>
    <div class="fluxus-meta-field">
        <label for="fluxus_show_featured_image" style="width: 42%"><?php _e( 'Display featured image on single post page', 'fluxus' ); ?></label>
        <?php $show_featured = $data['show_featured_image'] ? ' checked="checked"' : ''; ?>
        <input type="checkbox" id="fluxus_show_featured_image" value="1" name="fluxus_show_featured_image"<?php echo $show_featured; ?> />
    </div>
    <div class="fluxus-meta-field">
        <label for="fluxus_show_text_over_featured_image" style="width: 42%"><?php _e( 'Display post title over featured image on single post page', 'fluxus' ); ?></label>
        <?php $show_featured_text = $data['show_text_over_featured_image'] ? ' checked="checked"' : ''; ?>
        <input type="checkbox" id="fluxus_show_text_over_featured_image" value="1" name="fluxus_show_text_over_featured_image"<?php echo $show_featured_text; ?> />
    </div>
    <?php
}


/**
 * Save meta box.
 */
function fluxus_standard_post_meta_box_save( $post_id ) {

    if ( ! it_check_save_action( $post_id ) ) {
        return $post_id;
    }

    $fluxus_show_featured_image = isset( $_POST['fluxus_show_featured_image'] ) ? 1 : 0;
    update_post_meta( $post_id, 'fluxus_show_featured_image', $fluxus_show_featured_image );

    $fluxus_show_text_over_featured_image = isset( $_POST['fluxus_show_text_over_featured_image'] ) ? 1 : 0;
    update_post_meta( $post_id, 'fluxus_show_text_over_featured_image', $fluxus_show_text_over_featured_image );

}
add_action( 'save_post', 'fluxus_standard_post_meta_box_save' );


/**
 * Add Meta Box in Page.
 */
function fluxus_standard_post_add_meta_box() {
    add_meta_box(
            'fluxus_standard_meta_box',
            __( 'Standard Post', 'fluxus' ),
            'fluxus_standard_post_meta_box_contents',
            'post',
            'normal'
        );
}


/**
 * Initialize Admin-Side Post Format.
 */
function fluxus_post_format_standard_admin_init() {

    add_action( 'add_meta_boxes', 'fluxus_standard_post_add_meta_box' );

}
add_action( 'admin_init', 'fluxus_post_format_standard_admin_init', 1 );


