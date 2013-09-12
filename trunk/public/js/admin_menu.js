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
    $('a.adminmenu_logout').click(function(event) {
        if (!confirm(logout_question_text)) {
            event.preventDefault();
        }
    });
    
    $('#teacher_quick_langmenu').menu();
    
    $('div.teacher_quick_langmenu').mouseover(function() {
        $('#teacher_quick_langmenu').show();
    }).mouseout(function() {
        $('#teacher_quick_langmenu').hide();
    });
    
    $('#teacher_quick_prefered_course_menu').menu();
    
    $('div.teacher_quick_prefered_course_menu').mouseover(function() {
        $('#teacher_quick_prefered_course_menu').show();
    }).mouseout(function() {
        $('#teacher_quick_prefered_course_menu').hide();
    });
});