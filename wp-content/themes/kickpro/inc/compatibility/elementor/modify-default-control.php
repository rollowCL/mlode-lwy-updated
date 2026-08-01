<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'at_add_heading_animation_style_control' ) ) {
	function at_add_heading_animation_style_control( $element, $args ) {
			$element->add_control(
					'at_animation_heading_style',
					[
						'label' => __( 'Animation', 'kickpro' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'' => __( 'None', 'kickpro' ),
							'style-1' => __( 'Style 1', 'kickpro' ),
							'style-2' => __( 'Style 2', 'kickpro' ),
							'style-3' => __( 'Style 3', 'kickpro' ),
							'style-4' => __( 'Style 4', 'kickpro' ),
						],
						'prefix_class' => 'at-heading-animation at-animation-heading-',
						'default' => 'none',
					]
				);
	}
	
add_action( 'elementor/element/heading/section_title/before_section_end', 'at_add_heading_animation_style_control', 10, 2 );
add_action( 'elementor/element/elementskit-heading/ekit_heading_section_title/before_section_end', 'at_add_heading_animation_style_control', 10, 2 );
}

// Add animation control to elementor image widget
add_action( 'elementor/element/image/section_image/before_section_end', function( $element, $args ) {

	$element->add_control(
		'at_animation_image_style',
		[
			'label' => __( 'Animation', 'kickpro' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'' => __( 'None', 'kickpro' ),
				'style-1' => __( 'Reveal Style 1', 'kickpro' ),
			],
			'prefix_class' => 'at-image-animation at-animation-image-',
			'default' => 'none',
		]
	);
	
	$element->add_control(
		'at_animation_hover_effect',
		[
			'label' => __( 'Hover Effect', 'kickpro' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'' => __( 'None', 'kickpro' ),
				'shiny-glass-effect' => __( 'Shiny Glass', 'kickpro' ),
				'distortion-effect' => __( 'Distortion', 'kickpro' ),
			],
			'prefix_class' => 'at-',
			'default' => 'none',
		]
	);
	
}, 10, 2 );