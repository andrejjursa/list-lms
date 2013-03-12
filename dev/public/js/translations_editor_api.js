jQuery(document).ready(function($){
    
    var get_working_text = function() {
        return '<p>' + messages.working + '</p>';
    };
    
    var on_button_save_click = function(event) {
        var data = $(this).parents('tr.row_of_constant').find('textarea').serializeArray();
        var row = $(this).parents('tr.row_of_constant');
        var cell = $(this).parents('td.controlls');
        var url = global_base_url + 'index.php/admin_translationseditor/ajax_save/';
        cell.html(get_working_text());
        $.ajax(url, {
            cache: false,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.result != undefined && data.result == true) {
                    row.html(data.row);
                    row.removeClass('changed_row');
                    row.addClass('saved_row');
                    setTimeout(function() {
                        row.removeClass('saved_row');
                    }, 5000);
                } else {
                    alert(messages.error.operation_failed);
                }
            },
            error: function(data) {
                alert(messages.error.operation_failed);
            }
        });
    };
    
    var on_button_delete_click = function(event) {
        var classes = $(this).parents('tr.row_of_constant')[0].classList;
        var row = $(this).parents('tr.row_of_constant');
        var cell = $(this).parents('td.controlls');
        for ( i in classes) {
            if (classes[i].substr(0, 9) == 'constant_') {
                var constant = classes[i].substr(9);
                var url = global_base_url + 'index.php/admin_translationseditor/ajax_delete/' + constant + '/';
                if (!confirm(messages.delete_question.replace('[$constant$]', constant))) { return; }
                cell.html(get_working_text());
                $.ajax(url, {
                    cache: false,
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                        if (data == true) {
                            row.slideUp(1000, function() {
                                row.remove();
                            });
                        } else {
                            alert(messages.error.operation_failed);
                        }
                    },
                    error: function() {
                        alert(messages.error.operation_failed);
                    }
                });
                return;
            }
        }
    };
    
    var on_button_new_click = function(event) {
        var url = global_base_url + 'index.php/admin_translationseditor/new_constant/';
        $.fancybox(url, {
            type: 'iframe',
            width: 800,
            height: 400,
            autoSize: false,
            beforeClose: function() {
                $('#translations_table tbody tr td.controlls').html(get_working_text());
                var table_body = $('#translations_table tbody');
                var url = global_base_url + 'index.php/admin_translationseditor/reload_table/';
                $.ajax(url, {
                    dataType: 'html',
                    cache: false,
                    success: function(data) {
                        table_body.html(data);
                    }
                });
                return true;
            }
        });
    };
    
    var on_textarea_change = function(event) {
        var row = $(this).parents('tr.row_of_constant');
        row.removeClass('saved_row');
        row.addClass('changed_row');
    };
    
    $(document).on('change', 'textarea', on_textarea_change);
    $(document).on('key_up', 'textarea', on_textarea_change);
    
    $(document).on('click', 'input[type=button][name=button_save]', on_button_save_click);
    
    $(document).on('click', 'input[type=button][name=button_delete]', on_button_delete_click);
    
    $('input[type=button][name=button_new]').click(on_button_new_click);
    
});