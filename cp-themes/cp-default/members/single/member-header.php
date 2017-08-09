<?php do_action( 'bp_before_member_header' ) ?>

<div id="item-header-avatar">
	<a href="<?php bp_user_link() ?>">
		<?php bp_displayed_user_avatar( 'type=full' ) ?>
	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<h2 class="fn"><a href="<?php bp_user_link() ?>"><?php bp_displayed_user_fullname() ?></a> </h2>
	<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ) ?></span>

	<?php do_action( 'bp_before_member_header_meta' ) ?>


	<?php if ( bp_privacy_filter("wall") ) { ?>
    <?php } else { ?>
       	<div id="item-meta-jll">
		<?php if ( function_exists( 'bp_activity_latest_update' ) ) : ?>
			<div id="latest-update">
				<?php echo jll_get_activity_latest_update_no_view( bp_displayed_user_id() ) ?>
			</div>
		<?php endif; ?>
	<?php } ?>	

		<div id="item-buttons">
			<?php if ( function_exists( 'bp_add_friend_button' ) ) : ?>
				<?php bp_add_friend_button() ?>
			<?php endif; ?>
		
          
			<?php if ( is_user_logged_in() && !bp_is_my_profile() && function_exists( 'bp_send_private_message_link' ) ) : ?>
				<div class="generic-button" id="send-private-message">
					<a href="<?php bp_send_private_message_link() ?>" title="<?php _e( 'Send a private message to this user.', 'buddypress' ) ?>"><?php _e( 'Send Private Message', 'buddypress' ) ?></a>
				</div>
			<?php endif; ?>
		</div><!-- #item-buttons -->

		<?php
		 /***
		  * If you'd like to show specific profile fields here use:
		  * bp_profile_field_data( 'field=About Me' ); -- Pass the name of the field
		  */
		?>

		<?php do_action( 'bp_profile_header_meta' ) ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<?php do_action( 'bp_after_member_header' ) ?>

<?php do_action( 'template_notices' ) ?>






<?php $loggedinuser = '<a href="' . $bp->loggedin_user->domain . '" title="' . $bp->loggedin_user->fullname . '">' . $bp->loggedin_user->fullname . '</a>'; ?>
