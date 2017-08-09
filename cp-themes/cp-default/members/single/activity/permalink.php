	<?php get_header('buddypress'); ?>
		<div id="content" class="clearfix">
			<div id="content-area">			
				<div class="entry_buddypress clearfix">

<div class="activity no-ajax">
	<?php if ( bp_has_activities( 'display_comments=threaded&include=' . bp_current_action() ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">
		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php locate_template( array( 'activity/entry.php' ), true ) ?>

		<?php endwhile; ?>
		</ul>

	<?php endif; ?>
</div>

				</div> <!-- end .entry -->		
			</div> <!-- end #content-area -->	
	<?php get_sidebar('buddypress'); ?>
		</div> <!-- end #content --> 
	<?php get_footer(); ?>
