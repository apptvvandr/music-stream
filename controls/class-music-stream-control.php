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
				'ajax_url'                   => admin_url( 'admin-ajax.php' ),
			)
		);
		wp_enqueue_script( 'music_stream_js_handler' );

		echo '<div><button id="stop_music">' . __( 'Stop Music', 'music_stream' ) . '</button></div>';

		$query = new \WP_Query(
			array(
				'post_type'              => 'music_stream',
				'post_status'            => 'publish',
				'orderby'                => 'post_title',
				'order'                  => 'ASC',
			)
		);

		$posts = &$query->get_posts();

		echo '<ul id="music_stream_list">';
		foreach( $posts as &$post ) {
			echo "<li>";
			echo '<a href="#" data-post-id="'. $post->ID .'">' . $post->post_title . '</a>';
			echo "</li>";
		}
		echo "</ul>";
	}
}