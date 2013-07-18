jQuery(document).ready(function($) {
    
    make_switchable_form('#new_comment_form_id');
    
    var reload_all_comments = function() {
        var url = global_base_url + 'index.php/admin_task_sets/all_comments/' + task_set_id;
        var target = '#comments_content_id';
        api_ajax_load(url, target);
    };
    
    reload_all_comments();
    
    $('#new_comment_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = '#new_comment_form_id';
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_comment_form_id .flash_message.message_success').length > 0) {
                reload_all_comments();
            }
        };
        api_ajax_load(url, target, 'post', data, success);
    });
    
    var reload_my_comments_settings = function() {
        var url = global_base_url + 'index.php/admin_task_sets/my_comments_settings/' + task_set_id;
        var target = '#my_comments_settings_id';
        api_ajax_load(url, target);
    };
    
    reload_my_comments_settings();
    
    $(document).on('click', '#my_comments_settings_id a.subscribe, #my_comments_settings_id a.unsubscribe', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        api_ajax_update(url, 'post', {}, function(output) {
            if (output !== undefined && output.result !== undefined && output.message !== undefined) {
                if (output.result) {
                    show_notification(output.message, 'success');
                    reload_my_comments_settings();
                } else {
                    show_notification(output.message, 'error');
                }
            }
        });
    });
    
    $(document).on('click', '#comments_content_id a.delete_comment', function(event) {
        event.preventDefault();
        if (confirm(delete_question)) {
            var url = $(this).attr('href');
            api_ajax_update(url, 'post', {}, function(output) {
                if (output !== undefined && output.result !== undefined && output.message !== undefined) {
                    if (output.result) {
                        show_notification(output.message, 'success');
                        reload_all_comments();
                    } else {
                        show_notification(output.message, 'error');
                    }
                }
            });
        }
    });
    
    $(document).on('click', '#comments_content_id a.approve_comment', function(event) {
        event.preventDefault();
        if (confirm(approve_question)) {
            var url = $(this).attr('href');
            api_ajax_update(url, 'post', {}, function(output) {
                if (output !== undefined && output.result !== undefined && output.message !== undefined) {
                    if (output.result) {
                        show_notification(output.message, 'success');
                        reload_all_comments();
                    } else {
                        show_notification(output.message, 'error');
                    }
                }
            });
        }
    });
    
    $(document).on('click', '#comments_content_id a.reply_at', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.fancybox(url, {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            beforeClose: function() {
                reload_all_comments();
                return true;
            }
        });
    });
    
});


