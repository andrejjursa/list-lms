{extends file='layouts/backend_popup.tpl'}
{block title}{/block}
{block main_content}
    <h3>{translate line='admin_widget_configure_title'}</h3>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if !$no_widget_found}
        <form action="{internal_url url="admin_widget/save_configuration/{$widget_id}"}" method="post">
            <fieldset class="basefieldset">
                <legend>{$widget_type_name}</legend>
                {include file="widgets/admin/{$widget_type}/configure.tpl"}
                <div class="buttons">
                    <input type="submit" value="{translate line='admin_widget_configure_form_button_save'}" class="button" />
                </div>
            </fieldset>
        </form>
    {else}
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_widget_configure_no_widget_found' inline}
    {/if}
{/block}