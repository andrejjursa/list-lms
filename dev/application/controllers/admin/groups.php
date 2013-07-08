<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Groups controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Groups extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_groups_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        smarty_inject_days();
        $this->inject_courses();
        $this->inject_stored_filter();
        $this->_select_teacher_menu_pagetag('groups');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_groups/list.js');
        $this->parser->add_js_file('admin_groups/form.js');
        $this->parser->add_css_file('admin_groups.css');
        $this->parser->parse('backend/groups/index.tpl');
    }
    
    public function new_group_form() {
        smarty_inject_days();
        $this->inject_courses();
        $this->parser->parse('backend/groups/new_group_form.tpl');
    }
    
    public function get_table_content() {
        $fields_config = array(
            array('name' => 'created', 'caption' => 'lang:common_table_header_created'),
            array('name' => 'updated', 'caption' => 'lang:common_table_header_updated'),
            array('name' => 'name', 'caption' => 'lang:admin_groups_table_header_group_name'),
            array('name' => 'course', 'caption' => 'lang:admin_groups_table_header_group_course'),
            array('name' => 'rooms', 'caption' => 'lang:admin_groups_table_header_group_rooms'),
            array('name' => 'capacity', 'caption' => 'lang:admin_groups_table_header_group_capacity'),
        );
        smarty_inject_days();
        $groups = new Group();
        $rooms = $groups->room;
        $rooms->select_min('capacity');
        $rooms->where('group_id', '${parent}.id', FALSE);
        $groups->order_by_related('course/period', 'sorting', 'asc');
        $groups->order_by_related_with_constant('course', 'name', 'asc');
        $groups->order_by_with_constant('name', 'asc');
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        if (isset($filter['course_id']) && intval($filter['course_id']) > 0) {
            $groups->where_related_course('id', intval($filter['course_id']));
        }
        $groups->include_related('course', 'name', TRUE);
        $groups->include_related('course/period', 'name', TRUE);
        $groups->select_subquery($rooms, 'group_capacity');
        $groups->get_iterated();
        $this->parser->parse('backend/groups/table_content.tpl', array('groups' => $groups, 'fields_config' => $fields_config));
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
    
    public function edit() {
        $uri = $this->uri->ruri_to_assoc(3);
        $group_id = isset($uri['group_id']) ? intval($uri['group_id']) : 0;
        $group = new Group();
        $group->get_by_id($group_id);
        
        $this->inject_courses();
        $this->_select_teacher_menu_pagetag('groups');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_groups/form.js');
        $this->parser->parse('backend/groups/edit.tpl', array('group' => $group));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $group_id = intval($this->input->post('group_id'));
        
        $this->form_validation->set_rules('group_id', 'id', 'required');
        $this->form_validation->set_rules('group[name]', 'lang:admin_groups_form_field_group_name', 'required');
        $this->form_validation->set_rules('group[course_id]', 'lang:admin_groups_form_field_group_course', 'required');
        
        if ($this->form_validation->run()) {
            $group = new Group();
            $group->get_by_id($group_id);
            if ($group->exists()) {
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
            } else {
                $this->messages->add_message('lang:admin_groups_error_no_such_group_message', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_groups/index'));
        } else {
            $this->edit();
        }
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $uri = $this->uri->ruri_to_assoc(3);
        if (isset($uri['group_id'])) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $group = new Group();
            $group->get_by_id(intval($uri['group_id']));
            if ($group->exists()) {
                $group->room->get()->delete_all();
                $group->delete();
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->output->set_output(json_encode(TRUE));    
                } else {
                    $this->db->trans_rollback();
                    $this->output->set_output(json_encode(FALSE));
                }
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(FALSE)); 
            }
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    private function inject_courses() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = array( NULL => '' );
        if ($periods->exists()) { foreach ($periods as $period) {
            $period->course->order_by_with_constant('name', 'asc')->get_iterated();
            if ($period->course->exists() > 0) { foreach ($period->course as $course) {
                $data[$period->name][$course->id] = $course->name;
            }}
        }}
        $this->parser->assign('courses', $data);
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