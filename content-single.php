<?php
/**
 * @package fluxus
 * @since fluxus 1.0
 */

$post_categories = get_the_category();
$posted_in = false;

if ( $post_categories && fluxus_categorized_blog() ) {

	if ( count( $post_categories ) > 1 ) {
		foreach ( $post_categories as $post_cat ) {
			if ( $post_cat->term_id != 1 ) {
				$posted_in = $post_cat;
				break;
			}
		}
	} elseif ( !in_category( 1 ) ) {
		$posted_in = $post_categories[0];
	}

}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( get_post_format() != 'aside' ) : ?>
		<header class="entry-header"><?php

			$post_data = fluxus_standard_post_get_data( get_the_ID() );

			/**
			 * If a standard post has a featured image and user wish to shows it OR
			 * if link or quote post has a featured image.
			 */
			if ( ( has_post_thumbnail() && $post_data['show_featured_image'] && !get_post_format() ) ||
				 ( has_post_thumbnail() && in_array( get_post_format(), array( 'quote', 'link' ) ) ) ) :

				$image = it_get_post_thumbnail( get_the_ID(), 'fluxus-max', true );

				?>
				<div class="post-image">
					<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="" /><?php

					if ( get_post_format() == 'quote' ) :
						fluxus_quote();

					elseif ( get_post_format() == 'link' ) :
						fluxus_link();

					// Standard post
					elseif ( !get_post_format() && $post_data['show_text_over_featured_image'] ) : ?>
						<div class="cover">
							<h1><?php the_title(); ?></h1>
							<?php

								if ( $posted_in ) {
									printf( '<p>' . __( 'posted in %s', 'fluxus' ) . '</p> ', '<a href="' . esc_url( get_category_link( $posted_in->term_id ) ) . '">' . $posted_in->name . '</a>' );
								}

							?>
						</div>
						<?php

					endif;

					?>
				</div><?php

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


			endif;

			?>
			<h1 class="entry-title">
				<?php the_title(); ?>
			</h1>
			<div class="entry-meta">
				<?php
					fluxus_posted_by();
					fluxus_posted_on();
					fluxus_comment_count();
				?>
			</div>

		</header>
	<?php endif; ?>

	<div class="entry-content">
		<?php the_content(); ?>
		<div class="entry-navigation"><?php
			the_fluxus_tags( __( 'tagged with', 'fluxus' ) );
			fluxus_post_nav(); ?>
		</div>
		<?php edit_post_link( __( 'Edit', 'fluxus' ), '<span class="edit-link">', '</span>' ); ?>
	</div>

</article>