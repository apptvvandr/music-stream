<?php
namespace music_stream\context;

require_once( MUSIC_STREAM_PATH . '/lib/class-settings.php' );

use axis_framework\context\Base_Context;
use music_stream\libs\Settings;


class Settings_Context extends Base_Context {

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );
	}

	public function init_context() {

		$this->add_context_action( 'admin_init' );
	}

	protected function admin_init_callback() {

		/** @var \music_stream\view\Settings_View $view */
		$view = $this->loader->view( 'music_stream\view', 'settings' );

		$settings = Settings::get_instance();

		register_setting(
			'music_stream',
			'music_stream',
			array( '\music_stream\libs\Settings', 'sanitize_settings' )
		);

		add_settings_section(
			'music_stream_system',
			__( 'System Settings', 'music_stream' ),
			'__return_empty_string',
			'music_stream_settings'
		);

		add_settings_field(
			'command_player',
			__( 'Player Command', 'music_stream' ),
			array( $view, 'render_text_field' ),
			'music_stream_settings',
			'music_stream_system',
			array(
				'type'        => 'text',
				'class'       => 'text',
				'size'        => 60,
				'name'        => 'music_stream[command_player]',
				'description' => __(
					"Command line for player. e.g.) '/usr/bin/mplayer -ao alsa:device=bluetooth -quiet '",
					'music_stream'
				),
				'value'       => $settings->command_player,
			)
		);

		add_settings_field(
			'command_bt_device',
			__( "'bt-device' Command", 'music_stream' ),
			array( $view, 'render_text_field' ),
			'music_stream_settings',
			'music_stream_system',
			array(
				'type'        => 'text',
				'class'       => 'text',
				'size'        => 60,
				'name'        => 'music_stream[command_bt_device]',
				'description' => __(
					"Command line for 'bt-device'. e.g) 'sudo /usr/bin/bt-device '",
					'music_stream'
				),
				'value'       => $settings->command_bt_device,
			)
		);

		add_settings_field(
			'command_bt_audio',
			__( "'bt-audio' Command", 'music_stream' ),
			array( $view, 'render_text_field' ),
			'music_stream_settings',
			'music_stream_system',
			array(
				'type'        => 'text',
				'class'       => 'text',
				'size'        => 60,
				'name'        => 'music_stream[command_bt_audio]',
				'description' => __(
					"Command line for 'bt-audio'. e.g) 'sudo /usr/bin/bt-audio '",
					'music_stream'
				),
				'value'       => $settings->command_bt_audio,
			)
		);

		add_settings_field(
			'command_sed',
			__( "'sed' Command", 'music_stream' ),
			array( $view, 'render_text_field' ),
			'music_stream_settings',
			'music_stream_system',
			array(
				'type'        => 'text',
				'class'       => 'text',
				'name'        => 'music_stream[command_sed]',
				'description' => __(
					"Full path of 'sed'. e.g) /bin/sed",
					'music_stream'
				),
				'value'       => $settings->command_sed,
			)
		);

		add_settings_field(
			'device_name',
			__( "Device Name", 'music_stream' ),
			array( $view, 'render_text_field' ),
			'music_stream_settings',
			'music_stream_system',
			array(
				'type'        => 'text',
				'class'       => 'text',
				'name'        => 'music_stream[device_name]',
				'description' => __(
					"Your device name.",
					'music_stream'
				),
				'value'       => $settings->device_name,
			)
		);

		add_settings_field(
			'player_log_path',
			__( "Player Log Path", 'music_stream' ),
			array( $view, 'render_text_field' ),
			'music_stream_settings',
			'music_stream_system',
			array(
				'type'        => 'text',
				'class'       => 'text',
				'size'        => 60,
				'name'        => 'music_stream[player_log_path]',
				'description' => __(
					"Player's log path. Web server must have a privilege to access and write.",
					'music_stream'
				),
				'value'       => $settings->player_log_path,
			)
		);
	}
}
