jQuery(document).ready(function($) { 
    var config = {
        plugins: [
            "advlist autolink link image lists charmap preview hr anchor pagebreak autoresize",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons textcolor paste textcolor"
        ],

        toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect highlights",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | fullscreen | ltr rtl | visualchars visualblocks nonbreaking pagebreak",

        menubar: false,
        toolbar_items_size: 'small',
        entity_encoding: 'raw',
        document_base_url: global_base_url,
        convert_urls: false,
        relative_urls: false,
        resize: false,
        autoresize_max_height: 400,
        autoresize_min_height: 150
    };
    
    if (typeof highlighters !== 'undefined') {
        var menuitems = [];
        for(var i in highlighters) {
            var item = {
                text: highlighters[i].name,
                onclick: function() { tinymce_switch_highlight(editor, 'lang-' + highlighters[i].lang); }
            };
            menuitems.push(item);
        }
        config.setup = function(editor) {
            editor.addButton('highlights', {
                type: 'menubutton',
                text: 'Code highlighting',
                icon: false,
                menu: menuitems
            });
        };
    }
    
    $('textarea.tinymce').tinymce(config);
});