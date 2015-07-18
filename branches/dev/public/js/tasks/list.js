jQuery(document).ready(function($) {
    
    $(document).on('click', 'a.click_enlarge_comment', function(event) {
        event.preventDefault();
        var comment_id = $(this).attr('for');
        console.log(comment_id);
        var data_html = $('#' + comment_id).html();
        var title_html = $('#' + comment_id).attr('title');
        console.log(data_html);
        $.fancybox({
            type: 'inline',
            content: data_html,
            title: title_html,
            minWidth: '200',
            minHeight: '0',
            maxWidth: '500',
            maxHeight: '600',
            openEffect: 'elastic',
            closeEffect: 'elastic',
            orig: $(this)
        });
    });
    
});