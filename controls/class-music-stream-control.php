<?php

namespace music_stream\controls;

use axis_framework\controls\Base_Control;


class Music_Stream_Control extends Base_Control {

	public function __construct( array $args = [] ) {

		parent::__construct( $args );

		$this->pid_path = dirname( MUSIC_STREAM_MAIN_FILE ). '/mplayer.pid';
	}

	public function display() {

		if( !is_user_logged_in() ) {
			wp_die( __( 'You are required to log in', 'music-stream' ) );
		}

		wp_localize_script(
			'music_stream_js_handler',
			'ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

		wp_enqueue_script( 'music_stream_js_handler' );

		$query = new \WP_Query(
			array(
				'post_type'              => 'music_stream',
				'post_status'            => 'publish',
				'orderby'                => 'post_title',
				'order'                  => 'ASC',
				'nopaging'               => TRUE,
			)
		);

		$posts = &$query->get_posts();

		/** @var \music_stream\controls\Player_Control $player */
		$player = $this->loader->control( 'music_stream\controls', 'player' );
		$status = $player->get_log();

		$this->render_template( 'music-stream-list', array( 'posts' => &$posts, 'status' => &$status ) );
	}
}