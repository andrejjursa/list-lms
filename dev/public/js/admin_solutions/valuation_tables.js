jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');
    
    var reload_valuation_table = function() {
        var url = global_base_url + 'index.php/admin_solutions/get_valuation_table';
        var target = '#table_content_id';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            //update_content_width();
            //sort_table('#table_content_id table.valuation_table', '#filter_form_id');
            /*var oTable = $('#valutation_table').dataTable({
                'paging': false,
                'scrollX': '100%',
                'scrollY': '400px',
                'scrollCollapse': true,
                'order': [[ 1, 'asc' ]],
                'language': lang.dataTables,
                dom: '<"top"ifClp<"clear">>rt',
                colVis: {
                    exclude: [ 0, 1 ]
                }
            });
            
            new $.fn.dataTable.FixedColumns( oTable, {
                leftColumns: 2,
                rightColumns: 1
            });*/
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
        /*var valuation_table_outer_wrap = $('#table_content_id div.valuation_table_outer_wrap');
        var valuation_table_wrap = $('#table_content_id div.valuation_table_wrap');
        valuation_table_wrap.hide();
        valuation_table_wrap.css('width', valuation_table_outer_wrap.width());
        valuation_table_wrap.show();*/
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
    
    var sort_table_by_col = function(col_position, direction, sort_type) {
        if (typeof sort_type === 'undefined') { sort_type = 'numeric'; }
        
        var sorting = [];
        
        var temporary_location = $('<div></div>').css('display', 'none');
        temporary_location.appendTo('body');
        
        $('#valutation_table tbody tr').each(function(){
            var index = $(this).attr('data-row');
            var value = $(this).find('td[data-position="' + col_position + '"]').attr('data-order');
            sorting.push({idx: index, val: value});
            $(this).appendTo(temporary_location);
        });
        
        console.log(sorting);
        
        sorting.sort(function(a, b) {
            if (sort_type === 'numeric') {
                if (parseFloat(a.val) > parseFloat(b.val)) {
                    return direction === 'asc' ? 1 : -1;
                }
                if (parseFloat(a.val) < parseFloat(b.val)) {
                    return direction === 'asc' ? -1 : 1;
                }
            } else {
                if (a.val > b.val) {
                    return direction === 'asc' ? 1 : -1;
                }
                if (a.val < b.val) {
                    return direction === 'asc' ? -1 : 1;
                }
            }
            return 0;
        });
        
        console.log(sorting);
        
        var table = $('#valutation_table tbody');
        
        for(var idx in sorting) {
            var row = sorting[idx];
            var row_obj = temporary_location.find('tr[data-row="' + row.idx + '"]');
            row_obj.appendTo(table);
        }
        
        temporary_location.remove();
    };
    
    var last_order_by = '';
    var last_order_by_direction = 'asc';
    
    $(document).on('click', '#valutation_table thead tr th[data-position]', function() {
        var column_to_sort = $(this).attr('data-position');
        var sort_type = 'numeric';
        var direction = 'desc';
        if (column_to_sort <= 2) { sort_type = 'alpha'; direction = 'asc'; }
        if (last_order_by === column_to_sort) {
            direction = last_order_by_direction === 'asc' ? 'desc' : 'asc';
        }
        sort_table_by_col(column_to_sort, direction, sort_type);
        last_order_by = column_to_sort;
        last_order_by_direction = direction;
    });
    
});