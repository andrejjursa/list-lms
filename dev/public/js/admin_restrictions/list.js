jQuery(document).ready(function($) {
    
    make_switchable_form('#new_restriction_form_id');
    
    var reload_all_restrictions = function() {
        api_ajax_load(global_base_url + 'index.php/admin_restrictions/restrictions_list', '#table_container_id');
    };
    
    $('#new_restriction_form_id').submit(function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#new_restriction_form_id .flash_message.message_success').length > 0) {
                reload_all_restrictions();
            }
            $.getScript(global_base_url + 'public/js/admin_restrictions/form.js');
            $('#new_restriction_form_id').formErrorWarning();
        };
        api_ajax_load($(this).attr('action'), '#new_restriction_form_id', 'post', data, success);
    });
    
    var delete_restriction = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, function(output) {
            if (typeof output.status !== 'undefined' && typeof output.message !== 'undefined') {
                if (output.status) {
                    reload_all_restrictions();
                    show_notification(output.message, 'success');
                } else {
                    show_notification(output.message, 'error');
                }
            }
        });
    };
    
    var clear_old = function(event) {
        event.preventDefault();
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, function(output) {
            if (typeof output.status !== 'undefined' && typeof output.message !== 'undefined') {
                if (output.status) {
                    reload_all_restrictions();
                    show_notification(output.message, 'success');
                } else {
                    show_notification(output.message, 'error');
                }
            }
        });
    }
    
    $(document).on('click', '#table_container_id a.button.delete', delete_restriction);
    $(document).on('click', 'a.button.special.clear_old', clear_old);
    
    reload_all_restrictions();
    
});