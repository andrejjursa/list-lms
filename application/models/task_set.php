<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Task_set model.
 *
 * @property int                 $id
 * @property string              $updated                    date time format YYYY-MM-DD HH:MM:SS
 * @property string              $created                    date time format YYYY-MM-DD HH:MM:SS
 * @property string              $content_type               one of: `task_set`, `project`
 * @property string              $name
 * @property int|null            $course_id                  entity id of model {@see Course}
 * @property int|null            $task_set_type_id           entity id of model {@see Task_set_type}
 * @property bool                $published
 * @property string|null         $publish_start_time         date time format YYYY-MM-DD HH:MM:SS
 * @property string|null         $upload_end_time            date time format YYYY-MM-DD HH:MM:SS
 * @property int|null            $group_id                   entity id of model {@see Group}
 * @property int|null            $room_id                    entity id of model {@see Room}
 * @property string|null         $instructions
 * @property double|null         $points_override
 * @property bool                $comments_enabled
 * @property bool                $comments_moderated
 * @property string|null         $allowed_file_types         comma separated list
 * @property string|null         $allowed_test_types         comma separated list
 * @property string|null         $internal_comment
 * @property bool                $enable_tests_scoring
 * @property int                 $test_min_needed
 * @property int                 $test_max_allowed
 * @property string|null         $deadline_notification_emails
 * @property bool                $deadline_notified
 * @property int                 $deadline_notification_emails_handler
 * @property string|null         $project_selection_deadline date time format YYYY-MM-DD HH:MM:SS
 * @property int                 $test_priority
 * @property int                 $sorting
 * @property Task                $task
 * @property Solution            $solution
 * @property Comment             $comment
 * @property Task_set_permission $task_set_permission
 * @property Project_selection   $project_selection
 * @property Student             $comment_subscriber_student
 * @property Teacher             $comment_subscriber_teacher
 * @property Test_queue          $test_queue
 * @property Course              $course
 * @property Group               $group
 * @property Room                $room
 * @property Task_set_type       $task_set_type
 *
 * @method DataMapper where_related_task(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_solution(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_comment(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set_permission(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_project_selection(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_comment_subscriber_student(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_comment_subscriber_teacher(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_test_queue(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_course(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set_type(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_group(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_room(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Task_set extends DataMapper implements DataMapperExtensionsInterface
{
    
    public const OPEN_TASK_SET_SESSION_NAME = 'OPEN_TASK_SET_SESSION';
    public const STUDENT_FILE_NAME_REGEXP = '/^(?P<student_id>\d+)\_(?P<file_name>[a-zA-Z]+)\_(?P<random_hash>[a-zA-Z0-9]+)\_(?P<solution_version>\d+)\.zip$/i';
    
    private $filter_tasks_count_sql = '(SELECT COUNT(*) AS count FROM (`tasks`) LEFT OUTER JOIN `task_task_set_rel` task_task_set_rel ON `tasks`.`id` = `task_task_set_rel`.`task_id` LEFT OUTER JOIN `task_sets` `task_sets_subquery` ON `task_sets_subquery`.`id` = `task_task_set_rel`.`task_set_id` WHERE `task_sets_subquery`.`id` = `task_sets`.`id`)';
    private $max_solution_version = 0;
    
    private $related_permissions = [];
    
    private $related_task_set_type = [];
    
    private static $updated_field_key;
    
    public $has_many = [
        'task'                       => [
            'join_table' => 'task_task_set_rel',
        ],
        'solution',
        'comment',
        'task_set_permission',
        'project_selection',
        'comment_subscriber_student' => [
            'class'         => 'student',
            'other_field'   => 'comment_subscription',
            'join_self_as'  => 'comment_subscription',
            'join_other_as' => 'comment_subscriber_student',
            'join_table'    => 'task_set_comment_subscription_rel',
        ],
        'comment_subscriber_teacher' => [
            'class'         => 'teacher',
            'other_field'   => 'comment_subscription',
            'join_self_as'  => 'comment_subscription',
            'join_other_as' => 'comment_subscriber_teacher',
            'join_table'    => 'task_set_comment_subscription_rel',
        ],
        'test_queue',
    ];
    
    public $has_one = [
        'task_set_type',
        'course',
        'room',
        'group',
    ];
    
    /**
     * Add condition to load only task sets which have one or more related tasks.
     *
     * @return Task_set this object.
     */
    public function where_has_tasks(): Task_set
    {
        $this->where($this->filter_tasks_count_sql . ' > 0');
        return $this;
    }
    
    /**
     * Add condition to load only tasks which have no task in relation.
     *
     * @return Task_set this object.
     */
    public function where_has_no_tasks(): Task_set
    {
        $this->where($this->filter_tasks_count_sql . ' = 0');
        return $this;
    }
    
    /**
     * Set the currently loaded task set as open task set.
     *
     * @return boolean TRUE, if task set is set as opened, FALSE otherwise.
     */
    public function set_as_open(): bool
    {
        if (!is_null($this->id)) {
            $CI =& get_instance();
            $CI->load->database();
            $CI->load->library('session');
            
            $CI->session->set_userdata(self::OPEN_TASK_SET_SESSION_NAME, $this->id);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Load record of opened task set from database table.
     *
     * @return Task_set this object for method chaining.
     */
    public function get_as_open(): Task_set
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
        
        $id = $CI->session->userdata(self::OPEN_TASK_SET_SESSION_NAME);
        
        $this->get_by_id((int)$id);
        
        return $this;
    }
    
    /**
     * Returns opened task set ID.
     *
     * @return integer ID of opened task set.
     */
    public function get_open_task_set_id(): int
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
        
        return $CI->session->userdata(self::OPEN_TASK_SET_SESSION_NAME);
    }
    
    /**
     * Reads the directory for solutions of this task set and sorted array of all files belonging to student.
     *
     * @param integer      $student_id ID of student.
     * @param integer|NULL $version    concrete version of file or NULL for all files (default NULL).
     *
     * @return array<string> sorted array of files.
     */
    public function get_student_files(int $student_id, ?int $version = null): array
    {
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . $this->id . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                $student_files = [];
                if (count($all_files) > 0) {
                    foreach ($all_files as $single_file) {
                        if (is_file($path . $single_file) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $single_file, $matches) && (int)$matches['student_id'] === $student_id) {
                            if ($version === null || $version === (int)$matches['solution_version']) {
                                $student_files[(int)$matches['solution_version']] = [
                                    'file'          => $single_file,
                                    'filepath'      => $path . $single_file,
                                    'size'          => get_file_size($path . $single_file),
                                    'student_id'    => (int)$matches['student_id'],
                                    'file_name'     => $matches['file_name'],
                                    'random_hash'   => $matches['random_hash'],
                                    'last_modified' => filemtime($path . $single_file),
                                    'version'       => (int)$matches['solution_version'],
                                ];
                            }
                        }
                    }
                }
                ksort($student_files, SORT_NUMERIC);
                return $student_files;
            }
        }
        return [];
    }
    
    /**
     * Returns count of student files in this task set.
     *
     * @param integer $student_id ID of student.
     *
     * @return integer number of files.
     */
    public function get_student_files_count(int $student_id): int
    {
        $this->max_solution_version = 0;
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . $this->id . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                $count = 0;
                if (count($all_files) > 0) {
                    foreach ($all_files as $single_file) {
                        if (is_file($path . $single_file) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $single_file, $matches) && (int)$matches['student_id'] === $student_id) {
                            $count++;
                            $this->max_solution_version = max([$this->max_solution_version, (int)$matches['solution_version']]);
                        }
                    }
                }
                return $count;
            }
        }
        return 0;
    }
    
    /**
     * Return next version number of student solution for this task set.
     *
     * @param integer $student_id ID of student.
     *
     * @return integer next version number.
     */
    public function get_student_file_next_version(int $student_id): int
    {
        $this->get_student_files_count($student_id);
        return $this->max_solution_version + 1;
    }
    
    /**
     * Return info about solution file with specific real file name.
     *
     * @param string $real_filename solution file name.
     *
     * @return boolean|array returns FALSE if file is not found or array of file informations if it is found.
     */
    public function get_specific_file_info(string $real_filename)
    {
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . $this->id . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                if (in_array($real_filename, $all_files, true) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $real_filename, $matches)) {
                    return [
                        'file'          => $real_filename,
                        'filepath'      => $path . $real_filename,
                        'size'          => get_file_size($path . $real_filename),
                        'student_id'    => $matches['student_id'],
                        'file_name'     => $matches['file_name'],
                        'random_hash'   => $matches['random_hash'],
                        'last_modified' => filemtime($path . $real_filename),
                        'version'       => (int)$matches['solution_version'],
                    ];
                }
            }
        }
        return false;
    }
    
    /**
     * Returns the internal files in ZIP archive of student solution.
     *
     * @param string $real_filename solution file name.
     *
     * @return array<string> array of ZIP archive content (array keys are ZIP archive file indexes).
     */
    public function get_student_file_content(string $real_filename): array
    {
        if (!is_null($this->id)) {
            $path = 'private/uploads/solutions/task_set_' . $this->id . '/';
            if (file_exists($path)) {
                $all_files = scandir($path);
                if (in_array($real_filename, $all_files, true) && preg_match(self::STUDENT_FILE_NAME_REGEXP, $real_filename, $matches)) {
                    $output = [];
                    $zip_file = new ZipArchive();
                    if ($zip_file->open($path . $real_filename) === true) {
                        for ($i = 0; $i < $zip_file->numFiles; $i++) {
                            $output[$i] = $zip_file->getNameIndex($i);
                        }
                        $zip_file->close();
                    }
                    return $output;
                }
            }
        }
        return [];
    }
    
    /**
     * Extracts one file from given student ZIP file and index and returns its content.
     *
     * @param string  $real_filename solution file name.
     * @param integer $index         index in ZIP file.
     *
     * @return boolean|array returns array with file content, name and extension or FALSE on error.
     */
    public function extract_student_file_by_index(string $real_filename, int $index)
    {
        $file_info = $this->get_specific_file_info($real_filename);
        if ($file_info !== false) {
            $path = 'private/uploads/solutions/task_set_' . $this->id . '/';
            $CI =& get_instance();
            $CI->load->library('session');
            $supported_extensions = $this->trim_explode($CI->config->item('readable_file_extensions'));
            $all_userdata = $CI->session->all_userdata();
            $extract_path = 'private/extracted_solutions/task_set_' . $this->id . '/' . $all_userdata['session_id'] . '/';
            @mkdir('private/extracted_solutions/task_set_' . $this->id . '/', DIR_READ_MODE);
            @mkdir($extract_path, DIR_READ_MODE);
            $zip_file = new ZipArchive();
            $open = $zip_file->open($path . $real_filename);
            $content = '';
            $filename = '';
            $extension = '';
            $file_read = true;
            if ($open === true && $index >= 0 && $index < $zip_file->numFiles) {
                $extracted_file = $zip_file->getNameIndex($index);
                $extension = '';
                $ext_pos = strrpos($extracted_file, '.');
                if ($ext_pos !== false) {
                    $extension = substr($extracted_file, $ext_pos + 1);
                }
                if (in_array(strtolower($extension), $supported_extensions)) {
                    $zip_file->extractTo($extract_path, $extracted_file);
                    $content = @file_get_contents($extract_path . $extracted_file);
                    $filename = basename($extract_path . $extracted_file);
                } else {
                    $file_read = false;
                }
                $zip_file->close();
            } else {
                $file_read = false;
            }
            @unlink_recursive(rtrim($extract_path, '/'), true);
            return $file_read ? ['content' => $content, 'filename' => $filename, 'extension' => $extension] : false;
        }
        return false;
    }
    
    /**
     * This method will extract student file content to provided folder. Optionally can extract only files with
     * specified extensions.
     *
     * @param string             $real_filename solution file name.
     * @param string             $folder        destination folder.
     * @param array<string>|NULL $extensions    array of extensions to extract or NULL to extract all files.
     *
     * @return boolean TRUE on success, FALSE on error.
     */
    public function extract_student_zip_to_folder(string $real_filename, string $folder, ?array $extensions = null): bool
    {
        $file_info = $this->get_specific_file_info($real_filename);
        if ($file_info !== false) {
            $zip_file = new ZipArchive();
            $open = $zip_file->open($file_info['filepath']);
            if (!is_array($extensions)) {
                $zip_file->extractTo($folder);
            } else {
                for ($index = 0; $index < $zip_file->numFiles; $index++) {
                    $filename = $zip_file->getNameIndex($index);
                    $ext_pos = strrpos($filename, '.');
                    if ($ext_pos !== false) {
                        $ext = substr($filename, $ext_pos + 1);
                        if (in_array($ext, $extensions, true)) {
                            $zip_file->extractTo($folder, $filename);
                        }
                    }
                }
            }
            $zip_file->close();
            return true;
        }
        return false;
    }
    
    /**
     * Performs explode on given string by given delimiter and trims all array items in output array.
     *
     * @param string $string string to split to array.
     *
     * @return array<string> result array.
     */
    private function trim_explode(string $string): array
    {
        $array = explode(',', $string);
        if (count($array) > 0) {
            foreach ($array as $key => $value) {
                $array[$key] = trim($value);
            }
        }
        return $array;
    }
    
    /**
     * Deletes relations (if parameters are set) or this object from database.
     * All solutions related to this task set will be deleted as well.
     *
     * @param DataMapper|string $object        related object to delete from relation.
     * @param string            $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '')
    {
        $this_id = $this->id;
        if (empty($object) && !is_array($object) && !empty($this_id)) {
            $solutions = new Solution();
            $solutions->get_by_related('task_set', 'id', $this_id);
            foreach ($solutions as $solution) {
                set_time_limit(ini_get('max_execution_time'));
                $solution->delete();
            }
        }
        parent::delete($object, $related_field);
    }
    
    /**
     * Enforces download of all files submited as a solution of this task set.
     */
    public function download_all_solutions(): void
    {
        $filename = $this->get_new_solution_zip_filename();
        $zip_archive = new ZipArchive();
        if ($zip_archive->open($filename, ZipArchive::CREATE)) {
            $course = $this->course->get();
            $period = $course->period->get();
            $overlay_name = $this->lang->get_overlay('task_sets', $this->id, 'name');
            $readme = trim($overlay_name) === '' ? $this->name : $overlay_name;
            $readme .= "\r\n" . str_repeat('-', mb_strlen($readme));
            $readme .= "\r\n" . $this->lang->text($course->name);
            $readme .= "\r\n" . $this->lang->text($period->name);
            $zip_archive->addFromString('readme.txt', $readme);
            $this->add_files_to_zip_archive($zip_archive, $course);
            $zip_archive->close();
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename=' . basename($filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            ob_clean();
            flush();
            $f = fopen($filename, 'rb');
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
     *
     * @param ZipArchive  $zip_archive  open zip archive.
     * @param Course      $course       course object with loaded course.
     * @param string|NULL $subdirectory subdirectory where to add files.
     */
    public function add_files_to_zip_archive(ZipArchive $zip_archive, Course $course, ?string $subdirectory = null): void
    {
        if (!is_null($this->id)) {
            ini_set('max_execution_time', 300);
            $path_to_task_set_files = 'private/uploads/solutions/task_set_' . $this->id . '/';
            if (file_exists($path_to_task_set_files)) {
                $groups = $course->groups->get_iterated();
                $group_names = [0 => 'unassigned'];
                foreach ($groups as $group) {
                    $group_names[$group->id] = normalizeForFilesystem($this->lang->text($group->name));
                }
                $students = new Student();
                $students->include_related('participant');
                $students->where_related('participant/course', $course);
                $students->get_iterated();
                $student_groups = [];
                foreach ($students as $student) {
                    $student_groups[$student->id] = (int)$student->participant_group_id;
                }
                $files = scandir($path_to_task_set_files);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && preg_match(self::STUDENT_FILE_NAME_REGEXP, $file, $matches)) {
                        $student_id = (int)$matches['student_id'];
                        $path = ($subdirectory !== null && trim($subdirectory) !== '' ? $subdirectory . '/' : '') . $group_names[$student_groups[$student_id]] . '/' . $file;
                        $zip_archive->addFile($path_to_task_set_files . $file, $path);
                    }
                }
            }
        }
    }
    
    public function get_task_set_time_class($permission_id = null): string
    {
        $task_set_type = $this->get_related_task_set_type();
        
        if ($this->content_type === 'task_set' && (!isset($task_set_type->upload_solution) || !$task_set_type->upload_solution)) {
            return '';
        }
        
        $permissions = $this->get_related_permissions();
        
        $upload_end_time = null;
        if (($permissions !== null) && (count($permissions) > 0)) {
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
    
    private function get_related_permissions()
    {
        if (is_null($this->id)) {
            return [];
        }
        if (!isset($this->related_permissions[$this->id])) {
            $task_set_permissions = new Task_set_permission();
            $task_set_permissions->where_related('task_set', 'id', $this->id);
            $task_set_permissions->where('enabled', 1);
            $task_set_permissions->get_iterated();
            
            $this->related_permissions[$this->id] = [];
            
            foreach ($task_set_permissions as $task_set_permission) {
                $this->related_permissions[$this->id][$task_set_permission->id] = (object)$task_set_permission->to_array();
            }
        }
        return $this->related_permissions[$this->id];
    }
    
    private function get_related_task_set_type()
    {
        if (is_null($this->id)) {
            return (object)[];
        }
        if (!isset($this->related_task_set_type[$this->id])) {
            $task_set_type = new Task_set_type();
            if (isset($this->course_id)) {
                $task_set_type->select();
                $task_set_type->select('course_task_set_type_rel.upload_solution');
                $task_set_type->where_related('task_set', 'id', $this->id);
                $task_set_type->where_related('course', 'id', $this->course_id);
                $task_set_type->limit(1);
                $task_set_type->get();
            }
            
            if ($task_set_type->exists()) {
                $this->related_task_set_type[$this->id] = (object)$task_set_type->to_array();
                $this->related_task_set_type[$this->id]->upload_solution = (bool)$task_set_type->upload_solution;
            } else {
                $this->related_task_set_type[$this->id] = (object)[];
            }
        }
        
        return $this->related_task_set_type[$this->id];
    }
    
    /**
     * Returns unused file name for solution download.
     *
     * @return string file name with path.
     */
    private function get_new_solution_zip_filename(): string
    {
        $path = 'private/extracted_solutions/';
        $filename = '';
        $i = 0;
        do {
            $filename = 'task_set_solutions_' . date('U') . '_' . (string)($this->id ?? 'unknown') . ($i > 0 ? '_' . $i : '') . '.zip';
            $i++;
        } while (file_exists($path . $filename));
        return $path . $filename;
    }
    
    /**
     * Hide updated field so it will not be changed during update.
     */
    public function hide_updated_field(): void
    {
        if (($key = array_search('updated', $this->fields, true)) !== false) {
            unset($this->fields[$key]);
            self::$updated_field_key = $key;
        }
    }
    
    /**
     * Show updated field so it will be changed during update.
     * Must be hidden first.
     */
    public function show_updated_field(): void
    {
        if (($key = array_search('updated', $this->fields, true)) === false && !is_null(self::$updated_field_key)) {
            array_splice($this->fields, self::$updated_field_key, 0, ['updated']);
        }
    }
}
