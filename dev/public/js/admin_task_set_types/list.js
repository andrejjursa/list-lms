jQuery(document).ready(function($) {
    
    make_switchable_form('#new_task_set_type_form_id');
    
    var reload_all_task_set_types = function() {
        api_ajax_load(global_base_url + 'index.php/admin_task_set_types/get_table_content', '#task_set_types_table_content_id');
    };
    
    reload_all_task_set_types();
    
    $('#new_task_set_type_form_id').submit(function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_task_set_type_form_id .flash_message.message_success').length > 0) {
                reload_all_task_set_types();
            }
            $.getScript(global_base_url + 'public/js/admin_task_set_types/form.js');
        }
        api_ajax_load($(this).attr('action'), '#new_task_set_type_form_id', 'post', data, success);
    });
    
    var delete_task_set_type = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, function(output) {
            if (output == true) {
                reload_all_task_set_types();
                show_notification(messages.after_delete, 'success');
            }
        });
    };
    
    $(document).on('click', '#task_set_types_table_content_id a.delete', delete_task_set_type);
    
});