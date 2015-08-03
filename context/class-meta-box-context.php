<?php

namespace music_stream\context;

use axis_framework\context\Base_Context;


class Meta_Box_Context extends Base_Context {

	public function __construct( array $args = [] ) {

		parent::__construct( $args );
	}

	public function init_context() {

		add_action( 'post_edit_form_tag', array( &$this, 'post_edit_form_tag' ) );
		add_action( 'save_post',  array( &$this, 'save_post' ) );
	}

	public function post_edit_form_tag() {

		echo ' enctype="multipart/form-data"';
	}

	public function render_pls_file( $post ) {

		$enclosed_file = get_post_meta( $post->ID, 'music_stream_pls', TRUE );
		wp_nonce_field( 'music_stream_pls_nonce', 'music_stream_pls_nonce' );
		$html = '<p class="description">Upload .PLS</p>';
		if( !empty( $enclosed_file ) ) {
			$html .= '<p>Current: ' . basename( $enclosed_file['file'] ) . '</p>';
		}
		$html .= '<input type="file" id="music_stream_pls_attachment" name="music_stream_pls_attachment" />';
		echo $html;
	}

	function save_post( $post_id ) {

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		if( $_POST['post_type'] != 'music_stream' || !current_user_can( 'edit_post' ) ) {
			return;
		}

		if( !wp_verify_nonce( $_POST['music_stream_pls_nonce'], 'music_stream_pls_nonce' ) ) {
			return;
		}

		if( !empty( $_FILES['music_stream_pls_attachment']['name'] ) ) {

			add_filter( 'mime_types', function( $mime_types ) {
				$mime_types['pls'] = 'audio/x-scpls';
				return $mime_types;
			});

			$supported_types = array( 'audio/x-scpls' );
			$file_type = wp_check_filetype( basename($_FILES['music_stream_pls_attachment']['name'] ) );
			$uploaded_type = $file_type['type'];

			if( !in_array( $uploaded_type, $supported_types ) ) {
				wp_die( __( 'The file type is not supported.') );
			}

			$upload = wp_upload_bits(
				$_FILES['music_stream_pls_attachment']['name'],
				NULL,
				file_get_contents( $_FILES['music_stream_pls_attachment']['tmp_name'] )
			);

			update_post_meta( $post_id, 'music_stream_pls', $upload );
		}
	}
}