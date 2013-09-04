jQuery(document).ready(function($) {
    
    $('a.button.upload_avatar').click(function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.fancybox(url, {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            beforeClose: function() {
                window.location = document.URL;
                return true;
            }
        });
    });
    
    $('a.button.delete_avatar').click(function(event) {
        if (!confirm(messages.delete_avatar)) {
            event.preventDefault();
        }
    });
    
});