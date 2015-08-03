<form name="form" method="post" action="<?=admin_url( 'options.php' ) ?>">
	<?php settings_fields( 'music_stream' ); ?>
	<?php do_settings_sections( 'music_stream_settings' ); ?>
	<?php submit_button(); ?>
</form>