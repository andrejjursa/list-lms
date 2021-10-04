<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * @property int         $id
 * @property string      $updated                date time format YYYY-MM-DD HH:MM:SS
 * @property string      $created                date time format YYYY-MM-DD HH:MM:SS
 * @property string      $start                  date time format YYYY-MM-DD HH:MM:SS
 * @property string      $exec_start             date time format YYYY-MM-DD HH:MM:SS
 * @property string      $finish                 date time format YYYY-MM-DD HH:MM:SS
 * @property string      $test_type
 * @property int|null    $task_set_id            entity id of model {@see Task_set}
 * @property int|null    $student_id             entity id of model {@see Student}
 * @property int         $version
 * @property int|null    $task_id                entity id of model {@see Task}
 * @property int|null    $teacher_id             entity id of model {@see Teacher}
 * @property int         $priority
 * @property int         $original_priority
 * @property int|null    $worker
 * @property double      $points
 * @property double      $bonus
 * @property int         $status
 * @property string      $system_language
 * @property int         $age
 * @property string|null $result_html
 * @property string|null $result_message
 * @property string      $single_test_exec_start date time format YYYY-MM-DD HH:MM:SS
 * @property int         $restarts
 * @property Task_set    $task_set
 * @property Student     $student
 * @property Task        $task
 * @property Teacher     $teacher
 * @property Test        $test
 *
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_student(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_teacher(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_test(mixed $related, string $field = null, string $value = null)
 */
class Test_queue extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $table = 'tests_queue';
    
    public $has_one = [
        'task_set',
        'student',
        'task',
        'teacher',
    ];
    
    public $has_many = [
        'test' => [
            'join_table' => 'test_test_queue_rel',
        ],
    ];
    
}