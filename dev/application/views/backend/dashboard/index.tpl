{extends file='layouts/backend.tpl'}
{block main_content}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $widget_types_list}
    <form action="{internal_url url='admin_dashboard/add_widget'}" method="post">
        <fieldset class="basefieldset">
            <select name="widget_type" size="1">
                {html_options options=$widget_types_list}
            </select>
            <input type="submit" value="{translate line='admin_dashboard_button_add_widget'}" class="button" />
        </fieldset>
    </form>
    {/if}
    {foreach $widget_list as $widget_id}
        <div id="widget_container_{$widget_id}">{{translate line='admin_dashboard_message_loading_widget'}}</div>
    {/foreach}
{/block}
{block custom_head}<script type="text/javascript">
    var widget_list = {$widget_list|json_encode};
</script>{/block}