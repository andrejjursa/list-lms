jQuery(document).ready(function($) {
    if (jQuery.datepicker != undefined) {
        jQuery.datepicker.setDefaults(jQuery.datepicker.regional[jqueryui_datepicker_region]);
    }
    if (jQuery.timepicker != undefined) {
        jQuery.timepicker.setDefaults(jQuery.timepicker.regional[jqueryui_datepicker_region]);
    }

    jQuery('[title]').tooltip();
});

var block_ui_message = lang != undefined && lang.messages != undefined && lang.messages.ajax_standby != undefined ? lang.messages.ajax_standby : 'Please wait ...';
jQuery(document).ajaxStart(function () {
  jQuery.blockUI({
    message: '<h1>' + block_ui_message + '</h1>',
    css: {
      borderRadius: '10px',
      border: 'none',
      padding: '15px',
      backgroundColor: 'black',
      color: 'white',
      opacity: 0.5 
    }
  });
}).ajaxStop(function() {
    jQuery('[title]').tooltip();
    jQuery.unblockUI();
});

var make_filter_form = function(selector) {
    if (typeof(selector) == 'string') {
        var filter = jQuery(selector);
        if (filter.length == 1 && filter.is('form')) {
            var filter_header = jQuery('<div></div>');
            var filter_content = jQuery('<div></div>');
            filter.after(filter_content);
            filter.after(filter_header);
            filter_header.css({
                'font-weight': 'bold',
                'line-height': '1.3em',
                'cursor': 'pointer'
            }).addClass('ui-widget-header');
            var filter_text = '';
            if (lang != undefined && lang.messages != undefined && lang.messages.filter_header != undefined) { filter_text = lang.messages.filter_header; }
            filter_header.html('<span class="ui-icon ui-icon-plusthick" style="float: left;"></span> ' + filter_text);
            filter_content.css({
                'display': 'none'
            });
            filter.appendTo(filter_content);
            filter_header.click(function(){
                filter_content.toggle();
                filter_header.find('span.ui-icon').toggleClass('ui-icon-minusthick').toggleClass('ui-icon-plusthick');
            });
        }
    }
}

var show_notification = function(text, notif_type) {
  if (text == undefined) { return; }
  if (notif_type == undefined || notif_type == null) { notif_type = 'information'; }
  showNotification({
    message: text,
    type: notif_type,
    autoClose: true,
    duration: 5
  });
}

var api_make_tabs = function(structure_id_attr_value, options) {
    var structure = jQuery('#' + structure_id_attr_value);
    if (options == undefined) { options = {}; }
    structure.tabs(options);
    var tab_num = 1;
    jQuery(structure).find('> div').each(function() {
        if (jQuery(this).find('p.error').length > 0) {
            jQuery(structure).find('> ul li:nth-child(' + tab_num + ')').addClass('ui-state-error').removeClass('ui-state-default');
        }
        tab_num++;
    });
}

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
            if (typeof(target) == 'string') {
                jQuery(target).html(html);
            } else if (typeof(target) == 'object') {
                target.html(html);
            }
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

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}

/**
* Updated version to work with my replaced characters to support transport via url segments.
**/

var Base64url = {
    
    encode: function (input) {
        var encoded = Base64.encode(input);
        encoded = encoded.replace(/\//g, '-');
        encoded = encoded.replace(/\=/g, '_');
        return encoded;
    },
    
    decode: function (input) {
        var transformed_input = input.replace(/\-/g, '/');
        transformed_input = transformed_input.replace(/\_/g, '=');
        return Base64.decode(transformed_input);
    }
}

/**
 * Javascript functions to show top nitification
 * Error/Success/Info/Warning messages
 * Developed By: Ravi Tamada
 * url: http://androidhive.info
 * demo: http://demos.9lessons.info/jnotification
 * © androidhive.info
 * 
 * Created On: 10/4/2011
 * version 1.0
 * 
 * Usage: call this function with params 
 showNotification(params);
 **/

function showNotification(params){
    // options array
    var options = { 
        'showAfter': 0, // number of sec to wait after page loads
        'duration': 0, // display duration
        'autoClose' : false, // flag to autoClose notification message
        'type' : 'success', // type of info message error/success/info/warning
        'message': '', // message to dispaly
        'link_notification' : '', // link flag to show extra description
        'description' : '' // link to desciption to display on clicking link message
    }; 
    // Extending array from params
    jQuery.extend(true, options, params);
    
    var msgclass = 'succ_bg'; // default success message will shown
    if(options['type'] == 'error'){
        msgclass = 'error_bg'; // over write the message to error message
    } else if(options['type'] == 'information'){
        msgclass = 'info_bg'; // over write the message to information message
    } else if(options['type'] == 'warning'){
        msgclass = 'warn_bg'; // over write the message to warning message
    } 
    
    // Parent Div container
    var container = '<div id="info_message" class="'+msgclass+'"><div class="center_auto"><div class="info_message_text message_area">';
    container += options['message'];
    container += '</div><div class="info_close_btn button_area" onclick="return closeNotification()"></div><div class="clearboth"></div>';
    container += '</div><div class="info_more_descrption"></div></div>';
    
    $notification = jQuery(container);
    
    // Appeding notification to Body
    jQuery('body').append($notification);
    
    var divHeight = jQuery('div#info_message').height();
    // see CSS top to minus of div height
    jQuery('div#info_message').css({
        top : '-'+divHeight+'px'
    });
    
    // showing notification message, default it will be hidden
    jQuery('div#info_message').show();
    
    // Slide Down notification message after startAfter seconds
    slideDownNotification(options['showAfter'], options['autoClose'],options['duration']);
    
    jQuery(document).on('click', '.link_notification', function(){
        jQuery('.info_more_descrption').html(options['description']).slideDown('fast');
    });
    
}
// function to close notification message
// slideUp the message
function closeNotification(duration){
    var divHeight = jQuery('div#info_message').height();
    setTimeout(function(){
        jQuery('div#info_message').animate({
            top: '-'+divHeight
        }); 
        // removing the notification from body
        setTimeout(function(){
            jQuery('div#info_message').remove();
        },200);
    }, parseInt(duration * 1000));   
    

    
}

// sliding down the notification
function slideDownNotification(startAfter, autoClose, duration){    
    setTimeout(function(){
        jQuery('div#info_message').animate({
            top: 0
        }); 
        if(autoClose){
            setTimeout(function(){
                closeNotification(duration);
            }, duration);
        }
    }, parseInt(startAfter * 1000));    
}