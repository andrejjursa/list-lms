jQuery(document).ready(function($) {
    
    var reload_single_widget = function(id) {
        var url = global_base_url + 'index.php/admin_widget/showWidget/' + id;
        var target = '#widget_container_' + id;
        api_ajax_load(url, target);
    };
    
    var reload_widgets = function() {
        if (widget_list !== null) {
            for (var column in widget_list) {
                for (var index in widget_list[column]) {
                    reload_single_widget(widget_list[column][index]);
                }
            }
        }
    };
    
    reload_widgets();
    
    var save_widget_sorting = function() {
        var new_sorting = {};
        for (var column = 1; column <= columns; column++) {
            new_sorting['column[' + column + ']'] = [];
            $('div.widget_column_' + column + ' div.widget_container').each(function() {
                var id = api_read_class_config($(this), 'widget_id');
                new_sorting['column[' + column + ']'].push(id);
            });
            $('div.widget_column_' + column + ' div.widget_column_sizer').appendTo('div.widget_column_' + column);
        }
        var url = global_base_url + 'index.php/admin_widget/sort';
        api_ajax_update(url, 'post', new_sorting);
    };
    
    $('div.widget_column').sortable({
        connectWith: 'div.widget_column',
        handle: 'div.widget_header',
        placeholder: 'widget_sorting_placeholder',
        items: 'div.widget_container',
        delay: 300,
        opacity: 0.5,
        revert: true,
        forcePlaceholderSize: true,
        tolerance: 'pointer',
        start: function(event, ui) {
            $('div.widget_column').addClass('widget_sorting_add_padding');
        },
        stop: function(event, ui) {
            $('div.widget_column').removeClass('widget_sorting_add_padding');
            save_widget_sorting();
        }
    });
    
    var edit_widget_configuration = function(url, id) {
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
                if (id !== null) {
                    reload_single_widget(id);
                }
                return true;
            }
        });
    };
    
    $(document).on('click', 'a.widget_config_link', function(event) {
        event.preventDefault();
        var id = api_read_class_config($(this), 'widget_id');
        var url = $(this).attr('href');
        edit_widget_configuration(url, id);
    });
    
    $(document).on('click', 'a.widget_delete_link', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_widget)) {
            var id = api_read_class_config($(this), 'widget_id');
            var url = $(this).attr('href');
            api_ajax_update(url, 'post', {}, function(output) {
                if (typeof output.status !== 'undefined' && typeof output.message !== 'undefined') {
                    if (output.status) {
                        show_notification(output.message, 'success');
                        $('#widget_container_' + id).animate({
                            opacity: 0.0,
                            height: '0px'
                        }, 'normal', function() {
                            $(this).remove();
                        });
                    } else {
                        show_notification(output.message, 'error');
                    }
                }
            });
        }
    });
    
    $('#new_widget_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        api_ajax_update(url, 'post', data, function(output) {
            if (typeof output.status !== 'undefined' && typeof output.message !== 'undefined' && typeof output.new_id !== 'undefined' && typeof output.column !== 'undefined') {
                if (output.status) {
                    show_notification(output.message, 'success');
                    var container = $('<div></div>');
                    container.attr('id', 'widget_container_' + output.new_id);
                    container.addClass('widget_container');
                    container.addClass('widget_id:' + output.new_id);
                    container.html(messages.widget_loading);
                    container.appendTo('div.widget_column_' + output.column);
                    reload_single_widget(output.new_id);
                    var config_url = global_base_url + 'index.php/admin_widget/configure/' + output.new_id;
                    edit_widget_configuration(config_url, output.new_id);
                } else {
                    show_notification(output.message, 'error');
                }
            }
        });
    });
    
});