jQuery(document).ready(function($) {
    
    make_filter_form('#filter_form_id');

    var default_sorting = {
        col_position: 2,
        direction: 'asc',
        sort_type: 'alpha'
    };

    var last_sorting = default_sorting;
    
    var reload_valuation_table = function(callback) {
        var url = global_base_url + 'index.php/admin_solutions/get_valuation_table';
        var target = '#table_content_id';
        var data = $('#filter_form_id').serializeArray();
        var onSuccess = function() {
            $('ul.show_hide_menu').menu({
                my: 'left top',
                at: 'left bottom',
                of: this
            });
            link_cells_to_solution_editor();
            if (typeof callback === 'function') {
                callback();
            }
        };
        api_ajax_load(url, target, 'post', data, onSuccess);
    };
    
    reload_valuation_table();

    var link_cells_to_solution_editor = function() {
        $('#table_content_id td.type_task_set[data-solution-id][data-task-set-id]').each(function () {
            $(this).click(function() {
                var solution_id = $(this).attr('data-solution-id');
                var task_set_id = $(this).attr('data-task-set-id');
                var url = global_base_url + 'index.php/admin_solutions/valuation/' + task_set_id + '/' + solution_id;
                open_valuation_dialog(url);
            });
        });
    };

    var open_valuation_dialog = function(url) {
        $.fancybox(url, {
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
            },
            beforeClose: function() {
                reload_valuation_table(restore_table_sorting);
                return true;
            }
        });
    };
    
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

    var restore_table_sorting = function () {
        sort_table_by_col(last_sorting.col_position, last_sorting.direction, last_sorting.sort_type);
        last_order_by = last_sorting.col_position;
        last_order_by_direction = last_sorting.direction;
        $('#valutation_table thead tr th').removeClass('sort-asc');
        $('#valutation_table thead tr th').removeClass('sort-desc');
        $('thead tr th[data-position=' + last_order_by + ']').addClass('sort-' + last_order_by_direction);
    };
    
    var sort_table_by_col = function(col_position, direction, sort_type) {
        if (typeof sort_type === 'undefined') { sort_type = 'numeric'; }

        last_sorting.col_position = col_position;
        last_sorting.direction = direction;
        last_sorting.sort_type = sort_type;

        var sorting = [];
        
        var temporary_location = $('<div></div>').css('display', 'none');
        temporary_location.appendTo('body');
        
        $('#valutation_table tbody tr').each(function(){
            var index = $(this).attr('data-row');
            var value = $(this).find('td[data-position="' + col_position + '"]').attr('data-order');
            sorting.push({idx: index, val: value});
            $(this).appendTo(temporary_location);
        });
                
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
        
        var table = $('#valutation_table tbody');
        
        var col_index = 1;
        for(var idx in sorting) {
            var row = sorting[idx];
            var row_obj = temporary_location.find('tr[data-row="' + row.idx + '"]');
            row_obj.appendTo(table);
            row_obj.find('td.index').html(col_index + '.');
            col_index++;
        }
        
        temporary_location.remove();
    };
    
    var last_order_by = '2';
    var last_order_by_direction = 'asc';
    
    $(document).on('click', ' thead tr th[data-position]', function() {
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
        $('#valutation_table thead tr th').removeClass('sort-asc');
        $('#valutation_table thead tr th').removeClass('sort-desc');
        $(this).addClass('sort-' + last_order_by_direction);
    });
    
    $(document).on('click', 'a.show_hide_button', function(event) {
        event.preventDefault();
        $('ul.show_hide_menu').toggle();
    });
    
    var hide_column = function(column_position) {
        $('#valutation_table tbody tr td[data-position=' + column_position + ']').hide();
        $('#valutation_table thead tr th[data-position=' + column_position + ']').hide();
        var checked = 0;
        var stop = false;
        $('#valutation_table thead tr:first-child th').each(function() {
            if ($(this).hasClass('index')) { return; }
            if (stop) { return; }
            var size = $(this).attr('data-size');
            if (typeof size === 'undefined') { size = 1; } else { size = parseInt(size); }
            if (checked < column_position && column_position <= (checked + size) && $(this).hasClass('show_hide_multicolumn')) {
                var colspan = $(this).prop('colspan');
                if (typeof colspan !== 'undefined') {
                    colspan = parseInt(colspan);
                    colspan--;
                    if (colspan > 0) {
                        $(this).prop('colspan', colspan);
                    }
                    if (colspan === 0) {
                        $(this).hide();
                        $(this).addClass('multicolumn_hidden');
                    }
                }
                stop = true;
                return;
            }
            checked += size;
        });
    };
    
    var show_column = function(column_position) {
        $('#valutation_table tbody tr td[data-position=' + column_position + ']').show();
        $('#valutation_table thead tr th[data-position=' + column_position + ']').show();
        var checked = 0;
        var stop = false;
        $('#valutation_table thead tr:first-child th').each(function() {
            if ($(this).hasClass('index')) { return; }
            if (stop) { return; }
            var size = $(this).attr('data-size');
            if (typeof size === 'undefined') { size = 1; } else { size = parseInt(size); }
            if (checked < column_position && column_position <= (checked + size) && $(this).hasClass('show_hide_multicolumn')) {
                var colspan = $(this).prop('colspan');
                if (typeof colspan !== 'undefined') {
                    colspan = parseInt(colspan);
                    if ($(this).hasClass('multicolumn_hidden')) {
                        $(this).show();
                        $(this).removeClass('multicolumn_hidden');
                    } else {
                        colspan++;
                        $(this).prop('colspan', colspan);
                    }
                }
                stop = true;
                return;
            }
            checked += size;
        });
    };
    
    $(document).on('click', 'ul.show_hide_menu a[data-column]', function(event) {
        event.stopPropagation();
        var col_pos = $(this).attr('data-column');
        var span = $(this).find('span.ui-icon');
        if (span.length === 0) { return; }
        if (span.hasClass('ui-icon-circle-check')) {
            hide_column(col_pos);
            span.removeClass('ui-icon-circle-check');
            span.addClass('ui-icon-circle-close');
            $(this).css('text-decoration', 'line-through');
        } else {
            show_column(col_pos);
            span.removeClass('ui-icon-circle-close');
            span.addClass('ui-icon-circle-check');
            $(this).css('text-decoration', '');
        }
        
    });
    
});