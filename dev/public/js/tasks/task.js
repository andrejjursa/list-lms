jQuery(document).ready(function($) {
    
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
        api_ajax_load(url, target, 'post', data);
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
    
});