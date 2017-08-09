<?php
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-menu.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-classes.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-templatetags.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-functions.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-db.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-operations.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-ajax.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-widgets.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-filters.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-notifications.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-invites.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-module_eu.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-module_sc.php' );
require ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-admin.php' );



function jet_events_add_js() {
  global $bp;
	$jsload = 0;
	$jes_adata = get_site_option('jes_events' );


	if ( $bp->current_component == $bp->jes_events->slug )
		{
			// if ($jes_adata['jes_events_style'] == 'Calendar')
			//	{
			//		wp_enqueue_script( 'jquery-jes-calendar', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/fullcalendar.min.js' );
			//}
			if ($bp->current_action == 'create')
				{
					if (!jes_is_event_creation_step( 'event-avatar' ) )
						{
							$jsload = 1;
						}
				}
			if ($bp->current_action == 'admin')
				{
					if ( !'crop-image' == bp_get_avatar_admin_step() )
						{
							$jsload = 1;
						}
				}
		if ($jsload)
			{
		// bp ajax chat fix
		/* In /wp-content/plugins/buddypress-ajax-chat/bp-chat/bp-chat-cssjs.php
			change
				add_action( 'template_redirect', 'bp_chat_add_js', 1);</em>
			to 
				add_action( 'template_redirect', 'bp_chat_add_js', <strong>10</strong>);
		*/
				remove_action( 'template_redirect', 'bp_chat_add_js', 1 );				

				wp_enqueue_script( 'jquery-jes-151', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery-1.5.2.min.js', array('jquery') );				
				wp_enqueue_script( 'jquery-jes-uicore', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery.ui.core.js', array('jquery') );
				wp_enqueue_script( 'jquery-jes-uiwidget', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery.ui.widget.js', array('jquery') );
				wp_enqueue_script( 'jquery-jes-uidpcore', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery.datapicker.js', array('jquery') );
				wp_enqueue_script( 'jquery-jes-uidp', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jes.datepicker.js', array('jquery') );

				$locale = apply_filters( 'wordpress_locale', get_locale() );
				if (!file_exists( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery-iu-locale/jquery.ui.datepicker-'.$locale.'.js')) {
				wp_enqueue_script( 'jet-event-js-uilocale', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery-iu-locale/jquery.ui.datepicker-en_GB.js', array('jquery') );
				} else
				{
				wp_enqueue_script( 'jet-event-js-uilocale', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery-iu-locale/jquery.ui.datepicker-'.$locale.'.js', array('jquery') );
				}
			}
		}	
}
		
add_action( 'template_redirect', 'jet_events_add_js', 1 );	

function jes_admin_js() {
	wp_enqueue_script( 'jquery-jes-admin', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/jquery.tabs.js' );
	wp_enqueue_script( 'jquery-jes-admin-tabs', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/admintabs.js' );
}

add_action('admin_init','jes_admin_js');
	
function jes_events_add_css() {
  global $bp;
	$jsload = 0;
	$jes_adata = get_site_option('jes_events' );
	
	if ( $bp->current_component == $bp->jes_events->slug )
		{
			if ($jes_adata['jes_events_style'] == 'Calendar')
				{
					wp_enqueue_style( 'jes-cal-css', apply_filters( 'jes_events_fc_css', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/css/fullcalendar.css' ) );
			}
		if ($bp->current_action == 'create')
			if (!jes_is_event_creation_step( 'event-avatar' ) ) {
				$jsload = 1;
			}
		if ($bp->current_action == 'admin')
			if ( !'crop-image' == bp_get_avatar_admin_step() ) {
				$jsload = 1;
			}
		}
	if ($jsload)
		{
			wp_enqueue_style( 'jes-datepicker-css', apply_filters( 'jes_events_add_css', get_stylesheet_directory_uri() . '/events/css/datepicker.css' ) );
		}
}
add_action( 'init', 'jes_events_add_css' );

function jes_css_style()
	{
global $bp;
if ( $bp->current_component == $bp->jes_events->slug )
	{
		wp_enqueue_style("jes-css",WP_PLUGIN_URL."/communitypress/cp-modules/jet-event-system-for-buddypress/css/eventstyle.css");
	}
}
add_action("wp_print_styles", "jes_css_style");

function jes_events_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->jes_events->id = 'events';

	$bp->jes_events->table_name = $wpdb->base_prefix . 'jet_events';
	$bp->jes_events->table_name_activity = $wpdb->base_prefix . 'jet_events_activity';	
	$bp->jes_events->jes_table_name_members = $wpdb->base_prefix . 'jet_events_members';
	$bp->jes_events->table_name_eventmeta = $wpdb->base_prefix . 'jet_events_eventmeta';
	$bp->jes_events->format_notification_function = 'events_format_notifications';
	$bp->jes_events->slug = JES_SLUG;
	/* Register this in the active components array */
	$bp->active_components[$bp->jes_events->slug] = $bp->jes_events->id;

	$bp->jes_events->forbidden_names = apply_filters( 'events_forbidden_names', array( 'my-events', 'create', 'invites', 'send-invites', 'forum', 'delete', 'add', 'admin', 'request-join-to-event', 'members', 'settings', 'avatar', JES_SLUG ) );

	$bp->jes_events->event_creation_steps = apply_filters( 'events_create_event_steps', array(
		'event-details' => array( 'name' => __( 'Details', 'jet-event-system' ), 'position' => 0 ),
		'event-settings' => array( 'name' => __( 'Settings', 'jet-event-system' ), 'position' => 10 ),
		'event-avatar' => array( 'name' => __( 'Avatar', 'jet-event-system' ), 'position' => 20 ),
	) );

	$bp->jes_events->valid_status = apply_filters( 'events_valid_status', array( 'public', 'private', 'hidden' ) );

	do_action( 'jes_events_setup_globals' );
}
add_action( 'bp_setup_globals', 'jes_events_setup_globals' );

/* Ajax Tabs */
add_action( 'wp_ajax_events_filter', 'bp_dtheme_object_template_loader' );


function events_setup_root_component() {
	/* Register 'events' as a root component */
	bp_core_add_root_component( JES_SLUG );
}
add_action( 'bp_setup_root_components', 'events_setup_root_component' );


function events_directory_events_setup() {
	global $bp;

	if ( $bp->current_component == $bp->jes_events->slug && empty( $bp->current_action ) && empty( $bp->current_item ) ) {
		$bp->is_directory = true;

		do_action( 'events_directory_events_setup' );
		bp_core_load_template( apply_filters( 'events_template_directory_events', 'events/index' ) );
	}
}
add_action( 'wp', 'events_directory_events_setup', 2 );


/********************************************************************************
 * SEO Functions
 * Title, Description, Meta: Keywords 
 */
function jes_seo_title() {
	global $bp;
	if ($bp->current_component == $bp->jes_events->slug)
		{
			return;
		}
}
add_action('wp_head','jes_seo_title');

function jes_seo_keyword() {
	
}
	
function jes_seo_description() {

}

/* Screen Functions */
include( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-screen.php' );

/* Action Functions */
include( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-action.php' );

/* Activity Functions */
include( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-activity.php' );

/********************************************************************************
 * Business Functions
 *
 * Business functions are where all the magic happens in BuddyPress. They will
 * handle the actual saving or manipulation of information. Usually they will
 * hand off to a database class for data access, then return
 * true or false on success or failure.
 */

function events_get_event( $args = '' ) {
	$defaults = array(
		'event_id' => false,
		'load_users' => false
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	return apply_filters( 'events_get_event', new JES_Events_Event( $event_id, true, $load_users ) );
}

function jes_event_groups_dropdown( $grp_id ) {
	global $bp;
?>
<?php // JLL_MOD - removed input label tag ?>
		<select id="event-grouplink" name="event-grouplink">
	  	<option value="0" <?php if ( $grp_id == bp_get_group_id() ) { echo "selected"; } ?>></option>
<?php
if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) : while ( bp_groups() ) : bp_the_group();		
		?>
	   		<option value="<?php bp_group_id() ?>" <?php if ( $grp_id == bp_get_group_id() ) { echo "selected"; } ?>><?php bp_group_name()?></option>
<?php		endwhile; endif; ?>
	  </select>
<?php
}

function events_is_valid_status( $status ) {
	global $bp;

	return in_array( $status, (array)$bp->jes_events->valid_status );
}

function events_jes_check_slug( $slug ) {
	global $bp;

	if ( 'wp' == substr( $slug, 0, 2 ) )
		$slug = substr( $slug, 2, strlen( $slug ) - 2 );

	if ( in_array( $slug, (array)$bp->jes_events->forbidden_names ) ) {
		$slug = $slug . '-' . rand();
	}

	if ( JES_Events_Event::jes_check_slug( $slug ) ) {
		do {
			$slug = $slug . '-' . rand();
		}
		while ( JES_Events_Event::jes_check_slug( $slug ) );
	}

	return $slug;
}

function events_jes_get_slug( $event_id ) {
	$event = new JES_Events_Event( $event_id );
	return $event->slug;
}

/*** General Event Functions ****************************************************/

function events_check_jes_event_exists( $event_id ) {
	return JES_Events_Event::jes_event_exists( $event_id );
}

function events_get_event_admins( $event_id ) {
	return JES_Events_Member::jes_get_event_administrator_ids( $event_id );
}

function events_get_event_mods( $event_id ) {
	return JES_Events_Member::get_event_moderator_ids( $event_id );
}

function events_get_event_members( $event_id, $limit = false, $page = false ) {
	return JES_Events_Member::jes_get_all_for_event( $event_id, $limit, $page );
}

function events_jes_get_total_member_count( $event_id ) {
	return JES_Events_Event::jes_get_total_member_count( $event_id );
}

/*** Event Fetching, Filtering & Searching  *************************************/

function events_get_events( $args = '' ) {
	global $bp;

	$sdata = get_site_option('jes_events' );
	$sortby = $sdata[ 'jes_events_sort_by' ];
	$sortby_ad = $sdata[ 'jes_events_sort_by_ad' ];
	
	$defaults = array(
		'type' => $sortby, // active, newest, alphabetical, random, popular, most-forum-topics or most-forum-posts
		'user_id' => false, // Pass a user_id to limit to only events that this user is a member of
		'search_terms' => false, // Limit to events that match these search terms

		'per_page' => 10, // The number of results to return per page
		'page' => 1, // The page to return if limiting per page
		'populate_extras' => true, // Fetch meta such as is_banned and is_member
	);

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );
	switch ( $type ) {
		case 'active': default:
			$events = JES_Events_Event::jes_get_active( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'newest':
			$events = JES_Events_Event::jes_get_newest( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'popular':
			$events = JES_Events_Event::jes_get_popular( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'alphabetical':
			$events = JES_Events_Event::jes_get_alphabetically( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'random':
			$events = JES_Events_Event::jes_get_random( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'most-forum-topics':
			$events = JES_Events_Event::jet_get_by_most_forum_topics( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'most-forum-posts':
			$events = JES_Events_Event::jet_get_by_most_forum_posts( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'soon':
			$events = JES_Events_Event::jes_get_soon( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
		case 'calendar':
			$events = JES_Events_Event::jes_get_calendar( $per_page, $page, $user_id, $search_terms, $populate_extras );
			break;
	}
	
	return apply_filters( 'events_get_events', $events, &$params );
}

function events_jes_get_jes_total_event_count() {
	if ( !$count = wp_cache_get( 'bp_jes_total_event_count', 'bp' ) ) {
		$count = JES_Events_Event::jes_get_jes_total_event_count();
		wp_cache_set( 'bp_jes_total_event_count', $count, 'bp' );
	}

	return $count;
}

function events_get_user_events( $user_id = false, $pag_num = false, $pag_page = false ) {
	global $bp;

	if ( !$user_id )
		$user_id = $bp->displayed_user->id;

	return JES_Events_Member::jes_get_event_ids( $user_id, $pag_num, $pag_page );
}

function events_total_events_for_user( $user_id = false ) {
	global $bp;

	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

	if ( !$count = wp_cache_get( 'bp_total_events_for_user_' . $user_id, 'bp' ) ) {
		$count = JES_Events_Member::jes_total_event_count( $user_id );
		wp_cache_set( 'bp_total_events_for_user_' . $user_id, $count, 'bp' );
	}

	return $count;
}

/*** Event Avatars *************************************************************/

function events_avatar_upload_dir( $event_id = false ) {
	global $bp;

	if ( !$event_id )
		$event_id = $bp->jes_events->current_event->id;

	$path = BP_AVATAR_UPLOAD_PATH . '/event-avatars/' . $event_id;
	$newbdir = $path;

	if ( !file_exists( $path ) )
		@wp_mkdir_p( $path );

	$newurl = BP_AVATAR_URL . '/event-avatars/' . $event_id;
	$newburl = $newurl;
	$newsubdir = '/event-avatars/' . $event_id;

	return apply_filters( 'events_avatar_upload_dir', array( 'path' => $path, 'url' => $newurl, 'subdir' => $newsubdir, 'basedir' => $newbdir, 'baseurl' => $newburl, 'error' => false ) );
}

/*** Event Member Status Checks ************************************************/

function events_is_user_admin( $user_id, $event_id ) {
	return JES_Events_Member::jes_check_is_admin( $user_id, $event_id );
}

function events_is_user_mod( $user_id, $event_id ) {
	return JES_Events_Member::jes_check_is_mod( $user_id, $event_id );
}

function events_is_user_member( $user_id, $event_id ) {
	return JES_Events_Member::jes_check_is_member( $user_id, $event_id );
}

function events_is_user_banned( $user_id, $event_id ) {
	return JES_Events_Member::jes_check_is_banned( $user_id, $event_id );
}

/*** Event Activity Posting **************************************************/

function events_post_update( $args = '' ) {
	global $bp;

	$defaults = array(
		'content' => false,
		'user_id' => $bp->loggedin_user->id,
		'event_id' => $bp->jes_events->current_event->id
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( empty($content) || !strlen( trim( $content ) ) || empty($user_id) || empty($event_id) )
		return false;

	$bp->jes_events->current_event = new JES_Events_Event( $event_id );

	/* Be sure the user is a member of the event before posting. */
	if ( !is_site_admin() && !events_is_user_member( $user_id, $event_id ) )
		return false;

	/* Record this in activity streams */
	$activity_action = sprintf( __( '%s posted an update in the event %s:', 'jet-event-system'), bp_core_get_userlink( $user_id ), '<a href="' . jes_bp_get_event_permalink( $bp->jes_events->current_event ) . '">' . attribute_escape( $bp->jes_events->current_event->name ) . '</a>' );
	$activity_content = $content;

	$activity_id = events_record_activity( array(
		'user_id' => $user_id,
		'action' => apply_filters( 'events_activity_new_update_action', $activity_action ),
		'content' => apply_filters( 'events_activity_new_update_content', $activity_content ),
		'type' => 'activity_update',
		'item_id' => $event_id
	) );

 	/* Require the notifications code so email notifications can be set on the 'bp_activity_posted_update' action. */
	require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-notifications.php' );

	events_update_eventmeta( $event_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );
	do_action( 'jes_bp_events_posted_update', $content, $user_id, $event_id, $activity_id );

	return $activity_id;
}

/*** Event Promotion & Banning *************************************************/

function events_promote_member( $user_id, $event_id, $status ) {
	global $bp;

	if ( !$bp->is_item_admin )
		return false;

	$member = new JES_Events_Member( $user_id, $event_id );

	do_action( 'events_premote_member', $event_id, $user_id, $status );

	return $member->promote( $status );
}

function events_demote_member( $user_id, $event_id ) {
	global $bp;

	$member = new JES_Events_Member( $user_id, $event_id );

	do_action( 'events_demote_member', $event_id, $user_id );

	return $member->demote();
}

function events_ban_member( $user_id, $event_id ) {
	global $bp;

	if ( !$bp->is_item_admin )
		return false;

	$member = new JES_Events_Member( $user_id, $event_id );

	do_action( 'events_ban_member', $event_id, $user_id );

	if ( !$member->ban() )
		return false;

	update_usermeta( $user_id, 'jes_total_event_count', (int)$total_count - 1 );
}

function events_unban_member( $user_id, $event_id ) {
	global $bp;

	if ( !$bp->is_item_admin )
		return false;

	$member = new JES_Events_Member( $user_id, $event_id );

	do_action( 'events_unban_member', $event_id, $user_id );

	return $member->unban();
}

/*** Event Membership ****************************************************/

function events_send_membership_request( $requesting_user_id, $event_id ) {
	global $bp;

	/* Prevent duplicate requests */
	if ( events_jes_check_for_membership_request( $requesting_user_id, $event_id ) )
		return false;

	/* Check if the user is already a member or is banned */
	if ( events_is_user_member( $requesting_user_id, $event_id ) || events_is_user_banned( $requesting_user_id, $event_id ) )
		return false;

	$requesting_user = new JES_Events_Member;
	$requesting_user->event_id = $event_id;
	$requesting_user->user_id = $requesting_user_id;
	$requesting_user->inviter_id = 0;
	$requesting_user->is_admin = 0;
	$requesting_user->user_title = '';
	$requesting_user->date_modified = gmdate( "Y-m-d H:i:s" );
	$requesting_user->is_confirmed = 0;
	$requesting_user->comments = $_POST['event-request-join-to-event-comments'];

	if ( $requesting_user->save() ) {
		$admins = events_get_event_admins( $event_id );

		require_once ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-notifications.php' );

		for ( $i = 0; $i < count( $admins ); $i++ ) {
			// Saved okay, now send the email notification
			events_notification_new_membership_request( $requesting_user_id, $admins[$i]->user_id, $event_id, $requesting_user->id );
		}

		do_action( 'events_membership_requested', $requesting_user_id, $admins, $event_id, $requesting_user->id );

		return true;
	}

	return false;
}

function events_accept_membership_request( $membership_id, $user_id = false, $event_id = false ) {
	global $bp;

	if ( $user_id && $event_id )
		$membership = new JES_Events_Member( $user_id, $event_id );
	else
		$membership = new JES_Events_Member( false, false, $membership_id );

	$membership->jes_accept_request();

	if ( !$membership->save() )
		return false;

	/* Check if the user has an outstanding invite, if so delete it. */
	if ( events_check_user_has_invite( $membership->user_id, $membership->event_id ) )
		events_jes_delete_invite( $membership->user_id, $membership->event_id );

	/* Modify event member count */
	events_update_eventmeta( $membership->event_id, 'total_member_count', (int) events_get_eventmeta( $membership->event_id, 'total_member_count') + 1 );

	/* Record this in activity streams */
	$event = new JES_Events_Event( $membership->event_id );

	events_record_activity( array(
		'action'	=> apply_filters( 'events_activity_membership_accepted_action', sprintf( __( '%s joined the event %s', 'jet-event-system'), bp_core_get_userlink( $membership->user_id ), '<a href="' . jes_bp_get_event_permalink( $event ) . '">' . attribute_escape( $event->name ) . '</a>' ), $membership->user_id, &$event ),
		'type'		=> 'joined_event',
		'item_id'	=> $membership->event_id,
		'user_id'	=> $membership->user_id
	) );

	/* Send a notification to the user. */
	require_once ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-notifications.php' );
	events_notification_membership_request_completed( $membership->user_id, $membership->event_id, true );

	do_action( 'events_membership_accepted', $membership->user_id, $membership->event_id );

	return true;
}

function events_reject_membership_request( $membership_id, $user_id = false, $event_id = false ) {
	if ( !$membership = events_delete_membership_request( $membership_id, $user_id, $event_id ) )
		return false;

	// Send a notification to the user.
	require_once ( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/main/jet-events-notifications.php' );
	events_notification_membership_request_completed( $membership->user_id, $membership->event_id, false );

	do_action( 'events_membership_rejected', $membership->user_id, $membership->event_id );

	return true;
}

function events_delete_membership_request( $membership_id, $user_id = false, $event_id = false ) {
	if ( $user_id && $event_id )
		$membership = new JES_Events_Member( $user_id, $event_id );
	else
		$membership = new JES_Events_Member( false, false, $membership_id );

	if ( !JES_Events_Member::delete( $membership->user_id, $membership->event_id ) )
		return false;

	return $membership;
}

function events_jes_check_for_membership_request( $user_id, $event_id ) {
	return JES_Events_Member::jes_check_for_membership_request( $user_id, $event_id );
}

function events_accept_all_pending_membership_requests( $event_id ) {
	$user_ids = JES_Events_Member::jes_get_all_membership_request_user_ids( $event_id );

	if ( !$user_ids )
		return false;

	foreach ( (array) $user_ids as $user_id ) {
		events_accept_membership_request( false, $user_id, $event_id );
	}

	do_action( 'events_accept_all_pending_membership_requests', $event_id );

	return true;
}

/*** Event Meta ****************************************************/

function events_delete_eventmeta( $event_id, $meta_key = false, $meta_value = false ) {
	global $wpdb, $bp;

	if ( !is_numeric( $event_id ) )
		return false;

	$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

	if ( is_array($meta_value) || is_object($meta_value) )
		$meta_value = serialize($meta_value);

	$meta_value = trim( $meta_value );

	if ( !$meta_key ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM " . $bp->jes_events->table_name_eventmeta . " WHERE event_id = %d", $event_id ) );
	} else if ( $meta_value ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM " . $bp->jes_events->table_name_eventmeta . " WHERE event_id = %d AND meta_key = %s AND meta_value = %s", $event_id, $meta_key, $meta_value ) );
	} else {
		$wpdb->query( $wpdb->prepare( "DELETE FROM " . $bp->jes_events->table_name_eventmeta . " WHERE event_id = %d AND meta_key = %s", $event_id, $meta_key ) );
	}

	/* Delete the cached object */
	wp_cache_delete( 'jes_events_eventmeta_' . $event_id . '_' . $meta_key, 'bp' );

	return true;
}

function events_get_eventmeta( $event_id, $meta_key = '') {
	global $wpdb, $bp;

	$event_id = (int) $event_id;

	if ( !$event_id )
		return false;

	if ( !empty($meta_key) ) {
		$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

		if ( !$metas = wp_cache_get( 'jes_events_eventmeta_' . $event_id . '_' . $meta_key, 'bp' ) ) {
			$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM " . $bp->jes_events->table_name_eventmeta . " WHERE event_id = %d AND meta_key = %s", $event_id, $meta_key) );
			wp_cache_set( 'jes_events_eventmeta_' . $event_id . '_' . $meta_key, $metas, 'bp' );
		}
	} else {
		$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM " . $bp->jes_events->table_name_eventmeta . " WHERE event_id = %d", $event_id) );
	}

	if ( empty($metas) ) {
		if ( empty($meta_key) )
			return array();
		else
			return '';
	}

	$metas = array_map('maybe_unserialize', (array)$metas);

	if ( 1 == count($metas) )
		return $metas[0];
	else
		return $metas;
}

function events_update_eventmeta( $event_id, $meta_key, $meta_value ) {
	global $wpdb, $bp;

	if ( !is_numeric( $event_id ) )
		return false;

	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	if ( is_string($meta_value) )
		$meta_value = stripslashes($wpdb->escape($meta_value));

	$meta_value = maybe_serialize($meta_value);

	if (empty($meta_value)) {
		return events_delete_eventmeta( $event_id, $meta_key );
	}

	$cur = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $bp->jes_events->table_name_eventmeta . " WHERE event_id = %d AND meta_key = %s", $event_id, $meta_key ) );

	if ( !$cur ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO " . $bp->jes_events->table_name_eventmeta . " ( event_id, meta_key, meta_value ) VALUES ( %d, %s, %s )", $event_id, $meta_key, $meta_value ) );
	} else if ( $cur->meta_value != $meta_value ) {
		$wpdb->query( $wpdb->prepare( "UPDATE " . $bp->jes_events->table_name_eventmeta . " SET meta_value = %s WHERE event_id = %d AND meta_key = %s", $meta_value, $event_id, $meta_key ) );
	} else {
		return false;
	}

	/* Update the cached object and recache */
	wp_cache_set( 'jes_events_eventmeta_' . $event_id . '_' . $meta_key, $meta_value, 'bp' );

	return true;
}

/*** Event Cleanup Functions ****************************************************/

function jes_events_remove_data_for_user( $user_id ) {
	JES_Events_Member::jes_delete_all_for_user($user_id);

	do_action( 'jes_events_remove_data_for_user', $user_id );
}
// add_action( 'wpmu_delete_user', 'jes_events_remove_data_for_user' );
// add_action( 'delete_user', 'jes_events_remove_data_for_user' );
// add_action( 'make_spam_user', 'jes_events_remove_data_for_user' );


/********************************************************************************
 * Caching
 *
 * Caching functions handle the clearing of cached objects and pages on specific
 * actions throughout BuddyPress.
 */

function events_clear_event_object_cache( $event_id ) {
	wp_cache_delete( 'events_event_nouserdata_' . $event_id, 'bp' );
	wp_cache_delete( 'events_event_' . $event_id, 'bp' );
	wp_cache_delete( 'newest_events', 'bp' );
	wp_cache_delete( 'soon_events', 'bp' );
	wp_cache_delete( 'active_events', 'bp' );
	wp_cache_delete( 'popular_events', 'bp' );
	wp_cache_delete( 'events_random_events', 'bp' );
	wp_cache_delete( 'bp_jes_total_event_count', 'bp' );
}
add_action( 'events_event_deleted', 'events_clear_event_object_cache' );
add_action( 'events_settings_updated', 'events_clear_event_object_cache' );
add_action( 'events_details_updated', 'events_clear_event_object_cache' );
add_action( 'events_event_avatar_updated', 'events_clear_event_object_cache' );
add_action( 'events_create_event_step_complete', 'events_clear_event_object_cache' );

function events_clear_event_user_object_cache( $event_id, $user_id ) {
	wp_cache_delete( 'bp_total_events_for_user_' . $user_id );
}
add_action( 'events_join_event', 'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_leave_event', 'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_ban_member', 'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_unban_member', 'events_clear_event_user_object_cache', 10, 2 );

/* List actions to clear super cached pages on, if super cache is installed */
add_action( 'events_join_event', 'bp_core_clear_cache' );
add_action( 'events_leave_event', 'bp_core_clear_cache' );
add_action( 'events_jes_accept_invite', 'bp_core_clear_cache' );
add_action( 'events_reject_invite', 'bp_core_clear_cache' );
add_action( 'events_invite_user', 'bp_core_clear_cache' );
add_action( 'events_uninvite_user', 'bp_core_clear_cache' );
add_action( 'events_details_updated', 'bp_core_clear_cache' );
add_action( 'events_settings_updated', 'bp_core_clear_cache' );
add_action( 'events_unban_member', 'bp_core_clear_cache' );
add_action( 'events_ban_member', 'bp_core_clear_cache' );
add_action( 'events_demote_member', 'bp_core_clear_cache' );
add_action( 'events_premote_member', 'bp_core_clear_cache' );
add_action( 'events_membership_rejected', 'bp_core_clear_cache' );
add_action( 'events_membership_accepted', 'bp_core_clear_cache' );
add_action( 'events_membership_requested', 'bp_core_clear_cache' );
add_action( 'events_create_event_step_complete', 'bp_core_clear_cache' );
add_action( 'events_created_event', 'bp_core_clear_cache' );
add_action( 'events_event_avatar_updated', 'bp_core_clear_cache' );
?>