jQuery(document).ready(function($) {
    $('#csv_form_id button.select_all').click(function(event) {
        event.preventDefault();
        $('#csv_table_content_id table.csv_table tbody tr').each(function() {
            $(this).addClass('selected');
            $(this).find('input[type=checkbox]').prop('checked', true);
        });
    });
    
    $('#csv_form_id button.select_none').click(function(event) {
        event.preventDefault();
        $('#csv_table_content_id table.csv_table tbody tr').each(function() {
            $(this).removeClass('selected');
            $(this).find('input[type=checkbox]').prop('checked', false);
        });
    });
    
    $('#csv_table_content_id table.csv_table tbody tr td input[type=checkbox]').change(function(event) {
        var tr = $(this).parents('tr');
        if ($(this).is(':checked')) {
            tr.addClass('selected');
        } else {
            tr.removeClass('selected');
        }
    });
    
    $('#csv_table_content_id table.csv_table tbody tr td').click(function() {
        if (!$(this).is(':first-child')) {
            var tr = $(this).parents('tr');
            var checkbox = tr.find('input[type=checkbox]');
            checkbox.prop('checked', !checkbox.is(':checked'));
            checkbox.change();
        }
    });
    
    var update_content_width = function() {
        var table_wrapper = $('#csv_table_content_id > div:first-child');
        var parent_fieldset = table_wrapper.parents('fieldset');
        table_wrapper.hide();
        table_wrapper.css('width', parent_fieldset.width());
        table_wrapper.show();
    };
    
    $(window).on('resize', update_content_width);
});
