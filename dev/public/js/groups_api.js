jQuery(document).ready(function($) {
    
    var reload_all_groups = function() {
        var url = global_base_url + 'index.php/admin_groups/get_table_content';
        api_ajax_load(url, '#table_of_groups_container_id');
    }
    
    reload_all_groups();
    
    $('#groups_form_id').submit(function (event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var target = '#groups_form_id';
        var data = $(this).serializeArray();
        var success = function(html) {
            if ($('#groups_form_id .flash_message.message_success').length > 0) {
                reload_all_groups();
            }
            $.getScript(global_base_url + 'public/js/groups/form.js');
        };
        api_ajax_load(url, target, 'post', data, success);
    });
    
});