<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Secondary Image Meta box for post, page
*/
class kickpro_secondary_image_meta_box
{

    public static function add()
    {
        // define post types to show
        $screens = ['post', 'page'];

        foreach ($screens as $screen) {

            add_meta_box(
                'awaiken_secondary_image',
                __('Header Background Image', 'kickpro-theme-addons'),
                [self::class, 'post_page_icon_html'],
                $screen,
                'side',
                'high'
            );

          
        }
    }

    public static function post_page_icon_html( $post ) {
		self::load_assets();

		$image_url = self::get_meta( $post, 'awaiken_secondary_image' );
		$image_id  = attachment_url_to_postid( $image_url ); // Get attachment ID from URL
		$remove_icon_style = $image_url ? 'display:block' : 'display:none';
		?>
		<div class="aw-uploader">
			<p><?php esc_html_e( 'Header background image is intended for pages that are not created using Elementor.', 'kickpro-theme-addons' ); ?></p>
			<?php echo wp_nonce_field( 'awaiken_secondary_image_nonce_action', 'awaiken_secondary_image_nonce_name', true, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p>
				<input type="hidden" name="awaiken_secondary_image" id="post_page_icon_hidden" class="meta-image" value="<?php echo esc_url( $image_url ); ?>">
				<input type="button" class="button image-upload" value="<?php esc_attr_e( 'Choose Image', 'kickpro-theme-addons' ); ?>">
			</p>
			<div class="image-preview">
				<span id="remove_post_page_icon" style="cursor:pointer;<?php echo esc_attr( $remove_icon_style ); ?>">X</span>
				<?php
				if ( $image_id ) {
					echo wp_get_attachment_image( $image_id, 'medium', false, array( 'id' => 'post_page_icon_preview', 'style' => 'width:200px;' ) );
				}
				?>
			</div>
		</div>
		<?php
	}

   
    public static function save($post_id)
    {
		
		// check nonce
		if ( ! isset( $_POST['awaiken_secondary_image_nonce_name'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awaiken_secondary_image_nonce_name'] )), 'awaiken_secondary_image_nonce_action' )  ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
      
		if ( array_key_exists( 'awaiken_secondary_image', $_POST ) ) {

			update_post_meta(
				$post_id,
				'awaiken_secondary_image',
				esc_url_raw(sanitize_text_field( wp_unslash($_POST['awaiken_secondary_image'])))
			);
		}
      
    }

    public static function load_assets()
    {
        wp_enqueue_script('awaiken-meta-media', KICKPRO_ADDONS_URL . 'assets/js/meta-media.js', '', KICKPRO_PLUGIN_VERSION, true);
    }

    public static function get_meta($post, $fieldname)
    {
        if (isset($post) && !empty($fieldname)) {

            return get_post_meta($post->ID, $fieldname, true);

        }

    }

}
add_action('add_meta_boxes', ['kickpro_secondary_image_meta_box', 'add']);
add_action('save_post', ['kickpro_secondary_image_meta_box', 'save']);