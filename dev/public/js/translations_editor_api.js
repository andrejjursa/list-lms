jQuery(document).ready(function($){
    
    var on_button_save_click = function(event) {
        var data = $(this).parents('tr').find('textarea').serializeArray();
        var url = global_base_url + 'index.php/admin_translationseditor/ajax_save/';
        $.ajax(url, {
            cache: false,
            type: 'post',
            data: data,
            dataType: 'json'
        });
    };
    
    var on_button_delete_click = function(event) {
        var classes = $(this).parents('tr')[0].classList;
        for ( i in classes) {
            if (classes[i].substr(0, 9) == 'constant_') {
                var constant = classes[i].substr(9);
                var url = global_base_url + 'index.php/admin_translationseditor/ajax_delete/' + constant + '/';
                $.ajax(url, {
                    cache: false,
                    type: 'post',
                    dataType: 'json'
                });
                return;
            }
        }
    };
    
    $(document).on('click', 'input[type=button][name=button_save]', on_button_save_click);
    
    $(document).on('click', 'input[type=button][name=button_delete]', on_button_delete_click);
    
});