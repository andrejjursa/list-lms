jQuery(document).ready(function($) {
    
    api_make_tabs('tabs');
    
    $('#valuation_form_id').formErrorWarning();
    
    var last_zip_file = '';
    var last_index = '';
    var first_load = true;
    
    var prettiPrintContent = function() {
        var codepreview = $('pre.codepreview.prettyprint');
        var codepreview_copy_html = codepreview.html();
        var codepreview_copy = $('<pre></pre>');
        codepreview_copy.insertAfter(codepreview);
        codepreview_copy.addClass('codepreviewNohighlight');
        codepreview_copy.css({'display': 'none'});
        codepreview_copy.html(codepreview_copy_html);
        prettyPrint();
    };
    
    var switchCodePreview = function() {
        var codepreview = $('pre.codepreview');
        var codepreviewNohighlight = $('pre.codepreviewNohighlight');
        if (codepreview.css('display') === 'none') {
            codepreview.css('display', '');
            codepreviewNohighlight.css('display', 'none');
        } else {
            codepreview.css('display', 'none');
            codepreviewNohighlight.css('display', '');
        }
    };
    
    $('#filter_form_id').activeForm({
        speed: 0
    });
    
    $('#filter_form_id div.download_file_buttons').setActiveFormDisplayCondition(function() {
        return this.findElement('select[name="zip[file]"]').val() !== '';
    });
    
    $('#filter_form_id div.select_file').setActiveFormDisplayCondition(function() {
        if (this.isDisplayed('div.download_file_buttons')) {
            var zip_file = this.findElement('select[name="zip[file]"]').val();
            if (zip_file !== last_zip_file) {
                var url = global_base_url + 'index.php/admin_solutions/get_student_file_content/' + task_set_id + '/' + solution_id + '/' + zip_file;
                var target = '#zip_index_id';
                api_ajax_load(url, target, 'post', {}, function() {
                    if (first_load) {
                        var first_file = $(target + ' option:nth-child(2)');
                        if (first_file.length === 1) {
                            first_file.prop('selected', true);
                            setTimeout(function() {
                                load_file_content();
                            }, 50);
                        }
                        first_load = false;
                    }
                    $('#filter_form_id').activeForm().applyConditions();
                });
                last_zip_file = zip_file;
            }
            return true;
        }
        return false;
    });
    
    $('#filter_form_id div.read_file_buttons').setActiveFormDisplayCondition(function() {
        last_index = this.findElement('select[name="zip[index]"]').val();
        return last_index !== '' && this.findElement('select[name="zip[file]"]').val() !== '';
    });
    
    $('#filter_form_id').activeForm().applyConditions();
    
    $(document).on('click', '#filter_form_id input[name="download_file_button"]', function(event) {
        event.preventDefault();
        var url = global_base_url + 'index.php/tasks/download_solution/' + task_set_id + '/' + last_zip_file;
        window.open(url, '_blank');
    }); 
    
    var load_file_content = function() {
        var url = global_base_url + 'index.php/admin_solutions/show_file_content/' + task_set_id + '/' + solution_id + '/' + last_zip_file + '/' + last_index;
        var target = '#file_content_id';
        api_ajax_load(url, target, 'post', {}, function() {
            prettiPrintContent();
            $('div.codepreview_container').resizable({
                autoHide: true,
                handles: 's',
                minHeight: 350
            });
        });
    };
    
    $(document).on('change', '#filter_form_id #zip_index_id', function(event) {
        if ($(this).val() !== '') {
            event.preventDefault();
            load_file_content();
        }
    });
    
    $(document).on('click', '#filter_form_id input[name="read_file_button"]', function(event) {
        event.preventDefault();
        load_file_content();
    });
    
    $(document).on('click', '#filter_form_id input[name="switch_code_highlight"]', function(event) {
        event.preventDefault();
        switchCodePreview();
    });
    
    $('#tests_form_id').submit(function(event) {
        event.preventDefault();
        
        var tests_execution_area = $('#tests_execution_area_id');
        if (tests_execution_area.length === 1) {
            tests_execution_area.html('');
            if (tests_object !== undefined) {
                var test_form = $('#tests_form_id');
                var test_form_data = test_form.serializeObject();
                if (typeof test_form_data.test.version !== 'undefined' && test_form_data.test.version > 0 && typeof test_form_data.test.id !== 'undefined') {
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
                    for (var i in test_form_data.test.id) {
                        var test_id = test_form_data.test.id[i];
                        run_test(test_id, test_form_data.test.version, 'test_execution_' + test_id + '_id');
                    }
                } else {
                    show_notification(messages.test_no_selection, 'error');
                }
            }
        }
    });
    
    var run_test = function(test_id, version_id, output_to_element_id) {
        var url = global_base_url + 'index.php/admin_tests/run_test_for_task/' + test_id + '/' + task_set_id + '/' + student_id + '/' + version_id;
        api_ajax_update(url, 'post', {}, function(data) {
            if (data.code !== undefined && data.text !== undefined) {
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
        });
    };
    
    var resize_test_result_content = function(output_to_element_id) {
        $(window).on('resize', function() {
            var div = $('#' + output_to_element_id);
            var fieldset = div.parents('fieldset.testfieldset');
            div.hide();
            div.css('width', fieldset.width());
            div.show();
        });
    };
    
    $('input[type=checkbox].switch_checkboxes').change(function() {
        var class_to_check = $(this).attr('name');
        class_to_check = class_to_check.substr(18);
        class_to_check = class_to_check.substr(0, class_to_check.length - 1);
        if (typeof class_to_check !== 'string' || class_to_check === '') { return; }
        if ($(this).is(':checked')) {
            $('input[type=checkbox].' + class_to_check).prop('checked', true);
        } else {
            $('input[type=checkbox].' + class_to_check).prop('checked', false);
        }
    });
    
    $('input[type=checkbox].test_id').change(function() {
        var classes = $(this).attr('class').split(' ');
        var current_class = '';
        for (var i in classes) {
            if (classes[i].substr(0, 10) === 'test_type-') {
                current_class = classes[i];
                break;
            }
        }
        if (typeof current_class !== 'string' || current_class === '') { return; }
        if ($(this).is(':checked')) {
            var all = $('input[type=checkbox].test_id.' + current_class);
            var checked = $('input[type=checkbox].test_id.' + current_class + ':checked');
            if (all.length === checked.length) {
                $('input[type=checkbox][name="switch_checkboxes[' + current_class + ']"].switch_checkboxes').prop('checked', true);
            }
        } else {
            $('input[type=checkbox][name="switch_checkboxes[' + current_class + ']"].switch_checkboxes').prop('checked', false);
        }
    });
    
    $('a.button.go_to_next_solution').click(function(event) {
        event.preventDefault();
        api_ajax_update(urls.get_next_solution, 'post', {}, function(output) {
            if (output.have_next !== undefined && output.next_id !== undefined && output.error_message !== undefined) {
                if (output.have_next) {
                    var url = urls.valuation.replace('###SOLUTION_ID###', output.next_id);
                    window.location = url;
                } else {
                    show_notification(output.error_message, 'error');
                }
            }
        });
    });
    
});