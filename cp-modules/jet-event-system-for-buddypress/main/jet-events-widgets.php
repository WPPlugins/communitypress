<?php
class JES_BP_Events_Widget extends WP_Widget {
	function jes_bp_events_widget() {
		parent::WP_Widget(false, $name = __( 'Events', 'jet-event-system' ) );
		if ( is_active_widget( false, false, $this->id_base ) ) {
			if (!is_admin()) {
				wp_enqueue_script( 'events_widget_events_list-js', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/widget.events.js', array('jquery') );
			}
		}
	}

	function widget($args, $instance) {
		global $bp;
	    extract( $args );

		echo $before_widget;
		echo $before_title
			. $instance['title']
			. $after_title; 
	$data = get_site_option('jes_events' );
	$show_navi = $instance['show_navi'];
	$show_type = $instance['show_type'];
	$show_countrystate = $instance['show_countrystate'];
	$showonlyadmin = $instance['showonlyadmin'];
	$archive_color = $instance['archive_color'];
	$showtime = $instance['showtime'];
	$show_avatar = $instance['show_avatar'];	
?>
<?php	global $bp;
		$_key = $bp->displayed_user->id;
		$bp->displayed_user->id = 0; ?>
<!-- Jet Event System for BuddyPress http://milordk.ru/ -->
<?php if (!$data[ 'jes_events_code_index' ]) { ?>
<!-- <noindex> -->
<?php } ?>

		<?php if ( bp_jes_has_events( 'type=soon&per_page=' . $instance['max_events'] . '&max=' . $instance['max_events'] ) ) : ?>
<?php if ($show_navi) { ?>		
			<div class="item-options" id="events-list-options">
				<span class="ajax-loader" id="ajax-loader-events"></span>
				<a href="<?php echo site_url() . '/' . $bp->jes_events->slug ?>" id="soon-events"><?php _e("Upcoming", 'jet-event-system') ?></a> |
				<a href="<?php echo site_url() . '/' . $bp->jes_events->slug ?>" id="newest-events"><?php _e("Newest", 'jet-event-system') ?></a> |
				<a href="<?php echo site_url() . '/' . $bp->jes_events->slug ?>" id="recently-active-events"><?php _e("Most Active", 'jet-event-system') ?></a> |
				<a href="<?php echo site_url() . '/' . $bp->jes_events->slug ?>" id="popular-events" class="selected"><?php _e("Most Popular", 'jet-event-system') ?></a>
			</div>
<?php }
		$kdate_now = jes_datetounix();
?>
			<ul id="events-list" class="item-list">
				<?php while ( jes_bp_events() ) : bp_jes_the_event(); ?>
<?php
		$kdate_end = jes_datetounix(jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
		if (  (int)$kdate_now > (int)$kdate_end )
			{
				$check_keydate = 1;
			}
			else
			{
				$check_keydate = 0;
			}
		$keyvisible = 1;
		if ($check_keydate and $showonlyadmin )
			{
			if ( current_user_can('manage_options') )
				{
				$keyvisible = 1;
				}
				else
				{
				$keyvisible = 0;
				}
			}
	$showeventnona = $data[ 'jes_events_adminapprove_enable' ];
		if ( !jes_bp_get_event_eventapproved() and $showeventnona ) {
		if ( current_user_can('manage_options') )
			{ 
				$keyvisible = 1;
				$shiftcan = 1;
			}
				else
			{
				$keyvisible = 0;
				$shiftcan = 0;
			} 
		} 
		if (jes_bp_event_is_admin() || jes_bp_event_is_mod() || $bp->jes_events->current_event->is_user_member)
		{
			$keyvisible = 1;		
		}
		if ($keyvisible)
			{ ?>
				<li>
					<div class="item-avatar" id="jes-avatar-<?php jes_bp_event_id() ?>">
						<?php if ($show_avatar) { ?>
							<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar_thumb() ?></a>
						<?php } ?>
<?php // JLL_MOD - fixed display of info in widget, serveral items below ?>
				<em><span style="font-size: 80%;"><?php echo eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm()); ?></span></em>
<?php if ($show_type) { ?>
<br /><span style="font-size:80%;"><?php jes_bp_event_type() ?>, <strong><?php jes_bp_event_etype() ?></strong></span>
<?php } ?>
					</div>

					<div class="item" id="jes-item--<?php jes_bp_event_id() ?>">

						<div style="font-size:85%;" id="jes-placed-<?php jes_bp_event_id() ?>">
							<?php if ( $show_countrystate ) { ?>
								<?php if ( jes_bp_get_event_placedcountry() != null ) { 
									$kkey = 1;
								?>
									<span><?php jes_bp_event_placedcountry() ?>, </span>
								<?php } ?>
								<?php if ( jes_bp_get_event_placedstate() != null ) {
									$kkey = 1;
								?>						
									<span><?php jes_bp_get_event_placedstate() ?></span>
								<?php } ?>
								<?php if ( $kkey ) { ?>
									<br />
								<?php } ?>
							<?php } ?>
								<span><strong><a href="<?php jes_bp_event_permalink() ?>" title="<?php jes_bp_event_name() ?>"><?php jes_bp_event_name() ?></a></strong><br />
<?php // JLL_MOD - fixed                                				
				$shour = jes_bp_get_event_edtsth();
				if ( $shour == '00' ) { 
					$shour = '12';
					$stime = ' am';
				} elseif ( $shour > 12 ) { 
					$shour = $shour - 12;
					$stime = ' pm';
				} elseif ( $shour < 10 ) { 
					$shour = substr( $shour, -1 );
					$stime = ' am';
				} else {
					$stime = ' am';
				}
				$ehour = jes_bp_get_event_edteth();
				if ( $ehour == '00' ) { 
					$ehour = '12';
					$etime = ' am';
				} elseif ( $ehour > 12 ) { 
					$ehour = $ehour - 12;
					$etime = ' pm';
				} elseif ( $ehour < 10 ) { 
					$ehour = substr( $ehour, -1 );
					$etime = ' am';
				} else {
					$etime = ' am';
				}
				?>		
								<?php _e('Starts: ','jet-event-system') ?> <?php jes_bp_event_edtsd() ?><?php if ($showtime) { ?>, <?php echo $shour; ?>:<?php jes_bp_event_edtstm() ?><?php echo $stime; ?><?php } ?> 
								
<?php if ( jes_bp_get_event_edtsd() ==  jes_bp_get_event_edted() && jes_bp_get_event_edtsth() ==  jes_bp_get_event_edteth() && jes_bp_get_event_edtstm() ==  jes_bp_get_event_edtetm() ) {} else { ?>
								<br /><?php _e('Ends: ','jet-event-system') ?> <?php jes_bp_event_edted() ?><?php if ($showtime) { ?>, <?php echo $ehour; ?>:<?php jes_bp_event_edtetm() ?><?php echo $etime; ?><?php } }?></span>
						</div>
					</div>
				</li>
			<?php } ?>
				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'events_widget_events_list', '_wpnonce-events' ); ?>
			<input type="hidden" name="events_widget_max" id="events_widget_max" value="<?php echo attribute_escape( $instance['max_events'] ); ?>" />
		<?php else: ?>
			<div class="widget-error">
				<?php _e('There are no events to display.', 'jet-event-system') ?>
			</div>
		<?php endif; ?>
	<?php if (!$data[ 'jes_events_code_index' ]) { ?>
		<!-- </noindex> -->
	<?php } ?>
		<?php echo $after_widget; ?>
	<?php $bp->displayed_user->id = $_key; ?>
<?php
}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_events'] = strip_tags( $new_instance['max_events'] );
		$instance['show_navi'] = strip_tags( $new_instance['show_navi'] );
		$instance['show_type'] = strip_tags( $new_instance['show_type'] );
		$instance['show_countrystate'] = strip_tags( $new_instance['show_countrystate'] );
		$instance['showonlyadmin'] = strip_tags( $new_instance['showonlyadmin'] );		
		$instance['archive_color'] = strip_tags( $new_instance['archive_color'] );
		$instance['showtime'] = strip_tags( $new_instance['showtime'] );
		$instance['show_avatar'] = strip_tags( $new_instance['show_avatar'] );
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_events' => 5 ) );
		$max_events = strip_tags( $instance['max_events'] );
		$title = strip_tags( $instance['title'] );
		$show_navi = $instance['show_navi'];
		$show_type = $instance['show_type'];
		$show_countrystate = $instance['show_countrystate'];
		$showonlyadmin = $instance['showonlyadmin'];
		$showtime = $instance['showtime'];
		$archive_color = strip_tags( $instance['archive_color'] );		
		$show_avatar = strip_tags( $instance['show_avatar'] );
		?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'jet-event-system'); ?> 
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" style="width: 30%" /></label></p>
		<p><label for="<?php echo $this->get_field_id('max_events'); ?>"><?php _e('Max events to show:', 'jet-event-system'); ?> 
		<input id="<?php echo $this->get_field_id( 'max_events' ); ?>" name="<?php echo $this->get_field_name( 'max_events' ); ?>" type="text" value="<?php echo attribute_escape( $max_events ); ?>" style="width: 30%" /></label></p>

		<p><label for="<?php echo $this->get_field_id('show_navi'); ?>"><?php _e('Show navigation:', 'jet-event-system'); ?>
		<input class="checkbox" type="checkbox" <?php if ($show_navi) {echo 'checked="checked"';} ?> id="<?php echo $this->get_field_id('show_navi'); ?>" name="<?php echo $this->get_field_name('show_navi'); ?>" value="1" /></label></p>	

		<p><label for="<?php echo $this->get_field_id('showtime'); ?>"><?php _e('Show time:', 'jet-event-system'); ?>
		<input class="checkbox" type="checkbox" <?php if ($showtime) {echo 'checked="checked"';} ?> id="<?php echo $this->get_field_id('showtime'); ?>" name="<?php echo $this->get_field_name('showtime'); ?>" value="1" /></label></p>	

		<p><label for="<?php echo $this->get_field_id('showonlyadmin'); ?>"><?php _e('Show archived events only for administrator:', 'jet-event-system'); ?>
		<input type="checkbox" <?php if ($showonlyadmin) {echo 'checked="checked"';} ?> id="<?php echo $this->get_field_id('showonlyadmin'); ?>" name="<?php echo $this->get_field_name('showonlyadmin'); ?>" value="1" /></label></p>	
		
		<p><label for="<?php echo $this->get_field_id('archive_color'); ?>"><?php _e('Color to archive events:', 'jet-event-system'); ?> <input id="<?php echo $this->get_field_id( 'archive_color' ); ?>" name="<?php echo $this->get_field_name( 'archive_color' ); ?>" type="text" value="<?php echo attribute_escape( $archive_color ); ?>" style="width: 30%" /></label></p>	

		<p><label for="<?php echo $this->get_field_id('show_avatar'); ?>"><?php _e('Show avatar:', 'jet-event-system'); ?>
		<input type="checkbox" <?php if ($show_avatar) {echo 'checked="checked"';} ?> id="<?php echo $this->get_field_id('show_avatar'); ?>" name="<?php echo $this->get_field_name('show_avatar'); ?>" value="1" /></label></p>	
<?php
	}
}

class JES_BP_ShortEvents_Widget extends WP_Widget {
	function jes_bp_shortevents_widget() {
		parent::WP_Widget(false, $name = __( 'Short Events', 'jet-event-system' ) );
		if ( is_active_widget( false, false, $this->id_base ) ) {
			if (!is_admin()) {
				wp_enqueue_script( 'events_widget_events_list-js', WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/js/widget.events.js', array('jquery') );
			}
		}
	}

	function widget($args, $instance) {
		global $bp;
	    extract( $args );

		echo $before_widget;
		echo $before_title
			. $instance['title']
			. $after_title; 
	$data = get_site_option('jes_events' );
	$show_type = $instance['show_type'];
	$showonlyadmin = $instance['showonlyadmin'];
	$archive_color = $instance['archive_color'];
?>
<?php	global $bp;
		$_key = $bp->displayed_user->id;
		$bp->displayed_user->id = 0; ?>
<!-- Jet Event System for BuddyPress http://milordk.ru/ -->
<?php if (!$data[ 'jes_events_code_index' ]) { ?>
<!-- <noindex> -->
<?php } ?>

		<?php if ( bp_jes_has_events( 'type=soon&per_page=' . $instance['max_events'] . '&max=' . $instance['max_events'] ) ) : ?>
<?php 
		$kdate_now = jes_datetounix();
?>
			<ul id="events-list" class="item-list">
				<?php while ( jes_bp_events() ) : bp_jes_the_event(); ?>
<?php
		$kdate_end = jes_datetounix(jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
		if (  (int)$kdate_now > (int)$kdate_end )
			{
				$check_keydate = 1;
			}
			else
			{
				$check_keydate = 0;
			}
		$keyvisible = 1;
		if ($check_keydate and $showonlyadmin )
			{
			if ( current_user_can('manage_options') )
				{
				$keyvisible = 1;
				}
				else
				{
				$keyvisible = 0;
				}
			}
	$showeventnona = $data[ 'jes_events_adminapprove_enable' ];
		if ( !jes_bp_get_event_eventapproved() and $showeventnona ) {
		if ( current_user_can('manage_options') )
			{ 
				$keyvisible = 1;
				$shiftcan = 1;
			}
				else
			{
				$keyvisible = 0;
				$shiftcan = 0;
			} 
		} 
		if (jes_bp_event_is_admin() || jes_bp_event_is_mod() || $bp->jes_events->current_event->is_user_member)
		{
			$keyvisible = 1;		
		}
		if ($keyvisible)
			{ ?>
				<li>
					<div class="item" id="jes-item--<?php jes_bp_event_id() ?>">

						<div style="font-size:85%;" id="jes-placed-<?php jes_bp_event_id() ?>">
								<a href="<?php jes_bp_event_permalink() ?>" title="<?php jes_bp_event_name() ?>"><?php jes_bp_event_name() ?></a>&nbsp;
								<span><br /><?php _e('Starts: ','jet-event-system') ?> <?php jes_bp_event_edtsd() ?><?php if ($showtime) { ?>, <?php jes_bp_event_edtsth() ?>:<?php jes_bp_event_edtstm() ?><br /><?php } ?> 
								
								
								<?php // JLL_MOD - fixed
								if ( jes_bp_get_event_edtsd() ==  jes_bp_get_event_edted() && jes_bp_get_event_edtsth() ==  jes_bp_get_event_edteth() && jes_bp_get_event_edtstm() ==  jes_bp_get_event_edtetm() ) {} else { ?>
								
								<br /><?php _e('Ends: ','jet-event-system') ?> <?php jes_bp_event_edted() ?><?php if ($showtime) { ?>, <?php jes_bp_event_edteth() ?>:<?php jes_bp_event_edtetm() ?><?php } }?></span>
							<?php if ($show_type) { ?>
								<br /><span style="font-size:80%;"><?php jes_bp_event_type() ?>, <strong><?php jes_bp_event_etype() ?></strong></span>
							<?php } ?>
							<br /><em><span style="font-size: 80%;"><?php echo eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm()); ?></span></em>
						</div>
					</div>
				</li>
			<?php } ?>
				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'events_widget_events_list', '_wpnonce-events' ); ?>
			<input type="hidden" name="events_widget_max" id="events_widget_max" value="<?php echo attribute_escape( $instance['max_events'] ); ?>" />
		<?php else: ?>
			<div class="widget-error">
				<?php _e('There are no events to display.', 'jet-event-system') ?>
			</div>
		<?php endif; ?>
	<?php if (!$data[ 'jes_events_code_index' ]) { ?>
		<!-- </noindex> -->
	<?php } ?>
		<?php echo $after_widget; ?>
	<?php $bp->displayed_user->id = $_key; ?>
<?php
}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_events'] = strip_tags( $new_instance['max_events'] );
		$instance['show_type'] = strip_tags( $new_instance['show_type'] );
		$instance['showonlyadmin'] = strip_tags( $new_instance['showonlyadmin'] );		
		$instance['archive_color'] = strip_tags( $new_instance['archive_color'] );
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_events' => 5 ) );
		$max_events = strip_tags( $instance['max_events'] );
		$title = strip_tags( $instance['title'] );
		$show_type = $instance['show_type'];
		$showonlyadmin = $instance['showonlyadmin'];
		$archive_color = strip_tags( $instance['archive_color'] );		
		?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'jet-event-system'); ?> 
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" style="width: 30%" /></label></p>
		<p><label for="<?php echo $this->get_field_id('max_events'); ?>"><?php _e('Max events to show:', 'jet-event-system'); ?> 
		<input id="<?php echo $this->get_field_id( 'max_events' ); ?>" name="<?php echo $this->get_field_name( 'max_events' ); ?>" type="text" value="<?php echo attribute_escape( $max_events ); ?>" style="width: 30%" /></label></p>

	
		<p><label for="<?php echo $this->get_field_id('showonlyadmin'); ?>"><?php _e('Show archived events only for administrator:', 'jet-event-system'); ?>
		<input type="checkbox" <?php if ($showonlyadmin) {echo 'checked="checked"';} ?> id="<?php echo $this->get_field_id('showonlyadmin'); ?>" name="<?php echo $this->get_field_name('showonlyadmin'); ?>" value="1" /></label></p>	
		
		<p><label for="<?php echo $this->get_field_id('archive_color'); ?>"><?php _e('Color to archive events:', 'jet-event-system'); ?> <input id="<?php echo $this->get_field_id( 'archive_color' ); ?>" name="<?php echo $this->get_field_name( 'archive_color' ); ?>" type="text" value="<?php echo attribute_escape( $archive_color ); ?>" style="width: 30%" /></label></p>	
<?php
	}
}


function events_ajax_widget_events_list() {
	global $bp;

	check_ajax_referer('events_widget_events_list');

	switch ( $_POST['filter'] ) {
		case 'newest-events':
			$type = 'newest';
		break;
		case 'recently-active-events':
			$type = 'active';
		break;
		case 'popular-events':
			$type = 'popular';
		break;
		case 'soon-events':
			$type = 'soon';
		break;		
	}

	if ( bp_jes_has_events( 'type=' . $type . '&per_page=' . $_POST['max_events'] . '&max=' . $_POST['max_events'] ) ) : ?>
		<?php echo "0[[SPLIT]]"; ?>

		<ul id="events-list" class="item-list">
			<?php while ( jes_bp_events() ) : bp_jes_the_event(); ?>
				<li>
					<div class="item-avatar" id="jes-avatar">
						<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar_thumb() ?></a>
					</div>

					<div class="item" id="jes-title">
						<div class="item-title" id="jes-event-title"><a href="<?php jes_bp_event_permalink() ?>" title="<?php jes_bp_event_name() ?>"><?php jes_bp_event_name() ?></a></div>
						<div class="item-meta" id="jes-meta">
							<span>
								<?php
								if ( 'newest-events' == $_POST['filter'] ) {
									printf( __( 'created %s ago', 'jet-event-system' ), jes_bp_get_event_date_created() );
								} else if ( 'recently-active-events' == $_POST['filter'] ) {
									printf( __( 'active %s ago', 'jet-event-system' ), jes_bp_get_event_last_active() );
								} else if ( 'popular-events' == $_POST['filter'] ) {
									jes_bp_event_member_count();
								}
								 else if ( 'soon-events' == $_POST['filter'] ) { ?>
								 <?php _e('Starts: ','jet-event-system') ?> <?php jes_bp_event_edtsd() ?><br /><?php if ($showtime) { ?>, <?php jes_bp_event_edtsth() ?>:<?php jes_bp_event_edtstm() ?><br /><?php } ?> 
								
								
								<?php // JLL_MOD - fixed
								if ( jes_bp_get_event_edtsd() ==  jes_bp_get_event_edted() && jes_bp_get_event_edtsth() ==  jes_bp_get_event_edteth() && jes_bp_get_event_edtstm() ==  jes_bp_get_event_edtetm() ) {} else { ?>
								
								<?php _e('Ends: ','jet-event-system') ?> <?php jes_bp_event_edted() ?><?php if ($showtime) { ?>, <?php jes_bp_event_edteth() ?>:<?php jes_bp_event_edtetm() ?><?php } }
								}								
								?>
							</span>
						</div>
					</div>
				</li>

			<?php endwhile; ?>
		</ul>
		<?php wp_nonce_field( 'events_widget_events_list', '_wpnonce-events' ); ?>
		<input type="hidden" name="events_widget_max" id="events_widget_max" value="<?php echo attribute_escape( $_POST['max_events'] ); ?>" />

	<?php else: ?>

		<?php echo "-1[[SPLIT]]<li>" . __("No events matched the current filter.", 'jet-event-system'); ?>

	<?php endif;
}
	add_action('widgets_init', create_function('', 'return register_widget("JES_BP_Events_Widget");') );
	add_action('widgets_init', create_function('', 'return register_widget("JES_BP_ShortEvents_Widget");') );
	add_action( 'wp_ajax_widget_events_list', 'events_ajax_widget_events_list' );
?>