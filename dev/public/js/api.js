jQuery(document).ready(function($) {
    if (jQuery.datepicker !== undefined) {
        jQuery.datepicker.setDefaults(jQuery.datepicker.regional[jqueryui_datepicker_region]);
    }
    if (jQuery.timepicker !== undefined) {
        jQuery.timepicker.setDefaults(jQuery.timepicker.regional[jqueryui_datepicker_region]);
    }

    try {
        jQuery('[title]').tooltip();
    } catch (e) {
        console.log(e);
    }
});

(function($){
    $.fn.formErrorWarning = function() {
        var self = this;
        
        if (self.is('form')) {
            var errors = self.find('p.error span.message');
            if (errors.length > 0) {
                var dialog_content = $('<div></div>').addClass('error-notification-dialog-content');
                dialog_content.insertAfter($(self));
                var errors_list = $('<ul></ul>').addClass('error-notification-dialog-list');
                errors_list.appendTo(dialog_content);
                $(errors).each(function() {
                    var error_message = $('<li></li>').addClass('error-message');
                    error_message.appendTo(errors_list);
                    error_message.html($(this).html());
                });
                $('div.error-notification-dialog-content').dialog({
                    modal: true,
                    buttons: [ { text: lang.messages.error_notification_dialog_close, click: function() { $( this ).dialog( "close" ); } } ],
                    title: lang.messages.error_notification_dialog_title,
                    closeOnEscape: true,
                    closeText: lang.messages.error_notification_dialog_close,
                    minWidth: 400,
                    maxWidth: 1800,
                    maxHeight: 600,
                    dialogClass: 'alert',
                    close: function() {
                        dialog_content.remove();
                    }
                }).parent().addClass("ui-state-error");
                $('.ui-widget-overlay').css('background', '#ff9999');
                $('.ui-dialog-buttonpane, .ui-dialog-buttonpane .ui-button, .ui-dialog-titlebar, .ui-dialog-titlebar .ui-button').addClass('ui-state-error');
                $('.ui-dialog-buttonpane').css({
                    'border-left': '0 none',
                    'border-right': '0 none',
                    'border-bottom': '0 none'
                });
            }
        } else {
            console.log('Can\'t call formErrorWarning on not a form element!');
        }
        
        return self;
    };
})(jQuery);

(function($){
    $.fn.serializeObject = function(){

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
})(jQuery);

var block_ui_message = (lang !== undefined && lang.messages !== undefined && lang.messages.ajax_standby !== undefined) ? lang.messages.ajax_standby : 'Please wait ...';
jQuery(document).ajaxStart(function () {
  jQuery.blockUI({
    message: '<img src="' + global_base_url + 'public/images_ui/loading.gif" alt="' + block_ui_message + '" width="48"/>',
    showOverlay: false,
    css: {
      top: '25px',
      left: '',
      right: '20px',
      borderRadius: '100px',
      border: '3px solid black',
      padding: '5px 5px 2px 5px',
      backgroundColor: 'white',
      color: 'black',
      width: '48px',
      'box-shadow': '3px 3px 3px black',
      opacity: 0.65
    }
  });
}).ajaxStop(function() {
    try {
        jQuery('[title]').tooltip();
        jQuery.unblockUI();
    } catch (e) {
        console.log(e);
    }
});

var update_select_values_by = function(select_element, parent_value, values_array, selected_value, add_blank, callback) {
    if (selected_value === undefined) { selected_value = null; }
    if (add_blank === undefined) { add_blank = true; }
    if (callback === undefined) { callback = function() {}; }
    if (select_element.is('select')) {
        select_element.html('');
        if (add_blank) {
            var option = jQuery('<option></option>');
            option.appendTo(select_element);
        }
        if (typeof values_array !== 'undefined' && typeof values_array[parent_value] !== undefined) {
            for (var i in values_array[parent_value]) {
                var option = jQuery('<option></option>');
                option.appendTo(select_element);
                option.text(values_array[parent_value][i].text);
                option.attr('value', values_array[parent_value][i].value);
                if (selected_value !== null && values_array[parent_value][i].value.toString() === selected_value) {
                    option.attr('selected', true);
                }
            }
        }
    }
    if (typeof callback === 'function') {
        callback();
    }
};

var fields_filter = function(open_selector, reload_callback) {
    var open_button = jQuery(open_selector);
    if (open_button.length !== 0) {
        open_button = open_button[0];
        jQuery(open_button).click(function() {
            jQuery('#fields_filter_table_id').css({
                'position': 'absolute'
            }).show().position({
                of: jQuery(open_button),
                my: 'right top',
                at: 'right bottom',
                collision: 'flip flip'
            });
        }).addClass('fields_config_open_button');
        jQuery('#fields_filter_table_id a.close_button').click(function(event) {
            event.preventDefault();
            jQuery('#fields_filter_table_id').hide();
            reload_callback();
        });
    }
};

var field_filter_checkbox = function(checkbox_selector, filter_form_selector, field_name) {
    var checkbox = jQuery(checkbox_selector);
    if (checkbox.length !== 0) {
        checkbox = checkbox[0];
        var filter_form = jQuery(filter_form_selector);
        if (filter_form.length !== 0) {
            filter_form = filter_form[0];
            var filter_form_input = jQuery(filter_form).find('input[name="filter[fields][' + field_name + ']"]');
            if (filter_form_input.lenght !== 0) {
                filter_form_input = filter_form_input[0];
                jQuery(checkbox).change(function() {
                    if (jQuery(this).is(':checked')) {
                        jQuery(filter_form_input).val('1');
                    } else {
                        jQuery(filter_form_input).val('0');
                    }
                });
            }
        }
    }
};

var make_custom_switch = function(selector, text, custom_class) {
    jQuery(selector).each(function() {
        var element = jQuery(this);
        var wrapper = jQuery('<div></div>');
        wrapper.insertAfter(element);
        wrapper.addClass('custom_switch_wrapper');
        element.appendTo(wrapper);
        var header = jQuery('<div></div>');
        header.insertBefore(wrapper);
        header.css({
            'font-weight': 'bold',
            'line-height': '1.3em',
            'cursor': 'pointer'
        }).addClass('ui-widget-header').addClass('custom_switch_header');
        if (typeof custom_class === 'string') {
            wrapper.addClass(custom_class);
            header.addClass(custom_class);
        }
        var header_text = typeof text === 'string' ? text : '';
        header.html('<span class="ui-icon ui-icon-plusthick" style="float: left;"></span> ' + header_text);
        wrapper.css({
            display: 'none'
        });
        header.click(function(){
            wrapper.slideToggle('normal');
            header.find('span.ui-icon').toggleClass('ui-icon-minusthick').toggleClass('ui-icon-plusthick');
        });
    });
};

var make_overlay_editors = function() {
    jQuery('form div.overlay_block').each(function() {
        var element = jQuery(this);
        var overlay_header = jQuery('<div></div>');
        var overlay_header_wrap = jQuery('<div></div>');
        var overlay_wrapper = jQuery('<div></div>');
        element.find('> *').appendTo(overlay_wrapper);
        overlay_header_wrap.appendTo(element);
        overlay_header.appendTo(overlay_header_wrap);
        overlay_wrapper.appendTo(element);
        overlay_header.css({
            'font-weight': 'bold',
            'line-height': '1.3em',
            'cursor': 'pointer'
        }).addClass('ui-widget-header');
        overlay_header_wrap.addClass('overlay-editor-header');
        var header_text = '';
        if (lang !== undefined && lang.messages !== undefined && lang.messages.overlay_editor_header !== undefined) { header_text = lang.messages.overlay_editor_header; }
        overlay_header.html('<span class="ui-icon ui-icon-plusthick" style="float: left;"></span> ' + header_text);
        overlay_wrapper.css({
            display: 'none'
        });
        overlay_header.click(function(){
            overlay_wrapper.slideToggle('normal');
            overlay_header.find('span.ui-icon').toggleClass('ui-icon-minusthick').toggleClass('ui-icon-plusthick');
        });
    });
};

var make_switchable_form = function(selector) {
    if (typeof(selector) === 'string') {
        var filter = jQuery(selector);
        if (filter.length === 1 && filter.is('form')) {
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
            if (lang !== undefined && lang.messages !== undefined && lang.messages.form_header !== undefined) { filter_text = lang.messages.form_header; }
            filter_header.html('<span class="ui-icon ui-icon-plusthick" style="float: left;"></span> ' + filter_text);
            filter_content.css({
                'display': 'none'
            });
            filter.appendTo(filter_content);
            filter_header.click(function(){
                filter_content.slideToggle('normal');
                filter_header.find('span.ui-icon').toggleClass('ui-icon-minusthick').toggleClass('ui-icon-plusthick');
            });
        }
    }
};

var make_filter_form = function(selector) {
    if (typeof(selector) === 'string') {
        var filter = jQuery(selector);
        if (filter.length === 1 && filter.is('form')) {
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
            if (lang !== undefined && lang.messages !== undefined && lang.messages.filter_header !== undefined) { filter_text = lang.messages.filter_header; }
            filter_header.html('<span class="ui-icon ui-icon-plusthick" style="float: left;"></span> ' + filter_text);
            filter_content.css({
                'display': 'none'
            });
            filter.appendTo(filter_content);
            filter_header.click(function(){
                filter_content.slideToggle('normal');
                filter_header.find('span.ui-icon').toggleClass('ui-icon-minusthick').toggleClass('ui-icon-plusthick');
            });
        }
    }
};

var sort_table = function(table_selector, filter_selector) {
    var table = table_selector;
    if (typeof table_selector === 'string') {
        table = jQuery(table_selector);
    } 
    if (typeof table === 'object') {
        try {
            if ($(table[0]).is('table')) {
                table = table[0];
            } else {
                return;
            }
        } catch(e) {
            return;
        }
    }
    var filter = filter_selector;
    if (typeof filter_selector === 'string') {
        filter = jQuery(filter_selector);
    }
    if (typeof filter === 'object') {
        try {
            if ($(filter[0]).is('form')) {
                filter = filter[0];
            } else {
                return;
            }
        } catch(e) {
            return;
        }
    }
    var filter_field = jQuery(filter).find('input[type=hidden][name="filter[order_by_field]"]');
    var filter_direction = jQuery(filter).find('input[type=hidden][name="filter[order_by_direction]"]');
    if (filter_field.length === undefined || filter_field.length === 0 || filter_direction.length === undefined || filter_direction.length === 0) {
        try {
            console.log('Filter missing inputs of type hidden with names filter[order_by_field] or filter[order_by_direction]!');
        } catch (e) {
            alert('Filter missing inputs of type hidden with names filter[order_by_field] or filter[order_by_direction]!');    
        }
        return;
    }
    
    var regex_sort = /\bsort\:[a-z0-9_]+(:desc)?\b/i;
    
    var default_direction = filter_direction.val().toLowerCase();
    var default_field = filter_field.val();
    
    var replace_icons = function() {
        var default_direction = filter_direction.val().toLowerCase();
        var default_field = filter_field.val();
        jQuery(table).find('thead tr th').each(function() {
            var field_config = regex_sort.exec(jQuery(this).attr('class'));
            if (field_config !== null) {
                var parts = field_config[0].split(':');
                var field = parts[1];
                var icon = $(this).find('div.ui-icon');
                icon.attr('class', 'ui-icon ' + (default_field === field ? (default_direction === '' || default_direction === 'asc' ? 'ui-icon-circle-triangle-n' : 'ui-icon-circle-triangle-s') : 'ui-icon-circle-plus'));
            }
        });
    };
    
    jQuery(table).find('thead tr th').each(function() {
        var field_config = regex_sort.exec(jQuery(this).attr('class'));
        if (field_config !== null) {
            var parts = field_config[0].split(':');
            var field = parts[1];
            var direction = parts[2] === undefined ? 'asc' : 'desc';
            var icon = jQuery('<div></div>');
            icon.attr('class', 'ui-icon ' + (default_field === field ? (default_direction === '' || default_direction === 'asc' ? 'ui-icon-circle-triangle-n' : 'ui-icon-circle-triangle-s') : 'ui-icon-circle-plus'));
            icon.css('float', 'right').css('margin-top', '3px');
            var content = jQuery('<div></div>');
            content.html(jQuery(this).html());
            content.css('margin-right', '21px');
            jQuery(this).html('');
            jQuery(this).prepend(content);
            jQuery(this).prepend(icon);
            jQuery(this).click(function() {
                var old_field = filter_field.val();
                filter_field.val(field);
                if (old_field !== field) {
                    filter_direction.val(direction);
                } else {
                    var old_direction = filter_direction.val();
                    if (old_direction === '' || old_direction === 'asc') {
                        filter_direction.val('desc');
                    } else {
                        filter_direction.val('asc');
                    }
                }
                replace_icons();
                $(filter).submit();
            }).css('cursor', 'pointer');
        }
    });
};

var show_notification = function(text, notif_type) {
  if (text === undefined) { return; }
  display_notification(text, notif_type);
};

var api_read_class_config = function(obj, class_name) {
    if (typeof obj === 'string') {
        obj = jQuery(obj);
    }
    
    var classes = obj.attr('class');
    var sz = class_name.length;
    var idx = classes.indexOf(class_name + ':');
    if (idx >= 0) {
        var end = classes.indexOf(' ', idx + sz + 1);
        if (end >= 0) {
            return classes.substring(idx + sz + 1, end);
        } else {
            return classes.substring(idx + sz + 1);
        }
    } else {
        return null;
    }
};

var api_read_url_anchor = function() {
    var url = document.URL;
    var idx = url.indexOf('#');
    var anchor = idx !== -1 ? url.substring(idx + 1) : '';
    return anchor;
}; 

var api_make_tabs = function(structure_id_attr_value, options) {
    var structure = jQuery('#' + structure_id_attr_value);
    if (options === undefined) { options = {}; }
    structure.tabs(options);
    var tab_num = 1;
    jQuery(structure).find('> div').each(function() {
        if (jQuery(this).find('p.error').length > 0) {
            jQuery(structure).find('> ul li:nth-child(' + tab_num + ')').addClass('ui-state-error').removeClass('ui-state-default');
        }
        tab_num++;
    });
};

var api_ajax_load = function(url, target, method, data, onSuccess, onError) {
    method = method === undefined ? 'post' : method;
    data = data === undefined ? {} : data;
    onError = onError === undefined ? function(jqXHR) {
        var html = '<div style="margin: 2em; font-size: 125%;"><strong>HTTP ERROR occured, status ' + jqXHR.status + ' (' + jqXHR.statusText + '), with message:</strong></div><div style="margin: 2em;">' + jqXHR.responseText + '</div>';
        console.log(jqXHR);
        if (typeof(target) === 'string') {
            jQuery(target).html(html);
        } else if (typeof(target) === 'object') {
            target.html(html);
        }
    } : onError;
    onSuccess = onSuccess === undefined ? function() {}: onSuccess;
    
    jQuery.ajax(url, {
        cache: false,
        dataType: 'html',
        data: data,
        method: method,
        success: function(html) {
            if (typeof(target) === 'string') {
                jQuery(target).html(html);
            } else if (typeof(target) === 'object') {
                target.html(html);
            }
            onSuccess(html);
        },
        error: onError
    });
};

var api_ajax_update = function(url, method, data, onSuccess, onError, dataType) {
    method = method === undefined ? 'post' : method;
    data = data === undefined ? {} : data;
    onError = onError === undefined ? function() {} : onError;
    onSuccess = onSuccess === undefined ? function() {}: onSuccess;
    dataType = dataType === undefined ? 'json' : dataType;
    
    jQuery.ajax(url, {
        cache: false,
        dataType: dataType,
        data: data,
        method: method,
        success: onSuccess,
        error: onError
    });
};

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
 
			if (enc3 !== 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 !== 64) {
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
 
};

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
};

var display_notification = function(message, type) {
    
    if (type === undefined || type === null) { type = 'info'; }
    
    var notification_area = jQuery('#list_dynamic_notification_area_id');
    
    if (notification_area.length === 0) {
        notification_area = jQuery('<div id="list_dynamic_notification_area_id"></div>').appendTo('body');
    }
    
    var notification_box = jQuery('<div class="notification_box"></div>').prependTo(notification_area);
    notification_box.html('<div class="message">' + message + '</div>');
    notification_box.addClass('type_' + type);
    notification_box.css('display', 'none');
    notification_box.fadeIn(500, function() {
        notification_box.fadeTo(10000, 0.8, function() {
            notification_box.animate({
                height: 0,
                opacity: 0
            }, 500, 'swing', function() {
                notification_box.remove();
            });
        });
    });
    var close_button = jQuery('<span class="close_button">x</span>').prependTo(notification_box);
    close_button.click(function() {
        notification_box.stop();
        notification_box.animate({
            height: 0,
            opacity: 0
        }, 500, 'swing', function() {
            notification_box.remove();
        });
    });
};

var test_window_maximized = function() {
    if ( screen.height === window.innerHeight ) { // FullScreen Mode
        return true;
    }
    else if ( screen.availHeight === window.outerHeight ) { // Maximized
        return true;
    }
    return false;
};

var tinymce_switch_highlight = function(editor, language) {
    var element = editor.selection.getEnd();
    if (editor.dom.is(element, 'pre')) {
        var addClass = true;
        if (editor.dom.hasClass(element, 'prettyprint')) {
            if (editor.dom.hasClass(element, language)) {
                editor.dom.removeClass(element, 'prettyprint');
                editor.dom.removeClass(element, language);
                addClass = false;
            } else {
                var classes = editor.dom.getAttrib(element, 'class', '').split(' ');
                for (var i in classes) {
                    if (classes[i].substr(0, 5) === 'lang-') {
                        editor.dom.removeClass(element, classes[i]);
                    }
                }
            }
        }
        if (addClass) {
            editor.dom.addClass(element, 'prettyprint');
            editor.dom.addClass(element, language);
        }
    }
};

 var inArray = function(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] === needle) return true;
    }
    return false;
};