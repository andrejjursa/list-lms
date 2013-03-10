<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html" charset="utf-8" />
        <title>{block name='title'}{/block}</title>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        <script type="text/javascript" src="{'/public/js/jquery.js'|base_url}"></script>
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
        </script>
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
    </head>
    {include file='partials/backend_general/adminmenu.tpl' inline}
    <body>
        <h1>{translate line='adminmenu_administration_title'}</h1>
        <nav>{make_adminmenu menu=$list_adminmenu current=$list_adminmenu_current}</nav>
        <div class="backend_body" style="border: 1px solid black; border-radius: 5px; padding: 5px; margin: 5px 5px 0 0; box-shadow: 5px 5px 5px black;">{block name='main_content'}{/block}</div>
    </body>
</html>