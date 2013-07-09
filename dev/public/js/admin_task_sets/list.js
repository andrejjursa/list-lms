jQuery(document).ready(function($) {
    
    make_switchable_form('#new_task_set_form_id');
    make_filter_form('#filter_form_id');
    
    var reload_all_task_sets = function() {
        var url = global_base_url + 'index.php/admin_task_sets/get_all_task_sets';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            fields_filter('#open_fields_config_id', reload_all_task_sets);
            field_filter_checkbox('#fields_config_created_checkbox_id', '#filter_form_id', 'created');
            field_filter_checkbox('#fields_config_updated_checkbox_id', '#filter_form_id', 'updated');
            field_filter_checkbox('#fields_config_name_checkbox_id', '#filter_form_id', 'name');
            field_filter_checkbox('#fields_config_course_checkbox_id', '#filter_form_id', 'course');
            field_filter_checkbox('#fields_config_group_checkbox_id', '#filter_form_id', 'group');
            field_filter_checkbox('#fields_config_task_set_type_checkbox_id', '#filter_form_id', 'task_set_type');
            field_filter_checkbox('#fields_config_tasks_checkbox_id', '#filter_form_id', 'tasks');
            field_filter_checkbox('#fields_config_published_checkbox_id', '#filter_form_id', 'published');
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    };
    
    reload_all_task_sets();
    
    $('#new_task_set_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_task_set_form_id .flash_message.message_success').length > 0) {
                reload_all_task_sets();
            }
            $.getScript(global_base_url + 'public/js/admin_task_sets/form.js');
        };
        api_ajax_load(url, '#new_task_set_form_id', 'post', data, success);
    });
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_all_task_sets();
    });
    
    $(document).on('change', '#table_pagination_footer_id select[name=paging_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[page]"]').val(value);
        reload_all_task_sets();
    });
    
    $(document).on('change', '#table_pagination_footer_id select[name=paging_rows_per_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[rows_per_page]"]').val(value);
        reload_all_task_sets();
    });
    
    $(document).on('click', '#table_content_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function(output) {
                if (output == true) {
                    reload_all_task_sets();
                    show_notification(messages.after_delete, 'success');    
                }
            });
        }
    });
    
    $(document).on('click', '#table_content_id a.open_task_set_button', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        api_ajax_load(url, '#header_open_task_set_id', 'get', {}, function() {
            reload_all_task_sets();
            show_notification(messages.after_open, 'success');
        });
    });
});