{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_participants_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_participants_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_participants_fieldset_legend_add_participant'}</legend>
        <form action="{internal_url url='admin_participants/add_participant'}" method="post" id="add_participant_form_id">
            {include file='backend/participants/add_participant_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_participants_fieldset_legend_all_participants'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_participants/table_content'}" method="post" id="filter_form_id">
                <div class="field">
                    <label>{translate line='admin_participants_filter_label_student_fullname'}:</label>
                    <p class="input"><input type="text" name="filter[student_fullname]" value="{$filter.student_fullname|escape:'html'}" /></p>
                </div>
                <div class="field">
                    <label>{translate line='admin_participants_filter_label_course'}:</label>
                    <p class="input"><select name="filter[course]" size="1">{list_html_options options=$courses selected=$filter.course|intval}</select></p>
                </div>
                <div class="field group_field" style="display: none;">
                    <label>{translate line='admin_participants_filter_label_group'}:</label>
                    <p class="input"><select name="filter[group]" size="1" id="filter_group_id"></select></p>
                </div>
                <div class="group_field_else">
                    <input type="hidden" name="filter[group]" value="" />
                </div>
                <div class="field">
                    <label>{translate line='admin_participants_filter_label_group_set'}:</label>
                    <p class="input"><input type="radio" name="filter[group_set]" value="all" id="filter_group_set_all_id"{if $filter.group_set eq 'all' or empty($filter.group_set)} checked="checked"{/if} /> <label for="filter_group_set_all_id">{translate line='admin_participants_filter_label_group_set_all'}</label></p>
                    <p class="input"><input type="radio" name="filter[group_set]" value="none" id="filter_group_set_none_id"{if $filter.group_set eq 'none'} checked="checked"{/if} /> <label for="filter_group_set_none_id">{translate line='admin_participants_filter_label_group_set_none'}</label></p>
                    <p class="input"><input type="radio" name="filter[group_set]" value="assigned" id="filter_group_set_assigned_id"{if $filter.group_set eq 'assigned'} checked="checked"{/if} /> <label for="filter_group_set_assigned_id">{translate line='admin_participants_filter_label_group_set_assigned'}</label></p>
                </div>
                <div class="buttons">
                    <input type="submit" name="submit_button" value="{translate line='admin_participants_filter_button_submit'}" class="button" />
                    <input type="hidden" name="filter[page]" value="{$filter.page|default:1|intval}" />
                    <input type="hidden" name="filter[rows_per_page]" value="{$filter.rows_per_page|default:25|intval}" />
                    <input type="hidden" name="filter_selected_group_id" value="{$filter.group|intval}" />
                </div>
            </form>
        </div>
        <table class="participants_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{translate line='admin_participants_table_header_student_name_email'}</th>
                    <th>{translate line='admin_participants_table_header_course_name'}</th>
                    <th>{translate line='admin_participants_table_header_group_name'}</th>
                    <th>{translate line='admin_participants_table_header_allowed_status'}</th>
                    <th class="controlls" colspan="7">{translate line='admin_participants_table_header_controlls'}</th>
                </tr>
            </thead>
            <tfoot id="table_pagination_footer_id"></tfoot>
            <tbody id="table_content_id"></tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var students_cache = {ldelim}{rdelim};
    var messages = {
        disapprove_question: '{translate line='admin_participants_javascript_question_disapprove'}',
        delete_question: '{translate line='admin_participants_javascript_question_delete'}'
    };
</script>{/block}