<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once KICKPRO_THEME_DIR . '/inc/functions.php';
require_once KICKPRO_THEME_DIR . '/inc/compatibility/elementor/elementor.php';
require_once KICKPRO_THEME_DIR . '/inc/compatibility/elementor/modify-default-control.php';
require_once KICKPRO_THEME_DIR . '/inc/compatibility/elementskit-lite.php';
require_once KICKPRO_THEME_DIR . '/inc/breadcrumbs.php';
require_once KICKPRO_THEME_DIR . '/inc/customizer/customizer.php';


if(is_admin()) {
	// Admin related functions
	require_once KICKPRO_THEME_DIR . '/inc/admin/admin.php';
	if(kickpro_license_valid()){ 
		require_once KICKPRO_THEME_DIR . '/inc/ocdi.php';
		require_once KICKPRO_THEME_DIR . '/inc/required-plugins.php';
	}
	
	//Load theme updater functions
	function kickpro_theme_updater() {
		$theme_updater = apply_filters( 'kickpro_theme_updater_enabled', true );
		if($theme_updater) {
			require_once KICKPRO_THEME_DIR . '/inc/updater/theme-updater.php';
		}
	}
	add_action( 'after_setup_theme', 'kickpro_theme_updater' );
	
}