{extends file='layouts/backend.tpl'}
{block title}{translate line='adminmenu_title_translations_editor'}{/block}
{block main_content}
    <h2>{translate line='adminmenu_title_translations_editor'}</h2>
    <table style="min-width: 100%;">
        <thead>
            <tr>
                <th>{translate line='admin_translationseditor_table_header_constant'}</th>
                {foreach $languages as $language}
                <th>{$language}<br /><small>[{$language@key}]</small></th>
                {/foreach}
                <th>{translate line='admin_translationseditor_table_header_controlls'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $translations as $constant => $translation}
                <tr class="constant_{$constant}">
                {include file='backend/translationseditor/table_row.tpl'}
                </tr>
            {/foreach}
        </tbody>
    </table>
{/block}