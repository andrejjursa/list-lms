jQuery(document).ready(function($) {
    
    var reload_comparisons = function() {
        var url = $('#filter_form_id').attr('action');
        api_ajax_load_json(url, 'post', {}, function(data) {
            fill_table(data);
        });
    };

    var fill_table = function(data) {
        var tbody = $('#comparisons_table_id tbody');
        tbody.html('');
        
        for (var i in data.data) {
            var row = data.data[i];

            var tr = $('<tr>');
            tbody.append(tr);

            var tdId = $('<td>');
            tdId.text(row.id);
            tr.append(tdId);

            var tdTeacher = $('<td>');
            if (row.teacher === null) {
                tdTeacher.text('-');
            } else {
                tdTeacher.text(row.teacher.full_name);
            }
            tr.append(tdTeacher);

            var tdStatus = $('<td>');
            var statusHTML = '';
            if (row.status === 'queued') {
                statusHTML = '<i class="fa fa-arrow-right" aria-hidden="true" style="color: blue;"></i> ';
            } else if (row.status === 'processing') {
                statusHTML = '<i class="fa fa-cogs" aria-hidden="true" style="color: orange;"></i> ';
            } else if (row.status === 'finished') {
                statusHTML = '<i class="fa fa-check" aria-hidden="true" style="color: green;"></i> ';
            } else if (row.status === 'failed') {
                statusHTML = '<i class="fa fa-exclamation" aria-hidden="true" style="color: red;"></i> ';
            }
            statusHTML += row.status;
            tdStatus.html(statusHTML);
            tr.append(tdStatus);

            var tdStarted = $('<td>');
            if (row.processing_start === null) {
                tdStarted.html('<i class="fa fa-cog fa-spin fa-fw" aria-hidden="true" style="color: orange;"></i> pending');
            } else {
                tdStarted.html('<i class="fa fa-check" aria-hidden="true" style="color: green;"></i> ' + row.processing_start);
            }
            tr.append(tdStarted);

            var tdFinished = $('<td>');
            if (row.processing_finish === null) {
                tdFinished.html('<i class="fa fa-cog fa-spin fa-fw" aria-hidden="true" style="color: orange;"></i> pending');
            } else {
                var symbol = '<i class="fa fa-check" aria-hidden="true" style="color: green;"></i> ';
                if (row.status !== 'finished') {
                    symbol = '<i class="fa fa-exclamation" aria-hidden="true" style="color: red;"></i> ';
                }
                tdFinished.html(symbol + row.processing_finish);
            }
            tr.append(tdFinished);

            var tdControlsDetails = $('<td>');
            tdControlsDetails.addClass('controlls');

            var buttonDetails = $('<a>');
            buttonDetails.addClass('button');
            buttonDetails.html('<i class="fa fa-folder-open" aria-hidden="true"></i>');
            tdControlsDetails.append(buttonDetails);

            tr.append(tdControlsDetails);

            var tdControlsResults = $('<td>');
            tdControlsResults.addClass('controlls');
            if (row.status === 'finished') {
                var buttonResults = $('<a>');
                buttonResults.addClass('button').addClass('special').addClass('results');
                buttonResults.attr('href', row.result_link);
                buttonResults.attr('target', '_blank');
                buttonResults.html('<i class="fa fa-external-link" aria-hidden="true"></i>');
                tdControlsResults.append(buttonResults);
            }
            tr.append(tdControlsResults);
            var tdControlsRequeue = $('<td>');
            tdControlsRequeue.addClass('controlls');
            if (row.status === 'failed') {
                var buttonRequeue = $('<a>');
                buttonRequeue.addClass('button').addClass('delete');
                buttonRequeue.html('<i class="fa fa-refresh" aria-hidden="true"></i>');
                tdControlsRequeue.append(buttonRequeue);
            }
            tr.append(tdControlsRequeue);
        }

        fill_pagination(data.pagination);
    };

    var fill_pagination = function(pagination) {
        var tfoot = $('#comparisons_table_id tfoot');

        var strongs = $(tfoot).find('strong');

        $(strongs[0]).text(typeof pagination.current_page !== 'undefined' ? pagination.current_page : 0);
        $(strongs[1]).text(typeof pagination.total_pages !== 'undefined' ? pagination.total_pages : 0);
        $(strongs[2]).text(typeof pagination.current_row !== 'undefined' && typeof pagination.items_on_page !== 'undefined' ? pagination.current_row + Math.min(1, pagination.items_on_page) : 0);
        $(strongs[3]).text(typeof pagination.current_row !== 'undefined' && typeof pagination.items_on_page !== 'undefined' ? pagination.current_row + pagination.items_on_page : 0);
        $(strongs[4]).text(typeof pagination.total_rows !== 'undefined' ? pagination.total_rows : 0);

        var pages_select = $(tfoot).find('select[name=paging_page]').get(0);
        $(pages_select).html('');

        for (var page = 1; page <= (typeof pagination.total_pages !== 'undefined' ? pagination.total_pages : 0); ++page) {
            var selected = page === (typeof pagination.current_page !== 'undefined' ? pagination.current_page : 0);
            var option = $('<option>');
            option.attr('value', page);
            option.text(page);
            if (selected) {
                option.attr('selected', 'selected');
            }
            $(pages_select).append(option);
        }

        var per_page_select = $(tfoot).find('select[name=paging_rows_per_page]').get(0);
        var options = $(per_page_select).find('option');
        var toSelect = typeof pagination.page_size !== 'undefined' ? pagination.page_size : 0;
        var isSelected = false;
        for (var i = 0; i < options.length; ++i) {
            var option = $(options[i]);
            if (option.data('isTemporary') === true) {
                if (option.attr('value') != toSelect) {
                    option.remove();
                    continue;
                }
            }
            if (option.attr('value') == toSelect) {
                option.attr('selected', 'selected');
                isSelected = true;
            } else {
                option.removeAttr('selected');
            }
        }
        if (!isSelected) {
            var newOption = $('<option>');
            newOption.text(toSelect);
            newOption.attr('value', toSelect);
            newOption.data('isTemporary', true);
            newOption.attr('selected', 'selected');
            $(per_page_select).append(newOption);
        }
    };

    reload_comparisons();
    var timer = setInterval(reload_comparisons, 10000);

});