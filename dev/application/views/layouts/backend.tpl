<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html" charset="utf-8" />
        <title>{block name='title'}{/block}</title>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
    </head>
    {include file='partials/backend_general/adminmenu.tpl' inline}
    <body>
        <h1>A D M I N I S T A T I O N</h1>
        <nav>{make_adminmenu menu=$list_adminmenu current=$list_adminmenu_current}</nav>
        {block name='main_content'}{/block}
    </body>
</html>