jQuery(document).ready(function($) {
    
    prettyPrint();
    
    api_make_tabs('tabs');
    
    if (enable_countdown) {
        var current_date = new Date();
        if (countdown_to > current_date) {
            $('#remaining_time').countdown({
                until: countdown_to,
                layout: messages.countdown_time,
                onExpiry: function() {
                    show_notification(messages.countdown_expired, 'info');
                    $('#upload_solution_id').fadeOut('slow');
                    api_ajax_update(global_base_url + 'index.php/projects/reset_task_cache/' + project_id + '/' + task_id);
                }
            });
        } else {
            $('#upload_solution_id').hide();
        }
    }
    
});