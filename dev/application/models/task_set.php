<?php

/**
 * Task_set model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set extends DataMapper {
    
    private $filter_tasks_count_sql = '(SELECT COUNT(*) AS count FROM (`tasks`) LEFT OUTER JOIN `task_task_set_rel` task_task_set_rel ON `tasks`.`id` = `task_task_set_rel`.`task_id` LEFT OUTER JOIN `task_sets` `task_sets_subquery` ON `task_sets_subquery`.`id` = `task_task_set_rel`.`task_set_id` WHERE `task_sets_subquery`.`id` = `task_sets`.`id`)';
    
    public $has_many = array(
        'task' => array(
            'join_table' => 'task_task_set_rel',
        ),
    );
    
    public $has_one = array(
    	'task_set_type',
    	'course',
    );
    
    public function where_has_tasks() {
        $this->where($this->filter_tasks_count_sql . ' > 0');
        return $this;
    }
    
    public function where_has_no_tasks() {
        $this->where($this->filter_tasks_count_sql . ' = 0');
        return $this;
    }
}