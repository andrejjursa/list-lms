jQuery(document).ready(function($){
    
    make_filter_form('#filter_form_id');
    
    var reload_logs = function() {
        var url = global_base_url + 'index.php/admin_logs/all_logs';
        var data = $('#filter_form_id').serializeArray();
        api_ajax_load(url, '#table_content_id', 'post', data);
    };
    
    reload_logs();
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_logs();
    });
    
    $(document).on('change', '#table_content_id select[name=paging_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[page]"]').val(value);
        reload_logs();
    });
    
    $(document).on('change', '#table_content_id select[name=paging_rows_per_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[rows_per_page]"]').val(value);
        reload_logs();
    });
    
    $(document).on('click', '#table_content_id a.button.details', function(event) {
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
            }
        });
    });
    
});