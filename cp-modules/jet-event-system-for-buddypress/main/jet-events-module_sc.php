<?php
function jes_shortcode_post($atts, $content = null)
	{
	extract( shortcode_atts( array(
		'jevent' => '1',
	), $atts )); ?>
<?php
    global $bp,$events_template;
	$devents = events_get_event(array('event_id'=>$jevent));
	$events_template->event->id = $jevent;
	return '<table width="100%"><tr><td width="10%">'.jes_bp_get_event_avatar('type=thumb').'</td><td width="25%"><h5><a href="/'.JES_SLUG.'/'.$devents->slug.'">'.$devents->name.'</a></h5></td><td width="10%">'.unixtodate($devents->edtsdunix).'<br />'.unixtodate($devents->edtedunix).'</td><td>'.substr($devents->description,0,100).'</td></tr></table>';
}
add_shortcode( 'jpostevent', 'jes_shortcode_post' );
?>