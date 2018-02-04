jQuery(document).ready(function($) {
    
    make_switchable_form('#new_content_form_id');
    make_filter_form('#filter_form_id');

    var disabled = $('#new_content_form_id').attr('data-disabled');

    if (disabled === 'disabled') {
        $('#new_content_form_id input, #new_content_form_id textarea').attr('disabled', 'disabled');
        $('#new_content_form_id input[type=submit]').removeAttr('disabled').addClass('disabled');
    }

    var submit_form = function(event) {
        event.preventDefault();
        if (disabled === 'disabled') {
            show_notification(message_write_disabled, 'error');
            return false;
        }
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
        var data = $('#filter_form_id').serializeArray();
        api_ajax_load(url, '#table_content', 'post', data, function () {
            sort_table('table.course_content_table', '#filter_form_id');
        });
    };

    $('#new_content_form_id').submit(submit_form);

    var toggle_content = function(event) {
        event.preventDefault();
        var myID = $(this).attr('data-content-id');
        $('table tbody tr.content_overview[data-content-id="' + myID + '"]').toggleClass('show');
        $(this).parents('tr').toggleClass('show');
        $(this).find('i').toggleClass('fa-chevron-down').toggleClass('fa-chevron-up');
    };

    $(document).on('click', 'a.toggle_content', toggle_content);

    reload_content();

    $(document).on('change', '#table_pagination_footer_id select[name=paging_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[page]"]').val(value);
        reload_content();
    });

    $(document).on('change', '#table_pagination_footer_id select[name=paging_rows_per_page]', function() {
        var value = $(this).val();
        $('#filter_form_id input[name="filter[rows_per_page]"]').val(value);
        reload_content();
    });

    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_content();
    });
});