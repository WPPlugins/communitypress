<?php

/* Define the slug for the component */
if ( !defined( 'CP_WALL_SLUG' ) )
	define ( 'CP_WALL_SLUG', 'wall' );

/**
 * wall_setup_globals()
 *
 * Add the wall globals to the $bp global for use across the installation
 *
 * @package BuddyPress wall
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @global $wpdb WordPress DB access object.
 * @uses site_url() Returns the site URL
 */
function wall_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->wall->id = 'wall';
	$bp->wall->slug = CP_WALL_SLUG;
	//$bp->wall->format_notification_function = 'wall_format_notifications';

	/* Register this in the active components array */
	$bp->active_components[$bp->wall->slug] = $bp->wall->id;

	/* Set the support field type ids */
	$bp->wall->field_types = apply_filters( 'wall_field_types', array( 'textbox', 'textarea', 'radio', 'checkbox', 'selectbox', 'multiselectbox', 'datebox' ) );

	do_action( 'wall_setup_globals' );
}
add_action( 'bp_setup_globals', 'wall_setup_globals' );


/**
 * wall_setup_nav()
 *
 * Sets up the navigation items for the wall component
 *
 * @package BuddyPress wall
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_core_new_nav_item() Adds a navigation item to the top level buddypress navigation
 * @uses bp_core_new_subnav_item() Adds a sub navigation item to a nav item
 * @uses bp_is_my_wall() Returns true if the current user being viewed is equal the logged in user
 * @uses bp_core_fetch_avatar() Returns the either the thumb or full avatar URL for the user_id passed
 */
function wall_setup_nav() {
	global $bp;

// JLL_MOD - fixed wall Navigation, added 'Wall', and repositioned wall
	/* Add 'wall' to the main navigation */
	bp_core_new_nav_item( array( 'name' => __( 'Wall', 'buddypress' ), 'slug' => $bp->wall->slug, 'position' => 19, 'screen_function' => 'wall_screen_display_wall', 'default_subnav_slug' => 'wall', 'item_css_id' => $bp->wall->id, 'show_for_loggedin_user' => false ) );

	$wall_link = $bp->loggedin_user->domain . $bp->wall->slug . '/';

	/* Add the subnav items to the wall */
	//bp_core_new_subnav_item( array( 'name' => __( 'Wall', 'buddypress' ), 'slug' => 'wall', 'parent_url' => $wall_link, 'parent_slug' => $bp->wall->slug, 'screen_function' => 'wall_screen_display_wall', 'position' => 5 ) );




	if ( $bp->current_component == $bp->wall->slug ) {
		if ( $bp->displayed_user->id == $bp->loggedin_user->id ) {
			$bp->bp_options_title = __( 'My Wall', 'buddypress' );
		} else {
			$bp->bp_options_avatar = bp_core_fetch_avatar( array( 'item_id' => $bp->displayed_user->id, 'type' => 'thumb' ) );
			$bp->bp_options_title = $bp->displayed_user->fullname;
		}
	}

	do_action( 'wall_setup_nav' );
}
add_action( 'bp_setup_nav', 'wall_setup_nav' );


/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */
 
 
// JLL_MOD - screen func for custom nav
function wall_screen_display_wall() {
	global $bp;

	do_action( 'wall_screen_display_wall', $_GET['new'] );
	bp_core_load_template( apply_filters( 'display_wall', 'members/single/home' ) );
}



/********************************************************************************
 * Action Functions
 *
 * Action functions are exactly the same as screen functions, however they do not
 * have a template screen associated with them. Usually they will send the user
 * back to the default screen after execution.
 */


?>
