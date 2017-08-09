<?php
/*
Plugin Name: CommunityPress
Plugin URI: http://communitypress.jessesoffice.com
Description: Social Community in a box!
Version: 1.0
Author: Jesse LaReaux
Author URI: http://jessesoffice.com
License:
			Copyright 2011  Jesse LaReaux  (email : jlareaux@gmail.com)
			This program is free software; you can redistribute it and/or modify
			it under the terms of the GNU General Public License, version 2, as 
			published by the Free Software Foundation.
			
			This program is distributed in the hope that it will be useful,
			but WITHOUT ANY WARRANTY; without even the implied warranty of
			MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
			GNU General Public License for more details.
			
			You should have received a copy of the GNU General Public License
			along with this program; if not, write to the Free Software
			Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// All Modifications to to original plugins are preceded by a comment like the one below
// JLL_MOD - fixed file dir path (or whatever the modification was)

// Load enabled/standard modules

global $wpdb;
$disabled_components = get_site_option( 'bp-deactivated-components' );

require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/cp-widgets/cp-widgets.php' );
require_once( WP_PLUGIN_DIR . '/communitypress/cp-core/bp-loader.php' );
//require_once( WP_PLUGIN_DIR . '/communitypress/cp-core/cp-custom-post-op.php' );
if ( !isset( $disabled_components['fbconnect'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/wp-fb-autoconnect/Main.php' );}
if ( !isset( $disabled_components['photos'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/bp-album/loader.php' );}
if ( !isset( $disabled_components['disableemail'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/bp-disable-activation/bp-disable-activation-loader.php' );}
if ( !isset( $disabled_components['moderation'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/bp-moderation/bpModLoader.php' );}
if ( !isset( $disabled_components['events'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/jet-event-system-for-buddypress/jet-event-system.php' );}
if ( !isset( $disabled_components['mediapost'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/buddypress-activity-plus/bpfb.php' );}
if ( !isset( $disabled_components['userprivacy'] ) ){require_once( WP_PLUGIN_DIR . '/communitypress/cp-modules/cp-privacy/cp-privacy.php' );}
?>