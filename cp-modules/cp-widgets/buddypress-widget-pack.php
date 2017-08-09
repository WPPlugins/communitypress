<?php
// JLL_MOD - Removed Plugin header


/*** Make sure BuddyPress is loaded ********************************/
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) )
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	else
		return;
}
/*******************************************************************/


/*** define the Newest Members widget*******************************/
function widget_bp_newest_members($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . 'Newest Members'
                . $after_title; ?>
			<?php
				/* look for the newest members and display a maximum of 6 
				  change the number if you like */
				if ( bp_has_members( 'type=newest&max=8' ) ) : ?>
			<?php while ( bp_members() ) : bp_the_member(); ?>
                      <ul id="members-list" class="item-list">
                        <li>
                          <div class="item-avatar">
                            <a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar('type=full&width=50&height=50') ?></a>
                          </div>
                        
                          <div class="item">
                            <div class="item-title">
                              <a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>
                              <?php if ( bp_get_member_latest_update() ) : ?>
                              <span class="update"> - <?php bp_member_latest_update( 'length=10' ) ?></span>
                              <?php endif; ?>
                            </div>
                          <div class="item-meta"><span class="activity"><?php bp_member_last_active() ?></span></div>
                          
                          <?php do_action( 'bp_directory_members_item' ) ?>
                          </div>                        
                        </li>
                      </ul>
			<?php endwhile; ?>
			<?php endif; ?>
			<div class="clear"></div>
        <?php echo $after_widget; ?>

<?php
}
/*******************************************************************/


/*** define the Popular Members widget******************************/
function widget_bp_popular_members($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . 'Popular Members'
                . $after_title; ?>
			<?php 
				/* look for the popular members and display a maximum of 6 
				  change the number if you like */
				if ( bp_has_members( 'type=popular&max=8' ) ) : ?>
					<?php while ( bp_members() ) : bp_the_member(); ?>
                      <ul id="members-list" class="item-list">
                        <li>
                          <div class="item-avatar">
                            <a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar('type=full&width=50&height=50') ?></a>
                          </div>
                        
                          <div class="item">
                            <div class="item-title">
                              <a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>
                              <?php if ( bp_get_member_latest_update() ) : ?>
                              <span class="update"> - <?php bp_member_latest_update( 'length=10' ) ?></span>
                              <?php endif; ?>
                            </div>
                          <div class="item-meta"><span class="activity"><?php bp_member_last_active() ?></span></div>
                          
                          <?php do_action( 'bp_directory_members_item' ) ?>
                          </div>                        
                        </li>
                      </ul>
					<?php endwhile; ?>
			<?php endif; ?>
			<div class="clear"></div>
        <?php echo $after_widget; ?>
<?php
}  
/*******************************************************************/


/*** define the Random Members widget******************************/
function widget_bp_random_members($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . 'Random Members'
                . $after_title; ?>
			<?php if ( bp_has_members( 'type=random&max=8' ) ) : ?>
					<?php while ( bp_members() ) : bp_the_member(); ?>
                      <ul id="members-list" class="item-list">
                        <li>
                          <div class="item-avatar">
                            <a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar('type=full&width=50&height=50') ?></a>
                          </div>
                        
                          <div class="item">
                            <div class="item-title">
                              <a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>
                              <?php if ( bp_get_member_latest_update() ) : ?>
                              <span class="update"> - <?php bp_member_latest_update( 'length=10' ) ?></span>
                              <?php endif; ?>
                            </div>
                          <div class="item-meta"><span class="activity"><?php bp_member_last_active() ?></span></div>
                          
                          <?php do_action( 'bp_directory_members_item' ) ?>
                          </div>                        
                        </li>
                      </ul>
					<?php endwhile; ?>
			<?php endif; ?>
			<div class="clear"></div>
        <?php echo $after_widget; ?>
<?php
}
/*******************************************************************/


/*** add function to show how many friends *************************/
function total_friend_count( $user_id = false ) {
		global $wpdb, $bp;
		if ( !$user_id )
			$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
		/* This is stored in 'total_friend_count' usermeta.
		   This function will recalculate, update and return. */
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->friends->table_name} WHERE (initiator_user_id = %d OR friend_user_id = %d) AND is_confirmed = 1", $user_id, $user_id ) );
		if ( !$count )
			return 0;
		update_usermeta( $user_id, 'total_friend_count', $count );
		return $count;
	}
/*** define the Total Friends widget *****************************/
function widget_bp_friends_count($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . 'Total Friends'
                . $after_title; ?>
              <div id="friendcount">
              <h4>My Total Friends (<?php echo total_friend_count( $user_id = false ) ?>)</h4>
              </div>
              <div id="friend-connections">
              <?php if ( bp_has_activities( 'object=friends' ) ) : ?>
                  <div class="pagination">
                      <div class="pag-count" id="activity-count">
                          <?php bp_activity_pagination_count() ?>
                      </div>
                      <div class="pagination-links" id="activity-pag">
                          <?php bp_activity_pagination_links() ?>
                      </div>
                  </div>
              <h4>Recent Connections</h4>
                  <ul class="activity-list"">
                  <?php while ( bp_activities() ) : bp_the_activity(); ?>
                      <li class="<?php bp_activity_css_class() ?>" >
                          <div class="activity-avatar">
                              <?php bp_activity_avatar() ?>
                          </div>
                          <?php bp_activity_content() ?>
                      </li>
                  <?php endwhile; ?>
                  </ul>
              <?php else: ?>
                  <div class="widget-error">
                      <?php _e('There has been no recent site activity.', 'buddypress') ?>
                  </div>
              <?php endif;?>
              </div>       
<?php
}
/*******************************************************************/


/*** Register all of the widgets ******************************/
register_sidebar_widget('Newest Members','widget_bp_newest_members');
register_sidebar_widget('Popular Members','widget_bp_popular_members');
register_sidebar_widget('Random Members','widget_bp_random_members');
register_sidebar_widget('Total Friends','widget_bp_friends_count');
?>