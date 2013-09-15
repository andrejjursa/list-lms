jQuery(document).ready(function($) {
    
    var reload_all_tests = function() {
        api_ajax_load(all_tests_list_url, '#tests_content_id');
    };
    
    reload_all_tests();
    
    $(document).on('click', 'a.button.new_test_button, a.button.configure_test', function(event) {
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
    
});