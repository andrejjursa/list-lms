<?php

use Application\Interfaces\DataMapperExtensionsInterface;

class Test extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_one = array(
        'task',
    );
    
    public $has_many = array(
        'test_queue' => array(
            'join_table' => 'test_test_queue_rel',
        ),
    );


    /**
     * Delete this test or related object.
     * If no parameters are set, this method deletes current test and all files associated with this test.
     * @param DataMapper|string $object related object to delete from relation.
     * @param string $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '') {
        if (empty($object) && !is_array($object) && !empty($this->id)) {
            $path_to_test_files = 'private/uploads/unit_tests/test_' . $this->id;
            if (file_exists($path_to_test_files)) {
                unlink_recursive($path_to_test_files, TRUE);
            }
        }
        parent::delete($object, $related_field);
    }
    
}