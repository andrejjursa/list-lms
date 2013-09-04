jQuery(document).ready(function($) {
    
    prettyPrint();
    
    api_make_tabs('tabs');
    
    $(document).on('click', 'a.button.subscribe, a.button.unsubscribe', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var target = $(this).parents('div.comments_wrap').parent();
        api_ajax_load(url, target);
    });
    
    $(document).on('submit', '#comment_form_id', function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var target = $(this).parents('div.comments_wrap').parent();
        var success = function() {
            if (target.find('.flash_message.message_error').length > 0) {
                $.scrollTo(target.find('.flash_message.message_error'), 0, { margin: true, offset: { top: -30 } });
            }
        };
        api_ajax_load(url, target, 'post', data, success);
    });
    
    $(document).on('click', 'a.button.reply_at', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var target = $(this).parents('div.comments_wrap').parent();
        $.fancybox(url, {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            beforeClose: function() {
                var url = global_base_url + 'index.php/tasks/show_comments/' + task_id;
                api_ajax_load(url, target);
                return true;
            }
        });
    });
    
    var autoscroll = true;
    var url_anchor = api_read_url_anchor();
    if (url_anchor.substring(0, 8) === 'comments') {
        var comment_id = url_anchor.substring(9);
        if (comment_id !== '') {
            $(document).ajaxSuccess(function() {
                if (autoscroll) {
                    autoscroll = false;
                    setTimeout(function() {
                        $.scrollTo($('div.comments_wrap li.comment_id_' + comment_id), 0, { margin: true, offset: { top: -30 } });
                    }, 100);
                }
            });
        }
        $('#tabs li.comments_tab a').trigger('click');
    }
    
});