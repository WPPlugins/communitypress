<?php 

require_once(TEMPLATEPATH . '/epanel/custom_functions.php'); 

require_once(TEMPLATEPATH . '/includes/functions/comments.php'); 

require_once(TEMPLATEPATH . '/includes/functions/sidebars.php'); 

load_theme_textdomain('Minimal',get_template_directory().'/lang');

require_once(TEMPLATEPATH . '/epanel/options_minimal.php');

require_once(TEMPLATEPATH . '/epanel/core_functions.php'); 

require_once(TEMPLATEPATH . '/epanel/post_thumbnails_minimal.php');

function register_main_menus() {
	register_nav_menus(
		array(
			'visit-menu' => __( 'Userbar Menu' ),
			'primary-menu' => __( 'Primary Menu' ),
			'footer-menu' => __( 'Footer Menu' )
		)
	);
};
if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );

$wp_ver = substr($GLOBALS['wp_version'],0,3);
if ($wp_ver >= 2.8) include(TEMPLATEPATH . '/includes/widgets.php');


if ( "on" == get_site_option('cp-foot-widgets') ) {
if ( function_exists('register_sidebars') )
	register_sidebar(array('name'=>'Footer #1','before_title'=>'<h4>','after_title'=>'</h4>'));
	register_sidebar(array('name'=>'Footer #2','before_title'=>'<h4>','after_title'=>'</h4>'));
	register_sidebar(array('name'=>'Footer #3','before_title'=>'<h4>','after_title'=>'</h4>'));
	register_sidebar(array('name'=>'Footer #4','before_title'=>'<h4>','after_title'=>'</h4>'));
}









// CHANGE BP ADMIN BAR LOGO
function custom_bp_adminbar_logo() {
global $wpdb, $bp, $current_blog;
echo '<a href="' . $bp->root_domain . '"><img id="admin-bar-logo" src="' . $bp->root_domain . get_site_option('cp-minilogo-url') . '" /></a>';
}
remove_action( 'bp_adminbar_logo', 'bp_adminbar_logo' );
add_action( 'bp_adminbar_logo', 'custom_bp_adminbar_logo' );






  	function jll_bp_get_member_latest_update( $args = '' ) {
          global $members_template, $bp;
  
          $defaults = array(
              'length' => 70
          );
  
          $r = wp_parse_args( $args, $defaults );
          extract( $r, EXTR_SKIP );
  
          if ( !isset( $members_template->member->latest_update ) || !$update = maybe_unserialize( $members_template->member->latest_update ) )
              return false;
  
          $raw_update_content = apply_filters( 'bp_get_activity_latest_update', strip_tags( bp_create_excerpt( $update['content'], $length ) ) );
  
          if ( !empty( $update['id'] ) && bp_is_active( 'activity' ) )
              $raw_update_content .= ' &middot; <a href="' . bp_get_root_domain() . '/activity/p/' . $update['id'] . '">' . __( 'View', 'buddypress' ) . '</a>';
  
  		$update_content = preg_replace('/[@]+([A-Za-z0-9-_\.]+)/', '', $raw_update_content);
  
         return apply_filters( 'bp_get_member_latest_update', $update_content );
       }
  




// ------- JLL MODDED check if member's privacy settings prohibits a profile component from displaying  ---------------------------------- //

function bp_privacy_filter($profilecomponent) {
	global $bp, $current_user;
	if ( 'friendsonly' == get_user_meta( $bp->displayed_user->id, 'privacy_'.$profilecomponent, true ) ) {
		if ( ('is_friend' != BP_Friends_Friendship::check_is_friend( $bp->loggedin_user->id, $bp->displayed_user->id )) && (bp_loggedin_user_id() != bp_displayed_user_id()) && !is_super_admin( bp_loggedin_user_id() ) )
		return true; //returns private
	}
}







	function jll_get_activity_latest_update( $user_id = false ) {
		global $bp;

		if ( !$user_id )
			$user_id = $bp->displayed_user->id;

		if ( !$update = get_user_meta( $user_id, 'bp_latest_update', true ) )
			return false;

		$raw_last_update = apply_filters( 'bp_get_activity_latest_update_excerpt', trim( strip_tags( bp_create_excerpt( $update['content'], 50 ) ) ) );
		
		preg_match( '/[@]+([A-Za-z0-9-_\.]+)/', $raw_last_update, $mentionmatch );

		if (!empty($mentionmatch[1])) {
			$latest_update = preg_replace('/[@]+([A-Za-z0-9-_\.]+)/', '', $raw_last_update);
			$latest_update .= ' &middot; <a href="' . $bp->root_domain . '/' . BP_ACTIVITY_SLUG . '/p/' . $update['id'] . '/"> ' . __( 'View', 'buddypress' ) . '</a>';
		} else {
			$latest_update = '&quot;'.$raw_last_update.'&quot;'.' &middot; <a href="' . $bp->root_domain . '/' . BP_ACTIVITY_SLUG . '/p/' . $update['id'] . '/"> ' . __( 'View', 'buddypress' ) . '</a>';
		}

		return apply_filters( 'bp_get_activity_latest_update', $latest_update  );
	}


function jll_get_activity_latest_update_no_view( $user_id = false ) {
		global $bp;

		if ( !$user_id )
			$user_id = $bp->displayed_user->id;

		if ( !$update = get_user_meta( $user_id, 'bp_latest_update', true ) )
			return false;

		$raw_last_update = apply_filters( 'bp_get_activity_latest_update_excerpt', trim( strip_tags( bp_create_excerpt( $update['content'], 50 ) ) ) );
		
		preg_match( '/[@]+([A-Za-z0-9-_\.]+)/', $raw_last_update, $mentionmatch );

		if (!empty($mentionmatch[1])) {
			$latest_update = preg_replace('/[@]+([A-Za-z0-9-_\.]+)/', '', $raw_last_update);
		} else {
			$latest_update = '&quot;'.$raw_last_update.'&quot;';
		}

		return apply_filters( 'bp_get_activity_latest_update', $latest_update  );
	}












/***
 * AJAX Functions
 *
 * All of these functions enhance the responsiveness of the user interface in the default
 * theme by adding AJAX functionality.
 *
 * By default your child theme will inherit this AJAX functionality. You can however create
 * your own _inc/ajax.php file and add/remove AJAX functionality as you see fit.
 */

/***
 * This function looks scarier than it actually is. :)
 * Each object loop (activity/members/groups/blogs/forums) contains default parameters to
 * show specific information based on the page we are currently looking at.
 * The following function will take into account any cookies set in the JS and allow us
 * to override the parameters sent. That way we can change the results returned without reloading the page.
 * By using cookies we can also make sure that user settings are retained across page loads.
 */
function bp_dtheme_ajax_querystring( $query_string, $object ) {
	global $bp;

	if ( empty( $object ) )
		return false;

	/* Set up the cookies passed on this AJAX request. Store a local var to avoid conflicts */
	if ( !empty( $_POST['cookie'] ) )
		$_BP_COOKIE = wp_parse_args( str_replace( '; ', '&', urldecode( $_POST['cookie'] ) ) );
	else
		$_BP_COOKIE = &$_COOKIE;

	$qs = false;

	/***
	 * Check if any cookie values are set. If there are then override the default params passed to the
	 * template loop
	 */
	if ( !empty( $_BP_COOKIE['bp-' . $object . '-filter'] ) && '-1' != $_BP_COOKIE['bp-' . $object . '-filter'] ) {
		$qs[] = 'type=' . $_BP_COOKIE['bp-' . $object . '-filter'];
		$qs[] = 'action=' . $_BP_COOKIE['bp-' . $object . '-filter']; // Activity stream filtering on action
	}

	if ( !empty( $_BP_COOKIE['bp-' . $object . '-scope'] ) ) {
		if ( 'personal' == $_BP_COOKIE['bp-' . $object . '-scope'] ) {
			$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
			$qs[] = 'user_id=' . $user_id;
		}
		if ( 'all' != $_BP_COOKIE['bp-' . $object . '-scope'] && empty( $bp->displayed_user->id ) && !$bp->is_single_item )
			$qs[] = 'scope=' . $_BP_COOKIE['bp-' . $object . '-scope']; // Activity stream scope only on activity directory.
	}

	/* If page and search_terms have been passed via the AJAX post request, use those */
	if ( !empty( $_POST['page'] ) && '-1' != $_POST['page'] )
		$qs[] = 'page=' . $_POST['page'];

	if ( !empty( $_POST['search_terms'] ) && __( ' Search anything...', 'buddypress' ) != $_POST['search_terms'] && 'false' != $_POST['search_terms'] && 'undefined' != $_POST['search_terms'] )
		$qs[] = 'search_terms=' . $_POST['search_terms'];

	/* Now pass the querystring to override default values. */
	$query_string = empty( $qs ) ? '' : join( '&', (array)$qs );

	return apply_filters( 'bp_dtheme_ajax_querystring', $query_string, $object, $_BP_COOKIE['bp-' . $object . '-filter'], $_BP_COOKIE['bp-' . $object . '-scope'], $_BP_COOKIE['bp-' . $object . '-page'], $_BP_COOKIE['bp-' . $object . '-search-terms'], $_BP_COOKIE['bp-' . $object . '-extras'] );
}
add_filter( 'bp_ajax_querystring', 'bp_dtheme_ajax_querystring', 10, 2 );

/* This function will simply load the template loop for the current object. On an AJAX request */
function bp_dtheme_object_template_loader() {
	$object = esc_attr( $_POST['object'] );
	locate_template( array( "$object/$object-loop.php" ), true );
}
add_action( 'wp_ajax_members_filter', 'bp_dtheme_object_template_loader' );
add_action( 'wp_ajax_groups_filter', 'bp_dtheme_object_template_loader' );
add_action( 'wp_ajax_blogs_filter', 'bp_dtheme_object_template_loader' );
add_action( 'wp_ajax_forums_filter', 'bp_dtheme_object_template_loader' );

/* This function will load the activity loop template when activity is requested via AJAX */
function bp_dtheme_activity_template_loader() {
	global $bp;

	/* We need to calculate and return the feed URL for each scope */
	$feed_url = site_url( BP_ACTIVITY_SLUG . '/feed/' );

	switch ( $_POST['scope'] ) {
		case 'friends':
			$feed_url = $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/friends/feed/';
			break;
		case 'groups':
			$feed_url = $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/groups/feed/';
			break;
		case 'favorites':
			$feed_url = $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/favorites/feed/';
			break;
		case 'mentions':
			$feed_url = $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/mentions/feed/';
			delete_usermeta( $bp->loggedin_user->id, 'bp_new_mention_count' );
			break;
	}

	/* Buffer the loop in the template to a var for JS to spit out. */
	ob_start();
	locate_template( array( 'activity/activity-loop.php' ), true );
	$result['contents'] = ob_get_contents();
	$result['feed_url'] = apply_filters( 'bp_dtheme_activity_feed_url', $feed_url, $_POST['scope'] );
	ob_end_clean();

	echo json_encode( $result );
}
add_action( 'wp_ajax_activity_widget_filter', 'bp_dtheme_activity_template_loader' );
add_action( 'wp_ajax_activity_get_older_updates', 'bp_dtheme_activity_template_loader' );

/* AJAX update posting */
function bp_dtheme_post_update() {
	global $bp;

	/* Check the nonce */
	check_admin_referer( 'post_update', '_wpnonce_post_update' );

	if ( !is_user_logged_in() ) {
		echo '-1';
		return false;
	}

	if ( empty( $_POST['content'] ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'Please enter some content to post.', 'buddypress' ) . '</p></div>';
		return false;
	}

	if ( empty( $_POST['object'] ) && function_exists( 'bp_activity_post_update' ) ) {
		$activity_id = bp_activity_post_update( array( 'content' => $_POST['content'] ) );
	} elseif ( $_POST['object'] == 'groups' ) {
		if ( !empty( $_POST['item_id'] ) && function_exists( 'groups_post_update' ) )
			$activity_id = groups_post_update( array( 'content' => $_POST['content'], 'group_id' => $_POST['item_id'] ) );
	} else
		$activity_id = apply_filters( 'bp_activity_custom_update', $_POST['object'], $_POST['item_id'], $_POST['content'] );

	if ( !$activity_id ) {
		echo '-1<div id="message" class="error"><p>' . __( 'There was a problem posting your update, please try again.', 'buddypress' ) . '</p></div>';
		return false;
	}

	if ( bp_has_activities ( 'include=' . $activity_id ) ) : ?>
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php locate_template( array( 'activity/entry.php' ), true ) ?>
		<?php endwhile; ?>
	 <?php endif;
}
add_action( 'wp_ajax_post_update', 'bp_dtheme_post_update' );

/* AJAX activity comment posting */
function bp_dtheme_new_activity_comment() {
	global $bp;

	/* Check the nonce */
	check_admin_referer( 'new_activity_comment', '_wpnonce_new_activity_comment' );

	if ( !is_user_logged_in() ) {
		echo '-1';
		return false;
	}

	if ( empty( $_POST['content'] ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'Please do not leave the comment area blank.', 'buddypress' ) . '</p></div>';
		return false;
	}

	if ( empty( $_POST['form_id'] ) || empty( $_POST['comment_id'] ) || !is_numeric( $_POST['form_id'] ) || !is_numeric( $_POST['comment_id'] ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'There was an error posting that reply, please try again.', 'buddypress' ) . '</p></div>';
		return false;
	}

	$comment_id = bp_activity_new_comment( array(
		'content' => $_POST['content'],
		'activity_id' => $_POST['form_id'],
		'parent_id' => $_POST['comment_id']
	));

	if ( !$comment_id ) {
		echo '-1<div id="message" class="error"><p>' . __( 'There was an error posting that reply, please try again.', 'buddypress' ) . '</p></div>';
		return false;
	}

	if ( bp_has_activities ( 'include=' . $comment_id ) ) : ?>
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<li id="acomment-<?php bp_activity_id() ?>">
				<div class="acomment-avatar">
					<?php bp_activity_avatar() ?>
				</div>

				<div class="acomment-meta">
					<?php echo bp_core_get_userlink( bp_get_activity_user_id() ) ?> &middot; <?php printf( __( '%s ago', 'buddypress' ), bp_core_time_since( bp_core_current_time() ) ) ?> &middot;
					<a class="acomment-reply" href="#acomment-<?php bp_activity_id() ?>" id="acomment-reply-<?php echo esc_attr( $_POST['form_id'] ) ?>"><?php _e( 'Reply', 'buddypress' ) ?></a>
					 &middot; <a href="<?php echo wp_nonce_url( $bp->root_domain . '/' . $bp->activity->slug . '/delete/' . bp_get_activity_id() . '?cid=' . $comment_id, 'bp_activity_delete_link' ) ?>" class="delete acomment-delete confirm"><?php _e( 'Delete', 'buddypress' ) ?></a>
				</div>

				<div class="acomment-content">
					<?php bp_activity_content_body() ?>
				</div>
			</li>
		<?php endwhile; ?>
	 <?php endif;
}
add_action( 'wp_ajax_new_activity_comment', 'bp_dtheme_new_activity_comment' );

/* AJAX delete an activity */
function bp_dtheme_delete_activity() {
	global $bp;

	/* Check the nonce */
	check_admin_referer( 'bp_activity_delete_link' );

	if ( !is_user_logged_in() ) {
		echo '-1';
		return false;
	}

	$activity = new BP_Activity_Activity( $_POST['id'] );

	/* Check access */
	if ( !is_super_admin() && $activity->user_id != $bp->loggedin_user->id )
		return false;

	if ( empty( $_POST['id'] ) || !is_numeric( $_POST['id'] ) )
		return false;

	/* Call the action before the delete so plugins can still fetch information about it */
	do_action( 'bp_activity_action_delete_activity', $_POST['id'], $activity->user_id );

	if ( !bp_activity_delete( array( 'id' => $_POST['id'], 'user_id' => $activity->user_id ) ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'There was a problem when deleting. Please try again.', 'buddypress' ) . '</p></div>';
		return false;
	}

	return true;
}
add_action( 'wp_ajax_delete_activity', 'bp_dtheme_delete_activity' );

/* AJAX delete an activity comment */
function bp_dtheme_delete_activity_comment() {
	global $bp;

	/* Check the nonce */
	check_admin_referer( 'bp_activity_delete_link' );

	if ( !is_user_logged_in() ) {
		echo '-1';
		return false;
	}

	$comment = new BP_Activity_Activity( $_POST['id'] );

	/* Check access */
	if ( !is_super_admin() && $comment->user_id != $bp->loggedin_user->id )
		return false;

	if ( empty( $_POST['id'] ) || !is_numeric( $_POST['id'] ) )
		return false;

	/* Call the action before the delete so plugins can still fetch information about it */
	do_action( 'bp_activity_action_delete_activity', $_POST['id'], $comment->user_id );

	if ( !bp_activity_delete_comment( $comment->item_id, $comment->id ) ) {
		echo '-1<div id="message" class="error"><p>' . __( 'There was a problem when deleting. Please try again.', 'buddypress' ) . '</p></div>';
		return false;
	}

	return true;
}
add_action( 'wp_ajax_delete_activity_comment', 'bp_dtheme_delete_activity_comment' );

/* AJAX mark an activity as a favorite */
function bp_dtheme_mark_activity_favorite() {
	global $bp;

	bp_activity_add_user_favorite( $_POST['id'] );
	_e( 'Remove Favorite', 'buddypress' );
}
add_action( 'wp_ajax_activity_mark_fav', 'bp_dtheme_mark_activity_favorite' );

/* AJAX mark an activity as not a favorite */
function bp_dtheme_unmark_activity_favorite() {
	global $bp;

	bp_activity_remove_user_favorite( $_POST['id'] );
	_e( 'Favorite', 'buddypress' );
}
add_action( 'wp_ajax_activity_mark_unfav', 'bp_dtheme_unmark_activity_favorite' );

/* AJAX invite a friend to a group functionality */
function bp_dtheme_ajax_invite_user() {
	global $bp;

	check_ajax_referer( 'groups_invite_uninvite_user' );

	if ( !$_POST['friend_id'] || !$_POST['friend_action'] || !$_POST['group_id'] )
		return false;

	if ( !groups_is_user_admin( $bp->loggedin_user->id, $_POST['group_id'] ) )
		return false;

	if ( !friends_check_friendship( $bp->loggedin_user->id, $_POST['friend_id'] ) )
		return false;

	if ( 'invite' == $_POST['friend_action'] ) {

		if ( !groups_invite_user( array( 'user_id' => $_POST['friend_id'], 'group_id' => $_POST['group_id'] ) ) )
			return false;

		$user = new BP_Core_User( $_POST['friend_id'] );

		echo '<li id="uid-' . $user->id . '">';
		echo $user->avatar_thumb;
		echo '<h4>' . $user->user_link . '</h4>';
		echo '<span class="activity">' . esc_attr( $user->last_active ) . '</span>';
		echo '<div class="action">
				<a class="remove" href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->groups->slug . '/' . $_POST['group_id'] . '/invites/remove/' . $user->id, 'groups_invite_uninvite_user' ) . '" id="uid-' . esc_attr( $user->id ) . '">' . __( 'Remove Invite', 'buddypress' ) . '</a>
			  </div>';
		echo '</li>';

	} else if ( 'uninvite' == $_POST['friend_action'] ) {

		if ( !groups_uninvite_user( $_POST['friend_id'], $_POST['group_id'] ) )
			return false;

		return true;

	} else {
		return false;
	}
}
add_action( 'wp_ajax_groups_invite_user', 'bp_dtheme_ajax_invite_user' );

/* AJAX add/remove a user as a friend when clicking the button */
function bp_dtheme_ajax_addremove_friend() {
	global $bp;

	if ( 'is_friend' == BP_Friends_Friendship::check_is_friend( $bp->loggedin_user->id, $_POST['fid'] ) ) {

		check_ajax_referer('friends_remove_friend');

		if ( !friends_remove_friend( $bp->loggedin_user->id, $_POST['fid'] ) ) {
			echo __("Friendship could not be canceled.", 'buddypress');
		} else {
			echo '<a id="friend-' . $_POST['fid'] . '" class="add" rel="add" title="' . __( 'Add Friend', 'buddypress' ) . '" href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->friends->slug . '/add-friend/' . $_POST['fid'], 'friends_add_friend' ) . '">' . __( 'Add Friend', 'buddypress' ) . '</a>';
		}

	} else if ( 'not_friends' == BP_Friends_Friendship::check_is_friend( $bp->loggedin_user->id, $_POST['fid'] ) ) {

		check_ajax_referer('friends_add_friend');

		if ( !friends_add_friend( $bp->loggedin_user->id, $_POST['fid'] ) ) {
			echo __("Friendship could not be requested.", 'buddypress');
		} else {
			echo '<a href="' . $bp->loggedin_user->domain . $bp->friends->slug . '" class="requested">' . __( 'Friendship Requested', 'buddypress' ) . '</a>';
		}
	} else {
		echo __( 'Request Pending', 'buddypress' );
	}

	return false;
}
add_action( 'wp_ajax_addremove_friend', 'bp_dtheme_ajax_addremove_friend' );

/* AJAX accept a user as a friend when clicking the "accept" button */
function bp_dtheme_ajax_accept_friendship() {
	check_admin_referer( 'friends_accept_friendship' );

	if ( !friends_accept_friendship( $_POST['id'] ) )
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem accepting that request. Please try again.', 'buddypress' ) . '</p></div>';

	return true;
}
add_action( 'wp_ajax_accept_friendship', 'bp_dtheme_ajax_accept_friendship' );

/* AJAX reject a user as a friend when clicking the "reject" button */
function bp_dtheme_ajax_reject_friendship() {
	check_admin_referer( 'friends_reject_friendship' );

	if ( !friends_reject_friendship( $_POST['id'] ) )
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem rejecting that request. Please try again.', 'buddypress' ) . '</p></div>';

	return true;
}
add_action( 'wp_ajax_reject_friendship', 'bp_dtheme_ajax_reject_friendship' );

/* AJAX join or leave a group when clicking the "join/leave" button */
function bp_dtheme_ajax_joinleave_group() {
	global $bp;

	if ( groups_is_user_banned( $bp->loggedin_user->id, $_POST['gid'] ) )
		return false;

	if ( !$group = new BP_Groups_Group( $_POST['gid'], false, false ) )
		return false;

	if ( 'hidden' == $group->status )
		return false;

	if ( !groups_is_user_member( $bp->loggedin_user->id, $group->id ) ) {

		if ( 'public' == $group->status ) {

			check_ajax_referer( 'groups_join_group' );

			if ( !groups_join_group( $group->id ) ) {
				_e( 'Error joining group', 'buddypress' );
			} else {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="leave-group" rel="leave" title="' . __( 'Leave Group', 'buddypress' ) . '" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' ) . '">' . __( 'Leave Group', 'buddypress' ) . '</a>';
			}

		} else if ( 'private' == $group->status ) {

			check_ajax_referer( 'groups_request_membership' );

			if ( !groups_send_membership_request( $bp->loggedin_user->id, $group->id ) ) {
				_e( 'Error requesting membership', 'buddypress' );
			} else {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="membership-requested" rel="membership-requested" title="' . __( 'Membership Requested', 'buddypress' ) . '" href="' . bp_get_group_permalink( $group ) . '">' . __( 'Membership Requested', 'buddypress' ) . '</a>';
			}
		}

	} else {

		check_ajax_referer( 'groups_leave_group' );

		if ( !groups_leave_group( $group->id ) ) {
			_e( 'Error leaving group', 'buddypress' );
		} else {
			if ( 'public' == $group->status ) {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="join-group" rel="join" title="' . __( 'Join Group', 'buddypress' ) . '" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'join', 'groups_join_group' ) . '">' . __( 'Join Group', 'buddypress' ) . '</a>';
			} else if ( 'private' == $group->status ) {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="request-membership" rel="join" title="' . __( 'Request Membership', 'buddypress' ) . '" href="' . wp_nonce_url( bp_get_group_permalink( $group ) . 'request-membership', 'groups_send_membership_request' ) . '">' . __( 'Request Membership', 'buddypress' ) . '</a>';
			}
		}
	}
}
add_action( 'wp_ajax_joinleave_group', 'bp_dtheme_ajax_joinleave_group' );

/* AJAX close and keep closed site wide notices from an admin in the sidebar */
function bp_dtheme_ajax_close_notice() {
	global $userdata;

	if ( !isset( $_POST['notice_id'] ) ) {
		echo "-1<div id='message' class='error'><p>" . __('There was a problem closing the notice.', 'buddypress') . '</p></div>';
	} else {
		$notice_ids = get_user_meta( $userdata->ID, 'closed_notices', true );

		$notice_ids[] = (int) $_POST['notice_id'];

		update_user_meta( $userdata->ID, 'closed_notices', $notice_ids );
	}
}
add_action( 'wp_ajax_messages_close_notice', 'bp_dtheme_ajax_close_notice' );

/* AJAX send a private message reply to a thread */
function bp_dtheme_ajax_messages_send_reply() {
	global $bp;

	check_ajax_referer( 'messages_send_message' );

	$result = messages_new_message( array( 'thread_id' => $_REQUEST['thread_id'], 'content' => $_REQUEST['content'] ) );

	if ( $result ) { ?>
		<div class="message-box new-message">
			<div class="message-metadata">
				<?php do_action( 'bp_before_message_meta' ) ?>
				<?php echo bp_loggedin_user_avatar( 'type=thumb&width=30&height=30' ); ?>

				<strong><a href="<?php echo $bp->loggedin_user->domain ?>"><?php echo $bp->loggedin_user->fullname ?></a> <span class="activity"><?php printf( __( 'Sent %s ago', 'buddypress' ), bp_core_time_since( bp_core_current_time() ) ) ?></span></strong>

				<?php do_action( 'bp_after_message_meta' ) ?>
			</div>

			<?php do_action( 'bp_before_message_content' ) ?>

			<div class="message-content">
				<?php echo stripslashes( apply_filters( 'bp_get_the_thread_message_content', $_REQUEST['content'] ) ) ?>
			</div>

			<?php do_action( 'bp_after_message_content' ) ?>

			<div class="clear"></div>
		</div>
	<?php
	} else {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem sending that reply. Please try again.', 'buddypress' ) . '</p></div>';
	}
}
add_action( 'wp_ajax_messages_send_reply', 'bp_dtheme_ajax_messages_send_reply' );

/* AJAX mark a private message as unread in your inbox */
function bp_dtheme_ajax_message_markunread() {
	global $bp;

	if ( !isset($_POST['thread_ids']) ) {
		echo "-1<div id='message' class='error'><p>" . __('There was a problem marking messages as unread.', 'buddypress' ) . '</p></div>';
	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0; $i < count($thread_ids); $i++ ) {
			BP_Messages_Thread::mark_as_unread($thread_ids[$i]);
		}
	}
}
add_action( 'wp_ajax_messages_markunread', 'bp_dtheme_ajax_message_markunread' );

/* AJAX mark a private message as read in your inbox */
function bp_dtheme_ajax_message_markread() {
	global $bp;

	if ( !isset($_POST['thread_ids']) ) {
		echo "-1<div id='message' class='error'><p>" . __('There was a problem marking messages as read.', 'buddypress' ) . '</p></div>';
	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0; $i < count($thread_ids); $i++ ) {
			BP_Messages_Thread::mark_as_read($thread_ids[$i]);
		}
	}
}
add_action( 'wp_ajax_messages_markread', 'bp_dtheme_ajax_message_markread' );

/* AJAX delete a private message or array of messages in your inbox */
function bp_dtheme_ajax_messages_delete() {
	global $bp;

	if ( !isset($_POST['thread_ids']) ) {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem deleting messages.', 'buddypress' ) . '</p></div>';
	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0; $i < count($thread_ids); $i++ )
			BP_Messages_Thread::delete($thread_ids[$i]);

		_e('Messages deleted.', 'buddypress');
	}
}
add_action( 'wp_ajax_messages_delete', 'bp_dtheme_ajax_messages_delete' );

/* AJAX autocomplete your friends names on the compose screen */
function bp_dtheme_ajax_messages_autocomplete_results() {
	global $bp;

	$friends = false;

	// Get the friend ids based on the search terms
	if ( function_exists( 'friends_search_friends' ) )
		$friends = friends_search_friends( $_GET['q'], $bp->loggedin_user->id, $_GET['limit'], 1 );

	$friends = apply_filters( 'bp_friends_autocomplete_list', $friends, $_GET['q'], $_GET['limit'] );

	if ( $friends['friends'] ) {
		foreach ( (array)$friends['friends'] as $user_id ) {
			$ud = get_userdata($user_id);
			$username = $ud->user_login;
			echo bp_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => 'thumb', 'width' => 15, 'height' => 15 ) ) . ' &nbsp;' . bp_core_get_user_displayname( $user_id ) . ' (' . $username . ')
			';
		}
	}
}
add_action( 'wp_ajax_messages_autocomplete_results', 'bp_dtheme_ajax_messages_autocomplete_results' );

































// Stop the theme from killing WordPress if BuddyPress is not enabled.
if ( !class_exists( 'BP_Core_User' ) )
	return false;

// Load the javascript for the theme
wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery' ) );

// Add words that we need to use in JS to the end of the page so they can be translated and still used.
$params = array(
	'my_favs'           => __( 'My Favorites', 'buddypress' ),
	'accepted'          => __( 'Accepted', 'buddypress' ),
	'rejected'          => __( 'Rejected', 'buddypress' ),
	'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
	'show_all'          => __( 'Show all', 'buddypress' ),
	'comments'          => __( 'comments', 'buddypress' ),
	'close'             => __( 'Close', 'buddypress' ),
	'mention_explain'   => sprintf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", 'buddypress' ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname( bp_get_displayed_user_fullname() ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) )
);
wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );

/**
 * Add the JS needed for blog comment replies
 *
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_add_blog_comments_js() {
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'template_redirect', 'bp_dtheme_add_blog_comments_js' );

/**
 * HTML for outputting blog comments as defined by the WP comment API
 *
 * @param mixed $comment Comment record from database
 * @param array $args Arguments from wp_list_comments() call
 * @param int $depth Comment nesting level
 * @see wp_list_comments()
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_blog_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>

	<?php if ( 'pingback' == $comment->comment_type ) return false; ?>

	<li id="comment-<?php comment_ID(); ?>">
		<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_comment_author_url() ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => 50, 'height' => 50, 'email' => $comment->comment_author_email ) ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, 50 ) ?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		<div class="comment-content">

			<div class="comment-meta">
				<a href="<?php echo get_comment_author_url() ?>" rel="nofollow"><?php echo get_comment_author(); ?></a> <?php _e( 'said:', 'buddypress' ) ?>
				<em><?php _e( 'On', 'buddypress' ) ?> <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date() ?></a></em>
			</div>

			<?php if ( $comment->comment_approved == '0' ) : ?>
			 	<em class="moderate"><?php _e('Your comment is awaiting moderation.'); ?></em><br />
			<?php endif; ?>

			<?php comment_text() ?>

			<div class="comment-options">
				<?php echo comment_reply_link( array('depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ?>
				<?php edit_comment_link( __( 'Edit' ),'','' ); ?>
			</div>

		</div>
<?php
}

/**
 * Filter the dropdown for selecting the page to show on front to include "Activity Stream"
 *
 * @param string $page_html A list of pages as a dropdown (select list)
 * @see wp_dropdown_pages()
 * @return string
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_wp_pages_filter( $page_html ) {
	if ( !bp_is_active( 'activity' ) )
		return $page_html;

	if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
		return $page_html;

	$selected = false;
	$page_html = str_replace( '</select>', '', $page_html );

	if ( bp_dtheme_page_on_front() == 'activity' )
		$selected = ' selected="selected"';

	$page_html .= '<option class="level-0" value="activity"' . $selected . '>' . __( 'Activity Stream', 'buddypress' ) . '</option></select>';
	return $page_html;
}
add_filter( 'wp_dropdown_pages', 'bp_dtheme_wp_pages_filter' );

/**
 * Hijack the saving of page on front setting to save the activity stream setting
 *
 * @param $string $oldvalue Previous value of get_option( 'page_on_front' )
 * @param $string $oldvalue New value of get_option( 'page_on_front' )
 * @return string
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front_update( $oldvalue, $newvalue ) {
	if ( !is_admin() || !is_super_admin() )
		return false;

	if ( 'activity' == $_POST['page_on_front'] )
		return 'activity';
	else
		return $oldvalue;
}
add_action( 'pre_update_option_page_on_front', 'bp_dtheme_page_on_front_update', 10, 2 );

/**
 * Load the activity stream template if settings allow
 *
 * @param string $template Absolute path to the page template 
 * @return string
 * @global WP_Query $wp_query WordPress query object
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front_template( $template ) {
	global $wp_query;

	if ( empty( $wp_query->post->ID ) )
		return locate_template( array( 'activity/index.php' ), false );
	else
		return $template;
}
add_filter( 'page_template', 'bp_dtheme_page_on_front_template' );

/**
 * Return the ID of a page set as the home page.
 *
 * @return false|int ID of page set as the home page
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front() {
	if ( 'page' != get_option( 'show_on_front' ) )
		return false;

	return apply_filters( 'bp_dtheme_page_on_front', get_option( 'page_on_front' ) );
}

/**
 * Force the page ID as a string to stop the get_posts query from kicking up a fuss.
 *
 * @global WP_Query $wp_query WordPress query object
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_fix_get_posts_on_activity_front() {
	global $wp_query;

	if ( !empty($wp_query->query_vars['page_id']) && 'activity' == $wp_query->query_vars['page_id'] )
		$wp_query->query_vars['page_id'] = '"activity"';
}
add_action( 'pre_get_posts', 'bp_dtheme_fix_get_posts_on_activity_front' );

/**
 * WP 3.0 requires there to be a non-null post in the posts array
 *
 * @param array $posts Posts as retrieved by WP_Query
 * @global WP_Query $wp_query WordPress query object
 * @return array
 * @package BuddyPress Theme
 * @since 1.2.5
 */
function bp_dtheme_fix_the_posts_on_activity_front( $posts ) {
	global $wp_query;

	// NOTE: the double quotes around '"activity"' are thanks to our previous function bp_dtheme_fix_get_posts_on_activity_front()
	if ( empty( $posts ) && !empty( $wp_query->query_vars['page_id'] ) && '"activity"' == $wp_query->query_vars['page_id'] )
		$posts = array( (object) array( 'ID' => 'activity' ) );

	return $posts;
}
add_filter( 'the_posts', 'bp_dtheme_fix_the_posts_on_activity_front' );

/**
 * bp_dtheme_activity_secondary_avatars()
 *
 * Add secondary avatar image to this activity stream's record, if supported
 *
 * @param string $action The text of this activity
 * @param BP_Activity_Activity $activity Activity object
 * @return string
 * @package BuddyPress Theme
 * @since 1.2.6
 */
function bp_dtheme_activity_secondary_avatars( $action, $activity ) {
	switch ( $activity->component ) {
		case 'groups' :
		case 'friends' :
			// Only insert avatar if one exists
			if ( $secondary_avatar = bp_get_activity_secondary_avatar() ) {
				$reverse_content = strrev( $action );
				$position        = strpos( $reverse_content, 'a<' );
				$action          = substr_replace( $action, $secondary_avatar, -$position - 2, 0 );
			}
			break;
	}

	return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bp_dtheme_activity_secondary_avatars', 10, 2 );


/**
 * Show a notice when the theme is activated - workaround by Ozh (http://old.nabble.com/Activation-hook-exist-for-themes--td25211004.html)
 *
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_show_notice() { ?>
	<div id="message" class="updated fade">
		<p><?php printf( __( 'Theme activated! This theme contains <a href="%s">custom header image</a> support and <a href="%s">sidebar widgets</a>.', 'buddypress' ), admin_url( 'themes.php?page=custom-header' ), admin_url( 'widgets.php' ) ) ?></p>
	</div>

	<style type="text/css">#message2, #message0 { display: none; }</style>
	<?php
}
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" )
	add_action( 'admin_notices', 'bp_dtheme_show_notice' );


// Member Buttons
if ( bp_is_active( 'friends' ) )
	add_action( 'bp_member_header_actions',    'bp_add_friend_button' );

if ( bp_is_active( 'activity' ) )
	add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );

if ( bp_is_active( 'messages' ) )
	add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );

// Group Buttons
if ( bp_is_active( 'groups' ) ) {
	add_action( 'bp_group_header_actions',     'bp_group_join_button' );
	add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
	add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
}

// Blog Buttons
if ( bp_is_active( 'blogs' ) )
	add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
























/** all the credit to @apeatling, the code below is a modified version of apeatling's code for function bp_dtheme_post_update in bp-default/_inc/ajax.php */
 
//let us remove the default handler for activity posting
 
remove_action( 'wp_ajax_post_update', 'bp_dtheme_post_update' );
//add our own handler for activity posting
 
add_action(  'wp_ajax_post_update', 'bp_mytheme_post_update' );
 
/* AJAX update posting */
function bp_mytheme_post_update() {
global $bp;
 
/* Check the nonce */
check_admin_referer( 'post_update', '_wpnonce_post_update' );
 
if ( !is_user_logged_in() ) {
echo '-1';
return false;
}
 
if ( empty( $_POST['content'] ) ) {
echo '-1<div id="message"><p>' . __( 'Please enter some content to post.', 'buddypress' ) . '</p></div>';
return false;
}
 
if ( empty( $_POST['object'] ) && function_exists( 'bp_activity_post_update' ) ) {
 
//this is what I have changed
 
if(!bp_is_home()&& bp_is_member())
$content='<span class="atmentionremoval">@'. bp_get_displayed_user_username() . ' </span>"'.$_POST['content'].'"';
else
$content=$_POST['content'];
 
$activity_id = bp_activity_post_update( array( 'content' => $content ) );
//end of my changes
} elseif ( $_POST['object'] == 'groups' ) {
if ( !empty( $_POST['item_id'] )&&function_exists( 'groups_post_update' ) )
$activity_id = groups_post_update( array( 'content' => $_POST['content'], 'group_id' => $_POST['item_id'] ) );
} else
$activity_id = apply_filters( 'bp_activity_custom_update', $_POST['object'], $_POST['item_id'], $_POST['content'] );
 
if ( !$activity_id ) {
echo '-1<div id="message"><p>' . __( 'There was a problem posting your update, please try again.', 'buddypress' ) . '</p></div>';
return false;
}
 
if ( bp_has_activities ( 'include=' . $activity_id ) ) : ?>
<?php while ( bp_activities() ) : bp_the_activity(); ?>
<?php locate_template( array( 'activity/entry.php' ), true ) ?>
<?php endwhile; ?>
<?php endif;
} 
?>