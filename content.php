<?php
/**
 * @package fluxus
 * @since fluxus 1.0
 */

global $post;
global $featured_image_size;

$featured_image_size = $featured_image_size ? $featured_image_size : 'fluxus-thumbnail';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php

	if ( has_post_thumbnail() && ( ! in_array( get_post_format(), array( 'aside', 'video', 'link' ) ) ) ) :

		/**
		 * Post has a thumbnail. Show It.
		 */
		$image = it_get_post_thumbnail( get_the_ID(), $featured_image_size, true );

		if ( $image ) : ?>

			<a class="thumbnail" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Read more about %s', 'fluxus' ), the_title_attribute( 'echo=0' ) ) ); ?>">
				<img src="<?php echo esc_url( $image[0] ); ?>" class="resizable" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="" /><?php
				/**
				 * If post format is Quote or Link, then show the quote overlay on top of the image.
				 */
				if ( get_post_format() == 'quote' ) {
					fluxus_quote();
				}

				if ( get_post_format() == 'link' ) {
					fluxus_link();
				}

				?>
			</a><?php

		endif;

	elseif ( get_post_format() == 'quote' ) :

		/**
		 * Post without a thumbnail. Show Quote on top of solid color.
		 */
		fluxus_quote();

	elseif ( get_post_format() == 'video' ) :

		/**
		 * Post type is video, show video in place of the thumbnail image.
		 */
		fluxus_video();

	elseif ( get_post_format() == 'link' ) :

		/**
		 * Show big link.
		 */
		fluxus_link();

	endif; ?>
	<div class="text-contents"><?php

		if ( get_post_format() != 'aside' ) : ?>

			<header class="entry-header">
				<h1 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'fluxus' ), the_title_attribute( 'echo=0' ) ) ); ?>">
						<?php the_title(); ?>
					</a>
				</h1>
				<?php

					if ( 'post' == get_post_type() ) {
						fluxus_posted_on();
					}

					if ( is_sticky() ) : ?>
						<div class="sticky-icon icon-star" title="<?php echo esc_attr( __( 'Sticky post', 'fluxus' ) ); ?>"></div><?php
					endif;

				?>
			</header><?php

		endif; ?>

		<div class="entry-summary"><?php

			if ( ! fluxus_post_has_media() ) {

				// Post has no media, so there is additional space. Let's increase the excerpt length.
				add_filter( 'excerpt_length', 'fluxus_increased_excerpt_lenght', 1001 );

			}

			the_excerpt();

			remove_filter( 'excerpt_length', 'fluxus_increased_excerpt_lenght', 1001 );

			?>
		</div>

	</div>

</article>
