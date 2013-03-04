<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Students extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->init_language_for_student();
    }
    
    public function index() {
        $this->usermanager->student_login_protected_redirect(TRUE);
        echo 'OK, si prihlaseny!!!<br />';
        echo '<a href="' . create_internal_url('students/logout') . '">Odhlasit sa</a>';
    }
    
    public function login() {
        $uri_params = $this->uri->uri_to_assoc(3);
        $this->parser->parse('frontend/students/login.tpl', array('uri_params' => $uri_params));
    }
    
    public function do_login() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[email]', 'lang:students_login_field_email', 'required|valid_email');
        $this->form_validation->set_rules('student[password]', 'lang:students_login_field_password', 'required'); 
        
        if ($this->form_validation->run()) {
            $this->load->library('usermanager');
            $student_data = $this->input->post('student');
            if ($this->usermanager->authenticate_student_login($student_data['email'], $student_data['password'])) {
                $uri_params = $this->uri->uri_to_assoc(3);
                if (isset($uri_params['current_url'])) {
                    redirect(decode_from_url($uri_params['current_url']));
                } else {
                    redirect(create_internal_url('students/index'));
                }
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
        $this->form_validation->set_rules('student[email]', 'lang:students_registration_validation_field_email', 'required|valid_email|is_unique[students.email]');
        $this->form_validation->set_rules('student[password]', 'lang:students_registration_validation_field_password', 'required');
        $this->form_validation->set_rules('student[password_verification]', 'lang:students_registration_validation_field_password_verification', 'required|matches[student[password]]');
        if ($this->form_validation->run()) {
            $student_array = $this->input->post('student');
            $student_array['password'] = sha1($student_array['password']);
            $student = new Student();
            $student->from_array($student_array, array('fullname', 'email', 'password'));
            $student->language = $this->config->item('language');
            $student->trans_begin();
            $student->save();
            if ($student->trans_status()) {
                $student->trans_commit();
                redirect(create_internal_url('students/registered'));
            } else {
                $student->trans_rollback();
                $this->parser->assign('save_error', TRUE);
                $this->registration();
            }
        } else {
            $this->registration();
        }
    }
    
    public function registered() {
        
    }
}
