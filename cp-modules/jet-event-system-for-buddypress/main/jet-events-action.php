<?php
/********************************************************************************
 * Action Functions
 *
 * Action functions are exactly the same as screen functions, however they do not
 * have a template screen associated with them. Usually they will send the user
 * back to the default screen after execution.
 */

function events_action_create_event() {
	global $bp;

	/* If we're not at domain.org/events/create/ then return false */
	if ( $bp->current_component != $bp->jes_events->slug || 'create' != $bp->current_action )
		return false;

	if ( !is_user_logged_in() )
		return false;

	/* Make sure creation steps are in the right order */
	events_action_sort_creation_steps();

	/* If no current step is set, reset everything so we can start a fresh event creation */
	if ( !$bp->jes_events->current_create_step = $bp->action_variables[1] ) {

		unset( $bp->jes_events->current_create_step );
		unset( $bp->jes_events->completed_create_steps );

		setcookie( 'bp_new_event_id', false, time() - 1000, COOKIEPATH );
		setcookie( 'bp_completed_create_steps', false, time() - 1000, COOKIEPATH );

		$reset_steps = true;
		bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . array_shift( array_keys( $bp->jes_events->event_creation_steps ) ) . '/' );
	}

	/* If this is a creation step that is not recognized, just redirect them back to the first screen */
	if ( $bp->action_variables[1] && !$bp->jes_events->event_creation_steps[$bp->action_variables[1]] ) {
		bp_core_add_message( __('There was an error saving event details. Please try again.', 'jet-event-system'), 'error' );
		bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/' );
	}

	/* Fetch the currently completed steps variable */
	if ( isset( $_COOKIE['bp_completed_create_steps'] ) && !$reset_steps )
		$bp->jes_events->completed_create_steps = unserialize( stripslashes( $_COOKIE['bp_completed_create_steps'] ) );

	/* Set the ID of the new event, if it has already been created in a previous step */
	if ( isset( $_COOKIE['bp_new_event_id'] ) ) {
		$bp->jes_events->new_event_id = $_COOKIE['bp_new_event_id'];
		$bp->jes_events->current_event = new JES_Events_Event( $bp->jes_events->new_event_id );
	}

	/* If the save, upload or skip button is hit, lets calculate what we need to save */
	if ( isset( $_POST['save'] ) ) {

		/* Check the nonce */
		check_admin_referer( 'events_create_save_' . $bp->jes_events->current_create_step );

		if ( 'event-details' == $bp->jes_events->current_create_step ) {
			if ( empty( $_POST['event-edted'] ) ) { $eventedted = $_POST['event-edtsd']; } else { $eventedted = $_POST['event-edted']; }
			if ( empty( $_POST['event-name'] ) || empty( $_POST['event-desc'] ) || !strlen( trim( $_POST['event-name'] ) ) || !strlen( trim( $_POST['event-desc'] || $_POST['event-edtsd'] ) ) ) {
				bp_core_add_message( __( 'Please fill in all of the required fields', 'jet-event-system' ), 'error' );
				bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $bp->jes_events->current_create_step . '/' );
			}
// JLL_MOD - Modded all over - look care over entire file :(
if ( empty ($_POST['event-edtsd'] ) || empty ($_POST['event-edtsth'] ) ) {
					bp_core_add_message( __( 'Please complete event Start date and time.', 'jet-event-system' ), 'error' );
				bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $bp->jes_events->current_create_step . '/' );
						} else if ( jes_datetounix($_POST['event-edtsd'],$_POST['event-edtsth'],$_POST['event-edtstm']) > jes_datetounix($eventedted,$_POST['event-edteth'],$_POST['event-edtetm'])) {
					bp_core_add_message( __( 'There was an error updating event details. Date and time of completion of the event can not exceed the date of its beginning, please try again.', 'jet-event-system' ), 'error' );
				bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $bp->jes_events->current_create_step . '/' );
						} else {
						
			if ( !$bp->jes_events->new_event_id = events_create_event( array( 'event_id' => $bp->jes_events->new_event_id, 'name' => $_POST['event-name'], 'etype' => $_POST['event-etype'], 'eventapproved' => $_POST['event-eventapproved'], 'description' => $_POST['event-desc'], 'eventterms' => $_POST['event-eventterms'], 'placedcountry' => $_POST['event-placedcountry'], 'placedstate' => $_POST['event-placedstate'],'placedcity' => $_POST['event-placedcity'], 'placedaddress' => $_POST['event-placedaddress'], 'placednote' => $_POST['event-placednote'], 'placedgooglemap' => $_POST['event-placedgooglemap'], 'flyer' => $_POST['event-flyer'],'newspublic' => $_POST['event-newspublic'], 'newsprivate' => $_POST['event-newsprivate'], 'edtsd' => $_POST['event-edtsd'], 'edted' => $eventedted, 'edtsth' => $_POST['event-edtsth'], 'edteth' => $_POST['event-edteth'], 'edtstm' => $_POST['event-edtstm'], 'edtetm' => $_POST['event-edtetm'],'grouplink' => $_POST['grouplink'], 'forumlink' => $_POST['forumlink'], 'enablesocial' => $_POST['enablesocial'],'slug' => events_jes_check_slug( sanitize_title( esc_attr( $_POST['event-name'] ) ) ), 'date_created' => gmdate( "Y-m-d H:i:s" ), 'status' => 'public', 'notify_timed_enable' => $_POST['notifytimedenable']) ) ) {
				bp_core_add_message( __( 'There was an error saving event details, please try again [001]', 'jet-event-system' ), 'error' );
				bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $bp->jes_events->current_create_step . '/' );
			}
}
			events_update_eventmeta( $bp->jes_events->new_event_id, 'total_member_count', 1 );
			events_update_eventmeta( $bp->jes_events->new_event_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );
		}

		if ( 'event-settings' == $bp->jes_events->current_create_step ) {
			$event_status = 'public';
			$event_enable_forum = 1;

			if ( !isset($_POST['event-show-forum']) ) {
				$event_enable_forum = 0;
			} else {
			}

			if ( 'private' == $_POST['event-status'] )
				$event_status = 'private';
			else if ( 'hidden' == $_POST['event-status'] )
				$event_status = 'hidden';

			if ( !$bp->jes_events->new_event_id = events_create_event( array( 'event_id' => $bp->jes_events->new_event_id, 'status' => $event_status, 'grouplink' => $_POST['event-grouplink'], 'forumlink' => $_POST['event-forumlink'], 'enablesocial' => $_POST['enablesocial'],'enable_forum' => $event_enable_forum ) ) ) {
				bp_core_add_message( __( 'There was an error saving event details, please try again. [002]', 'jet-event-system' ), 'error' );
				bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $bp->jes_events->current_create_step . '/' );
			}
		}

		if ( 'event-invites' == $bp->jes_events->current_create_step ) {
			events_send_invite_jes( $bp->loggedin_user->id, $bp->jes_events->new_event_id );
		}

		do_action( 'events_create_event_step_save_' . $bp->jes_events->current_create_step );
		do_action( 'events_create_event_step_complete' ); // Mostly for clearing cache on a generic action name

		/**
		 * Once we have successfully saved the details for this step of the creation process
		 * we need to add the current step to the array of completed steps, then update the cookies
		 * holding the information
		 */
		if ( !in_array( $bp->jes_events->current_create_step, (array)$bp->jes_events->completed_create_steps ) )
			$bp->jes_events->completed_create_steps[] = $bp->jes_events->current_create_step;

		/* Reset cookie info */
		setcookie( 'bp_new_event_id', $bp->jes_events->new_event_id, time()+60*60*24, COOKIEPATH );
		setcookie( 'bp_completed_create_steps', serialize( $bp->jes_events->completed_create_steps ), time()+60*60*24, COOKIEPATH );

		/* If we have completed all steps and hit done on the final step we can redirect to the completed event */
		if ( count( $bp->jes_events->completed_create_steps ) == count( $bp->jes_events->event_creation_steps ) && $bp->jes_events->current_create_step == array_pop( array_keys( $bp->jes_events->event_creation_steps ) ) ) {
			unset( $bp->jes_events->current_create_step );
			unset( $bp->jes_events->completed_create_steps );

			/* Once we compelete all steps, record the event creation in the activity stream. */
			events_record_activity( array(
				'action' => apply_filters( 'events_activity_created_event_action', sprintf( __( '%s created the event %s', 'jet-event-system'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . jes_bp_get_event_permalink( $bp->jes_events->current_event ) . '">' . attribute_escape( $bp->jes_events->current_event->name ) . '</a>' ) ),
				'type' => 'created_event',
				'item_id' => $bp->jes_events->new_event_id
			) );

			do_action( 'events_event_create_complete', $bp->jes_events->new_event_id );

			bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) );
		} else {
			/**
			 * Since we don't know what the next step is going to be (any plugin can insert steps)
			 * we need to loop the step array and fetch the next step that way.
			 */
			foreach ( (array)$bp->jes_events->event_creation_steps as $key => $value ) {
				if ( $key == $bp->jes_events->current_create_step ) {
					$next = 1;
					continue;
				}

				if ( $next ) {
					$next_step = $key;
					break;
				}
			}

			bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $next_step . '/' );
		}
	}

	/* Event avatar is handled separately */
	if ( 'event-avatar' == $bp->jes_events->current_create_step && isset( $_POST['upload'] ) ) {
		if ( !empty( $_FILES ) && isset( $_POST['upload'] ) ) {
			/* Normally we would check a nonce here, but the event save nonce is used instead */

			/* Pass the file to the avatar upload handler */
			if ( bp_core_avatar_handle_upload( $_FILES, 'events_avatar_upload_dir' ) ) {
				$bp->avatar_admin->step = 'crop-image';

				/* Make sure we include the jQuery jCrop file for image cropping */
				add_action( 'wp', 'bp_core_add_jquery_cropper' );
			}
		}

		/* If the image cropping is done, crop the image and save a full/thumb version */
		if ( isset( $_POST['avatar-crop-submit'] ) && isset( $_POST['upload'] ) ) {
			/* Normally we would check a nonce here, but the event save nonce is used instead */

			if ( !bp_core_avatar_handle_crop( array( 'object' => 'event', 'avatar_dir' => 'event-avatars', 'item_id' => $bp->jes_events->current_event->id, 'original_file' => $_POST['image_src'], 'crop_x' => $_POST['x'], 'crop_y' => $_POST['y'], 'crop_w' => $_POST['w'], 'crop_h' => $_POST['h'] ) ) )
				bp_core_add_message( __( 'There was an error saving the event avatar, please try uploading again.', 'jet-event-system' ), 'error' );
			else
				bp_core_add_message( __( 'The event avatar was uploaded successfully!', 'jet-event-system' ) );
		}
	}

 	bp_core_load_template( apply_filters( 'events_template_create_event', 'events/create' ) );
}
add_action( 'wp', 'events_action_create_event', 3 );

function events_action_join_event() {
	global $bp;

	if ( !$bp->is_single_item || $bp->current_component != $bp->jes_events->slug || $bp->current_action != 'join' )
		return false;

	// Nonce check
	if ( !check_admin_referer( 'events_join_event' ) )
		return false;

	// Skip if banned or already a member
	if ( !events_is_user_member( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) && !events_is_user_banned( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) ) {

		// User wants to join a event that is not public
		if ( $bp->jes_events->current_event->status != 'public' ) {
			if ( !events_check_user_has_invite( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) ) {
				bp_core_add_message( __( 'There was an error joining the event.', 'jet-event-system' ), 'error' );
				bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) );
			}
		}

		// User wants to join any event
		if ( !events_join_event( $bp->jes_events->current_event->id ) )
			bp_core_add_message( __( 'There was an error joining the event.', 'jet-event-system' ), 'error' );
		else
			bp_core_add_message( __( 'You joined the event!', 'jet-event-system' ) );

		bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) );
	}

	bp_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}
add_action( 'wp', 'events_action_join_event', 3 );


function events_action_leave_event() {
	global $bp;

	if ( !$bp->is_single_item || $bp->current_component != $bp->jes_events->slug || $bp->current_action != 'leave-event' )
		return false;

	// Nonce check
	if ( !check_admin_referer( 'events_leave_event' ) )
		return false;

	// User wants to leave any event
	if ( events_is_user_member( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) ) {
		if ( !events_leave_event( $bp->jes_events->current_event->id ) ) {
			bp_core_add_message( __( 'There was an error leaving the event.', 'jet-event-system' ), 'error' );
		} else {
			bp_core_add_message( __( 'You successfully left the event.', 'jet-event-system' ) );
		}
		bp_core_redirect( jes_bp_get_event_permalink( $bp->jes_events->current_event ) );
	}

	bp_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}
add_action( 'wp', 'events_action_leave_event', 3 );


function events_action_sort_creation_steps() {
	global $bp;

	if ( $bp->current_component != JES_SLUG && $bp->current_action != 'create' )
		return false;

	if ( !is_array( $bp->jes_events->event_creation_steps ) )
		return false;

	foreach ( (array)$bp->jes_events->event_creation_steps as $slug => $step ) {
		while ( !empty( $temp[$step['position']] ) )
			$step['position']++;

		$temp[$step['position']] = array( 'name' => $step['name'], 'slug' => $slug );
	}

	/* Sort the steps by their position key */
	ksort($temp);
	unset($bp->jes_events->event_creation_steps);

	foreach( (array)$temp as $position => $step )
		$bp->jes_events->event_creation_steps[$step['slug']] = array( 'name' => $step['name'], 'position' => $position );
}

function events_action_redirect_to_random_event() {
	global $bp, $wpdb;

	if ( $bp->current_component == $bp->jes_events->slug && isset( $_GET['random-event'] ) ) {
		$event = events_get_events( array( 'type' => 'random', 'per_page' => 1 ) );

		bp_core_redirect( $bp->root_domain . '/' . $bp->jes_events->slug . '/' . $event['events'][0]->slug . '/' );
	}
}
add_action( 'wp', 'events_action_redirect_to_random_event', 6 );

function events_action_event_feed() {
	global $bp, $wp_query;

	if ( !bp_is_active( 'activity' ) || $bp->current_component != $bp->jes_events->slug || !$bp->jes_events->current_event || $bp->current_action != 'feed' )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	if ( 'public' != $bp->jes_events->current_event->status ) {
		if ( !events_is_user_member( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) )
			return false;
	}

	include_once( 'bp-activity/feeds/bp-activity-event-feed.php' );
	die;
}
add_action( 'wp', 'events_action_event_feed', 3 );
?>