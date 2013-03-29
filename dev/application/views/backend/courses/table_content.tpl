{foreach $courses as $course}
<tr>
    <td>{translate_text|escape:'html' text=$course->name}</td>
    <td>{overlay|escape:'html' table='courses' table_id=$course->id column='description' default=$course->description}</td>
    <td>{translate_text|default:{translate line='admin_courses_table_content_no_period'}|escape:'html' text=$course->period->get()->name}</td>
    <td class="controlls"><a href="{internal_url url="admin_courses/edit/course_id/{$course->id}"}" class="button edit">{translate line='admin_courses_table_controlls_edit'}</a></td>
    <td class="controlls"><a href="{internal_url url="admin_courses/delete/course_id/{$course->id}"}" class="button delete">{translate line='admin_courses_table_controlls_delete'}</a></td>
</tr>
{foreachelse}
<tr>
    <td colspan="4">
        {include file='partials/backend_general/error_box.tpl' message='lang:admin_courses_table_content_no_courses'}
    </td>
</tr>
{/foreach}