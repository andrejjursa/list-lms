jQuery(document).ready(function($) {
    
    $('form').activeForm({
        speed: 0
    });
    
    var last_group_id = 0;
    
    $('form div.field.task_set_permission_room_field').setActiveFormDisplayCondition(function() {
        var group_id = $('#taks_set_permission_group_id_id').val();
        if (course_id !== '' && group_id !== undefined && group_id !== null && group_id !== '' && group_id !== '0') {
            if (group_id !== last_group_id) {
                last_group_id = group_id;
                var target = '#taks_set_permission_room_id_id';
                var selected = $('input[type=hidden][name=taks_set_permission_room_id]').length === 1 ? $('input[type=hidden][name=taks_set_permission_room_id]').val() : '0';
                update_select_values_by($(target), group_id, all_rooms, selected);
            }
            return true;
        } else {
            return false;
        }
    });
    
    $('form div.field.task_set_permission_room_field_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.task_set_permission_room_field');
    });
    
    $('form').activeForm().applyConditions();
    
    $('#task_permission_set_publish_start_time_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
    $('#task_set_permission_upload_end_time_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
    
});