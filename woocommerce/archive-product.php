<?php
get_header();
?>
<div class="row mt-3 mb-3">
    <?php do_action( 'woocommerce_archive_description' ); ?>
</div>
<div class="mainshop mt-3 mb-5">
    <div class="container">
        <div class="row">
        <div class="col-sm-9">
            <?php 
            if(woocommerce_product_loop())
            {
                do_action( 'woocommerce_before_shop_loop' );
                woocommerce_product_loop_start();
                if ( wc_get_loop_prop( 'total' ) ) {
                    while ( have_posts() ) {
                        the_post();
                        do_action( 'woocommerce_shop_loop' );        
                        get_template_part( 'template-parts/content', 'product' );
                    }
                }
                woocommerce_product_loop_end();
                do_action( 'woocommerce_after_shop_loop' );
            }else{
                do_action( 'woocommerce_no_products_found' );
            }
            ?>
        </div>
        <div class="col-sm-3 woosidebar">
            <?php get_template_part( 'parts/woo', 'sidebar' ); ?>
        </div>
    </div>
</div>
</div>
<?php 
get_footer();