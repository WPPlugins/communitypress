<?php

Class JES_Events_Event {
	var $id;
	var $creator_id;
	var $name;
	var $etype;
	var $eventapproved;
	var $slug;
	var $description;
	var $eventterms;
	var $placedcountry;
	var $placedstate;
	var $placedcity;
	var $placedaddress;
	var $placednote;
	var $placedgooglemap;
	var $flyer;
	var $newspublic;
	var $newsprivate;
	var $edtsd;
	var $edted;
	var $edtsth;
	var $edteth;	
	var $edtstm;
	var $edtetm;
	var $status;
	var $enable_forum;
	var $grouplink;
	var $forumlink;
	var $enablesocial;
	var $date_created;
	var $notify_timed_enable;

	var $admins;
	var $total_member_count;

	function jes_events_event( $id = null ) {
		if ( $id ) {
			$this->id = $id;
			$this->populate();
		}
	}

	function populate() {
		global $wpdb, $bp;

		if ( $event = $wpdb->get_row( $wpdb->prepare( "SELECT g.*, gm.meta_value as last_activity, gm2.meta_value as total_member_count FROM {$bp->jes_events->table_name} g, {$bp->jes_events->table_name_eventmeta} gm, {$bp->jes_events->table_name_eventmeta} gm2 WHERE g.id = gm.event_id AND g.id = gm2.event_id AND gm.meta_key = 'last_activity' AND gm2.meta_key = 'total_member_count' AND g.id = %d LIMIT 1", $this->id ) ) ) {
			$this->id = $event->id;
			$this->creator_id = $event->creator_id;
			$this->name = stripslashes($event->name);
			$this->etype = stripslashes($event->etype);
			$this->eventapproved = stripslashes($event->eventapproved);		
			$this->slug = $event->slug;
			$this->description = stripslashes($event->description);
			$this->eventterms = stripslashes($event->eventterms);
			$this->placedcountry = stripslashes($event->placedcountry);			
			$this->placedstate = stripslashes($event->placedstate);			
			$this->placedcity = stripslashes($event->placedcity);
			$this->placedaddress = stripslashes($event->placedaddress);
			$this->placednote = stripslashes($event->placednote);
			$this->placedgooglemap = stripslashes($event->placedgooglemap);
			$this->flyer = stripslashes($event->flyer);
			$this->newspublic = stripslashes($event->newspublic);
			$this->newsprivate = stripslashes($event->newsprivate);
			$this->edtsd = stripslashes($event->edtsd);			
			$this->edted = stripslashes($event->edted);			
			$this->edtsth = stripslashes($event->edtsth);			
			$this->edteth = stripslashes($event->edteth);
			$this->edtstm = stripslashes($event->edtstm);			
			$this->edtetm = stripslashes($event->edtetm);
			$this->edtsdunix = stripslashes($event->edtsdunix);			
			$this->edtedunix = stripslashes($event->edtedunix);	
			$this->grouplink = stripslashes($event->grouplink);	
			$this->forumlink = stripslashes($event->forumlink);
			$this->enablesocial = stripslashes($event->enablesocial);
			$this->status = $event->status;
			$this->enable_forum = $event->enable_forum;
			$this->date_created = $event->date_created;
			$this->last_activity = $event->last_activity;
			$this->notify_timed_enable = $event->notify_timed_enable;
			$this->total_member_count = $event->total_member_count;
			$this->is_member = JES_Events_Member::jes_check_is_member( $bp->loggedin_user->id, $this->id );

			/* Get event admins and mods */
			$admin_mods = $wpdb->get_results( $wpdb->prepare( "SELECT u.ID as user_id, u.user_login, u.user_email, u.user_nicename, m.is_admin, m.is_mod FROM {$wpdb->users} u, {$bp->jes_events->jes_table_name_members} m WHERE u.ID = m.user_id AND m.event_id = %d AND ( m.is_admin = 1 OR m.is_mod = 1 )", $this->id ) );
			foreach( (array)$admin_mods as $user ) {
				if ( (int)$user->is_admin )
					$this->admins[] = $user;
				else
					$this->mods[] = $user;
			}
		}
	}

	function save() {
		global $wpdb, $bp;
	
		$this->creator_id = apply_filters( 'events_event_creator_id_before_save', $this->creator_id, $this->id );
		$this->name = apply_filters( 'events_event_name_before_save', $this->name, $this->id );
		$this->etype = apply_filters( 'events_event_etype_before_save', $this->etype, $this->id );
		$this->eventapproved = apply_filters( 'events_event_eventapproved_before_save', $this->eventapproved, $this->id );		
 		$this->slug = apply_filters( 'events_event_slug_before_save', $this->slug, $this->id );
		$this->description = apply_filters( 'events_event_description_before_save', $this->description, $this->id );
		$this->eventterms = apply_filters( 'events_event_eventterms_before_save', $this->eventterms, $this->id );
		$this->placedcountry = apply_filters( 'events_event_placedcountry_before_save', $this->placedcountry, $this->id );
		$this->placedstate = apply_filters( 'events_event_placedstate_before_save', $this->placedstate, $this->id );
		$this->placedcity = apply_filters( 'events_event_placedcity_before_save', $this->placedcity, $this->id );
		$this->placedaddress = apply_filters( 'events_event_placedaddress_before_save', $this->placedaddress, $this->id );		
		$this->placednote = apply_filters( 'events_event_placednote_before_save', $this->placednote, $this->id );
		$this->placedgooglemap = apply_filters( 'events_event_placedgooglemap_before_save', $this->placedgooglemap, $this->id );
		$this->flyer = apply_filters( 'events_event_flyer_before_save', $this->flyer, $this->id );
		$this->newspublic = apply_filters( 'events_event_newspublic_before_save', $this->newspublic, $this->id );
		$this->newsprivate = apply_filters( 'events_event_newsprivate_before_save', $this->newsprivate, $this->id );
		$this->edtsd = apply_filters( 'events_event_edtsd_before_save', $this->edtsd, $this->id );
		$this->edted = apply_filters( 'events_event_edted_before_save', $this->edted, $this->id );
		$this->edtsth = apply_filters( 'events_event_edtsth_before_save', $this->edtsth, $this->id );
		$this->edteth = apply_filters( 'events_event_edteth_before_save', $this->edteth, $this->id );
		$this->edtstm = apply_filters( 'events_event_edtstm_before_save', $this->edtstm, $this->id );
		$this->edtetm = apply_filters( 'events_event_edtetm_before_save', $this->edtetm, $this->id );
		$this->edtsdunix = jes_datetounix(apply_filters( 'events_event_edtsdunix_before_save', $this->edtsd, $this->id ), apply_filters( 'events_event_edtsth_before_save', $this->edtsth, $this->id ), apply_filters( 'events_event_edtstm_before_save', $this->edtstm, $this->id ));
		$this->edtedunix = jes_datetounix(apply_filters( 'events_event_edtedunix_before_save', $this->edted, $this->id ), apply_filters( 'events_event_edteth_before_save', $this->edteth, $this->id ), apply_filters( 'events_event_edtetm_before_save', $this->edtetm, $this->id ));		
		$this->grouplink = apply_filters( 'events_event_grouplink_before_save', $this->grouplink, $this->id );
		$this->forumlink = apply_filters( 'events_event_forumlink_before_save', $this->forumlink, $this->id );		
		$this->enablesocial = apply_filters( 'events_event_enablesocial_before_save', $this->enablesocial, $this->id );	
 		$this->status = apply_filters( 'events_event_status_before_save', $this->status, $this->id );
		$this->enable_forum = apply_filters( 'events_event_enable_forum_before_save', $this->enable_forum, $this->id );
		$this->date_created = apply_filters( 'events_event_date_created_before_save', $this->date_created, $this->id );
		$this->notify_timed_enable = apply_filters( 'events_event_notify_timed_enable_before_save', $this->notify_timed_enable, $this->id );
		
		do_action( 'events_event_before_save', $this );

		if ( $this->id ) {
			$sql = $wpdb->prepare(
				"UPDATE {$bp->jes_events->table_name} SET
					creator_id = %d,
					name = %s,
					etype = %s,
					eventapproved = %s,
					slug = %s,
					description = %s,
					eventterms = %s,
					placedcountry = %s,
					placedstate = %s,
					placedcity = %s,
					placedaddress = %s,
					placednote = %s,
					placedgooglemap = %s,
					flyer = %s,
					newspublic = %s,
					newsprivate = %s,
					edtsd = %s,
					edted = %s,
					edtsth = %s,
					edteth = %s,					
					edtstm = %s,
					edtetm = %s,
					edtsdunix = %s,
					edtedunix = %s,
					grouplink = %s,
					forumlink = %s,
					enablesocial = %d,
					status = %s,
					enable_forum = %d,
					date_created = %s,
					notify_timed_enable = %s
				WHERE
					id = %d
				",
					$this->creator_id,
					$this->name,
					$this->etype,
					$this->eventapproved,
					$this->slug,
					$this->description,
					$this->eventterms,
					$this->placedcountry,
					$this->placedstate,
					$this->placedcity,
					$this->placedaddress,
					$this->placednote,					
					$this->placedgooglemap,
					$this->flyer,
					$this->newspublic,
					$this->newsprivate,
					$this->edtsd,
					$this->edted,
					$this->edtsth,
					$this->edteth,
					$this->edtstm,
					$this->edtetm,					
					jes_datetounix($this->edtsd, $this->edtsth, $this->edtstm),
					jes_datetounix($this->edted, $this->edteth, $this->edtetm),
					$this->grouplink,
					$this->forumlink,
					$this->enablesocial,
					$this->status,
					$this->enable_forum,
					$this->date_created,
					$this->notify_timed_enable,
					$this->id
			);
		} else {
		
			$sql = $wpdb->prepare(
				"INSERT INTO {$bp->jes_events->table_name} (
					creator_id,
					name,
					etype,
					eventapproved,
					slug,
					description,
					eventterms,
					placedcountry,
					placedstate,
					placedcity,
					placedaddress,
					placednote,
					placedgooglemap,
					flyer,
					newspublic,
					newsprivate,
					edtsd,
					edted,
					edtsth,
					edteth,
					edtstm,
					edtetm,
					edtsdunix,
					edtedunix,
					grouplink,
					forumlink,
					enablesocial,
					status,
					enable_forum,
					date_created,
					notify_timed_enable
				) VALUES (
					%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %s
				)",
					$this->creator_id,
					$this->name,
					$this->etype,
					$this->eventapproved,
					$this->slug,
					$this->description,
					$this->eventterms,
					$this->placedcountry,
					$this->placedstate,
					$this->placedcity,
					$this->placedaddress,
					$this->placednote,
					$this->placedgooglemap,
					$this->flyer,
					$this->newspublic,
					$this->newsprivate,
					$this->edtsd,
					$this->edted,
					$this->edtsth,
					$this->edteth,					
					$this->edtstm,
					$this->edtetm,
					$this->edtsdunix,
					$this->edtedunix,
					$this->grouplink,
					$this->forumlink,
					$this->enablesocial,
					$this->status,
					$this->enable_forum,
					$this->date_created,
					$this->notify_timed_enable
			);
		}

		if ( false === $wpdb->query($sql) )
			return false;

		if ( !$this->id ) {
			$this->id = $wpdb->insert_id;
		}

		do_action( 'events_event_after_save', $this );

		return true;	
	}

	function delete() {
		global $wpdb, $bp;

		/* Delete eventmeta for the event */
		events_delete_eventmeta( $this->id );

		/* Fetch the user IDs of all the members of the event */
		$user_ids = JES_Events_Member::jes_get_event_member_ids( $this->id );
		$user_ids = implode( ',', (array)$user_ids );

		/* Modify event count usermeta for members */
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->usermeta} SET meta_value = meta_value - 1 WHERE meta_key = 'jes_total_event_count' AND user_id IN ( {$user_ids} )" ) );

		/* Now delete all event member entries */
		JES_Events_Member::jes_delete_all( $this->id );

		do_action( 'jes_bp_events_delete_event', $this );

		// Finally remove the event entry from the DB
		if ( !$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->table_name} WHERE id = %d", $this->id ) ) )
			return false;

		return true;
	}

	/* Static Functions */

	function jes_event_exists( $slug, $table_name = false ) {
		global $wpdb, $bp;

		if ( !$table_name )
			$table_name = $bp->jes_events->table_name;

		if ( !$slug )
			return false;

		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE slug = %s LIMIT 1", $slug ) );
	}

	function jes_get_id_from_slug( $slug ) {
		return JES_Events_Event::jes_event_exists( $slug );
	}

	function jes_get_invite_jes( $user_id, $event_id ) {
		global $wpdb, $bp;
		return $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d and is_confirmed = 0 AND inviter_id = %d", $event_id, $user_id ) );
	}

	
	function get_invitable_friend_count( $user_id, $event_id ) {
		global $wpdb, $bp;

		$friend_ids = BP_Friends_Friendship::get_friend_user_ids( $user_id );

		$invitable_count = 0;
		for ( $i = 0; $i < count($friend_ids); $i++ ) {

			if ( BP_Events_Member::check_is_member( (int)$friend_ids[$i], $event_id ) )
				continue;

			if ( BP_Events_Member::check_has_invite( (int)$friend_ids[$i], $event_id )  )
				continue;

			$invitable_count++;
		}

		return $invitable_count;
	}	
	
	
	function jes_filter_user_events( $filter, $user_id = false, $order = false, $limit = null, $page = null ) {
		global $wpdb, $bp;

		if ( !$user_id )
			$user_id = $bp->displayed_user->id;

		$filter = like_escape( $wpdb->escape( $filter ) );

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		// Get all the event ids for the current user's events.
		$gids = JES_Events_Member::jes_get_event_ids( $user_id );

		if ( !$gids['events'] )
			return false;

		$gids = implode( ',', $gids['events'] );

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT id as event_id FROM {$bp->jes_events->table_name} WHERE ( name LIKE '{$filter}%%' OR description LIKE '{$filter}%%' ) AND id IN ({$gids}) {$pag_sqle}" ) );
		$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->jes_events->table_name} WHERE ( name LIKE '{$filter}%%' OR description LIKE '{$filter}%%' ) AND id IN ({$gids})" ) );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_search_events( $filter, $limit = null, $page = null, $sort_by = false, $order = false ) {
		global $wpdb, $bp;

		$filter = like_escape( $wpdb->escape( $filter ) );

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( $sort_by && $order ) {
			$sort_by = $wpdb->escape( $sort_by );
			$order = $wpdb->escape( $order );
			$order_sql = "ORDER BY $sort_by $order";
		}

		if ( !is_site_admin() )
			$hidden_sql = "AND status != 'hidden'";

		$paged_events = $wpdb->get_results( "SELECT id as event_id FROM {$bp->jes_events->table_name} WHERE ( name LIKE '%%$filter%%' OR description LIKE '%%$filter%%' ) {$hidden_sql} {$order_sql} {$pag_sqle}" );
		$total_events = $wpdb->get_var( "SELECT COUNT(id) FROM {$bp->jes_events->table_name} WHERE ( name LIKE '%%$filter%%' OR description LIKE '%%$filter%%' ) {$hidden_sq}" );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_check_slug( $slug ) {
		global $wpdb, $bp;

		return $wpdb->get_var( $wpdb->prepare( "SELECT slug FROM {$bp->jes_events->table_name} WHERE slug = %s", $slug ) );
	}

	function jes_get_slug( $event_id ) {
		global $wpdb, $bp;

		return $wpdb->get_var( $wpdb->prepare( "SELECT slug FROM {$bp->jes_events->table_name} WHERE id = %d", $event_id ) );
	}

	function jes_has_members( $event_id ) {
		global $wpdb, $bp;

		$members = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d", $event_id ) );

		if ( !$members )
			return false;

		return true;
	}

	function jes_has_membership_requests( $event_id ) {
		global $wpdb, $bp;

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 0", $event_id ) );
	}

	function get_membership_requests( $event_id, $limit = null, $page = null ) {
		global $wpdb, $bp;

		if ( $limit && $page ) {
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
		}

		$paged_requests = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 0 AND inviter_id = 0{$pag_sqle}", $event_id ) );
		$total_requests = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 0 AND inviter_id = 0", $event_id ) );

		return array( 'requests' => $paged_requests, 'total' => $total_requests );
	}

	
	function get_invites( $user_id, $event_id ) {
		global $wpdb, $bp;
		return $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->events->table_name_members} WHERE event_id = %d and is_confirmed = 0 AND inviter_id = %d", $event_id, $user_id ) );
	}	

	function check_has_invite( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bp->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id != 0 AND invite_sent = 1", $user_id, $event_id ) );
	}

	function delete_invite( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id != 0 AND invite_sent = 1", $user_id, $event_id ) );
	}

	function delete_request( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

 		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id = 0 AND invite_sent = 0", $user_id, $event_id ) );
	}


	
	/* TODO: Merge all these get_() functions into one. */

	function jes_get_newest( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;
	$jes_adata = get_site_option('jes_events' );
	$sortby_ad = $jes_adata[ 'jes_events_sort_by_ad' ];
		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = "AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY g.date_created ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY g.date_created ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm INNER JOIN {$bp->jes_events->table_name} g ON gm.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'newest' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}
/* ------------ */

	function jes_get_soon( $limit = 10, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;
	$jes_adata = get_site_option('jes_events' );
	$sortby_ad = $jes_adata[ 'jes_events_sort_by_ad' ];
	
		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = "AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY g.edtsdunix ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY g.edtsdunix ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm INNER JOIN {$bp->jes_events->table_name} g ON gm.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'soon' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}
	
	function jes_get_calendar( $limit = 10, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;
	$jes_adata = get_site_option('jes_events' );
	$sortby_ad = $jes_adata[ 'jes_events_sort_by_ad' ];
	
		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = "AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY g.edtsdunix ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY g.edtsdunix ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm INNER JOIN {$bp->jes_events->table_name} g ON gm.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'soon' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}
/* ---------------- */	
	
	function jes_get_active( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;
	$jes_adata = get_site_option('jes_events' );
	$sortby_ad = $jes_adata[ 'jes_events_sort_by_ad' ];
		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = "AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY last_activity ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY last_activity ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm INNER JOIN {$bp->jes_events->table_name} g ON gm.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'newest' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_get_popular( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;
	$jes_adata = get_site_option('jes_events' );
	$sortby_ad = $jes_adata[ 'jes_events_sort_by_ad' ];
		if ( $limit && $page ) {
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
		}

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = "AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY CONVERT(gm1.meta_value, SIGNED) ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY CONVERT(gm1.meta_value, SIGNED) ".$sortby_ad." {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'newest' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_get_alphabetically( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = " AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY g.name ASC {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY g.name ASC {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'newest' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_get_all( $limit = null, $page = null, $only_public = true, $sort_by = false, $order = false ) {
		global $wpdb, $bp;

		// Default sql WHERE conditions are blank. TODO: generic handler function.
		$where_sql = null;
		$where_conditions = array();

		// Limit results to public status
		if ( $only_public )
			$where_conditions[] = $wpdb->prepare( "g.status = 'public'" );

		if ( !is_site_admin() )
			$where_conditions[] = $wpdb->prepare( "g.status != 'hidden'");

		// Build where sql statement if necessary
		if ( !empty( $where_conditions ) )
			$where_sql = 'WHERE ' . join( ' AND ', $where_conditions );

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( $sort_by && $order ) {
			$sort_by = $wpdb->escape( $sort_by );
			$order = $wpdb->escape( $order );
			$order_sql = "ORDER BY g.$sort_by $order";

			switch ( $sort_by ) {
				default:
					$sql = $wpdb->prepare( "SELECT * FROM {$bp->jes_events->table_name} g {$where_sql} {$order_sql} {$pag_sqle}" );
					break;
				case 'members':
					$sql = $wpdb->prepare( "SELECT * FROM {$bp->jes_events->table_name} g, {$bp->jes_events->table_name_eventmeta} gm WHERE g.id = gm.event_id AND gm.meta_key = 'total_member_count' {$hidden_sql} {$public_sql} ORDER BY CONVERT(gm.meta_value, SIGNED) {$order} {$pag_sqle}" );
					break;
				case 'last_active':
					$sql = $wpdb->prepare( "SELECT * FROM {$bp->jes_events->table_name} g, {$bp->jes_events->table_name_eventmeta} gm WHERE g.id = gm.event_id AND gm.meta_key = 'last_activity' {$hidden_sql} {$public_sql} ORDER BY CONVERT(gm.meta_value, SIGNED) {$order} {$pag_sqle}" );
					break;
			}
		} else {
			$sql = $wpdb->prepare( "SELECT * FROM {$bp->jes_events->table_name} g {$where_sql} {$order_sql} {$pag_sqle}" );
		}

		return $wpdb->get_results($sql);
	}

	function jes_get_by_letter( $letter, $limit = null, $page = null, $populate_extras = true ) {
		global $wpdb, $bp;

		if ( strlen($letter) > 1 || is_numeric($letter) || !$letter )
			return false;

		if ( !is_site_admin() )
			$hidden_sql = $wpdb->prepare( " AND status != 'hidden'");

		$letter = like_escape( $wpdb->escape( $letter ) );

		if ( $limit && $page ) {
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
			$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND g.name LIKE '$letter%%' {$hidden_sql} {$search_sql}" ) );
		}

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND g.name LIKE '$letter%%' {$hidden_sql} {$search_sql} ORDER BY g.name ASC {$pag_sqle}"  ) );

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'newest' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_get_random( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !is_user_logged_in() || ( !is_site_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
			$hidden_sql = "AND g.status != 'hidden'";

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
		}

		if ( $user_id ) {
			$user_id = $wpdb->escape( $user_id );
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY rand() {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m LEFT JOIN {$bp->jes_events->table_name_eventmeta} gm ON m.event_id = gm.event_id INNER JOIN {$bp->jes_events->table_name} g ON m.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
		} else {
			$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->table_name} g WHERE g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY rand() {$pag_sqle}" );
			$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->jes_events->table_name_eventmeta} gm INNER JOIN {$bp->jes_events->table_name} g ON gm.event_id = g.id WHERE gm.meta_key = 'last_activity'{$hidden_sql} {$search_sql}" );
		}

		if ( !empty( $populate_extras ) ) {
			foreach ( (array)$paged_events as $event ) $jes_event_ids[] = $event->id;
			$jes_event_ids = $wpdb->escape( join( ',', (array)$jes_event_ids ) );
			$paged_events = JES_Events_Event::get_event_extras( &$paged_events, $jes_event_ids, 'newest' );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function get_event_extras( $paged_events, $jes_event_ids, $type = false ) {
		global $bp, $wpdb;

		if ( empty( $jes_event_ids ) )
			return $paged_events;

		/* Fetch the logged in users status within each event */
		$user_status = $wpdb->get_col( $wpdb->prepare( "SELECT event_id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id IN ( {$jes_event_ids} ) AND is_confirmed = 1 AND is_banned = 0", $bp->loggedin_user->id ) );
		for ( $i = 0; $i < count( $paged_events ); $i++ ) {
			foreach ( (array)$user_status as $event_id ) {
				if ( $event_id == $paged_events[$i]->id )
					$paged_events[$i]->is_member = true;
			}
		}

		$user_banned = $wpdb->get_col( $wpdb->prepare( "SELECT event_id FROM {$bp->jes_events->jes_table_name_members} WHERE is_banned = 1 AND user_id = %d AND event_id IN ( {$jes_event_ids} )", $bp->loggedin_user->id ) );
		for ( $i = 0; $i < count( $paged_events ); $i++ ) {
			foreach ( (array)$user_banned as $event_id ) {
				if ( $event_id == $paged_events[$i]->id )
					$paged_events[$i]->is_banned = true;
			}
		}

		return $paged_events;
	}

	function jes_jes_delete_all_invite_jes( $event_id ) {
		global $wpdb, $bp;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND invite_sent = 1", $event_id ) );
	}

	function jes_get_jes_total_event_count() {
		global $wpdb, $bp;

		if ( !is_site_admin() )
			$hidden_sql = "WHERE status != 'hidden'";

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->jes_events->table_name} {$hidden_sql}" ) );
	}


	function jes_get_total_member_count( $event_id ) {
		global $wpdb, $bp;

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 1 AND is_banned = 0", $event_id ) );
	}
}

Class JES_Events_Member {
	var $id;
	var $event_id;
	var $user_id;
	var $inviter_id;
	var $is_admin;
	var $is_mod;
	var $is_banned;
	var $user_title;
	var $date_modified;
	var $is_confirmed;
	var $comments;
	var $invite_sent;

	var $user;

	function jes_events_member( $user_id = false, $event_id = false, $id = false, $populate = true ) {
		if ( $user_id && $event_id && !$id ) {
			$this->user_id = $user_id;
			$this->event_id = $event_id;

			if ( $populate )
				$this->populate();
		}

		if ( $id ) {
			$this->id = $id;

			if ( $populate )
				$this->populate();
		}
	}

	function populate() {
		global $wpdb, $bp;

		if ( $this->user_id && $this->event_id && !$this->id )
			$sql = $wpdb->prepare( "SELECT * FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d", $this->user_id, $this->event_id );

		if ( $this->id )
			$sql = $wpdb->prepare( "SELECT * FROM {$bp->jes_events->jes_table_name_members} WHERE id = %d", $this->id );

		$member = $wpdb->get_row($sql);

		if ( $member ) {
			$this->id = $member->id;
			$this->event_id = $member->event_id;
			$this->user_id = $member->user_id;
			$this->inviter_id = $member->inviter_id;
			$this->is_admin = $member->is_admin;
			$this->is_mod = $member->is_mod;
			$this->is_banned = $member->is_banned;
			$this->user_title = $member->user_title;
			$this->date_modified = $member->date_modified;
			$this->is_confirmed = $member->is_confirmed;
			$this->comments = $member->comments;
			$this->invite_sent = $member->invite_sent;

			$this->user = new BP_Core_User( $this->user_id );
		}
	}

	function save() {
		global $wpdb, $bp;

		$this->user_id = apply_filters( 'events_member_user_id_before_save', $this->user_id, $this->id );
		$this->event_id = apply_filters( 'events_member_event_id_before_save', $this->event_id, $this->id );
		$this->inviter_id = apply_filters( 'events_member_inviter_id_before_save', $this->inviter_id, $this->id );
		$this->is_admin = apply_filters( 'events_member_is_admin_before_save', $this->is_admin, $this->id );
		$this->is_mod = apply_filters( 'events_member_is_mod_before_save', $this->is_mod, $this->id );
		$this->is_banned = apply_filters( 'events_member_is_banned_before_save', $this->is_banned, $this->id );
		$this->user_title = apply_filters( 'events_member_user_title_before_save', $this->user_title, $this->id );
		$this->date_modified = apply_filters( 'events_member_date_modified_before_save', $this->date_modified, $this->id );
		$this->is_confirmed = apply_filters( 'events_member_is_confirmed_before_save', $this->is_confirmed, $this->id );
		$this->comments = apply_filters( 'events_member_comments_before_save', $this->comments, $this->id );
		$this->invite_sent = apply_filters( 'events_member_invite_sent_before_save', $this->invite_sent, $this->id );

		do_action( 'events_member_before_save', $this );

		if ( $this->id ) {
			$sql = $wpdb->prepare( "UPDATE {$bp->jes_events->jes_table_name_members} SET inviter_id = %d, is_admin = %d, is_mod = %d, is_banned = %d, user_title = %s, date_modified = %s, is_confirmed = %d, comments = %s, invite_sent = %d WHERE id = %d", $this->inviter_id, $this->is_admin, $this->is_mod, $this->is_banned, $this->user_title, $this->date_modified, $this->is_confirmed, $this->comments, $this->invite_sent, $this->id );
		} else {
			$sql = $wpdb->prepare( "INSERT INTO {$bp->jes_events->jes_table_name_members} ( user_id, event_id, inviter_id, is_admin, is_mod, is_banned, user_title, date_modified, is_confirmed, comments, invite_sent ) VALUES ( %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d )", $this->user_id, $this->event_id, $this->inviter_id, $this->is_admin, $this->is_mod, $this->is_banned, $this->user_title, $this->date_modified, $this->is_confirmed, $this->comments, $this->invite_sent );
		}

		if ( !$wpdb->query($sql) )
			return false;

		$this->id = $wpdb->insert_id;

		do_action( 'events_member_after_save', $this );

		return true;
	}

	function promote( $status = 'mod' ) {
		if ( 'mod' == $status ) {
			$this->is_admin = 0;
			$this->is_mod = 1;
			$this->user_title = __( 'Event Mod', 'jet-event-system' );
		}

		if ( 'admin' == $status ) {
			$this->is_admin = 1;
			$this->is_mod = 0;
			$this->user_title = __( 'Event Admin', 'jet-event-system' );
		}

		return $this->save();
	}

	function demote() {
		$this->is_mod = 0;
		$this->is_admin = 0;
		$this->user_title = false;

		return $this->save();
	}

	function ban() {
		if ( $this->is_admin )
			return false;

		$this->is_mod = 0;
		$this->is_banned = 1;

		events_update_eventmeta( $this->event_id, 'total_member_count', ( (int) events_get_eventmeta( $this->event_id, 'total_member_count' ) - 1 ) );

		$event_count = get_usermeta( $this->user_id, 'jes_total_event_count' );
		if ( !empty( $event_count ) )
			update_usermeta( $this->user_id, 'jes_total_event_count', (int)$event_count - 1 );

		return $this->save();
	}

	function unban() {
		if ( $this->is_admin )
			return false;

		$this->is_banned = 0;

		events_update_eventmeta( $this->event_id, 'total_member_count', ( (int) events_get_eventmeta( $this->event_id, 'total_member_count' ) + 1 ) );
		update_usermeta( $this->user_id, 'jes_total_event_count', (int)get_usermeta( $this->user_id, 'jes_total_event_count' ) + 1 );

		return $this->save();
	}

	function jes_accept_invite() {
		$this->inviter_id = 0;
		$this->is_confirmed = 1;
		$this->date_modified = gmdate( "Y-m-d H:i:s" );
	}

	function jes_accept_request() {
		$this->is_confirmed = 1;
		$this->date_modified = gmdate( "Y-m-d H:i:s" );
	}

	/* Static Functions */

	function delete( $user_id, $event_id ) {
		global $wpdb, $bp;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d", $user_id, $event_id ) );
	}

	function jes_get_event_ids( $user_id, $limit = false, $page = false ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		// If the user is logged in and viewing their own events, we can show hidden and private events
		if ( $user_id != $bp->loggedin_user->id ) {
			$event_sql = $wpdb->prepare( "SELECT DISTINCT m.event_id FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0{$pag_sqle}", $user_id );
			$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
		} else {
			$event_sql = $wpdb->prepare( "SELECT DISTINCT event_id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0{$pag_sqle}", $user_id );
			$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT event_id) FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id ) );
		}

		$events = $wpdb->get_col( $event_sql );

		return array( 'events' => $events, 'total' => (int) $total_events );
	}

	function jes_get_recently_joined( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( $filter ) {
			$filter = like_escape( $wpdb->escape( $filter ) );
			$filter_sql = " AND ( g.name LIKE '%%{$filter}%%' OR g.description LIKE '%%{$filter}%%' )";
		}

		if ( $user_id != $bp->loggedin_user->id )
			$hidden_sql = " AND g.status != 'hidden'";

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY m.date_modified DESC {$pag_sqle}", $user_id ) );
		$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND m.user_id = %d AND m.is_banned = 0 AND m.is_confirmed = 1 ORDER BY m.date_modified DESC", $user_id ) );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_get_is_admin_of( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( $filter ) {
			$filter = like_escape( $wpdb->escape( $filter ) );
			$filter_sql = " AND ( g.name LIKE '%%{$filter}%%' OR g.description LIKE '%%{$filter}%%' )";
		}

		if ( $user_id != $bp->loggedin_user->id )
			$hidden_sql = " AND g.status != 'hidden'";

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_admin = 1 ORDER BY m.date_modified ASC {$pag_sqle}", $user_id ) );
		$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_admin = 1 ORDER BY date_modified ASC", $user_id ) );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_get_is_mod_of( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( $filter ) {
			$filter = like_escape( $wpdb->escape( $filter ) );
			$filter_sql = " AND ( g.name LIKE '%%{$filter}%%' OR g.description LIKE '%%{$filter}%%' )";
		}

		if ( $user_id != $bp->loggedin_user->id )
			$hidden_sql = " AND g.status != 'hidden'";

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_mod = 1 ORDER BY m.date_modified ASC {$pag_sqle}", $user_id ) );
		$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_mod = 1 ORDER BY date_modified ASC", $user_id ) );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_total_event_count( $user_id = false ) {
		global $bp, $wpdb;

		if ( !$user_id )
			$user_id = $bp->displayed_user->id;

		if ( $user_id != $bp->loggedin_user->id && !is_site_admin() ) {
			return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id AND g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
		} else {
			return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
		}
	}

	function jes_get_invite_jes( $user_id, $limit = false, $page = false ) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->jes_events->table_name_eventmeta} gm1, {$bp->jes_events->table_name_eventmeta} gm2, {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND m.is_confirmed = 0 AND m.inviter_id != 0 AND m.invite_sent = 1 AND m.user_id = %d ORDER BY m.date_modified ASC {$pag_sqle}", $user_id ) );
		$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id AND m.is_confirmed = 0 AND m.inviter_id != 0 AND m.invite_sent = 1 AND m.user_id = %d ORDER BY date_modified ASC", $user_id ) );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	function jes_check_has_invite( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id != 0 AND invite_sent = 1", $user_id, $event_id ) );
	}

	function jes_delete_invite( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id != 0 AND invite_sent = 1", $user_id, $event_id ) );
	}

	function jes_delete_request( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

 		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id = 0 AND invite_sent = 0", $user_id, $event_id ) );
	}

	function jes_check_is_admin( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_admin = 1 AND is_banned = 0", $user_id, $event_id ) );
	}

	function jes_check_is_mod( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_mod = 1 AND is_banned = 0", $user_id, $event_id ) );
	}

	function jes_check_is_member( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id, $event_id ) );
	}

	function jes_check_is_banned( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->get_var( $wpdb->prepare( "SELECT is_banned FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d", $user_id, $event_id ) );
	}

	function jes_check_for_membership_request( $user_id, $event_id ) {
		global $wpdb, $bp;

		if ( !$user_id )
			return false;

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND is_banned = 0 AND inviter_id = 0", $user_id, $event_id ) );
	}

	function jes_get_random_events( $user_id, $total_events = 5 ) {
		global $wpdb, $bp;

		// If the user is logged in and viewing their random events, we can show hidden and private events
		if ( bp_is_my_profile() ) {
			return $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT event_id FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0 ORDER BY rand() LIMIT $total_events", $user_id ) );
		} else {
			return $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT m.event_id FROM {$bp->jes_events->jes_table_name_members} m, {$bp->jes_events->table_name} g WHERE m.event_id = g.id AND g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY rand() LIMIT $total_events", $user_id ) );
		}
	}

	function jes_get_event_member_ids( $event_id ) {
		global $bp, $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 1 AND is_banned = 0", $event_id ) );
	}

	function jes_get_event_administrator_ids( $event_id ) {
		global $bp, $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT user_id, date_modified FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_admin = 1 AND is_banned = 0", $event_id ) );
	}

	function get_event_moderator_ids( $event_id ) {
		global $bp, $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT user_id, date_modified FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_mod = 1 AND is_banned = 0", $event_id ) );
	}

	function jes_get_all_membership_request_user_ids( $event_id ) {
		global $bp, $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 0 AND inviter_id = 0", $event_id ) );
	}

	function jes_get_all_for_event( $event_id, $limit = false, $page = false, $exclude_admins_mods = true, $exclude_banned = true ) {
		global $bp, $wpdb;

		if ( $limit && $page )
			$pag_sqle = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( $exclude_admins_mods )
			$exclude_sql = $wpdb->prepare( "AND is_admin = 0 AND is_mod = 0" );

		if ( $exclude_banned )
			$banned_sql = $wpdb->prepare( " AND is_banned = 0" );

		if ( bp_is_active( 'xprofile' ) )
			$members = $wpdb->get_results( $wpdb->prepare( "SELECT m.user_id, m.date_modified, m.is_banned, u.user_login, u.user_nicename, u.user_email, pd.value as display_name FROM {$bp->jes_events->jes_table_name_members} m, {$wpdb->users} u, {$bp->profile->table_name_data} pd WHERE u.ID = m.user_id AND u.ID = pd.user_id AND pd.field_id = 1 AND event_id = %d AND is_confirmed = 1 {$banned_sql} {$exclude_sql} ORDER BY m.date_modified DESC {$pag_sqle}", $event_id ) );
		else
			$members = $wpdb->get_results( $wpdb->prepare( "SELECT m.user_id, m.date_modified, m.is_banned, u.user_login, u.user_nicename, u.user_email, u.display_name FROM {$bp->jes_events->jes_table_name_members} m, {$wpdb->users} u WHERE u.ID = m.user_id AND event_id = %d AND is_confirmed = 1 {$banned_sql} {$exclude_sql} ORDER BY m.date_modified DESC {$pag_sqle}", $event_id ) );

		if ( !$members )
			return false;

		if ( !isset($pag_sqle) )
			$total_member_count = count($members);
		else
			$total_member_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(user_id) FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d AND is_confirmed = 1 {$banned_sql} {$exclude_sql}", $event_id ) );

		/* Fetch whether or not the user is a friend */
		foreach ( (array)$members as $user ) $user_ids[] = $user->user_id;
		$user_ids = $wpdb->escape( join( ',', (array)$user_ids ) );

		if ( function_exists( 'friends_install' ) ) {
			$friend_status = $wpdb->get_results( $wpdb->prepare( "SELECT initiator_user_id, friend_user_id, is_confirmed FROM {$bp->friends->table_name} WHERE (initiator_user_id = %d AND friend_user_id IN ( {$user_ids} ) ) OR (initiator_user_id IN ( {$user_ids} ) AND friend_user_id = %d )", $bp->loggedin_user->id, $bp->loggedin_user->id ) );
			for ( $i = 0; $i < count( $members ); $i++ ) {
				foreach ( (array)$friend_status as $status ) {
					if ( $status->initiator_user_id == $members[$i]->user_id || $status->friend_user_id == $members[$i]->user_id )
						$members[$i]->is_friend = $status->is_confirmed;
				}
			}
		}

		return array( 'members' => $members, 'count' => $total_member_count );
	}

	function jes_delete_all( $event_id ) {
		global $wpdb, $bp;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->jes_table_name_members} WHERE event_id = %d", $event_id ) );
	}

	function jes_delete_all_for_user( $user_id ) {
		global $wpdb, $bp;

		// Get all the event ids for the current user's events and update counts
		$jes_event_ids = $this->jes_get_event_ids( $user_id );
		// fdd
		foreach ( $jes_event_ids->events as $event_id ) {
			events_update_eventmeta( $event_id, 'total_member_count', events_jes_get_total_member_count( $event_id ) - 1 );
		}

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->jes_events->jes_table_name_members} WHERE user_id = %d", $user_id ) );
	}
	
}

/**
 * API for creating event extensions without having to hardcode the content into
 * the theme.
 *
 * This class must be extended for each event extension and the following methods overridden:
 *
 * JES_Event_Extension::widget_display(), JES_Event_Extension::display(),
 * JES_Event_Extension::edit_screen_save(), JES_Event_Extension::edit_screen(),
 * JES_Event_Extension::create_screen_save(), JES_Event_Extension::create_screen()
 *
 * @package BuddyPress
 * @subpackage Events
 * @since 1.1
 */
class JES_Event_Extension {
	var $name = false;
	var $slug = false;

	/* Will this extension be visible to non-members of a event? Options: public/private */
	var $visibility = 'public';

	var $create_step_position = 81;
	var $nav_item_position = 81;

	var $enable_create_step = true;
	var $enable_nav_item = true;
	var $enable_edit_item = true;

	var $nav_item_name = false;

	var $display_hook = 'events_custom_event_boxes';
	var $template_file = 'events/single/plugins';

	// Methods you should override

	function display() {
		die( 'function JES_Event_Extension::display() must be over-ridden in a sub-class.' );
	}

	function widget_display() {
		die( 'function JES_Event_Extension::widget_display() must be over-ridden in a sub-class.' );
	}

	function edit_screen() {
		die( 'function JES_Event_Extension::edit_screen() must be over-ridden in a sub-class.' );
	}

	function edit_screen_save() {
		die( 'function JES_Event_Extension::edit_screen_save() must be over-ridden in a sub-class.' );
	}

	function create_screen() {
		die( 'function JES_Event_Extension::create_screen() must be over-ridden in a sub-class.' );
	}

	function create_screen_save() {
		die( 'function JES_Event_Extension::create_screen_save() must be over-ridden in a sub-class.' );
	}

	// Private Methods

	function _register() {
		global $bp;

		if ( $this->enable_create_step ) {
			/* Insert the event creation step for the new event extension */
			$bp->jes_events->event_creation_steps[$this->slug] = array( 'name' => $this->name, 'slug' => $this->slug, 'position' => $this->create_step_position );

			/* Attach the event creation step display content action */
			add_action( 'events_custom_create_steps', array( &$this, 'create_screen' ) );

			/* Attach the event creation step save content action */
			add_action( 'events_create_event_step_save_' . $this->slug, array( &$this, 'create_screen_save' ) );
		}

		/* Construct the admin edit tab for the new event extension */
		if ( $this->enable_edit_item ) {
			add_action( 'events_admin_tabs', create_function( '$current, $event_slug', 'if ( "' . attribute_escape( $this->slug ) . '" == $current ) $selected = " class=\"current\""; echo "<li{$selected}><a href=\"' . $bp->root_domain . '/' . $bp->jes_events->slug . '/{$event_slug}/admin/' . attribute_escape( $this->slug ) . '\">' . attribute_escape( $this->name ) . '</a></li>";' ), 10, 2 );

			/* Catch the edit screen and forward it to the plugin template */
			if ( $bp->current_component == $bp->jes_events->slug && 'admin' == $bp->current_action && $this->slug == $bp->action_variables[0] ) {
				add_action( 'wp', array( &$this, 'edit_screen_save' ) );
				add_action( 'events_custom_edit_steps', array( &$this, 'edit_screen' ) );

				if ( '' != locate_template( array( 'events/single/home.php' ), false ) ) {
					bp_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
				} else {
					add_action( 'bp_template_content_header', create_function( '', 'echo "<ul class=\"content-header-nav\">"; bp_event_admin_tabs(); echo "</ul>";' ) );
					add_action( 'bp_template_content', array( &$this, 'edit_screen' ) );
					bp_core_load_template( apply_filters( 'bp_core_template_plugin', '/events/single/plugins' ) );
				}
			}
		}

		/* When we are viewing a single event, add the event extension nav item */
		if ( $this->visbility == 'public' || ( $this->visbility != 'public' && $bp->jes_events->current_event->user_has_access ) ) {
			if ( $this->enable_nav_item ) {
				if ( $bp->current_component == $bp->jes_events->slug && $bp->is_single_item )
					bp_core_new_subnav_item( array( 'name' => ( !$this->nav_item_name ) ? $this->name : $this->nav_item_name, 'slug' => $this->slug, 'parent_slug' => JES_SLUG, 'parent_url' => jes_bp_get_event_permalink( $bp->jes_events->current_event ), 'position' => $this->nav_item_position, 'item_css_id' => 'nav-' . $this->slug, 'screen_function' => array( &$this, '_display_hook' ), 'user_has_access' => $this->enable_nav_item ) );

				/* When we are viewing the extension display page, set the title and options title */
				if ( $bp->current_component == $bp->jes_events->slug && $bp->is_single_item && $bp->current_action == $this->slug ) {
					add_action( 'bp_template_content_header', create_function( '', 'echo "' . attribute_escape( $this->name ) . '";' ) );
			 		add_action( 'bp_template_title', create_function( '', 'echo "' . attribute_escape( $this->name ) . '";' ) );
				}
			}

			/* Hook the event home widget */
			if ( $bp->current_component == $bp->jes_events->slug && $bp->is_single_item && ( !$bp->current_action || 'home' == $bp->current_action ) )
				add_action( $this->display_hook, array( &$this, 'widget_display' ) );
		}
	}

	function _display_hook() {
		add_action( 'bp_template_content', array( &$this, 'display' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', $this->template_file ) );
	}
}

function jes_bp_register_event_extension( $jes_event_extension_class ) {
	global $bp;

	if ( !class_exists( $jes_event_extension_class ) )
		return false;

	/* Register the event extension on the bp_init action so we have access to all plugins */
	add_action( 'bp_init', create_function( '', '$extension = new ' . $jes_event_extension_class . '; add_action( "wp", array( &$extension, "_register" ), 2 );' ), 11 );
}


?>