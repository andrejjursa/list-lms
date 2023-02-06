{extends file='layouts/backend.tpl'}
{block main_content}
    {include file='partials/backend_general/flash_messages.tpl' inline}
    {if $widget_types_list}
    <form action="{internal_url url='admin_dashboard/add_widget'}" method="post" id="new_widget_form_id">
        <fieldset class="basefieldset">
            <strong>{translate line='admin_dashboard_form_widget_type'}:</strong>
            <select name="widget_type" size="1">
                {html_options options=$widget_types_list}
            </select>
            <strong>{translate line='admin_dashboard_form_widget_column'}:</strong>
            <select name="widget_column" size="1">
                {for $i = 1 to $columns}
                    <option value="{$i}">{$i}</option>
                {/for}
            </select>
            <input type="submit" value="{translate line='admin_dashboard_button_add_widget'}" class="button" />
        </fieldset>
    </form>
    {/if}
    <div class="widget_columns_count_{$columns}">
        {for $column = 1 to $columns}
        <div class="widget_column_{$column} widget_column">
            {foreach $widget_list[$column] as $widget_id}
                <div id="widget_container_{$widget_id}" class="widget_container widget_id:{$widget_id}">{translate line='admin_dashboard_message_loading_widget'}</div>
            {/foreach}
            <div class="widget_column_sizer"></div>
        </div>
        {/for}
        <div class="widget_clear_columns"></div>
    </div>
    <form action="{internal_url url='admin_dashboard/set_columns'}" method="post">
        <fieldset class="basefieldset">
            <strong>{translate line='admin_dashboard_form_columns_count'}:</strong>
            <select name="widget_columns" size="1">{$columns_opts = [1 => 1, 2 => 2, 3 => 3, 4 => 4]}
                {html_options options=$columns_opts selected=$columns}
            </select>
            <input type="submit" value="{translate line='admin_dashboard_button_set_columns'}" class="button" />
        </fieldset>
    </form>
{/block}
{block custom_head}<script type="text/javascript">
    var widget_list = {$widget_list|json_encode};
    var link_back = '{internal_url|addslashes url='admin_dashboard'}';
    var columns = {$columns|intval};
    var messages = {
        delete_widget: '{translate|addslashes line='admin_dashboard_messages_delete_widget_question'}',
        widget_loading: '{translate|addslashes line='admin_dashboard_message_loading_widget'}'
    };
</script>{/block}