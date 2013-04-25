<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Students extends MY_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_students_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->add_js_file('students_api.js');
        $this->parser->add_css_file('admin_students.css');
        $this->inject_stored_filter();
        $this->parser->parse('backend/students/index.tpl');
    }
    
    public function new_student_form() {
        $this->parser->parse('backend/students/new_student_form.tpl');
    }
    
    public function table_content() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $students = new Student();
        $students->order_by('fullname', 'asc');
        $students->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->parser->parse('backend/students/table_content.tpl', array('students' => $students));
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[fullname]', 'lang:admin_students_form_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('student[email]', 'lang:admin_students_form_field_email', 'required|valid_email|is_unique[students.email]');
        $this->form_validation->set_rules('student[password]', 'lang:admin_students_form_field_password', 'required|min_length[6]|max_length[20]');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $student_data = $this->input->post('student');
            $student = new Student();
            $student->from_array($student_data, array('fullname', 'email'));
            $student->password = sha1($student_data['password']);
            $student->language = $this->config->item('language');
            if ($student->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_students_account_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->messages->add_message('lang:admin_students_account_save_fail', Messages::MESSAGE_TYPE_ERROR);
                $this->db->trans_rollback();
            }
            redirect(create_internal_url('admin_students/new_student_form'));
        } else {
            $this->new_student_form();
        }
    }
    
    public function edit() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->_select_teacher_menu_pagetag('students_manager');
        $url = $this->uri->ruri_to_assoc(3);
        $student_id = isset($url['student_id']) ? intval($url['student_id']) : 0;
        $student = new Student();
        $student->get_by_id($student_id);
        $this->parser->parse('backend/students/edit.tpl', array('student' => $student));
    }
    
    public function update() {
        $this->usermanager->teacher_login_protected_redirect();
        
        $this->load->library('form_validation');
        
        $student_id = intval($this->input->post('student_id'));
        
        $this->form_validation->set_rules('student[fullname]', 'lang:admin_students_form_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('student[email]', 'lang:admin_students_form_field_email', 'required|valid_email|callback__email_available[' . $student_id . ']');
        $this->form_validation->set_rules('student[password]', 'lang:admin_students_form_field_password', 'min_length_optional[6]|max_length_optional[20]');
        $this->form_validation->set_message('_email_available', $this->lang->line('admin_students_form_error_email_not_available'));
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $student = new Student();
            $student->get_by_id($student_id);
            if ($student->exists()) {
                $student_data = $this->input->post('student');
                $student->from_array($student_data, array('fullname', 'email'));
                if (isset($student_data['password']) && !empty($student_data['password'])) {
                    $student->password = sha1($student_data['password']);
                }
                if ($student->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_students_account_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->messages->add_message('lang:admin_students_account_save_fail', Messages::MESSAGE_TYPE_ERROR);
                    $this->db->trans_rollback();
                }
            } else {
                $this->messages->add_message('lang:admin_students_student_not_found', Messages::MESSAGE_TYPE_ERROR);
                $this->db->trans_rollback();
            }
            redirect(create_internal_url('admin_students'));
        } else {
            $this->edit();
        }
    }
    
    public function _email_available($str, $student_id) {
        $student = new Student();
        $student->where('email', $str)->where('id !=', $student_id);
        $count = $student->count();
        return $count == 0;
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $this->usermanager->teacher_login_protected_redirect();
        $url = $this->uri->ruri_to_assoc(3);
        $student_id = isset($url['student_id']) ? intval($url['student_id']) : 0;
        if ($student_id != 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->get_by_id($student_id);
            $student->delete();
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
    
    private function store_filter($filter) {
        if (is_array($filter)) {
            $old_filter = $this->session->userdata(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->session->set_userdata(self::STORED_FILTER_SESSION_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter() {
        $filter = $this->session->userdata(self::STORED_FILTER_SESSION_NAME);
        $this->parser->assign('filter', $filter);
    }
}