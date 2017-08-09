<?php

function events_notification_event_updated( $event_id ) {
	global $bp;

	$event = new JES_Events_Event( $event_id );
	$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . __( 'Event Details Updated', 'jet-event-system' );

	$user_ids = JES_Events_Member::jes_get_event_member_ids( $event->id );
	foreach ( (array)$user_ids as $user_id ) {
		if ( 'no' == get_usermeta( $user_id, 'notification_events_event_updated' ) ) continue;

		$ud = bp_core_get_core_userdata( $user_id );

		// Set up and send the message
		$to = $ud->user_email;

		$event_link = site_url( $bp->jes_events->slug . '/' . $event->slug );
		$settings_link = bp_core_get_user_domain( $user_id ) .  BP_SETTINGS_SLUG . '/notifications/';

		$message = sprintf( __(
'Event details for the event "%s" were updated:

To view the event: %s

---------------------
', 'jet-event-system' ), $event->name, $event_link );

		$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'jet-event-system' ), $settings_link );

		/* Send the message */
		$to = apply_filters( 'events_notification_event_updated_to', $to );
		$subject = apply_filters( 'events_notification_event_updated_subject', $subject, &$event );
		$message = apply_filters( 'events_notification_event_updated_message', $message, &$event, $event_link );

		wp_mail( $to, $subject, $message );

		unset( $message, $to );
	}
}

function events_notification_new_membership_request( $requesting_user_id, $admin_id, $event_id, $membership_id ) {
	global $bp;

	bp_core_add_notification( $requesting_user_id, $admin_id, 'events', 'new_membership_request', $event_id );

	if ( 'no' == get_usermeta( $admin_id, 'notification_events_membership_request' ) )
		return false;

	$requesting_user_name = bp_core_get_user_displayname( $requesting_user_id );
	$event = new JES_Events_Event( $event_id );

	$ud = bp_core_get_core_userdata($admin_id);
	$requesting_ud = bp_core_get_core_userdata($requesting_user_id);

	$event_requests = jes_bp_get_event_permalink( $event ) . 'admin/membership-requests';
	$profile_link = bp_core_get_user_domain( $requesting_user_id );
	$settings_link = bp_core_get_user_domain( $requesting_user_id ) .  BP_SETTINGS_SLUG . '/notifications/';

	// Set up and send the message
	$to = $ud->user_email;
	$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( 'Membership request for event: %s', 'jet-event-system' ), $event->name );

$message = sprintf( __(
'%s wants to join the event "%s".

Because you are the administrator of this event, you must either accept or reject the membership request.

To view all pending membership requests for this event, please visit:
%s

To view %s\'s profile: %s

---------------------
', 'jet-event-system' ), $requesting_user_name, $event->name, $event_requests, $requesting_user_name, $profile_link );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'jet-event-system' ), $settings_link );

	/* Send the message */
	$to = apply_filters( 'events_notification_new_membership_request_to', $to );
	$subject = apply_filters( 'events_notification_new_membership_request_subject', $subject, &$event );
	$message = apply_filters( 'events_notification_new_membership_request_message', $message, &$event, $requesting_user_name, $profile_link, $event_requests );

	wp_mail( $to, $subject, $message );
}

function events_notification_membership_request_completed( $requesting_user_id, $event_id, $accepted = true ) {
	global $bp;

	// Post a screen notification first.
	if ( $accepted )
		bp_core_add_notification( $event_id, $requesting_user_id, 'events', 'membership_request_accepted' );
	else
		bp_core_add_notification( $event_id, $requesting_user_id, 'events', 'membership_request_rejected' );

	if ( 'no' == get_usermeta( $requesting_user_id, 'notification_membership_request_completed' ) )
		return false;

	$event = new JES_Events_Event( $event_id );

	$ud = bp_core_get_core_userdata($requesting_user_id);

	$event_link = jes_bp_get_event_permalink( $event );
	$settings_link = bp_core_get_user_domain( $requesting_user_id ) .  BP_SETTINGS_SLUG . '/notifications/';

	// Set up and send the message
	$to = $ud->user_email;

	if ( $accepted ) {
		$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( 'Membership request for event "%s" accepted', 'jet-event-system' ), $event->name );
		$message = sprintf( __(
'Your membership request for the event "%s" has been accepted.

To view the event please login and visit: %s

---------------------
', 'jet-event-system' ), $event->name, $event_link );

	} else {
		$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( 'Membership request for event "%s" rejected', 'jet-event-system' ), $event->name );
		$message = sprintf( __(
'Your membership request for the event "%s" has been rejected.

To submit another request please log in and visit: %s

---------------------
', 'jet-event-system' ), $event->name, $event_link );
	}

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'jet-event-system' ), $settings_link );

	/* Send the message */
	$to = apply_filters( 'events_notification_membership_request_completed_to', $to );
	$subject = apply_filters( 'events_notification_membership_request_completed_subject', $subject, &$event );
	$message = apply_filters( 'events_notification_membership_request_completed_message', $message, &$event, $event_link  );

	wp_mail( $to, $subject, $message );
}

function events_notification_promoted_member( $user_id, $event_id ) {
	global $bp;

	if ( events_is_user_admin( $user_id, $event_id ) ) {
		$promoted_to = __( 'an administrator', 'jet-event-system' );
		$type = 'member_promoted_to_admin';
	} else {
		$promoted_to = __( 'a moderator', 'jet-event-system' );
		$type = 'member_promoted_to_mod';
	}

	// Post a screen notification first.
	bp_core_add_notification( $event_id, $user_id, 'events', $type );

	if ( 'no' == get_usermeta( $user_id, 'notification_events_admin_promotion' ) )
		return false;

	$event = new JES_Events_Event( $event_id );
	$ud = bp_core_get_core_userdata($user_id);

	$event_link = jes_bp_get_event_permalink( $event );
	$settings_link = bp_core_get_user_domain( $user_id ) .  BP_SETTINGS_SLUG . '/notifications/';

	// Set up and send the message
	$to = $ud->user_email;

	$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( 'You have been promoted in the event: "%s"', 'jet-event-system' ), $event->name );

	$message = sprintf( __(
'You have been promoted to %s for the event: "%s".

To view the event please visit: %s

---------------------
', 'jet-event-system' ), $promoted_to, $event->name, $event_link );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'jet-event-system' ), $settings_link );

	/* Send the message */
	$to = apply_filters( 'events_notification_promoted_member_to', $to );
	$subject = apply_filters( 'events_notification_promoted_member_subject', $subject, &$event );
	$message = apply_filters( 'events_notification_promoted_member_message', $message, &$event, $promoted_to, $event_link );

	wp_mail( $to, $subject, $message );
}
add_action( 'events_promoted_member', 'events_notification_promoted_member', 10, 2 );

function events_notification_jes_event_invite_jes( &$event, &$member, $inviter_user_id ) {
	global $bp;

	$inviter_ud = bp_core_get_core_userdata( $inviter_user_id );
	$inviter_name = bp_core_get_userlink( $inviter_user_id, true, false, true );
	$inviter_link = bp_core_get_user_domain( $inviter_user_id );

	$event_link = jes_bp_get_event_permalink( $event );

	if ( !$member->invite_sent ) {
		$invited_user_id = $member->user_id;

		// Post a screen notification first.
		bp_core_add_notification( $event->id, $invited_user_id, 'events', 'jes_event_invite' );

		if ( 'no' == get_usermeta( $invited_user_id, 'notification_events_invite' ) )
			return false;

		$invited_ud = bp_core_get_core_userdata($invited_user_id);

		$settings_link = bp_core_get_user_domain( $invited_user_id ) .  BP_SETTINGS_SLUG . '/notifications/';
		$invited_link = bp_core_get_user_domain( $invited_user_id );
		$invites_link = $invited_link . $bp->jes_events->slug . '/invites';

		// Set up and send the message
		$to = $invited_ud->user_email;

		$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( 'You have an invitation to the event: "%s"', 'jet-event-system' ), $event->name );

		$message = sprintf( __(
'One of your friends %s has invited you to the event: "%s".

To view your event invites visit: %s

To view the event visit: %s

To view %s\'s profile visit: %s

---------------------
', 'jet-event-system' ), $inviter_name, $event->name, $invites_link, $event_link, $inviter_name, $inviter_link );

		$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'jet-event-system' ), $settings_link );

		/* Send the message */
		$to = apply_filters( 'events_notification_jes_event_invites_to', $to );
		$subject = apply_filters( 'events_notification_jes_event_invites_subject', $subject, &$event );
		$message = apply_filters( 'events_notification_jes_event_invites_message', $message, &$event, $inviter_name, $inviter_link, $invites_link, $event_link );

		wp_mail( $to, $subject, $message );
	}
}

function events_at_message_notification( $content, $poster_user_id, $event_id, $activity_id ) {
	global $bp;

	/* Scan for @username strings in an activity update. Notify each user. */
	$pattern = '/[@]+([A-Za-z0-9-_]+)/';
	preg_match_all( $pattern, $content, $usernames );

	/* Make sure there's only one instance of each username */
	if ( !$usernames = array_unique( $usernames[1] ) )
		return false;

	$event = new JES_Events_Event( $event_id );

	foreach( (array)$usernames as $username ) {
		if ( !$receiver_user_id = bp_core_get_userid($username) )
			continue;

		/* Check the user is a member of the event before sending the update. */
		if ( !events_is_user_member( $receiver_user_id, $event_id ) )
			continue;

		// Now email the user with the contents of the message (if they have enabled email notifications)
		if ( 'no' != get_usermeta( $user_id, 'notification_activity_new_mention' ) ) {
			$poster_name = bp_core_get_user_displayname( $poster_user_id );

			$message_link = bp_activity_get_permalink( $activity_id );
			$settings_link = bp_core_get_user_domain( $receiver_user_id ) .  BP_SETTINGS_SLUG . '/notifications/';

			$poster_name = stripslashes( $poster_name );
			$content = jes_bp_events_filter_kses( stripslashes( $content ) );

			// Set up and send the message
			$ud = bp_core_get_core_userdata( $receiver_user_id );
			$to = $ud->user_email;
			$subject = '[' . get_site_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( '%s mentioned you in the event "%s"', 'jet-event-system' ), $poster_name, $event->name );

$message = sprintf( __(
'%s mentioned you in the event "%s":

"%s"

To view and respond to the message, log in and visit: %s

---------------------
', 'jet-event-system' ), $poster_name, $event->name, $content, $message_link );

			$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'jet-event-system' ), $settings_link );

			/* Send the message */
			$to = apply_filters( 'events_at_message_notification_to', $to );
			$subject = apply_filters( 'events_at_message_notification_subject', $subject, &$event, $poster_name );
			$message = apply_filters( 'events_at_message_notification_message', $message, &$event, $poster_name, $content, $message_link );

			wp_mail( $to, $subject, $message );
		}
	}
}
add_action( 'jes_bp_events_posted_update', 'events_at_message_notification', 10, 4 );


?>