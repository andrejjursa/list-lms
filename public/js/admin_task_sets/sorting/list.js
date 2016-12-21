jQuery(document).ready(function($) {
    make_filter_form('#filter_form_id');

    var load_sorting_task_sets = function () {
        var url = global_base_url + 'index.php/admin_task_sets/get_all_task_sets_sorting';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {

        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    };

    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        load_sorting_task_sets();
    });

    load_sorting_task_sets();
});