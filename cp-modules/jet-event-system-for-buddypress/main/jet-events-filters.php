<?php

/* Apply WordPress defined filters */
add_filter( 'jes_bp_get_event_description', 'wptexturize' );
add_filter( 'jes_bp_get_event_description_excerpt', 'wptexturize' );
add_filter( 'jes_bp_get_event_name', 'wptexturize' );

add_filter( 'jes_bp_get_event_description', 'convert_smilies' );
add_filter( 'jes_bp_get_event_eventterms', 'convert_smilies' );
add_filter( 'jes_bp_get_event_description_excerpt', 'convert_smilies' );

add_filter( 'jes_bp_get_event_description', 'convert_chars' );
add_filter( 'jes_bp_get_event_description_excerpt', 'convert_chars' );
add_filter( 'jes_bp_get_event_name', 'convert_chars' );

add_filter( 'jes_bp_get_event_description', 'wpautop' );
add_filter( 'jes_bp_get_event_description_excerpt', 'wpautop' );

add_filter( 'jes_bp_get_event_description', 'make_clickable' );
add_filter( 'jes_bp_get_event_description_excerpt', 'make_clickable' );

add_filter( 'jes_bp_get_event_name', 'wp_filter_kses', 1 );
add_filter( 'jes_bp_get_event_permalink', 'wp_filter_kses', 1 );
add_filter( 'jes_bp_get_event_description', 'jes_bp_events_filter_kses', 1 );
add_filter( 'jes_bp_get_event_description_excerpt', 'wp_filter_kses', 1 );
add_filter( 'events_event_name_before_save', 'wp_filter_kses', 1 );
add_filter( 'events_event_description_before_save', 'wp_filter_kses', 1 );

add_filter( 'jes_bp_get_event_description', 'stripslashes' );
add_filter( 'jes_bp_get_event_description_excerpt', 'stripslashes' );
add_filter( 'jes_bp_get_event_name', 'stripslashes' );
add_filter( 'bp_get_event_member_name', 'stripslashes' );
add_filter( 'bp_get_event_member_link', 'stripslashes' );



add_filter( 'events_event_name_before_save', 'force_balance_tags' );
add_filter( 'events_event_description_before_save', 'force_balance_tags' );

add_filter( 'bp_jes_get_jes_total_event_count', 'bp_core_number_format' );
add_filter( 'bp_get_event_total_for_member', 'bp_core_number_format' );
add_filter( 'jes_bp_get_event_total_members', 'bp_core_number_format' );

function jes_bp_events_filter_kses( $content ) {
	global $allowedtags;

	$events_allowedtags = $allowedtags;
	$events_allowedtags['a']['class'] = array();
	$events_allowedtags['img'] = array();
	$events_allowedtags['img']['src'] = array();
	$events_allowedtags['img']['alt'] = array();
	$events_allowedtags['img']['class'] = array();
	$events_allowedtags['img']['width'] = array();
	$events_allowedtags['img']['height'] = array();
	$events_allowedtags['img']['class'] = array();
	$events_allowedtags['img']['id'] = array();
	$events_allowedtags['code'] = array();

	$events_allowedtags = apply_filters( 'jes_bp_events_filter_kses', $events_allowedtags );
	return wp_kses( $content, $events_allowedtags );
}

?>