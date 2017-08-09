<?php get_header('buddypress'); ?>
		<div id="content" class="clearfix">
			<div id="content-area">			
				<div class="entry_buddypress clearfix">

		<form action="" method="post" id="groups-directory-form" class="dir-form">
			<h3><?php _e( 'Events Directory', 'jet-event-system' ) ?>&nbsp;&nbsp;</h3>
			<?php if ( is_user_logged_in() ) : 
			$jes_adata = get_site_option('jes_events' );
			$createa = $jes_adata[ 'jes_events_createnonadmin_disable' ];

		 if (!$createa )
			{ ?>
				<a class="button" href="<?php echo bp_get_root_domain() . '/' . JES_SLUG . '/create/' ?>"><?php _e( 'Create an Event', 'jet-event-system' ) ?></a>			
			<?php }
				else
			{
				if ( current_user_can('manage_options'))
						{ ?>
					<a class="button" href="<?php echo bp_get_root_domain() . '/' . JES_SLUG . '/create/' ?>"><?php _e( 'Create an Event', 'jet-event-system' ) ?></a>
					<?php	}
			}
			endif; 
			do_action( 'bp_before_directory_events_content' ) ?>

			<div id="group-dir-search" class="dir-search">
				<?php bp_directory_events_search_form() ?>
			</div><!-- #event-dir-search -->

			<div class="item-list-tabs">
				<ul>
					<li class="selected" id="events-all"><a href="<?php echo bp_get_root_domain() . '/' . JES_SLUG ?>"><?php printf( __( 'All Events (%s)', 'jet-event-system' ), bp_jes_get_jes_total_event_count() ) ?></a></li>

					<?php if ( is_user_logged_in() && bp_jes_get_jes_total_event_count_for_user( bp_loggedin_user_id() ) ) : ?>
						<li id="events-personal"><a href="<?php echo bp_loggedin_user_domain() . JES_SLUG . '/my-events/' ?>"><?php printf( __( 'My Events (%s)', 'jet-event-system' ), bp_jes_get_jes_total_event_count_for_user( bp_loggedin_user_id() ) ) ?></a></li>
					<?php endif; 
					
					do_action( 'jes_bp_events_directory_event_types' ) ?>

					<li id="events-order-select" class="last filter">
		<?php
			$sortby = $jes_adata[ 'jes_events_sort_by' ];
		
						_e( 'Order By:', 'jet-event-system' ) ?>
						<select>
							<option <?php if ($sortby == 'soon' ) { ?>selected<?php } ?> value="soon"><?php _e( 'Upcoming', 'jet-event-system' ) ?></option>
							<option <?php if ($sortby == 'active' ) { ?>selected<?php } ?> value="active"><?php _e( 'Last Active', 'jet-event-system' ) ?></option>
							<option <?php if ($sortby == 'popular' ) { ?>selected<?php } ?> value="popular"><?php _e( 'Most Members', 'jet-event-system' ) ?></option>
							<option <?php if ($sortby == 'newest' ) { ?>selected<?php } ?> value="newest"><?php _e( 'Newly Created', 'jet-event-system' ) ?></option>
							<option <?php if ($sortby == 'alphabetical' ) { ?>selected<?php } ?> value="alphabetical"><?php _e( 'Alphabetical', 'jet-event-system' ) ?></option>
							<?php do_action( 'jes_bp_events_directory_order_options' ) ?>
						</select>
					</li>
				</ul>
			</div><!-- .item-list-tabs -->

			<div id="groups-dir-list" class="events dir-list">
				<?php locate_template( array( 'events/events-loop.php' ), true ) ?>
			</div><!-- #events-dir-list -->

				<!-- Page Generate by Jet Event System for BuddyPress , http://milordk.ru/ Milordk Studio -->
				<?php if (is_admin()) { ?><span style="font-size:85%;"><a href="http://milordk.ru/projects/wordpress-buddypress/podderzhka.html"> | Admin, Donate to develop plug-in ;) -> is_admin only view ;) </a><?php } ?></span>

			<?php do_action( 'bp_directory_events_content' );
			
			wp_nonce_field( 'directory_events', '_wpnonce-events-filter' ) ?>

		</form><!-- #events-directory-form -->

		<?php do_action( 'bp_after_directory_events_content' ) ?>

				</div> <!-- end .entry -->		
			</div> <!-- end #content-area -->	
	<?php get_sidebar('buddypress'); ?>
		</div> <!-- end #content --> 
	<?php get_footer(); ?>