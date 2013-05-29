jQuery(document).ready(function($) {
    
    make_switchable_form('#new_teacher_form_id');
    
    var reload_all_teachers = function() {
        var url = global_base_url + 'index.php/admin_teachers/list_teachers_table';
        api_ajax_load(url, '#table_content_id');
    }
    
    reload_all_teachers();
    
    $('#new_teacher_form_id').submit(function (event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = '#new_teacher_form_id';
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#new_teacher_form_id .flash_message.message_success').length > 0) {
                reload_all_teachers();
            }
        };
        api_ajax_load(url, target, 'post', data, success);
    });
    
    $(document).on('click', '#table_content_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function() {
                reload_all_teachers();
                show_notification(messages.after_delete, 'success');
            });
        }
    });
    
});