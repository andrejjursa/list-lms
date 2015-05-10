jQuery(document).ready(function($) {
    
    prettyPrint();
    
    api_make_tabs('tabs');
    
    if (enable_countdown) {
        var current_date = new Date();
        if (countdown_to > current_date) {
            $('#remaining_time').countdown({
                until: countdown_to,
                layout: messages.countdown_time,
                onExpiry: function() {
                    show_notification(messages.countdown_expired, 'info');
                    $('#upload_solution_id').fadeOut('slow');
                    api_ajax_update(global_base_url + 'index.php/tasks/reset_task_cache/' + task_id);
                }
            });
        } else {
            $('#upload_solution_id').hide();
        }
    }
    
    $(document).on('click', 'a.button.subscribe, a.button.unsubscribe', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var target = $(this).parents('div.comments_wrap').parent();
        api_ajax_load(url, target);
    });
    
    $(document).on('submit', '#comment_form_id', function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var target = $(this).parents('div.comments_wrap').parent();
        var success = function() {
            if (target.find('.flash_message.message_error').length > 0) {
                $.scrollTo(target.find('.flash_message.message_error'), 0, { margin: true, offset: { top: -30 } });
            }
        };
        api_ajax_load(url, target, 'post', data, success);
    });
    
    $(document).on('click', 'a.button.reply_at', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var target = $(this).parents('div.comments_wrap').parent();
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
                var url = global_base_url + 'index.php/tasks/show_comments/' + task_id;
                api_ajax_load(url, target);
                return true;
            }
        });
    });
    
    var autoscroll = true;
    var url_anchor = api_read_url_anchor();
    if (url_anchor.substring(0, 8) === 'comments') {
        var comment_id = url_anchor.substring(9);
        if (comment_id !== '') {
            $(document).ajaxSuccess(function() {
                if (autoscroll) {
                    autoscroll = false;
                    setTimeout(function() {
                        $.scrollTo($('div.comments_wrap li.comment_id_' + comment_id), 0, { margin: true, offset: { top: -30 } });
                    }, 100);
                }
            });
        }
        $('#tabs li.comments_tab a').trigger('click');
    }
    
    $('#select_test_type_id').change(function(event) {
        event.preventDefault();
        
        var test_type = $(this).val();
        
        $('.solution_tests_table .test_header').each(function() {
            if (test_type === '' || $(this).hasClass('test_type_' + test_type)) {
                $(this).find('input[type=checkbox]').prop('checked', true).prop('disabled', false);
            } else {
                $(this).find('input[type=checkbox]').prop('checked', false).prop('disabled', true);
            }
        });
    });
    
    var reload_tests_queue = function() {
        var target_div = $('#tests_queue_container_id');
        if (target_div.length === 1) {
            var url = global_base_url + 'index.php/fetests/get_student_test_queue/' + task_id + '/' + student_id;
            api_ajax_load(url, '#tests_queue_container_id');
        }
    };
    
    var can_reload_tests_queue = true;
    
    var reload_interval = null;
    
    var start_tests_queue_reloading = function() {
        var target_div = $('#tests_queue_container_id');
        if (target_div.length === 1 && can_reload_tests_queue) {
            reload_interval = setInterval(function() {
                if (can_reload_tests_queue) {
                    reload_tests_queue();
                }
            }, 15000);
        }
    };
    
    reload_tests_queue();
    start_tests_queue_reloading();
    
    var reload_lock = false;
    
    $(document).on('click', 'a.reload_test_queue', function(event) {
        event.preventDefault();
        if (!reload_lock) {
            reload_tests_queue();
            reload_lock = true;
            setTimeout(function() { reload_lock = false; }, 2000);
        }
    });
    
    $(document).on('click', 'a.open_test_queue_results', function(event) {
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
            }
        });
    });
    
    var can_enqueue_tests = true;
    
    $('#tests_form_id').submit(function(event) {
        event.preventDefault();
        
        if (!can_enqueue_tests) { return; }
        
        var data = $(this).serializeArray();
        var url = global_base_url + 'index.php/fetests/enqueue_test';
        
        can_enqueue_tests = false;
        
        api_ajax_update(url, 'post', data, function(result) {
            if (typeof result.message !== 'undefined' && typeof result.status !== 'undefined') {
                if (result.status) {
                    show_notification(result.message, 'success');
                    reload_tests_queue();
                } else {
                    show_notification(result.message, 'error');
                }
            }
            setTimeout(function() {
                can_enqueue_tests = true;
            }, 2000);
        }, function() {
            setTimeout(function() {
                can_enqueue_tests = true;
            }, 2000);
        });
    });
    
    var resize_test_result_content = function(output_to_element_id) {
        $(window).on('resize', function() {
            var div = $('#' + output_to_element_id);
            var fieldset = div.parents('fieldset.testfieldset');
            div.hide();
            div.css('width', fieldset.width());
            div.show();
        });
    };
    
});