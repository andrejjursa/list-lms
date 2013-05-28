jQuery(document).ready(function($) {
    var loginbox = $('body > #mainwrap > #loginboxwarp > div.internal_padding > #loginbox');
    if (loginbox.length == 0) {
        var window_url = document.URL;
        var url = login_form_url;
        url = url.replace('###URL###', Base64url.encode(window_url));
        window.location = url;
    }
});