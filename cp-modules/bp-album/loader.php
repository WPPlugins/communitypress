<?php
/*
// JLL_MOD - Removed Plugin Header
*/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_album_init() {

	/* Define a constant that will hold the current version number of the component */
	define ( 'BP_ALBUM_VERSION', '0.1.8.11' );
	
	require( dirname( __FILE__ ) . '/includes/bp-album-core.php' );
	
	do_action('bp_album_init');
}
add_action( 'bp_init', 'bp_album_init' );


// Moved this function to loader.php file because this is the standard place for it
// and it will get very large once we add database upgrade code to migrate plugin versions
function bp_album_install(){
	global $bp,$wpdb;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";


    $sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_album (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            owner_type varchar(10) NOT NULL,
	            owner_id bigint(20) NOT NULL,
	            date_uploaded datetime NOT NULL,
	            title varchar(250) NOT NULL,
	            description longtext NOT NULL,
	            privacy tinyint(2) NOT NULL default '0',
	            pic_org_url varchar(250) NOT NULL,
	            pic_org_path varchar(250) NOT NULL,
	            pic_mid_url varchar(250) NOT NULL,
	            pic_mid_path varchar(250) NOT NULL,
	            pic_thumb_url varchar(250) NOT NULL,
	            pic_thumb_path varchar(250) NOT NULL,
	            KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";
// JLL_MOD - add a table for face-tagging
    $sqltag[] = "CREATE TABLE {$wpdb->base_prefix}bp_album_tags (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            photo_id bigint(20) NOT NULL,
	            tagged_id bigint(20),
	            tagged_name varchar(250) NOT NULL,
	            height bigint(20) NOT NULL,
	            width bigint(20) NOT NULL,
	            top_pos bigint(20) NOT NULL,
	            left_pos bigint(20) NOT NULL,
	            KEY photo_id (photo_id),
	            KEY tagged_id (tagged_id)
	            ) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// JLL_MOD - add a table for face-tagging
	dbDelta($sqltag);
	dbDelta($sql);

	update_site_option( 'bp-album-db-version', BP_ALBUM_DB_VERSION  );

        // Write default options to the WP database if they do not exist,
        // but do not overwrite options if the user has set them. Using
        // update_site_option() because it puts data in a top-level WP
        // table so it is easy to debug.

        if (!get_site_option( 'bp_album_slug' ))
            update_site_option( 'bp_album_slug', 'album');
	
        if ( !get_site_option( 'bp_album_max_upload_size' ))
            update_site_option( 'bp_album_max_upload_size', 6 ); /* 6mb */

        if (!get_site_option( 'bp_album_max_pictures' ))
            update_site_option( 'bp_album_max_pictures', false);

        if (!get_site_option( 'bp_album_max_priv0_pictures' ))
            update_site_option( 'bp_album_max_priv0_pictures', false);

        if (!get_site_option( 'bp_album_max_priv2_pictures' ))
            update_site_option( 'bp_album_max_priv2_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv4_pictures' ))
            update_site_option( 'bp_album_max_priv4_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv6_pictures' ))
            update_site_option( 'bp_album_max_priv6_pictures', false);

        if(!get_site_option( 'bp_album_keep_original' ))
            update_site_option( 'bp_album_keep_original', true);
        
        if(!get_site_option( 'bp_album_require_description' ))
            update_site_option( 'bp_album_require_description', false);

        if(!get_site_option( 'bp_album_enable_comments' ))
            update_site_option( 'bp_album_enable_comments', true);

        if(!get_site_option( 'bp_album_enable_wire' ))
            update_site_option( 'bp_album_enable_wire', true);

        if(!get_site_option( 'bp_album_middle_size' ))
            update_site_option( 'bp_album_middle_size', 600);

        if(!get_site_option( 'bp_album_thumb_size' ))
            update_site_option( 'bp_album_thumb_size', 150);
        
        if(!get_site_option( 'bp_album_per_page' ))
            update_site_option( 'bp_album_per_page', 20 );

        if(!get_site_option( 'bp_album_url_remap' ))
	    update_site_option( 'bp_album_url_remap', false);

        if(true) {
		$path = bp_get_root_domain() . '/wp-content/uploads/album';
		update_site_option( 'bp_album_base_url', $path );
	}

}

register_activation_hook( __FILE__, 'bp_album_install' );


function bp_album_check_installed() {
	global $wpdb, $bp;

	if ( !current_user_can('install_plugins') )
		return;
		
// JLL_MOD - removed version check for the plugin

if ( get_site_option( 'bp-album-db-version' ) < BP_ALBUM_DB_VERSION )
		bp_album_install();
}

add_action( 'admin_menu', 'bp_album_check_installed' );

function bp_album_compatibility_notices() {
	$message = 'BP Album needs at least BuddyPress 1.2 to work. Please either install or update BuddyPress';
	if (!defined('BP_VERSION')){
		$message .= ' Please install Buddypress';
	}elseif(version_compare(BP_VERSION, '1.2','<') ){
		$message .= ' Your current version is '.BP_VERSION.' please updrade.';
	}
	echo '<div class="error fade"><p>'.$message.'</p></div>';
}

function bp_album_activate() {
	bp_album_check_installed();

	do_action( 'bp_album_activate' );
}
register_activation_hook( __FILE__, 'bp_album_activate' );

function bp_album_deactivate() {
	do_action( 'bp_album_deactivate' );
}
register_deactivation_hook( __FILE__, 'bp_album_deactivate' );

?>