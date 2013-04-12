<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends MY_Controller {
    
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
        $this->parser->parse('backend/tasks/index.tpl');
    }
    
    public function get_all_tasks() {
        $tasks = new Task();
        $tasks->order_by('name', 'asc');
        $tasks->get_paged_iterated(1, 50);
        $this->parser->parse('backend/tasks/all_tasks.tpl', array('tasks' => $tasks));
    }
}