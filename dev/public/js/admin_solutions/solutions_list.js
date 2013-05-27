jQuery(document).ready(function($) {
    
    var refresh_all_solutions = function() {
        var url = global_base_url + 'index.php/admin_solutions/get_solutions_list_for_task_set/' + task_set_id;
        var target = '#table_content_id';
        api_ajax_load(url, target);
    }
    
    refresh_all_solutions();
    
});