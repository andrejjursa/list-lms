<?php

/**
 * Comment model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Comment extends DataMapper {
    
    public $default_order_by = array('created');

    public $has_one = array(
        'student' => array(
            'cascade_delete' => FALSE,
        ),
        'teacher' => array(
            'cascade_delete' => FALSE,
        ),
        'task_set',
        'reply_at' => array(
            'class' => 'comment',
            'other_field' => 'comment',
            'join_self_as' => 'comment',
            'join_other_as' => 'reply_at',
        ),
    );
    
    public $has_many = array(
        'comment' => array(
            'other_field' => 'reply_at',
            'join_self_as' => 'reply_at',
            'join_other_as' => 'comment',
        ),
    );
    
    /**
     * Returns structured comments for given task set.
     * @param Task_set|integer $task_set task set object or id.
     * @return array<Comment> structured array of comments (parent => key => Comment).
     */
    public static function get_comments_for_task_set($task_set) {
        if (!($task_set instanceof DataMapper) && !is_numeric($task_set)) { return array(); }
        if (!($task_set instanceof Task_set)) {
            $task_set = new Task_set(is_numeric($task_set) ? intval($task_set) : $task_set->id);
        }
        if ($task_set->exists()) {
            $comments = $task_set->comment;
            $comments->include_related('student', '*', true, true);
            $comments->include_related('teacher', '*', true, true);
            $comments->order_by('created');
            $comments->get();
            
            $output = array();
            
            if ($comments->exists()) { foreach ($comments->all as $comment) {
                $output[intval($comment->reply_at_id)][] = $comment;
            }}
            
            return $output;
        } else {
            return array();
        }
    }
    
    /**
     * Deletes relations (if parameters are set) or this object from database.
     * All comments which replies to this one will be deleted as well.
     * @param DataMapper|string $object related object to delete from relation.
     * @param string $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '') {
        $this_id = $this->id;
        if (empty($object) && !is_array($object) && !empty($this_id)) {
            $comments = $this->comment->get_iterated();
            foreach($comments as $comment) {
                $comment->delete();
            }
        }
        parent::delete($object, $related_field);
    }
    
}