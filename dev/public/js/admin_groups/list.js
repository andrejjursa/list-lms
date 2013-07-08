jQuery(document).ready(function($) {
    
    make_switchable_form('#groups_form_id');
    make_filter_form('#filter_form_id');
    
    var reload_all_groups = function() {
        var data = $('#filter_form_id').serializeArray();
        var url = global_base_url + 'index.php/admin_groups/get_table_content';
        api_ajax_load(url, '#table_of_groups_container_id', 'post', data, function() {
            fields_filter('#open_fields_config_id', reload_all_groups);
            field_filter_checkbox('#fields_config_created_checkbox_id', '#filter_form_id', 'created');
            field_filter_checkbox('#fields_config_updated_checkbox_id', '#filter_form_id', 'updated');
            field_filter_checkbox('#fields_config_name_checkbox_id', '#filter_form_id', 'name');
            field_filter_checkbox('#fields_config_course_checkbox_id', '#filter_form_id', 'course');
            field_filter_checkbox('#fields_config_rooms_checkbox_id', '#filter_form_id', 'rooms');
            field_filter_checkbox('#fields_config_capacity_checkbox_id', '#filter_form_id', 'capacity');
        });
    };
    
    reload_all_groups();
    
    $('#groups_form_id').submit(function (event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = '#groups_form_id';
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#groups_form_id .flash_message.message_success').length > 0) {
                reload_all_groups();
            }
            $.getScript(global_base_url + 'public/js/admin_groups/form.js');
        };
        api_ajax_load(url, target, 'post', data, success);
    });
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_all_groups();
    });
    
    $(document).on('click', '#table_of_groups_container_id a.rooms_editor', function(event) {
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
                reload_all_groups();
                return true;
            }
        });
    });
    
    $(document).on('click', '#table_of_groups_container_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function(output) {
                if (output == true) {
                    reload_all_groups();
                    show_notification(messages.after_delete, 'success');
                }
            });
        }
    });
    
});