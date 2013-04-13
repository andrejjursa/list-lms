{foreach $tasks as $task}
<tr>
    <td>{$task->name|escape:'html'}</td>
    <td>{foreach $task->category->order_by('name', 'asc')->get_iterated() as $category}{if !$category@first}, {/if}{translate_text|escape:'html' text=$category->name}{/foreach}</td>
    <td>{$task->task_set->count()}</td>
    <td></td>
    <td></td>
</tr>
{/foreach}
<tr id="pagination_row_id">
    <td colspan="5">{include file='partials/backend_general/pagination.tpl' paged=$tasks->paged inline}</td>
</tr>