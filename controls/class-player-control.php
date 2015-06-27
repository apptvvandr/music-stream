<?php

namespace music_stream\controls;

use axis_framework\controls\Base_Control;


class Player_Control extends Base_Control {

	const CMD_PLAYER    = '/usr/bin/mplayer -ao alsa:device=bluetooth -quiet ';
	const CMD_BT_DEVICE = 'sudo /usr/bin/bt-device ';
	const CMD_BT_AUDIO  = 'sudo /usr/bin/bt-audio ';
	const CMD_SED       = '/bin/sed';
	const DEVICE_NAME   = 'IA160';
	const MPLAYER_LOG   = '/var/log/music-stream/mplayer.log';

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );
	}

	private function exec( $command ) {

		$output     = array();
		$return_var = 0;

		exec( $command, $output, $return_var );

		if ( $return_var ) {
			error_log( "command '$command' returned value $return_var" );
		}

		return $output;
	}

	private function process_running( $pid ) {

		$command = "if ps -p $pid > /dev/null; then echo 1; else echo 0; fi";

		return $this->exec( $command );
	}

	public function play( $file ) {

		$this->device_connect();

		if ( $this->is_playing() ) {
			$this->stop();
		}

		if ( strlen( $file ) > 4 && substr( $file, - 4 ) == '.pls' ) {
			$file = '-playlist ' . $file;
		}

		$pid = $this->exec( self::CMD_PLAYER . $file . ' < /dev/null > ' . self::MPLAYER_LOG . ' 2>&1 & echo $!' );
		if ( $pid && is_array( $pid ) && is_numeric( $pid[0] ) ) {
			update_option( 'music_stream_pid', $pid[0] );
		}
	}

	public function stop( $force = FALSE ) {

		$pid = get_option( 'music_stream_pid', '' );
		if ( $pid > 0 ) {
			$this->exec( "kill {$pid}" );
			$this->remove_log();
		} else if( $force ) {
			$this->force_stop();
		}
		update_option( 'music_stream_pid', '' );
	}

	public function get_player_pid_list() {

		$command = "ps ax | grep \"" . self::CMD_PLAYER . "\" | grep -v grep | cut -f 1 -d \" \"";

		return $this->exec( $command );
	}

	public function is_playing() {

		$pid = get_option( 'music_stream_pid' );

		return is_numeric( $pid ) && $pid > 0;
	}

	public function force_stop() {

		$pid_list = implode( ' ', $this->get_player_pid_list() );
		$this->exec( "kill {$pid_list}" );
		$this->remove_log();
	}

	public function device_info() {

		return $this->exec( self::CMD_BT_DEVICE . ' -i ' . self::DEVICE_NAME );
	}

	public function list_devices() {

		return $this->exec( self::CMD_BT_DEVICE . '-l' );
	}

	public function device_check_connection() {

		$command = self::CMD_BT_DEVICE . ' -i ' . self::DEVICE_NAME . ' | ' .
		           self::CMD_SED . ' -rn \'s/ *Connected: ([[:digit:]])/\\1/p\'';
		$output  = $this->exec( $command );

		return $output[0];
	}

	public function device_connect() {

		$status = $this->device_check_connection();
		if ( $status == 0 ) {
			$command = self::CMD_BT_AUDIO . ' -c ' . self::DEVICE_NAME;
			$this->exec( $command );
		}

		return '1' == $this->device_check_connection();
	}

	public function device_disconnect() {

		$status = $this->device_check_connection();
		if ( $status == 1 ) {
			$command = self::CMD_BT_AUDIO . ' -d ' . self::DEVICE_NAME;
			$this->exec( $command );
		}

		return '0' == $this->device_check_connection();
	}

	public function remove_log() {

		if( file_exists( self::MPLAYER_LOG ) ) {
			unlink( self::MPLAYER_LOG );
		}
	}

	public function get_log() {

		$result           = new \stdClass();
		$result->name     = '';
		$result->genre    = '';
		$result->website  = '';
		$result->public   = '';
		$result->bitrate  = '';
		$result->songs    = array();
		$result->play_time = '';

		if( !file_exists( self::MPLAYER_LOG ) ) {
			return $result;
		}

		$fp = fopen( self::MPLAYER_LOG, 'r' );
		while ( ! feof( $fp ) ) {
			$line    = fgets( $fp );
			$matches = NULL;

			if ( preg_match( '/^(Name|Genre|Website|Public|Bitrate|ICY Info) *: *(.+)$/', $line, $matches ) ) {
				$criteria = trim( $matches[1] );

				switch ( $criteria ) {
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
					if( preg_match( '/^(\d+) +(.+)$/', $pi, $process_match ) ) {
						if( $pid == $process_match[1] ) {
							$result->play_time = $process_match[2];
							break;
						}
					}
				}
			}
		}

		return $result;
	}
}