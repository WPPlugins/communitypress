<?php

function photo_tagged_notification( $photo_id, $photo_owner_id, $tagged_id ) {
	global $bp;

	$photo_owner_name = bp_core_get_user_displayname( $photo_owner_id );

	$ud = get_userdata( $tagged_id );
	$photo_owner_ud = get_userdata( $photo_owner_id );

	$photo_tag_link = bp_core_get_user_domain( $photo_owner_id ) . BP_ALBUM_SLUG . '/picture/' . $photo_id . '/';
	$settings_link = bp_core_get_user_domain( $tagged_id ) .  BP_SETTINGS_SLUG . '/notifications';

	$photo_owner_link = bp_core_get_user_domain( $photo_owner_id );

	// Set up and send the message
	$to       = $ud->user_email;
	$sitename = wp_specialchars_decode( get_blog_option( BP_ROOT_BLOG, 'blogname' ), ENT_QUOTES );
	$subject  = '[' . $sitename . '] ' . sprintf( __( 'You were tagged in %s\'s photo', 'buddypress' ), $photo_owner_name );

	$message = sprintf( __(
"Someone tagged you in %s\'s photo.

To view the photo you're tagged in: %s

To view %s's profile: %s

---------------------
", 'buddypress' ), $photo_owner_name, $photo_tag_link, $photo_owner_name, $photo_owner_link );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'buddypress' ), $settings_link );

	/* Send the message */
	$to = apply_filters( 'friends_notification_new_request_to', $to );
	$subject = apply_filters( 'friends_notification_new_request_subject', $subject, $photo_owner_name );
	$message = apply_filters( 'friends_notification_new_request_message', $message, $photo_owner_name, $photo_owner_link, $photo_tag_link );

	wp_mail( $to, $subject, $message );
}
?>