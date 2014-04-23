<?php

/**
 * Comparator controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Comparator extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_comparator_filter_data';
    const SECONDS_TO_BE_CONSIDERED_OLD = 21600; // 6 hours
    const COMPARATOR_WORKING_DIRECTORY = 'public/comparator/';
    
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
        $this->_select_teacher_menu_pagetag('comparator');
        
        $this->parser->add_css_file('admin_comparator.css');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_comparator/comparator.js');
        
        $this->inject_courses();
        $this->inject_all_task_sets();
        
        $this->inject_stored_filter();
        
        $this->clear_old_reports();
        
        $this->parser->parse('backend/comparator/index.tpl');
    }
    
    public function list_solutions() {
        $task_sets_setup_data = $this->input->post('task_sets_setup');
        
        $this->store_filter($task_sets_setup_data);
        
        $course = new Course();
        $course->get_by_id((int)$task_sets_setup_data['course']);
        
        $task_set = new Task_set();
        $task_set->where_related($course);
        $task_set->get_by_id(isset($task_sets_setup_data['task_set']) ? (int)$task_sets_setup_data['task_set'] : 0);
        
        $this->parser->assign(array(
            'course' => $course,
            'task_set' => $task_set,
        ));
        
        if ($course->exists() && $task_set->exists()) {
            $solutions = new Solution();
            $solutions->include_related('student');
            $solutions->where_related($task_set);
            $solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
            $solutions->get_iterated();
            $this->parser->assign('solutions', $solutions);
        }
        
        $this->parser->parse('backend/comparator/list_solutions.tpl');
    }
    
    public function run_comparation() {
        $task_sets_setup_data = $this->input->post('task_sets_setup');
        $solutions_data = $this->input->post('solutions');
        $comparator_setup_data = $this->input->post('comparator_setup');
        
        $course = new Course();
        $course->get_by_id((int)$task_sets_setup_data['course']);
        
        $task_set = new Task_set();
        $task_set->where_related($course);
        $task_set->get_by_id(isset($task_sets_setup_data['task_set']) ? (int)$task_sets_setup_data['task_set'] : 0);
        
        $this->parser->assign(array(
            'course' => $course,
            'task_set' => $task_set,
        ));
        
        if ($course->exists() && $task_set->exists()) {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('solutions', 'lang:admin_comparator_list_solutions_form_field_solution_selection', 'callback__selected_solutions');
            $this->form_validation->set_rules('comparator_setup[threshold]', 'lang:admin_comparator_list_solutions_form_field_threshold', 'required|numeric|greater_than_equal[0]|less_than_equal[1]');
            $this->form_validation->set_rules('comparator_setup[min_tree_size]', 'lang:admin_comparator_list_solutions_form_field_min_tree_size', 'required|integer|greater_than_equal[1]');
            $this->form_validation->set_rules('comparator_setup[max_cutted_tree_size]', 'lang:admin_comparator_list_solutions_form_field_max_cutted_tree_size', 'required|integer|greater_than_equal[1]');
            $this->form_validation->set_rules('comparator_setup[branching_factor]', 'lang:admin_comparator_list_solutions_form_field_branching_factor', 'required|integer|greater_than_equal[1]');
            $this->form_validation->set_rules('comparator_setup[min_similarity]', 'lang:admin_comparator_list_solutions_form_field_minimum_similarity', 'required|numeric|greater_than_equal[0]|less_than_equal[1]');
            
            $this->form_validation->set_message('_selected_solutions', $this->lang->line('admin_comparator_list_solutions_validation_callback_selected_solutions'));
            if ($this->form_validation->run()) {
                $solutions = new Solution();
                $solutions->include_related('student');
                $solutions->where_related($task_set);
                $solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
                $solutions->get_iterated();
                $this->parser->assign('solutions', $solutions);
                
                $path = $this->get_random_hash_folder($course->id, $task_set->id);
                
                $path_source = $path . '/source';
                $path_output = $path . '/output';
                
                @mkdir($path_source, DIR_WRITE_MODE, TRUE);
                @mkdir($path_output, DIR_WRITE_MODE, TRUE);
                
                $all_extracted = TRUE;
                
                foreach ($solutions_data as $id => $config) {
                    if (isset($config['selected']) && $config['selected'] == 1) {
                        set_time_limit(120);
                        $version = isset($config['version']) ? $config['version'] : 0;
                        $student = isset($config['student']) ? $config['student'] : 0;
                        $file = $task_set->get_student_files($student, $version);
                        if (count($file) == 1) {
                            $file = $file[$version];
                            $subdir = '/' . $file['file_name'] . '_sid-' . $file['student_id'] . '_ver-' . $file['version'];
                            $extract_path = $path_source . $subdir;
                            @mkdir($extract_path, DIR_WRITE_MODE, TRUE);
                            $status = $task_set->extract_student_zip_to_folder($file['file'], $extract_path, array('java'));
                            $all_extracted = $all_extracted && $status;
                        }
                    }
                }
                
                if (!$all_extracted) {
                    unlink_recursive($path, TRUE);
                }
                
                $this->parser->assign('all_extracted', $all_extracted);
                $this->parser->assign('path', $path);
                
                $this->parser->assign('comparator_config', array(
                    't' => $comparator_setup_data['threshold'],
                    'm' => $comparator_setup_data['min_tree_size'],
                    'cut' => $comparator_setup_data['max_cutted_tree_size'],
                    'bf' => $comparator_setup_data['branching_factor'],
                    'mp' => $comparator_setup_data['min_similarity'],
                ));
                
                $this->parser->parse('backend/comparator/run_comparation.tpl');
            } else {
                $this->list_solutions();
            }
        } else {
            $this->list_solutions();
        }
    }
    
    public function execute() {
        set_time_limit(0);
        $config = $this->input->post('config');
        $path = $this->input->post('path');
        
        $exec_path = rtrim(getcwd(), '\\/') . '/';
        $exec_path .= 'comparator/';
        
        if (file_exists($exec_path . ENVIRONMENT . '/run')) {
            $exec_path .= ENVIRONMENT . '/';
        }
        
        $execute_command = $exec_path . 'run ' . $path . ' ' . $config['t'] . ' ' . $config['m'] . ' ' . $config['cut'] . ' ' . $config['bf'] . ' ' . $config['mp'];
        
        @exec($execute_command, $exec_output, $return_var);
        
        $output = '';
        if (file_exists($path . '/output/protocol.txt')) {
            $f = fopen($path . '/output/protocol.txt', 'r');
            while (!feof(($f))) {
                $output .= fread($f, 1024);
            }
            fclose($f);
        }
        
        echo '<pre>';
        print_r($output);
        echo '</pre>';
        echo '<p><a href="' . base_url($path . '/output/main.html') . '" class="button" target="_blank">' . $this->lang->line('admin_comparator_execute_button_open_report') . '</a></p>';
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
    
    private function clear_old_reports() {
        $path = self::COMPARATOR_WORKING_DIRECTORY;
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
    
    private function get_random_hash_folder($course, $task_set) {
        $path = self::COMPARATOR_WORKING_DIRECTORY;
        $path = ltrim($path, '\\/') . '/';
        
        $folder_name = '';
        
        do {
            $hash = md5(date('U') . '-' . rand(10000000, 99999999) . '-' . $course . '-' . $task_set);
            $folder_name = $course . '_' . $task_set . '_' . $hash;
        } while (file_exists($path . $folder_name));
        
        @mkdir($path, DIR_WRITE_MODE, TRUE);
        
        return $path . $folder_name;
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
    
}
