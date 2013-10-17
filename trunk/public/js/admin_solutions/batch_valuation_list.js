jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');
    
    var refresh_tasks_list = function() {
        if (task_set_id === 0) { return; }
        var url = global_base_url + 'index.php/admin_solutions/display_tasks_list/' + task_set_id;
        var target = '#task_set_content_id';
        api_ajax_load(url, target, 'post', {}, function() {
            prettyPrint();
        });
    };
    
    refresh_tasks_list();
    
    var refresh_valuation_list = function() {
        if (task_set_id === 0) { return; }
        //var url = global_base_url + 'index.php/admin_solutions/batch_valuation_list/' + task_set_id;
        var filter_form = $('#filter_form_id');
        var url = filter_form.attr('action');
        var data = filter_form.serializeArray();
        var target = '#batch_valuation_form_id';
        api_ajax_load(url, target, 'post', data);
    };
    
    refresh_valuation_list();
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        refresh_valuation_list();
    });
    
    $('#batch_valuation_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = $(this);
        var data = $(this).serializeArray();
        console.log(data);
        api_ajax_load(url, target, 'post', data);
    });
    
});