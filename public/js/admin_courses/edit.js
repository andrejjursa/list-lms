jQuery(document).ready(function($) {
    $('form').formErrorWarning();

    var store_json_data = function(links) {
        $('form input[name=\'course[additional_menu_links]\']').val(JSON.stringify(links).split('"').join('\\"'));
    };

    var rebuild_additional_links = function () {
        var links = [];
        $('#additional_links li').each(function () {
            var href = $(this).attr('data-href');
            var text = $(this).attr('data-text');
            links.push({
                'href': href,
                'text': text
            });
        });
        store_json_data(links);
    };

    var disable_all_delete_buttons = function () {
        $('#additional_links a.button.delete').addClass('disabled');
        $('#additional_links a.button.delete').attr('disabled', 'disabled');
    };

    var enable_all_delete_buttons = function() {
        $('#additional_links a.button.delete').removeClass('disabled');
        $('#additional_links a.button.delete').removeAttr('disabled');
    };

    var disable_add_additional_link = function () {
        $('#add_additional_link').addClass('disabled');
        $('#add_additional_link').attr('disabled', 'disabled');
        $('#new_additional_menu_link_href_id').attr('disabled', 'disabled');
        $('#new_additional_menu_link_name_id').attr('disabled', 'disabled');

        $('#additional_links').sortable('disable');
    };

    var enable_add_additional_link = function() {
        $('#add_additional_link').removeClass('disabled');
        $('#add_additional_link').removeAttr('disabled');
        $('#new_additional_menu_link_href_id').removeAttr('disabled');
        $('#new_additional_menu_link_name_id').removeAttr('disabled');
    };

    var enable_buttons_if_possible = function () {
        if ($('#additional_links a.button.cancel').is(':visible')) { return; }

        enable_all_delete_buttons();
        enable_add_additional_link();
        $('#additional_links').sortable('enable');
    };

    var get_json_data = function() {
        var jsondata = $('form input[name=\'course[additional_menu_links]\']').val();
        jsondata = jsondata.split('\\"').join('"');
        jsondata = JSON.parse(jsondata);

        return jsondata;
    };

    var delete_link = function(deleteButton, link, container) {
        if (deleteButton.hasClass('disabled')) { return; }
        disable_all_delete_buttons();
        container.sortable('disable');
        link.remove();
        rebuild_additional_links();
        load_additional_links();
    };


    var add_additional_link = function() {
        if ($('#add_additional_link').hasClass('disabled')) { return; }

        var href = $('#new_additional_menu_link_href_id').val();
        var text = $('#new_additional_menu_link_name_id').val();

        var errors = 0;

        $('#new_additional_menu_link_href_id').removeClass('exists');
        $('#additional_links li').removeClass('exists');

        if (text.trim() === '') {
            $('#new_additional_menu_link_name_id').addClass('error');
            errors++;
        } else {
            $('#new_additional_menu_link_name_id').removeClass('error');
        }

        if (!ValidURL(href)) {
            $('#new_additional_menu_link_href_id').addClass('error');
            errors++;
        } else {
            $('#new_additional_menu_link_href_id').removeClass('error');
        }

        if (errors > 0) { return; }

        var jsondata = get_json_data();

        for (var index in jsondata) {
            if (jsondata[index].href === href) {
                $('#new_additional_menu_link_href_id').addClass('exists');
                $('#additional_links li').each(function () {
                    if ($(this).attr('data-href') === href) {
                        $(this).addClass('exists');
                    }
                });
                return;
            }
        }

        jsondata.push({
            href: href,
            text: text
        });

        $('#new_additional_menu_link_href_id').val('');
        $('#new_additional_menu_link_name_id').val('');

        store_json_data(jsondata);
        load_additional_links();
    };

    var update_link = function(link, textInput, hrefInput) {
        var text = textInput.val();
        var href = hrefInput.val();

        var errors = 0;

        hrefInput.removeClass('exists');
        $('#additional_links li').removeClass('exists');

        if (text.trim() === '') {
            textInput.addClass('error');
            errors++;
        } else {
            textInput.removeClass('error');
        }

        if (!ValidURL(href)) {
            hrefInput.addClass('error');
            errors++;
        } else {
            hrefInput.removeClass('error');
        }

        if (errors > 0) { return false; }

        var duplicate = false;

        $('#additional_links li').each(function () {
            if ($(this).attr('data-index') != link.attr('data-index') && $(this).attr('data-href') === href) {
                hrefInput.addClass('exists');
                $('#additional_links li').each(function () {
                    if ($(this).attr('data-href') === href) {
                        $(this).addClass('exists');
                    }
                });
                duplicate = true;
            }
        });

        if (duplicate) { return false; }

        link.attr('data-text', text);
        link.attr('data-href', href);

        var link_href = link.find('span.link_href');
        link_href.appendTo(link);

        link_href.text('(' + href + ')');

        var link_text = link.find('span.link_text');
        link_text.text(text);

        link_href.appendTo(link_text);

        return true;
    };

    var make_link_markup = function(container, jsondata, index) {
        var link = $('<li>');
        link.attr('data-href', jsondata[index].href);
        link.attr('data-text', jsondata[index].text);
        link.attr('data-index', index);
        link.appendTo(container);

        var text = $('<span>');
        text.addClass('link_text');
        text.text(jsondata[index].text);
        text.appendTo(link);

        var href= $('<span>');
        href.addClass('link_href');
        href.text('(' + jsondata[index].href + ')');
        href.appendTo(text);

        var editHref = $('<input>');
        editHref.hide();
        editHref.attr('type', 'text');
        editHref.val(jsondata[index].href);
        editHref.attr('placeholder', inputs.href_placeholder);
        editHref.css('width', '200px');
        editHref.appendTo(link);

        var editText = $('<input>');
        editText.hide();
        editText.attr('type', 'text');
        editText.val(jsondata[index].text);
        editText.attr('placeholder', inputs.text_placeholder);
        editText.css('width', '200px');
        editText.appendTo(link);

        var deleteButton = $('<a>');

        var updateBuddon = $('<a>');
        var cancelButton = $('<a>');

        var editButton = $('<a>');
        editButton.addClass('button');
        editButton.addClass('edit');
        editButton.attr('href', 'javascript:void(0);');
        editButton.text(buttons.edit_link);
        editButton.click(function (event) {
            event.preventDefault();
            disable_all_delete_buttons();
            disable_add_additional_link();
            text.hide();
            editText.show();
            editHref.show();
            deleteButton.hide();
            editButton.hide();
            updateBuddon.show();
            cancelButton.show();
            $('#additional_links li').removeClass('exists');
            editHref.removeClass('exists');
        });
        editButton.appendTo(link);

        updateBuddon.hide();
        updateBuddon.addClass('button');
        updateBuddon.addClass('update');
        updateBuddon.attr('href', 'javascript:void(0);');
        updateBuddon.text(buttons.update_link);
        updateBuddon.click(function (event) {
            event.preventDefault();

            if (update_link(link, editText, editHref)) {
                rebuild_additional_links();
                cancelButton.click();
            }
        });
        updateBuddon.appendTo(link);

        cancelButton.hide();
        cancelButton.addClass('button');
        cancelButton.addClass('cancel');
        cancelButton.addClass('special');
        cancelButton.attr('href', 'javascript:void(0);');
        cancelButton.text(buttons.cancel_update_link);
        cancelButton.click(function (event) {
            event.preventDefault();

            text.show();
            editText.hide();
            editHref.hide();
            editHref.val(link.attr('data-href'));
            editText.val(link.attr('data-text'));
            deleteButton.show();
            editButton.show();
            updateBuddon.hide();
            cancelButton.hide();
            $('#additional_links li').removeClass('exists');
            editHref.removeClass('exists');

            enable_buttons_if_possible();
        });
        cancelButton.appendTo(link);

        deleteButton.addClass('button');
        deleteButton.addClass('delete');
        deleteButton.attr('href', 'javascript:void(0);');
        deleteButton.text(buttons.delete_link);
        deleteButton.click(function (event) {
            event.preventDefault();
            delete_link(deleteButton, link, container);
        });
        deleteButton.appendTo(link);
    };

    var load_additional_links = function() {
        var jsondata = get_json_data();

        var container = $('#additional_links');
        container.html('');

        for (var index in jsondata) {
            make_link_markup(container, jsondata, index);
        }

        container.sortable({
            update: function () {
                rebuild_additional_links();
                load_additional_links();
            }
        });
    };

    $('#add_additional_link').click(function (event) {
        event.preventDefault();
        add_additional_link();
    });

    load_additional_links();

    function ValidURL(str) {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-\\[\\]]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locater
        if(!pattern.test(str)) {
            return false;
        } else {
            return true;
        }
    }
});