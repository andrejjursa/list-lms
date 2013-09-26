jQuery(document).ready(function($) {
    
    make_switchable_form('#new_solution_form_id');
    make_filter_form('#filter_form_id');
    
    var refresh_all_solutions = function() {
        var data = $('#filter_form_id').serializeArray();
        var url = global_base_url + 'index.php/admin_solutions/get_solutions_list_for_task_set/' + task_set_id;
        var target = '#table_content_id';
        api_ajax_load(url, target, 'post', data);
    };
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        refresh_all_solutions();
    });
    
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
    
    $(document).on('click', '#table_content_id a.open_valuation_dialog', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.fancybox(url, {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            helpers: {
                overlay: {
                    css: {
                        background: 'rgba(255,255,255,0)'
                    }
                }
            },
            beforeClose: function() {
                refresh_all_solutions();
                return true;
            }
        });
    });
    
});