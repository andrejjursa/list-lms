<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Solutions controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Solutions extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_solutions_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('solutions');
        $this->inject_stored_filter();
        $this->inject_courses();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/index.tpl');
    }
    
    public function solutions_list($task_set_id = NULL) {
        $this->_select_teacher_menu_pagetag('solutions');
        $task_set = new Task_set();
        $task_set->include_related('course', 'name');
        $task_set->include_related('course/period', 'name');
        $task_set->get_by_id($task_set_id);
        
        $this->inject_students($task_set_id);
        $this->parser->add_js_file('admin_solutions/solutions_list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/solutions_list.tpl', array('task_set' => $task_set));
    }

    public function get_task_set_list() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $task_sets = new Task_set();
        $task_sets->select('`task_sets`.*, `course_course_task_set_type_rel`.`upload_solution` AS `join_upload_solution`');
        $task_sets->include_related_count('solution');
        $task_sets->include_related_count('task');
        $task_sets->include_related('course', 'name');
        $task_sets->include_related('course/period', 'name');
        $task_sets->include_related('group', 'name');
        $task_sets->include_related('task_set_type', 'name');
        $task_sets->include_related('course/task_set_type');
        $task_sets->where('(`course_task_set_types`.`id` = `task_sets`.`task_set_type_id`)');
        if (isset($filter['course']) && intval($filter['course']) > 0) {
            $task_sets->where_related_course('id', intval($filter['course']));
        }
        if (isset($filter['group']) && intval($filter['group']) > 0) {
            $task_sets->where_related_group('id', intval($filter['group']));
        }
        $task_sets->order_by_related('course/period', 'sorting', 'asc');
        $task_sets->order_by_related_with_constant('course', 'name', 'asc');
        $task_sets->order_by_with_overlay('name', 'asc');
        $task_sets->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), array('name'));
        $this->parser->parse('backend/solutions/task_set_list', array('task_sets' => $task_sets));
    }
    
    public function get_solutions_list_for_task_set($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $solutions = new Solution();
        if ($task_set->exists()) {
            $solutions->where_related($task_set);
            $solutions->include_related('student');
            $solutions->include_related('teacher');
            $solutions->get_iterated();
        }
        
        $this->parser->parse('backend/solutions/solutions_list_table_content.tpl', array('task_set' => $task_set, 'solutions' => $solutions));
    }
    
    public function get_groups_from_course($course_id, $selected_id = NULL) {
        $groups = new Group();
        $groups->select('id, name');
        $groups->where_related_course('id', $course_id);
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        $options = array(
            '' => ''
        );
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $this->parser->parse('backend/solutions/groups_from_course.tpl', array('groups' => $options, 'selected' => $selected_id));
    }
    
    private function store_filter($filter) {
        if (is_array($filter)) {
            $old_filter = $this->session->userdata(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->session->set_userdata(self::STORED_FILTER_SESSION_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter() {
        $filter = $this->session->userdata(self::STORED_FILTER_SESSION_NAME);
        $this->parser->assign('filter', $filter);
    }
    
    private function inject_courses() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = array( NULL => '' );
        if ($periods->exists()) { foreach ($periods as $period) {
            $period->course->order_by_with_constant('name', 'asc')->get_iterated();
            if ($period->course->exists() > 0) { foreach ($period->course as $course) {
                $data[$period->name][$course->id] = $course->name;
            }}
        }}
        $this->parser->assign('courses', $data);
    }
    
    public function inject_students($task_set_id) {
        $students = new Student();
        $students->where_related('participant', 'allowed', 1);
        $students->where_related('participant/course/task_set', 'id', $task_set_id);
        $students->where('students.id = `students`.`id` AND NOT EXISTS (SELECT * FROM `solutions` WHERE `solutions`.`student_id` = `students`.`id` AND `solutions`.`task_set_id` = ' . intval($task_set_id) . ')');
        $students->group_by('id');
        $students->order_by('fullname', 'asc');
        $students->order_by('email', 'asc');
        $students->get_iterated();
        
        $data = array('' => '');
        foreach ($students as $student) {
            $data[$student->id] = $student->fullname . ' (' . $student->email . ')';
        }
        $this->parser->assign('students', $data);
    }
}