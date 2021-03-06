var translation_selector = function(jquerySelector) {
    if (typeof jquerySelector != 'string') { return; }
    var element = jQuery(jquerySelector).filter('input[type=text], input[type=password], textarea');
    if (element.length > 0) {
        jQuery.ajax(global_base_url + 'index.php/admin_translationseditor/translations_json', {
            cache: true,
            dataType: 'json',
            success: function(json_data) {
                var selectorHTML = jQuery('<div class="custom_translations_selector" style="position: absolute; z-index: 50; display: none; border: 1px solid black; border-radius: 3px; background-color: white;"></div>');
                
                selectorHTML.html(render_content(json_data, $(element[0]).val()));
                $(element[0]).after(selectorHTML);
                $(element[0]).focus(function() {
                    selectorHTML.css('top', '').css('left', '');
                    selectorHTML.position({
                        'my': 'left top',
                        'at': 'left bottom',
                        'of': jquerySelector + ':first'
                    });
                    selectorHTML.css('width', $(element[0]).width());
                    selectorHTML.data('canhide', true);
                    selectCurrent(selectorHTML, $(this).val());
                    selectorHTML.show();
                }).blur(function(){
                    var canhide = selectorHTML.data('canhide');
                    if (canhide) {
                        selectorHTML.hide();
                    }
                }).keyup(function() {
                    selectorHTML.html(render_content(json_data, $(element[0]).val()));
                    selectCurrent(selectorHTML, $(this).val());
                    applyEvents();
                });
                selectorHTML.mouseover(function() {
                    selectorHTML.data('canhide', false);
                }).mousemove(function() {
                    selectorHTML.data('canhide', false);
                }).mouseout(function() {
                    selectorHTML.data('canhide', true);
                    $(element[0]).focus();
                });
                
                var applyEvents = function() {
                    selectorHTML.find('div.selection li').click(function() {
                        $(element[0]).val('lang:user_custom_' + $(this).attr('rel'));
                        selectorHTML.html(render_content(json_data, 'lang:user_custom_' + $(this).attr('rel')));
                        selectorHTML.find('div.selection li').removeClass('selected');
                        $(this).addClass('selected');
                        applyEvents();
                        $(element[0]).focus();
                    }).css('cursor', 'pointer').css('list-style-type', 'none').mouseover(function() {
                        $(this).addClass('mouseover');
                    }).mouseout(function() {
                        $(this).removeClass('mouseover');
                    });
                };
                var selectCurrent = function(selectorHTML, value) {
                    selectorHTML.find('div.selection li').each(function() {
                        $(this).removeClass('selected');
                        if (('lang:user_custom_' + $(this).attr('rel')) == value) {
                            $(this).addClass('selected');                            
                        }
                    });
                };
                
                applyEvents();
            }
        });
    }
    
    var render_content = function(data, filter) {
        var filter_regex = new RegExp(filter);
        var content = '<div class="selection" style="margin: 0; padding: 0; max-height: 12em; overflow-y: auto;">';
        content += '<ul style="margin: 0; padding: 0;">';
        for (i in data) {
            if (data[i].text != '' && data[i].constant != '' && filter_regex.test('lang:user_custom_' + data[i].constant)) {
                content += '<li rel="' + (data[i].constant) + '" style="margin: 0; padding: 0; line-height: 1.2em; padding: 0 0.1em;"><span style="width: 50%; display: inline-block;">' + (data[i].text) + '</span> (user_custom_' + (data[i].constant) + ')</li>';
            }
        }
        content += '</ul>';
        content += '</div>';
        
        return content;
    };
};