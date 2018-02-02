jQuery(document).ready(function($) {
    
    make_switchable_form('#new_content_form_id');

    var submit_form = function(event) {
        event.preventDefault();
        var url = $('#new_content_form_id').attr('action');
        var data = $('#new_content_form_id').serializeArray();

        for (var i = tinymce.editors.length - 1 ; i > -1 ; i--) {
            var ed_id = tinymce.editors[i].id;
            tinyMCE.execCommand("mceRemoveEditor", true, ed_id);
        }

        var success = function() {
            if ($('#new_content_form_id .flash_message.message_success').length > 0) {
                reload_content();
            }
            $('#new_content_form_id').formErrorWarning();
            $.getScript(global_base_url + 'public/js/admin_course_content/form.js');
            $.getScript(global_base_url + 'public/js/admin_tasks/form.js');
        };
        api_ajax_load(url, '#new_content_form_id', 'post', data, success);
    };

    var reload_content = function() {
        var url = global_base_url + '/admin_course_content/get_all_content';
        api_ajax_load(url, '#table_content');
    };

    $('#new_content_form_id').submit(function(event) {
        submit_form(event);
    });

    reload_content();

});