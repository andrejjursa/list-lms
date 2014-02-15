<?php

/**
 * Teacher model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Teacher extends DataMapper {
    
    public $has_many = array(
        'solution',
        'comment' => array(
            'cascade_delete' => FALSE,
        ),
        'task' => array(
            'other_field' => 'author',
            'join_self_as' => 'author',
            'join_other_as' => 'task'
        ),
        'comment_subscription' => array(
            'class' => 'task_set',
            'other_field' => 'comment_subscriber_teacher',
            'join_self_as' => 'comment_subscriber_teacher',
            'join_other_as' => 'comment_subscription',
            'join_table' => 'task_set_comment_subscription_rel',
        ),
        'room' => array(
            'join_table' => 'rooms_teachers_rel',
        ),
        'log',
    );
    public $has_one = array(
        'prefered_course' => array(
            'class' => 'course',
            'other_field' => 'prefered_for_teacher',
            'join_self_as' => 'prefered_for_teacher',
            'joint_other_as' => 'prefered_course',
        ),
    );
    
    /**
     * Return full path of avatar (with base url) for this teacher.
     * @return string path to avatar.
     */
    public function get_avatar() {
        $avatar_path = 'public/images_users/no_avatar.jpg';
        if (!is_null($this->id)) {
            $student_path_big_image = 'public/images_users/teachers/' . $this->id . '/avatar/big_avatar.jpg';
            $student_path_avatar = 'public/images_users/teachers/' . $this->id . '/avatar/avatar.jpg';
            if (file_exists($student_path_big_image)) {
                if (!file_exists($student_path_avatar)) {
                    $CI =& get_instance();
                    
                    $CI->load->library('image_lib');
                
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $student_path_big_image;
                    $config['width'] = 64;
                    $config['height'] = 96;
                    $config['maintain_ratio'] = FALSE;
                    $config['new_image'] = $student_path_avatar;

                    $CI->image_lib->initialize($config);
                    
                    $CI->image_lib->resize();
                }
                $avatar_path = $student_path_avatar;
            }
        }
        return base_url($avatar_path);
    }
    
    /**
     * Check if teacher has avatar image uploaded. It checks the big_avatar.jpg presence, not the small one.
     * @return boolean TRUE when avatar is uploaded, FALSE otherwise.
     */
    public function has_avatar() {
        if (!is_null($this->id)) {
            $student_path_big_image = 'public/images_users/teachers/' . $this->id . '/avatar/big_avatar.jpg';
            if (file_exists($student_path_big_image)) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Deletes teacher avatar.
     */
    public function delete_avatar() {
        if (!is_null($this->id)) {
            $student_path_big_image = 'public/images_users/teachers/' . $this->id . '/avatar/big_avatar.jpg';
            $student_path_avatar = 'public/images_users/teachers/' . $this->id . '/avatar/avatar.jpg';
            @unlink($student_path_big_image);
            @unlink($student_path_avatar);
        }
    }
    
}