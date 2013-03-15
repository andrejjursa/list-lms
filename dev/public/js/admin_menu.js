jQuery(document).ready(function($){
    var menuExists = false;
    $('#jMenu').each(function(){
        menuExists = true;
    });
    if (menuExists) {
        $('#jMenu').jMenu({
            openClick : false,
            ulWidth : 200,
            effects : {
                effectSpeedOpen : 150,
                effectSpeedClose : 150,
                effectTypeOpen : 'show',
                effectTypeClose : 'hide',
                effectOpen : 'linear',
                effectClose : 'linear'
            },
            TimeBeforeOpening : 100,
            TimeBeforeClosing : 11,
            animatedText : false,
            paddingLeft: 5
        });
    }
    $('nav a.adminmenu_logout').click(function(event) {
        if (!confirm(logout_question_text)) {
            event.preventDefault();
        }
    });
});