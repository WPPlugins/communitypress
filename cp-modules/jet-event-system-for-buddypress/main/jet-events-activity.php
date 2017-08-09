<?php
/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */

function events_register_activity_actions() {
	global $bp;

	if ( !function_exists( 'bp_activity_set_action' ) )
		return false;

	bp_activity_set_action( $bp->jes_events->id, 'created_event', __( 'Created a event', 'jet-event-system' ) );
	bp_activity_set_action( $bp->jes_events->id, 'joined_event', __( 'Joined a event', 'jet-event-system' ) );

	do_action( 'events_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'events_register_activity_actions' );

function events_record_activity( $args = '' ) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	/* If the event is not public, hide the activity sitewide. */
	if ( 'public' == $bp->jes_events->current_event->status )
		$hide_sitewide = false;
	else
		$hide_sitewide = true;

	$defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->jes_events->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => $hide_sitewide
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

function events_update_last_activity( $event_id ) {
	events_update_eventmeta( $event_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );
}
add_action( 'events_joined_event', 'events_update_last_activity' );
add_action( 'events_leave_event', 'events_update_last_activity' );
add_action( 'events_created_event', 'events_update_last_activity' );


function events_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {
		case 'new_membership_request':
			$event_id = $secondary_item_id;
			$requesting_user_id = $item_id;

			$event = new JES_Events_Event( $event_id );

			$event_link = jes_bp_get_event_permalink( $event );

			if ( (int)$total_items > 1 ) {
				return apply_filters( 'jes_bp_events_multiple_new_membership_requests_notification', '<a href="' . $event_link . '/admin/membership-requests/?n=1" title="' . __( 'Event Membership Requests', 'jet-event-system' ) . '">' . sprintf( __( '%d new membership requests for the event "%s"', 'jet-event-system' ), (int)$total_items, $event->name ) . '</a>', $event_link, $total_items, $event->name );
			} else {
				$user_fullname = bp_core_get_user_displayname( $requesting_user_id );
				return apply_filters( 'jes_bp_events_single_new_membership_request_notification', '<a href="' . $event_link . 'admin/membership-requests/?n=1" title="' . $user_fullname .' requests event membership">' . sprintf( __( '%s requests membership for the event "%s"', 'jet-event-system' ), $user_fullname, $event->name ) . '</a>', $event_link, $user_fullname, $event->name );
			}
		break;

		case 'membership_request_accepted':
			$event_id = $item_id;

			$event = new JES_Events_Event( $event_id );
			$event_link = jes_bp_get_event_permalink( $event );

			if ( (int)$total_items > 1 )
				return apply_filters( 'jes_bp_events_multiple_membership_request_accepted_notification', '<a href="' . $bp->loggedin_user->domain . $bp->jes_events->slug . '/?n=1" title="' . __( 'Events', 'jet-event-system' ) . '">' . sprintf( __( '%d accepted event membership requests', 'jet-event-system' ), (int)$total_items, $event->name ) . '</a>', $total_items, $event_name );
			else
				return apply_filters( 'jes_bp_events_single_membership_request_accepted_notification', '<a href="' . $event_link . '?n=1">' . sprintf( __( 'Membership for event "%s" accepted', 'jet-event-system' ), $event->name ) . '</a>', $event_link, $event->name );

		break;

		case 'membership_request_rejected':
			$event_id = $item_id;

			$event = new JES_Events_Event( $event_id );
			$event_link = jes_bp_get_event_permalink( $event );

			if ( (int)$total_items > 1 )
				return apply_filters( 'jes_bp_events_multiple_membership_request_rejected_notification', '<a href="' . site_url() . '/' . BP_MEMBERS_SLUG . '/' . $bp->jes_events->slug . '/?n=1" title="' . __( 'Events', 'jet-event-system' ) . '">' . sprintf( __( '%d rejected event membership requests', 'jet-event-system' ), (int)$total_items, $event->name ) . '</a>', $total_items, $event->name );
			else
				return apply_filters( 'jes_bp_events_single_membership_request_rejected_notification', '<a href="' . $event_link . '?n=1">' . sprintf( __( 'Membership for event "%s" rejected', 'jet-event-system' ), $event->name ) . '</a>', $event_link, $event->name );

		break;

		case 'member_promoted_to_admin':
			$event_id = $item_id;

			$event = new JES_Events_Event( $event_id );
			$event_link = jes_bp_get_event_permalink( $event );

			if ( (int)$total_items > 1 )
				return apply_filters( 'jes_bp_events_multiple_member_promoted_to_admin_notification', '<a href="' . $bp->loggedin_user->domain . $bp->jes_events->slug . '/?n=1" title="' . __( 'Events', 'jet-event-system' ) . '">' . sprintf( __( 'You were promoted to an admin in %d events', 'jet-event-system' ), (int)$total_items ) . '</a>', $total_items );
			else
				return apply_filters( 'jes_bp_events_single_member_promoted_to_admin_notification', '<a href="' . $event_link . '?n=1">' . sprintf( __( 'You were promoted to an admin in the event %s', 'jet-event-system' ), $event->name ) . '</a>', $event_link, $event->name );

		break;

		case 'member_promoted_to_mod':
			$event_id = $item_id;

			$event = new JES_Events_Event( $event_id );
			$event_link = jes_bp_get_event_permalink( $event );

			if ( (int)$total_items > 1 )
				return apply_filters( 'jes_bp_events_multiple_member_promoted_to_mod_notification', '<a href="' . $bp->loggedin_user->domain . $bp->jes_events->slug . '/?n=1" title="' . __( 'Events', 'jet-event-system' ) . '">' . sprintf( __( 'You were promoted to a mod in %d events', 'jet-event-system' ), (int)$total_items ) . '</a>', $total_items );
			else
				return apply_filters( 'jes_bp_events_single_member_promoted_to_mod_notification', '<a href="' . $event_link . '?n=1">' . sprintf( __( 'You were promoted to a mod in the event %s', 'jet-event-system' ), $event->name ) . '</a>', $event_link, $event->name );

		break;

		case 'jes_event_invite':
			$event_id = $item_id;

			$event = new JES_Events_Event( $event_id );
			$user_url = bp_core_get_user_domain( $user_id );

			if ( (int)$total_items > 1 )
				return apply_filters( 'jes_bp_events_multiple_jes_event_invite_notification', '<a href="' . $bp->loggedin_user->domain . $bp->jes_events->slug . '/invites/?n=1" title="' . __( 'Event Invites', 'jet-event-system' ) . '">' . sprintf( __( 'You have %d new event invitations', 'jet-event-system' ), (int)$total_items ) . '</a>', $total_items );
			else
				return apply_filters( 'jes_bp_events_single_jes_event_invite_notification', '<a href="' . $bp->loggedin_user->domain . $bp->jes_events->slug . '/invites/?n=1" title="' . __( 'Event Invites', 'jet-event-system' ) . '">' . sprintf( __( 'You have an invitation to the event: %s', 'jet-event-system' ), $event->name ) . '</a>', $event->name );

		break;
	}

	do_action( 'events_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}
?>