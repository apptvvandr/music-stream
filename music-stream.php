<?php
/*
Plugin Name: Music Stream
Plugin URI: http://github.com/chwnam/music-stream
Description: Music control interface for MiniServer
Version: 1.2
Author: Changwoo Nam
Author URI: http://github.com/chwnam
License: GPL2 or later
Text Domain: music_stream
*/

namespace music_stream;

use \axis_framework\contexts\Dispatch;

define( 'MUSIC_STREAM_MAIN_FILE', __FILE__ );
define( 'MUSIC_STREAM_PATH',      dirname( __FILE__ ) );
define( 'MUSIC_STREAM_URL',       plugin_dir_url( __FILE__ ) );

require_once( WPMU_PLUGIN_DIR . '/axis-framework/axis-defines.php' );

if( version_compare( AXIS_FRAMEWORK_VERSION, '0.20.1000', '=' ) ) {

	$dispatch = new Dispatch();
	$dispatch->setup( MUSIC_STREAM_MAIN_FILE , 'music_stream\contexts' );

} else {

	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>' .
		     __( 'Axis Framework must be version 0.20.1000. Music Stream plugin won\'t work!', 'music_stream' ) .
		     '</p></div>';
	} );
}

/**
 * music player aliased commands are:
 * bt_mplayer='mplayer -ao alsa:device=bluetooth'
 * ia160_info='sudo bt-device -i IA160'
 * bt_list='sudo bt-device -l'
 * ia160_connect='sudo bt-audio -c IA160'
 * ia160_disconnect='sudo bt-audio -d IA160'
 * ia160_check_connection="ia160_info | sed -rn 's/ *Connected: ([[:digit:]])/\1/p'"
 */