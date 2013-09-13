<table class="courses_table">
    <thead>
        <tr>
            <th>ID</th>
            {if $filter.fields.created}<th class="sort:created">{translate line='common_table_header_created'}</th>{/if}
            {if $filter.fields.updated}<th class="sort:updated">{translate line='common_table_header_updated'}</th>{/if}
            {if $filter.fields.name}<th class="sort:name">{translate line='admin_courses_table_header_course_name'}</th>{/if}
            {if $filter.fields.description}<th>{translate line='admin_courses_table_header_course_description'}</th>{/if}
            {if $filter.fields.period}<th class="sort:period">{translate line='admin_courses_table_header_course_period'}</th>{/if}
            {if $filter.fields.groups}<th class="sort:groups:desc">{translate line='admin_courses_table_header_course_groups'}</th>{/if}
            {if $filter.fields.task_set_types}<th class="sort:task_set_types:desc">{translate line='admin_courses_table_header_course_task_set_types'}</th>{/if}
            {if $filter.fields.capacity}<th class="sort:capacity:desc">{translate line='admin_courses_table_header_course_capacity'}</th>{/if}
            <th colspan="4" class="controlls"><div id="open_fields_config_id">{translate line='admin_courses_table_header_controlls'}</div>{include file='partials/backend_general/fields_filter.tpl' fields=$filter.fields inline}</th>
        </tr>
    </thead>
    <tbody>
        {foreach $courses as $course}
        <tr>
            <td>{$course->id|intval}</td>
            {if $filter.fields.created}<td>{$course->created|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.updated}<td>{$course->updated|date_format:{translate line='common_datetime_format'}}</td>{/if}
            {if $filter.fields.name}<td>{translate_text|escape:'html' text=$course->name}</td>{/if}
            {if $filter.fields.description}<td><div class="course_description">{overlay|add_base_url table='courses' table_id=$course->id column='description' default=$course->description}</div></td>{/if}
            {if $filter.fields.period}<td>{translate_text|default:{translate line='admin_courses_table_content_no_period'}|escape:'html' text=$course->period_name}</td>{/if}
            {if $filter.fields.groups}<td>{$course->group_count}</td>{/if}
            {if $filter.fields.task_set_types}<td>{$course->task_set_type_count}</td>{/if}
            {if $filter.fields.capacity}<td>{$course->capacity|intval}</td>{/if}
            <td class="controlls"><a href="{internal_url url="admin_courses/task_set_types/course_id/{$course->id}"}" class="button special task_set_types_editor">{translate line='admin_courses_table_controlls_task_set_types'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_courses/edit/course_id/{$course->id}"}" class="button edit">{translate line='admin_courses_table_controlls_edit'}</a></td>
            <td class="controlls"><a href="{internal_url url="admin_courses/delete/course_id/{$course->id}"}" class="button delete">{translate line='admin_courses_table_controlls_delete'}</a></td>
            <td class="controlls"><a href="{internal_url url="courses/show_details/{$course->id}/{$this->lang->get_current_idiom()}"}" target="_blank" class="button special" title="{translate|escape:'html' line='admin_courses_table_controlls_details_hint'}">{translate line='admin_courses_table_controlls_details'}</a></td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="10">
                {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_table_content_no_courses'}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>