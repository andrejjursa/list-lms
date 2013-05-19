{foreach $courses as $course}
<tr>
    <td>{$course->id|intval}</td>
    <td>{translate_text|escape:'html' text=$course->name}</td>
    <td><div class="course_description">{overlay table='courses' table_id=$course->id column='description' default=$course->description}</div></td>
    <td>{translate_text|default:{translate line='admin_courses_table_content_no_period'}|escape:'html' text=$course->period_name}</td>
    <td>{$course->group_count}</td>
    <td>{$course->task_set_type_count}</td>
    <td class="controlls"><a href="{internal_url url="admin_courses/task_set_types/course_id/{$course->id}"}" class="button special task_set_types_editor">{translate line='admin_courses_table_controlls_task_set_types'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_courses/edit/course_id/{$course->id}"}" class="button edit">{translate line='admin_courses_table_controlls_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_courses/delete/course_id/{$course->id}"}" class="button delete">{translate line='admin_courses_table_controlls_delete'}</a></td>
</tr>
{foreachelse}
<tr>
    <td colspan="9">
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_table_content_no_courses'}
    </td>
</tr>
{/foreach}