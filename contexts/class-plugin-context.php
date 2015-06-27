<?php

namespace music_stream\contexts;

require_once( AXIS_FRAMEWORK_PATH . '/contexts/trait-plugin-callback.php' );

use axis_framework\contexts\Base_Context;
use axis_framework\contexts\Plugin_Callback_Trait;


class Plugin_Context extends Base_Context {

	use Plugin_Callback_Trait;

	public function __construct( array $args = [] ) {

		parent::__construct( $args );
	}

	public function init_context() {

		$this->by_trait_activation_deactivation_hook( MUSIC_STREAM_MAIN_FILE );
		$this->by_trait_add_admin_menu();

		add_shortcode(
			'music_stream',
			$this->control_helper( 'music_stream\controls', 'music-stream', 'display', [], TRUE )
		);

		add_action(
			'wp_enqueue_scripts',
			array( &$this, 'wp_enqueue_scripts' )
		);

		add_action(
			'admin_enqueue_scripts',
			array( &$this, 'wp_enqueue_scripts' )
		);
	}

	public function add_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=music_stream',
			__( 'Force Stop All Music', 'music_stream' ),
			__( 'Stop Music', 'music_stream' ),
			'manage_options',
			'stop_all_music',
			$this->control_helper( 'music_stream\controls', 'player', 'force_stop' )
		);
	}

	public function wp_enqueue_scripts() {

		wp_register_script(
			'music_stream_js_handler',
			MUSIC_STREAM_URL . 'static/js/music-stream.js',
			array( 'jquery', ),
			NULL,
			FALSE
		);
	}

	public function on_activated() {

		if( FALSE === get_option( 'music_stream_pid' ) ) {
			add_option( 'music_stream_pid', '' );
		}
	}

	public function on_deactivated() {

		if( FALSE !== get_option( 'music_stream_pid' ) ) {
			delete_option( 'music_stream_pid' );
		}
	}
}