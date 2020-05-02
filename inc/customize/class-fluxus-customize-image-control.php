<?php

class Fluxus_Customize_Image_Control extends WP_Customize_Image_Control {
  public $description = '';

  public function enqueue() {
    wp_enqueue_style( 'fluxus-customize-image-control', get_template_directory_uri() . '/css/wp-admin/customize-image-control.css' );
    parent::enqueue();
  }

  /**
   * Render the control's content.
   *
   * @since 3.4.0
   */
  public function render_content() {
    $src = $this->value();
    if ( isset( $this->get_url ) )
      $src = call_user_func( $this->get_url, $src );

    ?>
    <div class="customize-image-picker">
      <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
      <?php if ( ! empty( $this->description ) ) : ?>
        <p class="control-description"><?php echo $this->description; ?></p>
      <?php endif; ?>
      <div class="customize-control-content">
        <div class="dropdown preview-thumbnail" tabindex="0">
          <div class="dropdown-content">
            <?php if ( empty( $src ) ): ?>
              <img style="display:none;" />
            <?php else: ?>
              <img src="<?php echo esc_url( set_url_scheme( $src ) ); ?>" />
            <?php endif; ?>
            <div class="dropdown-status"></div>
          </div>
          <div class="dropdown-arrow"></div>
        </div>
      </div>

      <div class="library">
        <ul>
          <?php foreach ( $this->tabs as $id => $tab ): ?>
            <li data-customize-tab='<?php echo esc_attr( $id ); ?>' tabindex='0'>
              <?php echo esc_html( $tab['label'] ); ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php foreach ( $this->tabs as $id => $tab ): ?>
          <div class="library-content" data-customize-tab='<?php echo esc_attr( $id ); ?>'>
            <?php call_user_func( $tab['callback'] ); ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="actions">
        <a href="#" class="remove"><?php _e( 'Remove Image' ); ?></a>
      </div>
    </div>
    <?php
  }

}