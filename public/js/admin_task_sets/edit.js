jQuery(document).ready(function($) {
    
    make_overlay_editors();
    make_custom_switch('form div.task', task_text_title, 'task_wrapper');
    make_custom_switch('form div.internal_comment', internal_comment_title, 'task_wrapper');
    
    $('form').formErrorWarning();
    
    $('textarea.tinymce').tinymce({
        plugins: [
            "advlist autolink link image lists charmap preview hr anchor pagebreak autoresize",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons textcolor paste textcolor"
        ],

        toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | fullscreen | ltr rtl | visualchars visualblocks nonbreaking pagebreak",

        menubar: false,
        toolbar_items_size: 'small',
        entity_encoding: 'raw',
        document_base_url: global_base_url,
        convert_urls: false,
        relative_urls: false,
        resize: false,
        autoresize_max_height: 400,
        autoresize_min_height: 150
    });
    
    $('#course_groups_change_deadline_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
    
    api_make_tabs('tabs');
    
    var compile_sorting = function() {
        var sorting = '';
        $('#tasks_sortable > li.task_sorting_item').each(function() {
            var element_id = $(this).attr('id');
            if (element_id.substr(0, 5) === 'task_') {
                var task_id = element_id.substr(5);
                sorting += (sorting === '' ? '' : ',') + task_id;
            }
        });
        $('input[name=tasks_sorting]').val(sorting);
    };
    
    var presort_tasks = function() {
        var sorting_str = $('input[name=tasks_sorting]').val();
        if (sorting_str.length > 0) {
            var sorting_array = sorting_str.split(',');
            var elements_array = [];
            for (var i in sorting_array) {
                elements_array.push($('#task_' + sorting_array[i]));
            }
            var container = $('<ul id="tasks_presortable" style="display: none;"></ul>');
            container.insertAfter('#tasks_sortable');
            var li_elements = $('#tasks_sortable > li.task_sorting_item');
            li_elements.appendTo(container);
            for (var i in elements_array) {
                elements_array[i].appendTo('#tasks_sortable');
            }
            container.find('> li.task_sorting_item').each(function() {
                $(this).appendTo('#tasks_sortable');
            });
            container.remove();
        }
        compile_sorting();
    };
    
    presort_tasks();
    
    $('#tasks_sortable').sortable({
        placeholder: 'ui-state-highlight placeholder',
        axis: 'y',
        update: function() {
            compile_sorting();
        }
    });
    
    $(document).on('change', '#tasks_sortable input.delete_checkbox', function() {
        if ($(this).is(':checked')) {
            if (!confirm(delete_question)) {
                $(this).removeAttr('checked');
            }
        }
    });
    
    var refresh_additional_permissions = function() {
        var url = global_base_url + 'index.php/admin_task_set_permissions/index/' + task_set_id;
        var target = '#additional_permissions_id';
        api_ajax_load(url, target);
    };
    
    refresh_additional_permissions();
    
    $(document).on('click', 'a.button.new_permission, a.button.edit_task_set_permission', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.fancybox(url, {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            helpers: {
                overlay: {
                    css: {
                        background: 'rgba(255,255,255,0)'
                    }
                }
            },
            beforeClose: function() {
                refresh_additional_permissions();
                return true;
            }
        });
    });
    
    $(document).on('click', 'a.button.delete_task_set_permission', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        if (confirm(delete_permission_question)) {
            api_ajax_update(url, 'POST', {}, function(output) {
                if (typeof output.result !== 'undefined' && typeof output.message !== 'undefined') {
                    if (output.result) {
                        show_notification(output.message, 'success');
                    } else {
                        show_notification(output.message, 'error');
                    }
                }
                refresh_additional_permissions();
            });
        }
    });
    
    var recompute_task_selections = function(task_id) {
        var count = $('div.project_selection_list.task_id_' + task_id).find('div.project_selection_student').length;
        $('span.project_selection_count.task_id_' + task_id).html(count);
    };
    
    var return_student_item = function(student_id, task_id) {
        var item = $('div.project_selection_student.student_id_' + student_id);
        var list = $('div.project_selection_list.task_id_' + task_id);
        item.appendTo(list);
    };
    
    var switch_student_item_task = function(student_id, task_id, original_task_id) {
        var item = $('div.project_selection_student.student_id_' + student_id);
        item.removeClass('original_task_id:' + original_task_id);
        item.addClass('original_task_id:' + task_id);
    };
        
    $('div.project_selection_list').sortable({
        connectWith: 'div.project_selection_list',
        cursor: 'move',
        axis: 'y',
        delay: 300,
        revert: true,
        cancel: 'div.project_selection_student.operations_disabled',
        receive: function(event, ui) {
            var original_task_id = api_read_class_config(ui.item, 'original_task_id');
            var task_id = api_read_class_config(ui.item.parent(), 'task_id');
            var student_id = api_read_class_config(ui.item, 'student_id');
            if (confirm(select_project_question)) {
                var url = global_base_url + 'index.php/admin_task_sets/select_project/' + task_set_id + '/' + task_id + '/' + student_id;
                $('div.project_selection_list').sortable('disable');
                api_ajax_update(url, 'post', {}, function(output) {
                    if (typeof output.status !== 'undefined' && typeof output.message !== 'undefined') {
                        if (output.status) {
                            show_notification(output.message, 'success');
                            switch_student_item_task(student_id, task_id, original_task_id);
                        } else {
                            show_notification(output.message, 'error');
                            return_student_item(student_id, original_task_id);
                        }
                    } else {
                        return_student_item(student_id, original_task_id);
                    }
                    recompute_task_selections(original_task_id);
                    recompute_task_selections(task_id);
                    $('div.project_selection_list').sortable('enable');
                }, function() {
                    return_student_item(student_id, original_task_id);
                    recompute_task_selections(original_task_id);
                    recompute_task_selections(task_id);
                    $('div.project_selection_list').sortable('enable');
                });
            } else {
                $('div.project_selection_list').sortable('disable');
                return_student_item(student_id, original_task_id);
                recompute_task_selections(original_task_id);
                recompute_task_selections(task_id);
                $('div.project_selection_list').sortable('enable');
            }
        }
    });
    
});