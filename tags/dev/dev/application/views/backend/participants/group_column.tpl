<a href="javascript:void(0);" class="button group_column special" rel="{$participant->course_id|intval},{$participant->group_id|intval}">{translate_text text=$participant->group_name default={translate line='admin_participants_column_empty_message'}}</a>
<span class="group_column_edit" style="display: none;">
    <select name="new_group_id" size="1"></select>&nbsp;<input type="button" class="button save_new_group" value="{translate line='admin_participants_table_button_save_new_group'}" rel="{$participant->id|intval}" />
</span>