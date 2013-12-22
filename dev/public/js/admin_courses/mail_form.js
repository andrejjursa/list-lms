jQuery(document).ready(function($) {
    
    $('form').formErrorWarning();
    
    $('textarea.tinymce').tinymce({
        plugins: [
            "advlist autolink link image lists charmap preview hr anchor pagebreak autoresize",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons textcolor paste textcolor"
        ],

        toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
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
    });
        
    $('a.button.switch_students_off').click(function(event) {
        event.preventDefault();
        var target = find_target($(this).attr('class'));
        if (target !== null) {
            $('input[type=checkbox].'+ target).prop('checked', false);
        }
    });
    
    $('a.button.switch_students_on').click(function(event) {
        event.preventDefault();
        var target = find_target($(this).attr('class'));
        if (target !== null) {
            $('input[type=checkbox].' + target).prop('checked', true);
        }
    });
    
    var find_target = function(classes) {
        var splited = classes.split(' ');
        
        for (var i in splited) {
            if (splited[i].substr(0, 7) === 'target:') {
                return splited[i].substr(7);
            }
        }
        
        return null;
    };
    
});