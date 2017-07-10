<ul data-task-set-type-id="{$task_set_type_id}" data-course-id="{$course->id}" class="sorting_list">
    {foreach $task_set_type_data.items as $task_set}
        <li data-id="{$task_set.id}" class="sorted_item">{overlay table='task_sets' table_id=$task_set.id column='name' default=$task_set.name}</li>
    {/foreach}
</ul>