<?php
/** @var array $posts array of posts */
?>

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