jQuery(document).ready(function($) { 
    make_overlay_editors();
    api_make_tabs('tabs');
    var task_id = $('input[name=task_id]').val();
    
    var reload_files = function() {
        var url = global_base_url + 'index.php/admin_tasks/get_task_files/' + task_id;
        api_ajax_load(url, '#files_table_content_id');
    }
    
    reload_files();
    
    $('form').formErrorWarning();
    
    $(document).on('click', '#files_table_content_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function(output) {
                if (output == true) {
                    reload_files();
                    show_notification(messages.after_delete, 'success');    
                }
            });
        }
    });
    
    $('#plupload_queue_id').plupload({
        runtimes: 'html5,flash,silverlight',
        url: global_base_url + 'index.php/admin_tasks/plupload_file/' + task_id,
        max_file_size: '1000mb',
        max_file_count: 20,
        chunk_size: '1mb',
        multiple_queues: true,
        flash_swf_url: global_base_url + 'public/swf/plupload.flash.swf',
        silverlight_xap_url : global_base_url + 'public/xap/plupload.silverlight.xap'
    });
    
    $('.plupload_container').attr('title', '');
    
    var uploader = $('#plupload_queue_id').plupload('getUploader');
    
    uploader.bind('UploadComplete', function(up, files) {
        reload_files();
    });
    
    var reload_hidden_files = function() {
        var url = global_base_url + 'index.php/admin_tasks/get_hidden_task_files/' + task_id;
        api_ajax_load(url, '#hidden_files_table_content_id');
    }
    
    reload_hidden_files();
    
    $(document).on('click', '#hidden_files_table_content_id a.delete', function(event) {
        event.preventDefault();
        if (confirm(messages.delete_question)) {
            api_ajax_update($(this).attr('href'), 'get', {}, function(output) {
                if (output == true) {
                    reload_hidden_files();
                    show_notification(messages.after_delete, 'success');    
                }
            });
        }
    });
    
    $('#plupload_queue_hidden_id').plupload({
        runtimes: 'html5,flash,silverlight',
        url: global_base_url + 'index.php/admin_tasks/plupload_hidden_file/' + task_id,
        max_file_size: '1000mb',
        max_file_count: 20,
        chunk_size: '1mb',
        multiple_queues: true,
        flash_swf_url: global_base_url + 'public/swf/plupload.flash.swf',
        silverlight_xap_url : global_base_url + 'public/xap/plupload.silverlight.xap'
    });
    
    $('.plupload_container').attr('title', '');
    
    var uploader_hidden = $('#plupload_queue_hidden_id').plupload('getUploader');
    
    uploader_hidden.bind('UploadComplete', function(up, files) {
        reload_hidden_files();
    });
    
    $(document).on('click', 'a.button.add_to_task_set', function(event) {
        event.preventDefault();
        $.fancybox($(this).attr('href'), {
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
    
});