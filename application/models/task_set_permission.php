<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Task_set_permission model.
 *
 * @property int         $id
 * @property string      $updated            date time format YYYY-MM-DD HH:MM:SS
 * @property string      $created            date time format YYYY-MM-DD HH:MM:SS
 * @property string|null $publish_start_time date time format YYYY-MM-DD HH:MM:SS
 * @property string|null $upload_end_time    date time format YYYY-MM-DD HH:MM:SS
 * @property int|null    $group_id           entity id of model {@see Group}
 * @property int|null    $room_id            entity id of model {@see Room}
 * @property int|null    $task_set_id        entity id of model {@see Task_set}
 * @property bool        $enabled
 * @property string|null $deadline_notification_emails
 * @property bool        $deadline_notified
 * @property int         $deadline_notification_emails_handler
 * @property Task_set    $task_set
 * @property Group       $group
 * @property Room        $room
 *
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_group(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_room(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Task_set_permission extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'task_set',
        'group',
        'room',
    ];
    
}