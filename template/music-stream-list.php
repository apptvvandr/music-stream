<?php
/** @var array $posts array of posts */
/** @var stdClass $status array of posts */
?>

<?php if( $status ) : ?>
	<?php //\axis_framework\core\utils\axis_dump_pre( $status, 'status' ); ?>
	<div class="now-playing-container">
		<div class="">
			<h5>Stream Information</h5>
			<img src="<?= plugin_dir_url( MUSIC_STREAM_MAIN_FILE ) . 'static/img/music_dark_blue_by_ravesangel.gif' ?>" />
		</div>
		<div class="station-name">
			<span><?=__( 'Station: ', 'music_stream' )?></span>
			<span><?=$status->name?></span>
		</div>
		<div class="genre">
			<span><?=__( 'Genre: ', 'music_stream' )?></span>
			<span><?=$status->genre?></span>
		</div>
		<div class="play-time">
			<span><?=__( 'Play time: ', 'music_stream' )?></span>
			<span><?=$status->play_time?></span>
		</div>
		<div class="song-list">
			<span><?=__( 'Recent Played: ', 'music_stream' )?></span>
			<?php $min_loop = min( count( $status->songs ), 5 ); ?>
			<ul>
				<?php for( $i = 0; $i < $min_loop; ++$i ) : ?>
					<li><span><?=$status->songs[ $i ]?></span></li>
				<?php endfor; ?>
			</ul>
		</div>
	</div>
<?php else: ?>
	<p><?=__('Player is stopped.', 'music_stream' )?></p>
<?php endif; ?>

<div>
	<button id="stop_music"><?=__( 'Stop Music', 'music_stream' ) ?></button>
</div>

<ul id="music_stream_list">
<?php
/** @var \WP_Post $post */
foreach( $posts as &$post ) : ?>
<li><a href="#" data-post-id="<?=$post->ID?>"><?=$post->post_title?></a></li>
<?php endforeach; ?>
</ul>


