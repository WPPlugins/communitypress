<?php
define("EAC_DIR_NAME",'jet-event-system-for-buddypress');
define("EAC_PLUGIN_DIR",WP_PLUGIN_DIR."/communitypress/cp-modules/".EAC_DIR_NAME);
define("EAC_PLUGIN_URL",WP_PLUGIN_URL."/communitypress/cp-modules/".EAC_DIR_NAME);

function jes_ea_enqueue_script()
	{
global $bp;
if ( $bp->current_component == $bp->jes_events->slug )
		{
		if ($bp->current_action == 'create')
			{
				if (!jes_is_event_creation_step( 'event-avatar' ) )
				{
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'jeseachecker' , EAC_PLUGIN_URL . '/js/eu_script.js' );
				}
			}
		}
}
add_action("wp_print_scripts", "jes_ea_enqueue_script");


function jes_ua_enqueue_style()
	{
global $bp;
if ( $bp->current_component == $bp->jes_events->slug )
		{
		if ($bp->current_action == 'create')
			{
				if (!jes_is_event_creation_step( 'event-avatar' ) )
				{
					wp_enqueue_style("ua-css",EAC_PLUGIN_URL."/css/eu_style.css");
				}
			}
		}
	}
add_action("wp_print_styles", "jes_ua_enqueue_style");

function jes_ea_check_eventname()
	{
		global $bp;
		if(!empty($_POST["event_name"]))
			{
				if(function_exists("get_current_site"))
					{
						global $wpdb;
						$eventname = $wpdb->get_row( $wpdb->prepare("SELECT * FROM " . $bp->jes_events->table_name . " WHERE name = %s", $_POST["event_name"]) );				
            if(!empty($eventname))
				{
					$msg=array("code"=>"error","message"=>__("An event with this name already exists .. do not want to change it?","jet-event-system"));
				} else {
				$msg=array("code"=>"available","message"=>__("good name for the event","jet-event-system"));
				}
					}
			}
				else
					$msg=array("code"=>"error","message"=>__("The event name can not be empty ..","buddypress"));
		echo json_encode($msg);
}
add_action("wp_ajax_check_eventname","jes_ea_check_eventname");
?>