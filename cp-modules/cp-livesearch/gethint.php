<?php
include '../../../../../wp-load.php';
global $wpdb, $bp;

//get the q parameter from URL
$q=$_GET["q"];

// search members
$table_name = $wpdb->prefix . "users";
$members = $wpdb->get_results( "SELECT id, user_url, display_name FROM " . $table_name, ARRAY_A );
for($i=0; $i<count($members); $i++) {
	$singlemember = $members[$i];
    $id[] = $singlemember[id];
    $name[] = $singlemember[display_name];
    $url[] = $singlemember[user_url];
	$avatar[] = bp_core_fetch_avatar( array( 'item_id' => $singlemember[id] ) );
	//$latest[] = jll_get_activity_latest_update_no_view( $singlemember[id] );
	//$lastactiv[] = bp_get_member_last_active( $singlemember[id] );
}
//lookup all hints from array if length of q>0
if (strlen($q) > 0) {
	$hint="";
	for($i=0; $i<count($name); $i++) {
			if (strtolower($q)==strtolower(substr($name[$i],0,strlen($q)))) {
				if ($hint=="") {
					$hint = '<tr height="60px"><td width="60px"><a href="' . $url[$i] . '">' . $avatar[$i] . '</a></td><td><a href="' . $url[$i] . '"><div class="lsitem">' . $name[$i] . '</div></a></td></tr>';
				} else {
					$hint = $hint . '<tr height="60px"><td width="60px"><a href="' . $url[$i] . '">' . $avatar[$i] . '</a></td><td><a href="' . $url[$i] . '"><div class="lsitem">' . $name[$i] . '</div></a></td></tr>';
				}
			}
	}
 }

//output the response  
if (!$hint == "") {
	$response=$hint;
	echo '<table width="200px">' . $response . '</table></ul>';
}
?>