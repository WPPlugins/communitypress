<?php
// JLL_MOD - Removed Plugin header

/* Load the plugin once BuddyPress is loaded*/
function my_plugin_init() {
	require( dirname( __FILE__ ) . '/bp-disable-activation.php' );
}
add_action( 'bp_init', 'my_plugin_init' );

?>