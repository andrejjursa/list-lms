jQuery(document).ready(function($) {
    
    $('#top_courses_selector_id').menu().css({
        'position': 'absolute'
    }).hide();
    
    $('#active_course_id').mouseover(function() {
        $('#top_courses_selector_id').show();
    }).mouseout(function() {
        $('#top_courses_selector_id').hide();
    });
    
});