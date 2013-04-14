<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>{block name='title'}{/block}</title>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
    </head>
    <body>
        {block name='main_content'}{/block}
    </body>
</html>