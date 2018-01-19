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
        $this->parser->add_css_file('admin_course_content.css');
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
        $course_content->get_paged_iterated($filter['page'] ?? 1, $filter['rows_per_page'] ?? 25);

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
    
    public function edit($id) {
        $this->_select_teacher_menu_pagetag('course_content');
        
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$id);
        
        $this->inject_courses();
        $this->parser->parse('backend/course_content/edit.tpl', [
            'content' => $course_content
        ]);
    }
    
    public function delete($id) {
    
    }
    
    public function get_course_content_groups($course_id, $selected_id = NULL, $course_content_id = NULL) {
        $this->inject_course_content_groups($course_id);
        
        $course_content = new Course_content_model();
        if (is_int($course_content_id)) {
            $course_content->get_by_id((int)$course_content_id);
        }
        
        $this->parser->assign('course_content', $course_content);
        $this->parser->assign('selected_id', $selected_id);
        
        $this->parser->parse('backend/course_content/course_content_groups_options.tpl');
    }

    private function inject_courses()
    {
        $this->parser->assign('courses', Course::get_all_courses_for_form_select());
    }
    
    private function inject_course_content_groups($course_id = NULL) {
        $this->parser->assign('course_content_groups', Course_content_group::get_all_groups($course_id));
    }

}