jQuery(document).ready(function($) {
    
    var refresh_tasks_list = function() {
        if (task_set_id === 0) { return; }
        var url = global_base_url + 'index.php/admin_solutions/display_tasks_list/' + task_set_id;
        var target = '#task_set_content_id';
        api_ajax_load(url, target);
    };
    
    refresh_tasks_list();
    
    var refresh_valuation_list = function() {
        if (task_set_id === 0) { return; }
        var url = global_base_url + 'index.php/admin_solutions/batch_valuation_list/' + task_set_id;
        var target = '#batch_valuation_form_id';
        api_ajax_load(url, target);
    };
    
    refresh_valuation_list();
    
    $('#batch_valuation_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = $(this);
        var data = $(this).serializeArray();
        api_ajax_load(url, target, 'post', data);
    });
    
});