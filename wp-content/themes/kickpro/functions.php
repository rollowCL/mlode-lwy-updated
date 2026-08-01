<?php
/**
 * Theme functions and definitions
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'KICKPRO_THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'KICKPRO_THEME_DIR', get_template_directory() );
define( 'KICKPRO_THEME_URL', get_template_directory_uri() );
define( 'AWAIKEN_ITEM_ID', 15583 );
define( 'AWAIKEN_ITEM_NAME', 'Kickpro' );
define( 'AWAIKEN_THEME_SLUG', 'kickpro' );
define( 'AWAIKEN_MP', 'TF' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

// Theme storage
// Attention! Must be in the global namespace to compatibility with WP-CLI
//-------------------------------------------------------------------------
$GLOBALS['KICKPRO_STORAGE'] = array(
		'social_sharing' => 'facebook,whatsapp,linkedin',
		'social_urls' => 'https://www.instagram.com/ ,https://www.facebook.com/ ,https://www.youtube.com/',
		'show_preloader' => 0,
		'magic_cursor' => 1,
		'custom_fancy_scrollbar' => 1,
		'show_small_heading_icon' => 1,
		'small_heading_icon' => '',
		'footer_copyright_text' => '',
		'smooth_scrolling' => 0,
		'archive_page_layout' => 'full-width',
		'blog_single_page_layout' => 'full-width',
		'preloader_icon' => '',
		'header_background_image' => '',
		'blog_page_header_background_image' => '',
		'not_found_image' => '',
		'not_found_heading' => '',
		'not_found_text' => '',
		'read_more_icon' => KICKPRO_THEME_DIR.'/assets/images/blog-arrow.svg',
);

if ( ! function_exists( 'kickpro_slug_fonts_url' ) ) {
	function kickpro_slug_fonts_url() {
		$fonts_url = ''; 
		  
		/* Translators: If there are characters in your language that are not
		* supported by Stack Sans Headline, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$font = _x( 'on', 'Stack Sans Headline font: on or off', 'kickpro' );
		 
		if ( 'off' !== $font ) {
			
			$font_families = array();
			 
			if ( 'off' !== $font ) {
				$font_families[] = 'Stack+Sans+Headline:wght@200..700';
			}
			 
			$query_args = array(
				'family'	=> implode( '&family=', $font_families ),
				'display' 	=> 'swap',
			);
		
			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css2' );
			
		}
		 
		return esc_url_raw( $fonts_url );
	}
}

if ( ! function_exists( 'kickpro_theme_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function kickpro_theme_setup() {
	
		register_nav_menus( 
			array( 
					'header' => esc_html__( 'Header', 'kickpro' ) ,
					'footer' => esc_html__( 'Footer', 'kickpro' ) 
				 )		
		);

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'editor-styles' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 100,
				'width'       => 350,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		
		/*
		 * Gutenberg wide images.
		 */
		add_theme_support( 'align-wide' );
		
		/**
        * Load textdomain.
        */
        load_theme_textdomain( 'kickpro', KICKPRO_THEME_DIR . '/languages' );

		
		if ( is_admin() ) { 
			add_editor_style( array( kickpro_slug_fonts_url(), 'assets/css/css-variable.css', 'assets/css/all.min.css', 'style-editor.css' ) );
		}

	}

}
add_action( 'after_setup_theme', 'kickpro_theme_setup' );

/**
 * Enqueue styles
 */
if ( ! function_exists( 'kickpro_theme_load_styles' ) ) {
	function kickpro_theme_load_styles() {
		
		global $KICKPRO_STORAGE;
		$css_rules = [];
		
		if( get_option( 'kickpro_demo_imported' ) !== '1' ) {
			wp_enqueue_style( 'kickpro-font', kickpro_slug_fonts_url(), array(), null );	
		}
		
		wp_enqueue_style( 'kickpro-css-variable', KICKPRO_THEME_URL . '/assets/css/css-variable.css', array(), KICKPRO_THEME_VERSION );
		wp_enqueue_style( 'fontawesome-6.4.0', KICKPRO_THEME_URL . '/assets/css/all.min.css', array(), KICKPRO_THEME_VERSION );
		wp_enqueue_style( 'bootstrap-5.3.2', KICKPRO_THEME_URL . '/assets/css/bootstrap.min.css', array(), KICKPRO_THEME_VERSION );
		wp_enqueue_style( 'kickpro-style', KICKPRO_THEME_URL . '/style.css', array('bootstrap-5.3.2','fontawesome-6.4.0'), KICKPRO_THEME_VERSION );
		
		$small_heading_icon =  get_theme_mod( 'small_heading_icon', $KICKPRO_STORAGE['small_heading_icon'] );
		if( $small_heading_icon ) { 
			$background_image 	= 	wp_get_attachment_image_src( $small_heading_icon , 'full' );
			if(isset($background_image[0])) {
				$background_image	=	$background_image[0];
				$css_rules[] = ".section-title .elementor-heading-title::before,
								.highlighted-section-title.section-title .elementor-heading-title::before{
						background-image: url(" . esc_url($background_image) . ");
						mask-image: none;
						background-color: initial;
						background-size: cover;
						background-repeat: no-repeat;
					}";
			}
		}
		
		$optimized_control =  get_option( 'elementor_experiment-e_optimized_markup' );
		if($optimized_control === 'inactive') {
			$css_rules[] = ".at-shiny-glass-effect .elementor-widget-container{
				position:relative;
				overflow:hidden;
			}
			
			.at-blog-shiny-glass-effect, .at-shiny-glass-effect{
				overflow: visible;
			}

			.at-shiny-glass-effect:after{
				display: none !important;
			}

			.at-shiny-glass-effect .elementor-widget-container:after{
				content: '';
				position: absolute;
				width: 200%;
				height: 0%;
				left: 50%;
				top: 50%;
				background-color: rgba(255,255,255,.3);
				transform: translate(-50%,-50%) rotate(-45deg);
				z-index: 1;
			}

			.at-shiny-glass-effect .elementor-widget-container:hover:after{
				height: 250%;
				transition: all 600ms linear !important;
				background-color: transparent;
			}";
		}
		
		if (!empty($css_rules)) {
			wp_add_inline_style( 'kickpro-style',implode("\n", $css_rules) );
		}
		
	}
}
add_action( 'wp_enqueue_scripts', 'kickpro_theme_load_styles', 998 );

/**
 * Enqueue scripts
 */
if ( ! function_exists( 'kickpro_theme_load_scripts' ) ) {
	function kickpro_theme_load_scripts() {
		global $KICKPRO_STORAGE;
	
		if( get_theme_mod( 'smooth_scrolling', $KICKPRO_STORAGE['smooth_scrolling'] ) ) { 
			wp_enqueue_script( 'SmoothScroll-theme', KICKPRO_THEME_URL . '/assets/js/SmoothScroll.min.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );
		}
		
		wp_enqueue_script( 'gsap-theme', KICKPRO_THEME_URL . '/assets/js/gsap.min.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );
		if( get_theme_mod( 'magic_cursor', $KICKPRO_STORAGE['magic_cursor'] ) ) { 
		wp_enqueue_script( 'magiccursor-theme', KICKPRO_THEME_URL . '/assets/js/magiccursor.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );
		}
		
		wp_enqueue_script( 'SplitText-theme', KICKPRO_THEME_URL . '/assets/js/SplitText.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );
		wp_enqueue_script( 'ScrollTrigger-theme', KICKPRO_THEME_URL . '/assets/js/ScrollTrigger.min.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );
		wp_enqueue_script( 'three-theme', KICKPRO_THEME_URL . '/assets/js/three.min.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );
		wp_enqueue_script( 'theme-js', KICKPRO_THEME_URL . '/assets/js/function.js', array( 'jquery'), KICKPRO_THEME_VERSION, true );
		wp_localize_script(
			'theme-js',
			'theme_vars',
			[
				'theme_uri' => get_stylesheet_directory_uri(),
			]
		);
		
		// js for comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}
}
add_action( 'wp_enqueue_scripts', 'kickpro_theme_load_scripts' );


/**
 * Register widget area.
 */
if ( ! function_exists( 'kickpro_widgets_init' ) ) {
	function kickpro_widgets_init() {
		
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar', 'kickpro' ),
			'id'            => 'main-sidebar',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'kickpro' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'kickpro_widgets_init' );

/**
*	Include required file
*/
require_once KICKPRO_THEME_DIR . '/inc/init.php';