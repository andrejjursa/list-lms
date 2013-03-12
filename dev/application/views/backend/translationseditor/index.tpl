{extends file='layouts/backend.tpl'}
{block title}{translate line='adminmenu_title_translations_editor'}{/block}
{block main_content}
    <h2>{translate line='adminmenu_title_translations_editor'}</h2>
    <input type="button" name="button_new" value="{translate line='admin_translationseditor_new_translation_button_text'}" />
    <table style="min-width: 100%;" id="translations_table">
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
            {include file='backend/translationseditor/table_body.tpl' inline}
        </tbody>
    </table>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        error: {
            operation_failed: '{translate line="admin_translationseditor_javascript_message_error_operation_failed"}'
        },
        working: '{translate line="admin_translationseditor_javascript_message_working"}',
        delete_question: '{translate line="admin_translationseditor_javascript_message_delete_question"}'
    };
</script>{/block}