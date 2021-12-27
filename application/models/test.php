<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * @property int         $id
 * @property string      $updated date time format YYYY-MM-DD HH:MM:SS
 * @property string      $created date time format YYYY-MM-DD HH:MM:SS
 * @property string      $name
 * @property string      $type
 * @property string      $subtype
 * @property int|null    $task_id entity id of model {@see Task}
 * @property string|null $configuration
 * @property bool        $enabled
 * @property string|null $instructions
 * @property bool        $enable_scoring
 * @property int         $timeout
 * @property Task        $task
 * @property Test_queue  $test_queue
 *
 * @method DataMapper where_related_task(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_test_queue(mixed $related, string $field = null, string $value = null)
 */
class Test extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'task',
    ];
    
    public $has_many = [
        'test_queue' => [
            'join_table' => 'test_test_queue_rel',
        ],
    ];
    
    /**
     * Delete this test or related object.
     * If no parameters are set, this method deletes current test and all files associated with this test.
     *
     * @param DataMapper|string $object        related object to delete from relation.
     * @param string            $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '')
    {
        if (empty($object) && !is_array($object) && !empty($this->id)) {
            $path_to_test_files = 'private/uploads/unit_tests/test_' . $this->id;
            if (file_exists($path_to_test_files)) {
                unlink_recursive($path_to_test_files, true);
            }
        }
        parent::delete($object, $related_field);
    }
    
}