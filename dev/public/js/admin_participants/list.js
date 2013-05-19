jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');
    
    var reload_all_participants = function() {
        var url = global_base_url + 'index.php/admin_participants/table_content';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            $('#table_pagination_footer_id').html('');
            $('#table_content_id #pagination_row_id').appendTo($('#table_pagination_footer_id'));
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    }
    
    reload_all_participants();
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_all_participants();
    });
    
    var filter_last_course_id = '';
    
    $('#filter_form_id').activeForm({
        speed: 0,
        hiddenClass: 'hidden'
    }).setDisplayCondition('div.group_field', function() {
        var filter_course_id = this.findElement('select[name="filter[course]"]').val();
        if (filter_course_id > 0 && $('#filter_group_set_none_id:checked').length == 0) {
            if (filter_course_id != filter_last_course_id) {
                var selected_id = this.findElement('input[name=filter_selected_group_id]').val() != undefined ? this.findElement('input[name=filter_selected_group_id]').val() : '0';
                var url = global_base_url + 'index.php/admin_participants/get_groups_from_course/' + filter_course_id + '/' + selected_id;
                var target = $('#filter_group_id');
                api_ajax_load(url, target);
                filter_last_course_id = filter_course_id;
            }
            return true;
        }
        return false;
    }).setDisplayCondition('div.group_field_else', function() {
        return !this.isDisplayed('div.group_field');
    });
    $('#filter_form_id').activeForm().applyConditions();
    
    
    $(document).on('click', 'a.button.participation_approve, a.button.participation_disapprove, a.button.participation_delete', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        api_ajax_update(url, 'post', {}, function(data) {
            if (data.status != undefined) {
                if (data.status) {
                    show_notification(data.message, 'success');
                    reload_all_participants();
                } else {
                    show_notification(data.message, 'error');
                }
            }
        });
    });
    
    /*make_filter_form('#filter_form_id');
    
    reload_all_tasks = function() {
        var url = global_base_url + 'index.php/admin_tasks/get_all_tasks';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            $('#table_pagination_footer_id').html('');
            $('#table_content_id #pagination_row_id').appendTo($('#table_pagination_footer_id'));
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    }
    
    reload_all_tasks();
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_all_tasks();
    });
    
    $(document).on('change', '#table_pagination_footer_id select[name=paging_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[page]"]').val(value);
        reload_all_tasks();
    });
    
    $(document).on('change', '#table_pagination_footer_id select[name=paging_rows_per_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[rows_per_page]"]').val(value);
        reload_all_tasks();
    });
    
    $(document).on('click', '#table_content_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function(output) {
                if (output == true) {
                    reload_all_tasks();
                    show_notification(messages.after_delete, 'success');    
                }
            });
        }
    });
    
    $(document).on('click', '#table_content_id a.preview', function(event) {
        event.preventDefault();
        $.fancybox($(this).attr('href'), {
            type: 'iframe',
            width: '75%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false
        })
    });
    
    $(document).on('click', '#table_content_id a.add_to_task_set', function(event) {
        event.preventDefault();
        $.fancybox($(this).attr('href'), {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            beforeClose: function() {
                reload_all_tasks();
                var url = global_base_url + 'index.php/admin_tasks/get_metainfo_open_task_set';
                api_ajax_load(url, '#header_open_task_set_id');
                return true;
            }
        })
    });*/
    
});