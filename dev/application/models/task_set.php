<?php

/**
 * Task_set model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set extends DataMapper {
    
    const OPEN_TASK_SET_SESSION_NAME = 'OPEN_TASK_SET_SESSION';
    
    private $filter_tasks_count_sql = '(SELECT COUNT(*) AS count FROM (`tasks`) LEFT OUTER JOIN `task_task_set_rel` task_task_set_rel ON `tasks`.`id` = `task_task_set_rel`.`task_id` LEFT OUTER JOIN `task_sets` `task_sets_subquery` ON `task_sets_subquery`.`id` = `task_task_set_rel`.`task_set_id` WHERE `task_sets_subquery`.`id` = `task_sets`.`id`)';
    
    public $has_many = array(
        'task' => array(
            'join_table' => 'task_task_set_rel',
        ),
    );
    
    public $has_one = array(
    	'task_set_type',
    	'course',
        'room',
        'group',
    );
    
    /**
     * Add condition to load only task sets which have one or more related tasks.
     * @return Task_set this object.
     */
    public function where_has_tasks() {
        $this->where($this->filter_tasks_count_sql . ' > 0');
        return $this;
    }
    
    /**
     * Add condition to load only tasks which have no task in relation.
     * @return Task_set this object.
     */
    public function where_has_no_tasks() {
        $this->where($this->filter_tasks_count_sql . ' = 0');
        return $this;
    }
    
    public function set_as_open() {
        if (!is_null($this->id)) {
            $CI =& get_instance();
            $CI->load->database();
            $CI->load->library('session');
            
            $CI->session->set_userdata(self::OPEN_TASK_SET_SESSION_NAME, $this->id);
            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_as_open() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
            
        $id = $CI->session->userdata(self::OPEN_TASK_SET_SESSION_NAME);
        
        $this->get_by_id(intval($id));
        
        return $this;
    }
    
    public function get_open_task_set_id() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
            
        return $CI->session->userdata(self::OPEN_TASK_SET_SESSION_NAME);
    }
}