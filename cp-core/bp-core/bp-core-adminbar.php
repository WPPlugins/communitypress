<?php

function bp_core_admin_bar() {
	global $bp, $wpdb, $current_blog;

	if ( defined( 'BP_DISABLE_ADMIN_BAR' ) )
		return false;

	if ( (int)get_site_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() )
		return false;

	$bp->doing_admin_bar = true;

	// JLL_MOD - fixed
	echo '<div id="wp-admin-bar" style="background: url(' . $bp->root_domain . get_site_option('cp-bar-img') . ')' . get_site_option('cp-bar-color') . ';"><div class="padder">';

	// **** Do bp-adminbar-logo Actions ********
	do_action( 'bp_adminbar_logo' );

	echo '<ul class="main-nav">';

	// **** Do bp-adminbar-menus Actions ********
	do_action( 'bp_adminbar_menus' );

	echo '</ul>';
	echo "</div></div><!-- #wp-admin-bar -->\n\n";

	$bp->doing_admin_bar = false;
}

// **** Default BuddyPress admin bar logo ********
function bp_adminbar_logo() {
	global $bp;

	echo '<a href="' . $bp->root_domain . '" id="admin-bar-logo">' . get_blog_option( BP_ROOT_BLOG, 'blogname') . '</a>';
}

// **** "Log In" and "Sign Up" links (Visible when not logged in) ********
function bp_adminbar_login_menu() {
	global $bp;

	if ( is_user_logged_in() )
		return false;
		
		// JLL_MOD - fixed login link
	echo '<li class="bp-login no-arrow"><a href="' . home_url() . '">' . __( 'Log In', 'buddypress' ) . '</a></li>';

	// Show "Sign Up" link if user registrations are allowed
	if ( bp_get_signup_allowed() )
		echo '<li class="bp-signup no-arrow"><a href="' . bp_get_signup_page(false) . '">' . __( 'Sign Up', 'buddypress' ) . '</a></li>';
}


// **** "My Account" Menu ******
function bp_adminbar_account_menu() {
	global $bp;

	if ( !$bp->bp_nav || !is_user_logged_in() )
		return false;

	echo '<li id="bp-adminbar-account-menu"><a href="' . bp_loggedin_user_domain() . '">';

	echo __( 'My Account', 'buddypress' ) . '</a>';
	echo '<ul>';

	/* Loop through each navigation item */
	$counter = 0;
	foreach( (array)$bp->bp_nav as $nav_item ) {
		$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

		echo '<li' . $alt . '>';
		echo '<a id="bp-admin-' . $nav_item['css_id'] . '" href="' . $nav_item['link'] . '">' . $nav_item['name'] . '</a>';

		if ( is_array( $bp->bp_options_nav[$nav_item['slug']] ) ) {
			echo '<ul>';
			$sub_counter = 0;

			foreach( (array)$bp->bp_options_nav[$nav_item['slug']] as $subnav_item ) {
				$link = str_replace( $bp->displayed_user->domain, $bp->loggedin_user->domain, $subnav_item['link'] );
				$name = str_replace( $bp->displayed_user->userdata->user_login, $bp->loggedin_user->userdata->user_login, $subnav_item['name'] );
				$alt = ( 0 == $sub_counter % 2 ) ? ' class="alt"' : '';
				echo '<li' . $alt . '><a id="bp-admin-' . $subnav_item['css_id'] . '" href="' . $link . '">' . $name . '</a></li>';
				$sub_counter++;
			}
			echo '</ul>';
		}

		echo '</li>';

		$counter++;
	}

	$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

	echo '<li' . $alt . '><a id="bp-admin-logout" class="logout" href="' . wp_logout_url( site_url() ) . '">' . __( 'Log Out', 'buddypress' ) . '</a></li>';
	echo '</ul>';
	echo '</li>';
}

// **** "Admin" Menu *********
function bp_adminbar_thisblog_menu() {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<li id="bp-adminbar-thisblog-menu"><a href="' . admin_url() . '">';

		_e( 'Admin', 'buddypress' );

		echo '</a>';
		echo '<ul>';

	global $bp;
	
	//user admin
	if ( $bp->displayed_user->id && is_super_admin() && !bp_is_my_profile() ){
		/* Don't show this menu to non site admins or if you're viewing your own profile */ ?>
				<li><a href="<?php echo $bp->displayed_user->domain . $bp->profile->slug ?>/edit/"><?php printf( __( "Edit %s's Profile", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ) ?></a></li>
				<li><a href="<?php echo $bp->displayed_user->domain . $bp->profile->slug ?>/change-avatar/"><?php printf( __( "Edit %s's Avatar", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ) ?></a></li>
	
				<?php if ( !bp_core_is_user_spammer( $bp->displayed_user->id ) ) : ?>
					<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/mark-spammer/', 'mark-unmark-spammer' ) ?>" class="confirm"><?php _e( "Mark as Spammer", 'buddypress' ) ?></a></li>
				<?php else : ?>
					<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/unmark-spammer/', 'mark-unmark-spammer' ) ?>" class="confirm"><?php _e( "Not a Spammer", 'buddypress' ) ?></a></li>
				<?php endif; ?>
	
				<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/delete-user/', 'delete-user' ) ?>" class="confirm"><?php printf( __( "Delete %s", 'buddypress' ), esc_attr( $bp->displayed_user->fullname ) ) ?></a></li>
<?php
    }


	// event admin
	elseif ( $bp->jes_events->current_event && is_super_admin() ){
	?>
			<li><a class="confirm" href="<?php echo wp_nonce_url( jes_bp_get_event_permalink( $bp->jes_events->current_event ) . 'admin/delete-event/', 'events_delete_event' ) ?>&amp;delete-event-button=1&amp;delete-event-understand=1"><?php _e( "Delete Event", 'jet-event-system' ) ?></a></li>
			<?php do_action( 'events_adminbar_menu_items' ) ?>
	<?php
}


	// groups admin
	elseif ( $bp->groups->current_group && is_super_admin() ){
	?>
			<li><a class="confirm" href="<?php echo wp_nonce_url( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/delete-group/', 'groups_delete_group' ) ?>&amp;delete-group-button=1&amp;delete-group-understand=1"><?php _e( "Delete Group", 'buddypress' ) ?></a></li>
			<?php do_action( 'groups_adminbar_menu_items' ) ?>
	<?php 
	} else {
	

		echo '<li class="alt"><a href="' . admin_url() . 'post-new.php">' . __( 'New Post', 'buddypress' ) . '</a></li>';
		echo '<li><a href="' . admin_url() . 'edit.php">' . __( 'Manage Posts', 'buddypress' ) . '</a></li>';
		echo '<li class="alt"><a href="' . admin_url() . 'edit-comments.php">' . __( 'Manage Comments', 'buddypress' ) . '</a></li>';

		do_action( 'bp_adminbar_thisblog_items' );
	}
		echo '</ul>';
		echo '</li>';
	}
}

// **** "Notifications" Menu *********
function bp_adminbar_notifications_menu() {
	global $bp;

	if ( !is_user_logged_in() )
		return false;

	echo '<li id="bp-adminbar-notifications-menu"><a href="' . $bp->loggedin_user->domain . '">';
	_e( 'Notifications', 'buddypress' );

	if ( $notifications = bp_core_get_notifications_for_user( $bp->loggedin_user->id ) ) { ?>
		<span><?php echo count( $notifications ) ?></span>
	<?php
	}

	echo '</a>';
	echo '<ul>';

	if ( $notifications ) {
		$counter = 0;
		for ( $i = 0; $i < count($notifications); $i++ ) {
			$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

			<li<?php echo $alt ?>><?php echo $notifications[$i] ?></li>

			<?php $counter++;
		}
	} else { ?>

		<li><a href="<?php echo $bp->loggedin_user->domain ?>"><?php _e( 'No new notifications.', 'buddypress' ); ?></a></li>

	<?php
	}

	echo '</ul>';
	echo '</li>';
}


// **** "User Icons" Menu *********
function bp_adminbar_home_menu() {
	global $bp, $wpdb;
	$disabled_components = get_site_option( 'bp-deactivated-components' );
	
	if ( !$bp->bp_nav || !is_user_logged_in() )
		return false;

		echo '<li id="navicon" class="bp-adminbar-home-menu"><a href="' . bp_loggedin_user_domain() . 'activity/" title="News Feed"><img src="' . home_url() . '/wp-content/plugins/communitypress/cp-core/bp-core/images/newsfeed.png" width="16" height="16" alt="News Feed" /></a>';
		echo '</li>';
		
		echo '<li id="navicon" class="bp-adminbar-home-menu"><a href="' . bp_loggedin_user_domain() . 'messages/" title="Messages"><img src="' . home_url() . '/wp-content/plugins/communitypress/cp-core/bp-core/images/messages.png" width="16" height="16" alt="News Feed" /></a>';
		echo '</li>';
		
		if ( !isset( $disabled_components['photos'] ) ) {
		echo '<li id="navicon" class="bp-adminbar-home-menu"><a href="' . bp_loggedin_user_domain() . get_site_option( 'bp_album_slug' ) . '/" title="My Photos"><img src="' . home_url() . '/wp-content/plugins/communitypress/cp-core/bp-core/images/photos2.png" width="16" height="16" alt="News Feed" /></a>';
		echo '</li>';
		}
		
		echo '<li id="navicon" class="bp-adminbar-home-menu"><a href="' . bp_loggedin_user_domain() . 'friends/" title="My Friends"><img src="' . home_url() . '/wp-content/plugins/communitypress/cp-core/bp-core/images/friends.png" width="16" height="16" alt="News Feed" /></a>';
		echo '</li>';
		
		echo '<li id="navicon" class="bp-adminbar-home-menu"><a href="' . bp_loggedin_user_domain() . 'groups/" title="My Groups"><img src="' . home_url() . '/wp-content/plugins/communitypress/cp-core/bp-core/images/groups.png" width="16" height="16" alt="News Feed" /></a>';
		echo '</li>';
		
		$jes_events = get_site_option('jes_events' );
		if ( !isset( $disabled_components['events'] ) ) {
		echo '<li id="navicon" class="bp-adminbar-home-menu"><a href="' . bp_loggedin_user_domain() . $jes_events[ 'jes_events_costumslug' ] . '/" title="My Events"><img src="' . home_url() . '/wp-content/plugins/communitypress/cp-core/bp-core/images/events.png" width="16" height="16" alt="News Feed" /></a>';
		echo '</li>';
		}
		
}


// JLL_MOD - Modded Visit Menu
// **** "Random" Menu ********
function bp_adminbar_random_menu() {
	global $bp, $current_blog; ?>

	<li id="bp-adminbar-visitrandom-menu">
		<a href="#"><?php echo get_site_option('cp-bar-label') ?></a>
		<ul class="random-list">


			<?php if ( bp_is_active( 'blogs' ) && is_multisite() ) : ?>

				<li><a href="<?php echo $bp->root_domain . '/' . $bp->blogs->slug . '/?random-blog' ?>"><?php _e( 'Random Blog', 'buddypress' ) ?></a></li>

			<?php endif; ?>

			<?php do_action( 'bp_adminbar_random_menu' ) ?>

		</ul>
				<?php $menuClass = 'random-list';
				$visitNav = '';
				
				if (function_exists('wp_nav_menu')) {
					$visitNav = wp_nav_menu( array( 'theme_location' => 'visit-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
				};
				if ($visitNav == '') { ?>
					<ul class="<?php echo $menuClass; ?>">
							<li><a href="<?php echo $bp->root_domain ?>/members/"><?php _e( 'Members', 'buddypress' ) ?></a></li>
							<li><a href="<?php echo $bp->root_domain ?>/groups/"><?php _e( 'Groups', 'buddypress' ) ?></a></li>
                    <?php $jes_events = get_site_option('jes_events' );
					$disabled_components = get_site_option( 'bp-deactivated-components' );
					if ( !isset( $disabled_components['events'] ) ) { ?>
							<li><a href="<?php echo $bp->root_domain . '/' . $jes_events[ 'jes_events_costumslug' ] ?>"><?php _e( 'Events', 'buddypress' ) ?></a></li>
					<?php } ?>
							<?php if ( !(int)get_site_option( 'bp-disable-forum-directory' ) && !isset( $disabled_components['bp-forums.php'] ) ) { ?>
							<li><a href="<?php echo $bp->root_domain ?>/forums/"><?php _e( 'Forums', 'buddypress' ) ?></a></li>
                            <?php } ?>
					</ul> <!-- end ul.nav -->
				<?php }
				else echo($visitNav); ?>
	</li>

<?php 
}

// **** "My Account" Menu ********
function bp_adminbar_new_my_account_menu() {
	global $bp;

	if ( !$bp->bp_nav || !is_user_logged_in() )
		return false;
		
		echo '<li id="bp-adminbar-thisblog-menu"><a href="' . bp_loggedin_user_domain() . '">';
		_e( 'My Account', 'buddypress' );
		echo '</a>';
		echo '<ul>';

		echo '<li class="alt"><a href="' . bp_loggedin_user_domain() . 'wall/">' . __( 'My Wall', 'buddypress' ) . '</a></li>';
		echo '<li><a href="' . bp_loggedin_user_domain() . 'profile/">' . __( 'My Info', 'buddypress' ) . '</a></li>';
		echo '<li class="alt"><a href="' . bp_loggedin_user_domain() . 'profile/change-avatar/">' . __( 'Edit Profile Picture', 'buddypress' ) . '</a></li>';
		echo '<li><a href="' . bp_loggedin_user_domain() . 'settings/">' . __( 'Account Settings', 'buddypress' ) . '</a></li>';
		
		global $wpdb;
		$disabled_components = get_site_option( 'bp-deactivated-components' );
		if ( !isset( $disabled_components['userprivacy'] ) ){
		echo '<li class="alt"><a href="' . bp_loggedin_user_domain() . 'settings/privacy/">' . __( 'Privacy Settings', 'buddypress' ) . '</a></li>';
		}
		echo '<li class="alt"><a id="bp-admin-logout" class="logout" href="' . wp_logout_url( site_url() ) . '">' . __( 'Log Out', 'buddypress' ) . '</a></li>';

		do_action( 'bp_adminbar_new_my_account_menu' );

		echo '</ul>';
		echo '</li>';
}

// **** "Live Search" Menu ********
function bp_adminbar_livesearch () {
	if( is_user_logged_in() ){
			global $bp;
	
		$search_value = ' search people';
		if ( !empty( $_GET['s'] ) )
			$search_value = $_GET['s'];
	
	?>
<div id="livesearchbox">
		<form action="<?php echo $bp->root_domain . '/members'; ?>" method="get" id="search-members-form" autocomplete="off" >
			<label><input type="text" name="s" id="livesearchform" value="<?php echo $search_value ?>"  onfocus="if (this.value == ' search people') {this.value = '';} else {showHint(this.value)}" onblur="hideHint(); if (this.value == '') {this.value = ' search people'}" onkeyup="showHint(this.value)" /></label>
			<button type="submit"  title="Search"></button>
		</form>
		<div id="livesearch"></div>
		</div>
		<?php
	}
}

/**
 * Provides fallback support for the WordPress 3.1 admin bar
 *
 * By default, this function turns off the WP 3.1 admin bar in favor of the classic BP BuddyBar.
 * To turn off the BP BuddyBar in favor of WP's admin bar, place the following in wp-config.php:
 * define( 'BP_USE_WP_ADMIN_BAR', true );
 *
 * @package BuddyPress Core
 * @since 1.2.8
 */
function bp_core_load_admin_bar() {
	global $wp_version;
	
	if ( defined( 'BP_USE_WP_ADMIN_BAR' ) && BP_USE_WP_ADMIN_BAR && version_compare( $wp_version, 3.1, '>=' ) ) {
		// TODO: Add BP support to WP admin bar
		return;
	} elseif ( !defined( 'BP_DISABLE_ADMIN_BAR' ) || !BP_DISABLE_ADMIN_BAR ) {
		// Keep the WP admin bar from loading
		if ( function_exists( 'show_admin_bar' ) )
			show_admin_bar( false );
		
		// Actions used to build the BP admin bar
		add_action( 'bp_adminbar_logo',  'bp_adminbar_logo' );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_login_menu',         2   );
		//add_action( 'bp_adminbar_menus', 'bp_adminbar_account_menu',       4   );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_home_menu',          8   );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_thisblog_menu',      40   );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_notifications_menu', 10   );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_random_menu',        30 );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_new_my_account_menu',        20 );
		add_action( 'bp_adminbar_menus', 'bp_adminbar_livesearch',         110 );
		
		// Actions used to append BP admin bar to footer
		add_action( 'wp_footer',    'bp_core_admin_bar', 8 );
		add_action( 'admin_footer', 'bp_core_admin_bar'    );	
	}
}
add_action( 'bp_loaded', 'bp_core_load_admin_bar' );

?>