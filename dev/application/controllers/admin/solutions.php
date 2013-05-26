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
        
    }

    public function get_task_set_list() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $task_sets = new Task_set();
        $task_sets->include_related_count('solution');
        $task_sets->include_related_count('task');
        $task_sets->include_related('course', 'name');
        $task_sets->include_related('course/period', 'name');
        $task_sets->include_related('group', 'name');
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
}