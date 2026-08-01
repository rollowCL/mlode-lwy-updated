<?php

defined( 'ABSPATH' ) || exit;

function kickpro_child_theme_enqueue_styles() {
	wp_enqueue_style( 'kickpro-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'kickpro-style' ), KICKPRO_THEME_VERSION ); 
}
add_action( 'wp_enqueue_scripts', 'kickpro_child_theme_enqueue_styles', 999 );
