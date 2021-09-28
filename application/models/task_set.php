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

    private $related_permissions = array();

    private $related_task_set_type = array();

    private static $updated_field_key = null;
    
    public $has_many = array(
        'task' => array(
            'join_table' => 'task_task_set_rel',
        ),
        'solution',
        'comment',
        'task_set_permission',
        'project_selection',
        'comment_subscriber_student' => array(
            'class' => 'student',
            'other_field' => 'comment_subscription',
            'join_self_as' => 'comment_subscription',
            'join_other_as' => 'comment_subscriber_student',
            'join_table' => 'task_set_comment_subscription_rel',
        ),
        'comment_subscriber_teacher' => array(
            'class' => 'teacher',
            'other_field' => 'comment_subscription',
            'join_self_as' => 'comment_subscription',
            'join_other_as' => 'comment_subscriber_teacher',
            'join_table' => 'task_set_comment_subscription_rel',
        ),
        'test_queue',
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
     * @param integer|NULL $version concrete version of file or NULL for all files (default NULL).
     * @return array<string> sorted array of files.
     */
    public function get_student_files($student_id, $version = NULL) {
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . intval($this->id) . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                $student_files = array();
                if (count($all_files) > 0) { foreach ($all_files as $single_file) {
                    if (is_file($path . $single_file) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $single_file, $matches) && intval($matches['student_id']) == intval($student_id)) {
                        if ($version === NULL || (int)$version == intval($matches['solution_version'])) {
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
            @mkdir('private/extracted_solutions/task_set_' . intval($this->id) . '/', DIR_READ_MODE);
            @mkdir($extract_path, DIR_READ_MODE);
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
                if (in_array(strtolower($extension), $supported_extensions)) {
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
     * This method will extract student file content to provided folder. Optionally can extract only files with specified extensions.
     * @param string $real_filename solution file name.
     * @param string $folder destination folder.
     * @param array<string>|NULL $extensions array of extensions to extract or NULL to extract all files.
     * @return boolean TRUE on success, FALSE on error.
     */
    public function extract_student_zip_to_folder($real_filename, $folder, $extensions = NULL) {
        $file_info = $this->get_specific_file_info($real_filename);
        if ($file_info !== FALSE) {
            $zip_file = new ZipArchive();
            $open = $zip_file->open($file_info['filepath']);
            if (is_null($extensions) || !is_array($extensions)) {
                $zip_file->extractTo($folder);
            } else {
                for($index = 0; $index < $zip_file->numFiles; $index++) {
                    $filename = $zip_file->getNameIndex($index);
                    $ext_pos = strrpos($filename, '.');
                    if ($ext_pos !== FALSE) {
                        $ext = substr($filename, $ext_pos + 1);
                        if (in_array($ext, $extensions)) {
                            $zip_file->extractTo($folder, $filename);
                        }
                    }
                }
            }
            $zip_file->close();
            return TRUE;
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
    
    /**
     * Deletes relations (if parameters are set) or this object from database.
     * All solutions related to this task set will be deleted as well.
     * @param DataMapper|string $object related object to delete from relation.
     * @param string $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '') {
        $this_id = $this->id;
        if (empty($object) && !is_array($object) && !empty($this_id)) {
            $solutions = new Solution();
            $solutions->get_by_related('task_set', 'id', $this_id);
            foreach($solutions as $solution) {
                set_time_limit(ini_get('max_execution_time'));
                $solution->delete();
            }
        }
        parent::delete($object, $related_field);
    }
    
    /**
     * Enforces download of all files submited as a solution of this task set.
     */
    public function download_all_solutions() {
        $filename = $this->get_new_solution_zip_filename();
        $zip_archive = new ZipArchive();
        if ($zip_archive->open($filename, ZipArchive::CREATE)) {
            $course = $this->course->get();
            $period = $course->period->get();
            $overlay_name = $this->lang->get_overlay('task_sets', $this->id, 'name');
            $readme = trim($overlay_name) == '' ? $this->name : $overlay_name;
            $readme .= "\r\n" . str_repeat('-', mb_strlen($readme));
            $readme .= "\r\n" . $this->lang->text($course->name);
            $readme .= "\r\n" . $this->lang->text($period->name);
            $zip_archive->addFromString('readme.txt', $readme);
            $this->add_files_to_zip_archive($zip_archive, $course);
            $zip_archive->close();
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename='.basename($filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            ob_clean();
            flush();
            $f = fopen($filename, 'r');
            while (!feof($f)) {
                echo fread($f, 1024);
            }
            fclose($f);
            unlink($filename);
        } else {
            header("HTTP/1.0 404 Not Found");
        }
        die();
    }
    
    /**
     * Add files to open zip archive.
     * @param ZipArchive $zip_archive open zip archive.
     * @param Course $course course object with loaded course.
     * @param string|NULL $subdirectory subdirectory where to add files.
     */
    public function add_files_to_zip_archive(ZipArchive $zip_archive, Course $course, $subdirectory = NULL) {
        if (!is_null($this->id)) {
            ini_set('max_execution_time', 300);
            $path_to_task_set_files = 'private/uploads/solutions/task_set_' . $this->id . '/';
            if (file_exists($path_to_task_set_files)) {
                $groups = $course->groups->get_iterated();
                $group_names = array(0 => 'unassigned');
                foreach ($groups as $group) {
                    $group_names[$group->id] = normalizeForFilesystem($this->lang->text($group->name));
                }
                $students = new Student();
                $students->include_related('participant');
                $students->where_related('participant/course', $course);
                $students->get_iterated();
                $student_groups = array();
                foreach ($students as $studnet) {
                    $student_groups[$studnet->id] = intval($studnet->participant_group_id);
                }
                $files = scandir($path_to_task_set_files);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        if (preg_match(self::STUDENT_FILE_NAME_REGEXP, $file, $matches)) {
                            $student_id = intval($matches['student_id']);
                            $path = ($subdirectory !== NULL && trim($subdirectory) != '' ? $subdirectory . '/' : '') . $group_names[$student_groups[$student_id]] . '/' . $file;
                            $zip_archive->addFile($path_to_task_set_files . $file, $path);
                        }
                    }
                }
            }
        }
    }

    public function get_task_set_time_class($permission_id = null) {
        $task_set_type = $this->get_related_task_set_type();

        if ($this->content_type == 'task_set' && (!isset($task_set_type->upload_solution) || !$task_set_type->upload_solution)) {
            return '';
        }

        $permissions = $this->get_related_permissions();

        $upload_end_time = null;
        if (($permissions != null) && (count($permissions) > 0)) {
            if ($permission_id !== null && isset($permissions[$permission_id])) {
                $upload_end_time = $permissions[$permission_id]->upload_end_time;
            } else {
                $most_soon_end_time = 0;
                $all_after = true;
                foreach ($permissions as $permission) {
                    if (strtotime($permission->upload_end_time . ' +7 days') < date('U')) {
                        if (strtotime($permission->upload_end_time) > $most_soon_end_time) {
                            $upload_end_time = $permission->upload_end_time;
                        } else {
                            $all_after = false;
                        }
                    }
                }
                if (!$all_after) {
                    $upload_end_time = null;
                }
            }
        } else {
            $upload_end_time = $this->upload_end_time;
        }

        $this->load->helper('task_sets');

        return get_task_set_timed_class($upload_end_time, true, 0);
    }

    private function get_related_permissions() {
        if (is_null($this->id)) {
            return array();
        }
        if (!isset($this->related_permissions[$this->id])) {
            $task_set_permissions = new Task_set_permission();
            $task_set_permissions->where_related('task_set', 'id', $this->id);
            $task_set_permissions->where('enabled', 1);
            $task_set_permissions->get_iterated();

            $this->related_permissions[$this->id] = array();

            foreach ($task_set_permissions as $task_set_permission) {
                $this->related_permissions[$this->id][$task_set_permission->id] = (object) $task_set_permission->to_array();
            }
        }
        return $this->related_permissions[$this->id];
    }

    private function get_related_task_set_type() {
        if (is_null($this->id)) {
            return (object) array();
        }
        if (!isset($this->related_task_set_type[$this->id])) {
            $task_set_type = new Task_set_type();
            if (isset($this->course_id)) {
                $task_set_type->select('*');
                $task_set_type->select('course_task_set_type_rel.upload_solution');
                $task_set_type->where_related('task_set', 'id', $this->id);
                $task_set_type->where_related('course', 'id', $this->course_id);
                $task_set_type->limit(1);
                $task_set_type->get();
            }

            if ($task_set_type->exists()) {
                $this->related_task_set_type[$this->id] = (object) $task_set_type->to_array();
                $this->related_task_set_type[$this->id]->upload_solution = (bool) $task_set_type->upload_solution;
            } else {
                $this->related_task_set_type[$this->id] = (object) array();
            }
        }

        return $this->related_task_set_type[$this->id];
    }

    /**
     * Returns unused file name for solution download.
     * @return string file name with path.
     */
    private function get_new_solution_zip_filename() {
        $path = 'private/extracted_solutions/';
        $filename = '';
        $i = 0;
        do {
            $filename = 'task_set_solutions_' . date('U') . '_' . ($this->id != NULL ? $this->id : 'unknown') . ($i > 0 ? '_' . $i : '') . '.zip';
            $i++;
        } while (file_exists($path . $filename));
        return $path . $filename;
    }

    /**
     * Hide updated field so it will not be changed during update.
     */
    public function hide_updated_field() {
        if(($key = array_search('updated', $this->fields)) !== false) {
            unset($this->fields[$key]);
            self::$updated_field_key = $key;
        }
    }

    /**
     * Show updated field so it will be changed during update.
     * Must be hidden first.
     */
    public function show_updated_field() {
        if(($key = array_search('updated', $this->fields)) === false && !is_null(self::$updated_field_key)) {
            array_splice($this->fields, self::$updated_field_key, 0, array('updated'));
        }
    }
}
