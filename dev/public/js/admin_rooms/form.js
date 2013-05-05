jQuery(document).ready(function($) {
    $('#room_time_begin_id').timepicker({
        showSecond: true,
        timeFormat: 'HH:mm:ss'
    });
    $('#room_time_end_id').timepicker({
        showSecond: true,
        timeFormat: 'HH:mm:ss'
    });
    translation_selector('#room_name_id');
});