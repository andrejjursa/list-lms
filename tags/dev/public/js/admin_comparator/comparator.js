jQuery(document).ready(function($) {
    var last_course_id = '';
    
    var reload_all_solutions = function() {
        var url = $('form.task_set_form').attr('action');
        api_ajax_load(url, '#solutions_table_content', 'post', $('form.task_set_form').serializeArray());
    };
    
    reload_all_solutions();
    
    $('form.task_set_form').submit(function(event) {
        event.preventDefault();
        reload_all_solutions();
    });
    
    $('form.task_set_form').activeForm();
    
    $('form.task_set_form div.field.field_task_set_selection').setActiveFormDisplayCondition(function() {
        var course_id = $('#task_sets_setup_course_id').val();
        if (course_id !== '') {
            if (course_id !== last_course_id) {
                last_course_id = course_id;
                var selected_id = $('form.task_set_form input[name=post_selected_task_set_setup_task_set]').val() !== undefined ? $('form.task_set_form input[name=post_selected_task_set_setup_task_set]').val() : '';
                update_select_values_by($('#task_sets_setup_task_set_id'), course_id, all_task_sets, selected_id, true, function() {
                    $('form.task_set_form div.field.field_task_set_selection input[name=\'task_sets_setup[task_set]\']').val($('#task_sets_setup_task_set_id').val());
                    $('form.task_set_form').activeForm().applyConditions();
                });
            }
            return true;
        }
        return false;
    });
    
    $('form.task_set_form div.field.field_task_set_selection_else').setActiveFormDisplayCondition(function() {
        return !this.isDisplayed('div.field.field_task_set_selection');
    });
    
    $('form.task_set_form').activeForm().applyConditions();
    
    $('#task_sets_setup_task_set_id').change(function() {
        $('form.task_set_form div.field.field_task_set_selection input[name=\'task_sets_setup[task_set]\']').val($(this).val());
    });
    
    $(document).on('submit', 'form.run_configuration_form', function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var url = $(this).attr('action');
        api_ajax_load(url, '#solutions_table_content', 'post', data);
    });
});

var exec_comparator = function(path, config) {
    var url = global_base_url + 'index.php/admin_comparator/execute/';
    var data = {
        'path': path
    };
    for(var i in config) {
        data['config[' + i + ']'] = config[i];
    }
    api_ajax_load(url, '#protocol_id', 'post', data, function() {
        
    });
};