jQuery(document).ready(function($) {

    var toggle_upload_solution = function() {
        if ($('select[name="task_set_type[join_virtual]"]').val() === "1") {
            $('#task_set_type_join_upload_solution_field_id').hide();
            $('select[name="task_set_type[join_upload_solution]"]').val("0");
            $('#task_set_type_join_formula_field_id').show();
        } else {
            $('#task_set_type_join_upload_solution_field_id').show()
            $('#task_set_type_join_formula_field_id').hide();
        }
    }
    
    if ($('#new_course_form_id').length !== 0) {
        make_switchable_form('#new_course_form_id');
        
        var reload_table_content = function() {
            var filter_data = $('#filter_form_id').serializeArray();
            api_ajax_load(global_base_url + 'index.php/admin_courses/get_table_content', '#table_content', 'post', filter_data, function() {
                fields_filter('#open_fields_config_id', reload_table_content);
                field_filter_checkbox('#fields_config_created_checkbox_id', '#filter_form_id', 'created');
                field_filter_checkbox('#fields_config_updated_checkbox_id', '#filter_form_id', 'updated');
                field_filter_checkbox('#fields_config_name_checkbox_id', '#filter_form_id', 'name');
                field_filter_checkbox('#fields_config_description_checkbox_id', '#filter_form_id', 'description');
                field_filter_checkbox('#fields_config_period_checkbox_id', '#filter_form_id', 'period');
                field_filter_checkbox('#fields_config_groups_checkbox_id', '#filter_form_id', 'groups');
                field_filter_checkbox('#fields_config_task_set_types_checkbox_id', '#filter_form_id', 'task_set_types');
                field_filter_checkbox('#fields_config_task_set_count_checkbox_id', '#filter_form_id', 'task_set_count');
                field_filter_checkbox('#fields_config_capacity_checkbox_id', '#filter_form_id', 'capacity');
                sort_table('#table_content table', '#filter_form_id');
            });
        };
        
        reload_table_content();
        
        $('#filter_form_id').submit(function(event) {
            event.preventDefault();
            reload_table_content();
        });
        
        $('#new_course_form_id').submit(function(event) {
            event.preventDefault();
            var data = $(this).serializeArray();
            var url = $(this).attr('action');
            var success = function(html) {
                if ($('#new_course_form_id .flash_message.message_success').length > 0) {
                    reload_table_content();
                }
                $.getScript(global_base_url + 'public/js/admin_courses/form.js');
                $('#new_course_form_id').formErrorWarning();
            };
            api_ajax_load(url, '#new_course_form_id', 'post', data, success);
        });
        
        var delete_course = function(event) {
            event.preventDefault();
            if (!confirm(messages.delete_question)) { return; }
            
            var url = $(this).attr('href');
            
            api_ajax_update(url, 'post', {}, function(output) {
                if (output === true) {
                    reload_table_content();
                    show_notification(messages.after_delete, 'success');
                }
            });
        };
        
        $(document).on('click', '#table_content a.delete', delete_course);
        
        $(document).on('click', '#table_content a.mail_to_course', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            $.fancybox(url, {
                type: 'iframe',
                width: '100%',
                height: '100%',
                autoSize: false,
                autoHeight: false,
                autoWidth: false,
                helpers: {
                    overlay: {
                        css: {
                            background: 'rgba(255,255,255,0)'
                        }
                    }
                }
            });
        });
        
    } else if ($('#add_task_set_type_form_id').length !== 0) {
        var reload_table_content = function() {
            api_ajax_load(global_base_url + 'index.php/admin_courses/get_task_set_types/course_id/' + current_course, '#table_content_id');
        };
        
        reload_table_content(); 
        toggle_upload_solution();

        $(document).on('change', 'select[name="task_set_type[join_virtual]"]', toggle_upload_solution);
        
    } else if ($('#edit_task_set_type_form_id').length !== 0) {
        toggle_upload_solution();

        $(document).on('change', 'select[name="task_set_type[join_virtual]"]', toggle_upload_solution);
    }
});