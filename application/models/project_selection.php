<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Project selection model.
 *
 * @property int      $id
 * @property string   $updated     date time format YYYY-MM-DD HH:MM:SS
 * @property string   $created     date time format YYYY-MM-DD HH:MM:SS
 * @property int|null $student_id  entity id of model {@see Student}
 * @property int|null $task_set_id entity id of model {@see Task_set}
 * @property int|null $task_id     entity id of model {@see Task}
 *
 * @method DataMapper where_related_student(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Project_selection extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'student',
        'task_set',
        'task',
    ];
    
}