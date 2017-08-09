<?php do_action( 'bp_before_event_header' ) ?>
<?php
	$_eventstatus = eventstatus(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm(),jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm());
	$jes_adata = get_site_option('jes_events' );	
?>
<div id="item-header-avatar">
	<a href="<?php jes_bp_event_permalink() ?>" title="<?php jes_bp_event_name() ?>">
		<?php jes_bp_event_avatar('height='.$jes_adata['jes_events_show_avatar_main_size'].'&width='.$jes_adata['jes_events_show_avatar_main_size']) ?>
	</a><br />
</div><!-- #item-header-avatar -->
	<h3><a href="<?php jes_bp_event_permalink() ?>" title="<?php jes_bp_event_name() ?>"><?php jes_bp_event_name() ?></a></h3>
<!-- Page Generate by Jet Event System for BP , http://milordk.ru Milordk Studio -->
	<div id="item-actions">

	<?php if ( jes_bp_event_is_visible() ) : ?>

		<h3><?php _e( 'Event Admins', 'jet-event-system' ) ?></h3>
		<?php jes_bp_event_list_admins() ?>

		<?php do_action( 'bp_after_event_menu_admins' ) ?>

		<?php if ( jes_bp_event_has_moderators() ) : ?>
			<?php do_action( 'bp_before_event_menu_mods' ) ?>

			<h3><?php _e( 'Event Mods' , 'jet-event-system' ) ?></h3>
			<?php jes_bp_event_list_mods() ?>

			<?php do_action( 'bp_after_event_menu_mods' ) ?>
		<?php endif; ?>

	<?php endif; ?>
</div><!-- #item-actions -->


<div id="item-header-content">
	<p><span class="highlight"><?php jes_bp_event_type() ?></span> - <span class="activity"><?php printf( __( 'active %s ago', 'jet-event-system' ), jes_bp_get_event_last_active() ) ?></span>
	<?php // JLL_MOD - removed event classification ?></p>
<?php 
/* Add to Outlook / iPhone Calendar */
if (bp_event_is_member()) { ?>
	<script type="text/javascript">
	<!--
		function onChoseOutlook(form)
			{
				document.jes_send_calendar.jes_send_type.value = 'Outlook';
				return true;
			}
		function onChoseiPhone(form)
			{
				document.jes_send_calendar.jes_send_type.value = 'iPhone';
				return true;
			}
	//-->
	</script>
<?php } ?>

	<div id="eventstyle" style="padding-top: 5px;">
	<form name="jes_send_calendar" action="<?php echo WP_PLUGIN_URL ?>/communitypress/cp-modules/jet-event-system-for-buddypress/tosend/calendar.php" method="post">
			<input type="hidden" name="jes_send_type" value="">
			<input type="hidden" name="jes-send-eventname" value="<?php jes_bp_event_name() ?>">
			<input type="hidden" name="jes-send-eventslug" value="<?php jes_bp_event_slug() ?>">
			<input type="hidden" name="jes-send-url" value="<?php jes_bp_event_permalink() ?>">
			<input type="hidden" name="jes-send-eventdesc" value="<?php jes_bp_event_description() ?>">
			<input type="hidden" name="jes-send-unixsd" value="<?php echo (jes_datetounix(jes_bp_get_event_edtsd(),jes_bp_get_event_edtsth(),jes_bp_get_event_edtstm())-jes_offset()) ?>">
			<input type="hidden" name="jes-send-unixed" value="<?php echo (jes_datetounix(jes_bp_get_event_edted(),jes_bp_get_event_edteth(),jes_bp_get_event_edtetm())-jes_offset()); ?>">
			<input type="hidden" name="jes-send-placed" value="<?php if ( $jes_adata[ 'jes_events_countryopt_enable' ] ) { jes_bp_event_placedcountry(); ?>, <?php } ?><?php if ( $jes_adata[ 'jes_events_stateopt_enable' ] ) { jes_bp_event_placedstate(); ?>, <?php } jes_bp_event_placedaddress(); ?>, <?php jes_bp_event_placednote(); ?>">
<?php if (bp_event_is_member() && jes_bp_get_event_enablesocial()) { ?>		
<?php echo $jes_adata['jes_events_social_code']; ?>
<?php } ?>
		<?php if ($jes_adata['jes_events_defavatar_iphone'] != null) { ?>
			<input name="jes-send-outlook" class="eventstyle" type="image" value="iPhone" src="<?php echo $jes_adata['jes_events_defavatar_iphone']; ?>" onClick="return onChoseOutlook(this.form)">Outlook
		<?php } else { ?>
			<input name="jes-send-outlook" class="eventstyle" type="image" value="iPhone" src="<?php echo WP_PLUGIN_URL ?>/communitypress/cp-modules/jet-event-system-for-buddypress/images/outlook.png" onClick="return onChoseOutlook(this.form)">-Outlook 
		<?php } ?>
		<?php if ($jes_adata['jes_events_defavatar_outlook'] != null) { ?>
			<input name="jes-send-iphone" class="eventstyle" type="image" value="Outlook" src="<?php echo $jes_adata['jes_events_defavatar_outlook']; ?>" onClick="return onChoseiPhone(this.form)">iPhone
		<?php } else { ?>
			<input name="jes-send-iphone" class="eventstyle" type="image" value="Outlook" src="<?php echo WP_PLUGIN_URL ?>/communitypress/cp-modules/jet-event-system-for-buddypress/images/iphone.png" onClick="return onChoseiPhone(this.form)">-iPhone 
		<?php } ?>
		</form>
	</div>

	<?php do_action( 'bp_before_event_header_meta' ) ?>

	<div id="item-meta">
			<?php 
				if (!strpos($_eventstatus,__('Past event','jet-event-system')))
					{
						bp_event_join_button();
					}
					else
					{ ?>
						<div id="message" class="info">
							<p><?php echo sprintf(__('You can not join or refuse to participate in an event as this - %s','jet-event-system'),$_eventstatus); ?></p>
						</div>
			<?php	}
			?>
		<?php do_action( 'bp_event_header_meta' ) ?>
	</div>
<?php
/*	if (jes_bp_event_forumlink())
		{
if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=1&populate_extras=0' ) ) : while ( bp_groups() ) : bp_the_group();		
		?>
	   		<option value="<?php bp_group_id() ?>" <?php if ( $grp_id == bp_get_group_id() ) { echo "selected"; } ?>><?php bp_group_name()?></option>
<?php		endwhile; endif; ?>
 }*/ ?>

</div><!-- #item-header-content -->

<?php do_action( 'bp_after_event_header' ) ?>

<?php do_action( 'template_notices' ) ?>