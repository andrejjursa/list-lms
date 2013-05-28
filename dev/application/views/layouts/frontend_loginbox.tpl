<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>{capture assign="list_title" name="list_title_cpt"}{block name='title'}{/block}{/capture}L.I.S.T.{if $list_title} - {$list_title}{/if}</title>
        <link type="text/css" rel="stylesheet" media="screen" href="{'/public/css/frontend_general.css'|base_url}" />
        <script type="text/javascript">
            var global_base_url = "{'/'|base_url}";
            var login_form_url = '{internal_url url='students/login/current_url/###URL###'}';
        </script>
        {foreach $list_internal_css_files as $file}{$file.html}{/foreach}
        {foreach $list_internal_js_files as $file}{$file.html}{/foreach}
    </head>
    <body>
        <div id="top_meta_informations">
            <div class="left">{include file='partials/frontend_general/selected_course.tpl' inline}</div>
            <div class="right">{include file='partials/frontend_general/student_panel.tpl' inline}</div>
            <div class="clear"></div>
        </div>
        <div id="mainwrap">
            <div id="loginboxwrap">
            <div class="internal_padding">{block name='main_content'}{/block}</div></div>
        </div>
    </body>
</html>