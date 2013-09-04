<table class="periods_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th>{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th>{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th>{translate line='admin_periods_table_header_name'}</th>{/if}
            {if $filter.fields.related_courses}<th>{translate line='admin_periods_table_header_relations_courses'}</th>{/if}
            <th colspan="4" class="controlls"><div id="open_fields_config_id">{translate line='admin_periods_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $periods as $period}
            <tr>
                <td>{$period->id|intval}</td>
                {if $filter.fields.created}<td>{$period->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
                {if $filter.fields.updated}<td>{$period->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
                {if $filter.fields.name}<td>{translate_text|escape:'html' text=$period->name}</td>{/if}
                {if $filter.fields.related_courses}<td>{$period->course_count}</td>{/if}
                <td class="controlls"><a href="{internal_url url="admin_periods/edit/period_id/{$period->id}"}" class="button button_edit">{translate line='admin_periods_table_button_edit'}</a></td>
                <td class="controlls"><a href="{internal_url url="admin_periods/delete/period_id/{$period->id}"}" class="button button_delete delete">{translate line='admin_periods_table_button_delete'}</a></td>
                <td class="controlls">{if !$period@first}<a href="{internal_url url="admin_periods/move_up/period_id/{$period->id}"}" class="button button_up special">{translate line='admin_periods_table_button_up'}</a>{/if}</td>
                <td class="controlls">{if !$period@last}<a href="{internal_url url="admin_periods/move_down/period_id/{$period->id}"}" class="button button_down special">{translate line='admin_periods_table_button_down'}</a>{/if}</td>
            </tr>
        {/foreach}
    </tbody>
</table>