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
        
        $this->form_validation->set_rules('student[email]', 'lang:students_login_field_email', 'required|valid_email');
        $this->form_validation->set_rules('student[password]', 'lang:students_login_field_password', 'required'); 
        
        if ($this->form_validation->run()) {
            $this->load->library('usermanager');
            $student_data = $this->input->post('student');
            if ($this->usermanager->authenticate_student_login($student_data['email'], $student_data['password'])) {
                echo 'login OK';
            } else {
                echo 'login FAILED';
                $this->login();
            }
        } else {
            $this->login();
        }
    }
    
    public function logout() {
        $this->load->library('usermanager');
        $this->usermanager->do_student_logout();
        $this->parser->parse('frontend/students/logout.tpl');
    }
    
    public function registration() {
        $this->parser->parse('frontend/students/registration.tpl');
    }
    
    public function do_registration() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[fullname]', 'lang:students_registration_validation_field_fullname', 'required');
        $this->form_validation->set_rules('student[email]', 'lang:students_registration_validation_field_email', 'required|valid_email');
        $this->form_validation->set_rules('student[password]', 'lang:students_registration_validation_field_password', 'required');
        $this->form_validation->set_rules('student[password_verification]', 'lang:students_registration_validation_field_password_verification', 'required|matches[student[password]]');
        if ($this->form_validation->run()) {
            
        } else {
            $this->registration();
        }
    }
}
