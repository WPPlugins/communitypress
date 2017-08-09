<?php
/* Menu and Navi */
function add_events_to_main_menu() {

	$class = (bp_is_page(JES_SLUG)) ? ' class="selected" ' : '';

	echo  '<li ' . $class. '><a href="' . get_option('home') . '/'.JES_SLUG.'" title="' . __( 'Events', 'jet-event-system' ) .'">' .  __( 'Events', 'jet-event-system' ) .'</a></li>';

}

add_action('bp_nav_items','add_events_to_main_menu');


function events_setup_nav() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && $event_id = JES_Events_Event::jes_event_exists($bp->current_action) ) {

		/* This is a single event page. */
		$bp->is_single_item = true;
		$bp->jes_events->current_event = new JES_Events_Event( $event_id );

		/* Using "item" not "event" for generic support in other components. */
		if ( is_site_admin() )
			$bp->is_item_admin = 1;
		else
			$bp->is_item_admin = events_is_user_admin( $bp->loggedin_user->id, $bp->jes_events->current_event->id );

		/* If the user is not an admin, check if they are a moderator */
		if ( !$bp->is_item_admin )
			$bp->is_item_mod = events_is_user_mod( $bp->loggedin_user->id, $bp->jes_events->current_event->id );

		/* Is the logged in user a member of the event? */
		$bp->jes_events->current_event->is_user_member = ( is_user_logged_in() && events_is_user_member( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) ) ? true : false;

		/* Should this event be visible to the logged in user? */
		$bp->jes_events->current_event->is_event_visible_to_member = ( 'public' == $bp->jes_events->current_event->status || $is_member ) ? true : false;
	}

	/* Add 'Events' to the main navigation - Profile */
	bp_core_new_nav_item( array( 'name' => sprintf( __( 'Events <span>(%d)</span>', 'jet-event-system' ), events_total_events_for_user() ), 'slug' => $bp->jes_events->slug, 'position' => 70, 'screen_function' => 'events_screen_my_events', 'default_subnav_slug' => 'my-events', 'item_css_id' => $bp->jes_events->id ) );

	$events_link = $bp->loggedin_user->domain . $bp->jes_events->slug . '/';

// JLL_MOD - Fixed Custom Nav and added 'Create an Event'
	/* Add the subnav items to the events nav item */
	bp_core_new_subnav_item( array( 'name' => __( 'My Events', 'jet-event-system' ), 'slug' => 'my-events', 'parent_url' => $events_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_my_events', 'position' => 10, 'item_css_id' => 'events-my-events' ) );

	/* Add the subnav items to the events nav item */
	
	bp_core_new_subnav_item( array( 'name' => __( 'Requests', 'jet-event-system' ), 'slug' => 'requests', 'parent_url' => $events_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_jes_event_invite_jes', 'position' => 30, 'user_has_access' => bp_is_my_profile() ) );
	
	bp_core_new_subnav_item( array( 'name' => __( 'Create an Event', 'jet-event-system' ), 'slug' => 'create', 'parent_url' => $events_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_jes_create_event', 'position' => 40, 'user_has_access' => bp_is_home() ) );
	
	if ( $bp->current_component == $bp->jes_events->slug ) {

		if ( bp_is_my_profile() && !$bp->is_single_item ) {

			$bp->bp_options_title = __( 'My Events', 'jet-event-system' );

		} else if ( !bp_is_my_profile() && !$bp->is_single_item ) {

			$bp->bp_options_avatar = bp_core_fetch_avatar( array( 'item_id' => $bp->displayed_user->id, 'type' => 'thumb' ) );
			$bp->bp_options_title = $bp->displayed_user->fullname;

		} else if ( $bp->is_single_item ) {
			// We are viewing a single event, so set up the
			// event navigation menu using the $bp->jes_events->current_event global.

			/* When in a single event, the first action is bumped down one because of the
			   event name, so we need to adjust this and set the event name to current_item. */
			$bp->current_item = $bp->current_action;
			$bp->current_action = $bp->action_variables[0];
			array_shift($bp->action_variables);

			$bp->bp_options_title = $bp->jes_events->current_event->name;

			if ( !$bp->bp_options_avatar = bp_core_fetch_avatar( array( 'item_id' => $bp->jes_events->current_event->id, 'object' => 'event', 'type' => 'thumb', 'avatar_dir' => 'event-avatars', 'alt' => __( 'Event Avatar', 'jet-event-system' ) ) ) )
				$bp->bp_options_avatar = '<img src="' . attribute_escape( $event->avatar_full ) . '" class="avatar" alt="' . attribute_escape( $event->name ) . '" />';

			$event_link = $bp->root_domain . '/' . $bp->jes_events->slug . '/' . $bp->jes_events->current_event->slug . '/';

			// If this is a private or hidden event, does the user have access?
			if ( 'private' == $bp->jes_events->current_event->status || 'hidden' == $bp->jes_events->current_event->status ) {
				if ( $bp->jes_events->current_event->is_user_member && is_user_logged_in() || is_site_admin() )
					$bp->jes_events->current_event->user_has_access = true;
				else
					$bp->jes_events->current_event->user_has_access = false;
			} else {
				$bp->jes_events->current_event->user_has_access = true;
			}

			/* Reset the existing subnav items */
			bp_core_reset_subnav_items($bp->jes_events->slug);

			/* Add a new default subnav item for when the events nav is selected. */
			bp_core_new_nav_default( array( 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_event_home', 'subnav_slug' => 'home' ) );

			/* Add the "Home" subnav item, as this will always be present */
			bp_core_new_subnav_item( array( 'name' => __( 'Home', 'jet-event-system' ), 'slug' => 'home', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_event_home', 'position' => 10, 'item_css_id' => 'home' ) );

$navi = get_site_option('jes_events' );
if ($navi['jes_events_googlemapopt_enable'])
		{
			/* Add Google map navi */
			bp_core_new_subnav_item( array( 'name' => __( 'Google Map', 'jet-event-system' ) , 'slug' => 'google-map', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_jes_event_google_map', 'position' => 20, 'item_css_id' => 'google_map' ) );
		}

if ($navi['jes_events_flyeropt_enable'])
		{
		/* Add Flyer navi */
			bp_core_new_subnav_item( array( 'name' => __( 'Flyer', 'jet-event-system' ) , 'slug' => 'flyer', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_jes_event_flyer', 'position' => 25, 'item_css_id' => 'flyer' ) );			
		}

			/* If the user is a event mod or more, then show the event admin nav item */
			if ( $bp->is_item_mod || $bp->is_item_admin )
				bp_core_new_subnav_item( array( 'name' => __( 'Admin', 'jet-event-system' ), 'slug' => 'admin', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_event_admin', 'position' => 80, 'user_has_access' => ( $bp->is_item_admin + (int)$bp->is_item_mod ), 'item_css_id' => 'admin' ) );

			// If this is a private event, and the user is not a member, show a "Request Membership" nav item.
			if ( !is_site_admin() && is_user_logged_in() && !$bp->jes_events->current_event->is_user_member && !events_jes_check_for_membership_request( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) && $bp->jes_events->current_event->status == 'private' )
				bp_core_new_subnav_item( array( 'name' => __( 'Request join to event', 'jet-event-system' ), 'slug' => 'request-join-to-event', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_event_request_membership', 'position' => 40, 'item_css_id' => 'request-join-to-event'  ) );

			bp_core_new_subnav_item( array( 'name' => sprintf( __( 'Members (%s)', 'jet-event-system' ), number_format( $bp->jes_events->current_event->total_member_count ) ), 'slug' => 'members', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_event_members', 'position' => 30, 'user_has_access' => $bp->jes_events->current_event->user_has_access, 'item_css_id' => 'members'  ) );
			
			if ( is_user_logged_in() && events_is_user_member( $bp->loggedin_user->id, $bp->jes_events->current_event->id ) ) {
				if ( function_exists('friends_install') )
					bp_core_new_subnav_item( array( 'name' => __( 'Send Invites', 'jet-event-system' ), 'slug' => 'send-invites', 'parent_url' => $event_link, 'parent_slug' => $bp->jes_events->slug, 'screen_function' => 'events_screen_jes_event_invite', 'item_css_id' => 'invite', 'position' => 70, 'user_has_access' => $bp->is_item_admin ) );
			}
/* Forum */
/*			if ( $bp->events->current_event->enable_forum && function_exists('bp_forums_setup') )
				bp_core_new_subnav_item( array( 'name' => __( 'Forum', 'jet-event-system' ), 'slug' => 'forum', 'parent_url' => $event_link, 'parent_slug' => $bp->events->slug, 'screen_function' => 'events_screen_event_forum', 'position' => 40, 'user_has_access' => $bp->events->current_event->user_has_access, 'item_css_id' => 'forums' ) );
*/
		}
	}

	do_action( 'events_setup_nav', $bp->jes_events->current_event->user_has_access );
}
add_action( 'bp_setup_nav', 'events_setup_nav' );


function events_setup_adminbar_menu() {
	global $bp;

	if ( !$bp->jes_events->current_event )
		return false;

	/* Don't show this menu to non site admins or if you're viewing your own profile */
	if ( !is_site_admin() )
		return false;
	?>
	<li id="bp-adminbar-adminoptions-menu">
		<a href=""><?php _e( 'Admin Options', 'jet-event-system' ) ?></a>

		<ul>
			<li><a class="confirm" href="<?php echo wp_nonce_url( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/delete-event/', 'events_delete_event' ) ?>&amp;delete-event-button=1&amp;delete-event-understand=1"><?php _e( "Delete Event", 'jet-event-system' ) ?></a></li>

			<?php do_action( 'events_adminbar_menu_items' ) ?>
		</ul>
	</li>
	<?php
}
// JLL_MOD - removed and modded in adminbar
//add_action( 'bp_adminbar_menus', 'events_setup_adminbar_menu', 20 );

?>