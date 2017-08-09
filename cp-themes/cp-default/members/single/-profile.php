<?php // >>>>>----->>>>> JLL MOD <<<<<------<<<<< ?>

	<div class="item-list-tabs no-ajax" id="subnav">
		<ul>
			<?php if ( bp_is_my_profile() ) : ?>
			<?php bp_get_options_nav() ?>
            <?php else : ?>
			<li id="wall-personal-li"><a id="wall" href="<?php bp_user_link() ?>profile/wall/">Wall</a></li>
            <li id="public-personal-li" class="current selected"><a id="public" href="<?php bp_user_link() ?>profile/info/">Info</a></li>
			<?php endif; ?>
		</ul>
	</div>
    
    
<?php if ( bp_privacy_filter("info") ) : ?>
		<?php locate_template( array( 'members/single/not-friend.php' ), true ) ?>
	<?php else : ?>
    
    
    
<?php do_action( 'bp_before_profile_content' ) ?>

<div class="profile">
	<?php if ( 'edit' == bp_current_action() ) : ?>
		<?php locate_template( array( 'members/single/profile/edit.php' ), true ) ?>

	<?php elseif ( 'change-avatar' == bp_current_action() ) : ?>
		<?php locate_template( array( 'members/single/profile/change-avatar.php' ), true ) ?>

	<?php else : ?>
		<?php locate_template( array( 'members/single/profile/profile-loop.php' ), true ) ?>

	<?php endif; ?>
</div><!-- .profile -->

<?php do_action( 'bp_after_profile_content' ) ?>
<?php endif; ?>
