jQuery(document).ready(function($) {
    
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
            $.getScript(global_base_url + 'public/js/task_set_types/form.js');
        }
        api_ajax_load($(this).attr('action'), '#new_task_set_type_form_id', 'post', data, success);
    });
    
    var delete_task_set_type = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, reload_all_task_set_types);
    };
    
    $(document).on('click', '#task_set_types_table_content_id a.delete', delete_task_set_type);
    
    /*var reload_all_periods = function() {
        api_ajax_load(global_base_url + 'index.php/admin_periods/ajax_periods_list', '#periods_container_id');
    };
    
    $('#new_period_form_id').submit(function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#new_period_form_id .flash_message.message_success').length > 0) {
                reload_all_periods();
            }
            $.getScript(global_base_url + 'public/js/periods/form.js');
        };
        api_ajax_load($(this).attr('action'), '#new_period_form_id', 'post', data, success);
    });
    
    var up_down_period = function(event) {
        event.preventDefault();
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, reload_all_periods);
    };
    
    var delete_period = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, reload_all_periods);
    };
    
    $(document).on('click', '#periods_container_id a.button_up', up_down_period);
    $(document).on('click', '#periods_container_id a.button_down', up_down_period);
    $(document).on('click', '#periods_container_id a.button_delete', delete_period);
    
    reload_all_periods();*/
    
});