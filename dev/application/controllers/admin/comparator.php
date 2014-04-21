<?php

/**
 * Comparator controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Comparator extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_comparator_filter_data';
    
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
        
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_comparator/task_set_selection.js');
        
        $this->inject_courses();
        $this->inject_all_task_sets();
        
        $this->inject_stored_filter();
        
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
