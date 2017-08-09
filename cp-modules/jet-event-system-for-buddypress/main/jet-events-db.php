<?php
function jes_events_init_jesdb() {
	global $wpdb, $bp;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

	$sql[] = "CREATE TABLE {$bp->jes_events->table_name} (
	  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			creator_id bigint(20) NOT NULL,
	  		name varchar(100) NOT NULL,
			etype varchar(20) NOT NULL,
			eventapproved varchar(1),
	  		slug varchar(100) NOT NULL,
	  		description longtext NOT NULL,
	  		eventterms longtext,
			placedcountry varchar(25),
			placedstate varchar(25),
			placedcity varchar(25) NOT NULL,
			placedaddress varchar(70) NOT NULL,
			placednote varchar(70),
			placedgooglemap varchar(250),
			flyer varchar(250),
			newspublic longtext,
			newsprivate longtext,
			edtsd varchar(16) NOT NULL,
			edted varchar(16) NOT NULL,
			edtsth varchar(2) NOT NULL DEFAULT '0',
			edteth varchar(2) NOT NULL DEFAULT '23',
			edtstm varchar(2) NOT NULL DEFAULT '0',
			edtetm varchar(2) NOT NULL DEFAULT '59',
			edtsdunix varchar(16) NOT NULL,
			edtedunix varchar(16) NOT NULL,
			status varchar(10) NOT NULL DEFAULT 'public',
			grouplink varchar(5) NOT NULL DEFAULT '0',
			forumlink varchar(5) NOT NULL DEFAULT '0',
			enablesocial tinyint(1) NOT NULL DEFAULT '0',
			enable_forum tinyint(1) NOT NULL DEFAULT '1',
			date_created datetime NOT NULL,
			notify_timed_enable varchar(1) NOT NULL DEFAULT '0',
		    KEY creator_id (creator_id),
		    KEY status (status),
			KEY etype (etype),
			KEY name (name),
			KEY eventapproved (eventapproved),
			KEY placedcity (placedcity),
			KEY placedcountry (placedcountry),
			KEY grouplink (grouplink)
	 	   ) {$charset_collate};";

	$sql[] = "CREATE TABLE {$bp->jes_events->jes_table_name_members} (
	  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			event_id bigint(20) NOT NULL,
			user_id bigint(20) NOT NULL,
			inviter_id bigint(20) NOT NULL,
			is_admin tinyint(1) NOT NULL DEFAULT '0',
			is_mod tinyint(1) NOT NULL DEFAULT '0',
			user_title varchar(100) NOT NULL,
			date_modified datetime NOT NULL,
			comments longtext NOT NULL,
			is_confirmed tinyint(1) NOT NULL DEFAULT '0',
			is_banned tinyint(1) NOT NULL DEFAULT '0',
			invite_sent tinyint(1) NOT NULL DEFAULT '0',
			KEY event_id (event_id),
			KEY is_admin (is_admin),
			KEY is_mod (is_mod),
		 	KEY user_id (user_id),
			KEY inviter_id (inviter_id),
			KEY is_confirmed (is_confirmed)
	 	   ) {$charset_collate};";

	$sql[] = "CREATE TABLE {$bp->jes_events->table_name_eventmeta} (
			id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			event_id bigint(20) NOT NULL,
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext DEFAULT NULL,
			KEY event_id (event_id),
			KEY meta_key (meta_key)
		   ) {$charset_collate};";

	$sql[] = "CREATE TABLE {$bp->jes_events->table_name_activity} (
	  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			creator_id bigint(20) NOT NULL,
	  		description longtext NOT NULL,
			a_datetime varchar(18) NOT NULL,
			a_datetime_unix varchar(18) NOT NULL,
		    KEY creator_id (creator_id),
			KEY a_datetime_unix (a_datetime_unix)
	 	   ) {$charset_collate};";

	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);

	do_action( 'jes_events_init_jesdb' );

	update_site_option( 'jes-events-db-version', JES_EVENTS_DB_VERSION );
}
?>
