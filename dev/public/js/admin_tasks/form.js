jQuery(document).ready(function($) { 
    $('textarea.tinymce').tinymce({
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

        style_formats: [
            {title: 'Highlight - Java', selector: 'pre', attributes: { 'lang': 'java', 'class': 'highlight' }},
            {title: 'Highlight - C++', selector: 'pre', attributes: { 'lang': 'cpp', 'class': 'highlight' }},
            {title: 'Highlight - PHP', selector: 'pre', attributes: { 'lang': 'php', 'class': 'highlight' }},
            {title: 'Highlight - HTML', selector: 'pre', attributes: { 'lang': 'html', 'class': 'highlight' }},
            {title: 'Highlight - JavaScript', selector: 'pre', attributes: { 'lang': 'javascript', 'class': 'highlight' }},
            {title: 'Highlight - jQuery', selector: 'pre', attributes: { 'lang': 'jquery', 'class': 'highlight' }},
            {title: 'Highlight - CSS', selector: 'pre', attributes: { 'lang': 'css', 'class': 'highlight' }},
            {title: 'Highlight - Haskell', selector: 'pre', attributes: { 'lang': 'haskell', 'class': 'highlight' }},
        ]
    });
});