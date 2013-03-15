var api_ajax_load = function(url, target, method, data, onSuccess, onError) {
    method = method == undefined ? 'post' : method;
    data = data == undefined ? {} : data;
    onError = onError == undefined ? function() {} : onError;
    onSuccess = onSuccess == undefined ? function() {}: onSuccess;
    
    jQuery.ajax(url, {
        cache: false,
        dataType: 'html',
        data: data,
        method: method,
        success: function(html) {
            jQuery(target).html(html);
            onSuccess(html);
        },
        error: onError
    });
}

var api_ajax_update = function(url, method, data, onSuccess, onError, dataType) {
    method = method == undefined ? 'post' : method;
    data = data == undefined ? {} : data;
    onError = onError == undefined ? function() {} : onError;
    onSuccess = onSuccess == undefined ? function() {}: onSuccess;
    dataType = dataType == undefined ? 'json' : dataType;
    
    jQuery.ajax(url, {
        cache: false,
        dataType: dataType,
        data: data,
        method: method,
        success: onSuccess,
        error: onError
    });
}