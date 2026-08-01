<?php 
/*
Plugin Name:  Kickpro Theme Addons
Plugin URI:   https://awaikenthemes.com
Description:  This plugin is intended for use with the kickpro theme.
Version:      1.0.1
Author:       Awaiken Technology
Author URI:   https://awaiken.com
License:      GNU General Public License v3 or later.
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  kickpro-theme-addons
Domain Path:  /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'KICKPRO_ADDONS_URL', plugins_url( '/', __FILE__ ) );
define( 'KICKPRO_ADDONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'KICKPRO_PLUGIN_VERSION', '1.0.1' );


// Load translation.
add_action( 'init', 'kickpro_i18n' );

/**
 * Load the plugin text domain for translation.
 *
 * @since    1.0.0
 */
function kickpro_i18n() {
	load_plugin_textdomain( 'kickpro-theme-addons' );
}

/* Allow SVG upload */
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
      'ext' => $filetype['ext'],
      'type' => $filetype['type'],
      'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function kickpro_allow_svg_upload( $mimes ) {
  $mimes['svg'] = 'image/svg+xml';
  $mimes['svgz'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'kickpro_allow_svg_upload' );

require KICKPRO_ADDONS_PATH . 'includes/secondary-image.php';