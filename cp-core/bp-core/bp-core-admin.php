<?php
// JLL_MOD - Cutomized whole admin menu, moods are all in the tables

function bp_core_communitypress_setup() {
	global $wpdb, $bp, $current_blog;
?>

	<?php
	if ( isset( $_POST['cp-basic-submit'] ) && isset( $_POST['cp-basic'] ) ) {
		if ( !check_admin_referer('cp-basic') )
			return false;
			

		// Settings form submitted, now save the settings.
		foreach ( (array)$_POST['cp-basic'] as $key => $value ) {

				if ( 'cp-logo-url' == $key ) {
					if ( !(string) $value )
					$value = "/wp-content/plugins/communitypress/cp-themes/cp-default/images/logo.png";
				}

				if ( 'cp-minilogo-url' == $key ) {
					if ( !(string) $value )
					$value = "/wp-content/plugins/communitypress/cp-themes/cp-default/images/logo_mini.png";
				}

				if ( 'cp-homebg-url' == $key ) {
					if ( !(string) $value )
					$value = "/wp-content/plugins/communitypress/cp-themes/cp-default/images/login_pic.png";
				}


				if ( 'cp-bar-color' == $key ) {
					if ( !(string) $value )
					$value = "";
				}
				if ( 'cp-bar-img' == $key ) {
					if ( !(string) $value )
					$value = "/wp-content/plugins/communitypress/cp-themes/cp-default/images/60pc_black.png";
				}
				if ( 'cp-bar-label' == $key ) {
					if ( !(string) $value )
					$value = "Browse";
				}


				if ( 'cp-home-text' == $key ) {
					if ( !(string) $value )
					$value = "<h3>A CommunityPress Social Network...</h3><p>Like facebook, users have a Profile, Wall, news feed, make friends, <br />upload pics and private messaging. Create groups,events or browse <br />members. Join in group discussion forums.</p>";
				}
				if ( 'cp-foot-widgets' == $key ) {
					if ( !(string) $value )
					$value = "on";
				}


			update_site_option( $key, $value );
		}

	}
	?>

	<div class="wrap">
    
    <div id="donations"><p><a href="http://communitypress.jessesoffice.com">CommunityPress</a> was a <em>big</em> project. <a href="http://jessesoffice.com">I'm a 1 man show!</a> <a href="http://communitypress.jessesoffice.com/donate">Help support development</a> if you like this plugin. If you find a bug, <a href="http://communitypress.jessesoffice.com/report-bugscontact/">please report it here.</a></p></div>
    
		<h2><?php _e( 'CommuntyPress Basic Setup', 'buddypress' ) ?></h2>
		<?php if ( isset( $_POST['cp-basic'] ) ) : ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Settings Saved', 'buddypress' ) ?></p>
			</div>
		<?php endif; ?>
		<form action="" method="post" id="cp-basic-form">
			<p><?php _e('Here you can setup up the basic look and feel of your community. NOTE: saving an empty field will reset it to default. SAVE the whole form blank to set to default installation.' ) ?></p>
			<table class="form-table">
			<tbody>
				<tr>
<td colspan="2">
						<h3><?php _e( 'Site logos and theme image', 'buddypress' ) ?></h3>
					</td>
				</tr>
				<tr>
					<th scope="row"><p><strong>Logo:</strong><br />Suggested height is 60px, image path must be relative to site root.</p></th>
					<td>
						<input style="width:300px;" name="cp-basic[cp-logo-url]" id="cp-logo-url" value="<?php echo get_site_option('cp-logo-url') ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><p><strong>Mini Logo</strong>:<br />Max height is 23px, image path must be relative to site root.</p></th>
					<td>
						<input style="width:300px;" name="cp-basic[cp-minilogo-url]" id="cp-minilogo-url" value="<?php echo get_site_option('cp-minilogo-url') ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><p><strong>Homepage Backgound</strong>:<br />Max height is 300px, Max width is 650px, image path must be relative to site root.</p></th>
					<td>
						<input style="width:300px;" name="cp-basic[cp-homebg-url]" id="cp-minilogo-url" value="<?php echo get_site_option('cp-homebg-url') ?>" />
					</td>
				</tr>
                
                
				<tr>
					<td colspan="2">
						<h3><?php _e( 'User Bar', 'buddypress' ) ?></h3>
					</td>
				</tr>
  				<tr>
					<th scope="row"><p><strong>User Bar Background Image</strong>:<br />Image path must be relative to site root, type 'null' if no image is desired.</p></th>
					<td>
						<input style="width:300px;" name="cp-basic[cp-bar-img]" id="cp-bar-img" value="<?php echo get_site_option('cp-bar-img') ?>" />
					</td>
				</tr>
  				<tr>
					<th scope="row"><p><strong>User Bar Background Color:</strong><br />Ie - green #666 #6d3b5f<br />No default.</p></th>
					<td>
						<input name="cp-basic[cp-bar-color]" id="cp-bar-color" value="<?php echo get_site_option('cp-bar-color') ?>" />
					</td>
				</tr>
  				<tr>
					<th scope="row"><p><strong>User Bar theme menu label:</strong><br />The user bar has a drop-down menu which may be set in <br />Themes-> Menus. By default it displays CommunityPress directory links. Set a custom label for this menu.<br />Default label is 'Browse'.</p></th>
					<td>
						<input name="cp-basic[cp-bar-label]" id="cp-bar-label" value="<?php echo get_site_option('cp-bar-label') ?>" />
					</td>
				</tr>

                
 				<tr>
					<td colspan="2">
						<h3><?php _e( 'Miscellaneous', 'buddypress' ) ?></h3>
					</td>
				</tr>
  				<tr>
					<th scope="row"><p><strong>Homepage Message:</strong><br />Type a custom message visitors will see when they open your homepage. Type a space character and save if no message is desired.</p></th>
					<td>
						<textarea name="cp-basic[cp-home-text]" id="cp-home-text" rows="6" cols="50"><?php echo get_site_option('cp-home-text') ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row"><p><strong>Footer Widgets</strong><br />Enable the widgetized footer area.<br />No default.</p></th>
					<td>
						<input type="radio" name="cp-basic[cp-foot-widgets]" id="cp-foot-widgets" value="on"<?php if ( "on" == get_site_option('cp-foot-widgets') ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="cp-basic[cp-foot-widgets]" id="cp-foot-widgets" value="off"<?php if ( "off" == get_site_option('cp-foot-widgets') ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>

                
                
                
                
			</tbody>
			</table>
			<p class="submit">
				<input class="button-primary" type="submit" name="cp-basic-submit" id="cp-basic-submit" value="<?php _e( 'Save Settings', 'buddypress' ) ?>"/>
			</p>
			<?php wp_nonce_field( 'cp-basic' ) ?>
		</form>
	</div>
<?php
}


// JLL_MOD - Cutomized whole admin menu, most mods are all in the tables

function bp_core_admin_settings() {
	global $wpdb, $bp, $current_blog;
?>

	<?php
	if ( isset( $_POST['bp-admin-submit'] ) && isset( $_POST['bp-admin'] ) ) {
		if ( !check_admin_referer('bp-admin') )
			return false;

		// Settings form submitted, now save the settings.
		foreach ( (array)$_POST['bp-admin'] as $key => $value ) {

			if ( function_exists( 'xprofile_install' ) ) {
				if ( 'bp-xprofile-base-group-name' == $key ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$bp->profile->table_name_groups} SET name = %s WHERE id = 1", $value ) );
				}

				if ( 'bp-xprofile-fullname-field-name' == $key ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$bp->profile->table_name_fields} SET name = %s WHERE group_id = 1 AND id = 1", $value ) );
				}
			}

			update_site_option( $key, $value );
		}
	}
	?>

	<div class="wrap">
    
    <div id="donations"><p><a href="http://communitypress.jessesoffice.com">CommunityPress</a> was a <em>big</em> project. <a href="http://jessesoffice.com">I'm a 1 man show!</a> <a href="http://communitypress.jessesoffice.com/donate">Help support development</a> if you like this plugin. If you find a bug, <a href="http://communitypress.jessesoffice.com/report-bugscontact/">please report it here.</a></p></div>
    
		<h2><?php _e( 'CommunityPress Settings', 'buddypress' ) ?></h2>

		<?php if ( isset( $_POST['bp-admin'] ) ) : ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Settings Saved', 'buddypress' ) ?></p>
			</div>
		<?php endif; ?>

		<form action="" method="post" id="bp-admin-form">

			<table class="form-table">
			<tbody>
				<?php if ( function_exists( 'xprofile_install' ) ) :?>
				<tr>
					<th scope="row"><p><?php _e( 'Base profile group name', 'buddypress' ) ?>:</p></th>
					<td>
						<input name="bp-admin[bp-xprofile-base-group-name]" id="bp-xprofile-base-group-name" value="<?php echo get_site_option('bp-xprofile-base-group-name') ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><p><?php _e( 'Full Name field name', 'buddypress' ) ?>:</p></th>
					<td>
						<input name="bp-admin[bp-xprofile-fullname-field-name]" id="bp-xprofile-fullname-field-name" value="<?php echo get_site_option('bp-xprofile-fullname-field-name') ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><p><?php _e( 'Disable CommunityPress to WordPress profile syncing?', 'buddypress' ) ?>:</p></th>
					<td>
						<input type="radio" name="bp-admin[bp-disable-profile-sync]"<?php if ( (int)get_site_option( 'bp-disable-profile-sync' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-profile-sync" value="1" /> <?php _e( 'Yes', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp-admin[bp-disable-profile-sync]"<?php if ( !(int)get_site_option( 'bp-disable-profile-sync' ) || '' == get_site_option( 'bp-disable-profile-sync' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-profile-sync" value="0" /> <?php _e( 'No', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<th scope="row"><p><?php _e( 'Hide User Bar for logged out users?', 'buddypress' ) ?>:</p></th>
					<td>
						<input type="radio" name="bp-admin[hide-loggedout-adminbar]"<?php if ( (int)get_site_option( 'hide-loggedout-adminbar' ) ) : ?> checked="checked"<?php endif; ?> id="bp-admin-hide-loggedout-adminbar-yes" value="1" /> <?php _e( 'Yes', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp-admin[hide-loggedout-adminbar]"<?php if ( !(int)get_site_option( 'hide-loggedout-adminbar' ) ) : ?> checked="checked"<?php endif; ?> id="bp-admin-hide-loggedout-adminbar-no" value="0" /> <?php _e( 'No', 'buddypress' ) ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><p><?php _e( 'Disable avatar uploads? (Gravatars will still work)', 'buddypress' ) ?>:</p></th>
					<td>
						<input type="radio" name="bp-admin[bp-disable-avatar-uploads]"<?php if ( (int)get_site_option( 'bp-disable-avatar-uploads' ) ) : ?> checked="checked"<?php endif; ?> id="bp-admin-disable-avatar-uploads-yes" value="1" /> <?php _e( 'Yes', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp-admin[bp-disable-avatar-uploads]"<?php if ( !(int)get_site_option( 'bp-disable-avatar-uploads' ) ) : ?> checked="checked"<?php endif; ?> id="bp-admin-disable-avatar-uploads-no" value="0" /> <?php _e( 'No', 'buddypress' ) ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><p><?php _e( 'Disable user account deletion?', 'buddypress' ) ?>:</p></th>
					<td>
						<input type="radio" name="bp-admin[bp-disable-account-deletion]"<?php if ( (int)get_site_option( 'bp-disable-account-deletion' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="1" /> <?php _e( 'Yes', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp-admin[bp-disable-account-deletion]"<?php if ( !(int)get_site_option( 'bp-disable-account-deletion' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="0" /> <?php _e( 'No', 'buddypress' ) ?>
					</td>
				</tr>
				<?php if ( function_exists( 'bp_forums_setup') ) : ?>
				<tr>
					<th scope="row"><p><?php _e( 'Disable global forum directory?', 'buddypress' ) ?>:</p></th>
					<td>
						<input type="radio" name="bp-admin[bp-disable-forum-directory]"<?php if ( (int)get_site_option( 'bp-disable-forum-directory' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-forum-directory" value="1" /> <?php _e( 'Yes', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp-admin[bp-disable-forum-directory]"<?php if ( !(int)get_site_option( 'bp-disable-forum-directory' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-forum-directory" value="0" /> <?php _e( 'No', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php if ( function_exists( 'bp_activity_install') ) : ?>
				<tr>
					<th scope="row"><p><?php _e( 'Disable activity stream commenting on blog and forum posts?', 'buddypress' ) ?>:</p></th>
					<td>
						<input type="radio" name="bp-admin[bp-disable-blogforum-comments]"<?php if ( (int)get_site_option( 'bp-disable-blogforum-comments' ) || false === get_site_option( 'bp-disable-blogforum-comments' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-blogforum-comments" value="1" /> <?php _e( 'Yes', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp-admin[bp-disable-blogforum-comments]"<?php if ( !(int)get_site_option( 'bp-disable-blogforum-comments' ) ) : ?> checked="checked"<?php endif; ?> id="bp-disable-blogforum-comments" value="0" /> <?php _e( 'No', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<th scope="row"><p><?php _e( 'Default User Avatar', 'buddypress' ) ?></p></th>
					<td>
						<p><?php _e( 'For users without a custom avatar of their own, you can either display a generic logo or a generated one based on their email address', 'buddypress' ) ?></p>

						<label><input name="bp-admin[user-avatar-default]" id="avatar_mystery" value="mystery" type="radio" <?php if ( get_site_option( 'user-avatar-default' ) == 'mystery' ) : ?> checked="checked"<?php endif; ?> /> &nbsp;<img alt="" src="http://www.gravatar.com/avatar/<?php md5( strtolower( $ud->user_email ) ) ?>&amp;?s=32&amp;d=<?php echo BP_PLUGIN_URL . '/bp-core/images/mystery-man.jpg' ?>&amp;r=PG&amp;forcedefault=1" class="avatar avatar-32" height="32" width="32"> &nbsp;<?php _e( 'Mystery Man', 'buddypress' ) ?></label><br>
						<label><input name="bp-admin[user-avatar-default]" id="avatar_identicon" value="identicon" type="radio" <?php if ( get_site_option( 'user-avatar-default' ) == 'identicon' ) : ?> checked="checked"<?php endif; ?> /> &nbsp;<img alt="" src="http://www.gravatar.com/avatar/<?php md5( strtolower( $ud->user_email ) ) ?>?s=32&amp;d=identicon&amp;r=PG&amp;forcedefault=1" class="avatar avatar-32" height="32" width="32"> &nbsp;<?php _e( 'Identicon (Generated)', 'buddypress' ) ?></label><br>
						<label><input name="bp-admin[user-avatar-default]" id="avatar_wavatar" value="wavatar" type="radio" <?php if ( get_site_option( 'user-avatar-default' ) == 'wavatar' ) : ?> checked="checked"<?php endif; ?> /> &nbsp;<img alt="" src="http://www.gravatar.com/avatar/<?php md5( strtolower( $ud->user_email ) ) ?>?s=32&amp;d=wavatar&amp;r=PG&amp;forcedefault=1" class="avatar avatar-32" height="32" width="32"> &nbsp;<?php _e( 'Wavatar (Generated)', 'buddypress' ) ?> </label><br>
						<label><input name="bp-admin[user-avatar-default]" id="avatar_monsterid" value="monsterid" type="radio" <?php if ( get_site_option( 'user-avatar-default' ) == 'monsterid' ) : ?> checked="checked"<?php endif; ?> /> &nbsp;<img alt="" src="http://www.gravatar.com/avatar/<?php md5( strtolower( $ud->user_email ) ) ?>?s=32&amp;d=monsterid&amp;r=PG&amp;forcedefault=1" class="avatar avatar-32" height="32" width="32"> &nbsp;<?php _e( 'MonsterID (Generated)', 'buddypress' ) ?></label>
					</td>
				</tr>

				<?php do_action( 'bp_core_admin_screen_fields' ) ?>
			</tbody>
			</table>

			<?php do_action( 'bp_core_admin_screen' ) ?>

			<p class="submit">
				<input class="button-primary" type="submit" name="bp-admin-submit" id="bp-admin-submit" value="<?php _e( 'Save Settings', 'buddypress' ) ?>"/>
			</p>

			<?php wp_nonce_field( 'bp-admin' ) ?>

		</form>
        <br />
        <br />
    <div id="credit">
    		<p><a href="http://communitypress.jessesoffice.com">CommunityPress</a> was developed with code from the following plugins. Giving credit where it's due.</p>
        <br />
            <ul>
 			 <li><a href="http://buddypress.org">Buddypress</a></li>
			  <li><a href="http://www.justin-klein.com/projects/wp-fb-autoconnect">WP-FB-AutoConnect</a></li>
			  <li><a href="http://premium.wpmudev.org/project/media-embeds-for-buddypress-activity">BuddyPress Activity Plus</a></li>
			  <li><a href="http://commons.esc.edu/damon-cook/">Buddypress Widget Pack</a></li>
			  <li><a href="http://dev.commons.gc.cuny.edu/2009/09/07/new-buddypress-plugin-enhanced-buddypress-widgets">Enhanced BuddyPress Widgets</a></li>
			  <li><a href="http://jes.milordk.ru/">Jet Event System</a></li>
 		  	 <li><a href="http://wordpress.org/extend/plugins/bp-album/">BuddyPress Album +</a></li>
 			 <li><a href="http://wordpress.org/extend/plugins/bp-moderation/">BuddyPress Moderation</a></li>
 			 <li><a href="http://wordpress.org/extend/plugins/bp-disable-activation/">BP Disable Activation</a></li>
			</ul>
    </div>

	</div>

<?php
}

function bp_core_admin_component_setup() {
	global $wpdb, $bp;
?>

	<?php
	if ( isset( $_POST['bp-admin-component-submit'] ) && isset( $_POST['bp_components'] ) ) {
		if ( !check_admin_referer('bp-admin-component-setup') )
			return false;

		// Settings form submitted, now save the settings.
		foreach ( (array)$_POST['bp_components'] as $key => $value ) {
			if ( !(int) $value )
				$disabled[$key] = 1;
		}
		update_site_option( 'bp-deactivated-components', $disabled );
	}
	?>

	<div class="wrap">
    
    <div id="donations"><p><a href="http://communitypress.jessesoffice.com">CommunityPress</a> was a <em>big</em> project. <a href="http://jessesoffice.com">I'm a 1 man show!</a> <a href="http://communitypress.jessesoffice.com/donate">Help support development</a> if you like this plugin. If you find a bug, <a href="http://communitypress.jessesoffice.com/report-bugscontact/">please report it here.</a></p></div>
    
		<h2><?php _e( 'CommuntyPress Component Setup', 'buddypress' ) ?></h2>

		<?php if ( isset( $_POST['bp-admin-component-submit'] ) ) : ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Settings Saved', 'buddypress' ) ?></p>
			</div>
		<?php endif; ?>

		<form action="" method="post" id="bp-admin-component-form">

			<p><?php _e('By default, all CommunityPress components are enabled. You can selectively disable any of the components by using the form below. Your CommunityPress installation will continue to function, however the features of the disabled components will no longer be accessible to anyone using the site.', 'buddypress' ) ?></p>

			<?php $disabled_components = get_site_option( 'bp-deactivated-components' ); ?>

			<table class="form-table" style="width: 80%">
			<tbody>
            	
				<?php /* if ( file_exists( BP_PLUGIN_DIR . '/bp-activity.php') ) : ?>
				<tr>
					<td><h3><?php _e( 'Posts and Updates', 'buddypress' ) ?></h3><p><?php _e( 'Allow users to post activity updates and track all activity across the entire site.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[bp-activity.php]" value="1"<?php if ( !isset( $disabled_components['bp-activity.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?> &nbsp;
						<input type="radio" name="bp_components[bp-activity.php]" value="0"<?php if ( isset( $disabled_components['bp-activity.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; */?>
                
				<?php if ( file_exists( BP_PLUGIN_DIR . '/bp-blogs.php') && bp_core_is_multisite() ) : ?>
				<tr>
					<td><h3><?php _e( 'Blog Tracking', 'buddypress' ) ?></h3><p><?php _e( 'Tracks blogs, blog posts and blogs comments for a user across a WPMU installation.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[bp-blogs.php]" value="1"<?php if ( !isset( $disabled_components['bp-blogs.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[bp-blogs.php]" value="0"<?php if ( isset( $disabled_components['bp-blogs.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>
                
                
                
				<?php /* if ( file_exists( BP_PLUGIN_DIR . '/bp-friends.php') ) : ?>
				<tr>
					<td><h3><?php _e( 'Friends', 'buddypress' ) ?></h3><p><?php _e( 'Allows the creation of friend connections between users.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[bp-friends.php]" value="1"<?php if ( !isset( $disabled_components['bp-friends.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[bp-friends.php]" value="0"<?php if ( isset( $disabled_components['bp-friends.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; */?>
                
                
                
				<tr>
					<td><h3><?php _e( 'Post Media', 'buddypress' ) ?></h3><p><?php _e( 'Let users post photos, videos and links in an update or wall post.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[mediapost]" value="1"<?php if ( !isset( $disabled_components['mediapost'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[mediapost]" value="0"<?php if ( isset( $disabled_components['mediapost'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>



				<tr>
					<td><h3><?php _e( 'Photos', 'buddypress' ) ?></h3><p><?php _e( 'Let users upload and tag friends in photos. Each member profile will have an album.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[photos]" value="1"<?php if ( !isset( $disabled_components['photos'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[photos]" value="0"<?php if ( isset( $disabled_components['photos'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>



				<tr>
					<td><h3><?php _e( 'Events', 'buddypress' ) ?></h3><p><?php _e( 'Let users create, join and participate in events.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[events]" value="1"<?php if ( !isset( $disabled_components['events'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[events]" value="0"<?php if ( isset( $disabled_components['events'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>



				<?php if ( file_exists( BP_PLUGIN_DIR . '/bp-groups.php') ) : ?>
				<tr style="display:none;">
					<td><h3><?php _e( 'Groups', 'buddypress' ) ?></h3><p><?php _e( 'Let users create, join and participate in groups.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[bp-groups.php]" value="1"<?php if ( !isset( $disabled_components['bp-groups.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[bp-groups.php]" value="0"<?php if ( isset( $disabled_components['bp-groups.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php if ( file_exists( BP_PLUGIN_DIR . '/bp-forums.php') ) : ?>
				<tr>
					<td><h3><?php _e( 'bbPress Forums', 'buddypress' ) ?></h3><p><?php _e( 'Activates bbPress forum support within CommunityPress groups or any other custom components.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[bp-forums.php]" value="1"<?php if ( !isset( $disabled_components['bp-forums.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[bp-forums.php]" value="0"<?php if ( isset( $disabled_components['bp-forums.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php if ( file_exists( BP_PLUGIN_DIR . '/bp-messages.php') ) : ?>
				<tr style="display:none;">
					<td><h3><?php _e( 'Private Messaging', 'buddypress' ) ?></h3><p><?php _e( 'Let users send private messages to one another. Site admins can also send site-wide notices.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[bp-messages.php]" value="1"<?php if ( !isset( $disabled_components['bp-messages.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[bp-messages.php]" value="0"<?php if ( isset( $disabled_components['bp-messages.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php /* if ( file_exists( BP_PLUGIN_DIR . '/bp-xprofile.php') ) : ?>
				<tr>
					<td><h3><?php _e( 'Extended Profiles', 'buddypress' ) ?></h3><p><?php _e( 'Activates customizable profiles and profile pictures for site users.', 'buddypress' ) ?></p></td>
					<td width="45%">
						<input type="radio" name="bp_components[bp-xprofile.php]" value="1"<?php if ( !isset( $disabled_components['bp-xprofile.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[bp-xprofile.php]" value="0"<?php if ( isset( $disabled_components['bp-xprofile.php'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
				<?php endif; */?>
                
                
                
				<tr>
					<td><h3><?php _e( 'User Privacy', 'buddypress' ) ?></h3><p><?php _e( 'Let users decide who can see their profile, and which parts.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[userprivacy]" value="1"<?php if ( !isset( $disabled_components['userprivacy'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[userprivacy]" value="0"<?php if ( isset( $disabled_components['userprivacy'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>

                
                
				<tr>
					<td><h3><?php _e( 'Facebook Connect', 'buddypress' ) ?></h3><p><?php _e( 'Allow users to sign in with their facebook credentials, and invite friends on facebook to join the site.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[fbconnect]" value="1"<?php if ( !isset( $disabled_components['fbconnect'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[fbconnect]" value="0"<?php if ( isset( $disabled_components['fbconnect'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>
                
       

				<tr>
					<td><h3><?php _e( 'Email Activation', 'buddypress' ) ?></h3><p><?php _e( 'Require users to activate their profile with an activation email.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[disableemail]" value="0"<?php if ( isset( $disabled_components['disableemail'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[disableemail]" value="1"<?php if ( !isset( $disabled_components['disableemail'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>

                
                
         
				<tr>
					<td><h3><?php _e( 'User self Moderation', 'buddypress' ) ?></h3><p><?php _e( 'Allow users to moderate inappropriate content by flagging it.', 'buddypress' ) ?></p></td>
					<td>
						<input type="radio" name="bp_components[moderation]" value="1"<?php if ( !isset( $disabled_components['moderation'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Enabled', 'buddypress' ) ?>  &nbsp;
						<input type="radio" name="bp_components[moderation]" value="0"<?php if ( isset( $disabled_components['moderation'] ) ) : ?> checked="checked" <?php endif; ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
					</td>
				</tr>

                
                
         
			</tbody>
			</table>

			<p class="submit">
				<input class="button-primary" type="submit" name="bp-admin-component-submit" id="bp-admin-component-submit" value="<?php _e( 'Save Settings', 'buddypress' ) ?>"/>
			</p>

			<?php wp_nonce_field( 'bp-admin-component-setup' ) ?>

		</form>

	</div>

<?php
}

?>