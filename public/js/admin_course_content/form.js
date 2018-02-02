jQuery(document).ready(function($) {
    make_overlay_editors();

    var last_course_id = '';

    $('#new_content_form_id').activeForm();

    $('#new_content_form_id div.field.course_content_group_field').setActiveFormDisplayCondition(function () {
        var course_id = $('#course_content_course_id_id').val();
        if (course_id !== last_course_id) {
            var selected_id = $('form input[name=post_selected_course_content_group_id]').val() !== undefined ? $('form input[name=post_selected_course_content_group_id]').val() : '';
            var target = '#course_content_course_content_group_id_id';
            update_select_values_by($(target), course_id, data.all_course_content_groups, selected_id);
            last_course_id = course_id;
        }
        if (course_id == '') {
            return false;
        }
        return true;
    });

    $('#new_content_form_id').activeForm().applyConditions();

    $('#course_content_published_from_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });

    $('#course_content_published_to_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
});