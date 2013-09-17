jQuery(document).ready(function ($) {
    
    if (can_execute === true) {
        var url = global_base_url + 'index.php/admin_tests/run_single_test/' + test_id + '/' + file_full_path;
        var target = $('#run_test_output_id')
        api_ajax_update(url, 'post', {}, function(output) {
            if (output.text !== undefined && output.code !== undefined) {
                target.html(output.text);
                if (output.code > 0) {
                    target.css({
                        'color': 'red'
                    });
                }
            }
        });
    }
    
});