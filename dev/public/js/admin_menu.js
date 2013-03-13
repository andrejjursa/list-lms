jQuery(document).ready(function($){
    var menuExists = false;
    $('#jMenu').each(function(){
        menuExists = true;
    });
    if (menuExists) {
        $('#jMenu').jMenu({
            openClick : false,
            ulWidth : 'auto',
            effects : {
                effectSpeedOpen : 150,
                effectSpeedClose : 150,
                effectTypeOpen : 'slide',
                effectTypeClose : 'slide',
                effectOpen : 'linear',
                effectClose : 'linear'
            },
            TimeBeforeOpening : 100,
            TimeBeforeClosing : 11,
            animatedText : true,
            paddingLeft: 5
        });
    }
    $('nav a.adminmenu_logout').click(function(event) {
        if (!confirm(logout_question_text)) {
            event.preventDefault();
        }
    });
});