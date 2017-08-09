<?php

/*****************************************************************************
 * Events Template Class/Tags
 **/

class JES_Events_Template {
	var $current_event = -1;
	var $event_count;
	var $events;
	var $event;
	var $in_the_loop;
	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $jes_total_event_count;
	var $single_event = false;
	var $sort_by;
	var $order;

	function jes_events_template( $user_id, $type, $page, $per_page, $max, $slug, $search_terms, $populate_extras ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['grpage'] ) ? intval( $_REQUEST['grpage'] ) : $page;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		if ( 'invites' == $type )
			$this->events = events_jes_get_invites_for_user( $user_id, $this->pag_num, $this->pag_page );
		else if ( 'single-event' == $type ) {
			$event = new stdClass;
			$event->event_id = JES_Events_Event::jes_get_id_from_slug($slug);
			$this->events = array( $event );
		} else
			$this->events = events_get_events( array( 'type' => $type, 'per_page' => $this->pag_num, 'page' =>$this->pag_page, 'user_id' => $user_id, 'search_terms' => $search_terms, 'populate_extras' => $populate_extras ) );

		if ( 'invites' == $type ) {
			$this->jes_total_event_count = (int)$this->events['total'];
			$this->event_count = (int)$this->events['total'];
			$this->events = $this->events['events'];
		} else if ( 'single-event' == $type ) {
			$this->single_event = true;
			$this->jes_total_event_count = 1;
			$this->event_count = 1;
		} else {
			if ( !$max || $max >= (int)$this->events['total'] )
				$this->jes_total_event_count = (int)$this->events['total'];
			else
				$this->jes_total_event_count = (int)$max;

			$this->events = $this->events['events'];

			if ( $max ) {
				if ( $max >= count($this->events) )
					$this->event_count = count($this->events);
				else
					$this->event_count = (int)$max;
			} else {
				$this->event_count = count($this->events);
			}
		}

		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( array( 'grpage' => '%#%', 'num' => $this->pag_num, 's' => $search_terms, 'sortby' => $this->sort_by, 'order' => $this->order ) ),
			'format' => '',
			'total' => ceil($this->jes_total_event_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
	}


function bp_is_event_members() {
	global $bp;

	if ( BP_EVENTS_SLUG == $bp->current_component && $bp->is_single_item && 'members' == $bp->current_action )
		return true;

	return false;
}

function bp_is_invite_invites() {
	global $bp;

	if ( BP_EVENTS_SLUG == $bp->current_component && 'send-invites' == $bp->current_action )
		return true;

	return false;
}

function bp_is_event_membership_request() {
	global $bp;

	if ( BP_EVENTS_SLUG == $bp->current_component && 'request-membership' == $bp->current_action )
		return true;

	return false;
}

	function jes_has_events() {
		if ( $this->event_count )
			return true;

		return false;
	}

	function jes_next_event() {
		$this->current_event++;
		$this->event = $this->events[$this->current_event];

		return $this->event;
	}

	function jes_rewind_events() {
		$this->current_event = -1;
		if ( $this->event_count > 0 ) {
			$this->event = $this->events[0];
		}
	}

	function events() {
		if ( $this->current_event + 1 < $this->event_count ) {
			return true;
		} elseif ( $this->current_event + 1 == $this->event_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->jes_rewind_events();
		}

		$this->in_the_loop = false;
		return false;
	}

	function jes_the_event() {
		global $event;

		$this->in_the_loop = true;
		$this->event = $this->jes_next_event();

		if ( $this->single_event )
			$this->event = new JES_Events_Event( $this->event->event_id, true );
		else {
			if ( $this->event )
				wp_cache_set( 'events_event_nouserdata_' . $event->event_id, $this->event, 'bp' );
		}

		if ( 0 == $this->current_event ) // loop has just started
			do_action('loop_start');
	}
}

function bp_jes_has_events( $args = '' ) {
	global $events_template, $bp;

	$sdata = get_site_option('jes_events' );
	$sortby = $sdata[ 'jes_events_sort_by' ];
	$sortby_ad = $sdata[ 'jes_events_sort_by_ad' ];	
	
	/***
	 * Set the defaults based on the current page. Any of these will be overridden
	 * if arguments are directly passed into the loop. Custom plugins should always
	 * pass their parameters directly to the loop.
	 */
	$type = $sortby;
	$user_id = false;
	$search_terms = false;
	$slug = false;

	/* User filtering */
	if ( !empty( $bp->displayed_user->id ) )
		$user_id = $bp->displayed_user->id;

	/* Type */
	if ( 'my-events' == $bp->current_action ) {
		if ( 'most-popular' == $order )
			$type = 'popular';
		else if ( 'alphabetically' == $order )
			$type = 'alphabetical';
	} else if ( 'invites' == $bp->current_action ) {
		$type = 'invites';
	} else if ( $bp->jes_events->current_event->slug ) {
		$type = 'single-event';
		$slug = $bp->jes_events->current_event->slug;
	}

	if ( isset( $_REQUEST['event-filter-box'] ) || isset( $_REQUEST['s'] ) )
		$search_terms = ( isset( $_REQUEST['event-filter-box'] ) ) ? $_REQUEST['event-filter-box'] : $_REQUEST['s'];

	$defaults = array(
		'type' => $type,
		'page' => 1,
		'per_page' => 20,
		'max' => false,

		'user_id' => $user_id, // Pass a user ID to limit to events this user has joined
		'slug' => $slug, // Pass a event slug to only return that event
		'search_terms' => $search_terms, // Pass search terms to return only matching events

		'populate_extras' => true // Get extra meta - is_member, is_banned
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	$events_template = new JES_Events_Template( (int)$user_id, $type, (int)$page, (int)$per_page, (int)$max, $slug, $search_terms, (bool)$populate_extras );
	return apply_filters( 'bp_jes_has_events', $events_template->jes_has_events(), &$events_template );
}

function jes_bp_events() {
	global $events_template;
	return $events_template->events();
}

function bp_jes_the_event() {
	global $events_template;
	return $events_template->jes_the_event();
}

function jes_bp_event_is_visible( $event = false ) {
	global $bp, $events_template;

	if ( $bp->loggedin_user->is_site_admin )
		return true;

	if ( !$event )
		$event =& $events_template->event;

	if ( 'public' == $event->status ) {
		return true;
	} else {
		if ( events_is_user_member( $bp->loggedin_user->id, $event->id ) ) {
			return true;
		}
	}

	return false;
}

function jes_bp_event_id() {
	echo bp_get_event_id();
}
	function bp_get_event_id( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_id', $event->id );
	}

function jes_bp_event_name() {
	echo (jes_bp_get_event_name());
}
	function jes_bp_get_event_name( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_name', $event->name );
	}


	
function jes_bp_event_type() {
	echo jes_bp_get_event_type();
}
	function jes_bp_get_event_type( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		if ( 'public' == $event->status ) {
			$type = __( "Public Event", "jet-event-system" );
		} else if ( 'hidden' == $event->status ) {
			$type = __( "Hidden Event", "jet-event-system" );
		} else if ( 'private' == $event->status ) {
			$type = __( "Private Event", "jet-event-system" );
		} else {
			$type = ucwords( $event->status ) . ' ' . __( 'Event', 'jet-event-system' );
		}

		return apply_filters( 'jes_bp_get_event_type', $type );
	}

function jes_bp_event_status() {
	echo jes_bp_get_event_status();
}
	function jes_bp_get_event_status( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_status', $event->status );
	}

	

/* -------------- */ 	

function jes_core_fetch_avatar( $args = '' ) {
	global $bp, $current_blog;

	// Set a few default variables
	$def_object		= 'event';
	$def_type		= 'thumb';
	$def_class		= 'avatar';
	$def_alt		= __( 'Avatar Image', 'buddypress' );

	// Set the default variables array
	$defaults = array(
		'item_id'		=> false,
		'object'		=> $def_object,	// user/group/blog/custom type (if you use filters)
		'type'			=> $def_type,	// thumb or full
		'avatar_dir'	=> false,		// Specify a custom avatar directory for your object
		'width'			=> false,		// Custom width (int)
		'height'		=> false,		// Custom height (int)
		'class'			=> $def_class,	// Custom <img> class (string)
		'css_id'		=> false,		// Custom <img> ID (string)
		'alt'			=> $def_alt,	// Custom <img> alt (string)
		'email'			=> false,		// Pass the user email (for gravatar) to prevent querying the DB for it
		'no_grav'		=> false,		// If there is no avatar found, return false instead of a grav?
		'html'			=> true			// Wrap the return img URL in <img />
	);

	// Compare defaults to passed and extract
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	// Set item_id if not passed
	if ( !$item_id ) {
		if ( 'event' == $object )
			$item_id = $current_event->id;
			
		$item_id = apply_filters( 'bp_core_avatar_item_id', $item_id, $object );

		if ( !$item_id ) return false;
	}

	// Set avatar_dir if not passed (uses $object)
	if ( !$avatar_dir ) {
		if ( 'event' == $object )
			$avatar_dir = 'event-avatars';
			
		$avatar_dir = apply_filters( 'bp_core_avatar_dir', $avatar_dir, $object );

		if ( !$avatar_dir ) return false;
	}

	// Add an identifying class to each item
	$class .= ' ' . $object . '-' . $item_id . '-avatar';

	// Set CSS ID if passed
	if ( !empty( $css_id ) )
		$css_id = " id='{$css_id}'";

	// Set avatar width
	if ( $width )
		$html_width = " width='{$width}'";
	else
		$html_width = ( 'thumb' == $type ) ? ' width="' . BP_AVATAR_THUMB_WIDTH . '"' : ' width="' . BP_AVATAR_FULL_WIDTH . '"';

	// Set avatar height
	if ( $height )
		$html_height = " height='{$height}'";
	else
		$html_height = ( 'thumb' == $type ) ? ' height="' . BP_AVATAR_THUMB_HEIGHT . '"' : ' height="' . BP_AVATAR_FULL_HEIGHT . '"';

	// Set avatar URL and DIR based on prepopulated constants
	$avatar_folder_url = apply_filters( 'bp_core_avatar_folder_url', BP_AVATAR_URL . '/' . $avatar_dir . '/' . $item_id, $item_id, $object, $avatar_dir );
	$avatar_folder_dir = apply_filters( 'bp_core_avatar_folder_dir', BP_AVATAR_UPLOAD_PATH . '/' . $avatar_dir . '/' . $item_id, $item_id, $object, $avatar_dir );

	/****
	 * Look for uploaded avatar first. Use it if it exists.
	 * Set the file names to search for, to select the full size
	 * or thumbnail image.
	 */
	$avatar_size = ( 'full' == $type ) ? '-bpfull' : '-bpthumb';
	$legacy_user_avatar_name = ( 'full' == $type ) ? '-avatar2' : '-avatar1';
	$legacy_group_avatar_name = ( 'full' == $type ) ? '-groupavatar-full' : '-groupavatar-thumb';

	// Check for directory
	if ( file_exists( $avatar_folder_dir ) ) {

		// Open directory
		if ( $av_dir = opendir( $avatar_folder_dir ) ) {

			// Stash files in an array once to check for one that matches
			$avatar_files = array();
			while ( false !== ( $avatar_file = readdir($av_dir) ) ) {
				// Only add files to the array (skip directories)
				if ( 2 < strlen( $avatar_file ) )
					$avatar_files[] = $avatar_file;
			}

			// Check for array
			if ( 0 < count( $avatar_files ) ) {

				// Check for current avatar
				foreach( $avatar_files as $key => $value ) {
					if ( strpos ( $value, $avatar_size )!== false )
						$avatar_url = $avatar_folder_url . '/' . $avatar_files[$key];
				}

				// Legacy avatar check
				if ( !isset( $avatar_url ) ) {
					foreach( $avatar_files as $key => $value ) {
						if ( strpos ( $value, $legacy_user_avatar_name )!== false )
							$avatar_url = $avatar_folder_url . '/' . $avatar_files[$key];
					}

					// Legacy group avatar check
					if ( !isset( $avatar_url ) ) {
						foreach( $avatar_files as $key => $value ) {
							if ( strpos ( $value, $legacy_group_avatar_name )!== false )
								$avatar_url = $avatar_folder_url . '/' . $avatar_files[$key];
						}
					}
				}
			}
		}

		// Close the avatar directory
		closedir( $av_dir );

		// If we found a locally uploaded avatar
		if ( $avatar_url ) {

			// Return it wrapped in an <img> element
			if ( true === $html ) {
				return apply_filters( 'bp_core_fetch_avatar', '<img src="' . $avatar_url . '" alt="' . $alt . '" class="' . $class . '"' . $css_id . $html_width . $html_height . ' />', $params, $item_id, $avatar_dir, $css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir );

			// ...or only the URL
			} else {
				return apply_filters( 'bp_core_fetch_avatar_url', $avatar_url );
			}
		}
	}

	// If no avatars could be found, try to display a gravatar

	// Skips gravatar check if $no_grav is passed
	if ( !$no_grav ) {

		// Set gravatar size
		if ( $width )
			$grav_size = $width;
		else if ( 'full' == $type )
			$grav_size = BP_AVATAR_FULL_WIDTH;
		else if ( 'thumb' == $type )
			$grav_size = BP_AVATAR_THUMB_WIDTH;

		// Set gravatar type
		if ( empty( $bp->grav_default->{$object} ) )
			$default_grav = 'wavatar';
		else if ( 'mystery' == $bp->grav_default->{$object} )
			$default_grav = apply_filters( 'bp_core_mysteryman_src', BP_AVATAR_DEFAULT, $grav_size );
		else
			$default_grav = $bp->grav_default->{$object};

		// Set gravatar object
		if ( empty( $email ) ) {
			if ( 'event' == $object ) {
				$email = "{$item_id}-{$object}@{$bp->root_domain}";
			}
		}

		// Set host based on if using ssl
		if ( is_ssl() )
			$host = 'https://secure.gravatar.com/avatar/';
		else
			$host = 'http://www.gravatar.com/avatar/';

	// Gravatar's
	
	$_opt = get_site_option('jes_events');			

	if ( $_opt[ 'jes_events_defavatar' ] == null ) { 
		// Filter gravatar vars
		$email		= apply_filters( 'bp_core_gravatar_email', $email, $item_id, $object );
		$gravatar	= apply_filters( 'bp_gravatar_url', $host ) . md5( strtolower( $email ) ) . '?d=' . $default_grav . '&amp;s=' . $grav_size;
		} else {
		$gravatar = $_opt[ 'jes_events_defavatar' ];
		}

		// Return gravatar wrapped in <img />
		if ( true === $html )
			return apply_filters( 'jes_core_fetch_avatar', '<img src="' . $gravatar . '" alt="' . $alt . '" class="' . $class . '"' . $css_id . $html_width . $html_height . ' />', $params, $item_id, $avatar_dir, $css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir );

		// ...or only return the gravatar URL
		else
			return apply_filters( 'jes_core_fetch_avatar_url', $gravatar );

	} else {
		return apply_filters( 'jes_core_fetch_avatar', false, $params, $item_id, $avatar_dir, $css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir );
	}
}
	
	
/* ----------------- */

	
function jes_bp_event_avatar( $args = '' ) {
	echo jes_bp_get_event_avatar( $args );
}
	function jes_bp_get_event_avatar( $args = '' ) {
		global $bp, $events_template;

		$defaults = array(
			'type' => 'full',
			'width' => false,
			'height' => false,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Event avatar', 'jet-event-system' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		/* Fetch the avatar from the folder, if not provide backwards compat. */
		if ( !$avatar = jes_core_fetch_avatar( array( 'item_id' => $events_template->event->id, 'object' => 'event', 'type' => $type, 'avatar_dir' => 'event-avatars', 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height ) ) )
			$avatar = '<img src="' . attribute_escape( $events_template->event->avatar_thumb ) . '" class="avatar" alt="' . attribute_escape( $events_template->event->name ) . '" />';

		return apply_filters( 'jes_bp_get_event_avatar', $avatar );
	}

function jes_bp_event_avatar_thumb() {
	echo jes_bp_get_event_avatar_thumb();
}
	function jes_bp_get_event_avatar_thumb( $event = false ) {
		return jes_bp_get_event_avatar( 'type=thumb' );
	}

function jes_bp_event_avatar_mini() {
	echo jes_bp_get_event_avatar_mini();
}
	function jes_bp_get_event_avatar_mini( $event = false ) {
		return jes_bp_get_event_avatar( 'type=thumb&width=30&height=30' );
	}

function jes_bp_event_avatar_top() {
	echo jes_bp_get_event_avatar_top();
}
	function jes_bp_get_event_avatar_top( $event = false ) {
		return jes_bp_get_event_avatar( 'type=thumb&width=75&height=75' );
	}
	
function jes_bp_event_avatar_main() {
	echo jes_bp_get_event_avatar_main();
}
	function jes_bp_get_event_avatar_main( $event = false ) {
		return jes_bp_get_event_avatar( 'type=full&width=150&height=150' );
	}
		
function jes_bp_event_last_active() {
	echo jes_bp_get_event_last_active();
}
	function jes_bp_get_event_last_active( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		$last_active = $event->last_activity;

		if ( !$last_active )
			$last_active = events_get_eventmeta( $event->id, 'last_activity' );

		if ( empty( $last_active ) ) {
			return __( 'not yet active', 'jet-event-system' );
		} else {
			return apply_filters( 'jes_bp_get_event_last_active', bp_core_time_since( $last_active ) );
		}
	}

function jes_bp_event_permalink() {
	echo jes_bp_get_event_permalink();
}
	function jes_bp_get_event_permalink( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_permalink', $bp->root_domain . '/' . $bp->jes_events->slug . '/' . $event->slug . '/' );
	}

function jes_bp_event_admin_permalink() {
	echo jes_bp_get_event_admin_permalink();
}
	function jes_bp_get_event_admin_permalink( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_admin_permalink', $bp->root_domain . '/' . $bp->jes_events->slug . '/' . $event->slug . '/admin' );
	}

function jes_bp_event_slug() {
	echo jes_bp_get_event_slug();
}
	function jes_bp_get_event_slug( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_slug', $event->slug );
	}

/* Description */
function jes_bp_event_description() {
	echo jes_bp_get_event_description();
}
	function jes_bp_get_event_description( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_description', stripslashes($event->description) );
	}

function jes_bp_event_description_editable() {
	echo jes_bp_get_event_description_editable();
}
	function jes_bp_get_event_description_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_description_editable', $event->description );
	}

function jes_bp_event_description_excerpt() {
	echo jes_bp_get_event_description_excerpt();
}
	function jes_bp_get_event_description_excerpt( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_description_excerpt', bp_create_excerpt( $event->description, 20 ) );
	}


/* Terms */
function jes_bp_event_eventterms() {
	echo jes_bp_get_event_eventterms();
}
	function jes_bp_get_event_eventterms( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_eventterms', stripslashes($event->eventterms) );
	}

function jes_bp_event_eventterms_editable() {
	echo jes_bp_get_event_eventterms_editable();
}
	function jes_bp_get_event_eventterms_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_eventterms_editable', $event->eventterms );
	}

function jes_bp_event_eventterms_excerpt() {
	echo jes_bp_get_event_eventterms_excerpt();
}
	function jes_bp_get_event_eventterms_excerpt( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_eventterms_excerpt', bp_create_excerpt( $event->eventterms, 20 ) );
	}
	
	
/* Placed */
// Country
function jes_bp_event_placedcountry() {
	echo jes_bp_get_event_placedcountry();
}
	function jes_bp_get_event_placedcountry( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedcountry', stripslashes($event->placedcountry) );
	}

function jes_bp_event_placedcountry_editable() {
	echo jes_bp_get_event_placedcountry_editable();
}
	function jes_bp_get_event_placedcountry_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedcountry_editable', $event->placedcountry );
	}

// State
function jes_bp_event_placedstate() {
	echo jes_bp_get_event_placedstate();
}
	function jes_bp_get_event_placedstate( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedstate', stripslashes($event->placedstate) );
	}

function jes_bp_event_placedstate_editable() {
	echo jes_bp_get_event_placedstate_editable();
}
	function jes_bp_get_event_placedstate_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedstate_editable', $event->placedstate );
	}

// Group/Forum Link
function jes_bp_event_grouplink() {
	echo jes_bp_get_event_grouplink();
}
	function jes_bp_get_event_grouplink( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_grouplink', stripslashes($event->grouplink) );
	}	
	
function jes_bp_event_forumlink() {
	echo jes_bp_get_event_forumlink();
}
	function jes_bp_get_event_forumlink( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_forumlink', stripslashes($event->forumlink) );
	}	

function jes_bp_event_enablesocial() {
	echo jes_bp_get_event_enablesocial();
}
	function jes_bp_get_event_enablesocial( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_enablesocial', stripslashes($event->enablesocial) );
	}	
		
// City
function jes_bp_event_placedcity() {
	echo jes_bp_get_event_placedcity();
}
	function jes_bp_get_event_placedcity( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedcity', stripslashes($event->placedcity) );
	}

function jes_bp_event_placedcity_editable() {
	echo jes_bp_get_event_placedcity_editable();
}
	function jes_bp_get_event_placedcity_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedcity_editable', $event->placedcity );
	}

function jes_bp_event_placedcity_excerpt() {
	echo jes_bp_get_event_placedcity_excerpt();
}
	function jes_bp_get_event_placedcity_excerpt( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedcity_excerpt', bp_create_excerpt( $event->placedcity, 30 ) );
	}

function jes_bp_event_placedaddress() {
	echo jes_bp_get_event_placedaddress();
}
	function jes_bp_get_event_placedaddress( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedaddress', stripslashes($event->placedaddress) );
	}

function jes_bp_event_placednote() {
	echo jes_bp_get_event_placednote();
}
	function jes_bp_get_event_placednote( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placednote', stripslashes($event->placednote) );
	}

function jes_bp_event_placedgooglemap() {
	echo jes_bp_get_event_placedgooglemap();
}
	function jes_bp_get_event_placedgooglemap( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedgooglemap', stripslashes($event->placedgooglemap) );
	}

function jes_bp_event_flyer() {
	echo jes_bp_get_event_flyer();
}
	function jes_bp_get_event_flyer( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_flyer', stripslashes($event->flyer) );
	}



function jes_bp_event_placedaddress_editable() {
	echo jes_bp_get_event_placedaddress_editable();
}
	function jes_bp_get_event_placedaddress_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedaddress_editable', $event->placedaddress );
	}

function jes_bp_event_placedaddress_excerpt() {
	echo jes_bp_get_event_placedaddress_excerpt();
}
	function jes_bp_get_event_placedassress_excerpt( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placedaddress_excerpt', bp_create_excerpt( $event->placedaddress, 30 ) );
	}

function jes_bp_event_placednote_editable() {
	echo jes_bp_get_event_placednote_editable();
}
	function jes_bp_get_event_placednote_editable( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placednote_editable', $event->placednote );
	}

function jes_bp_event_placednote_excerpt() {
	echo jes_bp_get_event_placednote_excerpt();
}
	function jes_bp_get_event_placednote_excerpt( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_placednote_excerpt', bp_create_excerpt( $event->placednote, 30 ) );
	}


/* ********* */	
	
	
function jes_bp_event_etype() {
	echo jes_bp_get_event_etype();
}
	function jes_bp_get_event_etype( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_etype', stripslashes($event->etype) );
	}	

function jes_bp_event_eventapproved() {
	echo jes_bp_get_event_eventapproved();
}
	function jes_bp_get_event_eventapproved( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_eventapproved', stripslashes($event->eventapproved) );
	}	
	
function jes_bp_event_edtsd() {
	echo jes_bp_get_event_edtsd();
}
	function jes_bp_get_event_edtsd( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_edtsd', stripslashes($event->edtsd) );
	}
	
function jes_bp_event_edted() {
	echo jes_bp_get_event_edted();
}
	function jes_bp_get_event_edted( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_edted', stripslashes($event->edted) );
	}

function jes_bp_event_edtsth() {
	echo jes_bp_get_event_edtsth();
}
	function jes_bp_get_event_edtsth( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_edtsth', stripslashes($event->edtsth) );
	}
	
function jes_bp_event_edteth() {
	echo jes_bp_get_event_edteth();
}
	function jes_bp_get_event_edteth( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_edteth', stripslashes($event->edteth) );
	}	
	
function jes_bp_event_edtstm() {
	echo jes_bp_get_event_edtstm();
}
	function jes_bp_get_event_edtstm( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_edtstm', stripslashes($event->edtstm) );
	}
	
function jes_bp_event_edtetm() {
	echo jes_bp_get_event_edtetm();
}
	function jes_bp_get_event_edtetm( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_edtetm', stripslashes($event->edtetm) );
	}	

function jes_bp_event_newspublic() {
	echo jes_bp_get_event_newspublic();
}
	function jes_bp_get_event_newspublic( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_newspublic', stripslashes($event->newspublic) );
	}
	
function jes_bp_event_newsprivate() {
	echo jes_bp_get_event_newsprivate();
}
	function jes_bp_get_event_newsprivate( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_newsprivte', stripslashes($event->newsprivate) );
	}
		
	
function jes_bp_event_public_status() {
	echo jes_bp_get_event_public_status();
}
	function jes_bp_get_event_public_status( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		if ( $event->is_public ) {
			return __( 'Public', 'jet-event-system' );
		} else {
			return __( 'Private', 'jet-event-system' );
		}
	}

function jes_bp_event_is_public() {
	echo jes_bp_get_event_is_public();
}
	function jes_bp_get_event_is_public( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_is_public', $event->is_public );
	}

function jes_bp_event_date_created() {
	echo jes_bp_get_event_date_created();
}
	function jes_bp_get_event_date_created( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_date_created', bp_core_time_since( strtotime( $event->date_created ) ) );
	}

function jes_bp_event_is_admin() {
	global $bp;

	return $bp->is_item_admin;
}

function jes_bp_event_is_mod() {
	global $bp;

	return $bp->is_item_mod;
}

function jes_bp_event_list_admins( $event = false ) {
	global $events_template;

	if ( !$event )
		$event =& $events_template->event;

	if ( $event->admins ) { ?>
		<ul id="event-admins">
			<?php foreach( (array)$event->admins as $admin ) { ?>
				<li>
					<a href="<?php echo bp_core_get_user_domain( $admin->user_id, $admin->user_nicename, $admin->user_login ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'email' => $admin->user_email ) ) ?></a>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<span class="activity"><?php _e( 'No Admins', 'jet-event-system' ) ?></span>
	<?php } ?>
<?php
}

function jes_bp_event_list_mods( $event = false ) {
	global $events_template;

	if ( !$event )
		$event =& $events_template->event;

	if ( $event->mods ) { ?>
		<ul id="event-mods">
			<?php foreach( (array)$event->mods as $mod ) { ?>
				<li>
					<a href="<?php echo bp_core_get_user_domain( $mod->user_id, $mod->user_nicename, $mod->user_login ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'email' => $mod->user_email ) ) ?></a>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<span class="activity"><?php _e( 'No Mods', 'jet-event-system' ) ?></span>
	<?php } ?>
<?php
}

function jes_bp_event_all_members_permalink() {
	echo jes_bp_get_event_all_members_permalink();
}
	function jes_bp_get_event_all_members_permalink( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_all_members_permalink', jes_bp_get_event_permalink( $event ) . 'members' );
	}

	
function jes_bp_event_search_form() {
	global $events_template, $bp;

	$action = $bp->displayed_user->domain . $bp->jes_events->slug . '/my-events/search/';
	$label = __('Filter Events', 'jet-event-system');
	$name = 'event-filter-box';

?>
	<form action="<?php echo $action ?>" id="event-search-form" method="post">
		<label for="<?php echo $name ?>" id="<?php echo $name ?>-label"><?php echo $label ?></label>
		<input type="search" name="<?php echo $name ?>" id="<?php echo $name ?>" value="<?php echo $value ?>"<?php echo $disabled ?> />

		<?php wp_nonce_field( 'event-filter-box', '_wpnonce_event_filter' ) ?>
	</form>
<?php
}

function jes_bp_event_show_no_events_message() {
	global $bp;

	if ( !events_total_events_for_user( $bp->displayed_user->id ) )
		return true;

	return false;
}

function jes_bp_event_is_activity_permalink() {
	global $bp;

	if ( !$bp->is_single_item || $bp->current_component != $bp->jes_events->slug || $bp->current_action != $bp->activity->slug )
		return false;

	return true;
}

function jes_bp_events_pagination_links() {
	echo jes_bp_get_events_pagination_links();
}
	function jes_bp_get_events_pagination_links() {
		global $events_template;

		return apply_filters( 'jes_bp_get_events_pagination_links', $events_template->pag_links );
	}

function jes_bp_events_pagination_count() {
	global $bp, $events_template;

	$start_num = intval( ( $events_template->pag_page - 1 ) * $events_template->pag_num ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $events_template->pag_num - 1 ) > $events_template->jes_total_event_count ) ? $events_template->jes_total_event_count : $start_num + ( $events_template->pag_num - 1 ) );
	$total = bp_core_number_format( $events_template->jes_total_event_count );

	echo sprintf( __( 'Viewing event %1$s to %2$s (of %3$s events)', 'jet-event-system' ), $from_num, $to_num, $total ); ?> &nbsp;
	<span class="ajax-loader"></span><?php
}

function jes_bp_event_total_members() {
	echo jes_bp_get_event_total_members();
}
	function jes_bp_get_event_total_members( $event = false ) {
		global $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'jes_bp_get_event_total_members', $event->total_member_count );
	}

function jes_bp_event_member_count() {
	echo jes_bp_get_event_member_count();
}
	function jes_bp_get_event_member_count() {
		global $events_template;

		if ( 1 == (int) $events_template->event->total_member_count )
			return apply_filters( 'jes_bp_get_event_member_count', sprintf( __( '%s member', 'jet-event-system' ), bp_core_number_format( $events_template->event->total_member_count ) ) );
		else
			return apply_filters( 'jes_bp_get_event_member_count', sprintf( __( '%s members', 'jet-event-system' ), bp_core_number_format( $events_template->event->total_member_count ) ) );
	}

function jes_bp_event_member_count_dec() {
	echo jes_bp_get_event_member_count_dec();
}
	function jes_bp_get_event_member_count_dec() {
		global $events_template;

		if ( 1 == (int) $events_template->event->total_member_count )
			return apply_filters( 'jes_bp_get_event_member_count_dec', sprintf( __( '(%s)', 'jet-event-system' ), bp_core_number_format( $events_template->event->total_member_count ) ) );
		else
			return apply_filters( 'jes_bp_get_event_member_count_dec', sprintf( __( '(%s)', 'jet-event-system' ), bp_core_number_format( $events_template->event->total_member_count ) ) );
	}
	
	

function jet_bp_event_show_status_setting( $setting, $event = false ) {
	global $events_template;

	if ( !$event )
		$event =& $events_template->event;

	if ( $setting == $event->status )
		echo ' checked="checked"';
}


function jet_bp_event_grouplink_setting( $setting, $event = false ) {
	global $events_template;

	if ( !$event )
		$event =& $events_template->event;

	if ( $setting == $event->grouplink )
		echo ' selected="selected"';
}


function jes_bp_event_admin_memberlist( $admin_list = false, $event = false ) {
	global $events_template;

	if ( !$event )
		$event =& $events_template->event;

	$admins = events_get_event_admins( $event->id );
?>
	<?php if ( $admins ) { ?>
		<ul id="admins-list" class="item-list<?php if ( $admin_list ) { ?> single-line<?php } ?>">
		<?php foreach ( (array)$admins as $admin ) { ?>
			<?php if ( $admin_list ) { ?>
			<li>
				<?php echo bp_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'type' => 'thumb', 'width' => 30, 'height' => 30 ) ) ?>
				<h5><?php echo bp_core_get_userlink( $admin->user_id ) ?>  <span class="small"> &mdash; <a class="confirm" href="<?php bp_event_member_demote_link($admin->user_id) ?>"><?php _e( 'Demote to Member', 'jet-event-system' ) ?></a></span></h5>
			</li>
			<?php } else { ?>
			<li>
				<?php echo bp_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'type' => 'thumb' ) ) ?>
				<h5><?php echo bp_core_get_userlink( $admin->user_id ) ?></h5>
				<span class="activity"><?php echo bp_core_get_last_activity( strtotime( $admin->date_modified ), __( 'joined %s ago', 'jet-event-system') ); ?></span>

				<?php if ( function_exists( 'friends_install' ) ) : ?>
					<div class="action" id="jes-add-friend">
						<?php bp_add_friend_button( $admin->user_id ) ?>
					</div>
				<?php endif; ?>
			</li>
			<?php } ?>
		<?php } ?>
		</ul>
	<?php } else { ?>
		<div id="message" class="info">
			<p><?php _e( 'This event has no administrators', 'jet-event-system' ); ?></p>
		</div>
	<?php }
}

function jes_bp_event_mod_memberlist( $admin_list = false, $event = false ) {
	global $events_template, $event_mods;

	if ( !$event )
		$event =& $events_template->event;

	$event_mods = events_get_event_mods( $event->id );
	?>
		<?php if ( $event_mods ) { ?>
			<ul id="mods-list" class="item-list<?php if ( $admin_list ) { ?> single-line<?php } ?>">
			<?php foreach ( (array)$event_mods as $mod ) { ?>
				<?php if ( $admin_list ) { ?>
				<li>
					<?php echo bp_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'type' => 'thumb', 'width' => 30, 'height' => 30 ) ) ?>
					<h5><?php echo bp_core_get_userlink( $mod->user_id ) ?>  <span class="small"> &mdash; <a href="<?php bp_event_member_promote_admin_link( array( 'user_id' => $mod->user_id ) ) ?>" class="confirm" title="<?php _e( 'Promote to Admin', 'jet-event-system' ); ?>"><?php _e( 'Promote to Admin', 'jet-event-system' ); ?></a> | <a class="confirm" href="<?php bp_event_member_demote_link($mod->user_id) ?>"><?php _e( 'Demote to Member', 'jet-event-system' ) ?></a></span></h5>
				</li>
				<?php } else { ?>
				<li>
					<?php echo bp_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'type' => 'thumb' ) ) ?>
					<h5><?php echo bp_core_get_userlink( $mod->user_id ) ?></h5>
					<span class="activity"><?php echo bp_core_get_last_activity( strtotime( $mod->date_modified ), __( 'joined %s ago', 'jet-event-system') ); ?></span>

					<?php if ( function_exists( 'friends_install' ) ) : ?>
						<div class="action" id="jes-add-friend">
							<?php bp_add_friend_button( $mod->user_id ) ?>
						</div>
					<?php endif; ?>
				</li>
				<?php } ?>
			<?php } ?>
			</ul>
		<?php } else { ?>
			<div id="message" class="info">
				<p><?php _e( 'This event has no moderators', 'jet-event-system' ); ?></p>
			</div>
		<?php }
}

function jes_bp_event_has_moderators( $event = false ) {
	global $event_mods, $events_template;

	if ( !$event )
		$event =& $events_template->event;

	return apply_filters( 'jes_bp_event_has_moderators', events_get_event_mods( $event->id ) );
}

function bp_event_member_promote_mod_link( $args = '' ) {
	echo bp_get_event_member_promote_mod_link( $args );
}
	function bp_get_event_member_promote_mod_link( $args = '' ) {
		global $members_template, $events_template, $bp;

		$defaults = array(
			'user_id' => $members_template->member->user_id,
			'event' => &$events_template->event
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'bp_get_event_member_promote_mod_link', wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'admin/manage-members/promote/mod/' . $user_id, 'events_promote_member' ) );
	}

function bp_event_member_promote_admin_link( $args = '' ) {
	echo bp_get_event_member_promote_admin_link( $args );
}
	function bp_get_event_member_promote_admin_link( $args = '' ) {
		global $members_template, $events_template, $bp;

		$defaults = array(
			'user_id' => $members_template->member->user_id,
			'event' => &$events_template->event
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'bp_get_event_member_promote_admin_link', wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'admin/manage-members/promote/admin/' . $user_id, 'events_promote_member' ) );
	}

function bp_event_member_demote_link( $user_id = false ) {
	global $members_template;

	if ( !$user_id )
		$user_id = $members_template->member->user_id;

	echo bp_get_event_member_demote_link( $user_id );
}
	function bp_get_event_member_demote_link( $user_id = false, $event = false ) {
		global $members_template, $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		if ( !$user_id )
			$user_id = $members_template->member->user_id;

		return apply_filters( 'bp_get_event_member_demote_link', wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'admin/manage-members/demote/' . $user_id, 'events_demote_member' ) );
	}

function bp_event_member_ban_link( $user_id = false ) {
	global $members_template;

	if ( !$user_id )
		$user_id = $members_template->member->user_id;

	echo bp_get_event_member_ban_link( $user_id );
}
	function bp_get_event_member_ban_link( $user_id = false, $event = false ) {
		global $members_template, $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_member_ban_link', wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'admin/manage-members/ban/' . $user_id, 'events_ban_member' ) );
	}

function bp_event_member_unban_link( $user_id = false ) {
	global $members_template;

	if ( !$user_id )
		$user_id = $members_template->member->user_id;

	echo bp_get_event_member_unban_link( $user_id );
}
	function bp_get_event_member_unban_link( $user_id = false, $event = false ) {
		global $members_template;

		if ( !$user_id )
			$user_id = $members_template->member->user_id;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_member_unban_link', wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'admin/manage-members/unban/' . $user_id, 'events_unban_member' ) );
	}

function bp_event_admin_tabs( $event = false ) {
	global $bp, $events_template;

	if ( !$event )
		$event = ( $events_template->event ) ? $events_template->event : $bp->jes_events->current_event;

	$current_tab = $bp->action_variables[0];
?>
	<?php if ( $bp->is_item_admin || $bp->is_item_mod ) { ?>
		<li<?php if ( 'edit-details' == $current_tab || empty( $current_tab ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/<?php echo $event->slug ?>/admin/edit-details"><?php _e('Edit Details', 'jet-event-system') ?></a></li>
	<?php } ?>

	<?php
		if ( !$bp->is_item_admin )
			return false;
	?>
	<li<?php if ( 'event-settings' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/<?php echo $event->slug ?>/admin/event-settings"><?php _e('Event Settings', 'jet-event-system') ?></a></li>
	<li<?php if ( 'event-avatar' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/<?php echo $event->slug ?>/admin/event-avatar"><?php _e('Event Avatar', 'jet-event-system') ?></a></li>
	<li<?php if ( 'manage-members' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/<?php echo $event->slug ?>/admin/manage-members"><?php _e('Manage Members', 'jet-event-system') ?></a></li>

	<?php if ( $events_template->event->status == 'private' ) : ?>
		<li<?php if ( 'membership-requests' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/<?php echo $event->slug ?>/admin/membership-requests"><?php _e('Membership Requests', 'jet-event-system') ?></a></li>
	<?php endif; ?>

	<?php do_action( 'events_admin_tabs', $current_tab, $event->slug ) ?>

	<li<?php if ( 'delete-event' == $current_tab ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/<?php echo $event->slug ?>/admin/delete-event"><?php _e('Delete Event', 'jet-event-system') ?></a></li>
<?php
}

function bp_event_total_for_member() {
	echo bp_get_event_total_for_member();
}
	function bp_get_event_total_for_member() {
		return apply_filters( 'bp_get_event_total_for_member', JES_Events_Member::jes_total_event_count() );
	}

function bp_event_form_action( $page ) {
	echo bp_get_event_form_action( $page );
}
	function bp_get_event_form_action( $page, $event = false ) {
		global $bp, $events_template;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_event_form_action', jes_bp_get_event_permalink( $event ) . $page );
	}

function bp_event_admin_form_action( $page = false ) {
	echo bp_get_event_admin_form_action( $page );
}
	function bp_get_event_admin_form_action( $page = false, $event = false ) {
		global $bp, $events_template;

		if ( !$event )
			$event =& $events_template->event;

		if ( !$page )
			$page = $bp->action_variables[0];

		return apply_filters( 'bp_event_admin_form_action', jes_bp_get_event_permalink( $event ) . 'admin/' . $page );
	}

function bp_event_has_requested_membership( $event = false ) {
	global $bp, $events_template;

	if ( !$event )
		$event =& $events_template->event;

	if ( events_jes_check_for_membership_request( $bp->loggedin_user->id, $event->id ) )
		return true;

	return false;
}

/**
 * bp_event_is_member()
 *
 * Checks if current user is member of a event.
 *
 * @uses is_site_admin Check if current user is super admin
 * @uses apply_filters Creates bp_event_is_member filter and passes $is_member
 * @usedby events/activity.php, events/single/forum/edit.php, events/single/forum/topic.php to determine template part visibility
 * @global array $bp BuddyPress Master global
 * @global object $events_template Current Event (usually in template loop)
 * @param object $event Event to check is_member
 * @return bool If user is member of event or not
 */
function bp_event_is_member( $event = false ) {
	global $bp, $events_template;

	// Site admins always have access
	if ( is_site_admin() )
		return true;

	// Load event if none passed
	if ( !$event )
		$event =& $events_template->event;

	// Check membership
	if ( null == $event->is_member )
		$is_member = false;
	else
		$is_member = true;

	// Return
	return apply_filters( 'bp_event_is_member', $is_member );
}

function bp_event_jes_accept_invite_link() {
	echo bp_get_event_jes_accept_invite_link();
}
	function bp_get_event_jes_accept_invite_link( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_jes_accept_invite_link', wp_nonce_url( $bp->loggedin_user->domain . $bp->jes_events->slug . '/invites/accept/' . $event->id, 'events_jes_accept_invite' ) );
	}

function bp_event_reject_invite_link() {
	echo bp_get_event_reject_invite_link();
}
	function bp_get_event_reject_invite_link( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_reject_invite_link', wp_nonce_url( $bp->loggedin_user->domain . $bp->jes_events->slug . '/invites/reject/' . $event->id, 'events_reject_invite' ) );
	}

function bp_event_leave_confirm_link() {
	echo bp_get_event_leave_confirm_link();
}
	function bp_get_event_leave_confirm_link( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_event_leave_confirm_link', wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'leave-event/yes', 'events_leave_event' ) );
	}

function bp_event_leave_reject_link() {
	echo bp_get_event_leave_reject_link();
}
	function bp_get_event_leave_reject_link( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_get_event_leave_reject_link', jes_bp_get_event_permalink( $event ) );
	}

function bp_event_send_invite_form_action() {
	echo bp_get_event_send_invite_form_action();
}
	function bp_get_event_send_invite_form_action( $event = false ) {
		global $events_template, $bp;

		if ( !$event )
			$event =& $events_template->event;

		return apply_filters( 'bp_event_send_invite_form_action', jes_bp_get_event_permalink( $event ) . 'send-invites/send' );
	}

function bp_event_friends_to_invite( $event = false ) {
	global $events_template, $bp;

	if ( !function_exists('friends_install') )
		return false;

	if ( !$event )
		$event =& $events_template->event;

	if ( !friends_check_user_has_friends( $bp->loggedin_user->id ) || !friends_count_invitable_friends( $bp->loggedin_user->id, $event->id ) )
		return false;

	return true;
}

function bp_event_join_button( $event = false ) {
	global $bp, $events_template;

	if ( !$event )
		$event =& $events_template->event;

	// If they're not logged in or are banned from the event, no join button.
	if ( !is_user_logged_in() || $event->is_banned )
		return false;

	if ( !$event->status )
		return false;

	if ( 'hidden' == $event->status && !$event->is_member )
		return false;

// JLL_MOD - added <div class="event-button-div"></div>
	echo '<div id="event-button-div"><div class="generic-button event-button-fix ' . $event->status . '" id="eventbutton-' . $event->id . '">';

	switch ( $event->status ) {
		case 'public':
			if ( $event->is_member ) {
			// if (!jes_bp_event_is_admin()) {
				echo '<a class="leave-event" href="' . wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'leave-event', 'events_leave_event' ) . '">' . __( 'Leave Event', 'jet-event-system' ) . '</a>'; //}
			} 
			else
				echo '<a class="join-event" href="' . wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'join', 'events_join_event' ) . '">' . __( 'Join Event', 'jet-event-system' ) . '</a>';
		break;

		case 'private':
			if ( $event->is_member ) {
				echo '<a class="leave-event" href="' . wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'leave-event', 'events_leave_event' ) . '">' . __( 'Leave Event', 'jet-event-system' ) . '</a>';
			} else {
				if ( !bp_event_has_requested_membership( $event ) )
					echo '<a class="request-join-to-event" href="' . wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'request-join-to-event', 'events_request_join_to_event' ) . '">' . __('Request to join Event', 'jet-event-system') . '</a>';
				else
					echo '<a class="membership-requested" href="' . jes_bp_get_event_permalink( $event ) . '">' . __( 'Request Sent', 'jet-event-system' ) . '</a>';
			}
		break;

		case 'hidden':
			if ( $event->is_member )
				echo '<a class="leave-event" href="' . wp_nonce_url( jes_bp_get_event_permalink( $event ) . 'leave-event', 'events_leave_event' ) . '">' . __( 'Leave Event', 'jet-event-system' ) . '</a>';
		break;
	}

	echo '</div></div>';
}

function jes_bp_event_status_message( $event = false ) {
	global $events_template;

	if ( !$event )
		$event =& $events_template->event;

	if ( 'private' == $event->status ) {
		if ( !bp_event_has_requested_membership() )
			if ( is_user_logged_in() )
				_e( 'This is a private event and you must request event membership in order to join.', 'jet-event-system' );
			else
				_e( 'This is a private event. To join you must be a registered site member and request event membership.', 'jet-event-system' );
		else
			_e( 'This is a private event. Your membership request is awaiting approval from the event administrator.', 'jet-event-system' );
	} else {
		_e( 'This is a hidden event and only invited members can join.', 'jet-event-system' );
	}
}

function bp_event_hidden_fields() {
	if ( isset( $_REQUEST['s'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . attribute_escape( $_REQUEST['s'] ) . '" name="search_terms" />';
	}

	if ( isset( $_REQUEST['letter'] ) ) {
		echo '<input type="hidden" id="selected_letter" value="' . attribute_escape( $_REQUEST['letter'] ) . '" name="selected_letter" />';
	}

	if ( isset( $_REQUEST['events_search'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . attribute_escape( $_REQUEST['events_search'] ) . '" name="search_terms" />';
	}
}

function bp_jes_total_event_count() {
	echo bp_jes_get_jes_total_event_count();
}
	function bp_jes_get_jes_total_event_count() {
		return apply_filters( 'bp_jes_get_jes_total_event_count', events_jes_get_jes_total_event_count() );
	}

function bp_jes_total_event_count_for_user( $user_id = false ) {
	echo bp_jes_get_jes_total_event_count_for_user( $user_id );
}
	function bp_jes_get_jes_total_event_count_for_user( $user_id = false ) {
		return apply_filters( 'bp_jes_get_jes_total_event_count_for_user', events_total_events_for_user( $user_id ) );
	}


/***************************************************************************
 * Event Members Template Tags
 **/

class JES_Events_Event_Members_Template {
	var $current_member = -1;
	var $member_count;
	var $members;
	var $member;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $jes_total_event_count;

	function jes_events_event_members_template( $event_id, $per_page, $max, $exclude_admins_mods, $exclude_banned ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['mlpage'] ) ? intval( $_REQUEST['mlpage'] ) : 1;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		$this->members = JES_Events_Member::jes_get_all_for_event( $event_id, $this->pag_num, $this->pag_page, $exclude_admins_mods, $exclude_banned );

		if ( !$max || $max >= (int)$this->members['count'] )
			$this->total_member_count = (int)$this->members['count'];
		else
			$this->total_member_count = (int)$max;

		$this->members = $this->members['members'];

		if ( $max ) {
			if ( $max >= count($this->members) )
				$this->member_count = count($this->members);
			else
				$this->member_count = (int)$max;
		} else {
			$this->member_count = count($this->members);
		}

		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( 'mlpage', '%#%' ),
			'format' => '',
			'total' => ceil( $this->total_member_count / $this->pag_num ),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
	}

	function jes_has_members() {
		if ( $this->member_count )
			return true;

		return false;
	}

	function next_member() {
		$this->current_member++;
		$this->member = $this->members[$this->current_member];

		return $this->member;
	}

	function rewind_members() {
		$this->current_member = -1;
		if ( $this->member_count > 0 ) {
			$this->member = $this->members[0];
		}
	}

	function members() {
		if ( $this->current_member + 1 < $this->member_count ) {
			return true;
		} elseif ( $this->current_member + 1 == $this->member_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_members();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_member() {
		global $member;

		$this->in_the_loop = true;
		$this->member = $this->next_member();

		if ( 0 == $this->current_member ) // loop has just started
			do_action('loop_start');
	}
}

function bp_event_jes_has_members( $args = '' ) {
	global $bp, $members_template;

	$defaults = array(
		'event_id' => $bp->jes_events->current_event->id,
		'per_page' => 20,
		'max' => false,
		'exclude_admins_mods' => 1,
		'exclude_banned' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$members_template = new JES_Events_Event_Members_Template( $event_id, $per_page, $max, (int)$exclude_admins_mods, (int)$exclude_banned );
	return apply_filters( 'bp_event_jes_has_members', $members_template->jes_has_members(), &$members_template );
}

function bp_event_members() {
	global $members_template;

	return $members_template->members();
}

function bp_event_the_member() {
	global $members_template;

	return $members_template->the_member();
}

function bp_event_member_avatar() {
	echo bp_get_event_member_avatar();
}
	function bp_get_event_member_avatar() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_avatar', bp_core_fetch_avatar( array( 'item_id' => $members_template->member->user_id, 'type' => 'full', 'email' => $members_template->member->user_email ) ) );
	}

function bp_event_member_avatar_thumb() {
	echo bp_get_event_member_avatar_thumb();
}
	function bp_get_event_member_avatar_thumb() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_avatar_thumb', bp_core_fetch_avatar( array( 'item_id' => $members_template->member->user_id, 'type' => 'thumb', 'email' => $members_template->member->user_email ) ) );
	}

function bp_event_member_avatar_mini( $width = 30, $height = 30 ) {
	echo bp_get_event_member_avatar_mini( $width, $height );
}
	function bp_get_event_member_avatar_mini( $width = 30, $height = 30 ) {
		global $members_template;

		return apply_filters( 'bp_get_event_member_avatar_mini', bp_core_fetch_avatar( array( 'item_id' => $members_template->member->user_id, 'type' => 'thumb', 'width' => $width, 'height' => $height, 'email' => $members_template->member->user_email ) ) );
	}

function bp_event_member_name() {
	echo bp_get_event_member_name();
}
	function bp_get_event_member_name() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_name', $members_template->member->display_name );
	}

function bp_event_member_url() {
	echo bp_get_event_member_url();
}
	function bp_get_event_member_url() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_url', bp_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) );
	}

function bp_event_member_link() {
	echo bp_get_event_member_link();
}
	function bp_get_event_member_link() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_link', '<a href="' . bp_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) . '">' . $members_template->member->display_name . '</a>' );
	}

function bp_event_member_domain() {
	echo bp_get_event_member_domain();
}
	function bp_get_event_member_domain() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_domain', bp_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) );
	}

function bp_event_member_is_friend() {
	echo bp_get_event_member_is_friend();
}
	function bp_get_event_member_is_friend() {
		global $members_template;

		if ( null === $members_template->member->is_friend )
			$friend_status = 'not_friends';
		else
			$friend_status = ( 0 == $members_template->member->is_friend ) ? 'pending' : 'is_friend';

		return apply_filters( 'bp_get_event_member_is_friend', $friend_status );
	}

function bp_event_member_is_banned() {
	echo bp_get_event_member_is_banned();
}
	function bp_get_event_member_is_banned() {
		global $members_template, $events_template;

		return apply_filters( 'bp_get_event_member_is_banned', $members_template->member->is_banned );
	}

function bp_event_member_joined_since() {
	echo bp_get_event_member_joined_since();
}
	function bp_get_event_member_joined_since() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_joined_since', bp_core_get_last_activity( $members_template->member->date_modified, __( 'joined %s ago', 'jet-event-system') ) );
	}

function bp_event_member_id() {
	echo bp_get_event_member_id();
}
	function bp_get_event_member_id() {
		global $members_template;

		return apply_filters( 'bp_get_event_member_id', $members_template->member->user_id );
	}

function bp_event_member_needs_pagination() {
	global $members_template;

	if ( $members_template->total_member_count > $members_template->pag_num )
		return true;

	return false;
}

function bp_event_pag_id() {
	echo bp_get_event_pag_id();
}
	function bp_get_event_pag_id() {
		global $bp;

		return apply_filters( 'bp_get_event_pag_id', 'pag' );
	}

function bp_event_member_pagination() {
	echo bp_get_event_member_pagination();
	wp_nonce_field( 'jes_events_member_list', '_member_pag_nonce' );
}
	function bp_get_event_member_pagination() {
		global $members_template;
		return apply_filters( 'bp_get_event_member_pagination', $members_template->pag_links );
	}

function bp_event_member_pagination_count() {
	echo bp_get_event_member_pagination_count();
}
	function bp_get_event_member_pagination_count() {
		global $members_template;

		$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
		$from_num = bp_core_number_format( $start_num );
		$to_num = bp_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
		$total = bp_core_number_format( $members_template->total_member_count );

		return apply_filters( 'bp_get_event_member_pagination_count', sprintf( __( 'Viewing members %1$s to %2$s (of %3$s members)', 'jet-event-system' ), $from_num, $to_num, $total ) );
	}

function bp_event_member_admin_pagination() {
	echo bp_get_event_member_admin_pagination();
	wp_nonce_field( 'jes_events_member_admin_list', '_member_admin_pag_nonce' );
}
	function bp_get_event_member_admin_pagination() {
		global $members_template;

		return $members_template->pag_links;
	}


/***************************************************************************
 * Event Creation Process Template Tags
 **/

function bp_event_creation_tabs() {
	global $bp;

	if ( !is_array( $bp->jes_events->event_creation_steps ) )
		return false;

	if ( !$bp->jes_events->current_create_step )
		$bp->jes_events->current_create_step = array_shift( array_keys( $bp->jes_events->event_creation_steps ) );

	$counter = 1;

	foreach ( (array)$bp->jes_events->event_creation_steps as $slug => $step ) {
		$is_enabled = bp_are_previous_event_creation_steps_complete( $slug ); ?>

		<li<?php if ( $bp->jes_events->current_create_step == $slug ) : ?> class="current"<?php endif; ?>><?php if ( $is_enabled ) : ?><a href="<?php echo $bp->root_domain . '/' . $bp->jes_events->slug ?>/create/step/<?php echo $slug ?>/"><?php else: ?><span><?php endif; ?><?php echo $counter ?>. <?php echo $step['name'] ?><?php if ( $is_enabled ) : ?></a><?php else: ?></span><?php endif ?></li><?php
		$counter++;
	}

	unset( $is_enabled );

	do_action( 'events_creation_tabs' );
}

function bp_event_creation_stage_title() {
	global $bp;

	echo apply_filters( 'bp_event_creation_stage_title', '<span>&mdash; ' . $bp->jes_events->event_creation_steps[$bp->jes_events->current_create_step]['name'] . '</span>' );
}

function bp_event_creation_form_action() {
	echo bp_get_event_creation_form_action();
}
	function bp_get_event_creation_form_action() {
		global $bp;

		if ( empty( $bp->action_variables[1] ) )
			$bp->action_variables[1] = array_shift( array_keys( $bp->jes_events->event_creation_steps ) );

		return apply_filters( 'bp_get_event_creation_form_action', $bp->root_domain . '/' . $bp->jes_events->slug . '/create/step/' . $bp->action_variables[1] );
	}

function jes_is_event_creation_step( $step_slug ) {
	global $bp;

	/* Make sure we are in the events component */
	if ( $bp->current_component != JES_SLUG || 'create' != $bp->current_action )
		return false;

	/* If this the first step, we can just accept and return true */
	if ( !$bp->action_variables[1] && array_shift( array_keys( $bp->jes_events->event_creation_steps ) ) == $step_slug )
		return true;

	/* Before allowing a user to see a event creation step we must make sure previous steps are completed */
	if ( !bp_is_first_event_creation_step() ) {
		if ( !bp_are_previous_event_creation_steps_complete( $step_slug ) )
			return false;
	}

	/* Check the current step against the step parameter */
	if ( $bp->action_variables[1] == $step_slug )
		return true;

	return false;
}

function bp_is_event_creation_step_complete( $step_slugs ) {
	global $bp;

	if ( !$bp->jes_events->completed_create_steps )
		return false;

	if ( is_array( $step_slugs ) ) {
		$found = true;

		foreach ( (array)$step_slugs as $step_slug ) {
			if ( !in_array( $step_slug, $bp->jes_events->completed_create_steps ) )
				$found = false;
		}

		return $found;
	} else {
		return in_array( $step_slugs, $bp->jes_events->completed_create_steps );
	}

	return true;
}

function bp_are_previous_event_creation_steps_complete( $step_slug ) {
	global $bp;

	/* If this is the first event creation step, return true */
	if ( array_shift( array_keys( $bp->jes_events->event_creation_steps ) ) == $step_slug )
		return true;

	reset( $bp->jes_events->event_creation_steps );
	unset( $previous_steps );

	/* Get previous steps */
	foreach ( (array)$bp->jes_events->event_creation_steps as $slug => $name ) {
		if ( $slug == $step_slug )
			break;

		$previous_steps[] = $slug;
	}

	return bp_is_event_creation_step_complete( $previous_steps );
}

function bp_new_event_id() {
	echo bp_get_new_event_id();
}
	function bp_get_new_event_id() {
		global $bp;
		return apply_filters( 'bp_get_new_event_id', $bp->jes_events->new_event_id );
	}

function bp_new_event_name() {
	echo bp_get_new_event_name();
}
	function bp_get_new_event_name() {
		global $bp;
		return apply_filters( 'bp_get_new_event_name', $bp->jes_events->current_event->name );
	}

function bp_new_event_etype() {
	echo bp_get_new_event_etype();
}
	function bp_get_new_event_etype() {
		global $bp;
		return apply_filters( 'bp_get_new_event_etype', $bp->jes_events->current_event->etype );
	}
	
function bp_new_event_eventapproved() {
	echo bp_get_new_event_eventapproved();
}
	function bp_get_new_event_eventapproved() {
		global $bp;
		return apply_filters( 'bp_get_new_event_eventapproved', $bp->jes_events->current_event->eventapproved );
	}

function bp_new_event_description() {
	echo bp_get_new_event_description();
}
	function bp_get_new_event_description() {
		global $bp;
		return apply_filters( 'bp_get_new_event_description', $bp->jes_events->current_event->description );
	}

function bp_new_event_eventterms() {
	echo bp_get_new_event_eventterms();
}
	function bp_get_new_event_eventterms() {
		global $bp;
		return apply_filters( 'bp_get_new_event_eventterms', $bp->jes_events->current_event->eventterms );
	}

	
// Group/Forum Link	
function bp_new_event_group_link() {
	echo bp_get_new_event_group_link();
}
	function bp_get_new_event_group_link() {
		global $bp;
		return apply_filters( 'bp_get_new_event_group_link', $bp->jes_events->current_event->group_link );
	}
	
function bp_new_event_forum_link() {
	echo bp_get_new_event_forum_link();
}
	function bp_get_new_event_forum_link() {
		global $bp;
		return apply_filters( 'bp_get_new_event_forum_link', $bp->jes_events->current_event->forum_link );
	}
	


/* Placed */
// Country
function bp_new_event_placedcountry() {
	echo bp_get_new_event_placedcountry();
}
	function bp_get_new_event_placedcountry() {
		global $bp;
		return apply_filters( 'bp_get_new_event_placedcountry', $bp->jes_events->current_event->placedcountry );
	}
	
// State
function bp_new_event_placedstate() {
	echo bp_get_new_event_placedstate();
}
	function bp_get_new_event_placedstate() {
		global $bp;
		return apply_filters( 'bp_get_new_event_placedcity', $bp->jes_events->current_event->placedstate );
	}
	
// City	
function bp_new_event_placedcity() {
	echo bp_get_new_event_placedcity();
}
	function bp_get_new_event_placedcity() {
		global $bp;
		return apply_filters( 'bp_get_new_event_placedcity', $bp->jes_events->current_event->placedcity );
	}

// Address	
function bp_new_event_placedaddress() {
	echo bp_get_new_event_placedaddress();
}
	function bp_get_new_event_placedaddress() {
		global $bp;
		return apply_filters( 'bp_get_new_event_placedaddress', $bp->jes_events->current_event->placedaddress );
	}
	
function bp_new_event_placednote() {
	echo bp_get_new_event_placednote();
}
	function bp_get_new_event_placednote() {
		global $bp;
		return apply_filters( 'bp_get_new_event_placednote', $bp->jes_events->current_event->placednote );
	}
	
function bp_new_event_placedgooglemap() {
	echo bp_get_new_event_placedgooglemap();
}
	function bp_get_new_event_placedgooglemap() {
		global $bp;
		return apply_filters( 'bp_get_new_event_placedgooglemap', $bp->jes_events->current_event->placedgooglemap );
	}
		
function bp_new_event_flyer() {
	echo bp_get_new_event_flyer();
}
	function bp_get_new_event_flyer() {
		global $bp;
		return apply_filters( 'bp_get_new_event_flyer', $bp->jes_events->current_event->flyer );
	}
		
function bp_new_event_newspublic() {
	echo bp_get_new_event_newspublic();
}
	function bp_get_new_event_newspublic() {
		global $bp;
		return apply_filters( 'bp_get_new_event_newspublic', $bp->jes_events->current_event->newspublic );
	}


function bp_new_event_newsprivate() {
	echo bp_get_new_event_newsprivate();
}
	function bp_get_new_event_newsprivate() {
		global $bp;
		return apply_filters( 'bp_get_new_event_newsprivate', $bp->jes_events->current_event->newsprivate );
	}
	
function bp_new_event_edtsd() {
	echo bp_get_new_event_edtsd();
}
	function bp_get_new_event_edtsd() {
		global $bp;
		return apply_filters( 'bp_get_new_event_edtsd', $bp->jes_events->current_event->edtsd );
	}

function bp_new_event_edted() {
	echo bp_get_new_event_edted();
}
	function bp_get_new_event_edted() {
		global $bp;
		return apply_filters( 'bp_get_new_event_edted', $bp->jes_events->current_event->edted );
	}
	
function bp_new_event_edtsth() {
	echo bp_get_new_event_edtsth();
}
	function bp_get_new_event_edtsth() {
		global $bp;
		return apply_filters( 'bp_get_new_event_edtsth', $bp->jes_events->current_event->edtsth );
	}

function bp_new_event_edteth() {
	echo bp_get_new_event_edteth();
}
	function bp_get_new_event_edteth() {
		global $bp;
		return apply_filters( 'bp_get_new_event_edteth', $bp->jes_events->current_event->edteth );
	}

function bp_new_event_edtstm() {
	echo bp_get_new_event_edtstm();
}
	function bp_get_new_event_edtstm() {
		global $bp;
		return apply_filters( 'bp_get_new_event_edtstm', $bp->jes_events->current_event->edtstm );
	}

function bp_new_event_edtetm() {
	echo bp_get_new_event_edtetm();
}
	function bp_get_new_event_edtetm() {
		global $bp;
		return apply_filters( 'bp_get_new_event_edtetm', $bp->jes_events->current_event->edtetm );
	}
	
function bp_new_event_allday() {
	echo bp_get_new_event_allday();
}
	function bp_get_new_event_allday() {
		global $bp;
		return apply_filters( 'bp_get_new_event_allday', $bp->jes_events->current_event->allday );
	}
	
function bp_new_event_weekly() {
	echo bp_get_new_event_weekly();
}
	function bp_get_new_event_weekly() {
		global $bp;
		return apply_filters( 'bp_get_new_event_weekly', $bp->jes_events->current_event->weekly );
	}
	
		
function bp_new_event_status() {
	echo bp_get_new_event_status();
}
	function bp_get_new_event_status() {
		global $bp;
		return apply_filters( 'bp_get_new_event_status', $bp->jes_events->current_event->status );
	}

function bp_new_event_forumlink() {
	echo bp_get_new_event_forumlink();
}
	function bp_get_new_event_forumlink() {
		global $bp;
		return apply_filters( 'bp_get_new_event_forumlink', $bp->jes_events->current_event->forumlink );
	}

function bp_new_event_enablesocial() {
	echo bp_get_new_event_enablesocial();
}
	function bp_get_new_event_enablesocial() {
		global $bp;
		return apply_filters( 'bp_get_new_event_enablesocial', $bp->jes_events->current_event->enablesocial );
	}
	
function bp_new_event_grouplink() {
	echo bp_get_new_event_grouplink();
}
	function bp_get_new_event_grouplink() {
		global $bp;
		return apply_filters( 'bp_get_new_event_grouplink', $bp->jes_events->current_event->grouplink );
	}	
	
function bp_new_event_avatar( $args = '' ) {
	echo bp_get_new_event_avatar( $args );
}
	function bp_get_new_event_avatar( $args = '' ) {
		global $bp;

		$defaults = array(
			'type' => 'full',
			'width' => false,
			'height' => false,
			'class' => 'avatar',
			'id' => 'avatar-crop-preview',
			'alt' => __( 'Event avatar', 'jet-event-system' ),
			'no_grav' => false
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
	$adata = get_site_option('jes_events' );
	 $_acheck = $adata['jes_events_defavatar'];
	if ($_acheck)
		{
			return '<img src="'.$_acheck.'">';
		}
			else
				{
					return apply_filters( 'bp_get_new_event_avatar', bp_core_fetch_avatar( array( 'item_id' => $bp->jes_events->current_event->id, 'object' => 'event', 'type' => $type, 'avatar_dir' => 'event-avatars', 'alt' => $alt, 'width' => $width, 'height' => $height, 'class' => $class, 'no_grav' => $no_grav ) ) );
				}
	}

function bp_event_creation_previous_link() {
	echo bp_get_event_creation_previous_link();
}
	function bp_get_event_creation_previous_link() {
		global $bp;

		foreach ( (array)$bp->jes_events->event_creation_steps as $slug => $name ) {
			if ( $slug == $bp->action_variables[1] )
				break;

			$previous_steps[] = $slug;
		}

		return apply_filters( 'bp_get_event_creation_previous_link', $bp->loggedin_user->domain . $bp->jes_events->slug . '/create/step/' . array_pop( $previous_steps ) );
	}

function bp_is_last_event_creation_step() {
	global $bp;

	$last_step = array_pop( array_keys( $bp->jes_events->event_creation_steps ) );

	if ( $last_step == $bp->jes_events->current_create_step )
		return true;

	return false;
}

function bp_is_first_event_creation_step() {
	global $bp;

	$first_step = array_shift( array_keys( $bp->jes_events->event_creation_steps ) );

	if ( $first_step == $bp->jes_events->current_create_step )
		return true;

	return false;
}

function bp_new_jes_event_invite_friend_list() {
	echo bp_get_new_jes_event_invite_friend_list();
}
	function bp_get_new_jes_event_invite_friend_list( $args = '' ) {
		global $bp;

		if ( !function_exists('friends_install') )
			return false;

		$defaults = array(
			'event_id' => false,
			'separator' => 'li'
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$jes_adata = get_site_option('jes_events' );
		$eshowavatar = $jes_adata[ 'jes_events_show_avatar_invite_enable' ];		
		
		if ( !$event_id )
			$event_id = ( $bp->jes_events->new_event_id ) ? $bp->jes_events->new_event_id : $bp->jes_events->current_event->id;

		$friends = friends_get_friends_invite_list( $bp->loggedin_user->id, $event_id );

		if ( $friends ) {
			$invites = events_jes_get_invites_for_event( $bp->loggedin_user->id, $event_id );

			for ( $i = 0; $i < count( $friends ); $i++ ) {
				if ( $invites ) {
					if ( in_array( $friends[$i]['id'], $invites ) ) {
						$checked = ' checked="checked"';
					} else {
						$checked = '';
					}
				}
			if ($eshowavatar) { 
				$adata = get_site_option('jes_events' );
				$size_for_avatar = $adata['jes_events_show_avatar_invite_size'];
				$padding_size = (int)($size_for_avatar/2);
			$items[] = '<' . $separator . ' style="padding-bottom:'.$padding_size.'px;">'.jes_get_member_avatar('id='.$friends[$i]['id'].'&alt='.$friends[$i]['full_name'].'&height='.$size_for_avatar.'&width='.$size_for_avatar).'<input' . $checked . ' type="checkbox" name="friends[]" id="f-' . $friends[$i]['id'] . '" value="' . attribute_escape( $friends[$i]['id'] ) . '" /> ' . $friends[$i]['full_name'] . '<br /></' . $separator . '><br />';
			} else {
				$items[] = '<' . $separator . '>'.'<input' . $checked . ' type="checkbox" name="friends[]" id="f-' . $friends[$i]['id'] . '" value="' . attribute_escape( $friends[$i]['id'] ) . '" /> ' . $friends[$i]['full_name'] . '</' . $separator . '>';
				}
			}
		}

		return implode( "\n", (array)$items );
	}

function bp_directory_events_search_form() {
	global $bp;

	$search_value = __( ' Search events...', 'buddypress' );
	if ( !empty( $_REQUEST['s'] ) )
	 	$search_value = $_REQUEST['s'];
?>
	<form action="" method="get" id="search-events-form">
		<label><input type="text" name="s" id="events_search" value="<?php echo esc_attr( $search_value ) ?>" onfocus="if (this.value == '<?php _e( ' Search events...', 'buddypress' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( ' Search events...', 'buddypress' ) ?>';}" /></label>
		<input type="submit" id="events_search_submit" name="events_search_submit" value="<?php _e( 'Search', 'jet-event-system' ) ?>" />
	</form>
<?php
}

function jes_bp_events_header_tabs() {
	global $bp, $create_event_step, $completed_to_step;
?>
	<li<?php if ( !isset($bp->action_variables[0]) || 'recently-active' == $bp->action_variables[0] ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . $bp->jes_events->slug ?>/my-events/recently-active"><?php _e( 'Recently Active', 'jet-event-system' ) ?></a></li>
	<li<?php if ( 'recently-joined' == $bp->action_variables[0] ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . $bp->jes_events->slug ?>/my-events/recently-joined"><?php _e( 'Recently Joined', 'jet-event-system' ) ?></a></li>
	<li<?php if ( 'most-popular' == $bp->action_variables[0] ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . $bp->jes_events->slug ?>/my-events/most-popular""><?php _e( 'Most Popular', 'jet-event-system' ) ?></a></li>
	<li<?php if ( 'admin-of' == $bp->action_variables[0] ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . $bp->jes_events->slug ?>/my-events/admin-of""><?php _e( 'Administrator Of', 'jet-event-system' ) ?></a></li>
	<li<?php if ( 'mod-of' == $bp->action_variables[0] ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . $bp->jes_events->slug ?>/my-events/mod-of""><?php _e( 'Moderator Of', 'jet-event-system' ) ?></a></li>
	<li<?php if ( 'alphabetically' == $bp->action_variables[0] ) : ?> class="current"<?php endif; ?>><a href="<?php echo $bp->displayed_user->domain . $bp->jes_events->slug ?>/my-events/alphabetically""><?php _e( 'Alphabetically', 'jet-event-system' ) ?></a></li>
<?php
	do_action( 'events_header_tabs' );
}

function jes_bp_events_filter_title() {
	global $bp;

	$current_filter = $bp->action_variables[0];

	switch ( $current_filter ) {
		case 'recently-active': default:
			_e( 'Recently Active', 'jet-event-system' );
			break;
		case 'recently-joined':
			_e( 'Recently Joined', 'jet-event-system' );
			break;
		case 'most-popular':
			_e( 'Most Popular', 'jet-event-system' );
			break;
		case 'admin-of':
			_e( 'Administrator Of', 'jet-event-system' );
			break;
		case 'mod-of':
			_e( 'Moderator Of', 'jet-event-system' );
			break;
		case 'alphabetically':
			_e( 'Alphabetically', 'jet-event-system' );
		break;
	}
	do_action( 'jes_bp_events_filter_title' );
}

function bp_is_event_admin_screen( $slug ) {
	global $bp;

	if ( $bp->current_component != JES_SLUG || 'admin' != $bp->current_action )
		return false;

	if ( $bp->action_variables[0] == $slug )
		return true;

	return false;
}

/************************************************************************************
 * Event Avatar Template Tags
 **/

function bp_event_current_avatar() {
	global $bp;

	if ( $bp->jes_events->current_event->avatar_full ) { ?>
		<img src="<?php echo attribute_escape( $bp->jes_events->current_event->avatar_full ) ?>" alt="<?php _e( 'Event Avatar', 'jet-event-system' ) ?>" class="avatar" />
	<?php } else { ?>
		<img src="<?php echo $bp->jes_events->image_base . '/none.gif' ?>" alt="<?php _e( 'No Event Avatar', 'jet-event-system' ) ?>" class="avatar" />
	<?php }
}

function bp_get_event_has_avatar() {
	global $bp;

	if ( !empty( $_FILES ) || !jes_core_fetch_avatar( array( 'item_id' => $bp->jes_events->current_event->id, 'object' => 'event', 'no_grav' => true ) ) )
		return false;

	return true;
}


/* --------------- */

function jes_core_delete_existing_avatar( $args = '' ) {
	global $bp;

	$defaults = array(
		'item_id' => false,
		'object' => 'event', // user OR group OR blog OR custom type (if you use filters)
		'avatar_dir' => false
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	if ( !$item_id ) {
		if ( 'event' == $object )
			$item_id = $current_event->id;
			
		$item_id = apply_filters( 'bp_core_avatar_item_id', $item_id, $object );

		if ( !$item_id ) return false;
	}

	if ( !$avatar_dir ) {
		if ( 'event' == $object )
			$avatar_dir = 'event-avatars';
			
		$avatar_dir = apply_filters( 'bp_core_avatar_dir', $avatar_dir, $object );

		if ( !$avatar_dir ) return false;
	}

	$avatar_folder_dir = apply_filters( 'bp_core_avatar_folder_dir', BP_AVATAR_UPLOAD_PATH . '/' . $avatar_dir . '/' . $item_id, $item_id, $object, $avatar_dir );

	if ( !file_exists( $avatar_folder_dir ) )
		return false;

	if ( $av_dir = opendir( $avatar_folder_dir ) ) {
		while ( false !== ( $avatar_file = readdir($av_dir) ) ) {
			if ( ( preg_match( "/-bpfull/", $avatar_file ) || preg_match( "/-bpthumb/", $avatar_file ) ) && '.' != $avatar_file && '..' != $avatar_file )
				@unlink( $avatar_folder_dir . '/' . $avatar_file );
		}
	}
	closedir($av_dir);

	@rmdir( $avatar_folder_dir );

	do_action( 'jes_core_delete_existing_avatar', $args );

	return true;
}


/* --------------- */

function jes_bp_event_avatar_delete_link() {
	echo jes_bp_get_event_avatar_delete_link();
}
	function jes_bp_get_event_avatar_delete_link() {
		global $bp;

		return apply_filters( 'jes_bp_get_event_avatar_delete_link', wp_nonce_url( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . '/admin/event-avatar/delete', 'jes_bp_event_avatar_delete' ) );
	}

function jes_bp_event_avatar_edit_form() {
	events_avatar_upload();
}

function bp_custom_event_boxes() {
	do_action( 'events_custom_event_boxes' );
}

function bp_custom_event_admin_tabs() {
	do_action( 'events_custom_event_admin_tabs' );
}

function bp_custom_event_fields_editable() {
	do_action( 'events_custom_event_fields_editable' );
}

function bp_custom_event_fields() {
	do_action( 'events_custom_event_fields' );
}


/************************************************************************************
 * Membership Requests Template Tags
 **/

class JES_Events_Membership_Requests_Template {
	var $current_request = -1;
	var $request_count;
	var $requests;
	var $request;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_request_count;

	function jes_events_membership_requests_template( $event_id, $per_page, $max ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['mrpage'] ) ? intval( $_REQUEST['mrpage'] ) : 1;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		$this->requests = JES_Events_Event::get_membership_requests( $event_id, $this->pag_num, $this->pag_page );

		if ( !$max || $max >= (int)$this->requests['total'] )
			$this->total_request_count = (int)$this->requests['total'];
		else
			$this->total_request_count = (int)$max;

		$this->requests = $this->requests['requests'];

		if ( $max ) {
			if ( $max >= count($this->requests) )
				$this->request_count = count($this->requests);
			else
				$this->request_count = (int)$max;
		} else {
			$this->request_count = count($this->requests);
		}

		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( 'mrpage', '%#%' ),
			'format' => '',
			'total' => ceil( $this->total_request_count / $this->pag_num ),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
	}

	function has_requests() {
		if ( $this->request_count )
			return true;

		return false;
	}

	function next_request() {
		$this->current_request++;
		$this->request = $this->requests[$this->current_request];

		return $this->request;
	}

	function rewind_requests() {
		$this->current_request = -1;
		if ( $this->request_count > 0 ) {
			$this->request = $this->requests[0];
		}
	}

	function requests() {
		if ( $this->current_request + 1 < $this->request_count ) {
			return true;
		} elseif ( $this->current_request + 1 == $this->request_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_requests();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_request() {
		global $request;

		$this->in_the_loop = true;
		$this->request = $this->next_request();

		if ( 0 == $this->current_request ) // loop has just started
			do_action('loop_start');
	}
}

function bp_event_jes_has_membership_requests( $args = '' ) {
	global $requests_template, $events_template;

	$defaults = array(
		'event_id' => $events_template->event->id,
		'per_page' => 10,
		'max' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$requests_template = new JES_Events_Membership_Requests_Template( $event_id, $per_page, $max );
	return apply_filters( 'bp_event_jes_has_membership_requests', $requests_template->has_requests(), &$requests_template );
}

function bp_event_membership_requests() {
	global $requests_template;

	return $requests_template->requests();
}

function bp_event_the_membership_request() {
	global $requests_template;

	return $requests_template->the_request();
}

function bp_event_request_user_avatar_thumb() {
	global $requests_template;

	echo apply_filters( 'bp_event_request_user_avatar_thumb', bp_core_fetch_avatar( array( 'item_id' => $requests_template->request->user_id, 'type' => 'thumb' ) ) );
}

function bp_event_request_reject_link() {
	global $requests_template, $events_template;

	echo apply_filters( 'bp_event_request_reject_link', wp_nonce_url( jes_bp_get_event_permalink( $events_template->event ) . '/admin/membership-requests/reject/' . $requests_template->request->id, 'events_reject_membership_request' ) );
}

function bp_event_request_accept_link() {
	global $requests_template, $events_template;

	echo apply_filters( 'bp_event_request_accept_link', wp_nonce_url( jes_bp_get_event_permalink( $events_template->event ) . '/admin/membership-requests/accept/' . $requests_template->request->id, 'events_accept_membership_request' ) );
}

function bp_event_request_time_since_requested() {
	global $requests_template;

	echo apply_filters( 'bp_event_request_time_since_requested', sprintf( __( 'requested %s ago', 'jet-event-system' ), bp_core_time_since( strtotime( $requests_template->request->date_modified ) ) ) );
}

function bp_event_request_comment() {
	global $requests_template;

	echo apply_filters( 'bp_event_request_comment', strip_tags( stripslashes( $requests_template->request->comments ) ) );
}

function bp_event_request_user_link() {
	global $requests_template;

	echo apply_filters( 'bp_event_request_user_link', bp_core_get_userlink( $requests_template->request->user_id ) );
}


/************************************************************************************
 * Invite Friends Template Tags
 **/

class BP_Events_Invite_Template {
	var $current_invite = -1;
	var $invite_count;
	var $invites;
	var $invite;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_invite_count;

	function jes_bp_events_invite_template( $user_id, $event_id ) {
		global $bp;

		$this->invites = events_jes_get_invites_for_event( $user_id, $event_id );
		$this->invite_count = count( $this->invites );
	}

	function has_invite_jes() {
		if ( $this->invite_count )
			return true;

		return false;
	}

	function next_invite() {
		$this->current_invite++;
		$this->invite = $this->invites[$this->current_invite];

		return $this->invite;
	}

	function rewind_invite_jes() {
		$this->current_invite = -1;
		if ( $this->invite_count > 0 ) {
			$this->invite = $this->invites[0];
		}
	}

	function invite_jes() {
		if ( $this->current_invite + 1 < $this->invite_count ) {
			return true;
		} elseif ( $this->current_invite + 1 == $this->invite_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_invite_jes();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_invite() {
		global $invite;

		$this->in_the_loop = true;
		$user_id = $this->next_invite();

		$this->invite = new stdClass;
		$this->invite->user = new BP_Core_User( $user_id );
		$this->invite->event_id = $event_id; // Globaled in bp_event_has_invite_jes()

		if ( 0 == $this->current_invite ) // loop has just started
			do_action('loop_start');
	}
}

function bp_event_has_invite_jes( $args = '' ) {
	global $bp, $invites_template, $event_id;

	$defaults = array(
		'event_id' => false,
		'user_id' => $bp->loggedin_user->id
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( !$event_id ) {
		/* Backwards compatibility */
		if ( $bp->jes_events->current_event ) $event_id = $bp->jes_events->current_event->id;
		if ( $bp->jes_events->new_event_id ) $event_id = $bp->jes_events->new_event_id;
	}

	if ( !$event_id )
		return false;

	$invites_template = new BP_Events_Invite_Template( $user_id, $event_id );
	return apply_filters( 'bp_event_has_invites', $invites_template->has_invite_jes(), &$invites_template );
}

function bp_jes_event_invite_jes() {
	global $invites_template;

	return $invites_template->has_invite_jes();
}

function bp_event_the_invite() {
	global $invites_template;

	return $invites_template->the_invite();
}

function bp_jes_event_invite_item_id() {
	echo bp_get_jes_event_invite_item_id();
}
	function bp_get_jes_event_invite_item_id() {
		global $invites_template;

		return apply_filters( 'bp_get_jes_event_invite_item_id', 'uid-' . $invites_template->invite->user->id );
	}

function bp_jes_event_invite_user_avatar() {
	echo bp_get_jes_event_invite_user_avatar();
}
	function bp_get_jes_event_invite_user_avatar() {
		global $invites_template;

		return apply_filters( 'bp_get_jes_event_invite_user_avatar', $invites_template->invite->user->avatar_thumb );
	}

function bp_jes_event_invite_user_link() {
	echo bp_get_jes_event_invite_user_link();
}
	function bp_get_jes_event_invite_user_link() {
		global $invites_template;

		return apply_filters( 'bp_get_jes_event_invite_user_link', bp_core_get_userlink( $invites_template->invite->user->id ) );
	}

function bp_jes_event_invite_user_last_active() {
	echo bp_get_jes_event_invite_user_last_active();
}
	function bp_get_jes_event_invite_user_last_active() {
		global $invites_template;

		return apply_filters( 'bp_get_jes_event_invite_user_last_active', $invites_template->invite->user->last_active );
	}

function bp_jes_event_invite_user_remove_invite_url() {
	echo bp_get_jes_event_invite_user_remove_invite_url();
}
	function bp_get_jes_event_invite_user_remove_invite_url() {
		global $invites_template;

		return wp_nonce_url( site_url( JES_SLUG . '/' . $invites_template->invite->event_id . '/invites/remove/' . $invites_template->invite->user->id ), 'events_invite_uninvite_user' );
	}

function jes_member_avatar( $args = '' ) {
	echo apply_filters( 'bp_member_avatar', jes_get_member_avatar( $args ) );
}
	function jes_get_member_avatar( $args = '' ) {
		global $bp, $members_template;

		$defaults = array(
			'type' => 'thumb',
			'width' => false,
			'height' => false,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'bp_get_member_avatar', bp_core_fetch_avatar( array( 'item_id' => $id, 'type' => $type, 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height ) ) );
	}
	
	
	
/***
 * Events RSS Feed Template Tags
 */

function bp_event_activity_feed_link() {
	echo bp_get_event_activity_feed_link();
}
	function bp_get_event_activity_feed_link() {
		global $bp;

		return apply_filters( 'bp_get_event_activity_feed_link', jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'feed/' );
	}

	
function bp_is_event() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->jes_events->current_event )
		return true;

	return false;
}

function bp_is_event_home() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && ( !$bp->current_action || 'home' == $bp->current_action ) )
		return true;

	return false;
}

function bp_is_event_create() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && 'create' == $bp->current_action )
		return true;

	return false;
}


function bp_is_event_admin_page() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'admin' == $bp->current_action )
		return true;

	return false;
}	
	
function bp_current_event_name() {
	echo bp_get_current_event_name();
}
	function bp_get_current_event_name() {
		global $bp;

		$name = apply_filters( 'jes_bp_get_event_name', $bp->jes_events->current_event->name );
		return apply_filters( 'bp_get_current_event_name', $name );
	}
	
function bp_is_event_activity() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'activity' == $bp->current_action )
		return true;

	return false;
}

function bp_is_event_forum_topic() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'forum' == $bp->current_action && 'topic' == $bp->action_variables[0] )
		return true;

	return false;
}


function bp_is_event_members() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'members' == $bp->current_action )
		return true;

	return false;
}

function bp_is_jes_event_invite_jes() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && 'send-invites' == $bp->current_action )
		return true;

	return false;
}

function bp_is_jes_event_google_map_jes() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && 'google-map' == $bp->current_action )
		return true;

	return false;
}

function bp_is_jes_event_flyer_jes() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && 'flyer' == $bp->current_action )
		return true;

	return false;
}

function bp_is_event_membership_request() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && 'request-join-to-event' == $bp->current_action )
		return true;

	return false;
}

function bp_is_event_leave() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'leave-event' == $bp->current_action )
		return true;

	return false;
}

function bp_is_event_single() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item )
		return true;

	return false;
}

function bp_is_user_events() {
	global $bp;

	if ( JES_SLUG == $bp->current_component )
		return true;

	return false;
}
function jet_new_event_enable_forum() {
	echo jet_get_new_event_enable_forum();
}
	function jet_get_new_event_enable_forum() {
		global $bp;
		return (int) apply_filters( 'bp_get_new_event_enable_forum', $bp->jes_events->current_event->enable_forum );
	}

function bp_is_event_forum() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'forum' == $bp->current_action )
		return true;

	return false;
}

function jes_is_event_forum_topic() {
	global $bp;

	if ( BP_EVENTS_SLUG == $bp->current_component && $bp->is_single_item && 'forum' == $bp->current_action && 'topic' == $bp->action_variables[0] )
		return true;

	return false;
}

function jes_is_event_forum_topic_edit() {
	global $bp;

	if ( JES_SLUG == $bp->current_component && $bp->is_single_item && 'forum' == $bp->current_action && 'topic' == $bp->action_variables[0] && 'edit' == $bp->action_variables[2] )
		return true;

	return false;
}

?>