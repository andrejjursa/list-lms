<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends MY_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_tasks_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('tasks');
        $this->parser->add_js_file('tasks_api.js');
        $this->inject_stored_filter();
        $this->parser->parse('backend/tasks/index.tpl');
    }
    
    public function get_all_tasks() {
        $tasks = new Task();
        $tasks->order_by('name', 'asc');
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $tasks->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->parser->parse('backend/tasks/all_tasks.tpl', array('tasks' => $tasks));
    }
    
    public function new_task() {
        $this->_load_teacher_langfile('categories');
        $this->_select_teacher_menu_pagetag('tasks');
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->parser->parse('backend/tasks/new_task.tpl', array('structure' => $structure));
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
}