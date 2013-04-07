<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courses extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('courses');
        
        $this->inject_periods();
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('courses_api.js');
        $this->parser->add_js_file('courses/form.js');
        $this->parser->add_css_file('admin_courses.css');
        $this->parser->parse('backend/courses/index.tpl');
    }
    
    public function get_table_content() {
        $courses = new Course();
        $courses->get_iterated();
        $this->lang->init_overlays('courses', $courses->all_to_array(), array('description'));
        $this->parser->parse('backend/courses/table_content.tpl', array('courses' => $courses));
    }
    
    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('course[name]', 'lang:admin_courses_form_field_name', 'required');
        $this->form_validation->set_rules('course[period_id]', 'lang:admin_courses_form_field_period', 'required');
        
        if ($this->form_validation->run()) {
            $course = new Course();
            $course_data = $this->input->post('course');
            $course->from_array($course_data, array('name', 'period_id', 'description'));
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            if ($course->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_courses_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_courses_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_courses/new_course_form'));
        } else {
            $this->new_course_form();
        }
    }
    
    public function new_course_form() {
        $this->inject_periods();
        $this->parser->parse('backend/courses/new_course_form.tpl');
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        if ($course_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $course = new Course();
            $course->get_by_id($course_id);
            $course->delete();
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
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('courses');
        
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('courses_api.js');
        $this->parser->add_js_file('courses/form.js');
        
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        $course = new Course();
        $course->get_by_id($course_id);
        $this->inject_periods();
        $this->inject_languages();
        $this->lang->load_all_overlays('courses', $course_id);
        
        $this->parser->parse('backend/courses/edit.tpl', array('course' => $course));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('course_id', 'id', 'required');
        $this->form_validation->set_rules('course[name]', 'lang:admin_courses_form_field_name', 'required');
        $this->form_validation->set_rules('course[period_id]', 'lang:admin_courses_form_field_period', 'required');
        
        if ($this->form_validation->run()) {
            $course_id = intval($this->input->post('course_id'));
            $course = new Course();
            $course->get_by_id($course_id);
            if ($course->exists()) {
                $course_data = $this->input->post('course');
                $course->from_array($course_data, array('name', 'period_id', 'description'));
                
                $overlay = $this->input->post('overlay');
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($course->save() && $this->lang->save_overlay_array($overlay) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_courses_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_courses_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_courses_error_course_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_courses/index'));
        } else {
            $this->edit();
        }
    }
        
    private function inject_periods() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $query = $periods->get_raw();
        $data = array(
            NULL => '',
        );
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $data[(int) $row->id] = $row->name;
        }}
        $this->parser->assign('periods', $data);
        $query->free_result();
    }
    
    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
}