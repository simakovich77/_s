<?php
/**
 * _s functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package _s
 */

if ( ! function_exists( '_s_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function _s_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on _s, use a find and replace
	 * to change '_s' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( '_s', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'menu-1' => esc_html__( 'Primary', '_s' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( '_s_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif;
add_action( 'after_setup_theme', '_s_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function _s_content_width() {
	$GLOBALS['content_width'] = apply_filters( '_s_content_width', 640 );
}
add_action( 'after_setup_theme', '_s_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function _s_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', '_s' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', '_s' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', '_s_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function _s_scripts() {
	wp_enqueue_style( '_s-style', get_stylesheet_uri() );

	wp_enqueue_script( '_s-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( '_s-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', '_s_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

//Add variable url with link to admin-ajax.php
add_action( 'wp_enqueue_scripts', '_s_ajax_data', 99 );
function _s_ajax_data(){
    wp_localize_script('s-script', 's_customize_register_theme',
        array(
            'url' => admin_url('admin-ajax.php')
        )
    );
}?>

<?php
//Add action for jQuery function
add_action('wp_footer', '_s_action_javascript', 99);
function _s_action_javascript() {
    ?>
    <script type="text/javascript" >
        jQuery(document).ready(function($) {
            var data = {
                action: '_s_action'
            };
            //Request on url with name of action
            jQuery.post( _s_ajax.url, data, function(response) {
                //entry into div with id #posts response from server
                $('#posts').html(response);
            });
        });
    </script>
    <?php
}
//Add callback function for user with administrator priveleges and without it
add_action('wp_ajax__s_action', '_s_action_callback');
add_action('wp_ajax_nopriv_s_action', '_s_action_callback');

function _s_action_callback()
{

    //set custom criteria for select data from database
    $args = array(
        'posts_per_page' => 5,
        'orderby' => 'comment_count'
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<a href="'.get_permalink().'">'.get_the_title().'"</a><br>';
        }
    } else {
        echo 'Posts not found!';
    }
    //Return original data of posts
    wp_reset_postdata();
    wp_die();
}

//function for change color fonts and logo
function s_customize_register( $wp_customize )
{
    //Description section for change color of fonts in site
    $wp_customize->add_section( 'section_color_text' , array(
        'title'      => __( 'Цвет шрифтов', 's' ),
        'priority'   => 30,
    ) );
    //Description settings for change color of fonts in site
    $wp_customize->add_setting( 'setting_color_text' , array(
        'default'     => '#000000',
        'transport'   => 'refresh',
    ) );
    //Description control for section_color_text and setting_color_text - for section and controller for change color of fonts in site
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
        'label'      => __( 'Color', 's' ),
        'section'    => 'section_color_text',
        'settings'   => 'setting_color_text',
    ) ) );
    //Description section for change logo in main page
    $wp_customize->add_section( 'section_logo_picture' , array(
        'title'      => __( 'Картинка', 's' ),
        'priority'   => 31,
    ) );
    //Description settings of section for change logo in main page
    $wp_customize->add_setting( 'setting_logo_picture' , array(
        'default'     => '#000000',
        'transport'   => 'refresh',
    ) );
    //Description control for section_logo_picture и setting_logo_picture - for section and controller for change logo in main page
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'link_picture', array(
        'label'      => __( 'Logo', 's' ),
        'section'    => 'section_logo_picture',
        'settings'   => 'setting_logo_picture',
    ) ) );
}
//CSS settings for change color text
add_action( 'customize_register', 's_customize_register');
    function s_customize_css()
    {
        ?>
        <style type="text/css">
            body { color: <?php echo get_theme_mod('setting_color_text', '#000000'); ?>; }
        </style>
        <?php
    }
    add_action( 'wp_head', 's_customize_css');
?>


<?php
    //Add custom meta box
    function add_custom_meta_box()
    {
    add_meta_box("demo-meta-box", "Custom Meta Box", "custom_meta_box_markup", "post", "side", "high", null);
    }
    add_action("add_meta_boxes", "add_custom_meta_box");
?>


<?php
//Creating custom meta box
function custom_meta_box_markup($object)
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    ?>
        <label for="meta-box-dropdown">Dropdown</label>
        <select name="meta-box-dropdown">
            <?php
            //Select values
            $option_values = array(1, 2, 3, 4, 5);

            foreach($option_values as $key => $value)
            {
                if($value == get_post_meta($object->ID, "meta-box-dropdown", true))
                {
                    ?>
                    <option selected><?php echo $value; ?></option>
                    <?php
                }
                else
                {
                    ?>
                    <option><?php echo $value; ?></option>
                    <?php
                }
            }
            ?>
        </select>
        <br>
    </div>
    <?php
}
?>

<?php
//Save data of meta box
function save_custom_meta_box($post_id, $post, $update)
{
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if (!current_user_can("edit_post", $post_id))
        return $post_id;

    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
    $slug = "post";

    if ($slug != $post->post_type)
        return $post_id;
    $meta_box_dropdown_value = "";

    if (isset($_POST["meta-box-dropdown"])) {
        $meta_box_dropdown_value = $_POST["meta-box-dropdown"];
    }
    update_post_meta($post_id, "meta-box-dropdown", $meta_box_dropdown_value);

}
add_action("save_post", "save_custom_meta_box", 10, 3);
?>
