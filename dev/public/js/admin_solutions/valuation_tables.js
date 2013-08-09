jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');
    
    var reload_valuation_table = function() {
        var url = global_base_url + 'index.php/admin_solutions/get_valuation_table';
        var target = '#table_content_id';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            update_content_width();
        };
        api_ajax_load(url, target, 'post', data, onSuccess);
    };
    
    reload_valuation_table();
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        reload_valuation_table();
    });
    
    var filter_last_course_id = '';
    
    $('#filter_form_id').activeForm({
        speed: 0,
        hiddenClass: 'hidden'
    }).setDisplayCondition('div.group_field', function() {
        var filter_course_id = this.findElement('select[name="filter[course]"]').val();
        if (filter_course_id > 0) {
            if (filter_course_id !== filter_last_course_id) {
                var selected_id = this.findElement('input[name=filter_selected_group_id]').val() !== undefined ? this.findElement('input[name=filter_selected_group_id]').val() : '0';
                var url = global_base_url + 'index.php/admin_solutions/get_groups_from_course/' + filter_course_id + '/' + selected_id;
                var target = $('#filter_group_id');
                api_ajax_load(url, target, 'post', {}, function() {
                    target.find('option[value=NULL]').remove();
                    update_filter_group();
                });
                filter_last_course_id = filter_course_id;
            }
            return true;
        }
        return false;
    }).setDisplayCondition('div.group_field_else', function() {
        return !this.isDisplayed('div.group_field');
    });
    $('#filter_form_id').activeForm().applyConditions();
    
    var update_filter_group = function() {
        $('#filter_form_id input[name="filter[group]"]').val($('#filter_group_id').val());
    };
    
    $(document).on('change', '#filter_group_id', update_filter_group);
    
    var update_content_width = function() {
        $('#table_content_id div.valuation_table_outer_wrap').hide();
        var width = $('#table_content_id div.valuation_table_outer_wrap').parents('fieldset').width();
        if (!test_window_maximized() && window.innerWidth > 815) {
            width -= 20;
        }
        console.log(window.innerWidth);
        $('#table_content_id div.valuation_table_outer_wrap').css('width', width - 10).show();
        $('#table_content_id div.valuation_table_wrap').hide();
        width = $('#table_content_id div.valuation_table_outer_wrap').width();
        $('#table_content_id div.valuation_table_wrap').css('width', width).show();
    };
    
    $(window).resize(function() {
        update_content_width();
    });
    
    $(document).on('click', '#table_content_id table.valuation_table tbody tr td', function() {
        $(this).parent().toggleClass('marked');
    });
    
    $(document).on('mousedown', '#table_content_id table.valuation_table tbody tr td', function() {
        $(this).parent().addClass('clicked');
    });
    
    $(document).on('mouseup', '#table_content_id table.valuation_table tbody tr td', function() {
        $(this).parent().removeClass('clicked');
    });
    
});