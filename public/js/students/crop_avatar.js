jQuery(document).ready(function($) {
    
    var update_coords = function(coords) {
        $('input[type=hidden][name="crop[x]"]').val(coords.x);
        $('input[type=hidden][name="crop[y]"]').val(coords.y);
        $('input[type=hidden][name="crop[width]"]').val(coords.w);
        $('input[type=hidden][name="crop[height]"]').val(coords.h);
    };
    
    var reset_coords = function() {
        $('input[type=hidden][name^=crop]').val('');
    };
    
    $('#cropbox_id').Jcrop({
        onChange: update_coords,
        onSelect: update_coords,
        onRelease: reset_coords,
        bgOpacity: .2,
        aspectRatio: 2 / 3
    });
    
    $('form').submit(function(event) {
        var can_submit = true;
        
        $('input[type=hidden][name^=crop]').each(function(){
            if ($(this).val() === '') {
                can_submit = false;
            }
        });
        
        if (!can_submit) {
            event.preventDefault();
            show_notification(nothing_selected_notification, 'error');
        }
    });
    
});