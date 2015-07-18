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
                effectSpeedOpen : 100,
                effectSpeedClose : 100,
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
    $('#jMenu a.manual_index').click(function(event) {
        event.preventDefault();
        var url = jQuery(this).attr('href');
        window.open(url, '_blank', 'channelmode=no, directories=no, fullscreen=no, height=768, left=0, location=no, menubar=no, resizable=yes, scrollbars=yes, status=yes, titlebar=yes, toolbar=no, top=0, width=1024');
    });
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