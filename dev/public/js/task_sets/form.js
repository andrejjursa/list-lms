jQuery(document).ready(function($) {
    var last_course_id = '';
    $('form').activeForm();
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
    $('form').activeForm().applyConditions();
});