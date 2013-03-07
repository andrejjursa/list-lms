<?php

class Teachers extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
    }
    
    public function index() {
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function login() {
        $uri_params = $this->uri->uri_to_assoc(3);
        $this->parser->parse('backend/teachers/login.tpl', array('uri_params' => $uri_params));
    }
    
    public function do_login() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('teacher[email]', 'lang:admin_teachers_login_field_email', 'required|valid_email');
        $this->form_validation->set_rules('teacher[password]', 'lang:admin_teachers_login_field_password', 'required|min_length[6]|max_length[20]');
        
        if ($this->form_validation->run()) {
            $teacher_data = $this->input->post('teacher');
            if ($this->usermanager->authenticate_teacher_login($teacher_data['email'], $teacher_data['password'])) {
                $uri_params = $this->uri->uri_to_assoc(3);
                if (isset($uri_params['current_url'])) {
                    redirect(decode_from_url($uri_params['current_url']));
                } else {
                    $redirects = $this->config->item('after_login_redirects');
                    redirect(create_internal_url($redirects['teacher']));
                }
            } else {
                $this->parser->assign('general_error', $this->lang->line('admin_teachers_login_error_bad_email_or_password'));
                $this->login();
            }
        } else {
            $this->login();
        }
    }
    
    public function logout() {
        $this->usermanager->do_teacher_logout();
    }
    
}