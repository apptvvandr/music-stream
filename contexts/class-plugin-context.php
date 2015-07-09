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

		/**
		 * Check user status. If user is not logged in, then redirect to login url.
		 * This process should be done before sending head part.
		 */
		add_action(
			'template_redirect',
			$this->control_helper( 'music_stream\controls', 'music-stream', 'check_login' )
		);

		/**
		 * Music stream shortcode handler
		 */
		add_shortcode(
			'music_stream',
			$this->control_helper( 'music_stream\controls', 'music-stream', 'display', [], TRUE )
		);

		/**
		 * Javascript for music stream
		 */
		add_action(
			'wp_enqueue_scripts',
			array( &$this, 'wp_enqueue_scripts' )
		);

		/**
		 * Javascript for music stream (admin)
		 */
		add_action(
			'admin_enqueue_scripts',
			array( &$this, 'wp_enqueue_scripts' )
		);

		$this->add_context_action( 'plugins_loaded' );
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

		add_options_page(
			__( 'Music Stream', 'music_stream' ),
			__( 'Music Stream', 'music_stream' ),
			'manage_options',
			'music_stream_settings',
			$this->control_helper( 'music_stream\controls', 'music-stream', 'settings' )
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

	protected function plugins_loaded_callback() {

		load_plugin_textdomain(
			'music_stream',
			false,
			dirname( plugin_basename( MUSIC_STREAM_MAIN_FILE ) ) . '/languages'
		);
	}
}