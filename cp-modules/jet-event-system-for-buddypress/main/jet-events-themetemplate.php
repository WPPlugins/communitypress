<?php 
/* Themplates for Event Loop */


function jes_theme_template_standart() {
global $bp;
/**************************************/
/* Standart Template for Event Loop   */
/*                                    */
/* Base Template					  */
/**************************************/
?>
<?php
	$jes_adata = get_site_option('jes_events' );
	$_eventstatus = eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
?>
<li>
	<div class="item-avatar" id="jes-avatar">
		<?php if ($jes_adata['jes_events_show_avatar_directory_size'] > 50 ) { ?>
		<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar( 'type=full&width='.$jes_adata['jes_events_show_avatar_directory_size'].'&height='.$jes_adata['jes_events_show_avatar_directory_size'] ) ?></a>
		<?php } else { ?>
		<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar( 'type=thumb&width='.$jes_adata['jes_events_show_avatar_directory_size'].'&height='.$jes_adata['jes_events_show_avatar_directory_size'] ) ?></a>
		<?php } ?>
	</div>

	<div class="item" style="width:80%;" id="jes-templ-item-<?php jes_bp_event_id() ?>">
		<div class="item-title" id="jes-title"><a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_name() ?></a></div>			
			<div class="item-meta" id="jes-template-meta-<?php jes_bp_event_id() ?>">
				<em><?php echo $_eventstatus; ?></em> , <em><?php jes_bp_event_type() ?></em>
				<div class="item-desc" id="jes-timedate-<?php jes_bp_event_id() ?>">
					<strong>Starts:</strong> <span class="meta"><?php jes_bp_event_edtsd() ?>, 
					
									<?php
									// JLL_MOD - conert military time to 12 hour
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

					<?php echo $shour; ?>:<?php jes_bp_event_edtstm() ?><?php echo $stime; ?></span>
                    <?php if ( jes_bp_get_event_edtsd() ==  jes_bp_get_event_edted() && jes_bp_get_event_edtsth() ==  jes_bp_get_event_edteth() && jes_bp_get_event_edtstm() ==  jes_bp_get_event_edtetm() ) {} else { ?>
                    <strong>Ends:</strong> <span><?php jes_bp_event_edted() ?>, <?php echo $ehour; ?>:<?php jes_bp_event_edtetm() ?><?php echo $etime; ?></span><?php }; ?>
				</div>
			<div class="item-desc" id="jes-desc-<?php jes_bp_event_id() ?>">
				<span><strong>Where: </strong> <?php // JLL_MOD - removed  ?><?php if ( jes_bp_event_is_visible() ) { ?><?php jes_bp_event_placedaddress() ?> <?php } ?></span><br />
			</div>
		<?php if ($jes_adata['jes_events_style'] == 'Standart') { ?>
			<?php // JLL_MOD - Removed text ?><?php jes_bp_event_description_excerpt() ?>
		<?php } else { ?>
			<?php jes_bp_event_description() ?>
		<?php } ?>
			</div>				
<?php do_action( 'bp_directory_events_item' ) ?>
		</div>
		<div class="action" id="jes-button">
			<?php 
				if (!strpos($_eventstatus,__('Past event','jet-event-system')))
					{
						bp_event_join_button();
					}
			?>
				<div class="meta" id="jes-approval-<?php jes_bp_event_id() ?>">
					<?php if ( $shiftcan ) 
							{ ?>
								<span class="meta"><em><?php _e('Event requires approval!','jet-event-system'); ?></em></span>
					<?php }	?>
					<?php // JLL_MOD - removed event classification ?>
					<p><?php jes_bp_event_member_count() ?></p>
					<span class="activity"><?php printf( __( 'Last activity:<br /> %s ago', 'jet-event-system' ), jes_bp_get_event_last_active() ) ?></span>
				</div>
<?php do_action( 'bp_directory_events_actions' ) ?>
			</div>
		<div class="clear"></div>
</li>
<?php
// End Standart Template
}
?>
<?php
function jes_theme_template_calendar() {
global $bp;
/**************************************/
/* Calendar Template for Event Loop   */
/*                                    */
/* Base Template					  */
/**************************************/
?>

<?php

	$nowds = jes_datetounix(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm());
	$nowde = jes_datetounix(jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
	$s_year = date('Y', $nowds );
	$s_month = date('m', $nowds);
	$s_days = date('d', $nowds);

	$e_year = date('Y', $nowde );
	$e_month = date('m', $nowde);
	$e_days = date('d', $nowde);
	
	$eventtitle = jes_bp_get_event_name();
?>	
    {
	id: "<?php jes_bp_event_id() ?>",
	title: "<?php echo $eventtitle; ?>",
	start: new Date(<?php echo $s_year ?>, <?php echo $s_month ?>-1, <?php echo $s_days; ?>),
	end: new Date(<?php echo $e_year ?>, <?php echo $e_month ?>-1, <?php echo $e_days; ?>),
	url: '<?php jes_bp_event_permalink() ?>'
    },
<?php
	$jes_adata = get_site_option('jes_events' );
	$_eventstatus = eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
?>
<?php
// End Calendar Template
}
?>


<?php
function jes_theme_template_standartfull() {
global $bp;
/**************************************/
/* Standart Full Template for Event Loop   */
/*                                    */
/* Base Template					  */
/**************************************/
?>
<?php
	$jes_adata = get_site_option('jes_events' );
	$_eventstatus = eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
?>
<li>
	<div class="item-avatar">
		<?php if ($jes_adata['jes_events_show_avatar_directory_size'] > 50 ) { ?>
		<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar( 'type=full&width='.$jes_adata['jes_events_show_avatar_directory_size'].'&height='.$jes_adata['jes_events_show_avatar_directory_size'] ) ?></a>
		<?php } else { ?>
		<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar( 'type=thumb&width='.$jes_adata['jes_events_show_avatar_directory_size'].'&height='.$jes_adata['jes_events_show_avatar_directory_size'] ) ?></a>
		<?php } ?>
	</div>

	<div class="item" style="width:80%;" id="jes-templ-item-<?php jes_bp_event_id() ?>">
		<span style="font-size:80%;"><a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_name() ?></a> 
		<em><?php echo $_eventstatus; ?></em>				
		<em><?php jes_bp_event_type() ?></em>, <strong><?php jes_bp_event_etype() ?></strong>
			<br />
		<?php _e('From: ','jet-event-system') ?><span class="meta"><?php jes_bp_event_edtsd() ?>, <?php jes_bp_event_edtsth() ?>:<?php jes_bp_event_edtstm() ?></span> <?php _e('to: ','jet-event-system') ?> <?php jes_bp_event_edted() ?>, <?php jes_bp_event_edteth() ?>:<?php jes_bp_event_edtetm() ?>
		<?php _e('The event will take place:','jet-event-system'); 
		if ( $jes_adata[ 'jes_events_countryopt_enable' ] )
			{
				jes_bp_event_placedcountry(); ?> ,
		<?php }
		if ( $jes_adata[ 'jes_events_stateopt_enable' ] )
			{
				jes_bp_event_placedstate(); ?> ,
		<?php } ?>
		<?php _e('in city:','jet-event-system') ?> <?php jes_bp_event_placedcity() ?><?php if ( jes_bp_event_is_visible() ) { ?>, <?php _e('at ','jet-event-system') ?> <?php jes_bp_event_placedaddress() ?> <?php } ?><br />

		<?php _e('Description:','jet-event-system') ?> <?php jes_bp_event_description() ?>	
	</span>					
<?php do_action( 'bp_directory_events_item' ) ?>
	</div>
	<div class="action">
			<?php 
				if (!strpos($_eventstatus,__('Past event','jet-event-system')))
					{
						bp_event_join_button();
					}
			?>
		<div class="meta" id="jes-template-meta-<?php jes_bp_event_id() ?>">
			<?php if ( $shiftcan ) 
					{ ?>
						<span class="meta"><em><?php _e('Event requires approval!','jet-event-system'); ?></em></span>
			<?php }	?>
				<br />
			<?php jes_bp_event_member_count() ?>
			<span class="activity"><?php printf( __( 'Last activity:<br /> %s ago', 'jet-event-system' ), jes_bp_get_event_last_active() ) ?></span>
		</div>
<?php do_action( 'bp_directory_events_actions' ) ?>
	</div>
	<div class="clear"></div>
</li>
<?php
// End Template Standart Full
}
?>
<?php
function jes_theme_template_twitter() {
global $bp;
/**************************************/
/* Twitter Template for Event Loop   */
/*                                    */
/* Base Template 					  */
/**************************************/
?>
<?php
	$jes_adata = get_site_option('jes_events' );
	$_eventstatus = eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
?>
	<li>
		<div class="item-avatar">
			<a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_avatar( 'type=thumb&width=25&height=25' ) ?></a>
		</div>

		<div class="item" style="width:80%;" id="jes-templ-item-<?php jes_bp_event_id() ?>">
			<span style="font-size:80%;"><a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_name() ?></a> 
			<em><?php echo $_eventstatus; ?></em>				
			<em><?php jes_bp_event_type() ?></em>, <strong><?php jes_bp_event_etype() ?></strong>
				<br />
			<?php _e('From: ','jet-event-system') ?><span class="meta"><?php jes_bp_event_edtsd() ?>, <?php jes_bp_event_edtsth() ?>:<?php jes_bp_event_edtstm() ?></span> <?php _e('to: ','jet-event-system') ?> <?php jes_bp_event_edted() ?>, , <?php jes_bp_event_edteth() ?>:<?php jes_bp_event_edtetm() ?>
			<?php _e('The event will take place:','jet-event-system');
			if ( $jes_adata[ 'jes_events_countryopt_enable' ] )
				{
					jes_bp_event_placedcountry(); ?> ,
			<?php }
			if ( $jes_adata[ 'jes_events_stateopt_enable' ] )
				{
					jes_bp_event_placedstate(); ?> ,
			<?php }
			_e('in city:','jet-event-system') ?> <?php jes_bp_event_placedcity() ?><?php if ( jes_bp_event_is_visible() ) { ?>, <?php _e('at ','jet-event-system') ?><?php jes_bp_event_placedaddress() ?> <?php } ?><br />				
			<?php _e('Description:','jet-event-system') ?> <?php jes_bp_event_description() ?>	
	</span>					
<?php do_action( 'bp_directory_events_item' ) ?>
		</div>
	<div class="action">
			<?php 
				if (!strpos($_eventstatus,__('Past event','jet-event-system')))
					{
						bp_event_join_button();
					}
			?>
		<div class="meta" id="jes-template-meta-<?php jes_bp_event_id() ?>">
			<?php if ( $shiftcan ) 
					{ ?>
						<span class="meta"><em><?php _e('Event requires approval!','jet-event-system'); ?></em></span>
			<?php }	?>
				<br />
			<?php jes_bp_event_member_count() ?>
				<span class="activity"><?php printf( __( 'Last activity:<br /> %s ago', 'jet-event-system' ), jes_bp_get_event_last_active() ) ?></span>
		</div>
<?php do_action( 'bp_directory_events_actions' ) ?>
	</div>
	<div class="clear"></div>
</li>
<?php
// End Template Twitter
}
?>