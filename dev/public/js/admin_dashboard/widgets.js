jQuery(document).ready(function($) {
    
    var reload_single_widget = function(id) {
        var url = global_base_url + 'index.php/admin_widget/showWidget/' + id;
        var target = '#widget_container_' + id;
        api_ajax_load(url, target);
    };
    
    var reload_widgets = function() {
        if (widget_list !== null) {
            for (var i in widget_list) {
                reload_single_widget(widget_list[i]);
            }
        }
    };
    
    reload_widgets();
    
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
    
});