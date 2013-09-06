jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');
    
    make_switchable_form('#new_student_form_id');
    
    sort_table('table.students_table', '#filter_form_id');
    
    var reload_all_students = function() {
        var url = global_base_url + 'index.php/admin_students/table_content';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            $('#table_pagination_footer_id').html('');
            $('#table_content_id #pagination_row_id').appendTo($('#table_pagination_footer_id'));
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    };
    
    reload_all_students();
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_all_students();
    });
    
    $(document).on('change', '#table_pagination_footer_id select[name=paging_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[page]"]').val(value);
        reload_all_students();
    });
    
    $(document).on('change', '#table_pagination_footer_id select[name=paging_rows_per_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[rows_per_page]"]').val(value);
        reload_all_students();
    });
    
    $('#new_student_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_student_form_id .flash_message.message_success').length > 0) {
                reload_all_students();
            }
        };
        api_ajax_load(url, '#new_student_form_id', 'post', data, success);
    });
    
    $(document).on('click', '#table_content_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function(output) {
                if (output == true) {
                    reload_all_students();
                    show_notification(messages.after_delete, 'success');    
                }
            });
        }
    });
    
});