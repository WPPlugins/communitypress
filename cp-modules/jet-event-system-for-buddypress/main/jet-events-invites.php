<?php
/*** Event Invitations *********************************************************/

function events_jes_get_invites_for_user( $user_id = false, $limit = false, $page = false ) {
	global $bp;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	return JES_Events_Member::jes_get_invite_jes( $user_id, $limit, $page );
}

function events_invite_user( $args = '' ) {
	global $bp;

	$defaults = array(
		'user_id' => false,
		'event_id' => false,
		'inviter_id' => $bp->loggedin_user->id,
		'date_modified' => gmdate( "Y-m-d H:i:s" ),
		'is_confirmed' => 0
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	if ( !$user_id || !$event_id )
		return false;

	if ( !events_is_user_member( $user_id, $event_id ) && !events_check_user_has_invite( $user_id, $event_id ) ) {
		$invite = new JES_Events_Member;
		$invite->event_id = $event_id;
		$invite->user_id = $user_id;
		$invite->date_modified = $date_modified;
		$invite->inviter_id = $inviter_id;
		$invite->is_confirmed = $is_confirmed;

		if ( !$invite->save() )
			return false;

		do_action( 'events_invite_user', $args );
	}

	return true;
}

function events_uninvite_user( $user_id, $event_id ) {
	global $bp;

	if ( !JES_Events_Member::delete( $user_id, $event_id ) )
		return false;

	do_action( 'events_uninvite_user', $event_id, $user_id );

	return true;
}

function events_jes_accept_invite( $user_id, $event_id ) {
	global $bp;

	if ( events_is_user_member( $user_id, $event_id ) )
		return false;

	$member = new JES_Events_Member( $user_id, $event_id );
	$member->jes_accept_invite();

	if ( !$member->save() )
		return false;

	/* Remove request to join */
	if ( $member->jes_check_for_membership_request( $user_id, $event_id ) )
		$member->jes_delete_request( $user_id, $event_id );

	/* Modify event meta */
	events_update_eventmeta( $event_id, 'total_member_count', (int) events_get_eventmeta( $event_id, 'total_member_count') + 1 );
	events_update_eventmeta( $event_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );

	bp_core_delete_notifications_for_user_by_item_id( $user_id, $event_id, $bp->jes_events->id, 'jes_event_invite' );

	do_action( 'events_jes_accept_invite', $user_id, $event_id );
	return true;
}

function events_reject_invite( $user_id, $event_id ) {
	if ( !JES_Events_Member::delete( $user_id, $event_id ) )
		return false;

	do_action( 'events_reject_invite', $user_id, $event_id );

	return true;
}

function events_jes_delete_invite( $user_id, $event_id ) {
	global $bp;

	$delete = JES_Events_Member::jes_delete_invite( $user_id, $event_id );

	if ( $delete )
		bp_core_delete_notifications_for_user_by_item_id( $user_id, $event_id, $bp->jes_events->id, 'jes_event_invite' );

	return $delete;
}

function events_send_invite_jes( $user_id, $event_id ) {
	global $bp;

	require_once ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-notifications.php' );

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	// Send friend invites.
	$invited_users = events_jes_get_invites_for_event( $user_id, $event_id );
	$event = new JES_Events_Event( $event_id );

	for ( $i = 0; $i < count( $invited_users ); $i++ ) {
		$member = new JES_Events_Member( $invited_users[$i], $event_id );

		// Send the actual invite
		events_notification_jes_event_invite_jes( $event, $member, $user_id );

		$member->invite_sent = 1;
		$member->save();
	}

	do_action( 'events_send_invites', $bp->jes_events->current_event->id, $invited_users );
}

function events_jes_get_invites_for_event( $user_id, $event_id ) {
	return JES_Events_Event::jes_get_invite_jes( $user_id, $event_id );
}

function events_check_user_has_invite( $user_id, $event_id ) {
	return JES_Events_Member::jes_check_has_invite( $user_id, $event_id );
}

function events_jes_delete_all_jes_event_invite_jes( $event_id ) {
	return JES_Events_Event::jes_jes_delete_all_invite_jes( $event_id );
}

?>