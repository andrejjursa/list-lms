jQuery(document).ready(function($) {
    setInterval(function() {
        $('span.mceEditor table.mceLayout[id!=mce_fullscreen_tbl]').css('width', '99%');
    }, 1000);
});