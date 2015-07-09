<?php

namespace music_stream\views;

require_once( AXIS_FRAMEWORK_PATH . '/forms/class-form-renderer.php' );

use axis_framework\views\Base_View;
use axis_framework\forms\Form_Renderer;


class Settings_View extends Base_View {

	/** @var \axis_framework\forms\Form_Renderer */
	private $form;

	public function __construct( array $args = [ ] ) {

		parent::__construct( $args );

		$this->form = new Form_Renderer();
	}

	public function render_text_field( $args ) {

		$attributes = array_intersect_key( $args, array( 'type'=> '', 'name' => '', 'class' => '', 'value' => '', 'size' => '' ) );
		$this->form->start_end_tag( 'input', $attributes );

		if( isset( $args['description'] ) ) {
			$attr = array( 'class' => 'description' );
			$this->form->start_end_tag( 'br' );
			$this->form->start_tag( 'span', $attr );
			echo $args['description'];
			$this->form->end_tag( 'span' );
		}
	}
}