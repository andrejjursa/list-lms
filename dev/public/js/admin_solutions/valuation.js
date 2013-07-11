jQuery(document).ready(function($) {
    
    api_make_tabs('tabs');
    make_filter_form('#filter_form_id');
    
    var last_zip_file = '';
    var last_index = '';
    
    $('#filter_form_id').activeForm({
        speed: 0
    });
    
    $('#filter_form_id div.download_file_buttons').setActiveFormDisplayCondition(function() {
        return this.findElement('select[name="zip[file]"]').val() != '';
    });
    
    $('#filter_form_id div.select_file').setActiveFormDisplayCondition(function() {
        if (this.isDisplayed('div.download_file_buttons')) {
            var zip_file = this.findElement('select[name="zip[file]"]').val();
            if (zip_file != last_zip_file) {
                var url = global_base_url + 'index.php/admin_solutions/get_student_file_content/' + task_set_id + '/' + solution_id + '/' + zip_file;
                var target = '#zip_index_id';
                api_ajax_load(url, target, 'post', {}, function() {
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
        return last_index != '' && this.findElement('select[name="zip[file]"]').val() != '';
    });
    
    $('#filter_form_id').activeForm().applyConditions();
    
    $(document).on('click', '#filter_form_id input[name="download_file_button"]', function(event) {
        event.preventDefault();
        var url = global_base_url + 'index.php/tasks/download_solution/' + task_set_id + '/' + last_zip_file;
        window.open(url, '_blank');
    }); 
    
    $(document).on('click', '#filter_form_id input[name="read_file_button"]', function(event) {
        event.preventDefault();
        var url = global_base_url + 'index.php/admin_solutions/show_file_content/' + task_set_id + '/' + solution_id + '/' + last_zip_file + '/' + last_index;
        var target = '#file_content_id';
        api_ajax_load(url, target, 'post', {}, function() {}, function() {
            url += '/yes';
            api_ajax_load(url, target);
        });
    });
    
});