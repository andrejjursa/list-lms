jQuery(document).ready(function($) {
    
    var reload_comparisons = function() {
        var url = $('#filter_form_id').attr('action');
        console.log('URL:', url);
        api_ajax_load_json(url, 'post', {}, function(data) {
            console.log(data);
            fill_table(data);
        });
    };

    reload_comparisons();

    var fill_table = function(data) {
        var tbody = $('#comparisons_table_id tbody');
        tbody.html('');
        
        for (var i in data.data) {
            row = data.data[i];

            tr = $('<tr>');
            tbody.append(tr);

            tdId = $('<td>');
            tdId.text(row.id);
            tr.append(tdId);

            tdTeacher = $('<td>');
            if (row.teacher === null) {
                tdTeacher.text('-');
            } else {
                tdTeacher.text(row.teacher.full_name);
            }
            tr.append(tdTeacher);

            tdStatus = $('<td>');
            statusHTML = '';
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

            tdStarted = $('<td>');
            if (row.processing_start === null) {
                tdStarted.html('<i class="fa fa-calculator" aria-hidden="true" style="color: orange;"></i> pending');
            } else {
                tdStarted.text(row.processing_start);
            }
            tr.append(tdStarted);

            tdFinished = $('<td>');
            if (row.processing_finish === null) {
                tdFinished.html('<i class="fa fa-calculator" aria-hidden="true" style="color: orange;"></i> pending');
            } else {
                tdFinished.text(row.processing_finish);
            }
            tr.append(tdFinished);
        }
    };

});