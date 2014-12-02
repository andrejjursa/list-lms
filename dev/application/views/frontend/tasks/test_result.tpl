{extends file='layouts/frontend_popup.tpl'}
{block main_content}
    {if is_object($test_queue) and $test_queue->exists()}
        <h3>{overlay table='task_sets' table_id=$test_queue->task_set_id column='name' default=$test_queue->task_set_name} / {translate_text text=$test_queue->task_set_course_name} / {translate_text text=$test_queue->task_set_course_period_name}</h3>
        
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:tasks_test_result_error_test_queue_not_found' inline}
    {/if}
{/block}