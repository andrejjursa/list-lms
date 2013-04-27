jQuery(document).ready(function($) { 
    api_make_tabs('tabs');
    var task_id = $('input[name=task_id]').val();
    $('#file_upload_id').uploadify({
        'swf' : global_base_url + 'public/swf/uploadify.swf',
        'uploader' : global_base_url + 'index.php/admin_tasks/add_files/' + task_id,
        'queueID' : 'uploadify_queue_id',
        'width' : 120,
        'height' : 28,
        'fileObjName' : 'file_upload',
        'buttonText' : select_files_text,
        'onQueueComplete' : function() {
            reload_files();
        },
        'onUploadSuccess' : function(file, data) {
            if (data != 'ok') {
                show_notification(data, 'error');    
            }
        }
    });
    
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
});