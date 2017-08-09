<?php
include '../../../../../wp-load.php';
global $bp;

// JLL_MOD - Add Photo tagging to Activity stream

function bp_album_record_tag_activity($photo_id, $tagged_id, $tagid ) {

	global $bp;

	$pic_data = new BP_Album_Picture($photo_id);

	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_album_enable_wire) {
		return false;
	}

	$primary_link = bp_core_get_user_domain($pic_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pic_data->id . '/';
	
	$title = $pic_data->title;
	$desc = $pic_data->description;

	// Using mb_strlen adds support for unicode (asian languages). Unicode uses TWO bytes per character, and is not
	// accurately counted in most string length functions
	// ========================================================================================================

	if ( function_exists( 'mb_strlen' ) ) {

	    $title = ( mb_strlen($title)<= 20 ) ? $title : mb_substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( mb_strlen($desc)<= 400 ) ? $desc : mb_substr($desc, 0 ,400-1).'&#8230;';

	} 
	else {

	    $title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';
	}
	
	$action = sprintf( __( '%s was tagged in the photo: %s', 'bp-album' ), bp_core_get_userlink($tagged_id), '<a href="'. $primary_link .'">'.$title.'</a>' );


	// Image path workaround for virtual servers that do not return correct base URL
	// ===========================================================================================================

	if($bp->album->bp_album_url_remap == true){

	    $filename = substr( $pic_data->pic_thumb_url, strrpos($pic_data->pic_thumb_url, '/') + 1 );
	    $owner_id = $pic_data->owner_id;
	    $image_path = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;
	}
	else {

	    $image_path = bp_get_root_domain().$pic_data->pic_thumb_url;
	}

	// ===========================================================================================================


	$content = '<p> <a href="'. $primary_link .'" class="picture-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';
	
	$type = 'photo-tag';
	$item_id = $tagid;
	$hide_sitewide = $pic_data->privacy != 0;

	return bp_activity_add( array( 'user_id' => $tagged_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'recorded_time' => $pic_data->date_uploaded , 'hide_sitewide' => $hide_sitewide ) );
	
	
}


?>