jQuery(document).ready(function($) {
    var loginbox = $('body > #mainwrap > #loginboxwrap > div.internal_padding > #loginbox');
    if (loginbox.length === 0) {
        var window_url = document.URL;
        var url = login_form_url;
        url = url.replace('###URL###', Base64url.encode(window_url));
        window.location = url;
    }
    var email_field = $('#id_students_email');
    if (email_field.length > 0) {
        email_field.focus();
    }
});