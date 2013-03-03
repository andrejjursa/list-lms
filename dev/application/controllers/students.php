<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Students extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->init_language_for_student();
    }
    
    public function index() {
    }
    
    public function login() {
        $this->parser->parse('frontend/students/login.tpl');
    }
    
    public function do_login() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[email]', $this->lang->line('students_login_field_email'), 'required');
        $this->form_validation->set_rules('student[password]', $this->lang->line('students_login_field_password'), 'required'); 
        
        if ($this->form_validation->run()) {
            $this->load->library('login');
            $student_data = $this->input->post('student');
            if ($this->login->authenticate_student_login($student_data['email'], $student_data['password'])) {
                echo 'login OK';
            } else {
                echo 'login FAILED';
                $this->login();
            }
        } else {
            $this->login();
        }
    }
}
