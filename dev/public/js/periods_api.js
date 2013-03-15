jQuery(document).ready(function($) {
    
    var reload_all_periods = function() {
        api_ajax_load(global_base_url + 'index.php/admin_periods/ajax_periods_list', '#periods_container_id');
    };
    
    $('#new_period_form_id').submit(function(event) {
        event.preventDefault();
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#new_period_form_id .flash_message.message_success').length > 0) {
                reload_all_periods();
            }
        };
        api_ajax_load($(this).attr('action'), '#new_period_form_id', 'post', data, success);
    });
    
    var up_down_period = function(event) {
        event.preventDefault();
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, reload_all_periods);
    };
    
    var delete_period = function(event) {
        event.preventDefault();
        if (!confirm(messages.delete_question)) { return; }
        
        var url = $(this).attr('href');
        
        api_ajax_update(url, 'post', {}, reload_all_periods);
    };
    
    $(document).on('click', '#periods_container_id a.button_up', up_down_period);
    $(document).on('click', '#periods_container_id a.button_down', up_down_period);
    $(document).on('click', '#periods_container_id a.button_delete', delete_period);
    
    reload_all_periods();
    
});