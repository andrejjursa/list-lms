jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');
    
    var reload_all_task_sets = function() {
        var url = global_base_url + 'index.php/admin_solutions/get_task_set_list';
        var target = '#table_content_id';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            $('#table_pagination_footer_id').html('');
            $('#table_content_id #pagination_row_id').appendTo($('#table_pagination_footer_id'));
        };
        api_ajax_load(url, target, 'post', data, onSuccess);
    };
    
    reload_all_task_sets();
    
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
    
    var filter_last_course_id = '';
    
    $('#filter_form_id').activeForm({
        speed: 0,
        hiddenClass: 'hidden'
    }).setDisplayCondition('div.group_field', function() {
        var filter_course_id = this.findElement('select[name="filter[course]"]').val();
        if (filter_course_id > 0) {
            if (filter_course_id !== filter_last_course_id) {
                var selected_id = this.findElement('input[name=filter_selected_group_id]').val() !== undefined ? this.findElement('input[name=filter_selected_group_id]').val() : '0';
                var url = global_base_url + 'index.php/admin_solutions/get_groups_from_course/' + filter_course_id + '/' + selected_id;
                var target = $('#filter_group_id');
                api_ajax_load(url, target, 'post', {}, function() {
                    update_filter_group();
                });
                filter_last_course_id = filter_course_id;
            }
            return true;
        }
        return false;
    }).setDisplayCondition('div.group_field_else', function() {
        return !this.isDisplayed('div.group_field');
    });
    $('#filter_form_id').activeForm().applyConditions();
    
    var update_filter_group = function() {
        $('#filter_form_id input[name="filter[group]"]').val($('#filter_group_id').val());
    };
    
    $(document).on('change', '#filter_group_id', update_filter_group);
    
    sort_table('table.task_sets_table', '#filter_form_id');
    
    var remove_points_dialog_submit_to_url = '';
    var remove_points_dialog = $('#remove_points_dialog_id');
    
    $(document).on('click', 'table.task_sets_table a.button.remove_points', function(event) {
        event.preventDefault();
        var regexp = /points\:([0-9]+(\.[0-9]+)?)/i;
        var data = regexp.exec($(this).attr('class'));
        var default_points = 3;
        if (data !== null) {
            default_points = data[1];
        }
        remove_points_dialog_submit_to_url = $(this).attr('href');
        create_remove_points_dialog(default_points);
    });
    
    var submit_and_close_dialog = function() {
        var data = remove_points_dialog.find('form').serializeArray();
        api_ajax_update(remove_points_dialog_submit_to_url, 'post', data, function(output) {
            if (output.result !== undefined && output.message !== undefined) {
                if (output.result === true) {
                    show_notification(output.message, 'success');
                    remove_points_dialog.find('form').each(function() {
                        this.reset();
                    });
                    remove_points_dialog.dialog('close');
                    reload_all_task_sets();
                } else {
                    show_notification(output.message, 'error');
                }
            }
        });
    };
    
    var create_remove_points_dialog = function(default_points) {
        remove_points_dialog.dialog({
            modal: true,
            autoOpen: true,
            width: 500,
            buttons: [
                {
                    text: remove_points_dialog_ok_button,
                    click: submit_and_close_dialog
                },
                {
                    text: remove_points_dialog_cancel_button,
                    click: function() { 
                        $(this).find('form').each(function() {
                            this.reset();
                        });
                        $(this).dialog('close');
                    }
                }
            ],
            close: function() {
                $(this).find('form').each(function() {
                    this.reset();
                });
            }
        }).submit(function(event){
            event.preventDefault();
            submit_and_close_dialog();
        }).find('input[name=points]').val(default_points);
    };
    
});