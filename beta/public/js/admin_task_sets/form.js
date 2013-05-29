jQuery(document).ready(function($) {
    var last_course_id = '';
    var last_course_id2 = '';
    var last_group_id = '';
    $('form').activeForm({
        speed: 0
    });
    $('form div.field.task_set_type_field').setActiveFormDisplayCondition(function() {
        var course_id = $('#taks_set_course_id_id').val();
        if (course_id != '') {
            if (course_id != last_course_id) {
                var task_set_id = $('form input[name=task_set_id]').val() != undefined ? $('form input[name=task_set_id]').val() : '';
                var selected_id = $('form input[name=post_selected_task_set_type_id]').val() != undefined ? $('form input[name=post_selected_task_set_type_id]').val() : '';
                var url = global_base_url + 'index.php/admin_task_sets/get_task_set_types/' + course_id + '/' + selected_id + '/' + task_set_id;
                var target = '#taks_set_task_set_type_id_id';
                api_ajax_load(url, target);
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
        if (course_id != '') {
            if (course_id != last_course_id2) {
                var task_set_id = $('form input[name=task_set_id]').val() != undefined ? $('form input[name=task_set_id]').val() : '';
                var selected_id = $('form input[name=post_selected_group_id_id]').val() != undefined ? $('form input[name=post_selected_group_id_id]').val() : '';
                var url = global_base_url + 'index.php/admin_task_sets/get_task_set_groups/' + course_id + '/' + selected_id + '/' + task_set_id;
                var target = '#taks_set_group_id_id';
                api_ajax_load(url, target);
                last_course_id2 = course_id;
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
        var group_id = $('#taks_set_group_id_id').val() == undefined ? $('form input[name=post_selected_group_id_id]').val() : $('#taks_set_group_id_id').val();
        if (course_id != '' && group_id != undefined && group_id != null && group_id != '' && group_id != '0') {
            if (group_id != last_group_id) {
                var task_set_id = $('form input[name=task_set_id]').val() != undefined ? $('form input[name=task_set_id]').val() : '';
                var selected_id = $('form input[name=post_selected_room_id_id]').val() != undefined ? $('form input[name=post_selected_room_id_id]').val() : '';
                var url = global_base_url + 'index.php/admin_task_sets/get_task_set_group_rooms/' + course_id + '/' + group_id + '/' + selected_id + '/' + task_set_id;
                var target = '#taks_set_room_id_id';
                api_ajax_load(url, target);
                last_group_id = group_id;
            }
            return true;
        } else {
            return false;
        }
    });
    $('form div.field.task_set_room_field_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.task_set_room_field');
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