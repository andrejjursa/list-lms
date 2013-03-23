var translation_selector = function(jquerySelector) {
    if (typeof jquerySelector != 'string') { return; }
    var element = jQuery(jquerySelector).filter('input[type=text], input[type=password], textarea');
    if (element.length > 0) {
        jQuery.ajax(global_base_url + 'index.php/admin_translationseditor/translations_json', {
            cache: true,
            dataType: 'json',
            success: function(json_data) {
                var selectorHTML = jQuery('<div class="custom_translations_selector" style="position: absolute; z-index: 100; display: none; border: 1px solid black; background-color: white;"></div>');
                selectorHTML.position({
                    'my': 'left top',
                    'at': 'left bottom',
                    'of': jquerySelector + ':first'
                });
                selectorHTML.html(render_content(json_data));
                selectorHTML.css('max-width', $(element[0]).width());
                $(element[0]).after(selectorHTML);
                $(element[0]).focus(function() {
                    selectorHTML.show();
                }).blur(function(){
                    var canhide = selectorHTML.data('canhide');
                    if (canhide) {
                        selectorHTML.hide();
                    }
                });
                selectorHTML.mouseover(function() {
                    selectorHTML.data('canhide', false);
                }).mousemove(function() {
                    selectorHTML.data('canhide', false);
                }).mouseout(function() {
                    selectorHTML.data('canhide', true);
                    $(element[0]).focus();
                });
                selectorHTML.find('div.selection li').click(function(){
                    $(element[0]).val('lang:user_custom_' + $(this).attr('rel'));
                }).css('cursor', 'pointer').css('list-style-type', 'none').mouseover(function() {
                    $(this).addClass('mouseover');
                }).mouseout(function() {
                    $(this).removeClass('mouseover');
                });
            }
        });
    }
    
    var render_content = function(data) {
        var content = '<div class="selection" style="margin: 0; padding: 0; max-height: 6em; overflow-y: auto;">';
        content += '<ul style="margin: 0; padding: 0;">';
        for (i in data) {
            if (data[i].text != '') {
                content += '<li rel="' + (data[i].constant) + '" style="margin: 0; padding: 0; line-height: 1.2em; padding: 0 0.1em;">' + (data[i].text) + '</li>';
            }
        }
        content += '</ul>';
        content += '</div>';
        
        return content;
    };
};