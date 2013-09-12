jQuery(document).ready(function($) {
    
    make_switchable_form('#new_period_form_id');
    
    var reload_all_periods = function() {
        var filter_data = $('#filter_form_id').serializeArray();
        api_ajax_load(global_base_url + 'index.php/admin_periods/ajax_periods_list', '#periods_container_id', 'post', filter_data, function() {
            fields_filter('#open_fields_config_id', reload_all_periods);
            field_filter_checkbox('#fields_config_created_checkbox_id', '#filter_form_id', 'created');
            field_filter_checkbox('#fields_config_updated_checkbox_id', '#filter_form_id', 'updated');
            field_filter_checkbox('#fields_config_name_checkbox_id', '#filter_form_id', 'name');
            field_filter_checkbox('#fields_config_related_courses_checkbox_id', '#filter_form_id', 'related_courses');
        });
    };
    
    $('#new_period_form_id').submit(function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#new_period_form_id .flash_message.message_success').length > 0) {
                reload_all_periods();
            }
            $.getScript(global_base_url + 'public/js/admin_periods/form.js');
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
        
        api_ajax_update(url, 'post', {}, function(output) {
            if (output == true) {
                reload_all_periods();
                show_notification(messages.after_delete, 'success');    
            }
        });
    };
    
    $(document).on('click', '#periods_container_id a.button_up', up_down_period);
    $(document).on('click', '#periods_container_id a.button_down', up_down_period);
    $(document).on('click', '#periods_container_id a.button_delete', delete_period);
    
    reload_all_periods();
    
});