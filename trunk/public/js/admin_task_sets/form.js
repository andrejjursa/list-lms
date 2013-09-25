jQuery(document).ready(function($) {
    var last_course_id = '';
    var last_course_id2 = '';
    var last_group_id = '';
    $('form').activeForm({
        speed: 0
    });
    $('form div.field.task_set_type_field').setActiveFormDisplayCondition(function() {
        var course_id = $('#taks_set_course_id_id').val();
        if (course_id !== '') {
            if (course_id !== last_course_id) {
                var selected_id = $('form input[name=post_selected_task_set_type_id]').val() !== undefined ? $('form input[name=post_selected_task_set_type_id]').val() : '';
                var target = '#taks_set_task_set_type_id_id';
                update_select_values_by($(target), course_id, all_task_set_types, selected_id);
                last_course_id = course_id;
            }
            return true;
        } else {
            return false;
        }
    });
    $('form div.field.task_set_type_field_msg').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.task_set_type_field');
    });
    $('form div.field.task_set_group_field').setActiveFormDisplayCondition(function() {
        var course_id = $('#taks_set_course_id_id').val();
        if (course_id !== '') {
            if (course_id !== last_course_id2) {
                last_course_id2 = course_id;
                var selected_id = $('form input[name=post_selected_group_id_id]').val() !== undefined ? $('form input[name=post_selected_group_id_id]').val() : '';
                var target = '#taks_set_group_id_id';
                update_select_values_by($(target), course_id, all_groups, selected_id, true, function() {
                    $('form').activeForm().applyConditions();
                });
            }
            return true;
        } else {
            return false;
        }
    });
    $('form div.field.task_set_group_field_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.task_set_group_field');
    });
    $('form div.field.task_set_room_field').setActiveFormDisplayCondition(function() {
        var course_id = $('#taks_set_course_id_id').val();
        var group_id = $('#taks_set_group_id_id').val() === undefined ? $('form input[name=post_selected_group_id_id]').val() : $('#taks_set_group_id_id').val();
        if (course_id !== '' && group_id !== undefined && group_id !== null && group_id !== '' && group_id !== '0') {
            if (group_id !== last_group_id) {
                last_group_id = group_id;
                var selected_id = $('form input[name=post_selected_room_id_id]').val() !== undefined ? $('form input[name=post_selected_room_id_id]').val() : '';
                var target = '#taks_set_room_id_id';
                update_select_values_by($(target), group_id, all_rooms, selected_id);
            }
            return true;
        } else {
            return false;
        }
    });
    $('form div.field.task_set_room_field_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.task_set_room_field');
    });
    $('form div.task_set_comments_moderated').setActiveFormDisplayCondition(function() {
        var comments_enabled_checkbox = $('#task_set_comments_enabled_id');
        if (comments_enabled_checkbox.is(':checked')) {
            return true;
        }
        return false;
    });
    $('form div.field.task_set_comments_moderated_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.task_set_comments_moderated');
    });
    $('form div.field.task_set_points_override').setActiveFormDisplayCondition(function() {
        return $('#task_set_points_override_enabled_id').is(':checked');
    });
    $('form').activeForm().applyConditions();
    
    $('#task_set_publish_start_time_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
    $('#task_set_upload_end_time_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
});