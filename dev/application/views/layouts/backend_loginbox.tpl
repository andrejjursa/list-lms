<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, follow" />
        <title>{block name='title'}{/block}</title>
        <link href="{'/public/css/admin_general.css'|base_url}" rel="stylesheet" type="text/css" media="screen" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var login_form_url = "{internal_url url='admin_teachers/login/current_url/###URL###'}";
        </script>
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url}"></script>
        <script type="text/javascript" src="{'/public/js/api.js'|base_url}"></script>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
    </head>
    <body>
        <div class="loginbox">{block name='main_content'}{/block}</div>
    </body>
</html>