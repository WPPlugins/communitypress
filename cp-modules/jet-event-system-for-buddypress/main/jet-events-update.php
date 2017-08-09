<?php
//avoid direct calls to this file where wp core files not present
if ( !function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/* Update Templates from Admin Panel */
function move_template($jthemepath, $fpatch, $fname, $shownotice = 'yes')
	{
		$permisionsbug = 0;
		$stringtoreturn = ' ';
		$filename1 = WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/templates/'.$fpatch.'/'.$fname;
		$filename2 = $jthemepath . '/'.$fpatch.'/'.$fname;
		if ( file_exists($filename2) )
			{
				if (!@unlink($filename2))
					{
						$permisionsbug = 1;
					}
			}		
		if ( !copy($filename1,$filename2) )
			{
				$permisionsbug = 1;
			}
				else

			{
				$permisionsbug = 0;
			}
		if ($permisionsbug)
			{
				if ( $shownotice == 'yes' )
					{ ?>		
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/error.jpg'; ?>" alt="<?php echo $jthemepath . '/'.$fpatch.'/'.$fname; ?>">
				<?php }
				return false;
			} else {
						if ( $shownotice == 'yes' )
							{ ?>
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="<?php echo $jthemepath . '/'.$fpatch.'/'.$fname; ?>">
				<?php }
				return true;
			}
}

function update_template($jthemepath = null, $shownotice = 'yes')
	{

if ($jthemepath == null)
	{
		$jthemepath = STYLESHEETPATH;
}
	
?>
<?php if ($shownotice == 'yes')
		{ ?>
			<p>Code create folders:&nbsp;
<?php }

// Create Dir
	$mkcode = 1;
		if (!@mkdir( $jthemepath . '/events', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
				<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
			<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}
		if (!@mkdir( $jthemepath . '/events/single', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>	
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}
		if (!@mkdir( $jthemepath . '/events/looptemplates', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events/looptemplates">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>	
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events/looptemplates">
						<?php }
					}
		
		if (!@mkdir( $jthemepath . '/members', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>	
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}
		if (!@mkdir( $jthemepath . '/members/single', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>	
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}
		if (!@mkdir( $jthemepath . '/members/single/events', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>
				<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}
		if (!@mkdir( $jthemepath . '/events/css', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}	
		if (!@mkdir( $jthemepath . '/events/css/images', 0777, 1 ))
			{ 
				$mkcode = 0;
				if ($shownotice == 'yes')
					{ ?>
						<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/warning.jpg'; ?>" alt="/events">
				<?php }
			} else {
						if ($shownotice == 'yes')
							{ ?>
								<img src="<?php echo WP_PLUGIN_URL . '/communitypress/cp-modules/jet-event-system-for-buddypress/images/normal.jpg'; ?>" alt="/events">
						<?php }
					}
	if ($shownotice == 'yes')
		{
			echo '</p>';
		}

	if ($shownotice == 'yes')
		{ ?>
			<p>Code transfer Templates:&nbsp;
<?php }

// Copy templates files
	/* Events Main */
$rezult = 1;
		if (!move_template( $jthemepath,  'events','create.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events','events-loop.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events','index.php',$shownotice)) { $rezult = 0; }
	/* Events single */
		if (!move_template( $jthemepath,  'events/single','activity.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','admin.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','details.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','event-header.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','home.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','members.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','plugins.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','request-join-to-event.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','send-invites.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','google-map.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/single','flyer.php',$shownotice)) { $rezult = 0; }
/* Templates */
		if (!file_exists($jthemepath . '/events/looptemplates/custom.php'))
			{
				if (!move_template( $jthemepath,  'events/looptemplates','custom.php',$shownotice)) { $rezult = 0; }
			}
	/* Datepicker */
		if (!move_template( $jthemepath,  'events/css','datepicker.css',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_flat_0_aaaaaa_40x100.png',$shownotice)) { $rezult = 0; }		
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_flat_75_ffffff_40x100.png',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_glass_55_fbf9ee_1x400.png',$shownotice)) { $rezult = 0; }		
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_glass_65_ffffff_1x400.png',$shownotice)) { $rezult = 0; }		
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_glass_75_dadada_1x400.png',$shownotice)) { $rezult = 0; }		
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_glass_75_e6e6e6_1x400.png',$shownotice)) { $rezult = 0; }		
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_glass_95_fef1ec_1x400.png',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-bg_highlight-soft_75_cccccc_1x100.png',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-icons_2e83ff_256x240.png',$shownotice)) { $rezult = 0; }		
		if (!move_template( $jthemepath,  'events/css/images','ui-icons_222222_256x240.png',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-icons_454545_256x240.png',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-icons_888888_256x240.png',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'events/css/images','ui-icons_cd0a0a_256x240.png',$shownotice)) { $rezult = 0; }
	/* Member section */
		if (!move_template( $jthemepath,  'members/single','events.php',$shownotice)) { $rezult = 0; }
		if (!move_template( $jthemepath,  'members/single/events','invites.php',$shownotice)) { $rezult = 0; }
	if ($shownotice == 'yes')
		{ 
			echo '</p>';
		}
	update_site_option( 'jes-theme-version', JES_EVENTS_THEME_VERSION );		
	return $rezult;
}

function check_template($jthemepath = null)
	{
if ($jthemepath == null)
	{
		$jthemepath = STYLESHEETPATH;
}

	/* Events Main */
$rezult = 1;
		if (!file_exists( $jthemepath . '/events/create.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/events-loop.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/index.php')) { $rezult = 0; }
	/* Events single */
		if (!file_exists( $jthemepath . '/events/single/activity.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/admin.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/details.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/event-header.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/home.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/members.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/plugins.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/request-join-to-event.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/send-invites.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/google-map.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/single/flyer.php')) { $rezult = 0; }
/* Templates */
		if (!file_exists( $jthemepath . '/events/looptemplates/standart.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/looptemplates/standartfull.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/looptemplates/twitter.php')) { $rezult = 0; }
	/* Datepicker */
		if (!file_exists( $jthemepath .'/events/css/datepicker.css')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_flat_0_aaaaaa_40x100.png')) { $rezult = 0; }		
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_flat_75_ffffff_40x100.png')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_glass_55_fbf9ee_1x400.png')) { $rezult = 0; }		
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_glass_65_ffffff_1x400.png')) { $rezult = 0; }		
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_glass_75_dadada_1x400.png')) { $rezult = 0; }		
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_glass_75_e6e6e6_1x400.png')) { $rezult = 0; }		
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_glass_95_fef1ec_1x400.png')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-bg_highlight-soft_75_cccccc_1x100.png')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-icons_2e83ff_256x240.png')) { $rezult = 0; }		
		if (!file_exists( $jthemepath . '/events/css/images/ui-icons_222222_256x240.png')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-icons_454545_256x240.png')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-icons_888888_256x240.png')) { $rezult = 0; }
		if (!file_exists( $jthemepath . '/events/css/images/ui-icons_cd0a0a_256x240.png')) { $rezult = 0; }
	/* Member section */
		if (!file_exists( $jthemepath . '/members/single/events.php')) { $rezult = 0; }
		if (!file_exists( $jthemepath . 'members/single/events/invites.php')) { $rezult = 0; }
	return $rezult;
}

?>