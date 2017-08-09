<?php do_action( 'bp_before_event_details_admin' ); ?>

<?php $jes_adata = get_site_option('jes_events' ); ?>

<table width="100%" valign="top">
	<tr>
		<td width="49%" style="vertical-align:top;">
				<h4><?php _e('Event details','jet-event-system'); ?></h4>
					<?php jes_bp_event_description() ?>
		</td>
		<td style="vertical-align:top;">
				<h4><?php _e('Where:', 'jet-event-system') ?></h4>
	
		<?php if ( jes_bp_event_is_visible() ) { ?>
					<p><?php jes_bp_event_placedaddress() ?></p>
		<?php } else {?>	
					<p>join this event to see its location...</p>
		<?php } ?>		
	
			<h4><?php _e('When:','jet-event-system') ?></h4>
				<p><strong><?php _e('Starts', 'jet-event-system') ?>:&nbsp;&nbsp;</strong> <?php jes_bp_event_edtsd() ?>, <strong><?php _e('at','jet-event-system') ?></strong> 
				
				
				
				<?php
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
				<?php echo $shour; ?>:<?php jes_bp_event_edtstm() ?><?php echo $stime; ?><br />
			<?php if ( jes_bp_get_event_edtsd() ==  jes_bp_get_event_edted() && jes_bp_get_event_edtsth() ==  jes_bp_get_event_edteth() && jes_bp_get_event_edtstm() ==  jes_bp_get_event_edtetm() ) {} else { ?>
            <strong><?php _e('Ends', 'jet-event-system') ?>:&nbsp;&nbsp;&nbsp;&nbsp;</strong> 
			<?php jes_bp_event_edted() ?>, <strong><?php _e('at','jet-event-system') ?></strong> 
			<?php echo $ehour; ?>:<?php jes_bp_event_edtetm() ?><?php echo $etime; }?>
            </p>
	<?php	if ( $jes_adata[ 'jes_events_specialconditions_enable' ] )
				{ ?>
					<?php if ( jes_bp_get_event_eventterms() != null ) { ?>
							<h4><?php _e('Special Conditions', 'jet-event-system') ?>:</h4>
							<?php jes_bp_event_eventterms() ?>
					<?php } ?>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td width="49%" style="vertical-align:bottom;">
		<?php if ( $jes_adata[ 'jes_events_publicnews_enable' ] )
					{ ?>			
						<?php if ( jes_bp_get_event_newspublic() != null ) { ?>
								<h4><?php _e('Public Event News', 'jet-event-system') ?>:</h4>
								<p><?php jes_bp_event_newspublic() ?></p>
						<?php } ?>
				<?php } ?>		
		</td>
		<td style="vertical-align:top;">
		<?php if ( $jes_adata[ 'jes_events_privatenews_enable' ] )
					{ ?>			
						<?php if (bp_is_user_events()) { ?>
								<?php if ( jes_bp_get_event_newsprivate() != null ) { ?>
											<h4><?php _e('Private Event News', 'jet-event-system') ?>:</h4>
											<p><?php jes_bp_event_newsprivate() ?></p>
								<?php } ?>
						<?php } ?>
				<?php } ?>
		</td>
	</tr>
</table>

<?php do_action( 'bp_after_event_details_admin' ); ?>