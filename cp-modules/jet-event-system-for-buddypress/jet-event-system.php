<?php
/*
// JLL_MOD - Removed Plugin Header
Site Wide Only: true
Network: true
*/

define ('Jet Events System', '1.7.0');
define ('JES_EVENTS_VERSION', '1.7' );
define ('JES_EVENTS_BUILD', '1' );
define ('JES_EVENTS_DB_VERSION', 22 );
define ('JES_EVENTS_THEME_VERSION', 34 );
define ('JES_EVENTS_RELEASE', '2011-07-16');

/* Define the slug for the component */
if ( !defined( 'JES_SLUG' ) ) {
$jes_adata = get_site_option('jes_events' );
if (!$jes_adata[ 'jes_events_costumslug_enable' ])
	{
		define ( 'JES_SLUG', 'events' );
	}
		else
	{
		define ( 'JES_SLUG', $jes_adata[ 'jes_events_costumslug' ] );	
	}
}

// JLL_MOD - fixed file dir path (Ctrl+f & replaced on entire module)
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-event-start.php' );


function jes_activation() {
/* Update DB and Templates */

/* DB */
//	if ( get_site_option( 'jes-events-db-version' ) != JES_EVENTS_DB_VERSION )
//		{
// include( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-db.php' );
			jes_events_init_jesdb();
//		} 
/* Template */
// 	if ( get_site_option( 'jes-theme-version' ) < JES_EVENTS_THEME_VERSION )
		//{
//			update_template(null,'no');
//		}
}

function jes_deactivation() {
// delete_option( 'jes_events' ); 
}

register_activation_hook( __FILE__, 'jes_activation' );
register_deactivation_hook( __FILE__, 'jes_deactivation' );

/* LOAD LANGUAGES */
function jet_event_system_load_textdomain() {
	$locale = apply_filters( 'wordpress_locale', get_locale() );
	$mofile = dirname( __File__ ) . "/lang/jet-event-system-$locale.mo";

	if ( file_exists( $mofile ) )
		load_textdomain( 'jet-event-system', $mofile );
}
add_action ( 'plugins_loaded', 'jet_event_system_load_textdomain', 7 );
?>