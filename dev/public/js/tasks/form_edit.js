jQuery(document).ready(function($) { 
    api_make_tabs('tabs');
    var task_id = $('input[name=task_id]').val();
    
    var reload_files = function() {
        var url = global_base_url + 'index.php/admin_tasks/get_task_files/' + task_id;
        api_ajax_load(url, '#files_table_content_id');
    }
    
    reload_files();
    
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
        flash_swf_url: global_base_url + 'public/swf/plupload.flash.swf'
    });
    
    $('.plupload_container').attr('title', '');
    
    var uploader = $('#plupload_queue_id').plupload('getUploader');
    
    uploader.bind('UploadComplete', function(up, files) {
        reload_files();
    });
});