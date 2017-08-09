	<?php get_header('buddypress'); ?>
		<div id="content" class="clearfix">
			<div id="content-area">			
				<div class="entry_buddypress clearfix">
	<?php do_action( 'bp_before_directory_blogs_content' ) ?>

		<?php do_action( 'template_notices' ) ?>

		<h3><?php _e( 'Create a Blog', 'buddypress' ) ?> &nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . BP_BLOGS_SLUG . '/' ?>"><?php _e( 'Blogs Directory', 'buddypress' ) ?></a></h3>

		<?php do_action( 'bp_before_create_blog_content' ) ?>

		<?php if ( bp_blog_signup_enabled() ) : ?>

			<?php bp_show_blog_signup_form() ?>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'Blog registration is currently disabled', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>

		<?php do_action( 'bp_after_create_blog_content' ) ?>
	<?php do_action( 'bp_after_directory_blogs_content' ) ?>

				</div> <!-- end .entry -->		
			</div> <!-- end #content-area -->	
	<?php get_sidebar('buddypress'); ?>
		</div> <!-- end #content --> 
	<?php get_footer(); ?>

