jQuery(document).ready(function($) {
    make_filter_form('#filter_form_id');

    var load_course_content_sorting = function () {
        var url = global_base_url + 'index.php/admin_course_content/get_all_course_content_sorting';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            delete_empty_inner_sorters();
            create_sorting_lists();
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    };

    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        load_course_content_sorting();
    });

    load_course_content_sorting();

    var create_sorting_lists = function () {
        var self = $('#table_content_id div.top_level_sorting');
        $('#table_content_id div.top_level_sorting').sortable({
            'placeholder': 'content_or_group_placeholder',
            'update': function () {
                var order = [];
                self.find('> div[data-id]').each(function () {
                    order.push({ 'id': $(this).attr('data-id'), 'type': $(this).attr('data-type') });
                });
                update_sorting(order, null, $('#current_course').attr('data-id'));
            }
        });

        $('#table_content_id div.inner_content').each(function () {
            var self = $(this);
            $(this).sortable({
                'placeholder': 'content_or_group_placeholder',
                'update': function () {
                    var order = [];
                    var group = self.attr('data-parent');
                    self.find('> div[data-id]').each(function () {
                        order.push({ 'id': $(this).attr('data-id'), 'type': $(this).attr('data-type') });
                    });
                    update_sorting(order, group, $('#current_course').attr('data-id'));
                }
            });
        });
    };

    var delete_empty_inner_sorters = function() {
        $('#table_content_id div.inner_content').each(function() {
            if ($(this).text().trim() == '') {
                $(this).remove();
            }
        });
    };

    var lock_all_sorters = function() {
        $('#table_content_id div.top_level_sorting, #table_content_id div.inner_content').addClass('disabled').sortable('disable');
    };

    var update_sorting = function (order, group_id, course_id) {
        var url = global_base_url + 'index.php/admin_course_content/update_sorting';
        var data = {
            'group_id': group_id,
            'course_id': course_id,
            'order': order
        };
        lock_all_sorters();
        api_ajax_update(url, 'post', data, function (result) {
            if (typeof result.status !== 'undefined' && typeof result.message !== 'undefined') {
                if (result.status == true) {
                    show_notification(result.message, 'success');
                } else {
                    show_notification(result.message, 'error');
                }
            }
            load_course_content_sorting();
        }, function () {
            load_course_content_sorting();
        });
    };
});