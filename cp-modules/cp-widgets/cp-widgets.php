<?php
require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/cp-widgets/bp-show-friends.php' );
require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/cp-widgets/buddypress-widget-pack.php' );
function enhanced_buddypress_widgets_init() {
	require( dirname( __FILE__ ) . '/enhanced-buddypress-widgets-bp-functions.php' );
}
add_action( 'bp_init', 'enhanced_buddypress_widgets_init' );
?>