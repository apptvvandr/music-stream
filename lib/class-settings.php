<?php

namespace music_stream\libs;

use axis_framework\core\Singleton;


class Settings extends Singleton {

	public $command_player;
	public $command_bt_device;
	public $command_bt_audio;
	public $command_sed;
	public $device_name;
	public $player_log_path;

	protected function __construct() {

		parent::__construct();
		$this->fetch();
	}

	public static function sanitize_settings( $options ) {

		// well, I trust myself.
		return $options;
	}

	public function fetch() {

		$option = get_option( 'music_stream' );
		if( $option !== FALSE ) {
			$this->command_player    = isset( $option['command_player'] )    ? $option['command_player']    : '';
			$this->command_bt_device = isset( $option['command_bt_device'] ) ? $option['command_bt_device'] : '';
			$this->command_bt_audio  = isset( $option['command_bt_audio'] )  ? $option['command_bt_audio']  : '';
			$this->command_sed       = isset( $option['command_sed'] )       ? $option['command_sed']       : '';
			$this->device_name       = isset( $option['device_name'] )       ? $option['device_name']       : '';
			$this->player_log_path   = isset( $option['player_log_path'] )   ? $option['player_log_path']   : '';
		}
	}
}