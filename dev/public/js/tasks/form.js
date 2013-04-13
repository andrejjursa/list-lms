jQuery(document).ready(function($) { 
    $('textarea.tinymce').tinymce({
        theme: 'modern',
        plugins: 'preview link code fullscreen table searchreplace visualblocks visualchars hr insertdatetime charmap lists nonbreaking advlist contextmenu layer',
        toolbar1: 'undo redo | cut copy paste | alignleft aligncenter alignright alignjustify',
        toolbar2: 'styleselect | bold italic underline strikethrough superscript subscript | link table hr | fullscreen preview code | forecolor backcolor | bullist numlist outdent indent',
        style_formats: [
            {title: 'Headers', items: [
                {title: 'h1', block: 'h1'},
                {title: 'h2', block: 'h2'},
                {title: 'h3', block: 'h3'},
                {title: 'h4', block: 'h4'},
                {title: 'h5', block: 'h5'},
                {title: 'h6', block: 'h6'}
            ]},
            {title: 'Blocks', items: [
                {title: 'p', block: 'p'},
                {title: 'div', block: 'div'},
                {title: 'pre', block: 'pre'},
                {title: 'code', block: 'code'}
            ]},
            {title: 'Containers', items: [
                {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
                {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
                {title: 'blockquote', block: 'blockquote', wrapper: true},
                {title: 'hgroup', block: 'hgroup', wrapper: true},
                {title: 'aside', block: 'aside', wrapper: true},
                {title: 'figure', block: 'figure', wrapper: true}
            ]},
            {title: 'Highlight', items: [
                {title: 'Java', selector: 'code', attributes: {'lang': 'java'}},
                {title: 'C++', selector: 'code', attributes: {'lang': 'cpp'}},
                {title: 'PHP', selector: 'code', attributes: {'lang': 'php'}},
                {title: 'HTML', selector: 'code', attributes: {'lang': 'html'}},
                {title: 'JavaScript', selector: 'code', attributes: {'lang': 'javascript'}},
                {title: 'CSS', selector: 'code', attributes: {'lang': 'css'}},
            ]},
        ],
    });
});