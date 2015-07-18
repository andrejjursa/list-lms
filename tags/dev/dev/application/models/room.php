<?php

/**
 * Room model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Room extends DataMapper {
    
    public $has_one = array(
        'group'
    );
    public $has_many = array(
        'teacher' => array(
            'join_table' => 'rooms_teachers_rel',
        ),
        'task_set',
        'task_set_permission',
    );
    
    public function selected_teachers() {
        if (!is_null($this->id)) {
            $teachers = new Teacher();
            $teachers->where_related($this);
            $teachers->get_iterated();
            $output = array();
            foreach($teachers as $teacher) {
                $output[$teacher->id] = $teacher->id;
            }
            return $output;
        }
        return array();
    }
    
}