<?php

use Application\Interfaces\DataMapperExtensionsInterface;

class Test_queue extends DataMapper implements DataMapperExtensionsInterface {
    
    public $table = 'tests_queue';
    
    public $has_one = array(
        'task_set',
        'student',
        'task',
        'teacher',
    );
    
    public $has_many = array(
        'test' => array(
            'join_table' => 'test_test_queue_rel',
        ),
    );
    
}