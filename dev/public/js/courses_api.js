jQuery(document).ready(function($) {
    
    var reload_table_content = function() {
        api_ajax_load(global_base_url + 'index.php/admin_courses/get_table_content', '#table_content');
    }
    
    reload_table_content();
    
    $('#new_course_form_id').submit(function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var url = $(this).attr('action');
        var success = function(html) {
            if ($('#new_course_form_id .flash_message.message_success').length > 0) {
                reload_table_content();
            }
            $.getScript(global_base_url + 'public/js/courses/form.js');
        };
        api_ajax_load(url, '#new_course_form_id', 'post', data, success);
    });
    
    var delete_course = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, reload_table_content);
    };
    
    $(document).on('click', '#table_content a.delete', delete_course);
    
});