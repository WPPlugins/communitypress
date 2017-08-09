<?php
// JLL_MOD - Removed Plugin Header


define ('BPFB_PLUGIN_SELF_DIRNAME', basename(dirname(__FILE__)), true);

// JLL_MOD - fixed file dir path
//Setup proper paths/URLs and load text domains
if (is_multisite() && defined('WPMU_PLUGIN_URL') && defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/communitypress/cp-modules/' . basename(__FILE__))) {
	define ('BPFB_PLUGIN_LOCATION', 'mu-plugins', true);
	define ('BPFB_PLUGIN_BASE_DIR', WPMU_PLUGIN_DIR . '/communitypress/cp-modules', true);
	define ('BPFB_PLUGIN_URL', WPMU_PLUGIN_URL . '/communitypress/cp-modules', true);
	$textdomain_handler = 'load_muplugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/communitypress/cp-modules/' . BPFB_PLUGIN_SELF_DIRNAME . '/' . basename(__FILE__))) {
	define ('BPFB_PLUGIN_LOCATION', 'subfolder-plugins', true);
	define ('BPFB_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/communitypress/cp-modules/' . BPFB_PLUGIN_SELF_DIRNAME, true);
	define ('BPFB_PLUGIN_URL', WP_PLUGIN_URL . '/communitypress/cp-modules/' . BPFB_PLUGIN_SELF_DIRNAME, true);
	$textdomain_handler = 'load_plugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/communitypress/cp-modules/' . basename(__FILE__))) {
	define ('BPFB_PLUGIN_LOCATION', 'plugins', true);
	define ('BPFB_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/communitypress/cp-modules', true);
	define ('BPFB_PLUGIN_URL', WP_PLUGIN_URL . '/communitypress/cp-modules', true);
	$textdomain_handler = 'load_plugin_textdomain';
} else {
	// No textdomain is loaded because we can't determine the plugin location.
	// No point in trying to add textdomain to string and/or localizing it.
	wp_die(__('There was an issue determining where Google Maps plugin is installed. Please reinstall.'));
}
$textdomain_handler('bpfb', false, BPFB_PLUGIN_SELF_DIRNAME . '/languages/');








// Override oEmbed width in wp-config.php
if (!defined('BPFB_OEMBED_WIDTH')) define('BPFB_OEMBED_WIDTH', 450, true);


$wp_upload_dir = wp_upload_dir();
define('BPFB_TEMP_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/tmp/', true);
define('BPFB_TEMP_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/tmp/', true);
define('BPFB_BASE_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/', true);
define('BPFB_BASE_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/', true);


// Hook up the installation routine and check if we're really, really set to go
require_once BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_installer.php';
register_activation_hook(__FILE__, array(BpfbInstaller, 'install'));
BpfbInstaller::check();


/**
 * Helper functions for going around the fact that
 * BuddyPress is NOT multisite compatible.
 */
function bpfb_get_image_url ($blog_id) {
	if (!defined('BP_ENABLE_MULTIBLOG') || !BP_ENABLE_MULTIBLOG) return BPFB_BASE_IMAGE_URL;
	if (!$blog_id) return BPFB_BASE_IMAGE_URL;
	switch_to_blog($blog_id);
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();
	return $wp_upload_dir['baseurl'] . '/bpfb/';
}
function bpfb_get_image_dir ($blog_id) {
	if (!defined('BP_ENABLE_MULTIBLOG') || !BP_ENABLE_MULTIBLOG) return BPFB_BASE_IMAGE_DIR;
	if (!$blog_id) return BPFB_BASE_IMAGE_DIR;
	switch_to_blog($blog_id);
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();
	return $wp_upload_dir['basedir'] . '/bpfb/';
}


/**
 * Includes the core requirements and serves the improved activity box.
 */
function bpfb_plugin_init () {
	require_once(BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_binder.php');
	require_once(BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_codec.php');
	// Group Documents integration
	if (defined('BP_GROUP_DOCUMENTS_IS_INSTALLED') && BP_GROUP_DOCUMENTS_IS_INSTALLED) {
		require_once(BPFB_PLUGIN_BASE_DIR . '/lib/bpfb_group_documents.php');
	}
	do_action('bpfb_init');
	BpfbBinder::serve();
}
// Only fire off if BP is actually loaded.
add_action('bp_loaded', 'bpfb_plugin_init');