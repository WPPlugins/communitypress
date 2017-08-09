<?php // >>>>>----->>>>> JLL CUSTOM PHP PAGE <<<<<------<<<<< ?>
<?php global $bp; bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, 'activity', 'new_at_mention' ); ?>

<?php do_action( 'bp_before_member_activity_post_form' ) ?>

<?php if ( is_user_logged_in()&&( '' == bp_current_action() || 'wall' == bp_current_action() ) ) : ?>
<?php locate_template( array( 'activity/post-form.php'), true ) ?>
<?php endif; ?>

<?php do_action( 'bp_after_member_activity_post_form' ) ?>
<?php do_action( 'bp_before_member_activity_content' ) ?>

<div class="activity">
	<?php locate_template( array( 'members/single/profile/wall-loop.php' ), true ) ?>
</div><!-- .activity -->

<?php do_action( 'bp_after_member_activity_content' ) ?>
