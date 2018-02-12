jQuery(document).ready(function($) {
    make_overlay_editors();

    make_custom_switch('div.uploader_switch', show_uploader_text);

    var last_course_id = '';

    var upload_folder_name = $('input[type=hidden][name="course_content[folder_name]"]').val();

    var requesting_directory = false;

    $('#new_content_form_id, #edit_form').activeForm();

    $('#new_content_form_id div.field.course_content_group_field, #edit_form div.field.course_content_group_field').setActiveFormDisplayCondition(function () {
        var course_id = $('#course_content_course_id_id').val();
        if (course_id !== last_course_id) {
            var selected_id = $('form input[name=post_selected_course_content_group_id]').val() !== undefined ? $('form input[name=post_selected_course_content_group_id]').val() : '';
            var target = '#course_content_course_content_group_id_id';
            update_select_values_by($(target), course_id, data.all_course_content_groups, selected_id);
            last_course_id = course_id;
        }
        if (course_id == '') {
            return false;
        }
        return true;
    });

    $('#new_content_form_id, #edit_form').activeForm().applyConditions();

    $('#course_content_published_from_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });

    $('#course_content_published_to_id').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });

    var delete_file = function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var language = $(this).attr('data-language');
        var file = $(this).attr('data-file');
        var message = delete_file_question;
        var confirmed = confirm(message.replace("{0}", file));
        if (!confirmed) { return; }
        api_ajax_update(url, 'post', [], function(response) {
            if (typeof response.status !== 'undefined' && typeof response.message !== 'undefined') {
                if (response.status) {
                    show_notification(response.message, 'success');
                } else {
                    show_notification(response.message, 'error');
                }
            }
            reload_file_list(upload_folder_name, language);
        }, function() {
            reload_file_list(upload_folder_name, language);
        });
    };

    var load_hidden_status = function(tr) {
        var config = JSON.parse($('#files_visibility').val().split('\\"').join('"'));
        var file = $(tr).attr('data-file');
        var language = $(tr).attr('data-language');

        console.log(file + ' ' + language);

        if (typeof config[language] !== 'undefined' && typeof config[language][file] !== 'undefined' && config[language][file] === true) {
            $(tr).addClass('hidden');
            $(tr).find('a.switch_visibility').addClass('hidden');
            $(tr).find('a.switch_visibility i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
        }
    };

    var set_hidden_status = function(language, file, status) {
        var config = JSON.parse($('#files_visibility').val().split('\\"').join('"'));

        if (typeof config[language] === 'undefined') {
            config[language] = {};
        }
        config[language][file] = status;

        $('#files_visibility').val(JSON.stringify(config).split('"').join('\\"'));
    };

    var copy_link_function = function(event) {
        event.preventDefault();

        var link = $(this).attr('data-link');

        if (copyTextToClipboard(link)) {
            show_notification(coppied_to_clipboard, 'info');
        } else {
            prompt('Link:', link);
        }
    };

    var reload_file_list = function (upload_folder, language) {
        var url = global_base_url + 'index.php/admin_course_content/file_list/' + language + '/' + upload_folder;
        api_ajax_load(url, 'table.course_content_files_table tbody.file_list_' + language, 'post', [], function () {
            $('table.course_content_files_table tbody.file_list_' + language + ' a.delete_file').click(delete_file);
            $('table.course_content_files_table tbody.file_list_' + language + ' a.switch_visibility').click(function (event) {
                event.preventDefault();

                var file = $(this).attr('data-file');
                var language = $(this).attr('data-language');

                $(this).toggleClass('hidden');
                $(this).parents('tr').toggleClass('hidden');
                $(this).find('i').toggleClass('fa-minus-circle').toggleClass('fa-plus-circle');

                set_hidden_status(language, file, $(this).hasClass('hidden'));
            });

            $('table.course_content_files_table tbody.file_list_' + language + ' tr').each(function () {
                load_hidden_status($(this));
                $(this).find('a.button.copy_link').click(copy_link_function);
            });
        });
    };

    var request_upload_directory = function() {
        if (upload_folder_name === '' && !requesting_directory) {
            requesting_directory = true;

            var url = global_base_url + 'index.php/admin_course_content/request_temporary_directory';
            api_ajax_update(url, 'post', [], function(result) {
                if (typeof result.directory !== 'undefined' && result.directory !== '') {
                    upload_folder_name = result.directory;
                    $('input[type=hidden][name="course_content[folder_name]"]').val(upload_folder_name);
                    requesting_directory = false;
                }
            }, function () {
                requesting_directory = false;
            }, 'json', true, 5000);
        }

        return upload_folder_name;
    };

    var make_plupload = function(language) {
        $('#plupload_content_files_' + language + '_id').plupload({
            runtimes: 'html5,flash,silverlight',
            url: global_base_url + 'index.php/admin_course_content/plupload_file/' + upload_folder_name + '/' + language,
            max_file_size: '1000mb',
            max_file_count: 20,
            chunk_size: '1mb',
            multiple_queues: true,
            flash_swf_url: global_base_url + 'public/swf/plupload.flash.swf',
            silverlight_xap_url : global_base_url + 'public/xap/plupload.silverlight.xap',
            init: {
                UploadComplete: function () {
                    reload_file_list(request_upload_directory(), language);
                },
                FileUploaded: function () {
                    reload_file_list(request_upload_directory(), language);
                },
                Error: function () {
                    reload_file_list(request_upload_directory(), language);
                },
                BeforeUpload: function() {
                    var folder = request_upload_directory();
                    if (folder === '') {
                        return false;
                    }
                    this.settings.url = global_base_url + 'index.php/admin_course_content/plupload_file/' + folder + '/' + language;
                }
            }
        });
        reload_file_list(upload_folder_name, language);
    };

    for (var language in languages) {
        make_plupload(language);
    }

    make_plupload('default');
});