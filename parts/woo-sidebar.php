<?php 
wp_enqueue_script( 'wc-price-slider' );
wp_enqueue_script( 'wc-jquery-ui-touchpunch' );
wp_enqueue_script( 'accounting' );
 $form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
    <div class="product-search">
        <input class="mb-2 border border-secondary w-100 rounded-1 p-2" type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'Product Search', 'woocommerce' ) . '" />
        <input type="hidden" name="post_type" value="product" />
        <input type="submit" class="btn bg-secondary text-white" id="searchsubmit" value="'. esc_attr__( 'Search', 'woocommerce' ) .'" />
    </div>
</form>';
echo $form;
$taxonomy = 'product_cat'; 
$woo_cat_args = array(
                'taxonomy'=>$taxonomy,
                'orderby'=>'name',
                'hide_empty'=>0,
                'parent'=>0);
$woo_categories = get_categories( $woo_cat_args );
if($woo_categories){
    $catprint = "<div class='woocat card mt-4'>
    <h2 class='card-header'>Category</h2>
    <ul class='list-group'>";
    foreach($woo_categories as $wc){
        $term_link = get_term_link( $wc, $taxonomy ); 
        $catprint .= '<li class="list-group-item"><a href='.$term_link.'>'. $wc->name.'</a>';
        $subargs = array(
                'hierarchical' => 1,
                'show_option_none' => '',
                'hide_empty' => 0,
                'parent' => $wc->term_id,
                'taxonomy' => $taxonomy
            );
            $subcats = get_categories($subargs);
            if(!empty($subcats)){
                $catprint .= "<ul class='second'>";
                foreach($subcats as $sc){
                    $sc_term_link = get_term_link( $sc, $taxonomy ); 
                    $catprint .= '<li class="list-group-item"><a href='.$sc_term_link.'>'. $sc->name.'</a>';
                    $subsubcats = get_terms($taxonomy, array(
                        'parent' => $sc->term_id,
                        'hierarchical' => 1,
                        'hide_empty' => 0,
                        'show_option_none' => '',
                    ));
                    if(!empty($subsubcats)){
                        $catprint .= "<ul class='third'>";
                        foreach($subsubcats as $ssc){
                            $ssc_term_link = get_term_link( $ssc, $taxonomy ); 
                            $catprint .= '<li class="list-group-item"><a href='.$ssc_term_link.'>'. $ssc->name.'</a></li>';
                        }
                        $catprint .= '</ul>';
                    }
                    $catprint .= '</li>';
                }
                $catprint .= '</ul>';
            }
            $catprint .= '</li>';
    }
}
$catprint .= "</ul></div>";
echo $catprint;
$product_tag = 'product_tag';
$product_terms = get_terms(array('taxonomy' => $product_tag, 'hide_empty' => true)); 
$tagprint = "<div class='wootag card mt-4'>
    <h2 class='card-header'>Tag</h2>
    <ul class='list-group-tag p-3'>";
foreach ( $product_terms as $pt ) { 
    $ssc_term_link = get_term_link( $pt, $product_tag ); 
    $tagprint .= '<a class="me-2 mb-2 btn btn-outline-secondary" href='.$ssc_term_link.'>'. $pt->name.'</a>';
}
$tagprint .='</div>';
echo $tagprint;

$attrprint ='';
// Loop through WooCommerce registered product attributes
foreach( wc_get_attribute_taxonomies() as $values ) {
    // Get the array of term names for each product attribute
    $attr_names = get_terms( array('taxonomy' => 'pa_' . $values->attribute_name ) );
    $attrprint .= "<div class='wooattr card mt-4'>
    <h2 class='card-header'>$values->attribute_label</h2>
    <ul class='list-group-tag p-3'>";
    foreach($attr_names as $an){
        $an_term_link = get_term_link( $an->slug, 'pa_' . $values->attribute_name);
        $attrprint .= '<a class="me-2 mb-2 btn btn-outline-secondary" href='.esc_url($an_term_link).'>'. $an->name.'</a>';
    }
    $attrprint .='</ul></div>';
}
echo $attrprint; 
$current_min_price = (@$_REQUEST['min_price']?$_REQUEST['min_price']:'');
$current_max_price = (@$_REQUEST['max_price']?$_REQUEST['max_price']:'');
?>
<div class='wooattr card mt-4'>
    <h2 class='card-header'>Price Filter</h2>
    <ul class='list-group-tag p-3'>
        <li>
        <form method="get">
            <div class="price_slider_wrapper">
            <div class="price_slider" style="display:none;"></div>
                <div class="price_slider_amount" data-step="10">
                    <label class="screen-reader-text" for="min_price"><?php esc_html_e( 'Min price', 'woocommerce' ); ?></label>
                    <input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( @$current_min_price ); ?>" data-min="0" placeholder="<?php echo esc_attr__( 'Min price', 'woocommerce' ); ?>" />
                    <label class="screen-reader-text" for="max_price"><?php esc_html_e( 'Max price', 'woocommerce' ); ?></label>
                    <input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( @$current_max_price ); ?>" data-max="1000" placeholder="<?php echo esc_attr__( 'Max price', 'woocommerce' ); ?>" />
                    <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php echo esc_html__( 'Filter', 'woocommerce' ); ?></button>
                    <div class="price_label" style="display:none;">
                        <?php echo esc_html__( 'Price:', 'woocommerce' ); ?> <span class="from"></span> &mdash; <span class="to"></span>
                    </div>
                    <?php echo wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ); ?>
                    <div class="clear"></div>
                </div>
            </div>
        </form>
        </li>
    </ul>
</div>
<style>
    .price_slider{
        width: 100%;
        display: inline-block;
        height: 10px;
        background: #cfc8d8;
        position: relative;
        border-radius: 10px;
    }
    .ui-slider-range.ui-corner-all.ui-widget-header{
        left: 0%;
        width: 31%;
        height: 10px;
        background: cornflowerblue;
        border-radius: 10px;
    }
    </style>