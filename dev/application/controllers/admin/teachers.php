<?php

/**
 * Teachers controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Teachers extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
    }
    
    public function login() {
        $uri_params = $this->uri->uri_to_assoc(3);
        if ($this->usermanager->is_teacher_session_valid()) {
            if (isset($uri_params['current_url'])) {
                redirect(decode_from_url($uri_params['current_url']));
            } else {
                $redirects = $this->config->item('after_login_redirects');
                redirect(create_internal_url($redirects['teacher']));
            }
        } else {
            $this->parser->add_js_file('teachers/login.js');
            $this->parser->parse('backend/teachers/login.tpl', array('uri_params' => $uri_params));
        }
    }
    
    public function do_login() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('teacher[email]', 'lang:admin_teachers_login_field_email', 'required|valid_email');
        $this->form_validation->set_rules('teacher[password]', 'lang:admin_teachers_login_field_password', 'required|min_length[6]|max_length[20]');
        
        if ($this->form_validation->run()) {
            $teacher_data = $this->input->post('teacher');
            if ($this->usermanager->authenticate_teacher_login($teacher_data['email'], $teacher_data['password'])) {
                $uri_params = $this->uri->uri_to_assoc(3);
                $this->messages->add_message('lang:admin_teachers_login_success', Messages::MESSAGE_TYPE_SUCCESS);
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
        $this->messages->add_message('lang:admin_teachers_logout_success', Messages::MESSAGE_TYPE_SUCCESS);
        redirect(create_internal_url('admin_teachers/login'));
    }
    
    public function my_account() {
        $this->_select_teacher_menu_pagetag('teacher_account');
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $teacher = new Teacher();
        $teacher->where('id', $this->usermanager->get_teacher_id());
        $teacher->get();
        
        $languages_available = $this->lang->get_list_of_languages();
        
        $this->parser->parse('backend/teachers/my_account.tpl', array('teacher' => $teacher, 'languages' => $languages_available));
    }
    
    public function save_basic_information() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('teacher[fullname]', 'lang:admin_teachers_my_account_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('teacher[language]', 'lang:admin_teachers_my_account_field_language', 'required');
        $this->form_validation->set_rules('teacher_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            $teacher_id = intval($this->input->post('teacher_id'));
            if ($teacher_id == $this->usermanager->get_teacher_id()) {
                $teacher = new Teacher();
                $teacher->get_by_id($teacher_id);
                if ($teacher->exists()) {
                    $teacher->from_array($this->input->post('teacher'), array('fullname', 'language'));
                    if ($teacher->save()) {
                        $this->messages->add_message('lang:admin_teachers_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->usermanager->refresh_teacher_userdata();
                    } else {
                        $this->messages->add_message('lang:admin_teachers_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->messages->add_message('lang:admin_teachers_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_teachers_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_teachers/my_account'));
        } else {
            $this->my_account();
        }
    }
    
    public function save_password() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->load->library('form_validation');
        
        $teacher_id = intval($this->input->post('teacher_id'));
        $this->form_validation->set_rules('teacher[password_old]', 'lang:admin_teachers_my_account_field_old_password', 'required|callback__validate_old_password[' . $teacher_id . ']');
        $this->form_validation->set_rules('teacher[password]', 'lang:admin_teachers_my_account_field_password', 'required|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('teacher[password_validation]', 'lang:admin_teachers_my_account_field_password_validation', 'required|matches[teacher[password]]');
        $this->form_validation->set_rules('teacher_id', 'id', 'required');
        $this->form_validation->set_message('_validate_old_password', $this->lang->line('admin_teachers_my_account_field_old_password_error_message'));
        
        if ($this->form_validation->run()) {
            if ($teacher_id == $this->usermanager->get_teacher_id()) {
                $teacher = new Teacher();
                $teacher->get_by_id($teacher_id);
                if ($teacher->exists()) {
                    $teacher_post = $this->input->post('teacher');
                    $teacher->password = sha1($teacher_post['password']);
                    if ($teacher->save()) {
                        $this->messages->add_message('lang:admin_teachers_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                    } else {
                        $this->messages->add_message('lang:admin_teachers_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->messages->add_message('lang:admin_teachers_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_teachers_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_teachers/my_account'));
        } else {
            $this->my_account();
        }
    }
    
    public function _validate_old_password($str, $teacher_id) {
        $teacher = new Teacher();
        $teacher->where('password', sha1($str));
        $teacher->where('id', intval($teacher_id));
        $teacher->get();
        return $teacher->exists();
    }
    
    public function save_email() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->load->library('form_validation');
        
        $teacher_id = intval($this->input->post('teacher_id'));
        $this->form_validation->set_rules('teacher[email]', 'lang:admin_teachers_my_account_field_email', 'required|valid_email|is_unique[teachers.email]');
        $this->form_validation->set_rules('teacher[email_validation]', 'lang:admin_teachers_my_account_field_email_validation', 'required|matches[teacher[email]]');
        $this->form_validation->set_rules('teacher_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            $error_invalid = true;
            if ($teacher_id == $this->usermanager->get_teacher_id()) {
                $teacher = new Teacher();
                $teacher->get_by_id($teacher_id);
                if ($teacher->exists()) {
                    $teacher_post = $this->input->post('teacher');
                    $teacher->email = $teacher_post['email'];
                    if ($teacher->save()) {
                        $this->messages->add_message('lang:admin_teachers_my_account_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                    } else {
                        $this->messages->add_message('lang:admin_teachers_my_account_error_save', Messages::MESSAGE_TYPE_ERROR);
                    }
                    $error_invalid = false;
                }
            }
            if ($error_invalid) {
                $this->messages->add_message('lang:admin_teachers_my_account_error_invalid_account', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_teachers/my_account'));
        } else {
            $this->my_account();
        }
    }
    
    public function list_index() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->_select_teacher_menu_pagetag('teachers_list');
        $this->parser->add_js_file('teachers_api.js');
        $this->parser->add_css_file('admin_teachers.css');
        $this->parser->parse('backend/teachers/list_index.tpl');
    }
    
    public function list_teachers_table() {
        $this->usermanager->teacher_login_protected_redirect();
        
        $teachers = new Teacher();
        $teachers->order_by('fullname', 'asc')->where('id !=', $this->usermanager->get_teacher_id())->get_iterated();
        $this->parser->parse('backend/teachers/list_teachers_table.tpl', array('teachers' => $teachers));
    }
    
    public function get_new_teacher_form() {
        $this->usermanager->teacher_login_protected_redirect();
        $this->parser->parse('backend/teachers/new_teacher_form.tpl');
    }
    
    public function create_teacher() {
        $this->usermanager->teacher_login_protected_redirect();
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('teacher[fullname]', 'lang:admin_teachers_list_form_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('teacher[email]', 'lang:admin_teachers_list_form_field_email', 'required|valid_email|is_unique[teachers.email]');
        $this->form_validation->set_rules('teacher[password]', 'lang:admin_teachers_list_form_field_password', 'required|min_length[6]|max_length[20]');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $teacher_data = $this->input->post('teacher');
            $teacher = new Teacher();
            $teacher->from_array($teacher_data, array('fullname', 'email'));
            $teacher->password = sha1($teacher_data['password']);
            $teacher->language = $this->config->item('language');
            if ($teacher->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_teachers_list_account_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->messages->add_message('lang:admin_teachers_list_account_save_fail', Messages::MESSAGE_TYPE_ERROR);
                $this->db->trans_rollback();
            }
            redirect(create_internal_url('admin_teachers/get_new_teacher_form'));
        } else {
            $this->db->trans_rollback();
            $this->get_new_teacher_form();
        }
    }
    
    public function edit_teacher() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->_select_teacher_menu_pagetag('teachers_list');
        $url = $this->uri->ruri_to_assoc(3);
        $teacher_id = isset($url['teacher_id']) ? intval($url['teacher_id']) : 0;
        $teacher = new Teacher();
        $teacher->where('id !=', $this->usermanager->get_teacher_id())->get_by_id($teacher_id);
        $this->parser->parse('backend/teachers/edit_teacher.tpl', array('teacher' => $teacher));
    }
    
    public function update_teacher() {
        $this->usermanager->teacher_login_protected_redirect();
        
        $this->load->library('form_validation');
        
        $teacher_id = intval($this->input->post('teacher_id'));
        
        $this->form_validation->set_rules('teacher[fullname]', 'lang:admin_teachers_list_form_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('teacher[email]', 'lang:admin_teachers_list_form_field_email', 'required|valid_email|callback__email_available[' . $teacher_id . ']');
        $this->form_validation->set_rules('teacher[password]', 'lang:admin_teachers_list_form_field_password', 'min_length_optional[6]|max_length_optional[20]');
        $this->form_validation->set_message('_email_available', $this->lang->line('admin_teachers_list_form_error_email_not_available'));
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $teacher = new Teacher();
            $teacher->where('id !=', $this->usermanager->get_teacher_id())->get_by_id($teacher_id);
            if ($teacher->exists()) {
                $teacher_data = $this->input->post('teacher');
                $teacher->from_array($teacher_data, array('fullname', 'email'));
                if (isset($teacher_data['password']) && !empty($teacher_data['password'])) {
                    $teacher->password = sha1($teacher_data['password']);
                }
                if ($teacher->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_teachers_list_account_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->messages->add_message('lang:admin_teachers_list_account_save_fail', Messages::MESSAGE_TYPE_ERROR);
                    $this->db->trans_rollback();
                }
            } else {
                $this->messages->add_message('lang:admin_teachers_list_teacher_not_found', Messages::MESSAGE_TYPE_ERROR);
                $this->db->trans_rollback();
            }
            redirect(create_internal_url('admin_teachers/list_index'));
        } else {
            $this->edit_teacher();
        }
    }
    
    public function _email_available($str, $teacher_id) {
        $teacher = new Teacher();
        $teacher->where('email', $str)->where('id !=', $teacher_id);
        $count = $teacher->count();
        return $count == 0;
    }
    
    public function delete_teacher() {
        $this->output->set_content_type('application/json');
        $this->usermanager->teacher_login_protected_redirect();
        $url = $this->uri->ruri_to_assoc(3);
        $teacher_id = isset($url['teacher_id']) ? intval($url['teacher_id']) : 0;
        if ($teacher_id != 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $teacher = new Teacher();
            $teacher->where('id !=', $this->usermanager->get_teacher_id())->get_by_id($teacher_id);
            $teacher->delete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->output->set_output(json_encode(TRUE));    
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(FALSE));                
            }
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    public function switch_language($language, $current_url) {
        $this->usermanager->teacher_login_protected_redirect();
        if ($this->usermanager->set_teacher_language($language)) {
            $this->messages->add_message('lang:admin_teachers_teacher_language_quick_changed', Messages::MESSAGE_TYPE_DEFAULT);
        }
        redirect(decode_from_url($current_url));
    }
}