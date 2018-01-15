<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Course content controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Course_content extends LIST_Controller {

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
        $this->_select_teacher_menu_pagetag('course_content');
        $this->parser->add_js_file('admin_course_content/list.js');
        $this->inject_courses();
        $this->parser->parse('backend/course_content/index.tpl');
    }

    public function new_content_form() {
        $this->inject_courses();
        $this->parser->parse('backend/course_content/new_content_form.tpl');
    }

    public function get_all_content() {
        $course_content = new Course_content_model();

        $course_content->select('*');
        $course_content->include_related('course', 'name');
        $course_content->include_related('course/period', 'name');
        $course_content->get_iterated();

        $this->lang->init_overlays('course_content', $course_content->all_to_array(), ['title', 'content']);

        $this->parser->parse('backend/course_content/table_content.tpl', ['course_content' => $course_content]);
    }

    public function create() {
        $this->load->library('form_validation');

        $course_content_data = $this->input->post('course_content');

        $this->form_validation->set_rules('course_content[title]', 'lang:admin_course_content_form_field_title', 'required');
        $this->form_validation->set_rules('course_content[course_id]', 'lang:admin_course_content_form_field_course_id', 'required|exists_in_table[courses.id]');

        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $course_content = new Course_content_model();
            $course_content->from_array($course_content_data, ['title', 'content', 'course_id']);
            $course_content->published = FALSE;
            if ($course_content->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_course_content_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_course_content_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_course_content/new_content_form'));
        } else {
            $this->new_content_form();
        }
        $this->db->trans_rollback();
    }

    private function inject_courses()
    {
        $this->parser->assign('courses', Course::get_all_courses_for_form_select());
    }

}