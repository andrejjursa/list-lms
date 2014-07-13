<?php

/**
 * MOSS controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class moss extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_moss_filter_data';
    
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
        
        $this->parser->add_css_file('admin_moss.css');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_moss/moss.js');
        
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
    
    private function inject_courses() {
        $courses = new Course();
        $courses->include_related('period', 'name');
        $courses->order_by_related_with_constant('period', 'sorting', 'asc');
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
    
}