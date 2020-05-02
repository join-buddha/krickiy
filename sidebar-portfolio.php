<?php
/**
 * Portfolio Single Sidebar.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

?>
<div class="sidebar sidebar-portfolio widget-area">

    <?php do_action( 'before_sidebar' ); ?>

    <div class="scroll-container">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
        <div class="viewport">
            <div class="overview">
                <?php dynamic_sidebar( 'sidebar-portfolio' ); ?>
            </div>
        </div>
    </div>

</div>