jQuery(document).ready(function() {
    jQuery('button#stop_music').click(function() {
        jQuery.ajax(ajax_object.ajax_url, {
            'data': {
                'action': 'music_stream_stop'
            },
            'success': function(data) {
                console.log(data);
            }
        });
    });
    jQuery('ul#music_stream_list li > a').click(function() {

        var postId = jQuery(this).data('post-id');
        jQuery.ajax(ajax_object.ajax_url, {
            'data': {
                'action': 'music_stream_play',
                'post-id': postId
            },
            'success': function(data) {
                console.log(data);
            }
        });
    });
});
