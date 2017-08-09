<?php $jes_adata = get_site_option('jes_events' ); ?>
<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php bp_event_admin_tabs(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<form action="<?php bp_event_admin_form_action() ?>" name="eventsettingsform" id="event-settings-form" class="standard-form" method="post" enctype="multipart/form-data">

<?php do_action( 'bp_before_event_admin_content' ) ?>

<?php /* Edit Event Details */ ?>
<?php if ( bp_is_event_admin_screen( 'edit-details' ) ) : ?>

	<?php do_action( 'bp_before_event_details_admin' ); ?>
<div style="clear:left;"> </div>
<table width="100%" valign="top">
	<tr>
		<td width="50%" style="vertical-align:top;">
			<h4><?php _e('Base event details','jet-event-system') ?></h4>
				<label for="event-name"><?php _e('* Event Name', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
				<input type="text" name="event-name" id="event-name" value="<?php jes_bp_event_name() ?>" />
		<?php
			$showeventnona = $jes_adata[ 'jes_events_adminapprove_enable' ];
		if ( current_user_can('manage_options') and $showeventnona)
			{ ?>
				<label for="event-eventapproved"><?php _e('Approve Event?', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
				<select name="event-eventapproved" id="event-eventapproved" size = "1">
					<option <?php if (jes_bp_get_event_eventapproved() == 1) { ?>selected<?php } ?> value="1"><?php _e('Approve','jet-event-system'); ?></option> 
					<option <?php if (jes_bp_get_event_eventapproved() == 0) { ?>selected<?php } ?> value="0"><?php _e('Not Approve','jet-event-system'); ?></option> 
				</select>
		<?php } ?>
				<label for="event-desc"><?php _e('* Event Description', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
				<textarea name="event-desc" id="event-desc"><?php jes_bp_event_description() ?></textarea>
	<?php if ($jes_adata[ 'jes_events_countryopt_enable' ])
			{ ?>
				<label for="event-placedcountry"><?php _e('* Event Country', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
				<input type="text" name="event-placedcountry" id="event-placedcountry" size="15" maxlength="25" value="<?php jes_bp_event_placedcountry() ?>" />
	<?php } else { ?>
				<input type="hidden" name="event-placedcountry" id="event-placedcountry" value="<?php $jes_adata[ 'jes_events_countryopt_def' ] ?>" />		
	<?php } ?>

	<?php if ($jes_adata[ 'jes_events_stateopt_enable' ])
			{ ?>
				<label for="event-placedstate"><?php _e('* Event State', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
				<input type="text" name="event-placedstate" id="event-placedstate" size="15" maxlength="25" value="<?php jes_bp_event_placedstate() ?>" />
	<?php } else { ?>
				<input type="hidden" name="event-placedstate" id="event-placedstate" value="<?php $jes_adata[ 'jes_events_stateopt_def' ] ?>" />		
	<?php } ?>
	<?php if ($jes_adata[ 'jes_events_cityopt_enable' ])
			{ ?>
				<label for="event-placedcity"><?php _e('* Event City', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
				<input type="text" name="event-placedcity" id="event-placedcity" value="<?php jes_bp_event_placedcity() ?>" />
	<?php } else { ?>
				<input type="hidden" name="event-placedcity" id="event-placedcity" value="<?php $jes_adata[ 'jes_events_cityopt_def' ] ?>" />		
	<?php } ?>
				<label for="event-placedaddress"><?php _e('Event address', 'jet-event-system') ?></label>
				<input type="text" name="event-placedaddress" id="event-placedaddress" size="25" maxlength="40" value="<?php jes_bp_event_placedaddress() ?>" />

	<?php if ($jes_adata[ 'jes_events_noteopt_enable' ])
			{ ?>
				<label for="event-placednote"><?php _e('Event note', 'jet-event-system') ?></label>
				<input type="text" name="event-placednote" id="event-placednote" size="25" maxlength="40" value="<?php jes_bp_event_placednote() ?>" />
		
				<input type="hidden" name="event-placednote" id="event-placednote" size="25" maxlength="40" value="<?php jes_bp_event_placednote() ?>" />
	<?php } else { ?>
				<input type="hidden" name="event-placednote" id="event-placednote" size="25" maxlength="40" value="<?php jes_bp_event_placednote() ?>" />
	<?php } ?>

	<?php if ($jes_adata[ 'jes_events_googlemapopt_enable' ])
			{
			if ($jes_adata[ 'jes_events_googlemapopt_type' ] == 'image') { ?>			
				<label for="event-placedgooglemap"><?php _e('Link to Google Maps or any other image', 'jet-event-system') ?></label>
				<input type="text" name="event-placedgooglemap" id="event-placedgooglemap" size="50" maxlength="250" value="<?php jes_bp_event_placedgooglemap() ?>" />
		<?php } else { ?>
				<input type="hidden" name="event-placedgooglemap" id="event-placedgooglemap" size="25" maxlength="40" value="<?php jes_bp_event_placedgooglemap() ?>" />
		<?php } ?>		
	<?php } else { ?>
				<input type="hidden" name="event-placedgooglemap" id="event-placedgooglemap" size="50" maxlength="250" value="<?php jes_bp_event_placedgooglemap() ?>" />
	<?php } ?>

	<?php if ($jes_adata[ 'jes_events_flyeropt_enable' ])
			{ ?>
				<label for="event-flyer"><?php _e('Link to image flyer', 'jet-event-system') ?></label>
				<input type="text" name="event-flyer" id="event-flyer" size="50" maxlength="250" value="<?php jes_bp_event_flyer() ?>" />
	<?php } else { ?>
				<input type="hidden" name="event-flyer" id="event-flyer" size="50" maxlength="250" value="<?php jes_bp_event_flyer() ?>" />
	<?php } ?>
		</td>
	</tr>
	<tr>
	<td width="40%" style="vertical-align:bottom;">
	<tr>
		<td>
        	<br />
			<h4><?php _e('Event Date','jet-event-system') ?></h4>
        </td>
     </tr>
     <tr>
     	<td>
				<table>
					<tr>
						<td width="50px">
							<label for="event-edtsd">Starts: </label>
						</td>
						<td>
								<input type="text" readonly name="event-edtsd" id="event-edtsd" size="6" maxlength="20" value="<?php bp_new_event_edtsd() ?>" />
                        </td>
                        <td>
                        <script type="text/javascript">
						
							function endDate(objDropDown) {
								if(document.eventsettingsform.edcb.checked) {
									document.getElementById('endbox').style.display="inline";
								} else {
									document.getElementById("endbox").style.display="none"
									;
								}
							}
							function endhours(objDropDown) {
								if(document.eventsettingsform.edcb.checked) {
									document.getElementById('endbox').style.display="inline";
								} else {
											  var temp = objDropDown.value;
											  if (temp == '') { return; }
											  var tmp = temp.split(':');
											  var objHidden = document.getElementById("event-edteth");
											  objHidden.value = tmp[0];
								}
							}
							function endminutes(objDropDown) {
								if(document.eventsettingsform.edcb.checked) {
									document.getElementById('endbox').style.display="inline";
								} else {
											  var temp = objDropDown.value;
											  if (temp == '') { return; }
											  var tmp = temp.split(':');
											  var objHidden = document.getElementById("event-edtetm");
											  objHidden.value = tmp[1];
								}
							}
						</script>
									<script type="text/javascript">
											function SetSHours (objDropDown) {
											  var temp = objDropDown.value;
											  if (temp == '') { return; }
											  var tmp = temp.split(':');
											  var objHidden = document.getElementById("event-edtsth");
											  objHidden.value = tmp[0];
											}
											
											function SetSMinutes (objDropDown) {
											  var temp = objDropDown.value;
											  if (temp == '') { return; }
											  var tmp = temp.split(':');
											  var objHidden = document.getElementById("event-edtstm");
											  objHidden.value = tmp[1];
											}
                                    </script>
		                                <select name="event-stime" id="event-stime" size=1 onchange="SetSHours(this);SetSMinutes(this);endhours(this);endminutes(this);">
                                        <option value=""></option>
                                        <option value="00:00">12:00 am</option>
                                        <option value="00:30">12:30 am</option>
                                        <option value="01:00">&nbsp;1:00 am</option>
                                        <option value="01:30">&nbsp;1:30 am</option>
                                        <option value="02:00">&nbsp;2:00 am</option>
                                        <option value="02:30">&nbsp;2:30 am</option>
                                        <option value="03:00">&nbsp;3:00 am</option>
                                        <option value="03:30">&nbsp;3:30 am</option>
                                        <option value="04:00">&nbsp;4:00 am</option>
                                        <option value="04:30">&nbsp;4:30 am</option>
                                        <option value="05:00">&nbsp;5:00 am</option>
                                        <option value="05:30">&nbsp;5:30 am</option>
                                        <option value="06:00">&nbsp;6:00 am</option>
                                        <option value="06:30">&nbsp;6:30 am</option>
                                        <option value="07:00">&nbsp;7:00 am</option>
                                        <option value="07:30">&nbsp;7:30 am</option>
                                        <option value="08:00">&nbsp;8:00 am</option>
                                        <option value="08:30">&nbsp;8:30 am</option>
                                        <option value="09:00">&nbsp;9:00 am</option>
                                        <option value="09:30">&nbsp;9:30 am</option>
                                        <option value="10:00">10:00 am</option>
                                        <option value="10:30">10:30 am</option>
                                        <option value="11:00">11:00 am</option>
                                        <option value="11:30">11:30 am</option>
                                        
                                        <option value="12:00">12:00 pm</option>
                                        <option value="12:30">12:30 pm</option>
                                        <option value="13:00">&nbsp;1:00 pm</option>
                                        <option value="13:30">&nbsp;1:30 pm</option>
                                        <option value="14:00">&nbsp;2:00 pm</option>
                                        <option value="14:30">&nbsp;2:30 pm</option>
                                        <option value="15:00">&nbsp;3:00 pm</option>
                                        <option value="15:30">&nbsp;3:30 pm</option>
                                        <option value="16:00">&nbsp;4:00 pm</option>
                                        <option value="16:30">&nbsp;4:30 pm</option>
                                        <option value="17:00">&nbsp;5:00 pm</option>
                                        <option value="17:30">&nbsp;5:30 pm</option>
                                        <option value="18:00">&nbsp;6:00 pm</option>
                                        <option value="18:30">&nbsp;6:30 pm</option>
                                        <option value="19:00">&nbsp;7:00 pm</option>
                                        <option value="19:30">&nbsp;7:30 pm</option>
                                        <option value="20:00">&nbsp;8:00 pm</option>
                                        <option value="20:30">&nbsp;8:30 pm</option>
                                        <option value="21:00">&nbsp;9:00 pm</option>
                                        <option value="21:30">&nbsp;9:30 pm</option>
                                        <option value="22:00">10:00 pm</option>
                                        <option value="22:30">10:30 pm</option>
                                        <option value="23:00">11:00 pm</option>
                                        <option value="23:30">11:30 pm</option>
								</select>
                                <input type="hidden" name="event-edtsth" id="event-edtsth" value="" />
                                <input type="hidden" name="event-edtstm" id="event-edtstm" value="" />
						</td>
                        <td>
                        	 &nbsp;&nbsp;&nbsp;Add end time <input type="checkbox" name="edcb" onchange="endDate(this.value);" />
                        </td>
					</tr>
				</table>
		</td>
        </tr>

        <tr id="endbox" name="endbox" style="display:none;">
		<td style="vertical-align:bottom;">
			<table>
				<tr>
					<td width="50px">
						<label for="event-edtsd">Ends: </label>
					</td>
					<td>
						<input type="text" readonly name="event-edted" id="event-edted" size="6" maxlength="20" value="<?php jes_bp_event_edted() ?>" />
                        </td>
                        <td>
									<script type="text/javascript">
											function SetEHours (objDropDown) {
											  var temp = objDropDown.value;
											  if (temp == '') { return; }
											  var tmp = temp.split(':');
											  var objHidden = document.getElementById("event-edteth");
											  objHidden.value = tmp[0];
											}
											
											function SetEMinutes (objDropDown) {
											  var temp = objDropDown.value;
											  if (temp == '') { return; }
											  var tmp = temp.split(':');
											  var objHidden = document.getElementById("event-edtetm");
											  objHidden.value = tmp[1];
											}
                                    </script>
		                                <select name="event-etime" id="event-etime" size=1 onchange="SetEHours(this);SetEMinutes(this);">
                                        <option value=""></option>
                                        <option value="00:00">12:00 am</option>
                                        <option value="00:30">12:30 am</option>
                                        <option value="01:00">&nbsp;1:00 am</option>
                                        <option value="01:30">&nbsp;1:30 am</option>
                                        <option value="02:00">&nbsp;2:00 am</option>
                                        <option value="02:30">&nbsp;2:30 am</option>
                                        <option value="03:00">&nbsp;3:00 am</option>
                                        <option value="03:30">&nbsp;3:30 am</option>
                                        <option value="04:00">&nbsp;4:00 am</option>
                                        <option value="04:30">&nbsp;4:30 am</option>
                                        <option value="05:00">&nbsp;5:00 am</option>
                                        <option value="05:30">&nbsp;5:30 am</option>
                                        <option value="06:00">&nbsp;6:00 am</option>
                                        <option value="06:30">&nbsp;6:30 am</option>
                                        <option value="07:00">&nbsp;7:00 am</option>
                                        <option value="07:30">&nbsp;7:30 am</option>
                                        <option value="08:00">&nbsp;8:00 am</option>
                                        <option value="08:30">&nbsp;8:30 am</option>
                                        <option value="09:00">&nbsp;9:00 am</option>
                                        <option value="09:30">&nbsp;9:30 am</option>
                                        <option value="10:00">10:00 am</option>
                                        <option value="10:30">10:30 am</option>
                                        <option value="11:00">11:00 am</option>
                                        <option value="11:30">11:30 am</option>
                                        
                                        <option value="12:00">12:00 pm</option>
                                        <option value="12:30">12:30 pm</option>
                                        <option value="13:00">&nbsp;1:00 pm</option>
                                        <option value="13:30">&nbsp;1:30 pm</option>
                                        <option value="14:00">&nbsp;2:00 pm</option>
                                        <option value="14:30">&nbsp;2:30 pm</option>
                                        <option value="15:00">&nbsp;3:00 pm</option>
                                        <option value="15:30">&nbsp;3:30 pm</option>
                                        <option value="16:00">&nbsp;4:00 pm</option>
                                        <option value="16:30">&nbsp;4:30 pm</option>
                                        <option value="17:00">&nbsp;5:00 pm</option>
                                        <option value="17:30">&nbsp;5:30 pm</option>
                                        <option value="18:00">&nbsp;6:00 pm</option>
                                        <option value="18:30">&nbsp;6:30 pm</option>
                                        <option value="19:00">&nbsp;7:00 pm</option>
                                        <option value="19:30">&nbsp;7:30 pm</option>
                                        <option value="20:00">&nbsp;8:00 pm</option>
                                        <option value="20:30">&nbsp;8:30 pm</option>
                                        <option value="21:00">&nbsp;9:00 pm</option>
                                        <option value="21:30">&nbsp;9:30 pm</option>
                                        <option value="22:00">10:00 pm</option>
                                        <option value="22:30">10:30 pm</option>
                                        <option value="23:00">11:00 pm</option>
                                        <option value="23:30">11:30 pm</option>
								</select>
                                <input type="hidden" name="event-edteth" id="event-edteth" value="" />
                                <input type="hidden" name="event-edtetm" id="event-edtetm" value="" />
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<label for="event-notify-members"><?php _e('Notify event participants about this update?', 'jet-event-system') ?>
			<input type="checkbox" name="event-notify-members" id="event-notify-members" value="1" /></label>
		</td>
		<td>
		</td>
	</tr>
</table>		
	<?php do_action( 'bp_after_event_details_admin' ); ?>

	<p><input type="submit" value="<?php _e( 'Save Changes', 'jet-event-system' ) ?> &rarr;" id="save" name="save" /></p>
	<?php wp_nonce_field( 'events_edit_event_details' ) ?>

<?php endif; ?>

<?php /* Manage Event Settings */ ?>
<?php if ( bp_is_event_admin_screen( 'event-settings' ) ) : ?>

	<?php do_action( 'bp_before_event_settings_admin' ); ?>

	<h4><?php _e( 'Privacy Options', 'jet-event-system' ); ?></h4>

	<div class="radio">
		<label>
			<input type="radio" name="event-status" value="public"<?php jet_bp_event_show_status_setting('public') ?> />
			<strong><?php _e( 'This is a public event', 'jet-event-system' ) ?></strong>
			<ul>
				<li><?php _e( 'Any site member can join this event.', 'jet-event-system' ) ?></li>
				<li><?php _e( 'This event will be listed in the events directory and in search results.', 'jet-event-system' ) ?></li>
				<li><?php _e( 'Event content and activity will be visible to any site member.', 'jet-event-system' ) ?></li>
			</ul>
		</label>
		<label>
			<input type="radio" name="event-status" value="private"<?php jet_bp_event_show_status_setting('private') ?> />
			<strong><?php _e( 'This is a private event', 'jet-event-system' ) ?></strong>
			<ul>
				<li><?php _e( 'Only users who request membership and are accepted can join the event.', 'jet-event-system' ) ?></li>
				<li><?php _e( 'This event will be listed in the events directory and in search results.', 'jet-event-system' ) ?></li>
				<li><?php _e( 'Event content and activity will only be visible to members of the event.', 'jet-event-system' ) ?></li>
			</ul>
		</label>
		<label>
			<input type="radio" name="event-status" value="hidden"<?php jet_bp_event_show_status_setting('hidden') ?> />
			<strong><?php _e( 'This is a hidden event', 'jet-event-system' ) ?></strong>
			<ul>
				<li><?php _e( 'Only users who are invited can join the event.', 'jet-event-system' ) ?></li>
				<li><?php _e( 'This event will not be listed in the events directory or search results.', 'jet-event-system' ) ?></li>
				<li><?php _e( 'Event content and activity will only be visible to members of the event.', 'jet-event-system' ) ?></li>
			</ul>
		</label>
	</div>
	
	<?php do_action( 'bp_after_event_settings_admin' ); ?>
	<br />
	<p><input type="submit" value="<?php _e( 'Save Changes', 'jet-event-system' ) ?> &rarr;" id="save" name="save" /></p>
	<?php wp_nonce_field( 'events_edit_event_settings' ) ?>

<?php endif; ?>

<?php /* Event Avatar Settings */ ?>
<?php if ( bp_is_event_admin_screen( 'event-avatar' ) ) : ?>

	<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

	<p><?php _e("Upload an image to use as an avatar for this event. The image will be shown on the main event page, and in search results.", 'jet-event-system') ?></p>
		<p>
			<input type="file" name="file" id="file" />
			<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'jet-event-system' ) ?>" />
			<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
		</p>

	<?php if ( bp_get_event_has_avatar() ) : ?>
		<p><?php _e( "If you'd like to remove the existing avatar but not upload a new one, please use the delete avatar button.", 'jet-event-system' ) ?></p>
		<div class="generic-button" id="delete-event-avatar-button">
			<a class="edit" href="<?php jes_bp_event_avatar_delete_link() ?>" title="<?php _e( 'Delete Avatar', 'jet-event-system' ) ?>"><?php _e( 'Delete Avatar', 'jet-event-system' ) ?></a>
		</div>
	<?php endif; ?>

			<?php wp_nonce_field( 'bp_avatar_upload' ) ?>

<?php endif; ?>

<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

	<h3><?php _e( 'Crop Avatar', 'jet-event-system' ) ?></h3>
	<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'jet-event-system' ) ?>" />

	<div id="avatar-crop-pane">
		<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'jet-event-system' ) ?>" />
	</div>

	<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'jet-event-system' ) ?>" />

	<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />

<?php wp_nonce_field( 'bp_avatar_cropstore' ) ?>

	<?php endif; ?>

<?php endif; ?>

<?php /* Manage Event Members */ ?>
<?php if ( bp_is_event_admin_screen( 'manage-members' ) ) : ?>

	<?php do_action( 'bp_before_event_manage_members_admin' ); ?>

	<div class="bp-widget">
		<h4><?php _e( 'Administrators', 'jet-event-system' ); ?></h4>
		<?php jes_bp_event_admin_memberlist( true ) ?>
	</div>

	<?php if ( jes_bp_event_has_moderators() ) : ?>

		<div class="bp-widget">
			<h4><?php _e( 'Moderators', 'jet-event-system' ) ?></h4>
			<?php jes_bp_event_mod_memberlist( true ) ?>
		</div>

	<?php endif; ?>

	<div class="bp-widget">
		<h4><?php _e("Members", "jet-event-system"); ?></h4>

		<?php if ( bp_event_jes_has_members( 'per_page=15&exclude_banned=false' ) ) : ?>

			<?php if ( bp_event_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_event_member_pagination_count() ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_event_member_admin_pagination() ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="members-list" class="item-list single-line">
				<?php while ( bp_event_members() ) : bp_event_the_member(); ?>

					<?php if ( bp_get_event_member_is_banned() ) : ?>

						<li class="banned-user">
							<?php bp_event_member_avatar_mini() ?>

							<h5><?php bp_event_member_link() ?> <?php _e( '(banned)', 'jet-event-system') ?> <span class="small"> - <a href="<?php bp_event_member_unban_link() ?>" class="confirm" title="<?php _e( 'Kick and ban this member', 'jet-event-system' ) ?>"><?php _e( 'Remove Ban', 'jet-event-system' ); ?></a> </h5>

					<?php else : ?>

						<li>
							<?php bp_event_member_avatar_mini() ?>
							<h5><?php bp_event_member_link() ?>  <span class="small"> - <a href="<?php bp_event_member_ban_link() ?>" class="confirm" title="<?php _e( 'Kick and ban this member', 'jet-event-system' ); ?>"><?php _e( 'Kick &amp; Ban', 'jet-event-system' ); ?></a> | <a href="<?php bp_event_member_promote_mod_link() ?>" class="confirm" title="<?php _e( 'Promote to Mod', 'jet-event-system' ); ?>"><?php _e( 'Promote to Mod', 'jet-event-system' ); ?></a> | <a href="<?php bp_event_member_promote_admin_link() ?>" class="confirm" title="<?php _e( 'Promote to Admin', 'jet-event-system' ); ?>"><?php _e( 'Promote to Admin', 'jet-event-system' ); ?></a></span></h5>

					<?php endif; ?>

							<?php do_action( 'bp_event_manage_members_admin_item' ); ?>
						</li>

				<?php endwhile; ?>
			</ul>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'This event has no members.', 'jet-event-system' ); ?></p>
			</div>

		<?php endif; ?>

	</div>

	<?php do_action( 'bp_after_event_manage_members_admin' ); ?>

<?php endif; ?>

<?php /* Manage Membership Requests */ ?>
<?php if ( bp_is_event_admin_screen( 'membership-requests' ) ) : ?>

	<?php do_action( 'bp_before_event_membership_requests_admin' ); ?>

	<?php if ( bp_event_jes_has_membership_requests() ) : ?>

		<ul id="request-list" class="item-list">
			<?php while ( bp_event_membership_requests() ) : bp_event_the_membership_request(); ?>

				<li>
					<?php bp_event_request_user_avatar_thumb() ?>
					<h4><?php bp_event_request_user_link() ?> <span class="comments"><?php bp_event_request_comment() ?></span></h4>
					<span class="activity"><?php bp_event_request_time_since_requested() ?></span>

					<?php do_action( 'bp_event_membership_requests_admin_item' ); ?>

					<div class="action" id="jes-accept-button">

						<div class="generic-button accept">
							<a href="<?php bp_event_request_accept_link() ?>"><?php _e( 'Accept', 'jet-event-system' ); ?></a>
						</div>

					 &nbsp;

						<div class="generic-button reject">
							<a href="<?php bp_event_request_reject_link() ?>"><?php _e( 'Reject', 'jet-event-system' ); ?></a>
						</div>

						<?php do_action( 'bp_event_membership_requests_admin_item_action' ); ?>

					</div>
				</li>

			<?php endwhile; ?>
		</ul>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'There are no pending membership requests.', 'jet-event-system' ); ?></p>
		</div>

	<?php endif; ?>

	<?php do_action( 'bp_after_event_membership_requests_admin' ); ?>

<?php endif; ?>

<?php do_action( 'events_custom_edit_steps' ) // Allow plugins to add custom event edit screens ?>

<?php /* Delete Event Option */ ?>
<?php if ( bp_is_event_admin_screen( 'delete-event' ) ) : ?>

	<?php do_action( 'bp_before_event_delete_admin' ); ?>

	<div id="message" class="info">
		<p><?php _e( 'WARNING: Deleting this event will completely remove ALL content associated with it. There is no way back, please be careful with this option.', 'jet-event-system' ); ?></p>
	</div>

	<input type="checkbox" name="delete-event-understand" id="delete-event-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-event-button').disabled = ''; } else { document.getElementById('delete-event-button').disabled = 'disabled'; }" /> <?php _e( 'I understand the consequences of deleting this event.', 'jet-event-system' ); ?>

	<?php do_action( 'bp_after_event_delete_admin' ); ?>

	<div class="submit">
		<input type="submit" disabled="disabled" value="<?php _e( 'Delete Event', 'jet-event-system' ) ?> &rarr;" id="delete-event-button" name="delete-event-button" />
	</div>

	<input type="hidden" name="event-id" id="event-id" value="<?php jes_bp_event_id() ?>" />

	<?php wp_nonce_field( 'events_delete_event' ) ?>

<?php endif; ?>

<?php /* This is important, don't forget it */ ?>
	<input type="hidden" name="event-id" id="event-id" value="<?php jes_bp_event_id() ?>" />

<?php do_action( 'bp_after_event_admin_content' ) ?>

</form><!-- #event-settings-form -->
