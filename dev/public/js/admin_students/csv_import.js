jQuery(document).ready(function($) {
    
    var current_row = 0;
    var progressbar = $('#progress_bar_wrap_id div.progress_bar').progressbar({
        value: 0,
        max: csv_rows
    });
    
    var add_log_item = function(html) {
        var log = $('#import_log_id');
        log.html(html + log.html());
    };
    
    var import_next_row = function() {
        current_row++;
        if (current_row <= csv_rows) {
            var data = csv_data[current_row-1];
            data.options = {};
            data.options.password_type = password_type;
            data.options.send_mail = send_mail;
            data.options.assign_to_course = assign_to_course;
            console.log(data);
            api_ajax_update(global_base_url + 'index.php/admin_students/import_single_line', 'post', data, function(output) {
                add_log_item(output);
                progressbar.progressbar('value', current_row);
                import_next_row();
            }, function() {
                progressbar.progressbar('value', current_row);    
                import_next_row();
            });
        } else {
            api_ajax_update(global_base_url + 'index.php/admin_students/delete_csv_file/' + url_config, 'post');
        }
    };
    
    import_next_row();
});