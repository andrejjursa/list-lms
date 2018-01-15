jQuery(document).ready(function($) {
    
    make_switchable_form('#new_content_form_id');

    var submit_form = function(event) {
        event.preventDefault();
        var url = $('#new_content_form_id').attr('action');
        var data = $('#new_content_form_id').serializeArray();
        var success = function() {
            if ($('#new_content_form_id .flash_message.message_success').length > 0) {
                
            }
            $('#new_content_form_id').formErrorWarning();
        };
        api_ajax_load(url, '#new_content_form_id', 'post', data, success);
    };

    $('#new_content_form_id').submit(function(event) {
        submit_form(event);
    });

});