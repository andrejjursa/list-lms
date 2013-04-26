<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Task sets controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Task_sets extends MY_Controller {
	
    const STORED_FILTER_SESSION_NAME = 'admin_task_sets_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }

    public function index() {
        $this->_select_teacher_menu_pagetag('task_sets');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('task_sets_api.js');
        $this->parser->add_js_file('task_sets/form.js');
        $this->parser->add_css_file('admin_task_sets.css');
        $this->inject_courses();
        $this->inject_stored_filter();
        $this->inject_task_set_types();
        $this->parser->parse('backend/task_sets/index.tpl');
    }
    
    public function new_task_set_form() {
        $this->inject_courses();
        $this->parser->parse('backend/task_sets/new_task_set_form.tpl');
    }
    
    public function get_task_set_types($course_id, $selected_id = NULL, $task_set_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $query = $course->task_set_type->order_by('name', 'asc')->get_raw();
        
        $task_set_types = array('' => '');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $task_set_types[$row->id] = $row->name;
        }}
        
        $query->free_result();
        
        $task_set = new Task_set();
        $task_set->where_related_course('id', $course_id)->get_by_id($task_set_id);
        
        $this->parser->parse('backend/task_sets/task_set_types_options.tpl', array('task_set_types' => $task_set_types, 'task_set' => $task_set, 'selected_id' => $selected_id));
    }
    
    public function get_all_task_sets() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $task_sets = new Task_set();
        $task_sets->order_by('name', 'asc');
        $task_sets->include_related('course', '*', TRUE, TRUE);
        $task_sets->include_related('course/period', 'name', TRUE);
        $task_sets->include_related('task_set_type', '*', TRUE, TRUE);
        $task_sets->include_related_count('task');
        if (isset($filter['course']) && intval($filter['course']) > 0) {
            $task_sets->where_related_course('id', intval($filter['course']));
        }
        if (isset($filter['task_set_type']) && intval($filter['task_set_type']) > 0) {
            $task_sets->where_related_task_set_type('id', intval($filter['task_set_type']));
        }
        if (isset($filter['tasks']) && is_numeric($filter['tasks']) && intval($filter['tasks']) == 0) {
            $task_sets->where_has_no_tasks();
        } else if (isset($filter['tasks']) && is_numeric($filter['tasks']) && intval($filter['tasks']) == 1) {
            $task_sets->where_has_tasks();
        }
        $task_sets->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), array('name'));
        $this->parser->parse('backend/task_sets/table_content.tpl', array('task_sets' => $task_sets));
    }

    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
        $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
        $this->form_validation->set_rules('task_set[task_set_type_id]', 'lang:admin_task_sets_form_field_task_set_type_id', 'required|exists_in_table[task_set_types.id]');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $task_set = new Task_set();
            $task_set_data = $this->input->post('task_set');
            $task_set->from_array($task_set_data, array('name', 'course_id', 'task_set_type_id'));
            if ($task_set->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_task_sets_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_sets_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_task_sets/new_task_set_form'));
        } else {
            $this->new_task_set_form();
        }
        $this->db->trans_rollback();
    }
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('task_sets');
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : 0;
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('task_sets/form.js');
        $this->inject_courses();
        $this->inject_languages();
        $this->parser->parse('backend/task_sets/edit.tpl', array('task_set' => $task_set));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
        $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
        $this->form_validation->set_rules('task_set[task_set_type_id]', 'lang:admin_task_sets_form_field_task_set_type_id', 'required|exists_in_table[task_set_types.id]');
        
        if ($this->form_validation->run()) {
            $task_set_id = intval($this->input->post('task_set_id'));
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                $task_set_data = $this->input->post('task_set');
                $task_set->from_array($task_set_data, array('name', 'course_id', 'task_set_type_id'));
                
                $overlay = $this->input->post('overlay');
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($task_set->save() && $this->lang->save_overlay_array($overlay) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_task_sets_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_task_sets_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_task_sets'));
        } else {
            $this->edit();
        }
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : 0;
        if ($task_set_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            $task_set->delete();
            $this->lang->delete_overlays('task_sets', intval($task_set_id));
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
    
    private function inject_task_set_types() {
        $task_set_types = new Task_set_type();
        $task_set_types->order_by('name', 'asc');
        $task_set_types->get_iterated();
        $data = array( NULL => '' );
        if ($task_set_types->exists()) { foreach ($task_set_types as $task_set_type) {
            $data[$task_set_type->id] = $task_set_type->name;
        }}
        $this->parser->assign('task_set_types', $data);
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
    
    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
}