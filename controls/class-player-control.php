<?php

namespace music_stream\controls;

use axis_framework\controls\Base_Control;
use music_stream\libs\Settings;


class Player_Control extends Base_Control {

	const CMD_PLAYER    = '/usr/bin/mplayer -ao alsa:device=bluetooth -quiet';
	const CMD_BT_DEVICE = 'sudo /usr/bin/bt-device';
	const CMD_BT_AUDIO  = 'sudo /usr/bin/bt-audio';
	const CMD_SED       = '/bin/sed';
	const DEVICE_NAME   = 'IA160';
	const MPLAYER_LOG   = '/var/log/music-stream/mplayer.log';

	private $cmd_player;
	private $cmd_bt_device;
	private $cmd_bt_audio;
	private $cmd_sed;
	private $device_name;
	private $mplayer_log;

	private $settings;

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );

		$this->settings = Settings::get_instance();

		$this->cmd_player    = isset( $this->settings->command_player ) ? $this->settings->command_player : self::CMD_PLAYER;
		$this->cmd_bt_device = isset( $this->settings->command_bt_device ) ? $this->settings->command_bt_device : self::CMD_BT_DEVICE;
		$this->cmd_bt_audio  = isset( $this->settings->command_bt_audio ) ? $this->settings->command_bt_audio : self::CMD_BT_AUDIO;
		$this->cmd_sed       = isset( $this->settings->command_sed ) ? $this->settings->command_sed : self::CMD_SED;
		$this->device_name   = isset( $this->settings->device_name ) ? $this->settings->device_name : self::DEVICE_NAME;
		$this->mplayer_log   = isset( $this->settings->player_log_path ) ? $this->settings->player_log_path : self::MPLAYER_LOG;

		$this->cmd_player    .= ' ';
		$this->cmd_bt_device .= ' ';
		$this->cmd_bt_audio  .= ' ';
		$this->cmd_sed       .= ' ';
	}

	public function play( $file ) {

		$this->device_connect();

		if( $this->is_playing() ) {
			$this->stop();
		}

		if( strlen( $file ) > 4 && substr( $file, - 4 ) == '.pls' ) {
			$file = '-playlist ' . $file;
		}

		$pid = $this->exec( $this->cmd_player . $file . ' < /dev/null > ' . $this->mplayer_log . ' 2>&1 & echo $!' );
		if( $pid && is_array( $pid ) && is_numeric( $pid[0] ) ) {
			update_option( 'music_stream_pid', $pid[0] );
		}
	}

	public function device_connect() {

		$status = $this->device_check_connection();
		if( $status == 0 ) {
			$command = $this->cmd_bt_audio . ' -c ' . $this->device_name;
			$this->exec( $command );
		}

		return '1' == $this->device_check_connection();
	}

	public function device_check_connection() {

		$command = $this->cmd_bt_device . '-i ' . $this->device_name . '| ' .
		           $this->cmd_sed . ' -rn \'s/ *Connected: ([[:digit:]])/\\1/p\'';
		$output  = $this->exec( $command );

		return $output[0];
	}

	public function is_playing() {

		$pid = get_option( 'music_stream_pid' );

		return is_numeric( $pid ) && $pid > 0;
	}

	public function stop( $force = FALSE ) {

		$pid = get_option( 'music_stream_pid', '' );
		if( $pid > 0 ) {
			$this->exec( "kill {$pid}" );
			$this->remove_log();
		} else if( $force ) {
			$this->force_stop();
		}
		update_option( 'music_stream_pid', '' );
	}

	public function remove_log() {

		if( file_exists( $this->mplayer_log ) ) {
			unlink( $this->mplayer_log );
		}
	}

	public function force_stop() {

		$pid_list = implode( ' ', $this->get_player_pid_list() );
		$this->exec( "kill {$pid_list}" );
		$this->remove_log();
	}

	public function get_player_pid_list() {

		$command = "ps ax | grep \"" . $this->cmd_player . "\" | grep -v grep | cut -f 1 -d \" \"";

		return $this->exec( $command );
	}

	public function device_info() {

		return $this->exec( $this->cmd_bt_device . '-i ' . $this->device_name );
	}

	public function list_devices() {

		return $this->exec( $this->cmd_bt_device . '-l' );
	}

	public function device_disconnect() {

		$status = $this->device_check_connection();
		if( $status == 1 ) {
			$command = $this->cmd_bt_audio . '-d ' . $this->device_name;
			$this->exec( $command );
		}

		return '0' == $this->device_check_connection();
	}

	public function get_log() {

		$result            = new \stdClass();
		$result->name      = '';
		$result->genre     = '';
		$result->website   = '';
		$result->public    = '';
		$result->bitrate   = '';
		$result->songs     = array();
		$result->play_time = '';

		if( !file_exists( $this->mplayer_log ) ) {
			return NULL;
		}

		$fp = fopen( $this->mplayer_log, 'r' );
		while( !feof( $fp ) ) {
			$line    = fgets( $fp );
			$matches = NULL;

			if( preg_match( '/^(Name|Genre|Website|Public|Bitrate|ICY Info) *: *(.+)$/', $line, $matches ) ) {
				$criteria = trim( $matches[1] );

				switch( $criteria ) {
					case 'Name':
						$result->name = trim( $matches[2] );
						break;
					case 'Genre':
						$result->genre = trim( $matches[2] );
						break;
					case 'Website':
						$result->website = trim( $matches[2] );
						break;
					case 'Public':
						$result->public = trim( $matches[2] );
						break;
					case 'Bitrate':
						$result->bitrate = trim( $matches[2] );
						break;
					case 'ICY Info':
						$icy_info   = trim( $matches[2] );
						$song_match = NULL;
						if( preg_match( '/StreamTitle=\'(.+)\';StreamUrl/', $icy_info, $song_match ) ) {
							$result->songs[] = $song_match[1];
						}
						break;
				}
			}
		}
		fclose( $fp );

		$result->songs = array_reverse( $result->songs );

		$pid = get_option( 'music_stream_pid' );
		if( $pid ) {
			$process_info = $this->exec( "ps -eo pid,etime | grep {$pid}" );
			if( is_array( $process_info ) ) {
				foreach( $process_info as $pi ) {
					$process_match = NULL;
					if( preg_match( '/^\s*(\d+)\s*(.+)/', $pi, $process_match ) ) {
						if( $pid == $process_match[1] ) {
							$result->play_time = $process_match[2];
							break;
						}
					}
				}
			}

			return $result;
		}

		return NULL;
	}

	private function process_running( $pid ) {

		$command = "if ps -p $pid > /dev/null; then echo 1; else echo 0; fi";

		return $this->exec( $command );
	}

	private function exec( $command ) {

		$output     = array();
		$return_var = 0;

		exec( $command, $output, $return_var );

		if( $return_var ) {
			error_log( "command '$command' returned value $return_var" );
		}

		return $output;
	}
}