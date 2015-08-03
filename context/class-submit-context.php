<?php

namespace music_stream\context;

use axis_framework\context\Base_Context;


class Submit_Context extends Base_Context {

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );
	}

	public function init_context() {

		add_action( 'wp_ajax_music_stream_play', array( $this, 'play_callback' ) ) ;
		add_action( 'wp_ajax_music_stream_stop', array( $this, 'stop_callback' ) ) ;
	}

	public function play_callback() {

		$post_id = $_GET['post-id'];
		$meta = get_post_meta( $post_id, 'music_stream_pls', TRUE );
		if( $meta ) {
			$file = $meta['file'];
			$control = $this->loader->control( 'music_stream\control', 'player' );
			$control->play( $file );
		}
		die();
	}

	public function stop_callback() {

		$control = $this->loader->control( 'music_stream\control', 'player' );
		$control->stop();
		die();
	}
}