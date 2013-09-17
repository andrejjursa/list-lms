jQuery(document).ready(function($) {
    
    var reload_all_tests = function() {
        api_ajax_load(all_tests_list_url, '#tests_content_id');
    };
    
    reload_all_tests();
    
    $(document).on('click', 'a.button.new_test_button, a.button.configure_test, a.button.execute_test', function(event) {
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
                reload_all_tests();
                return true;
            }
        });
    });
    
    $(document).on('click', 'a.button.delete_test', function(event) {
        event.preventDefault();
        if (confirm(test_delete_question)) {
            var url = $(this).attr('href');
            api_ajax_update(url, 'post', {}, function(output) {
                if (output.result !== undefined && output.message !== undefined) {
                    show_notification(output.message, output.result ? 'success' : 'error');
                }
                reload_all_tests();
            });
        }
    });
    
});