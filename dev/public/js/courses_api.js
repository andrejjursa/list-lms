jQuery(document).ready(function($) {
    
    var reload_table_content = function() {
        api_ajax_load(global_base_url + 'index.php/admin_courses/get_table_content', '#table_content');
    }
    
    reload_table_content();
    
});