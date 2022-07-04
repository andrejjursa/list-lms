jQuery(document).ready(function($) {

    var timer;

    var comparisonTable = $('#comparisons_table_id');

    var create_url_query = function() {
        var data = {};

        data.page = $($('select[name=paging_page]').get(0)).val();
        data.pageSize = $($('select[name=paging_rows_per_page]').get(0)).val();

        var query = $.param(data);
        return query === '' ? '' : '?' + query;
    };

    var reload_comparisons = function() {
        var url = $('#filter_form_id').attr('action');
        api_ajax_load_json(url + create_url_query(), 'post', {}, function(data) {
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

            var tdName = $('<td>');
            tr.append(tdName);

            if (row.comparison_name !== null) {
                var tdNameSpan = $('<span>');
                var short = row.comparison_name.split(' ').slice(0, 5).join(' ');
                if (short !== row.comparison_name) {
                    short += ' ...';
                }
                tdNameSpan.text(short);
                tdNameSpan.attr('title', row.comparison_name);
                tdName.append(tdNameSpan);
            }

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
            } else if (row.status === 'restart') {
                statusHTML = '<i class="fa fa-repeat" aria-hidden="true" style="color: fuchsia;"></i> ';
            }
            statusHTML += comparisonTable.attr('data-lang_comparison_status_' + row.status);
            if (row.status === 'restart') {
                statusHTML += ' (' + (row.restarts === null ? '-' : row.restarts) + ')';
            }
            tdStatus.html(statusHTML);
            tr.append(tdStatus);

            var tdStarted = $('<td>');
            if (row.processing_start === null) {
                if (row.status === 'queued' || row.status === 'restart') {
                    tdStarted.html(
                        '<i class="fa fa-ellipsis-h" aria-hidden="true" style="color: blue;"></i> '
                        + comparisonTable.attr('data-lang_comparison_action_waiting')
                    );
                } else {
                    tdStarted.html(
                        '<i class="fa fa-cog fa-spin fa-fw" aria-hidden="true" style="color: orange;"></i> '
                        + comparisonTable.attr('data-lang_comparison_action_pending')
                    );
                }
            } else {
                tdStarted.html('<i class="fa fa-check" aria-hidden="true" style="color: green;"></i> ' + row.processing_start);
            }
            tr.append(tdStarted);

            var tdFinished = $('<td>');
            if (row.processing_finish === null) {
                if (row.status === 'queued' || row.status === 'restart') {
                    tdFinished.html(
                        '<i class="fa fa-ellipsis-h" aria-hidden="true" style="color: blue;"></i> '
                        + comparisonTable.attr('data-lang_comparison_action_waiting')
                    );
                } else {
                    tdFinished.html(
                        '<i class="fa fa-cog fa-spin fa-fw" aria-hidden="true" style="color: orange;"></i> '
                        + comparisonTable.attr('data-lang_comparison_action_pending')
                    );
                }
            } else {
                var symbol = '<i class="fa fa-check" aria-hidden="true" style="color: green;"></i> ';
                if (row.status !== 'finished') {
                    symbol = '<i class="fa fa-exclamation" aria-hidden="true" style="color: red;"></i> ';
                }
                tdFinished.html(symbol + row.processing_finish);
            }
            tr.append(tdFinished);

            const tdControls = $('<td>');
            tdControls.addClass('controlls');

            if (row.status === 'finished') {
                const buttonResults = $('<a>');
                buttonResults.addClass('button').addClass('results').addClass('controlElement');
                buttonResults.attr('href', row.result_link);
                buttonResults.attr('target', '_blank');
                buttonResults.html('<i class="fa fa-external-link" aria-hidden="true"></i>');
                tdControls.append(buttonResults);
            } else if (row.status === 'failed' || row.status === 'restart') {
                if (row.failure_message !== null) {
                    const buttonFailureStatus = $('<a>');
                    buttonFailureStatus.addClass('button').addClass('delete').addClass('controlElement');
                    buttonFailureStatus.html('<i class="fa fa-question" aria-hidden="true"></i>');
                    const failureMessage = row.failure_message;
                    const eventFunction = function () {
                        alert(failureMessage);
                    };
                    buttonFailureStatus.on('click', eventFunction);
                    tdControls.append(buttonFailureStatus);
                }
            } else {
                const buttonRequeue = $('<a>');
                buttonRequeue.addClass('button').addClass('special').addClass('controlElement');
                buttonRequeue.html('<i class="fa fa-refresh" aria-hidden="true"></i>');
                const thisRowID = row.id;
                const eventFunction = function () {
                    api_ajax_load_json(
                        comparisonTable.attr('data-link_requeue') + '/' + thisRowID,
                        'post',
                        {},
                        function (data) {
                            if (data.status === 'queued') {
                                show_notification(
                                    comparisonTable.attr('data-lang_comparison_requeue_queued'),
                                    'success'
                                );
                                reload_comparisons();
                            } else if (data.status === 'notFound') {
                                show_notification(
                                    comparisonTable.attr('data-lang_comparison_requeue_notFound'),
                                    'error'
                                );
                            } else if (data.status === 'invalidStatus') {
                                show_notification(
                                    comparisonTable.attr('data-lang_comparison_requeue_invalidStatus'),
                                    'error'
                                );
                                reload_comparisons();
                            }
                        }
                    );
                };
                buttonRequeue.click(eventFunction);
                tdControls.append(buttonRequeue);
            }

            tr.append(tdControls);
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

        $(pages_select).unbind('change');

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

        $(pages_select).on('change', function () {
            reload_comparisons();
        });

        var per_page_select = $(tfoot).find('select[name=paging_rows_per_page]').get(0);

        $(per_page_select).unbind('change');

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

        $(per_page_select).on('change', function () {
            reload_comparisons();
        });
    };

    reload_comparisons();
    timer = setInterval(reload_comparisons, 10000);

});