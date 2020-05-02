<?php
/**
 * General sidebar.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

?>
<div class="sidebar sidebar-general widget-area">
	<div class="scroll-container">
		<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
		<div class="viewport">
			<div class="overview">
				<?php

					do_action( 'before_sidebar' );

					dynamic_sidebar( 'sidebar-main' );

				?>
			</div>
		</div>
	</div>

</div>