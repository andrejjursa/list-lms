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
            }, 30000);
        }
    };
    
    reload_tests_queue();
    start_tests_queue_reloading();
    
    $('#tests_form_id').submit(function(event) {
        event.preventDefault();
        
        var data = $(this).serializeArray();
        var url = global_base_url + 'index.php/fetests/enqueue_test';
        
        api_ajax_update(url, 'post', data, function(result) {
            if (typeof result.message !== 'undefined' && typeof result.status !== 'undefined') {
                if (result.status) {
                    show_notification(result.message, 'success');
                    reload_tests_queue();
                    start_tests_queue_reloading();
                } else {
                    show_notification(result.message, 'error');
                }
            }
        });
    });
    
    /*var tests_token = '';
    
    var tests_to_run = 0;
    
    var test_file_version_id = 0;
    
    var test_results_field_div = null;
    
    var selected_test_type = '';
    
    $('#tests_form_id').submit(function(event) {
        event.preventDefault();
        
        if (test_evaluation_enabled) {
            get_tests_token_and_execute();
        } else {
            batch_tests_execution();
        }
    });
    
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
    
    var batch_tests_execution = function() {
        var tests_execution_area = $('#tests_execution_area_id');
        selected_test_type = $('#select_test_type_id').val();
        if (tests_execution_area.length === 1) {
            tests_execution_area.html('');
            if (tests_object !== undefined) {
                var test_form = $('#tests_form_id');
                var test_form_data = test_form.serializeObject();
                if (selected_test_type !== '') {
                    if (typeof test_form_data.test.version !== 'undefined' && typeof test_form_data.test.id !== 'undefined') {
                        if (test_evaluation_enabled) {
                            var test_results_field = $('<fieldset></fieldset>');
                            var test_results_field_legend = $('<legend>' + messages.test_result_area + '<legend>');
                            test_results_field_div = $('<div>' + messages.test_result_tests_in_progress + '</div>');
                            test_results_field_legend.appendTo(test_results_field);
                            test_results_field.appendTo(tests_execution_area).addClass('basefieldset').addClass('test_result_area');
                            test_results_field_div.appendTo(test_results_field);
                        }
                        for (var task_id in tests_object) {
                            var task_header = $('<h4 class="test_task_name">' + tests_object[task_id].name + '</h4>');
                            task_header.appendTo(tests_execution_area);
                            for (var test_id in tests_object[task_id]) {
                                if (typeof tests_object[task_id][test_id].name !== 'undefined' && inArray(test_id, test_form_data.test.id)) {
                                    var test_fieldset = $('<fieldset></fieldset>');
                                    var test_fieldset_legend = $('<legend>' + tests_object[task_id][test_id].name + '</legend>');
                                    var test_div = $('<div></div>');
                                    test_fieldset_legend.appendTo(test_fieldset);
                                    test_fieldset.appendTo(tests_execution_area).addClass('basefieldset').addClass('testfieldset');
                                    test_div.appendTo(test_fieldset).attr('id', 'test_execution_' + test_id + '_id').addClass('test_execution_div');
                                    test_div.html('<p>' + messages.test_being_executed + '</p>');
                                }
                            }
                        }
                        test_file_version_id = test_form_data.test.version;
                        tests_to_run = 0;
                        for (var i in test_form_data.test.id) {
                            var test_id = test_form_data.test.id[i];
                            run_test(test_id, test_form_data.test.version, 'test_execution_' + test_id + '_id');
                        }
                        test_finalization_check();
                    } else {
                        show_notification(messages.test_no_selection, 'error');
                    }
                } else {
                    show_notification(messages.test_type_not_selected, 'error');
                }
            }
        }
    };
    
    var run_test = function(test_id, version_id, output_to_element_id) {
        tests_to_run++;
        var url = global_base_url + 'index.php/fetests/run_test_for_task/' + test_id + '/' + task_id + '/' + student_id + '/' + version_id + '/' + tests_token;
        api_ajax_update(url, 'post', {}, function(data) {
            if (data.code !== undefined && data.text !== undefined) {
                console.log(data.token);
                var div = $('#' + output_to_element_id);
                var fieldset = div.parents('fieldset.testfieldset');
                div.hide();
                div.css('width', fieldset.width());
                div.show();
                div.html(data.text);
                if (data.code > 0) {
                    div.css('color', 'red');
                }
                resize_test_result_content(output_to_element_id);
            }
            tests_to_run--;
        }, function() {
            tests_to_run--;
        });
    };
    
    var get_tests_token_and_execute = function() {
        selected_test_type = $('#select_test_type_id').val();
        var test_form = $('#tests_form_id');
        var test_form_data = test_form.serializeObject();
        if (selected_test_type !== '') {
            if (typeof test_form_data.test.version !== 'undefined' && typeof test_form_data.test.id !== 'undefined') {
                var url = global_base_url + 'index.php/fetests/request_token/';
                api_ajax_update(url, 'post', {}, function(result) {
                    tests_token = result;
                    batch_tests_execution();
                }, function() {
                    show_notification(messages.test_result_token_failed, 'error');
                });
            } else {
                show_notification(messages.test_no_selection, 'error');
            }
        } else {
            show_notification(messages.test_type_not_selected, 'error');
        }
    };
    
    var test_finalization_check = function() {
        if (test_evaluation_enabled) {
            if (tests_to_run === 0) {
                var url = global_base_url + 'index.php/fetests/evaluate_test_result/' + task_id + '/' + student_id + '/' + test_file_version_id + '/' + selected_test_type + '/' + tests_token;
                console.log(url);
                api_ajax_update(url, 'post', {}, function(data) {
                    if (data.result === false) {
                        test_results_field_div.html(data.message);
                        show_notification(data.message, 'error');
                    } else {
                        var msg = messages.test_result_evaluation;
                        msg = msg.replace('###OLD###', data.points_before);
                        msg = msg.replace('###NEW###', data.points_new);
                        test_results_field_div.html(msg);
                        show_notification(msg, 'success');
                    }
                    if (data.evaluation !== undefined) {
                        test_results_field_div.html(test_results_field_div.html() + data.evaluation);
                    }
                }, function() {
                    test_results_field_div.html(messages.test_result_not_obtained);
                    show_notification(messages.test_result_not_obtained, 'error');
                });
            } else {
                setTimeout(test_finalization_check, 100);
            }
        }
    };*/
    
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