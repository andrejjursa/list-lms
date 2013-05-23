<?php

/**
 * Student model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Student extends DataMapper {
    
    public $has_many = array(
        'participant',
    );
    
    public $has_one = array(
        'active_course' => array(
            'class' => 'course',
            'other_field' => 'active_for_student',
        ),
    );
    
    public function delete($object = '', $related_field = '') {
        if (empty($object) && !is_array($object) && !empty($this->id)) {
            $participant = new Participant();
            $participant->where_related($this);
            $participant->get();
            $participant->delete_all();
        }
        parent::delete($object, $related_field);
    }
}