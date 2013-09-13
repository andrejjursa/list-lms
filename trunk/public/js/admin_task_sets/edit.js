jQuery(document).ready(function($) {
    
    make_overlay_editors();
    
    $('textarea.tinymce').tinymce({
        script_url : global_base_url + 'public/js/tinymce/tiny_mce.js',
        theme: 'advanced',
        plugins : 'autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',
        theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
        theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
        theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
        theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
        theme_advanced_toolbar_location : 'top',
        theme_advanced_toolbar_align : 'left',
        theme_advanced_statusbar_location : 'bottom',
        entity_encoding: 'raw',
        document_base_url: global_base_url,
        relative_urls: false
    });
    
    $('#course_groups_change_deadline_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
    
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