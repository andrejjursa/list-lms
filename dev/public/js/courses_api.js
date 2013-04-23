jQuery(document).ready(function($) {
    
    if ($('#new_course_form_id').length != 0) {
        
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
        
        $(document).on('click', '#table_content a.task_set_types_editor', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            $.fancybox(url, {
                type: 'iframe',
                width: '100%',
                height: '100%',
                autoSize: false,
                autoHeight: false,
                autoWidth: false,
                beforeClose: function() {
                    reload_table_content();
                    return true;
                }
            });
        });
        
    } else if ($('#add_task_set_type_form_id').length != 0) {
        
        var reload_table_content = function() {
            api_ajax_load(global_base_url + 'index.php/admin_courses/get_task_set_types/course_id/' + current_course, '#table_content_id');
        }
        
        var reload_form = function() {
            api_ajax_load(global_base_url + 'index.php/admin_courses/get_task_set_type_form/course_id/' + current_course, '#add_task_set_type_form_id');
        }
        
        reload_table_content(); 
        
        $('#add_task_set_type_form_id').submit(function(event) {
            event.preventDefault();
            var url = $(this).attr('action');
            var data = $(this).serializeArray();
            var success = function() {
                if ($('#add_task_set_type_form_id .flash_message.message_success').length > 0) {
                    reload_table_content();
                }
            };
            api_ajax_load(url, '#add_task_set_type_form_id', 'post', data, success);
        }); 
        
        $(document).on('click', '#table_content_id a.save_button', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var data = {};
            $(this).parents('tr.task_set_types_table_row').find('select, input').each(function() {
                data[$(this).attr('name')] = $(this).val();
            });
            console.log(url);
            console.log(data);
            api_ajax_update(url, 'post', data, function(output) {
                if (output) {
                    reload_table_content();
                    show_notification(messages.save_success, 'success');
                } else {
                    show_notification(messages.save_failed, 'error');
                }
            }, function() {
                show_notification(messages.save_failed, 'error');
            });
        });  
        
        $(document).on('click', '#table_content_id a.delete', function(event) {
            event.preventDefault();
            
            if (!confirm(messages.delete_question)) { return; }
            
            var url = $(this).attr('href');
            var data = {};
            $(this).parents('tr.task_set_types_table_row').find('select, input').each(function() {
                data[$(this).attr('name')] = $(this).val();
            });
            console.log(url);
            console.log(data);
            api_ajax_update(url, 'post', data, function(output) {
                if (output) {
                    reload_table_content();
                    reload_form();
                    show_notification(messages.delete_success, 'success');
                } else {
                    show_notification(messages.delete_failed, 'error');
                }
            }, function() {
                show_notification(messages.delete_failed, 'error');
            });
        }); 
    }
    
});