jQuery(document).ready(function($) { 
    var config = {
        script_url : global_base_url + 'public/js/tinymce/tiny_mce.js',
        theme: 'advanced',
        plugins : 'autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',
        theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
        theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
        theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
        theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
        theme_advanced_toolbar_location : 'top',
        theme_advanced_toolbar_align : 'left',
        theme_advanced_statusbar_location : 'bottom',
        entity_encoding: 'raw',
        document_base_url: global_base_url,
        relative_urls: false,

        style_formats: []
    };
    
    if (typeof highlighters !== 'undefined') {
        for(var i in highlighters) {
            var item = {
                title: 'Prettify - ' + highlighters[i].name,
                selector: 'pre',
                attributes: {
                    'class': 'prettyprint lang-' + highlighters[i].lang
                }
            };
            config.style_formats.push(item);
        }
    }
    
    $('textarea.tinymce').tinymce(config);
});