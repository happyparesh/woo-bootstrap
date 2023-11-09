<?php
get_header();
echo '<div class="container py-5 bd-layout">';
while ( have_posts() ) : 
	 the_post();
	 wc_get_template_part( 'content', 'single-product' ); 
endwhile; // end of the loop. 
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
echo '</div>';
get_footer();
