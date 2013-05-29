<?php

/**
 * Student model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Student extends DataMapper {
    
    public $has_many = array(
        'participant',
        'solution',
    );
    
    public $has_one = array(
        'active_course' => array(
            'class' => 'course',
            'other_field' => 'active_for_student',
        ),
    );
    
    /**
     * Delete this student or related object.
     * If no parameters are set, this method deletes current student and all participant record related with this student.
     * @param DataMapper|string $object related object to delete from relation.
     * @param string $related_field relation internal name.
     */
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