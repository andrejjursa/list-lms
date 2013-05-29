jQuery(document).ready(function($) {
    
    make_switchable_form('#add_participant_form_id');
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
    
    $('#add_participant_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        api_ajax_load(url, '#add_participant_form_id', 'post', data, function() {
            if ($('#add_participant_form_id .flash_message.message_success').length > 0) {
                reload_all_participants();
            }
            $.getScript(global_base_url + 'public/js/admin_participants/form.js');
        });
    });
    
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
    
    $(document).on('click', 'a.button.participation_approve', function(event) {
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
    
    $(document).on('click', 'a.button.participation_disapprove', function(event) {
        event.preventDefault();
        if (!confirm(messages.disapprove_question)) { return; }
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
    
    $(document).on('click', 'a.button.participation_delete', function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
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
    
    $(document).on('click', '#table_content_id a.group_column', function(event) {
        event.preventDefault();
        var parent_td = $(this).parents('td.group_column');
        var course_group = $(this).attr('rel').split(',', 2);
        var url = global_base_url + 'index.php/admin_participants/get_groups_from_course/' + course_group[0] + '/' + course_group[1];
        var target = parent_td.find('span.group_column_edit select');
        api_ajax_load(url, target, 'post', {}, function() {
            parent_td.find('a.group_column').hide();
            parent_td.find('span.group_column_edit').show();
        });
    });
    
    $(document).on('click', '#table_content_id span.group_column_edit input.save_new_group', function(event) {
        event.preventDefault();
        var parent_td = $(this).parents('td.group_column');
        var data = { 'group_id': parent_td.find('span.group_column_edit select').val() };
        var url = global_base_url + 'index.php/admin_participants/change_group/' + $(this).attr('rel');
        api_ajax_load(url, parent_td, 'post', data);
    });
    
});