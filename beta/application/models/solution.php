<?php

/**
 * Solution model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Solution extends DataMapper {
    
    public $has_one = array(
        'task_set',
        'student',
        'teacher',
    );
    
}