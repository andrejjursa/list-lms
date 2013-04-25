<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Task sets controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Task_sets extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->_init_language_for_teacher();
		$this->_load_teacher_langfile();
		$this->_initialize_teacher_menu();
		$this->usermanager->teacher_login_protected_redirect();
	}
	
	public function index() {
		$this->_select_teacher_menu_pagetag('task_sets');
		$this->parser->parse('backend/task_sets/index.tpl');
	}
	
}