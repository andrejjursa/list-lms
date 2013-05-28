jQuery(document).ready(function($) {
    
    var refresh_all_solutions = function() {
        var url = global_base_url + 'index.php/admin_solutions/get_solutions_list_for_task_set/' + task_set_id;
        var target = '#table_content_id';
        api_ajax_load(url, target);
    }
    
    refresh_all_solutions();
    
    $('#new_solution_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_solution_form_id .flash_message.message_success').length > 0) {
                refresh_all_solutions();
            }
        };
        api_ajax_load(url, '#new_solution_form_id', 'post', data, success);
    });
    
});