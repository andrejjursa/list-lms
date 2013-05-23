<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Students controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Students extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->_initialize_student_menu();
    }
    
    public function index() {
        $this->usermanager->student_login_protected_redirect(TRUE);
        echo 'OK, si prihlaseny!!!<br />';
        echo '<a href="' . create_internal_url('students/logout') . '">Odhlasit sa</a>';
    }
    
    /**
     * Display login form for student.
     */
    public function login() {
        if ($this->usermanager->is_student_session_valid()) {
            $uri_params = $this->uri->uri_to_assoc(3);
            if (isset($uri_params['current_url'])) {
                redirect(decode_from_url($uri_params['current_url']));
            } else {
                $redirects = $this->config->item('after_login_redirects');
                redirect(create_internal_url($redirects['student']));
            }
        }
        $this->_select_student_menu_pagetag('test3');
        $uri_params = $this->uri->uri_to_assoc(3);
        $this->parser->parse('frontend/students/login.tpl', array('uri_params' => $uri_params));
    }
    
    /**
     * Performs student login authentification and redirects him to desired url.
     */
    public function do_login() {
        $uri_params = $this->uri->uri_to_assoc(3);
        if ($this->usermanager->is_student_session_valid()) {
            if (isset($uri_params['current_url'])) {
                redirect(decode_from_url($uri_params['current_url']));
            } else {
                $redirects = $this->config->item('after_login_redirects');
                redirect(create_internal_url($redirects['student']));
            }
        }
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[email]', 'lang:students_login_field_email', 'required|valid_email');
        $this->form_validation->set_rules('student[password]', 'lang:students_login_field_password', 'required|min_length[6]|max_length[20]');
        
        if ($this->form_validation->run()) {
            $student_data = $this->input->post('student');
            if ($this->usermanager->is_login_attempts_exceeded($student_data['email'], Usermanager::ACCOUNT_TYPE_STUDENT)) {
                $message = sprintf($this->lang->line('students_login_error_attempts_exceeded'), $this->config->item('student_login_security_allowed_attempts'), $this->config->item('student_login_security_timeout'));
                $this->parser->assign('general_error', $message);
                $this->login();
            } else {
                if ($this->usermanager->authenticate_student_login($student_data['email'], $student_data['password'])) {
                    $this->messages->add_message('lang:students_login_successful', Messages::MESSAGE_TYPE_SUCCESS);
                    if (isset($uri_params['current_url'])) {
                        redirect(decode_from_url($uri_params['current_url']));
                    } else {
                        $redirects = $this->config->item('after_login_redirects');
                        redirect(create_internal_url($redirects['student']));
                    }
                } else {
                    $this->parser->assign('general_error', $this->lang->line('students_login_error_bad_email_or_password'));
                    $this->login();
                }
            }
        } else {
            $this->login();
        }
    }
    
    /**
     * Logs out student account.
     */
    public function logout() {
        $this->usermanager->do_student_logout();
        $this->messages->add_message('lang:students_logout_logout_message', Messages::MESSAGE_TYPE_SUCCESS);
        $redirects = $this->config->item('login_redirects');
        redirect(create_internal_url($redirects['student']));
    }
    
    /**
     * Display registration form for student account registration.
     */
    public function registration() {
        if ($this->usermanager->is_student_session_valid()) {
            $redirects = $this->config->item('after_login_redirects');
            redirect(create_internal_url($redirects['student']));
        }
        $this->parser->parse('frontend/students/registration.tpl');
    }
    
    /**
     * Performs student account registration and redirects him to students/registered controller action.
     */
    public function do_registration() {
        if ($this->usermanager->is_student_session_valid()) {
            $redirects = $this->config->item('after_login_redirects');
            redirect(create_internal_url($redirects['student']));
        }
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
    
    public function my_account() {
        $this->usermanager->student_login_protected_redirect();
        $this->_select_student_menu_pagetag('student_account');
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $languages_available = $this->lang->get_list_of_languages();
        
        $this->parser->parse('frontend/students/my_account.tpl', array('student' => $student, 'languages' => $languages_available));
    }
    
    public function save_basic_information() {
        $this->usermanager->student_login_protected_redirect();
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[fullname]', 'lang:students_my_account_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('student[language]', 'lang:students_my_account_field_language', 'required');
        $this->form_validation->set_rules('student_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            $student_id = intval($this->input->post('student_id'));
            if ($student_id == $this->usermanager->get_student_id()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $student = new Student();
                $student->get_by_id($student_id);
                if ($student->exists()) {
                    $student->from_array($this->input->post('student'), array('fullname', 'language'));
                    if ($student->save() && $this->db->trans_status()) {
                        $this->messages->add_message('lang:students_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->db->trans_commit();
                        $this->usermanager->refresh_student_userdata();
                    } else {
                        $this->messages->add_message('lang:students_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
                        $this->db->trans_rollback();
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:students_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:students_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('students/my_account'));
        } else {
            $this->my_account();
        }
    }
    
    public function save_password() {
        $this->usermanager->student_login_protected_redirect();
        $this->load->library('form_validation');
        
        $student_id = intval($this->input->post('student_id'));
        $this->form_validation->set_rules('student[password_old]', 'lang:students_my_account_field_old_password', 'required|callback__validate_old_password[' . $student_id . ']');
        $this->form_validation->set_rules('student[password]', 'lang:students_my_account_field_password', 'required|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('student[password_validation]', 'lang:students_my_account_field_password_validation', 'required|matches[student[password]]');
        $this->form_validation->set_rules('student_id', 'id', 'required');
        $this->form_validation->set_message('_validate_old_password', $this->lang->line('students_my_account_field_old_password_error_message'));
        
        if ($this->form_validation->run()) {
            if ($student_id == $this->usermanager->get_student_id()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $student = new Student();
                $student->get_by_id($student_id);
                if ($student->exists()) {
                    $student_post = $this->input->post('student');
                    $student->password = sha1($student_post['password']);
                    if ($student->save() && $this->db->trans_status()) {
                        $this->messages->add_message('lang:students_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->db->trans_commit();
                    } else {
                        $this->messages->add_message('lang:students_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
                        $this->db->trans_rollback();
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:students_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:students_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('students/my_account'));
        } else {
            $this->my_account();
        }
    }
    
    public function _validate_old_password($str, $student_id) {
        $student = new Student();
        $student->where('password', sha1($str));
        $student->get_by_id(intval($student_id));
        return $student->exists();
    }
    
    public function save_email() {
        $this->usermanager->student_login_protected_redirect();
        $this->load->library('form_validation');
        
        $student_id = intval($this->input->post('student_id'));
        $this->form_validation->set_rules('student[email]', 'lang:students_my_account_field_email', 'required|valid_email|is_unique[students.email]');
        $this->form_validation->set_rules('student[email_validation]', 'lang:students_my_account_field_email_validation', 'required|matches[student[email]]');
        $this->form_validation->set_rules('student_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            if ($student_id == $this->usermanager->get_student_id()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $student = new Student();
                $student->get_by_id($student_id);
                if ($student->exists()) {
                    $student_post = $this->input->post('student');
                    $student->email = $student_post['email'];
                    if ($student->save() && $this->db->trans_status()) {
                        $this->messages->add_message('lang:students_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->db->trans_commit();
                    } else {
                        $this->messages->add_message('lang:students_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
                        $this->db->trans_rollback();
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:students_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:students_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('students/my_account'));
        } else {
            $this->my_account();
        }
    }
}
