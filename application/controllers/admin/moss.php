<?php

/**
 * MOSS controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class moss extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_moss_filter_data';
    const MOSS_WORKING_DIRECTORY = 'private/moss/';
    const SECONDS_TO_BE_CONSIDERED_OLD = 21600; // 6 hours
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('moss');
        
        $this->inject_stored_filter();

        $this->inject_courses();
        $this->inject_all_task_sets();

        $this->clear_old_directories();

        $this->parser->add_css_file('admin_moss.css');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_moss/moss.js');
        
        $this->parser->assign('moss_enabled', $this->is_moss_user_id_set());

        $this->parser->parse('backend/moss/index.tpl');
    }
    
    public function list_solutions() {
        $task_sets_setup_data = $this->input->post('task_sets_setup');
        
        $this->store_filter($task_sets_setup_data);
        
        $course = new Course();
        $course->get_by_id((int)$task_sets_setup_data['course']);
        
        $task_set = new Task_set();
        $task_set->where_related($course);
        $task_set->get_by_id(isset($task_sets_setup_data['task_set']) ? (int)$task_sets_setup_data['task_set'] : 0);
        
        $this->config->load('moss');
        
        $languages = $this->config->item('moss_langs_for_list');
        asort($languages);
        
        $this->parser->assign(array(
            'course' => $course,
            'task_set' => $task_set,
            'languages' => $languages,
        ));
        
        $this->parser->assign('moss_enabled', $this->is_moss_user_id_set());
        
        if ($course->exists() && $task_set->exists()) {
            $solutions = new Solution();
            $solutions->include_related('student');
            $solutions->where_related($task_set);
            $solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
            $solutions->get_iterated();
            $this->parser->assign('solutions', $solutions);
            
            $tasks = new Task();
            $tasks->where_related($task_set);
            $tasks->get_iterated();
            
            $base_files_list = array();
            
            foreach ($tasks as $task) {
                $base_files_list[$task->id] = array(
                    'task_id' => $task->id,
                    'task_name' => $this->lang->get_overlay_with_default('tasks', $task->id, 'name', $task->name),
                    'files' => $this->construct_base_files_for_task($task->id),
                );
            }
            
            $this->parser->assign('base_files_list', $base_files_list);
        }
        
        $this->parser->parse('backend/moss/list_solutions.tpl');
    }
    
    public function run_comparation() {
        $task_sets_setup_data = $this->input->post('task_sets_setup');
        $solutions_data = $this->input->post('solutions');
        $moss_setup_data = $this->input->post('moss_setup');
        $moss_base_files_data = $this->input->post('moss_base_files');
        
        $course = new Course();
        $course->get_by_id((int)$task_sets_setup_data['course']);
        
        $task_set = new Task_set();
        $task_set->where_related($course);
        $task_set->get_by_id(isset($task_sets_setup_data['task_set']) ? (int)$task_sets_setup_data['task_set'] : 0);
        
        $this->config->load('moss');
        
        $languages = $this->config->item('moss_langs_for_list');
        asort($languages);
        
        $this->parser->assign(array(
            'course' => $course,
            'task_set' => $task_set,
            'languages' => $languages,
        ));
        
        $this->parser->assign('moss_enabled', $this->is_moss_user_id_set());
        
        if ($course->exists() && $task_set->exists()) {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('solutions', 'lang:admin_moss_list_solutions_form_field_solution_selection', 'callback__selected_solutions');
            $this->form_validation->set_rules('moss_setup[l]', 'lang:admin_moss_list_solutions_form_field_language', 'required');
            $this->form_validation->set_rules('moss_setup[m]', 'lang:admin_moss_list_solutions_form_field_sensitivity', 'required|integer|greater_than[1]');
            $this->form_validation->set_rules('moss_setup[n]', 'lang:admin_moss_list_solutions_form_field_matching_files', 'required|integer|greater_than[1]');
            
            $this->form_validation->set_message('_selected_solutions', $this->lang->line('admin_moss_list_solutions_validation_callback_selected_solutions'));
            if ($this->form_validation->run() && $this->is_moss_user_id_set()) {
                $solutions = new Solution();
                $solutions->include_related('student');
                $solutions->where_related($task_set);
                $solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
                $solutions->get_iterated();
                $this->parser->assign('solutions', $solutions);
                
                $tasks = new Task();
                $tasks->where_related($task_set);
                $tasks->get_iterated();

                $base_files_list = array();

                foreach ($tasks as $task) {
                    $base_files_list[$task->id] = array(
                        'task_id' => $task->id,
                        'task_name' => $this->lang->get_overlay_with_default('tasks', $task->id, 'name', $task->name),
                        'files' => $this->construct_base_files_for_task($task->id),
                    );
                }
                
                $this->parser->assign('base_files_list', $base_files_list);
                
                $path = $this->get_random_hash_folder($course->id, $task_set->id);
                
                $path_source = $path . '/source';
                $path_base = $path . '/base';
                
                @mkdir($path_source, DIR_WRITE_MODE, TRUE);
                @mkdir($path_base, DIR_WRITE_MODE, TRUE);
                
                $all_extracted = TRUE;
                
                $moss_langs_extensions = $this->config->item('moss_langs_file_extensions');
                
                if (isset($moss_langs_extensions[$moss_setup_data['l']])) {
                    $file_extensions = $moss_langs_extensions[$moss_setup_data['l']];
                } else {
                    $all_extracted = FALSE;
                }
                
                foreach ($solutions_data as $id => $config) {
                    if (!$all_extracted) { break; }
                    if (isset($config['selected']) && $config['selected'] == 1) {
                        set_time_limit(120);
                        $version = isset($config['version']) ? $config['version'] : 0;
                        $student = isset($config['student']) ? $config['student'] : 0;
                        $file = $task_set->get_student_files($student, $version);
                        if (count($file) == 1) {
                            $file = $file[$version];
                            $subdir = '/' . normalize($file['file_name']) . '_sid-' . $file['student_id'] . '_ver-' . $file['version'];
                            $extract_path = $path_source . $subdir;
                            @mkdir($extract_path, DIR_WRITE_MODE, TRUE);
                            $status = $task_set->extract_student_zip_to_folder($file['file'], $extract_path, $file_extensions);
                            $all_extracted = $all_extracted && $status;
                        }
                    }
                }
                
                if (is_array($moss_base_files_data) && count($moss_base_files_data)) { foreach ($moss_base_files_data as $task_id => $path_array) {
                    if (!$all_extracted) { break; }
                    if (is_array($path_array) && count($path_array)) { foreach ($path_array as $path_hash => $file_path) {
                        if (!$all_extracted) { break; }
                        if (preg_match('/\.zip(?P<indexNumberBox>\[(?P<zipIndexNumber>[0-9]+)\])$/', $file_path, $matches)) {
                            $zipfile = mb_substr($file_path, 0, mb_strlen($file_path) - mb_strlen($matches['indexNumberBox']));
                            $zipindex = (int)$matches['zipIndexNumber'];
                            $zip = new ZipArchive();
                            if ($zip->open($zipfile)) {
                                set_time_limit(120);
                                @mkdir($path_base . '/' . $task_id . '/' . $path_hash, DIR_WRITE_MODE, TRUE);
                                if (!$zip->extractTo($path_base . '/' . $task_id . '/' . $path_hash, $zip->getNameIndex($zipindex))) {
                                    $all_extracted = FALSE;
                                }
                                $zip->close();
                            } else {
                                $all_extracted = FALSE;
                            }
                        } else {
                            set_time_limit(120);
                            @mkdir($path_base . '/' . $task_id . '/' . $path_hash, DIR_WRITE_MODE, TRUE);
                            $path_info = pathinfo($file_path);
                            if (!copy($file_path, $path_base . '/' . $task_id . '/' . $path_hash . '/' . $path_info['basename'])) {
                                $all_extracted = FALSE;
                            }
                        }
                    }}
                }}
                
                if (!$all_extracted) {
                    unlink_recursive($path, TRUE);
                }
                
                $this->parser->assign('all_extracted', $all_extracted);
                $this->parser->assign('path', $path);
                $this->parser->assign('moss_config', $moss_setup_data);
                
                $this->parser->parse('backend/moss/run_comparation.tpl');
            } else {
                $this->list_solutions();
            }
        }
    }
    
    public function execute() {
        set_time_limit(0);

        $path = $this->input->post('path');
        $config = $this->input->post('config');

        if ($this->is_moss_user_id_set()) {
            $this->load->library('mosslib');
            $this->load->helper('moss');

            $this->mosslib->setLanguage($config['l']);
            $this->mosslib->setIngoreLimit((int)$config['m']);
            $this->mosslib->setResultLimit((int)$config['n']);

            $this->load->config('moss');

            $moss_lang_file_extensions = $this->config->item('moss_langs_file_extensions');
            $extensions = $moss_lang_file_extensions[$config['l']];

            moss_add_all_files(rtrim($path, '/\\') . '/', 'source/', $extensions);
            moss_add_all_base_files(rtrim($path, '/\\') . '/', 'base/', $extensions);

            $current_path = getcwd();
            chdir($path);
            $results = $this->mosslib->send();
            chdir($current_path);

            echo '<a href="' . $results . '" class="button" target="_blank">' . $this->lang->line('admin_moss_execute_results_button_text') . '</a>';
        } else {
            echo '<p>' . $this->lang->line('admin_moss_general_error_user_id_not_set') . '</p>';
        }
        
        if (mb_substr($path, 0, mb_strlen(self::MOSS_WORKING_DIRECTORY)) == self::MOSS_WORKING_DIRECTORY && mb_strlen(self::MOSS_WORKING_DIRECTORY) < mb_strlen($path)) {
            @unlink_recursive($path, TRUE);
        }
    }
    
    public function _selected_solutions($solutions) {
        if (!is_array($solutions) || count($solutions) == 0) { return FALSE; }
        
        $output = FALSE;
        
        foreach ($solutions as $id => $config) {
            if (isset($config['selected']) && $config['selected'] == 1) {
                $output = TRUE;
                break;
            }
        }
        
        return $output;
    }
    
    private function get_random_hash_folder($course, $task_set) {
        $path = self::MOSS_WORKING_DIRECTORY;
        $path = ltrim($path, '\\/') . '/';
        
        $folder_name = '';
        
        do {
            $hash = md5(date('U') . '-' . rand(10000000, 99999999) . '-' . $course . '-' . $task_set);
            $folder_name = $course . '_' . $task_set . '_' . $hash;
        } while (file_exists($path . $folder_name));
        
        @mkdir($path . $folder_name, DIR_WRITE_MODE, TRUE);
        
        return $path . $folder_name;
    }
    
    private function inject_courses() {
        $courses = new Course();
        $courses->include_related('period', 'name');
        $courses->order_by_related('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name');
        $courses->get_iterated();
        
        $data = array();
        
        foreach ($courses as $course) {
            $data[$this->lang->text($course->period_name)][$course->id] = $this->lang->text($course->name);
        } 
        
        $this->parser->assign('courses', $data);
    }
    
    private function inject_all_task_sets() {
        $task_sets = new Task_set();
        $task_set_permissions = $task_sets->task_set_permission;
        
        $task_set_permissions->select_func('COUNT', '*', 'count');
        $task_set_permissions->where('enabled', 1);
        $task_set_permissions->where_related('task_set', 'id', '${parent}.id');
        
        $task_sets->select('*');
        $task_sets->include_related('group', 'name');
        $task_sets->select_subquery($task_set_permissions, 'task_set_permissions_count');
        $task_sets->order_by_with_overlay('name');
        $task_sets->get_iterated();
        
        $data = array();
        
        $this->lang->init_all_overlays('task_sets');
        
        foreach($task_sets as $task_set) {
            $text_groups = '';
            if ((int)$task_set->task_set_permissions_count > 0) {
                $task_set_permissions = new Task_set_permission();
                $task_set_permissions->include_related('group', 'name');
                $task_set_permissions->where('enabled', 1);
                $task_set_permissions->where_related_task_set($task_set);
                $task_set_permissions->order_by_related_with_constant('group', 'name', 'asc');
                $task_set_permissions->get_iterated();
                $groups = array();
                foreach ($task_set_permissions as $task_set_permission) {
                    $groups[] = $this->lang->text($task_set_permission->group_name);
                }
                if (count($groups) > 0) {
                    $text_groups = ' ... (' . implode(', ', $groups) . ')';
                }
            } elseif (!is_null($task_set->group_id) && (int)$task_set->group_id > 0) {
                $text_groups = ' ... (' . $this->lang->text($task_set->group_name) . ')';
            }
            $data[$task_set->course_id][] = array(
                'value' => $task_set->id,
                'text' => $this->lang->get_overlay_with_default('task_sets', $task_set->id, 'name', $task_set->name) . $text_groups
            );
        }
        
        $this->parser->assign('task_sets', $data);
    }
    
    private function store_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_FILTER_SESSION_NAME, 'course');
        }
    }
    
    private function inject_stored_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course');
        $this->parser->assign('filter', $filter);
    }
    
    private function construct_base_files_for_task($task_id) {
        $output = array();
        
        $base_path = 'private/uploads/task_files/task_' . (int)$task_id . '/';
        
        $this->load->config('moss');
        $ext_lists = $this->config->item('moss_langs_file_extensions');
        $extensions = array();
        if (is_array($ext_lists) && count($ext_lists)) { foreach ($ext_lists as $ext_list) {
            if (is_array($ext_list) && count($ext_list)) { foreach ($ext_list as $ext) {
                $extensions[] = strtolower($ext);
            }}
        }}
        
        $this->recursive_build_task_base_files($base_path, $base_path, $extensions, $output);
        
        return $output;
    }
    
    private function recursive_build_task_base_files($path, $base_path, $extensions, &$output) {
        $base_path_length = strlen($base_path);
        if (file_exists($path)) {
            if (is_dir($path)) {
                $dir_content = scandir($path);
                foreach ($dir_content as $dir_or_file) {
                    if ($dir_or_file != '.' && $dir_or_file != '..') {
                        $new_path = rtrim($path, '\\/') . DIRECTORY_SEPARATOR . $dir_or_file;
                        $this->recursive_build_task_base_files($new_path . (is_dir($new_path) ? DIRECTORY_SEPARATOR : ''), $base_path, $extensions, $output);
                    }
                }
            } else {
                $path_info = pathinfo($path);
                if (strtolower($path_info['extension']) == 'zip') {
                    $zip_archive = new ZipArchive();
                    if ($zip_archive->open($path)) {
                        for ($index = 0; $index < $zip_archive->numFiles; $index++) {
                            $file_name = $zip_archive->getNameIndex($index);
                            if (substr($file_name, -1) !== '/' && substr($file_name, -1) !== '\\') {
                                $zip_path_info = pathinfo($file_name);
                                if (in_array(strtolower($zip_path_info['extension']), $extensions)) {
                                    $output[$path . '[' . $index . ']'] = substr($path, $base_path_length) . ' : ' . $file_name;
                                }
                            }
                        }
                        $zip_archive->close();
                    }
                } else {
                    if (in_array($path_info['extension'], $extensions)) {
                        $output[$path] = substr($path, $base_path_length);
                    }
                }
            }
        }
    }
    
    private function clear_old_directories() {
        $path = self::MOSS_WORKING_DIRECTORY;
        $path = ltrim($path, '\\/') . '/';
        
        $directories = scandir($path);
        
        $current_time = time();
        
        if (is_array($directories) && count($directories) > 0) {
            foreach ($directories as $directory) {
                if (is_dir($path . $directory) && $directory != '.' && $directory != '..') {
                    $last_mod_time = filemtime($path . $directory);
                    if ($current_time - $last_mod_time >= self::SECONDS_TO_BE_CONSIDERED_OLD) {
                        unlink_recursive($path . $directory, TRUE);
                    }
                }
            }
        }
    }
    
    protected function is_moss_user_id_set() {
        $this->load->config('moss');
        return preg_match('/^[0-9]+$/', $this->config->item('moss_user_id')) && (int)$this->config->item('moss_user_id') > 0;
    }
    
}