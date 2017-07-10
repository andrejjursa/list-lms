jQuery(document).ready(function($) {
    make_filter_form('#filter_form_id');

    var load_sorting_task_sets = function () {
        var url = global_base_url + 'index.php/admin_task_sets/get_all_task_sets_sorting';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            create_sorting_lists();
            hide_empty_task_set_types();
        };
        api_ajax_load(url, '#table_content_id', 'post', data, onSuccess);
    };

    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        load_sorting_task_sets();
    });

    load_sorting_task_sets();

    var create_sorting_lists = function (for_task_set_type_id) {
        var request_query = 'ul[data-task-set-type-id][data-course-id]';
        if (for_task_set_type_id !== undefined) {
            request_query = 'ul[data-task-set-type-id=' + for_task_set_type_id + '][data-course-id]';
        }
        $(request_query).each(function() {
            var task_set_type_id = $(this).attr('data-task-set-type-id');
            var course_id = $(this).attr('data-course-id');
            var self = $(this);
            $(this).sortable({
                placeholder: 'task-set-sorting-placeholder',
                update: function () {
                    var order = [];
                    self.find('li[data-id]').each(function () {
                        order.push($(this).attr('data-id'));
                    });
                    update_sorting(order, task_set_type_id, course_id);
                }
            });
        });
    };

    var hide_empty_task_set_types = function() {
        $('fieldset.sorting_list').each(function() {
            if ($(this).find('div').text().trim() == '') {
                $(this).hide();
            }
        });
    };

    var update_sorting = function (order, task_set_type_id, course_id) {
        var url = global_base_url + 'index.php/admin_task_sets/update_sorting';
        var data = {
            'task_set_type_id': task_set_type_id,
            'course_id': course_id,
            'order': order
        };
        api_ajax_update(url, 'post', data, function (result) {
            if (result.status == true) {
                show_notification(result.message, 'success');
                $('div[data-task-set-type-id=' + task_set_type_id + ']').hide();
                $('div[data-task-set-type-id=' + task_set_type_id + ']').html(result.content);
                create_sorting_lists(task_set_type_id);
                $('div[data-task-set-type-id=' + task_set_type_id + ']').show();
            } else {
                show_notification(result.message, 'error');
            }
        });
    };
});