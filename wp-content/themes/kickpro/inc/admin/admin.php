<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function kickpro_admin_css() {
	wp_enqueue_style( 'theme-default-font-admin', kickpro_slug_fonts_url(), array(), null );
	wp_enqueue_style( 'kickpro-admin', KICKPRO_THEME_URL . '/assets/css/admin.css', array(), KICKPRO_THEME_VERSION );	
	
	$documentation_link = apply_filters('kickpro_documentation_link', true);

    if ($documentation_link) {
		wp_enqueue_script( 'kickpro-admin-js', KICKPRO_THEME_URL . '/assets/js/admin.js', array( 'jquery' ), KICKPRO_THEME_VERSION, true );    
    }
	
}

// Hook the custom_admin_css function to the admin_enqueue_scripts action.
add_action('admin_enqueue_scripts', 'kickpro_admin_css', 11);

add_action('admin_menu', 'kickpro_custom_appearance_submenu');

function kickpro_custom_appearance_submenu() {
	
    $documentation_link = apply_filters('kickpro_documentation_link', true);

    if (!$documentation_link) {
        return;
    }
	
    add_submenu_page(
        'themes.php', 
        __( 'Documentation', 'kickpro' ), 
        __( 'Documentation', 'kickpro' ), 
        'manage_options', 
        'custom_documentation_link', 
        '__return_null' 
    );
}