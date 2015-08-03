<?php

namespace music_stream\view;

require_once( AXIS_FRAMEWORK_PATH . '/form/class-form-tag.php' );

use axis_framework\view\Base_View;
use axis_framework\form\tag\Base_Tag;


class Settings_View extends Base_View {

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );
	}

	public function render_text_field( $args ) {

		$attributes = array_intersect_key( $args, array( 'type'=> '', 'name' => '', 'class' => '', 'value' => '', 'size' => '' ) );

		if( !isset( $attributes['name'] ) ) {
			$attributes['name'] = '';
		}

		$tag = new Base_Tag( 'input', '', $attributes['name'], $attributes );
		$tag->self_closing_tag();

		if( isset( $args['description'] ) ) {

			$description = new Base_Tag( 'br' );
			$description->self_closing_tag();

			$span = new Base_Tag( 'span', '', '', array( 'class' => 'description' ) );
			$span->start_tag();
			echo $args['description'];
			$tag->end_tag( 'span' );
		}
	}
}