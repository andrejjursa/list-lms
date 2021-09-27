<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Admin_widget model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 *
 * @property int @id
 * @property string $updated date time format YYYY-MM-DD HH:MM:SS
 * @property string $created date time format YYYY-MM-DD HH:MM:SS
 * @property int|null $teacher_id entity id of model Teacher
 * @property string $widget_type
 * @property string|null $widget_config
 * @property int $position
 * @property int $column
 *
 * @method DataMapper where_related_teacher(mixed $related, string $field = NULL, string $value = NULL)
 */
class Admin_widget extends DataMapper implements DataMapperExtensionsInterface {
     
    public $has_one = array('teacher');
    
}