<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Set our Customizer default options
*/
if ( ! function_exists( 'awaiken_generate_defaults' ) ) {
	function awaiken_generate_defaults() {
		global $KICKPRO_STORAGE;

		return apply_filters( 'awaiken_customizer_defaults', $KICKPRO_STORAGE );
	}
}


/**
 * Customizer Setup and Custom Controls
 *
 */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class awaiken_initialise_customizer_settings {
	// Get our default values
	private $defaults;

	public function __construct() {
		// Get our Customizer defaults
		$this->defaults = awaiken_generate_defaults();


		// Register sections
		add_action( 'customize_register', array( $this, 'awaiken_add_customizer_sections' ) );
		
		// Register general control
		add_action( 'customize_register', array( $this, 'awaiken_register_general_options_controls' ) );

		// Register blog control
		add_action( 'customize_register', array( $this, 'awaiken_register_blog_options_controls' ) );

		// Register 404 control
		add_action( 'customize_register', array( $this, 'awaiken_register_404_options_controls' ) );
		
		// Register footer control
		add_action( 'customize_register', array( $this, 'awaiken_register_footer_options_controls' ) );
		
	}


	/**
	 * Register the Customizer sections
	 */
	public function awaiken_add_customizer_sections( $wp_customize ) {
		
		// Add section general options
		$wp_customize->add_section( 'general_options' , array(
			'title'      => __( 'General Options', 'kickpro' ),
		) );
		
		// Add section blog options
		$wp_customize->add_section( 'blog_options' , array(
			'title'      => __( 'Blog Options', 'kickpro' ),
		) );

		// Add section 404 options
		$wp_customize->add_section( '404_options' , array(
			'title'      => __( '404 Options', 'kickpro' ),
		) );
		
		// Add section footer options
		$wp_customize->add_section( 'footer_options' , array(
			'title'      => __( 'Footer Options', 'kickpro' ),
		) );
		
	}
	
	/**
	 * Register general option controls
	 */

	public function awaiken_register_general_options_controls( $wp_customize ) {  
		
		$section	=	'general_options';
		
		// Preloader
		$wp_customize->add_setting( 'show_preloader',
			array(
				'default' => $this->defaults['show_preloader'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_switch_sanitization'
			)
		);
		
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'show_preloader',
			array(
				'label' => __( 'Preloader', 'kickpro' ),
				'description' => esc_html__( 'Display preloader while the page is loading.', 'kickpro' ),
				'section' => $section
			)
		) );
		
		// Magic Cursor
		$wp_customize->add_setting( 'magic_cursor',
			array(
				'default' => $this->defaults['magic_cursor'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_switch_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'magic_cursor',
			array(
				'label' => __( 'Magic Cursor', 'kickpro' ),
				'description' => esc_html__( 'Show Magic Cursor.', 'kickpro' ),
				'section' => $section
			)
		) );

		// Custom fancy scrollbar
		$wp_customize->add_setting( 'custom_fancy_scrollbar',
			array(
				'default' => $this->defaults['custom_fancy_scrollbar'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_switch_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'custom_fancy_scrollbar',
			array(
				'label' => __( 'Custom Fancy Scrollbar', 'kickpro' ),
				'description' => esc_html__( 'Custom fancy scrollbar Disable/Enable.', 'kickpro' ),
				'section' => $section
			)
		) );
		
		// Smooth scrolling
		$wp_customize->add_setting( 'smooth_scrolling',
			array(
				'default' => $this->defaults['smooth_scrolling'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_switch_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'smooth_scrolling',
			array(
				'label' => __( 'Smooth Scrolling', 'kickpro' ),
				'description' => esc_html__( 'Smooth Scrolling Disable/Enable', 'kickpro' ),
				'section' => $section
			)
		) );
		
		// heading icon 
		$wp_customize->add_setting( 'show_small_heading_icon',
			array(
				'default' => $this->defaults['show_small_heading_icon'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_switch_sanitization'
			)
		);
		
		$wp_customize->add_control( new Skyrocket_Toggle_Switch_Custom_control( $wp_customize, 'show_small_heading_icon',
			array(
				'label' => __( 'Display Small Icon', 'kickpro' ),
				'description' => esc_html__( 'Display small icon before small heading.', 'kickpro' ),
				'section' => $section
			)
		) );
		
		// heading icon
		$wp_customize->add_setting( 'small_heading_icon',
			array(
				'default' => $this->defaults['small_heading_icon'],
				'transport' => 'refresh',
				'sanitize_callback' => 'absint'
			)
		);
		
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'small_heading_icon',
			array(
				'label' => __( 'Small heading icon', 'kickpro' ),
				'description' => esc_html__( 'If you want to change the current icon, select it here.', 'kickpro' ),
				'section' => $section,
				'mime_type' => 'image',
				'button_labels' => array(
					'select' => __( 'Select File', 'kickpro' ),
					'change' => __( 'Change File', 'kickpro' ),
					'default' => __( 'Default', 'kickpro' ),
					'remove' => __( 'Remove', 'kickpro' ),
					'placeholder' => __( 'No file selected', 'kickpro' ),
					'frame_title' => __( 'Select File', 'kickpro' ),
					'frame_button' => __( 'Choose File', 'kickpro' ),
				)
			)
		) );
		
		// Preloader icon
		$wp_customize->add_setting( 'preloader_icon',
			array(
				'default' => $this->defaults['preloader_icon'],
				'transport' => 'refresh',
				'sanitize_callback' => 'absint'
			)
		);
		
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'preloader_icon',
			array(
				'label' => __( 'Preloader icon', 'kickpro' ),
				'description' => esc_html__( 'If you want to change the current loading icon, select it here.', 'kickpro' ),
				'section' => $section,
				'mime_type' => 'image',
				'button_labels' => array(
					'select' => __( 'Select File', 'kickpro' ),
					'change' => __( 'Change File', 'kickpro' ),
					'default' => __( 'Default', 'kickpro' ),
					'remove' => __( 'Remove', 'kickpro' ),
					'placeholder' => __( 'No file selected', 'kickpro' ),
					'frame_title' => __( 'Select File', 'kickpro' ),
					'frame_button' => __( 'Choose File', 'kickpro' ),
				)
			)
		) );
		
		// Header background image
		$wp_customize->add_setting( 'header_background_image',
			array(
				'default' => '',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint'
			)
		);
		
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'header_background_image',
			array(
				'label' => __( 'Header Background Image', 'kickpro' ),
				'description' => esc_html__( 'Header background image is intended for pages that are not created using Elementor.', 'kickpro' ),
				'section' => $section,
				'mime_type' => 'image',
				'button_labels' => array(
					'select' => __( 'Select File', 'kickpro' ),
					'change' => __( 'Change File', 'kickpro' ),
					'default' => __( 'Default', 'kickpro' ),
					'remove' => __( 'Remove', 'kickpro' ),
					'placeholder' => __( 'No file selected', 'kickpro' ),
					'frame_title' => __( 'Select File', 'kickpro' ),
					'frame_button' => __( 'Choose File', 'kickpro' ),
				)
			)
		) );

	}
	
	/**
	 * Register blog option controls
	 */
	
	public function awaiken_register_blog_options_controls( $wp_customize ) { 
			
		$section	=	'blog_options';

		// Blog page title 
		$wp_customize->add_setting( 'blog_page_title', array(
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'blog_page_title', array(
			'type' => 'text',
			'section' => $section,
			'label'       => esc_html__( 'Blog Page Title', 'kickpro' ),
		) );
		
		//Header Background Image
		$wp_customize->add_setting( 'blog_page_header_background_image',
			array(
				'default' => '',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint'
			)
		);
		
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'blog_page_header_background_image',
			array(
				'label' => __( 'Header Background Image', 'kickpro' ),
				'description' => esc_html__( 'Header background image for blog archive and single page.', 'kickpro' ),
				'section' => $section,
				'mime_type' => 'image',
				'button_labels' => array(
					'select' => __( 'Select File', 'kickpro' ),
					'change' => __( 'Change File', 'kickpro' ),
					'default' => __( 'Default', 'kickpro' ),
					'remove' => __( 'Remove', 'kickpro' ),
					'placeholder' => __( 'No file selected', 'kickpro' ),
					'frame_title' => __( 'Select File', 'kickpro' ),
					'frame_button' => __( 'Choose File', 'kickpro' ),
				)
			)
		) );
		
		// Archive page layout
		$wp_customize->add_setting( 'archive_page_layout', array(
		  'default' => $this->defaults['archive_page_layout'],
		   'sanitize_callback' => 'sanitize_text_field',
		) );
		
		$wp_customize->add_control( 'archive_page_layout', array(
			  'label'          => __( 'Archive Page Layout', 'kickpro' ),
			  'section' => $section,
			  'settings' => 'archive_page_layout',
			  'type' => 'radio',
			  'choices' => array(
				'full-width'   => __( 'Full Width', 'kickpro' ),
				'with-sidebar'  => __( 'With Sidebar', 'kickpro' )
			  ),
		) );
		
		// Archive page single page layout
		$wp_customize->add_setting( 'blog_single_page_layout', array(
		  'default' => $this->defaults['blog_single_page_layout'],
		   'sanitize_callback' => 'sanitize_text_field',
		) );
		
		$wp_customize->add_control( 'blog_single_page_layout', array(
			  'label'          => __( 'Blog Single Layout', 'kickpro' ),
			  'description' => esc_html__( 'Works with the Default Template only.', 'kickpro' ),
			  'section' => $section,
			  'settings' => 'blog_single_page_layout',
			  'type' => 'radio',
			  'choices' => array(
				'full-width'   => __( 'Full Width', 'kickpro' ),
				'with-sidebar'  => __( 'With Sidebar', 'kickpro' )
			  ),
		) );
		
		// Social Sharing
		$wp_customize->add_setting( 'social_sharing',
			array(
				'default' => $this->defaults['social_sharing'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_text_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Pill_Checkbox_Custom_Control( $wp_customize, 'social_sharing',
			array(
				'label' => __( 'Social Sharing', 'kickpro' ),
				'description' => esc_html__( 'Choose the social network you want to display in the social share box.', 'kickpro' ),
				'section' => $section,
				'input_attrs' => array(
					'sortable' => true,
					'fullwidth' => true,
				),
				'choices' => array(
					'facebook' => esc_attr__( 'Facebook', 'kickpro' ),
					'twitter' => esc_attr__( 'Twitter', 'kickpro' ),
					'whatsapp' => esc_attr__( 'Whatsapp', 'kickpro' ),
					'linkedin' => esc_attr__( 'LinkedIn', 'kickpro' ),
					'reddit' => esc_attr__( 'Reddit', 'kickpro' ),
					'tumblr' => esc_attr__( 'Tumblr', 'kickpro' ),
					'pinterest' => esc_attr__( 'Pinterest', 'kickpro' ),
					'vk' => esc_attr__( 'vk', 'kickpro' ),
					'email' => esc_attr__( 'Email', 'kickpro' ),
					'telegram' => esc_attr__( 'Telegram', 'kickpro' ),
				)
			)
		) );

	}

	/**
	 * Register 404 controls
	 */
	
	 public function awaiken_register_404_options_controls( $wp_customize ) { 
			
		$section	=	'404_options';
		
		// 404 Image
		$wp_customize->add_setting( 'not_found_image',
			array(
				'default' => '',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint'
			)
		);
		
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'not_found_image',
			array(
				'label' => __( '404 Image', 'kickpro' ),
				'section' => $section,
				'mime_type' => 'image',
				'button_labels' => array(
					'select' => __( 'Select File', 'kickpro' ),
					'change' => __( 'Change File', 'kickpro' ),
					'default' => __( 'Default', 'kickpro' ),
					'remove' => __( 'Remove', 'kickpro' ),
					'placeholder' => __( 'No file selected', 'kickpro' ),
					'frame_title' => __( 'Select File', 'kickpro' ),
					'frame_button' => __( 'Choose File', 'kickpro' ),
				)
			)
		) );
		
		// 404 Heading
		$wp_customize->add_setting( 'not_found_heading',
			array(
				'default' => $this->defaults['not_found_heading'],
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_kses_post'
			)
		);
		$wp_customize->add_control( 'not_found_heading',
			array(
				'label' => esc_html__( '404 Heading', 'kickpro' ),
				'section' => $section,
				'type' => 'text',

			)
		);
		
		// 404 text
		$wp_customize->add_setting( 'not_found_text',
			array(
				'default' => $this->defaults['not_found_text'],
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_kses_post'
			)
		);
		$wp_customize->add_control( 'not_found_text',
			array(
				'label' => esc_html__( '404 Text', 'kickpro' ),
				'section' => $section,
				'type' => 'textarea',
			)
		);
	}
	
	/**
	 * Register footer controls
	 */
	
	public function awaiken_register_footer_options_controls( $wp_customize ) { 
		
		$section	=	'footer_options';
		
		//Footer logo
		$wp_customize->add_setting( 'footer_logo',
			array(
				'default' => '',
				'transport' => 'refresh',
				'sanitize_callback' => 'absint'
			)
		);
		
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'footer_logo',
			array(
				'label' => __( 'Footer Logo', 'kickpro' ),
				'section' => $section,
				'mime_type' => 'image',
				'button_labels' => array(
					'select' => __( 'Select File', 'kickpro' ),
					'change' => __( 'Change File', 'kickpro' ),
					'default' => __( 'Default', 'kickpro' ),
					'remove' => __( 'Remove', 'kickpro' ),
					'placeholder' => __( 'No file selected', 'kickpro' ),
					'frame_title' => __( 'Select File', 'kickpro' ),
					'frame_button' => __( 'Choose File', 'kickpro' ),
				)
			)
		) );
		
		// Copyright text
		$wp_customize->add_setting( 'footer_copyright_text',
			array(
				'default' => $this->defaults['footer_copyright_text'],
				'transport' => 'refresh',
				'sanitize_callback' => 'wp_kses_post'
			)
		);
		$wp_customize->add_control( 'footer_copyright_text',
			array(
				'label' => __( 'Copyright Text', 'kickpro' ),
				'section' => $section,
				'type' => 'textarea',
			)
		);
		
		// Social media URLs
		$wp_customize->add_setting( 'social_urls',
			array(
				'default' => $this->defaults['social_urls'],
				'transport' => 'refresh',
				'sanitize_callback' => 'skyrocket_url_sanitization'
			)
		);
		$wp_customize->add_control( new Skyrocket_Sortable_Repeater_Custom_Control( $wp_customize, 'social_urls',
			array(
				'label' => __( 'Social URLs', 'kickpro' ),
				'description' => esc_html__( 'Enter the social profile URLs.', 'kickpro' ),
				'section' => $section,
				'button_labels' => array(
					'add' => __( 'Add Row', 'kickpro' ),
				)
			)
		) );
		
	}
	
}

/**
 * Load all our Customizer Custom Controls
 */
require_once KICKPRO_THEME_DIR . '/inc/customizer/custom-controls.php';

/**
 * Initialise our Customizer settings
 */
$awaiken_settings = new awaiken_initialise_customizer_settings();
