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
        'comment' => array(
            'cascade_delete' => FALSE,
        ),
        'comment_subscription' => array(
            'class' => 'task_set',
            'other_field' => 'comment_subscriber_student',
            'join_self_as' => 'comment_subscriber_student',
            'join_other_as' => 'comment_subscription',
            'join_table' => 'task_set_comment_subscription_rel',
        ),
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
    
    /**
     * Create random password token for student.
     * If student is exists, it will be automaticaly updated (only password token information).
     */
    public function generate_random_password_token() {
        $this->load->library('form_validation');
        
        do {
            $this->password_token = sha1(time() . '-' . $this->config->item('encryption_key') . '-' . $_SERVER['SCRIPT_FILENAME'] . '-' . rand(1000000, 9999999));
        } while(!$this->form_validation->is_unique($this->password_token, 'students.password_token'));
        
        if (!is_null($this->id) && is_numeric($this->id) && intval($this->id) > 0) {
            $student = new Student(intval($this->id));
            if ($student->exists()) {
                $student->password_token = $this->password_token;
                $student->save();
            }
            unset($student);
        }
    }
}