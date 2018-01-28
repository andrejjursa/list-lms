jQuery(document).ready(function($) {
    
    make_switchable_form('#new_content_group_form_id');
    make_filter_form('#filter_form_id');

    var submit_form = function(event) {
        event.preventDefault();
        var url = $('#new_content_group_form_id').attr('action');
        var data = $('#new_content_group_form_id').serializeArray();
        var success = function() {
            if ($('#new_content_group_form_id .flash_message.message_success').length > 0) {
                reload_content();
            }
            $('#new_content_group_form_id').formErrorWarning();
        };
        api_ajax_load(url, '#new_content_group_form_id', 'post', data, success);
    };

    var reload_content = function() {
        var url = global_base_url + '/admin_course_content_groups/get_all_content_groups';
        var data = $('#filter_form_id').serializeArray();
        api_ajax_load(url, '#table_content', 'post', data, function () {
            sort_table('table.course_content_group_table', '#filter_form_id');
        });
    };

    $('#new_content_group_form_id').submit(function(event) {
        submit_form(event);
    });

    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_content();
    });

    reload_content();

    $(document).on('change', '#table_pagination_footer_id select[name=paging_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[page]"]').val(value);
        reload_content();
    });

    $(document).on('change', '#table_pagination_footer_id select[name=paging_rows_per_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[rows_per_page]"]').val(value);
        reload_content();
    });

});