<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Room model.
 *
 * @property int                 $id
 * @property string              $updated     date time format YYYY-MM-DD HH:MM:SS
 * @property string              $created     date time format YYYY-MM-DD HH:MM:SS
 * @property string              $name
 * @property int|null            $group_id    entity id of model {@see Group}
 * @property int                 $time_begin
 * @property int                 $time_end
 * @property int                 $time_day
 * @property int                 $capacity
 * @property string|null         $teachers_plain
 * @property Group               $group
 * @property Teacher             $teacher
 * @property Task_set            $task_set
 * @property Task_set_permission $task_set_permission
 *
 * @method DataMapper where_related_group(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_teacher(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set_permission(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Room extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'group',
    ];
    public $has_many = [
        'teacher' => [
            'join_table' => 'rooms_teachers_rel',
        ],
        'task_set',
        'task_set_permission',
    ];
    
    public function selected_teachers(): array
    {
        if (!is_null($this->id)) {
            $teachers = new Teacher();
            $teachers->where_related($this);
            $teachers->get_iterated();
            $output = [];
            foreach ($teachers as $teacher) {
                $output[$teacher->id] = $teacher->id;
            }
            return $output;
        }
        return [];
    }
    
}