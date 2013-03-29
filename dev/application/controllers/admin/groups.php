<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Groups extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->inject_courses();
        $this->_select_teacher_menu_pagetag('groups');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('groups_api.js');
        $this->parser->add_js_file('groups/form.js');
        $this->parser->add_css_file('admin_groups.css');
        $this->parser->parse('backend/groups/index.tpl');
    }
    
    public function new_group_form() {
        $this->inject_courses();
        $this->parser->parse('backend/groups/new_group_form.tpl');
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('group[name]', 'lang:admin_groups_form_field_group_name', 'required');
        $this->form_validation->set_rules('group[course_id]', 'lang:admin_groups_form_field_group_course', 'required');
        
        if ($this->form_validation->run()) {
            $group = new Group();
            $group_data = $this->input->post('group');
            $group->from_array($group_data, array('name', 'course_id'));
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            if ($group->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_groups_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_groups_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_groups/new_group_form'));
        } else {
            $this->new_group_form();
        }
    }
    
    private function inject_courses() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = array( NULL => '' );
        if ($periods->exists()) { foreach ($periods as $period) {
            $period->course->get_iterated();
            if ($period->course->exists() > 0) { foreach ($period->course as $course) {
                $data[$period->name][$course->id] = $course->name;
            }}
        }}
        $this->parser->assign('courses', $data);
    }
    
}