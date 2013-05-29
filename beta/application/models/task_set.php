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
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                $student_files = array();
                if (count($all_files) > 0) { foreach ($all_files as $single_file) {
                    if (is_file($path . $single_file) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $single_file, $matches) && intval($matches['student_id']) == intval($student_id)) {
                        $student_files[intval($matches['solution_version'])] = array(
                            'file' => $single_file,
                            'filepath' => $path . $single_file,
                            'size' => get_file_size($path . $single_file),
                            'student_id' => intval($matches['student_id']),
                            'file_name' => $matches['file_name'],
                            'random_hash' => $matches['random_hash'],
                            'last_modified' => filemtime($path . $single_file),
                            'version' => intval($matches['solution_version']),
                        );
                    }
                }}
                ksort($student_files, SORT_NUMERIC);
                return $student_files;
            }
        }
        return array();
    }
    
    /**
     * Returns count of student files in this task set.
     * @param integer $student_id ID of student.
     * @return integer number of files.
     */
    public function get_student_files_count($student_id) {
        $this->max_solution_version = 0;
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                $count = 0;
                if (count($all_files) > 0) { foreach ($all_files as $single_file) {
                    if (is_file($path . $single_file) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $single_file, $matches) && intval($matches['student_id']) == intval($student_id)) {
                        $count++;
                        $this->max_solution_version = max(array($this->max_solution_version, intval($matches['solution_version'])));
                    }
                }}
                return $count;
            }
        }
        return 0;
    }
    
    /**
     * Return next version number of student solution for this task set.
     * @param integer $student_id ID of student.
     * @return integer next version number.
     */
    public function get_student_file_next_version($student_id) {
        $this->get_student_files_count($student_id);
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
    
    /**
     * Returns the internal files in ZIP archive of student solution.
     * @param string $real_filename solution file name.
     * @return array<string> array of ZIP archive content (array keys are ZIP archive file indexes).
     */
    public function get_student_file_content($real_filename) {
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                if (in_array($real_filename, $all_files) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $real_filename, $matches)) {
                    $output = array();
                    $zip_file = new ZipArchive();
                    if ($zip_file->open($path . $real_filename) === TRUE) {
                        for ($i = 0; $i < $zip_file->numFiles; $i++) {
                            $output[$i] = $zip_file->getNameIndex($i);
                        }
                        $zip_file->close();
                    }
                    return $output;
                }
            }
        }
        return array();
    }
    
    /**
     * Extracts one file from given student ZIP file and index and returns its content.
     * @param string $real_filename solution file name.
     * @param integer $index index in ZIP file.
     * @return boolean|array returns array with file content, name and extension or FALSE on error.
     */
    public function extract_student_file_by_index($real_filename, $index) {
        $file_info = $this->get_specific_file_info($real_filename);
        if ($file_info !== FALSE) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            $CI =& get_instance();
            $CI->load->library('session');
            $supported_extensions = $this->trim_explode(',', $CI->config->item('readable_file_extensions'));
            $all_userdata = $CI->session->all_userdata();
            $extract_path = 'private/extracted_solutions/task_set_' . intval($this->id) . '/' . $all_userdata['session_id'] . '/';
            @mkdir('private/extracted_solutions/task_set_' . intval($this->id) . '/');
            @mkdir($extract_path);
            $zip_file = new ZipArchive();
            $open = $zip_file->open($path . $real_filename);
            $content = '';
            $filename = '';
            $extension = '';
            $file_read = TRUE;
            if ($open === TRUE && intval($index) >= 0 && intval($index) < $zip_file->numFiles) {
                $extracted_file = $zip_file->getNameIndex(intval($index));
                $extension = '';
                $ext_pos = strrpos($extracted_file, '.');
                if ($ext_pos !== FALSE) { $extension = substr($extracted_file, $ext_pos + 1); }
                if (in_array($extension, $supported_extensions)) {
                    $zip_file->extractTo($extract_path, $extracted_file);
                    $content = @file_get_contents($extract_path . $extracted_file);
                    $filename = basename($extract_path . $extracted_file);
                } else {
                    $file_read = FALSE;
                }
                $zip_file->close();
            } else {
                $file_read = FALSE;
            }
            @unlink_recursive(rtrim($extract_path, '/'), TRUE);
            return $file_read ? array('content' => $content, 'filename' => $filename, 'extension' => $extension) : FALSE;
        }
        return FALSE;
    }
    
    /**
     * Performs explode on given string by given delimiter and trims all array items in output array.
     * @param string $delimiter delimiter.
     * @param string $string string to split to array.
     * @return array<string> result array.
     */
    private function trim_explode($delimiter, $string) {
        $array = explode($delimiter, $string);
        if (count($array) > 0) { foreach ($array as $key => $value) {
            $array[$key] = trim($value);
        }}
        return $array;
    }
}