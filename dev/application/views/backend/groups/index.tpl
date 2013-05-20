{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_groups_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_groups_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl' inline}
    <fieldset>
        <legend>{translate line='admin_groups_fieldset_legend_new_group'}</legend>
        <form action="{internal_url url='admin_groups/create'}" method="post" id="groups_form_id">
            {include file='backend/groups/new_group_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_groups_fieldset_legend_all_groups'}</legend>
        <div class="filter_wrap">
            <form action="{internal_url url='admin_groups/get_table_content'}" method="post" id="filter_form_id">
                <div class="field">
                    <label for="filter_course_id_id">{translate line='admin_groups_filter_by_course'}:</label>
                    <p class="input">
                        <select name="filter[course_id]" size="1" id="filter_course_id_id">{list_html_options options=$courses selected=$filter.course_id}</select>
                    </p>
                </div>
                <div class="buttons">
                    <input type="submit" name="filter_submit" value="{translate line='admin_groups_filter_submit_button'}" class="button" />
                </div>
            </form>
        </div>
        <table class="groups_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{translate line='admin_groups_table_header_group_name'}</th>
                    <th>{translate line='admin_groups_table_header_group_course'}</th>
                    <th>{translate line='admin_groups_table_header_group_rooms'}</th>
                    <th>{translate line='admin_groups_table_header_group_capacity'}</th>
                    <th colspan="3" class="controlls">{translate line='admin_groups_table_header_controlls'}</th>
                </tr>
            </thead>
            <tbody id="table_of_groups_container_id">
            </tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_groups_delete_period_question"}',
        after_delete: '{translate line="admin_groups_message_after_delete"}'
    };
</script>{/block}