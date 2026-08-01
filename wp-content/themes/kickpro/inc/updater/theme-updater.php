<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Sample Theme
 */

// Includes the files needed for the theme updater
if ( ! class_exists( 'AWAIKEN_Theme_Updater_Admin' ) ) {
	include dirname( __FILE__ ) . '/theme-updater-admin.php';
}

// Loads the updater classes
$updater = new AWAIKEN_Theme_Updater_Admin(
	// Config settings
	array(
		'remote_api_url' => 'https://awaikenthemes.com/', // Site where EDD is hosted
		'tf_pc_val_api_url' => 'https://awaikenthemes.com/wp-json/themeforest/v1/validate-purchase', // Site where EDD is hosted
		'item_name'      => AWAIKEN_ITEM_NAME, // Name of theme
		'theme_slug'     => AWAIKEN_THEME_SLUG, // Theme slug
		'version'        => wp_get_theme( get_template() )->get( 'Version' ), // The current version of this theme
		'author'         => 'Awaiken Themes', // The author of this theme
		'download_id'    => '', // Optional, used for generating a license renewal link
		'renew_url'      => '', // Optional, allows for a custom license renewal link
		'beta'           => false, // Optional, set to true to opt into beta versions
		'item_id'        => AWAIKEN_ITEM_ID,
	),
	// Strings
	array(
		'activate-license-info'     => __( 'Please activate the theme license key to proceed.', 'kickpro' ),
		'maybe-later-btn'    	 => __( 'Maybe Later', 'kickpro' ),
		'dismiss-notice-btn'    	 => __( 'Dismiss this notice', 'kickpro' ),
		'theme-license'             => __( 'Theme License', 'kickpro' ),
		'enter-key-tf'              => __( 'Enter your Item Purchase Code. <a href="%s" rel="noopener noreferrer" target="_blank">Refer to the article for instructions on where to find it</a>.', 'kickpro' ),
		'enter-key'                 => __( 'Enter your theme license key.', 'kickpro' ),
		'license-key'               => __( 'License Key', 'kickpro' ),
		'license-buyer-email'       => __( 'Email', 'kickpro' ),
		'license-action'            => __( 'License Action', 'kickpro' ),
		'deactivate-license'        => __( 'Deactivate License', 'kickpro' ),
		'activate-license'          => __( 'Activate License', 'kickpro' ),
		'status-unknown'            => __( 'License status is unknown.', 'kickpro' ),
		'renew'                     => __( 'Renew?', 'kickpro' ),
		'unlimited'                 => __( 'unlimited', 'kickpro' ),
		'license-key-is-active'     => __( 'License key is active.', 'kickpro' ),
		/* translators: the license expiration date */
		'expires%s'                 => __( 'Expires %s.', 'kickpro' ),
		'expires-never'             => __( 'Lifetime License.', 'kickpro' ),
		/* translators: 1. the number of sites activated 2. the total number of activations allowed. */
		'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'kickpro' ),
		'activation-limit'          => __( 'Your license key has reached its activation limit.', 'kickpro' ),
		/* translators: the license expiration date */
		'license-key-expired-%s'    => __( 'License key expired %s.', 'kickpro' ),
		'license-key-expired'       => __( 'License key has expired.', 'kickpro' ),
		/* translators: the license expiration date */
		'license-expired-on'        => __( 'Your license key expired on %s.', 'kickpro' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'kickpro' ),
		'license-is-inactive'       => __( 'License is inactive.', 'kickpro' ),
		'license-key-is-disabled'   => __( 'License key is disabled.', 'kickpro' ),
		'license-key-invalid'       => __( 'Invalid license.', 'kickpro' ),
		'site-is-inactive'          => __( 'Your license is not active for this URL.', 'kickpro' ),
		/* translators: the theme name */
		'item-mismatch'             => __( 'This appears to be an invalid license key for %s.', 'kickpro' ),
		'license-status-unknown'    => __( 'License status is unknown.', 'kickpro' ),
		'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'kickpro' ),
		'error-generic'             => __( 'An error occurred, please try again.', 'kickpro' ),
		'pending-active'            => __( 'Click the Activate License button to activate the license.', 'kickpro' ),
	)
);
