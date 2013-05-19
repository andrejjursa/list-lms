{foreach $periods as $period}
    <tr>
        <td>{$period->id|intval}</td>
        <td>{translate_text|escape:'html' text=$period->name}</td>
        <td>{$period->course_count}</td>
        <td class="controlls"><a href="{internal_url url="admin_periods/edit/period_id/{$period->id}"}" class="button button_edit">{translate line='admin_periods_table_button_edit'}</a></td>
        <td class="controlls"><a href="{internal_url url="admin_periods/delete/period_id/{$period->id}"}" class="button button_delete delete">{translate line='admin_periods_table_button_delete'}</a></td>
        <td class="controlls">{if !$period@first}<a href="{internal_url url="admin_periods/move_up/period_id/{$period->id}"}" class="button button_up special">{translate line='admin_periods_table_button_up'}</a>{/if}</td>
        <td class="controlls">{if !$period@last}<a href="{internal_url url="admin_periods/move_down/period_id/{$period->id}"}" class="button button_down special">{translate line='admin_periods_table_button_down'}</a>{/if}</td>
    </tr>
{/foreach}