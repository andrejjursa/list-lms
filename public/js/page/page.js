jQuery(document).ready(function($) {

    function set_segment(new_segment) {
        var url = window.location.href;
        if (url.indexOf('#') > 0) {
            url = url.split('#')[0];
        }
        url = url + '#' + new_segment;
        window.location.href = url;
    }

    function determine_segment() {
        var url = window.location.href;
        if (url.indexOf('#') > 0) {
            var parts = url.split('#');
            return parts[parts.length - 1];
        }
        return '';
    }

    function load_default_page() {
        var default_id = determine_segment();

        default_id = default_id === '' ? 'description' : default_id;

        load_inner_link(default_id);
    }

    function load_inner_link(id) {
        var link = $('nav a[data-link-id=' + id + ']');

        if (!link) {
            load_inner_link('description');
            return;
        }

        var url = link.attr('href');

        $('#main_content').attr('src', url);

        set_segment(id);
    }

    $('nav a[data-link-id]').click(function (event) {
        event.preventDefault();

        var id = $(this).attr('data-link-id');

        load_inner_link(id);
    });

    $('nav a.language_switch').click(function (event) {
        event.preventDefault();

        var segment = determine_segment();

        var url = $(this).attr('href');

        window.location.href = url + '#' + segment;
    });

    load_default_page();

});