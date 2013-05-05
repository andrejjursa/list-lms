jQuery(document).ready(function($) {
    
    var get_structured_tree = function() {
        var url = global_base_url + 'index.php/admin_categories/tree_structure';
        api_ajax_load(url, '#category_tree_id');
    };
    
    get_structured_tree();
    
    $('#new_category_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_category_form_id .flash_message.message_success').length > 0) {
                get_structured_tree();
            }
            $.getScript(global_base_url + 'public/js/admin_categories/form.js');
        }
        api_ajax_load(url, '#new_category_form_id', 'post', data, success);
    });
    
    var delete_category = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, function(output) {
            if (output == true) {
                get_structured_tree();
                show_notification(messages.after_delete, 'success');
            }
        });
    }
    
    $(document).on('click', '#category_tree_id a.delete', delete_category);
    
});