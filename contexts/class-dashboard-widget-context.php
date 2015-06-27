<?php

namespace music_stream\contexts;

use axis_framework\contexts\Base_Context;


class Dashboard_Widget_Context extends Base_Context {

	public function init_context() {

		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	public function add_dashboard_widgets() {

		wp_add_dashboard_widget(
			'music_stream_dashboard',
			__( 'Music Stream', 'music_stream' ),
			$this->control_helper( 'music_stream\controls', 'music-stream', 'display' )
		);
	}
}