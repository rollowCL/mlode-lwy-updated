<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('admin_head', 'kickpro_admin_head');
function kickpro_admin_head() {
  echo '<style>
    .ocdi-install-plugins-content-content,
.ocdi-install-plugins-content-header,
.ocdi-imported-footer a:first-of-type
{
		    display: none;
	}
  </style>';
}

function kickpro_ocdi_before_content_import( $selected_import ) {
	update_option( 'elementor_experiment-e_font_icon_svg', 'inactive' );
	update_option( 'elementor_experiment-nested-elements', 'active' );
	update_option( 'elementor_experiment-e_optimized_markup', 'active' );
	update_option( 'elementor_experiment-e_lazyload', 'inactive' );
	update_option( 'elementor_experiment-e_element_cache', 'inactive' );
	update_option( 'elementor_element_cache_ttl', 'disable' );
	update_option( 'elementor_local_google_fonts', '0' );
	update_option( 'elementor_unfiltered_files_upload', '1' );
}
add_action( 'ocdi/before_content_import', 'kickpro_ocdi_before_content_import' );


function kickpro_ocdi_plugin_intro_text( $default_text ) {
	
    $default_text = '<div class="ocdi__intro-text"><p>'.esc_html__( 'Importing demo data (post, pages, images, theme settings, etc.) is the quickest and easiest way to set up your new theme. It allows you to simply edit everything instead of creating content and layouts from scratch.', 'kickpro' ).'</p></div>';

    return $default_text;
}
add_filter( 'ocdi/plugin_intro_text', 'kickpro_ocdi_plugin_intro_text' );

function kickpro_get_demo_config() {
    return [
        'main' => [
            'Demo' => [
                'slug' => '',
				'key'   => '',
                'thumb' => 'demo.jpg',
            ],
        ]
    ];
}


function kickpro_ocdi_import_files() {
    $base_cdn = 'https://cdn.awaikenthemes.com/demo-content/kickpro';
    $base_demo = 'https://demo.awaikenthemes.com/kickpro';

    $demos = kickpro_get_demo_config();

    $demo_lists = [];

    foreach ($demos['main'] as $name => $info) {
        $slug = $info['slug'];
        $demo_lists[] = [
            'import_file_name' => $name,
            'import_file_url' => $base_cdn.($slug ? "/".$slug : "")."/kickpro.xml",
            'import_customizer_file_url' => $base_cdn . ($slug ? "/".$slug : "")."/kickpro.dat",
            'import_preview_image_url' => $base_demo."/assets/".$info['thumb'],
            'preview_url' => $slug ? $base_demo."/".$slug."/" : $base_demo."/",
        ];
    }
	
    return $demo_lists;
}
add_filter('ocdi/import_files', 'kickpro_ocdi_import_files');


function kickpro_theme_replace_fonts_meta_urls($old_url, $new_url) {
    if (empty($old_url) || empty($new_url) || $old_url === $new_url) {
        return; // Avoid processing if input is invalid
    }

    $meta_keys = ['fonts-data', 'fonts-face'];

    $query = new WP_Query([
        'post_type'      => 'bsf_custom_fonts',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids', // Return only post IDs for better performance
        'no_found_rows'  => true,  // Avoid unnecessary COUNT query
        'cache_results'  => false, // Save memory
    ]);

    foreach ($query->posts as $post_id) {
        foreach ($meta_keys as $meta_key) {
            $meta_value = get_post_meta($post_id, $meta_key, true);
            if (!$meta_value) {
                continue;
            }

            $unserialized = maybe_unserialize($meta_value);

            if (is_array($unserialized) || is_object($unserialized)) {
                $updated = kickpro_theme_recursive_replace($old_url, $new_url, $unserialized);
                if ($updated !== $unserialized) {
                    update_post_meta($post_id, $meta_key, $updated);
                }
            } elseif (is_string($meta_value)) {
                $replaced = str_replace($old_url, $new_url, $meta_value);
                if ($replaced !== $meta_value) {
                    update_post_meta($post_id, $meta_key, $replaced);
                }
            }
        }
    }

    wp_reset_postdata();
}

function kickpro_theme_recursive_replace($search, $replace, $data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = kickpro_theme_recursive_replace($search, $replace, $value);
        }
    } elseif (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->$key = kickpro_theme_recursive_replace($search, $replace, $value);
        }
    } elseif (is_string($data)) {
        $data = str_replace($search, $replace, $data);
    }
    return $data;
}

function kickpro_ocdi_after_import_setup( $selected_import ) {
	global $wpdb;
	
	$main_demo_imported = false;

	//Update the class 
	$demo_config = kickpro_get_demo_config();
	$selected_name = $selected_import['import_file_name'];
	if ( isset( $demo_config['main'][ $selected_name ] ) ) {
		
		$slug = $demo_config['main'][ $selected_name ]['slug'];
		$key  = $demo_config['main'][ $selected_name ]['key'];

		$old_url = 'https://demo.awaikenthemes.com/kickpro' . ( $slug ? "/".$slug : '' );
		update_option( 'kickpro_active_demo', $key, 'no' );
		$main_demo_imported = true;
		
		// Assign menus to their locations.
		$header_menu = get_term_by( 'name', 'Header Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );
		update_option( 'kickpro_demo_imported', 1, 'no' );
		
	}
	
	if( $main_demo_imported ) {
		if( isset($header_menu->term_id) ){
			set_theme_mod( 'nav_menu_locations', array(
					'header' => $header_menu->term_id,
				)
			);
		}
		
		if( isset($footer_menu->term_id) ){
			set_theme_mod( 'nav_menu_locations', array(
					'footer' => $footer_menu->term_id
				)
			);
		}
		
		// Get the front page.
		$front_page = get_posts(
			[
				'post_type'              => 'page',
				'title'                  => 'Home',
				'post_status'            => 'all',
				'numberposts'            => 1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			]
		);
		 
		if ( ! empty( $front_page ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_page[0]->ID );
		}
		  
		// Get the blog page.
		$blog_page = get_posts(
			[
			  'post_type'              => 'page',
			  'title'                  => 'Blog',
			  'post_status'            => 'all',
			  'numberposts'            => 1,
			  'update_post_term_cache' => false,
			  'update_post_meta_cache' => false,
			]
		);
		
		if ( ! empty( $blog_page ) ) {
			update_option( 'page_for_posts', $blog_page[0]->ID );
		 }
		
		
		// Get elementor Kit.
		$kit_page = get_posts(
			[
			  'post_type'              => 'elementor_library',
			  'title'                  => 'Kickpro - Default Kit',
			  'post_status'            => 'all',
			  'numberposts'            => 1,
			  'update_post_term_cache' => false,
			  'update_post_meta_cache' => false,
			]
		);
		
		if ( ! empty( $kit_page ) ) {
			update_option( 'elementor_active_kit', $kit_page[0]->ID );
		}
		  
		  // Get the current site's home URL without trailing slash
		$new_url = untrailingslashit( home_url() );
		
		
		// Replace in _elementor_data (postmeta)
		$escaped_from = str_replace( '/', '\\/', $old_url );
		$escaped_to = str_replace( '/', '\\/', $new_url );
		$meta_value_like = '[%'; // meta_value LIKE '[%' are json formatted

		$rows_affected = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} " .
				'SET `meta_value` = REPLACE(`meta_value`, %s, %s) ' .
				"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE %s;",
				$escaped_from,
				$escaped_to,
				$meta_value_like
			)
		);

		// Replace in custom menu item links (with and without trailing slash)
		$menu_items = get_posts( array(
			'post_type'      => 'nav_menu_item',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		) );

		if ( ! empty( $menu_items ) && is_array( $menu_items ) ) {
			foreach ( $menu_items as $menu_item_id ) {

				// Get the current custom URL from menu item
				$url = get_post_meta( $menu_item_id, '_menu_item_url', true );

				// Skip if no URL or it's not a valid string
				if ( empty( $url ) || ! is_string( $url ) ) {
					continue;
				}

				// Replace old URL with new one (handling both with and without trailing slash)
				$new_link = str_replace(
					array( untrailingslashit( $old_url ), trailingslashit( $old_url ) ),
					array( untrailingslashit( $new_url ), trailingslashit( $new_url ) ),
					$url
				);

				// Only update if something changed
				if ( $new_link !== $url ) {
					update_post_meta( $menu_item_id, '_menu_item_url', esc_url_raw( $new_link ) );
				}
			}
		}
		
		kickpro_theme_replace_fonts_meta_urls( $old_url, $new_url );

		// Check if Elementor is active
		if ( did_action( 'elementor/loaded' ) ) {
			// Regenerate CSS files
			\Elementor\Plugin::instance()->files_manager->clear_cache();
		}
	} //$main_demo_imported

}
add_action( 'ocdi/after_import', 'kickpro_ocdi_after_import_setup' );