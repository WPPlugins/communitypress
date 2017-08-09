<?php
/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

function events_screen_my_events() {
	global $bp;

	if ( isset($_GET['n']) ) {
		// Delete event request notifications for the user
		bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'membership_request_accepted' );
		bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'membership_request_rejected' );
		bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'member_promoted_to_mod' );
		bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'member_promoted_to_admin' );
	}

	do_action( 'events_screen_my_events' );

	bp_core_load_template( apply_filters( 'events_template_my_events', 'members/single/events' ) );
}

function events_screen_jes_event_invite_jes() {
	global $bp;

	$event_id = $bp->action_variables[1];

	if ( isset($bp->action_variables) && in_array( 'accept', (array)$bp->action_variables ) && is_numeric($event_id) ) {
		/* Check the nonce */
		if ( !check_admin_referer( 'events_jes_accept_invite' ) )
			return false;

		if ( !events_jes_accept_invite( $bp->loggedin_user->id, $event_id ) ) {
			bp_core_add_message( __('Event invite could not be accepted', 'jet-event-system'), 'error' );
		} else {
			bp_core_add_message( __('Event invite accepted', 'jet-event-system') );

			/* Record this in activity streams */
			$event = new JES_Events_Event( $event_id );

			events_record_activity( array(
				'action' => apply_filters( 'events_activity_accepted_invite_action', sprintf( __( '%s joined the event %s', 'jet-event-system'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . jes_bp_get_event_permalink( $event ) . '">' . attribute_escape( $event->name ) . '</a>' ), $bp->loggedin_user->id, &$event ),
				'type' => 'joined_event',
				'item_id' => $event->id
			) );
		}

		bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/' . $bp->current_action );

	} else if ( isset($bp->action_variables) && in_array( 'reject', (array)$bp->action_variables ) && is_numeric($event_id) ) {
		/* Check the nonce */
		if ( !check_admin_referer( 'events_reject_invite' ) )
			return false;

		if ( !events_reject_invite( $bp->loggedin_user->id, $event_id ) ) {
			bp_core_add_message( __('Event invite could not be rejected', 'jet-event-system'), 'error' );
		} else {
			bp_core_add_message( __('Event invite rejected', 'jet-event-system') );
		}

		bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/' . $bp->current_action );
	}

	// Remove notifications
	bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'jes_event_invite' );

	do_action( 'events_screen_jes_event_invites', $event_id );

	bp_core_load_template( apply_filters( 'events_template_jes_event_invites', 'members/single/events' ) );
}

function events_screen_event_home() {
	global $bp;

	if ( $bp->is_single_item ) {
		if ( isset($_GET['n']) ) {
			// Delete event request notifications for the user
			bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'membership_request_accepted' );
			bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'membership_request_rejected' );
			bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'member_promoted_to_mod' );
			bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'member_promoted_to_admin' );
		}

		do_action( 'events_screen_event_home' );

		bp_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
	}
}

function events_screen_jes_event_google_map() {
	global $bp;

		do_action( 'events_screen_jes_event_google_map' );

		bp_core_load_template( apply_filters( 'events_template_event_google_map', 'events/single/home' ) );
}

function events_screen_jes_event_flyer() {
	global $bp;

		do_action( 'events_screen_jes_event_flyer' );

		bp_core_load_template( apply_filters( 'events_template_event_flyer', 'events/single/home' ) );
}

function events_screen_event_members() {
	global $bp;

	if ( $bp->is_single_item ) {
		/* Refresh the event member count meta */
		events_update_eventmeta( $bp->jes_events->current_event->id, 'total_member_count', events_jes_get_total_member_count( $bp->jes_events->current_event->id ) );

		do_action( 'events_screen_event_members', $bp->jes_events->current_event->id );
		bp_core_load_template( apply_filters( 'events_template_event_members', 'events/single/home' ) );
	}
}

function events_screen_jes_event_invite() {
	global $bp;

	if ( $bp->is_single_item ) {
		if ( isset($bp->action_variables) && 'send' == $bp->action_variables[0] ) {

			if ( !check_admin_referer( 'events_send_invites', '_wpnonce_send_invites' ) )
				return false;

			// Send the invites.
			events_send_invite_jes( $bp->loggedin_user->id, $bp->jes_events->current_event->id );

			bp_core_add_message( __('Event invites sent.', 'jet-event-system') );

			do_action( 'events_screen_jes_event_invite', $bp->jes_events->current_event->id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) );
		} else {
			// Show send invite page
			bp_core_load_template( apply_filters( 'events_template_jes_event_invite', 'events/single/home' ) );
		}
	}
}

function events_screen_event_request_membership() {
	global $bp;

	if ( !is_user_logged_in() )
		return false;

	if ( 'private' == $bp->jes_events->current_event->status ) {
		// If the user has submitted a request, send it.
		if ( isset( $_POST['event-request-send']) ) {
			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_request_join_to_event' ) )
				return false;

			if ( !events_send_membership_request( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) ) {
				bp_core_add_message( __( 'There was an error sending your join event request, please try again.', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'Your request to join was sent to the event administrator successfully. You will be notified when the event administrator responds to your request.', 'jet-event-system' ) );
			}
			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) );
		}

		do_action( 'events_screen_event_request_membership', $bp->jes_events->current_event->id );

		bp_core_load_template( apply_filters( 'events_template_event_request_membership', 'events/single/home' ) );
	}
}

function events_screen_event_activity_permalink() {
	global $bp;

	if ( $bp->current_component != $bp->jes_events->slug || $bp->current_action != $bp->activity->slug || empty( $bp->action_variables[0] ) )
		return false;

	$bp->is_single_item = true;

	bp_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}
add_action( 'wp', 'events_screen_event_activity_permalink', 3 );

function events_screen_event_admin() {
	global $bp;

	if ( $bp->current_component != JES_SLUG || 'admin' != $bp->current_action )
		return false;

	if ( !empty( $bp->action_variables[0] ) )
		return false;

	bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/edit-details/' );
}

function events_screen_event_admin_edit_details() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && 'edit-details' == $bp->action_variables[0] ) {

		if ( $bp->is_item_admin || $bp->is_item_mod  ) {

			// If the edit form has been submitted, save the edited details
			if ( isset( $_POST['save'] ) ) {
				/* Check the nonce first. */
				if ( !check_admin_referer( 'events_edit_event_details' ) )
					return false;
			if ( empty( $_POST['event-edted'] ) ) { $eventedted = $_POST['event-edtsd']; } else { $eventedted = $_POST['event-edted']; }

// JLL_MOD - Modded all over - look care over entire file :(
if ( jes_datetounix($_POST['event-edtsd'],$_POST['event-edtsth'],$_POST['event-edtstm']) > jes_datetounix($eventedted,$_POST['event-edteth'],$_POST['event-edtetm'])) {
	$eventedted = $_POST['event-edtsd'];			
}
if ( empty( $_POST['event-edtsth'] ) ) {bp_core_add_message( __( 'There was an error updating event details. Please set the start time', 'jet-event-system' ), 'error' );}
elseif ( jes_datetounix($_POST['event-edtsd'],$_POST['event-edtsth'],$_POST['event-edtstm']) > jes_datetounix($eventedted,$_POST['event-edteth'],$_POST['event-edtetm']) ) {
					bp_core_add_message( __( 'There was an error updating event details. Date and time of completion of the event can not exceed the date of its beginning, please try again.', 'jet-event-system' ), 'error' );
} else {					
				if ( !events_edit_base_event_details( $_POST['event-id'], $_POST['event-name'], $_POST['event-etype'], $_POST['event-eventapproved'], $_POST['event-desc'], $_POST['event-eventterms'], $_POST['event-placedcountry'], $_POST['event-placedstate'], $_POST['event-placedcity'], $_POST['event-placedaddress'], $_POST['event-placednote'], $_POST['event-placedgooglemap'], $_POST['event-flyer'], $_POST['event-newspublic'], $_POST['event-newsprivate'], $_POST['event-edtsd'], $eventedted, $_POST['event-edtsth'], $_POST['event-edteth'], $_POST['event-edtstm'], $_POST['event-edtetm'] ,(int)$_POST['event-notify-members'], $_POST['notifytimedenable'] ) ) {
					bp_core_add_message( __( 'There was an error updating event details, please try again.', 'jet-event-system' ), 'error' );
				} else {
					bp_core_add_message( __( 'Event details were successfully updated.', 'jet-event-system' ) );
				}
}
				do_action( 'events_event_details_edited', $bp->jes_events->current_event->id );

				bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/edit-details/' );
			}

			do_action( 'events_screen_event_admin_edit_details', $bp->jes_events->current_event->id );

			bp_core_load_template( apply_filters( 'events_template_event_admin', 'events/single/home' ) );
		}
	}
}
add_action( 'wp', 'events_screen_event_admin_edit_details', 4 );

function events_screen_event_admin_settings() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && 'event-settings' == $bp->action_variables[0] ) {

		if ( !$bp->is_item_admin )
			return false;

		// If the edit form has been submitted, save the edited details
		if ( isset( $_POST['save'] ) ) {
			$enable_forum = ( isset($_POST['event-show-forum'] ) ) ? 1 : 0;
			$allowed_status = apply_filters( 'events_allowed_status', array( 'public', 'private', 'hidden' ) );
			$status = ( in_array( $_POST['event-status'], (array)$allowed_status ) ) ? $_POST['event-status'] : 'public';

			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_edit_event_settings' ) )
				return false;

			if ( !events_edit_event_settings( $_POST['event-id'], $enable_forum, $_POST['event-grouplink'], $_POST['event-forumlink'], $_POST['enablesocial'], $status ) ) {
				bp_core_add_message( __( 'There was an error updating event settings, please try again.', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'Event settings were successfully updated.', 'jet-event-system' ) );
			}

			do_action( 'events_event_settings_edited', $bp->jes_events->current_event->id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/event-settings/' );
		}

		do_action( 'events_screen_event_admin_settings', $bp->jes_events->current_event->id );

		bp_core_load_template( apply_filters( 'events_template_event_admin_settings', 'events/single/home' ) );
	}
}
add_action( 'wp', 'events_screen_event_admin_settings', 4 );

function events_screen_event_admin_avatar() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && 'event-avatar' == $bp->action_variables[0] ) {

		if ( !$bp->is_item_admin )
			return false;

		/* If the event admin has deleted the admin avatar */
		if ( 'delete' == $bp->action_variables[1] ) {

			/* Check the nonce */
			check_admin_referer( 'jes_bp_event_avatar_delete' );

			if ( jes_core_delete_existing_avatar( array( 'item_id' => $bp->jes_events->current_event->id, 'object' => 'event' ) ) )
				bp_core_add_message( __( 'Your avatar was deleted successfully!', 'jet-event-system' ) );
			else
				bp_core_add_message( __( 'There was a problem deleting that avatar, please try again.', 'jet-event-system' ), 'error' );

		}

		$bp->avatar_admin->step = 'upload-image';

		if ( !empty( $_FILES ) ) {

			/* Check the nonce */
			check_admin_referer( 'bp_avatar_upload' );

			/* Pass the file to the avatar upload handler */
			if ( bp_core_avatar_handle_upload( $_FILES, 'events_avatar_upload_dir' ) ) {
				$bp->avatar_admin->step = 'crop-image';

				/* Make sure we include the jQuery jCrop file for image cropping */
				add_action( 'wp', 'bp_core_add_jquery_cropper' );
			}

		}

		/* If the image cropping is done, crop the image and save a full/thumb version */
		if ( isset( $_POST['avatar-crop-submit'] ) ) {

			/* Check the nonce */
			check_admin_referer( 'bp_avatar_cropstore' );

			if ( !bp_core_avatar_handle_crop( array( 'object' => 'event', 'avatar_dir' => 'event-avatars', 'item_id' => $bp->jes_events->current_event->id, 'original_file' => $_POST['image_src'], 'crop_x' => $_POST['x'], 'crop_y' => $_POST['y'], 'crop_w' => $_POST['w'], 'crop_h' => $_POST['h'] ) ) )
				bp_core_add_message( __( 'There was a problem cropping the avatar, please try uploading it again', 'jet-event-system' ) );
			else
				bp_core_add_message( __( 'The new event avatar was uploaded successfully!', 'jet-event-system' ) );

		}

		do_action( 'events_screen_event_admin_avatar', $bp->jes_events->current_event->id );

		bp_core_load_template( apply_filters( 'events_template_event_admin_avatar', 'events/single/home' ) );
	}
}
add_action( 'wp', 'events_screen_event_admin_avatar', 4 );

function events_screen_event_admin_manage_members() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && 'manage-members' == $bp->action_variables[0] ) {

		if ( !$bp->is_item_admin )
			return false;

		if ( 'promote' == $bp->action_variables[1] && ( 'mod' == $bp->action_variables[2] || 'admin' == $bp->action_variables[2] ) && is_numeric( $bp->action_variables[3] ) ) {
			$user_id = $bp->action_variables[3];
			$status = $bp->action_variables[2];

			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_promote_member' ) )
				return false;

			// Promote a user.
			if ( !events_promote_member( $user_id, $bp->jes_events->current_event->id, $status ) ) {
				bp_core_add_message( __( 'There was an error when promoting that user, please try again', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'User promoted successfully', 'jet-event-system' ) );
			}

			do_action( 'events_promoted_member', $user_id, $bp->jes_events->current_event->id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/manage-members/' );
		}

		if ( 'demote' == $bp->action_variables[1] && is_numeric( $bp->action_variables[2] ) ) {
			$user_id = $bp->action_variables[2];

			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_demote_member' ) )
				return false;

			// Demote a user.
			if ( !events_demote_member( $user_id, $bp->jes_events->current_event->id ) ) {
				bp_core_add_message( __( 'There was an error when demoting that user, please try again', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'User demoted successfully', 'jet-event-system' ) );
			}

			do_action( 'events_demoted_member', $user_id, $bp->jes_events->current_event->id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/manage-members/' );
		}

		if ( 'ban' == $bp->action_variables[1] && is_numeric( $bp->action_variables[2] ) ) {
			$user_id = $bp->action_variables[2];

			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_ban_member' ) )
				return false;

			// Ban a user.
			if ( !events_ban_member( $user_id, $bp->jes_events->current_event->id ) ) {
				bp_core_add_message( __( 'There was an error when banning that user, please try again', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'User banned successfully', 'jet-event-system' ) );
			}

			do_action( 'events_banned_member', $user_id, $bp->jes_events->current_event->id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/manage-members/' );
		}

		if ( 'unban' == $bp->action_variables[1] && is_numeric( $bp->action_variables[2] ) ) {
			$user_id = $bp->action_variables[2];

			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_unban_member' ) )
				return false;

			// Remove a ban for user.
			if ( !events_unban_member( $user_id, $bp->jes_events->current_event->id ) ) {
				bp_core_add_message( __( 'There was an error when unbanning that user, please try again', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'User ban removed successfully', 'jet-event-system' ) );
			}

			do_action( 'events_unbanned_member', $user_id, $bp->jes_events->current_event->id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/manage-members/' );
		}

		do_action( 'events_screen_event_admin_manage_members', $bp->jes_events->current_event->id );

		bp_core_load_template( apply_filters( 'events_template_event_admin_manage_members', 'events/single/home' ) );
	}
}
add_action( 'wp', 'events_screen_event_admin_manage_members', 4 );

function events_screen_event_admin_requests() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && 'membership-requests' == $bp->action_variables[0] ) {

		/* Ask for a login if the user is coming here via an email notification */
		if ( !is_user_logged_in() )
			bp_core_redirect( site_url( 'wp-login.php?redirect_to=' . $bp->root_domain . '/' . $bp->current_component . '/' . $bp->current_item . '/admin/membership-requests/' ) );

		if ( !$bp->is_item_admin || 'public' == $bp->jes_events->current_event->status )
			return false;

		// Remove any screen notifications
		bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->jes_events->id, 'new_membership_request' );

		$request_action = $bp->action_variables[1];
		$membership_id = $bp->action_variables[2];

		if ( isset($request_action) && isset($membership_id) ) {
			if ( 'accept' == $request_action && is_numeric($membership_id) ) {

				/* Check the nonce first. */
				if ( !check_admin_referer( 'events_accept_membership_request' ) )
					return false;

				// Accept the membership request
				if ( !events_accept_membership_request( $membership_id ) ) {
					bp_core_add_message( __( 'There was an error accepting the membership request, please try again.', 'jet-event-system' ), 'error' );
				} else {
					bp_core_add_message( __( 'Event membership request accepted', 'jet-event-system' ) );
				}

			} else if ( 'reject' == $request_action && is_numeric($membership_id) ) {
				/* Check the nonce first. */
				if ( !check_admin_referer( 'events_reject_membership_request' ) )
					return false;

				// Reject the membership request
				if ( !events_reject_membership_request( $membership_id ) ) {
					bp_core_add_message( __( 'There was an error rejecting the membership request, please try again.', 'jet-event-system' ), 'error' );
				} else {
					bp_core_add_message( __( 'Event membership request rejected', 'jet-event-system' ) );
				}

			}

			do_action( 'events_event_request_managed', $bp->jes_events->current_event->id, $request_action, $membership_id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/membership-requests/' );
		}

		do_action( 'events_screen_event_admin_requests', $bp->jes_events->current_event->id );

		bp_core_load_template( apply_filters( 'events_template_event_admin_requests', 'events/single/home' ) );
	}
}
add_action( 'wp', 'events_screen_event_admin_requests', 4 );

function events_screen_event_admin_delete_event() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && 'delete-event' == $bp->action_variables[0] ) {

		if ( !$bp->is_item_admin && !is_site_admin() )
			return false;

		if ( isset( $_REQUEST['delete-event-button'] ) && isset( $_REQUEST['delete-event-understand'] ) ) {
			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_delete_event' ) )
				return false;

			// Event admin has deleted the event, now do it.
			if ( !events_delete_event( $bp->jes_events->current_event->id ) ) {
				bp_core_add_message( __( 'There was an error deleting the event, please try again.', 'jet-event-system' ), 'error' );
			} else {
				bp_core_add_message( __( 'The event was deleted successfully', 'jet-event-system' ) );

				do_action( 'events_event_deleted', $bp->jes_events->current_event->id );

				bp_core_redirect( $bp->loggedin_user->domain . $bp->jes_events->slug . '/' );
			}

			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component );
		}

		do_action( 'events_screen_event_admin_delete_event', $bp->jes_events->current_event->id );

		bp_core_load_template( apply_filters( 'events_template_event_admin_delete_event', 'events/single/home' ) );
	}
}
add_action( 'wp', 'events_screen_event_admin_delete_event', 4 );

function events_screen_notification_settings() {
	global $current_user; ?>
	<table class="notification-settings zebra" id="events-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _e( 'Events', 'jet-event-system' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'jet-event-system' ) ?></th>
				<th class="no"><?php _e( 'No', 'jet-event-system' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td></td>
				<td><?php _e( 'A member invites you to join a event', 'jet-event-system' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_invite]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_events_invite') || 'yes' == get_usermeta( $current_user->id, 'notification_events_invite') ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_events_invite]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_events_invite') ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'Event information is updated', 'jet-event-system' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_event_updated]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_events_event_updated') || 'yes' == get_usermeta( $current_user->id, 'notification_events_event_updated') ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_events_event_updated]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_events_event_updated') ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			
			<tr>
				<td></td>
				<td><?php _e( 'You are promoted to a event administrator or moderator', 'jet-event-system' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_admin_promotion]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_events_admin_promotion') || 'yes' == get_usermeta( $current_user->id, 'notification_events_admin_promotion') ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_events_admin_promotion]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_events_admin_promotion') ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'A member requests to join a private event for which you are an admin', 'jet-event-system' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_membership_request]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_events_membership_request') || 'yes' == get_usermeta( $current_user->id, 'notification_events_membership_request') ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_events_membership_request]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_events_membership_request') ) { ?>checked="checked" <?php } ?>/></td>
			</tr>

			<tr>
				<td></td>
				<td><?php _e( 'You come reminder of imminent events', 'jet-event-system' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_reminder]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_events_reminder') || 'yes' == get_usermeta( $current_user->id, 'notification_events_reminder') ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_events_reminder]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_events_reminder') ) { ?>checked="checked" <?php } ?>/></td>
			</tr>			
			
			<?php do_action( 'events_screen_notification_settings' ); ?>
		</tbody>
	</table>
<?php
}
add_action( 'bp_notification_settings', 'events_screen_notification_settings' );
?>