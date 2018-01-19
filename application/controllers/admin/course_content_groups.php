<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Course content groups controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Course_content_groups extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('course_content_groups');
        
        $this->inject_courses();
        
        $this->parser->parse('backend/course_content_groups/index.tpl');
    }
    
    public function new_group_form() {
        $this->inject_courses();
        
        $this->parser->parse('backend/course_content_groups/new_content_group_form.tpl');
    }
    
    public function create() {
        $this->load->library('form_validation');
    
        $course_content_group_data = $this->input->post('course_content_group');
    
        $this->form_validation->set_rules('course_content_group[title]', 'lang:admin_course_content_groups_form_field_title', 'required');
        $this->form_validation->set_rules('course_content_group[course_id]', 'lang:admin_course_content_groups_form_field_course_id', 'required|exists_in_table[courses.id]');
    
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $course_content_group = new Course_content_group();
            $course_content_group->from_array($course_content_group_data, ['title', 'course_id']);
            if ($course_content_group->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_course_content_groups_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_course_content_groups_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_course_content_groups/new_group_form'));
        } else {
            $this->new_group_form();
        }
        $this->db->trans_rollback();
    }
    
    private function inject_courses()
    {
        $this->parser->assign('courses', Course::get_all_courses_for_form_select());
    }
    
}