<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('usermanager');
        $this->usermanager->set_student_data_to_smarty();
    }
    
    protected function init_language_for_student() {
        $this->lang->load(strtolower(get_class($this)), $this->usermanager->get_student_language());
    }
    
    protected function init_language_for_teacher() {
        
    }
    
}