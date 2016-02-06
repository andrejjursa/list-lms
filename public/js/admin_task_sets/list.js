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
            field_filter_checkbox('#fields_config_content_type_checkbox_id', '#filter_form_id', 'content_type');
            field_filter_checkbox('#fields_config_course_checkbox_id', '#filter_form_id', 'course');
            field_filter_checkbox('#fields_config_group_checkbox_id', '#filter_form_id', 'group');
            field_filter_checkbox('#fields_config_task_set_type_checkbox_id', '#filter_form_id', 'task_set_type');
            field_filter_checkbox('#fields_config_tasks_checkbox_id', '#filter_form_id', 'tasks');
            field_filter_checkbox('#fields_config_published_checkbox_id', '#filter_form_id', 'published');
            field_filter_checkbox('#fields_config_publish_start_time_checkbox_id', '#filter_form_id', 'publish_start_time');
            field_filter_checkbox('#fields_config_upload_end_time_checkbox_id', '#filter_form_id', 'upload_end_time');
            field_filter_checkbox('#fields_config_project_selection_deadline_checkbox_id', '#filter_form_id', 'project_selection_deadline');
            sort_table('table.task_sets_table', '#filter_form_id');
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    };
    
    $('#new_task_set_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_task_set_form_id .flash_message.message_success').length > 0) {
                reload_all_task_sets();
            }
            $.getScript(global_base_url + 'public/js/admin_task_sets/form.js');
            $('#new_task_set_form_id').formErrorWarning();
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
                if (output === true) {
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
    
    $(document).on('click', '#table_content_id a.clone_task_set', function(event) {
        event.preventDefault();
        if (confirm(messages.clone_question)) {
            var url = $(this).attr('href');
            api_ajax_update(url, 'post', {}, function(output) {
                if (output.result !== undefined && output.message !== undefined) {
                    show_notification(output.message, output.result ? 'success' : 'error');
                    if (output.result === true) {
                        reload_all_task_sets();
                    }
                }
            });
        }
    });
    
    $('#filter_form_id').activeForm();
    
    var last_course_id = null;
    
    $('#filter_form_id div.field.group_select_field').setActiveFormDisplayCondition(function() {
        var course_id = $('select[name="filter[course]"]').val();
        if (course_id !== '') {
            if (course_id !== last_course_id) {
                var selected_id = $('#filter_form_id input[name=filter_selected_group_id]').val() !== undefined ? $('#filter_form_id input[name=filter_selected_group_id]').val() : '';
                var target = $('#filter_group_id');
                update_select_values_by(target, course_id, all_groups, selected_id);
                last_course_id = course_id;
            }
            return true;
        } else {
            return false;
        }
    });
    $('form div.group_select_field_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.group_select_field');
    });
    
    $('#filter_form_id').activeForm().applyConditions();
    
    $(document).on('click', '#table_content_id a.preview_task_set', function(event) {
        event.preventDefault();
        $.fancybox($(this).attr('href'), {
            type: 'iframe',
            width: '75%',
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

    $(document).on('click', '#table_content_id a.change_publication_status', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        console.log(url);
        api_ajax_update(url, 'post', {}, function(output) {
            console.log(output);
            if (typeof output.status !== undefined && typeof output.status !== undefined) {
                if (output.status) {
                    reload_all_task_sets();
                    show_notification(output.message, 'success');
                } else {
                    show_notification(output.message, 'error');
                }
            }
        });
    });
    
    reload_all_task_sets();
});