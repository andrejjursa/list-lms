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
        $this->_add_tinymce();
        $this->parser->add_js_file('tasks/form.js');
        $this->parser->parse('backend/tasks/new_task.tpl', array('structure' => $structure));
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task[name]', 'lang:admin_tasks_form_field_name', 'required');
        $this->form_validation->set_rules('task[text]', 'lang:admin_tasks_form_field_text', 'required');
        $this->form_validation->set_rules('task[categories][]', 'lang:admin_tasks_form_field_categories', 'required');
        
        if ($this->form_validation->run()) {
            $task_data = $this->input->post('task');
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $categories = new Category();
            $categories->where_in('id', $task_data['categories']);
            $categories->get();
            
            $task = new Task();
            $task->from_array($task_data, array('name', 'text'));
            if ($task->save($categories->all) && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_tasks_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_tasks_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            
            redirect(create_internal_url('admin_tasks'));
        } else {
            $this->new_task();
        }
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