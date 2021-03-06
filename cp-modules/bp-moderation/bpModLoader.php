<?php
// JLL_MOD - Removed Plugin header

//load the plugin
bpModLoader::bootstrap();

/**
 * Loader for bp-moderation
 */
class bpModLoader {

	/**
	 * hook the plugin in buddypress and in activation/deactivation
	 */
	function bootstrap(){
		//bp_init is from bp 1.2 and bp 1.2 require wp 2.9, so no need to check compatibility for now
		add_action( 'bp_init', array( __CLASS__, 'init' ) );

		register_activation_hook( __FILE__, array( __CLASS__, 'call_activate' ) );

		register_deactivation_hook( __FILE__, array( __CLASS__, 'call_deactivate' ) );

		//uninstall hook ??
		}

	/**
	 * Load needed class
	 */
	function init() {
		
		if(is_admin())
		{
			// if this is an admin page and the curren user is not a site admin then the plugin don't load at all
			if( !is_super_admin() )
				return;
				
		// JLL_MOD - fixed so tables do install	
		$installer = bpModLoader::load_class('bpModInstaller');
		$installer = new bpModInstaller();
		$installer->activate();
			
			if( !empty($_REQUEST['bpmod-action']))
				// backend request: only bpModBackend is needed
				$mainclass = 'bpModBackendActions';
			else
				// backend page: only bpModBackend is needed
				$mainclass = 'bpModBackend';

		}
		elseif( !empty($_REQUEST['bpmod-action']) || !empty($_REQUEST['bpmod-ajax']) )
		// there is an ajax request or an action triggered by link: only bpModActions is needed
		{
			$mainclass = 'bpModActions';
		}
		else
		// we are in frontend page: only bpModFrontend is needed
		{
			$mainclass = 'bpModFrontend';
		}

		//load locales: first look in wp-content/plugins/bp-moderation-xx_XX.mo
		//for easy override and then in the packed language
		if (!load_plugin_textdomain('bp-moderation')) {
			load_plugin_textdomain('bp-moderation', false, 'bp-moderation/lang');
		}

		//create an istance of the main class for this situation
		bpModLoader::load_class( 'bpModeration' );
		$bpmod =& bpModeration::get_istance($mainclass);

		//load the default content types
		bpModLoader::load_class( 'bpModDefaultContentTypes' );
		bpModDefaultContentTypes::init($bpmod);

		//note: do_action support object reference only in one-elements array, it automatically extract it so the called function will receive only the obj, not the array
		do_action('bp_moderation_init', array(&$bpmod) );
	}

	/**
	 * activation callback
	 */
	function call_activate() {
		$installer = bpModLoader::load_class('bpModInstaller');
		$installer = new bpModInstaller();
		$installer->activate();
	}

	/**
	 * deactivation callback
	 */
	function call_deactivate() {
		$installer = bpModLoader::load_class('bpModInstaller');
		$installer = new bpModInstaller();
		$installer->deactivate();
	}

	/**
	 * uninstall callback
	 */
	function call_uninstall() {
		$installer = bpModLoader::load_class('bpModInstaller');
		$installer = new bpModInstaller();
		$installer->uninstall();
	}

	/**
	 * load a class
	 *
	 * @param string $cname classname
	 */
	function load_class( $cname ){
		if( class_exists($cname) ) return;

		$dir= apply_filters( 'bp_moderation_class_dir', dirname(__FILE__).'/classes/', $cname );
		require_once $dir.$cname.'.php';
	}

	/**
	 * bpModLoader file path
	 *
	 * @return string this file path
	 */
	function file(){
		return __FILE__;
	}

	/**
	 * generated random data
	 */
	function test_data(){
		set_time_limit  ( 0 );

		global $wpdb;
		$users = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE ID != 1" );

		if ( bp_core_is_multisite() ){
			$wpdb->query( "DELETE FROM $wpdb->signups" );
		}

		foreach ($users as $id)
			bp_core_delete_account( $id );

		$ngu = 2; #how much only good users
		$ngbu = 2; #how much not only good or only bad users
		$nbu = 2; #how much only bad users
		$content_types = array('A','B','C','D');

		$bpmod =& bpModeration::get_istance();
		$statuses = array_keys($bpmod->content_stati);
		$n_contents = 20;
		$flags_per_cont = 20; # +/- 30%

		$goodusers = array();
		$badusers = array();

		for ($i = 1; $i <= $ngu+$ngbu+$nbu; $i++) {
			$uid = bp_core_signup_user( 'user'.$i, 'pass', $i.'@foo.bar', array() );
			if ( bp_core_is_multisite() ){
				global $wpdb;
				$key_sql = "SELECT activation_key FROM $wpdb->signups WHERE user_email = '".$i."@foo.bar'";
				//echo $key_sql;
				$key = $wpdb->get_var( $key_sql );
				//var_dump($key);
			}else
				$key = get_usermeta( $uid, 'activation_key');
			
			$uid = bp_core_activate_signup($key);

			bp_core_is_multisite() and
				wp_set_password('pass',$uid);

			if ($i <= $ngu+$ngbu)
				$goodusers[] = $uid;
			if ($i > $ngu)
				$badusers[] = $uid;
		}

		bpModLoader::load_class('bpModObjContent');
		bpModLoader::load_class('bpModObjFlag');

		for($i = 1; $i<=$n_contents; $i++){
			$badu = $badusers[mt_rand(0, count($badusers)-1 )];
			$cont = new bpModObjContent();
			$cont->item_type = $content_types[mt_rand(0, count($content_types)-1 )] ;
			$cont->item_id = mt_rand(1, 1000000) ;
			$cont->item_author = $badu ;
			$cont->item_date = gmdate( "Y-m-d H:i:s" , time()-mt_rand(1000000, 2000000));
			$cont->status = $statuses[mt_rand(0,count($statuses)-1)] ;
			$cont->save();

			$flags = mt_rand($flags_per_cont*0.7, $flags_per_cont*1.3);

			for($j = 1; $j<=$flags; $j++){
				while ( $badu == ($goodu = $goodusers[mt_rand(0, count($goodusers)-1 )]) );

				$f = new bpModObjFlag();
				$f->content_id = $cont->content_id;
				$f->reporter_id = $goodu;
				$f->date = gmdate( "Y-m-d H:i:s" , time()-mt_rand(0, 1000000));
				$f->save();
			}


		}

		update_site_option('bp_moderation_test_data_check', 'success');

	}
}

?>
