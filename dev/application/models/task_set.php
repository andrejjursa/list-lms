<?php

/**
 * Task_set model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task_set extends DataMapper {
    
    const OPEN_TASK_SET_SESSION_NAME = 'OPEN_TASK_SET_SESSION';
    const STUDENT_FILE_NAME_REGEXP = '/^(?P<student_id>[0-9]+)\_(?P<file_name>[a-zA-Z]+)\_(?P<random_hash>[a-zA-Z0-9]+)\_(?P<solution_version>[0-9]+)\.zip$/i';
    
    private $filter_tasks_count_sql = '(SELECT COUNT(*) AS count FROM (`tasks`) LEFT OUTER JOIN `task_task_set_rel` task_task_set_rel ON `tasks`.`id` = `task_task_set_rel`.`task_id` LEFT OUTER JOIN `task_sets` `task_sets_subquery` ON `task_sets_subquery`.`id` = `task_task_set_rel`.`task_set_id` WHERE `task_sets_subquery`.`id` = `task_sets`.`id`)';
    private $max_solution_version = 0;
    
    public $has_many = array(
        'task' => array(
            'join_table' => 'task_task_set_rel',
        ),
        'solution',
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
    
    /**
     * Set the currently loaded task set as open task set.
     * @return boolean TRUE, if task set is set as opened, FALSE otherwise.
     */
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
    
    /**
     * Load record of opened task set from database table.
     * @return Task_set this object for method chaining.
     */
    public function get_as_open() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
            
        $id = $CI->session->userdata(self::OPEN_TASK_SET_SESSION_NAME);
        
        $this->get_by_id(intval($id));
        
        return $this;
    }
    
    /**
     * Returns opened task set ID.
     * @return integer ID of opened task set.
     */
    public function get_open_task_set_id() {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
            
        return $CI->session->userdata(self::OPEN_TASK_SET_SESSION_NAME);
    }
    
    /**
     * Reads the directory for solutions of this task set and sorted array of all files belonging to student.
     * @param integer $student_id ID of student.
     * @return array<string> sorted array of files.
     */
    public function get_student_files($student_id) {
        $this->max_solution_version = 0;
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                $student_files = array();
                if (count($all_files) > 0) { foreach ($all_files as $single_file) {
                    if (is_file($path . $single_file) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $single_file, $matches)) {
                        $student_files[intval($matches['solution_version'])] = array(
                            'file' => $single_file,
                            'filepath' => $path . $single_file,
                            'size' => get_file_size($path . $single_file),
                            'student_id' => $matches['student_id'],
                            'file_name' => $matches['file_name'],
                            'random_hash' => $matches['random_hash'],
                            'last_modified' => filemtime($path . $single_file),
                            'version' => intval($matches['solution_version']),
                        );
                        $this->max_solution_version = max(array($this->max_solution_version, intval($matches['solution_version'])));
                    }
                }}
                ksort($student_files, SORT_NUMERIC);
                return $student_files;
            }
        }
        return array();
    }
    
    /**
     * Return next version number of student solution for this task set.
     * @param integer $student_id ID of student.
     * @return integer next version number.
     */
    public function get_student_file_next_version($student_id) {
        $this->get_student_files($student_id);
        return $this->max_solution_version + 1;
    }
    
    /**
     * Return info about solution file with specific real file name.
     * @param string $real_filename solution file name.
     * @return boolean|array<mixed> returns FALSE if file is not found or array of file informations if it is found.
     */
    public function get_specific_file_info($real_filename) {
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                if (in_array($real_filename, $all_files) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $real_filename, $matches)) {
                    return array(
                        'file' => $real_filename,
                        'filepath' => $path . $real_filename,
                        'size' => get_file_size($path . $real_filename),
                        'student_id' => $matches['student_id'],
                        'file_name' => $matches['file_name'],
                        'random_hash' => $matches['random_hash'],
                        'last_modified' => filemtime($path . $real_filename),
                        'version' => intval($matches['solution_version']),
                    );
                }
            }
        }
        return FALSE;
    }
}