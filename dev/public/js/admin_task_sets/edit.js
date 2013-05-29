jQuery(document).ready(function($) {
    
    api_make_tabs('tabs');
    
    var compile_sorting = function() {
        var sorting = '';
        $('#tasks_sortable li').each(function() {
            var element_id = $(this).attr('id');
            if (element_id.substr(0, 5) == 'task_') {
                var task_id = element_id.substr(5);
                sorting += (sorting == '' ? '' : ',') + task_id;
            }
        });
        $('input[name=tasks_sorting]').val(sorting);
    }
    
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
            var li_elements = $('#tasks_sortable li');
            li_elements.appendTo(container);
            for (var i in elements_array) {
                elements_array[i].appendTo('#tasks_sortable');
            }
            container.find('li').each(function() {
                $(this).appendTo('#tasks_sortable');
            });
            container.remove();
        }
        compile_sorting();
    }
    
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
    
});