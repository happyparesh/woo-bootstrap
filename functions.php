<?php
if (!function_exists('mytheme_setup')):
	function mytheme_setup() {
		add_theme_support( 'title-tag' );
		add_theme_support('custom-logo');
		add_theme_support('automatic-feed-links');
		add_theme_support('post-thumbnails' );
		add_theme_support('post-formats',  array( 'aside', 'gallery', 'quote', 'image', 'video' ));
		add_theme_support('responsive-embeds' );
		add_theme_support('html5', array( 'style','script'));
		register_nav_menus( array(
			'primary'   => __( 'Primary Menu', 'mythememenu'),
			'secondary' => __( 'Secondary Menu', 'mythememenu'),
		));
        /***for  woocommerce*/
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
	}
endif;
add_action( 'after_setup_theme', 'mytheme_setup' );

/* Custom Post Type Start */
function create_posttype() {
	register_post_type('work',
	array(
	  'labels' => array(
	   'name' => __( 'Works' ),
	   'singular_name' => __( 'Works' ),
	  ),
	'supports'=> array( 'title', 'editor', 'thumbnail'),
	'public' => true,
	'menu_icon'=>'dashicons-chart-pie',
	'has_archive' => false,
	'rewrite' => array('slug' => 'works'),
	));

	register_post_type('services',
	array(
	   'labels' => array(
		'name' => __( 'Services' ),
		'singular_name' => __( 'Services' ),
	),
	'supports'=> array( 'title', 'editor', 'thumbnail'),
	'menu_icon'=>'dashicons-welcome-widgets-menus',
	'public' => true,
	'has_archive' => false,
	'rewrite' => array('slug' => 'services'),
	));
}
add_action( 'init', 'create_posttype' );
/* Custom Post Type End */

/* Register the widgets */
add_action( 'widgets_init', 'mytheme_widgets_init');
function mytheme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'mytheme' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Secondary Sidebar', 'mytheme' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<ul><li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li></ul>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
/**css/js enqueue***/
add_action( 'wp_enqueue_scripts', 'mytheme_scripts' );
function mytheme_scripts(){
	wp_enqueue_style( 'style-name', get_stylesheet_uri() );
}
require_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';
add_filter( 'nav_menu_link_attributes', 'prefix_bs5_dropdown_data_attribute', 20, 3 );
function prefix_bs5_dropdown_data_attribute( $atts, $item, $args ) {
    if ( is_a( $args->walker, 'WP_Bootstrap_Navwalker' ) ) {
        if ( array_key_exists( 'data-toggle', $atts ) ) {
            unset( $atts['data-toggle'] );
            $atts['data-bs-toggle'] = 'dropdown';
        }
    }
    return $atts;
}

function remove_css_js_version( $src ) {
    if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_css_js_version', 9999 );
add_filter( 'script_loader_src', 'remove_css_js_version', 9999 );

require_once get_template_directory() . '/inc/custom_breadcrumb.php';

add_filter('get_the_archive_title', function ($title) {
    if(is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif (is_tax()) { //for custom post types
        $title = sprintf(__('%1$s'), single_term_title('', false));
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    }elseif(is_search()) {
        $title = 'Search for: '.get_search_query();
    }
	else{
		$title = get_the_title();
	}
    return $title;
});

/********/
function custom_theme_settings_page() {
    add_menu_page(
        'Site Settings',
        'Site Settings',
        'manage_options',
        'custom-theme-settings', //slug
        'custom_theme_settings_form',//form function
        'dashicons-admin-site-alt3', //icon
        99
    );
}
add_action('admin_menu', 'custom_theme_settings_page');

function custom_theme_settings_form() {
    ?>
    <div class="wrap">
        <h2>Site Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom-theme-settings-group');
            do_settings_sections('custom-theme-settings-page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function custom_theme_initialize_settings() {
    register_setting('custom-theme-settings-group', 'site_title');
    register_setting('custom-theme-settings-group', 'site_social_facebok');
    register_setting('custom-theme-settings-group', 'site_contact_phone');
    // Add more settings fields as needed
}
add_action('admin_init', 'custom_theme_initialize_settings');


function custom_theme_settings_fields() {
    add_settings_section('custom-theme-general', 'General Settings', 'custom_theme_general_section', 'custom-theme-settings-page');
    add_settings_field('site_title', 'Site Title', 'custom_theme_site_title_field', 'custom-theme-settings-page', 'custom-theme-general');
    add_settings_field('site_social_facebok', 'Social Facebook Link', 'custom_theme_site_social_facebok_field', 'custom-theme-settings-page', 'custom-theme-general');
    add_settings_field('site_contact_phone', 'Contact Person phone', 'custom_theme_site_contact_phone_field', 'custom-theme-settings-page', 'custom-theme-general');
    // Add more fields as needed
}
add_action('admin_init', 'custom_theme_settings_fields');

function custom_theme_general_section() {
    // Section description (if needed)
}

function custom_theme_site_title_field() {
    $site_title = get_option('site_title');
    echo '<input type="text" name="site_title" value="' . esc_attr($site_title) . '" />';
}

function custom_theme_site_contact_phone_field() {
    $site_contact_phone = get_option('site_contact_phone');
    echo '<input type="text" placeholder="Contact person phone" name="site_contact_phone" value="' . esc_attr($site_contact_phone) . '" />';
}

function custom_theme_site_social_facebok_field() {
    $site_social_facebok = get_option('site_social_facebok');
    echo '<input type="text" placeholder="Facebook Page Link" name="site_social_facebok" value="' . esc_attr($site_social_facebok) . '" />';
}


