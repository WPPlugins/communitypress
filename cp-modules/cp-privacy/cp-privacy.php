<?php
// JLL_MOD - Adds User Privacy
function bp_core_add_privacy_nav() {
	global $bp;

	/* Set up settings as a sudo-component for identification and nav selection */
	$settings_link = $bp->loggedin_user->domain . $bp->settings->slug . '/';

	bp_core_new_subnav_item( array( 'name' => __( 'Privacy', 'buddypress' ), 'slug' => 'privacy', 'parent_url' => $settings_link, 'parent_slug' => $bp->settings->slug, 'screen_function' => 'bp_core_screen_privacy_settings', 'position' => 30, 'user_has_access' => bp_is_my_profile() ) );

}
add_action( 'bp_setup_nav', 'bp_core_add_privacy_nav' );


/***** PRIVACY SETTINGS ******/

function bp_core_screen_privacy_settings() {
	global $current_user, $bp_settings_updated;

	$bp_settings_updated = false;

	if ( $_POST['submit'] ) {
		check_admin_referer('bp_settings_privacy');

		if ( $_POST['privacy'] ) {
			foreach ( (array)$_POST['privacy'] as $key => $value ) {
				update_user_meta( (int)$current_user->id, $key, $value );
			}
		}

		$bp_settings_updated = true;
	}

	add_action( 'bp_template_title', 'bp_core_screen_privacy_settings_title' );
	add_action( 'bp_template_content', 'bp_core_screen_privacy_settings_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_core_screen_privacy_settings_title() {
//	_e( 'Privacy', 'buddypress' );
}

function bp_core_screen_privacy_settings_content() {
	global $bp, $current_user, $bp_settings_updated;
	
global $wpdb, $bp;
$disabled_components = get_site_option( 'bp-deactivated-components' ); ?>

	<?php if ( $bp_settings_updated ) { ?>
		<div id="message" class="updated fade">
			<p><?php _e( 'Changes Saved.', 'buddypress' ) ?></p>
		</div>
	<?php } ?>

	<form action="<?php echo $bp->loggedin_user->domain . BP_SETTINGS_SLUG . '/privacy' ?>" method="post" id="privacy-form">
    
		<h3><?php _e( 'Member Privacy', 'buddypress' ) ?></h3>
		<p><?php _e( 'Control who can see your profile:', 'buddypress' ) ?></p>

<table class="privacy-settings zebra" id="privacy-settings">
 <thead>
  <tr>
	<th class="title"><?php _e( 'Who can see:', 'buddypress' ) ?></th>
	<th class="public"><?php _e( 'Everyone', 'buddypress' ) ?></th>
	<th class="friendsonly"><?php _e( 'Friends Only', 'buddypress' )?></th>
  </tr>
 </thead>
 <tbody>
  <tr>
    <td><?php _e( 'Your Wall posts and status', 'buddypress' ) ?></td>
    <td class="public"><input type="radio" name="privacy[privacy_wall]" value="public" <?php if ( !get_user_meta( $current_user->id, 'privacy_wall', true ) || 'public' == get_user_meta( $current_user->id, 'privacy_wall', true ) ) { ?>checked="checked" <?php } ?>/></td>
    <td class="friendsonly"><input type="radio" name="privacy[privacy_wall]" value="friendsonly" <?php if ( 'friendsonly' == get_user_meta( $current_user->id, 'privacy_wall', true ) ) { ?>checked="checked" <?php } ?>/></td>
  </tr>
  <tr>
    <td><?php _e( 'Your Bio and Information', 'buddypress' ) ?></td>
    <td class="public"><input type="radio" name="privacy[privacy_info]" value="public" <?php if ( !get_user_meta( $current_user->id, 'privacy_info', true ) || 'public' == get_user_meta( $current_user->id, 'privacy_info', true ) ) { ?>checked="checked" <?php } ?>/></td>
    <td class="friendsonly"><input type="radio" name="privacy[privacy_info]" value="friendsonly" <?php if ( 'friendsonly' == get_user_meta( $current_user->id, 'privacy_info', true ) ) { ?>checked="checked" <?php } ?>/></td>
  </tr>
  
<?php if ( !isset( $disabled_components['photos'] ) ) { ?>
  <tr>
    <td><?php _e( 'Your Photo Albums', 'buddypress' ) ?></td>
    <td class="public"><input type="radio" name="privacy[privacy_photos]" value="public" <?php if ( !get_user_meta( $current_user->id, 'privacy_photos', true ) || 'public' == get_user_meta( $current_user->id, 'privacy_photos', true ) ) { ?>checked="checked" <?php } ?>/></td>
    <td class="friendsonly"><input type="radio" name="privacy[privacy_photos]" value="friendsonly" <?php if ( 'friendsonly' == get_user_meta( $current_user->id, 'privacy_photos', true ) ) { ?>checked="checked" <?php } ?>/></td>
  </tr>
<?php } ?>  
  <tr>
    <td><?php _e( 'Your Friend Connections', 'buddypress' ) ?></td>
    <td class="public"><input type="radio" name="privacy[privacy_friends]" value="public" <?php if ( !get_user_meta( $current_user->id, 'privacy_friends', true ) || 'public' == get_user_meta( $current_user->id, 'privacy_friends', true ) ) { ?>checked="checked" <?php } ?>/></td>
    <td class="friendsonly"><input type="radio" name="privacy[privacy_friends]" value="friendsonly" <?php if ( 'friendsonly' == get_user_meta( $current_user->id, 'privacy_friends', true ) ) { ?>checked="checked" <?php } ?>/></td>
  </tr>
<?php if ( !isset( $disabled_components['bp-groups.php'] ) ) { ?>
  <tr>
    <td><?php _e( 'Your Groups you\'ve joined', 'buddypress' ) ?></td>
    <td class="public"><input type="radio" name="privacy[privacy_groups]" value="public" <?php if ( !get_user_meta( $current_user->id, 'privacy_groups', true ) || 'public' == get_user_meta( $current_user->id, 'privacy_groups', true ) ) { ?>checked="checked" <?php } ?>/></td>
    <td class="friendsonly"><input type="radio" name="privacy[privacy_groups]" value="friendsonly" <?php if ( 'friendsonly' == get_user_meta( $current_user->id, 'privacy_groups', true ) ) { ?>checked="checked" <?php } ?>/></td>
  </tr>
  <?php } ?>  
<?php if ( !isset( $disabled_components['events'] ) ) { ?>
  <tr>
    <td><?php _e( 'Your Events you\'ve joined', 'buddypress' ) ?></td>
    <td class="public"><input type="radio" name="privacy[privacy_events]" value="public" <?php if ( !get_user_meta( $current_user->id, 'privacy_events', true ) || 'public' == get_user_meta( $current_user->id, 'privacy_events', true ) ) { ?>checked="checked" <?php } ?>/></td>
    <td class="friendsonly"><input type="radio" name="privacy[privacy_events]" value="friendsonly" <?php if ( 'friendsonly' == get_user_meta( $current_user->id, 'privacy_events', true ) ) { ?>checked="checked" <?php } ?>/></td>
  </tr>
<?php } ?>  

 <tbody>
</table>
		<div class="submit">
			<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ) ?>" id="submit" class="auto" />
		</div>

		<?php wp_nonce_field('bp_settings_privacy') ?>

	</form>
<?php
}
