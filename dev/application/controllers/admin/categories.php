<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('categories');
        $this->parser->parse('backend/categories/index.tpl');
    }
    
}