var histogram_bin_size = 0.5;

jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');

    histogram_bin_size = parseFloat($('#histogram_size_id').val());
    
    var refresh_tasks_list = function() {
        if (task_set_id === 0) { return; }
        var url = global_base_url + 'index.php/admin_solutions/display_tasks_list/' + task_set_id;
        var target = '#task_set_content_id';
        api_ajax_load(url, target, 'post', {}, function() {
            prettyPrint();
        });
    };

    $('#histogramForm').submit(function(event) {
        event.preventDefault();
    });

    $('#histogram_size_id').change(function() {
        histogram_bin_size = parseFloat($(this).val());
        refresh_valuation_list();
        refresh_points_overview(histogram_bin_size);
    });
    
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
    refresh_points_overview(histogram_bin_size);
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        refresh_valuation_list();
        refresh_points_overview(histogram_bin_size);
    });
    
    $('#batch_valuation_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = $(this);
        var data = $(this).serializeArray();
        api_ajax_load(url, target, 'post', data, function() {
            refresh_points_overview(histogram_bin_size);
        });
    });
    
});