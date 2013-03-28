{extends file="layouts/backend.tpl"}
{block title}{translate line='admin_courses_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_courses_page_title'}</h2>
    {include file='partials/backend_general/flash_messages.tpl'}
    <fieldset>
        <legend>{translate line='admin_courses_fieldset_legend_new_course'}</legend>
        <form action="{internal_url url='admin_courses/create'}" method="post" id="new_course_form_id">
            {include file='backend/courses/new_course_form.tpl' inline}
        </form>
    </fieldset>
    <fieldset>
        <legend>{translate line='admin_courses_fieldset_legend_all_courses'}</legend>
        <table class="courses_table">
            <thead>
                <tr>
                    <th>{translate line='admin_courses_table_header_course_name'}</th>
                    <th>{translate line='admin_courses_table_header_course_period'}</th>
                    <th colspan="2" class="controlls">{translate line='admin_courses_table_header_controlls'}</th>
                </tr>
            </thead>
            <tbody id="table_content">
            </tbody>
        </table>
    </fieldset>
{/block}
{block custom_head}<script type="text/javascript">
    var messages = {
        delete_question: '{translate line="admin_courses_message_delete_question"}'
    };
</script>{/block}