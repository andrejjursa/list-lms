<table class="course_content_table">
    <thead>

    </thead>
    <tbody>
        {foreach $course_content as $content}
        <tr>
            <td>{$content->id}</td>
            <td>{overlay table='course_content' table_id=$content->id column='title' default=$content->title}</td>
            <td>{$content->published}</td>
            <td>{translate_text text=$content->course_period_name} {translate_text text=$content->course_name}</td>
        </tr>
        {/foreach}
    </tbody>
    <tfoot>

    </tfoot>

</table>
