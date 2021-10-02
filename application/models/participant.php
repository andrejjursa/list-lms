<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Participant model.
 *
 * @property int      $id
 * @property string   $updated    date time format YYYY-MM-DD HH:MM:SS
 * @property string   $created    date time format YYYY-MM-DD HH:MM:SS
 * @property int|null $student_id entity id of model {@see Student}
 * @property int|null $course_id  entity id of model {@see Course}
 * @property int|null $group_id   entity id of model {@see Group}
 * @property bool     $allowed
 * @property Student  $student
 * @property Course   $course
 * @property Group    $group
 *
 * @method DataMapper where_related_student(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_course(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_group(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Participant extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'student',
        'course',
        'group',
    ];
    
}