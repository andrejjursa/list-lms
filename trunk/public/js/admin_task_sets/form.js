jQuery(document).ready(function($) {
    var last_course_id = '';
    var last_course_id2 = '';
    var last_group_id = '';
    $('form').activeForm();
    $('form div.field.task_set_type_field').setActiveFormDisplayCondition(function() {
        var course_id = $('#taks_set_course_id_id').val();
        var content_type = $('#task_set_content_type_id').val();
        if (course_id !== '' && content_type === 'task_set') {
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
        var content_type = $('#task_set_content_type_id').val();
        if (content_type === 'project') { return false; }
        return !this.isDisplayed('div.field.task_set_type_field');
    });
    $('form div.field.task_set_group_field').setActiveFormDisplayCondition(function() {
        var course_id = $('#taks_set_course_id_id').val();
        var content_type = $('#task_set_content_type_id').val();
        if (course_id !== '' && content_type === 'task_set') {
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
        var content_type = $('#task_set_content_type_id').val();
        var group_id = $('#taks_set_group_id_id').val() === undefined ? $('form input[name=post_selected_group_id_id]').val() : $('#taks_set_group_id_id').val();
        if (course_id !== '' && group_id !== undefined && group_id !== null && group_id !== '' && group_id !== '0' && content_type === 'task_set') {
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
    $('form div.field.task_set_comments_enabled').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set';
    });
    $('form div.task_set_comments_moderated').setActiveFormDisplayCondition(function() {
        var comments_enabled_checkbox = $('#task_set_comments_enabled_id');
        var content_type = $('#task_set_content_type_id').val();
        if (comments_enabled_checkbox.is(':checked') && content_type === 'task_set') {
            return true;
        }
        return false;
    });
    $('form div.field.task_set_comments_moderated_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.task_set_comments_moderated');
    });
    $('form div.field.task_set_points_override_enabled').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set';
    });
    $('form div.field.task_set_points_override_enabled_project').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'project';
    });
    $('form div.field.task_set_points_override').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        if (content_type === 'project') {
            $('div.field.task_set_points_override label').addClass('required');
            $('#task_set_upload_end_time_hint_id').hide();
            $('div.field.task_set_upload_end_time label').addClass('required');
        } else {
            $('div.field.task_set_points_override label').removeClass('required');
            $('#task_set_upload_end_time_hint_id').show();
            $('div.field.task_set_upload_end_time label').removeClass('required');
        }
        return $('#task_set_points_override_enabled_id').is(':checked') || content_type === 'project';
    });
    $('form div.field.task_sets_form_label_allowed_file_types').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set';
    });
    $('form div.field.task_sets_form_label_allowed_test_types').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set';
    });
    $('form div.field.task_sets_form_label_enable_tests_scoring').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set';
    });
    $('form div.field.task_set_test_min_needed').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set' && $('#task_set_enable_tests_scoring_id').is(':checked');
    });
    $('form div.field.task_set_test_max_allowed').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set' && $('#task_set_enable_tests_scoring_id').is(':checked');
    });
    $('form div.field.task_set_project_selection_deadline').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'project';
    });
    $('form div.field.task_set_test_priority').setActiveFormDisplayCondition(function() {
        var content_type = $('#task_set_content_type_id').val();
        return content_type === 'task_set';
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
    $('#task_set_project_selection_deadline_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
});