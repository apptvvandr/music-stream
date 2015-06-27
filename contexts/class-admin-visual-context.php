<?php

namespace music_stream\contexts;

use axis_framework\contexts\Base_Context;


class Admin_Visual_Context extends Base_Context {

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );
	}

	public function init_context() {

		// add_filter( 'post_row_actions', array( &$this, 'post_row_actions' ) );
	}

	public function post_row_actions( $actions, $post ) {

		if( $post->post_type != 'music_stream' ) {
			return $actions;
		}
	}
}