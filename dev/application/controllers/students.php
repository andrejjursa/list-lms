<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Students controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Students extends LIST_Controller {
    
    private $student_registration_config = array();
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->_initialize_student_menu();
        $this->student_registration_config = $this->config->item('student_registration');
    }
        
    /**
     * Simple redirect to application default controller.
     */
    public function index() {
        redirect('/');
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
        $this->parser->add_js_file('students/login.js');
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
     * Displays password recovery form.
     */
    public function password_recovery() {
        $uri_params = $this->uri->uri_to_assoc(3);
        $this->parser->parse('frontend/students/password_recovery.tpl', array('uri_params' => $uri_params));
    }
    
    /**
     * Sends password recovery e-mail to student.
     */
    public function do_password_recovery() {
        $uri_params = $this->uri->uri_to_assoc(3);
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('student[email]', 'lang:student_password_recovery_form_field_email', 'required|valid_email');
        
        if ($this->form_validation->run()) {
            $student_post = $this->input->post('student');
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->get_by_email($student_post['email']);
            if ($student->exists()) {
                $student->password_token = sha1(time() . '-' . $this->config->item('encryption_key') . '-' . $_SERVER['SCRIPT_FILENAME'] . '-' . rand(1000000, 9999999));
                if ($student->save()) {
                    $this->db->trans_commit();
                    $this->load->library('email');
                    $this->email->from_system();
                    $this->email->reply_to_system();
                    $this->email->to($student->email);
                    $this->email->build_message_body('file:emails/frontend/students/password_recovery.tpl', array('student' => $student));
                    $this->email->subject('LIST - ' . $this->lang->line('students_password_recovery_email_body_subject'));
                    if ($this->email->send()) {
                        $this->messages->add_message('lang:students_password_recovery_email_sent', Messages::MESSAGE_TYPE_SUCCESS);
                    } else {
                        $this->messages->add_message('lang:students_password_recovery_email_sent_error', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:students_password_recovery_email_sent', Messages::MESSAGE_TYPE_SUCCESS);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:students_password_recovery_email_sent', Messages::MESSAGE_TYPE_SUCCESS);
            }
            
            redirect(create_internal_url('students/login/' . implode_uri_params($uri_params)));
        } else {
            $this->password_recovery();
        }
    }
    
    /**
     * This method will verify token and email and then displays form for password change.
     * @param string $token 40 characters length security token.
     * @param string $encoded_email encoded email address.
     */
    public function change_password($token, $encoded_email) {
        if ($this->usermanager->is_student_session_valid()) {
            $this->messages->add_message('lang:students_change_password_student_loged_in', Messages::MESSAGE_TYPE_ERROR);
            redirect('/');
        }
        $this->load->library('form_validation');
        $email = decode_from_url($encoded_email);
        if ($this->form_validation->valid_email($email) && preg_match('/^[0-9a-f]{40}$/', $token)) {
            $student = new Student();
            $student->where('password_token', $token);
            $student->where('email', $email);
            $student->get();
            if ($student->exists()) {
                $this->_init_language_for_student($student);
                $this->parser->parse('frontend/students/change_password.tpl', array('student' => $student, 'token' => $token, 'encoded_email' => $encoded_email));
            } else {
                $this->messages->add_message('lang:students_change_password_invalid_token_email', Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('students/login'));
            }
        } else {
            $this->messages->add_message('lang:students_change_password_invalid_token_email', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('students/login'));
        }
    }
    
    public function do_change_password($token, $encoded_email) {
        if ($this->usermanager->is_student_session_valid()) {
            $this->messages->add_message('lang:students_change_password_student_loged_in', Messages::MESSAGE_TYPE_ERROR);
            redirect('/');
        }
        $this->load->library('form_validation');
        $email = decode_from_url($encoded_email);
        if ($this->form_validation->valid_email($email) && preg_match('/^[0-9a-f]{40}$/', $token)) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->where('password_token', $token);
            $student->where('email', $email);
            $student->get();
            if ($student->exists()) {
                $this->_init_language_for_student($student);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:students_change_password_invalid_token_email', Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('students/login'));
            }
            $this->form_validation->set_rules('student[password]', 'lang:students_change_password_form_field_password', 'required|min_length[6]|max_length[20]');
            $this->form_validation->set_rules('student[verify]', 'lang:students_change_password_form_field_verify', 'required|matches[student[password]]');
            if ($this->form_validation->run()) {
                $student_post = $this->input->post('student');
                $student->password = sha1($student_post['password']);
                $student->password_token = NULL;
                if ($student->save()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:students_change_password_success', Messages::MESSAGE_TYPE_SUCCESS);
                    redirect(create_internal_url('students/login'));
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:students_change_password_failed', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('students/login'));
                }
            } else {
                $this->db->trans_rollback();
                $this->change_password($token, $encoded_email);
            }
        } else {
            $this->messages->add_message('lang:students_change_password_invalid_token_email', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('students/login'));
        }
    }

    /**
     * Display registration form for student account registration.
     */
    public function registration() {
        if ($this->student_registration_config['enabled']) {
            if ($this->usermanager->is_student_session_valid()) {
                $redirects = $this->config->item('after_login_redirects');
                redirect(create_internal_url($redirects['student']));
            }
            $this->parser->parse('frontend/students/registration.tpl');
        } else {
            $this->messages->add_message('lang:students_registration_disabled', Messages::MESSAGE_TYPE_ERROR);
            redirect('/');
        }
    }
    
    /**
     * Performs student account registration and redirects him to students/registered controller action.
     */
    public function do_registration() {
        if ($this->student_registration_config['enabled']) {
            if ($this->usermanager->is_student_session_valid()) {
                $redirects = $this->config->item('after_login_redirects');
                redirect(create_internal_url($redirects['student']));
            }
            $this->load->library('form_validation');

            $this->form_validation->set_rules('student[fullname]', 'lang:students_registration_validation_field_fullname', 'required');
            $this->form_validation->set_rules('student[email]', 'lang:students_registration_validation_field_email', 'required|valid_email|is_unique[students.email]');
            $this->form_validation->set_rules('student[password]', 'lang:students_registration_validation_field_password', 'required|min_length[6]|max_length[20]');
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
        } else {
            $this->messages->add_message('lang:students_registration_disabled', Messages::MESSAGE_TYPE_ERROR);
            redirect('/');
        }
    }
    
    public function registered() {
        if ($this->student_registration_config['enabled']) {
            $this->parser->parse('frontend/students/registered.tpl');
        } else {
            $this->messages->add_message('lang:students_registration_disabled', Messages::MESSAGE_TYPE_ERROR);
            redirect('/');
        }
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
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:students_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->usermanager->refresh_student_userdata();
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:students_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
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
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:students_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:students_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
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
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:students_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:students_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
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
