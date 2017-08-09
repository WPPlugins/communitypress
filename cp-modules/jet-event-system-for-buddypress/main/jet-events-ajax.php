<?php
/* AJAX invite a friend to a event functionality */
function bp_dtheme_ajax_invite_user_to_event() {
	global $bp;

	check_ajax_referer( 'events_invite_uninvite_user' );

	if ( !$_POST['friend_id'] || !$_POST['friend_action'] || !$_POST['event_id'] )
		return false;

	if ( !events_is_user_admin( $bp->loggedin_user->id, $_POST['event_id'] ) )
		return false;

	if ( !friends_check_friendship( $bp->loggedin_user->id, $_POST['friend_id'] ) )
		return false;

	if ( 'invite' == $_POST['friend_action'] ) {

		if ( !events_invite_user( array( 'user_id' => $_POST['friend_id'], 'event_id' => $_POST['event_id'] ) ) )
			return false;

		$user = new BP_Core_User( $_POST['friend_id'] );

		echo '<li id="uid-' . $user->id . '">';
		echo $user->avatar_thumb;
		echo '<h4>' . $user->user_link . '</h4>';
		echo '<span class="activity">' . esc_attr( $user->last_active ) . '</span>';
		echo '<div class="action" id="jes-remove-action">
				<a class="remove" href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->events->slug . '/' . $_POST['event_id'] . '/invites/remove/' . $user->id, 'events_invite_uninvite_user' ) . '" id="uid-' . attribute_escape( $user->id ) . '">' . __( 'Remove Invite', 'buddypress' ) . '</a>
			  </div>';
		echo '</li>';

	} else if ( 'uninvite' == $_POST['friend_action'] ) {

		if ( !events_uninvite_user( $_POST['friend_id'], $_POST['event_id'] ) )
			return false;

		return true;

	} else {
		return false;
	}
}
add_action( 'wp_ajax_events_invite_user', 'bp_dtheme_ajax_invite_user_to_event' );

function jet_events_add_ajaxjs() {
global $bp;
	if ( $bp->current_component == $bp->jes_events->slug )
		{
			wp_enqueue_script( 'events_widget_events_invite-js', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/ajax.js', array('jquery') );
	}
}
add_action( 'template_redirect', 'jet_events_add_ajaxjs', 1 );
?>