jQuery(document).ready(function($){
    $('nav#list-navigation a.manual_index').click(function(event) {
        event.preventDefault();
        var url = jQuery(this).attr('href');
        window.open(url, '_blank', 'channelmode=no, directories=no, fullscreen=no, height=768, left=0, location=no, menubar=no, resizable=yes, scrollbars=yes, status=yes, titlebar=yes, toolbar=no, top=0, width=1024');
    });

    $(document).on('click', 'a.adminmenu_logout', function(event) {
        if (!confirm(logout_question_text)) {
            event.preventDefault();
        }
    });
});