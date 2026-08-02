<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
* Lets a non-technical admin edit the option list shown in the "subject"
* select field of the "Conact Us Form" (Contact Form 7, form ID 3108)
* without touching the raw CF7 shortcode syntax.
*
* This form is reused on multiple pages (Kontakt, Pricing Plan, Home
* variants, team member pages), so this is a single, centralized settings
* screen under Contact Form 7's own admin menu ("wpcf7").
*
* Each option has a stable, URL-safe id (e.g. "1,Travel A") so a page can
* link to the contact form with a subject preselected via ?subjectId=1
* without depending on the label text surviving URL-encoding (Polish
* diacritics, spaces, renames, etc.) - see filter_subject_tag() and
* print_preselect_script().
*/
class kickpro_contact_subject_options
{
	const OPTION_NAME       = 'kickpro_contact_subject_settings';
	const OPTION_GROUP      = 'kickpro_contact_subject_group';
	const PAGE_SLUG         = 'kickpro-contact-subject-options';
	const TARGET_FORM_ID    = 3108; // "Conact Us Form"
	const TARGET_FIELD_NAME = 'subject';

	/**
	 * Whether filter_subject_tag() actually matched and overrode the
	 * target field on the page currently being rendered. Used by
	 * print_preselect_script() to only print the preselect script on
	 * pages that actually contain the form.
	 */
	protected static $rendered_on_page = false;

	/**
	 * Default label + options, matching the current hard-coded shortcode:
	 * [select* subject id:subject first_as_label "Wybierz temat" "Informacje o treningach" "Zapisy na treningi" "Obóz wakacyjny w Kątach Rybackich"]
	 */
	public static function get_defaults()
	{
		return array(
			'label'   => 'Wybierz temat',
			'options' => array(
				array( 'id' => '1', 'label' => 'Informacje o treningach' ),
				array( 'id' => '2', 'label' => 'Zapisy na treningi' ),
				array( 'id' => '3', 'label' => 'Obóz wakacyjny w Kątach Rybackich' ),
			),
		);
	}

	/**
	 * Saved settings, merged with defaults so a partially-saved/empty
	 * option can never break the live form.
	 */
	public static function get_settings()
	{
		$saved = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$defaults = self::get_defaults();
		$settings = wp_parse_args( $saved, $defaults );

		$settings['options'] = self::normalize_options( $settings['options'] );

		if ( empty( $settings['options'] ) ) {
			$settings['options'] = $defaults['options'];
		}

		if ( '' === trim( (string) $settings['label'] ) ) {
			$settings['label'] = $defaults['label'];
		}

		return $settings;
	}

	/**
	 * Accepts either the current {id, label} shape or the older plain
	 * string list this option used before ids existed, and always
	 * returns a clean, uniquely-id'd list. Legacy string entries get
	 * sequential numeric ids assigned on the fly (not persisted until
	 * the admin next saves the settings page).
	 */
	protected static function normalize_options( $options )
	{
		if ( ! is_array( $options ) ) {
			return array();
		}

		$normalized  = array();
		$used_ids    = array();
		$next_legacy = 1;

		foreach ( $options as $option ) {
			if ( is_array( $option ) && isset( $option['id'], $option['label'] ) ) {
				$id    = (string) $option['id'];
				$label = (string) $option['label'];
			} elseif ( is_string( $option ) ) {
				// Legacy format: a plain label with no id.
				while ( isset( $used_ids[ (string) $next_legacy ] ) ) {
					$next_legacy++;
				}
				$id = (string) $next_legacy;
				$next_legacy++;
				$label = $option;
			} else {
				continue;
			}

			$id    = trim( $id );
			$label = trim( wp_strip_all_tags( $label ) );

			if ( '' === $id || '' === $label || isset( $used_ids[ $id ] ) ) {
				continue;
			}

			$used_ids[ $id ] = true;
			$normalized[]    = array( 'id' => $id, 'label' => $label );
		}

		return $normalized;
	}

	/* ---------------------------------------------------------------- */
	/* Admin menu + Settings API                                        */
	/* ---------------------------------------------------------------- */

	public static function add_menu()
	{
		add_submenu_page(
			'wpcf7',
			__( 'Contact Subject Options', 'kickpro-theme-addons' ),
			__( 'Subject Options', 'kickpro-theme-addons' ),
			'wpcf7_edit_contact_forms',
			self::PAGE_SLUG,
			array( self::class, 'render_page' )
		);
	}

	public static function register_settings()
	{
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			'kickpro_contact_subject_section',
			__( 'Dropdown Options', 'kickpro-theme-addons' ),
			array( self::class, 'section_intro_html' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'kickpro_contact_subject_label',
			__( 'Placeholder / Label', 'kickpro-theme-addons' ),
			array( self::class, 'label_field_html' ),
			self::PAGE_SLUG,
			'kickpro_contact_subject_section'
		);

		add_settings_field(
			'kickpro_contact_subject_options',
			__( 'Options (one per line)', 'kickpro-theme-addons' ),
			array( self::class, 'options_field_html' ),
			self::PAGE_SLUG,
			'kickpro_contact_subject_section'
		);
	}

	public static function section_intro_html()
	{
		?>
		<p>
			<?php esc_html_e( 'These options control the "Wybierz temat" dropdown on the contact form.', 'kickpro-theme-addons' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Enter one option per line, as: id,Label - for example "4,Travel A". The id must be unique and only use letters, numbers, - or _ (no commas, no spaces). The label is the text visitors see and what appears in the notification email; the id never appears to visitors, it only exists so a link elsewhere on the site can preselect this option via ?subjectId=4. Blank lines are ignored.', 'kickpro-theme-addons' ); ?>
		</p>
		<?php
	}

	public static function label_field_html()
	{
		$settings = self::get_settings();
		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[label]"
			value="<?php echo esc_attr( $settings['label'] ); ?>"
		/>
		<p class="description">
			<?php esc_html_e( 'Placeholder text shown as the first, non-selectable option in the dropdown.', 'kickpro-theme-addons' ); ?>
		</p>
		<?php
	}

	public static function options_field_html()
	{
		$settings = self::get_settings();
		$lines    = array_map(
			static function ( $option ) {
				return $option['id'] . ',' . $option['label'];
			},
			$settings['options']
		);
		?>
		<textarea
			class="large-text"
			rows="8"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[options]"
		><?php echo esc_textarea( implode( "\n", $lines ) ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Format: id,Label - one per line (e.g. "4,Travel A").', 'kickpro-theme-addons' ); ?>
		</p>
		<?php
	}

	public static function render_page()
	{
		if ( ! current_user_can( 'wpcf7_edit_contact_forms' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Contact Form Subject Options', 'kickpro-theme-addons' ); ?></h1>
			<p>
				<?php
				printf(
					/* translators: %d: Contact Form 7 form ID */
					esc_html__( 'Edit the "Wybierz temat" dropdown options used by the contact form (form ID %d). This form appears on the Kontakt page and is reused on several other pages, so changes here apply everywhere it is used - no shortcode editing required.', 'kickpro-theme-addons' ),
					self::TARGET_FORM_ID
				);
				?>
			</p>
			<?php settings_errors(); ?>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button( __( 'Save Options', 'kickpro-theme-addons' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitize the submitted settings.
	 *
	 * - label: plain text, falls back to the previously saved (or default)
	 *   label if left empty.
	 * - options: textarea split on newlines; each non-blank line must be
	 *   "id,Label". Invalid lines (no comma, empty id, empty label,
	 *   duplicate id, or an id with characters outside [A-Za-z0-9_-]) are
	 *   dropped rather than rejecting the whole save. Falls back to the
	 *   previously saved (or default) list if nothing valid remains, so
	 *   the live form can never end up with an empty dropdown.
	 *
	 * Must be idempotent: WordPress (via some hook in this stack - a
	 * global "re-sanitize changed options" listener, not something in
	 * this plugin) calls the sanitize_option filter a second time within
	 * the same save, feeding it this function's own previous return
	 * value instead of the raw textarea string. So besides the normal
	 * "id,Label\nid,Label" string, options may also arrive already as a
	 * list of {id, label} arrays - that shape is validated directly
	 * instead of being misread as text lines (which would wipe the list).
	 */
	public static function sanitize( $input )
	{
		$previous = self::get_settings();

		$label = isset( $input['label'] ) ? sanitize_text_field( wp_unslash( $input['label'] ) ) : '';

		if ( '' === $label ) {
			$label = $previous['label'];
		}

		$raw_options = isset( $input['options'] ) ? wp_unslash( $input['options'] ) : '';

		if ( is_array( $raw_options ) && isset( $raw_options[0] ) && is_array( $raw_options[0] )
			&& array_key_exists( 'id', $raw_options[0] ) && array_key_exists( 'label', $raw_options[0] )
		) {
			// Already-normalized {id, label} shape (re-entrant sanitize call).
			$options = self::validate_options( $raw_options );

			if ( empty( $options ) ) {
				$options = $previous['options'];
				add_settings_error(
					self::OPTION_NAME,
					'kickpro_subject_options_empty',
					__( 'The options list cannot be empty. The previous list was kept.', 'kickpro-theme-addons' )
				);
			}

			return array(
				'label'   => $label,
				'options' => array_values( $options ),
			);
		}

		// Normal case: a plain textarea string, "id,Label" per line.
		$lines = is_array( $raw_options ) ? $raw_options : preg_split( '/\r\n|\r|\n/', (string) $raw_options );

		$options       = array();
		$used_ids      = array();
		$had_bad_lines = false;

		foreach ( (array) $lines as $line ) {
			if ( is_array( $line ) ) {
				continue; // Ignore unexpectedly nested values.
			}

			$line = trim( wp_strip_all_tags( (string) $line ) );

			if ( '' === $line ) {
				continue;
			}

			$parts = explode( ',', $line, 2 );

			if ( 2 !== count( $parts ) ) {
				$had_bad_lines = true;
				continue;
			}

			$id    = trim( $parts[0] );
			$label_text = trim( $parts[1] );

			if ( '' === $id || '' === $label_text || ! preg_match( '/^[A-Za-z0-9_-]+$/', $id ) || isset( $used_ids[ $id ] ) ) {
				$had_bad_lines = true;
				continue;
			}

			$used_ids[ $id ] = true;
			$options[]       = array( 'id' => $id, 'label' => $label_text );
		}

		if ( $had_bad_lines ) {
			add_settings_error(
				self::OPTION_NAME,
				'kickpro_subject_options_bad_line',
				__( 'Some lines were skipped: each line must be "id,Label", the id must be unique and use only letters, numbers, - or _.', 'kickpro-theme-addons' )
			);
		}

		if ( empty( $options ) ) {
			$options = $previous['options'];
			add_settings_error(
				self::OPTION_NAME,
				'kickpro_subject_options_empty',
				__( 'The options list cannot be empty. The previous list was kept.', 'kickpro-theme-addons' )
			);
		}

		return array(
			'label'   => $label,
			'options' => array_values( $options ),
		);
	}

	/**
	 * Validates an array already shaped as a list of {id, label} arrays
	 * (as opposed to raw textarea lines) - same rules as the per-line
	 * parser in sanitize(): id restricted to [A-Za-z0-9_-]+, unique,
	 * non-empty label. Invalid entries are dropped, not fatal.
	 */
	protected static function validate_options( $raw_options )
	{
		$options  = array();
		$used_ids = array();

		foreach ( $raw_options as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['id'], $entry['label'] ) ) {
				continue;
			}

			$id    = trim( (string) $entry['id'] );
			$label = trim( wp_strip_all_tags( (string) $entry['label'] ) );

			if ( '' === $id || '' === $label || ! preg_match( '/^[A-Za-z0-9_-]+$/', $id ) || isset( $used_ids[ $id ] ) ) {
				continue;
			}

			$used_ids[ $id ] = true;
			$options[]       = array( 'id' => $id, 'label' => $label );
		}

		return $options;
	}

	/* ---------------------------------------------------------------- */
	/* Contact Form 7 integration                                       */
	/* ---------------------------------------------------------------- */

	/**
	 * Overrides the values/labels of the [select* subject ...] form-tag
	 * in the target contact form only, using the saved settings instead
	 * of the hard-coded shortcode text.
	 *
	 * Runs on the 'wpcf7_form_tag' filter, which CF7 applies to every
	 * scanned form-tag (see WPCF7_FormTagsManager::replace_form_tags())
	 * before it becomes a WPCF7_FormTag object. We only touch the tag
	 * named "subject" of basetype "select", and only when it belongs to
	 * the target form (checked via WPCF7_ContactForm::get_current()),
	 * so no other select field or form on the site is affected.
	 *
	 * The "first_as_label" option on the shortcode is preserved: it is
	 * read directly off the shortcode markup by CF7's select handler at
	 * render time, so as long as index 0 of values/labels is the
	 * placeholder text (as it is here), the placeholder keeps behaving
	 * as the disabled/first option.
	 *
	 * The submitted field value (and what shows up in the notification
	 * email) stays the human-readable label - the per-option id is never
	 * sent to CF7 here, it is only used client-side, see
	 * print_preselect_script().
	 */
	public static function filter_subject_tag( $scanned_tag, $replace )
	{
		if ( empty( $scanned_tag['name'] ) || self::TARGET_FIELD_NAME !== $scanned_tag['name'] ) {
			return $scanned_tag;
		}

		if ( empty( $scanned_tag['basetype'] ) || 'select' !== $scanned_tag['basetype'] ) {
			return $scanned_tag;
		}

		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			return $scanned_tag;
		}

		$current_form = WPCF7_ContactForm::get_current();

		if ( ! $current_form || self::TARGET_FORM_ID !== (int) $current_form->id() ) {
			return $scanned_tag;
		}

		$settings = self::get_settings();
		$labels   = wp_list_pluck( $settings['options'], 'label' );

		$raw_values = array_merge( array( $settings['label'] ), $labels );

		// Mirror WPCF7_FormTagsManager::replace_form_tags()'s own
		// values/labels construction so behaviour stays identical to a
		// normally-authored shortcode (pipes, trimming, etc.).
		if ( class_exists( 'WPCF7_Pipes' ) && defined( 'WPCF7_USE_PIPE' ) && WPCF7_USE_PIPE ) {
			$pipes  = new WPCF7_Pipes( $raw_values );
			$values = $pipes->collect_befores();

			$scanned_tag['pipes'] = $pipes;
		} else {
			$values = $raw_values;

			$scanned_tag['pipes'] = null;
		}

		$scanned_tag['raw_values'] = $raw_values;
		$scanned_tag['values']     = array_map( 'trim', $values );
		$scanned_tag['labels']     = $scanned_tag['values'];

		self::$rendered_on_page = true;

		return $scanned_tag;
	}

	/**
	 * Prints a small inline script that preselects the subject dropdown
	 * from a ?subjectId=<id> URL parameter, e.g. linking to
	 * /kontakt/?subjectId=4 preselects whatever option currently has id
	 * "4" in Subject Options.
	 *
	 * Only prints on pages where filter_subject_tag() actually matched
	 * the target form (see $rendered_on_page), so pages without the
	 * contact form never get this markup. The id -> label map is passed
	 * as PHP-generated JSON (not decoded from the URL), so this works
	 * correctly regardless of accented characters or URL-encoding - the
	 * URL only ever needs to carry the plain-ASCII id.
	 *
	 * If the requested id doesn't exist (typo, removed option), the
	 * script does nothing and the placeholder stays selected.
	 */
	public static function print_preselect_script()
	{
		if ( ! self::$rendered_on_page ) {
			return;
		}

		$settings = self::get_settings();
		$map      = array();

		foreach ( $settings['options'] as $option ) {
			$map[ $option['id'] ] = $option['label'];
		}

		$json_flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
		?>
		<script>
		( function () {
			var subjectOptionMap = <?php echo wp_json_encode( $map, $json_flags ); ?>;
			document.addEventListener( 'DOMContentLoaded', function () {
				var params = new URLSearchParams( window.location.search );
				var subjectId = params.get( 'subjectId' );

				if ( ! subjectId || ! Object.prototype.hasOwnProperty.call( subjectOptionMap, subjectId ) ) {
					return;
				}

				var label = subjectOptionMap[ subjectId ];
				var selects = document.querySelectorAll( 'select#subject' );

				selects.forEach( function ( select ) {
					for ( var i = 0; i < select.options.length; i++ ) {
						if ( select.options[ i ].value === label ) {
							select.selectedIndex = i;
							select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
							break;
						}
					}
				} );
			} );
		} )();
		</script>
		<?php
	}
}

add_action( 'admin_menu', array( 'kickpro_contact_subject_options', 'add_menu' ), 20 );
add_action( 'admin_init', array( 'kickpro_contact_subject_options', 'register_settings' ) );
add_filter( 'wpcf7_form_tag', array( 'kickpro_contact_subject_options', 'filter_subject_tag' ), 10, 2 );
add_action( 'wp_footer', array( 'kickpro_contact_subject_options', 'print_preselect_script' ) );
