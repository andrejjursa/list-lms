<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    protected function init_language_for_student() {
        $this->load->library('login');
        $this->lang->load(strtolower(get_class($this)), $this->login->get_student_language());
    }
    
    protected function init_language_for_teacher() {
        
    }
    
}