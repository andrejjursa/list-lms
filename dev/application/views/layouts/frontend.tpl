<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link type="text/css" rel="stylesheet" media="screen" href="{'/public/css/frontend_general.css'|base_url}" />
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
    </head>
    <body>
        {include file='partials/frontend_general/pagemenu.tpl' inline}
        <div id="mainwrap">
            <div id="leftwrap">
                {make_pagemenu menu=$list_pagemenu current=$list_pagemenu_current}
            </div>
            <div id="rightwrap">{block name='main_content'}{/block}</div>
        </div>
    </body>
</html>