jQuery(document).ready(function($) {
    
    var reload_all_tasks = function() {
        var url = global_base_url + 'index.php/admin_tasks/get_all_tasks';
        var onSuccess = function() {
            $('#table_content_id #pagination_row_id').appendTo($('#table_pagination_footer_id'));
        };
        api_ajax_load(url, '#table_content_id', 'post', {}, onSuccess);
    }
    
    reload_all_tasks();
    
    /*var reload_all_groups = function() {
        var data = $('#filter_form_id').serializeArray();
        var url = global_base_url + 'index.php/admin_groups/get_table_content';
        api_ajax_load(url, '#table_of_groups_container_id', 'post', data);
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
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_all_groups();
    });
    
    $(document).on('click', '#table_of_groups_container_id a.rooms_editor', function(event) {
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
                reload_all_groups();
                return true;
            }
        });
    });
    $(document).on('click', '#table_of_groups_container_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function() {
                reload_all_groups();
            });
        }
    });*/
    
});