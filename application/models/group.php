<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Group model.
 *
 * @property int                 $id
 * @property string              $updated   date time format YYYY-MM-DD HH:MM:SS
 * @property string              $created   date time format YYYY-MM-DD HH:MM:SS
 * @property string              $name
 * @property int|null            $course_id entity id of model {@see Course}
 * @property Course              $course
 * @property Room                $room
 * @property Participant         $participant
 * @property Task_set            $task_set
 * @property Task_set_permission $task_set_permission
 *
 * @method DataMapper where_related_course(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_room(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_participant(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set_permission(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Group extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'course',
    ];
    
    public $has_many = [
        'room',
        'participant',
        'task_set',
        'task_set_permission',
    ];
}