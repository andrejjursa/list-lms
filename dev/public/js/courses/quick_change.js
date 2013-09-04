jQuery(document).ready(function($) {
    
    var current_url = Base64url.encode(document.URL);
    
    $('#top_courses_selector_id a').each(function() {
        var url = $(this).attr('href');
        url = url.replace('-CURRENT_URL-', current_url);
        $(this).attr('href', url);
    });
    
    $('#top_courses_selector_id').menu().css({
        'position': 'absolute'
    }).hide();
    
    $('#active_course_id').mouseover(function() {
        $('#top_courses_selector_id').show();
    }).mouseout(function() {
        $('#top_courses_selector_id').hide();
    });
    
});