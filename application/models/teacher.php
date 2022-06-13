<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Teacher model.
 *
 * @property int                      $id
 * @property string                   $updated            date time format YYYY-MM-DD HH:MM:SS
 * @property string                   $created            date time format YYYY-MM-DD HH:MM:SS
 * @property string                   $fullname
 * @property string                   $email
 * @property string                   $password
 * @property string                   $language
 * @property int|null                 $prefered_course_id entity id of model {@see Course}
 * @property int                      $widget_columns
 * @property Solution                 $solution
 * @property Comment                  $comment
 * @property Task                     $task
 * @property Task_set                 $comment_subscription
 * @property Room                     $room
 * @property Log                      $log
 * @property Admin_widget             $admin_widget
 * @property Test_queue               $test_queue
 * @property Course_content_model     $created_content
 * @property Course_content_model     $updated_content
 * @property Course                   $prefered_course
 * @property Parallel_moss_comparison $parallel_moss_comparison
 *
 * @method DataMapper where_related_solution(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_comment(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_comment_subscription(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_room(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_log(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_admin_widget(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_test_queue(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_created_content(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_updated_content(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_prefered_course(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_parallel_moss_comparison(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Teacher extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_many = [
        'solution',
        'comment'              => [
            'cascade_delete' => false,
        ],
        'task'                 => [
            'other_field'   => 'author',
            'join_self_as'  => 'author',
            'join_other_as' => 'task',
        ],
        'comment_subscription' => [
            'class'         => 'task_set',
            'other_field'   => 'comment_subscriber_teacher',
            'join_self_as'  => 'comment_subscriber_teacher',
            'join_other_as' => 'comment_subscription',
            'join_table'    => 'task_set_comment_subscription_rel',
        ],
        'room'                 => [
            'join_table' => 'rooms_teachers_rel',
        ],
        'log',
        'admin_widget',
        'test_queue',
        'created_content'      => [
            'class'         => 'Course_content_model',
            'other_field'   => 'creator',
            'join_self_as'  => 'creator',
            'join_other_as' => 'created_content',
        ],
        'updated_content'      => [
            'class'         => 'Course_content_model',
            'other_field'   => 'updator',
            'join_self_as'  => 'updator',
            'join_other_as' => 'updated_content',
        ],
        'parallel_moss_comparison',
    ];
    public $has_one = [
        'prefered_course' => [
            'class'          => 'course',
            'other_field'    => 'prefered_for_teacher',
            'join_self_as'   => 'prefered_for_teacher',
            'joint_other_as' => 'prefered_course',
        ],
    ];
    
    /**
     * Return full path of avatar (with base url) for this teacher.
     *
     * @return string path to avatar.
     */
    public function get_avatar(): string
    {
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
                    $config['maintain_ratio'] = false;
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
     *
     * @return boolean TRUE when avatar is uploaded, FALSE otherwise.
     */
    public function has_avatar(): bool
    {
        if (!is_null($this->id)) {
            $student_path_big_image = 'public/images_users/teachers/' . $this->id . '/avatar/big_avatar.jpg';
            if (file_exists($student_path_big_image)) {
                return true;
            }
        }
        return false;
    }
    
}