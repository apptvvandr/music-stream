<?php

namespace music_stream\contexts;

use axis_framework\contexts\Base_Context;


class Post_Type_Context extends Base_Context {

	public function __construct( array $args = [] ) {

		parent::__construct( $args );
	}

	public function init_context() {

		add_action( 'init', array( &$this, 'register_music_stream_post_type' ) );
	}

	public function register_music_stream_post_type() {

		register_post_type(
			'music_stream',
			array(
				'label'                          => __( 'Music Streams', 'music_stream' ),
				'labels'                         => array(
					'name'                          => __( 'Music Streams', 'music_stream' ),
					'singular_name'                 => __( 'Music Stream', 'music_stream' ),
					'name_admin_bar'                => _x( 'Music Stream', '', 'music_stream' ),
					'all_items'                     => _x( 'All Music Streams', '', 'music_stream' ),
					'add_new'                       => _x( 'Add New', '', 'music_stream' ),
					'add_new_item'                  => _x( 'Add New Music Stream', '', 'music_stream' ),
					'edit_item'                     => _x( 'Edit Music Stream', '', 'music_stream' ),
					'new_item'                      => _x( 'New Music Stream', '', 'music_stream' ),
					'view_item'                     => _x( 'View radio station', '', 'music_stream' ),
					'search_items'                  => _x( 'Search radio stations', '', 'music_stream' ),
					'not_found'                     => _x( 'No radio stations found ', '', 'music_stream' ),
					'not_found_in_trash'            => _x( 'No radio stations found in trash', '', 'music_stream' ),
					'parent_item_colon'             => NULL,
				),
				'menu_icon'                      => 'dashicons-format-audio',
				'public'                         => TRUE,
				'publicly_queryable'             => TRUE,
				'show_ui'                        => TRUE,
				'supports'                       => array( 'title', 'editor', 'thumbnail' ),
				// 'taxonomies'                     => array(),
				'has_archive'                    => FALSE,
				'register_meta_box_cb'           => array( &$this, 'music_stream_add_meta_box'),
			)
		);
	}

	public function music_stream_add_meta_box() {

		$meta_box_context = $this->get_context('meta-box');

		add_meta_box(
			'music_stream_pls_file_metabox',
			__( 'PLS File', 'music_stream' ),
			array( &$meta_box_context, 'render_pls_file' ),
			NULL,
			'advanced',
			'high'
		);
	}
}