<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Course content groups controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Course_content_groups extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_course_content_groups_filter_data';
    
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
        
        $this->parser->add_js_file('admin_course_content_groups/list.js');
        $this->parser->add_css_file('admin_course_content_groups.css');
        
        $this->inject_courses();
        $this->inject_stored_filter();
        
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
            $course_content_group->sorting = Course_content_model::get_next_sorting_number((int)$course_content_group_data['course_id']);
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
    
    public function get_all_content_groups() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        
        $content_groups = new Course_content_group();
        $content_groups->include_related('course', 'name');
        $content_groups->include_related('course/period', 'name');
        $content_groups->include_related_count('course_content_model', 'course_content_count');
    
        if (isset($filter['course_id']) && intval($filter['course_id']) > 0) {
            $content_groups->where_related_course('id', intval($filter['course_id']));
        }
    
        $order_by_direction = $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] == 'title') {
            $content_groups->order_by('title', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'course') {
            $content_groups->order_by_related('course/period', 'sorting', $order_by_direction);
            $content_groups->order_by_related_with_constant('course', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'content_count') {
            $course_content_count = new Course_content_model();
            $course_content_count->select_func('COUNT', ['@id'], 'content_count');
            $course_content_count->where('course_content_group_id', '${parent}.id', FALSE);
            $course_content_count->group_by('course_content_group_id');
            $content_groups->order_by_subquery_fixed($course_content_count, strtoupper($order_by_direction));
        }
        
        $content_groups->get_paged_iterated($filter['page'] ?? 1, $filter['rows_per_page'] ?? 25);
    
        $this->lang->init_overlays('course_content_groups', $content_groups->all_to_array(), ['title']);
        
        $this->parser->parse('backend/course_content_groups/table_content.tpl', [
            'content_groups' => $content_groups,
        ]);
    }
    
    public function edit($id) {
        $content_group = new Course_content_group();
        $content_group->get_by_id((int)$id);
        
        $this->inject_courses();
        $this->inject_languages();
        
        $this->parser->parse('backend/course_content_groups/edit.tpl', ['content_group' => $content_group]);
    }
    
    public function update() {
        $id = $this->input->post('content_group_id');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $course_content_group = new Course_content_group();
        $course_content_group->get_by_id((int)$id);
        
        if ($course_content_group->exists()) {
            $this->load->library('form_validation');
    
            $this->form_validation->set_rules('content_group[title]', 'lang:admin_course_content_groups_form_field_title', 'required');
            $this->form_validation->set_rules('content_group[course_id]', 'lang:admin_course_content_groups_form_field_course_id', 'required|exists_in_table[courses.id]');
            
            if ($this->form_validation->run()) {
                $course_content_group_data = $this->input->post('content_group');
                
                if ((int)$course_content_group->course_id != (int)$course_content_group_data['course_id']) {
                    $course_content_group->sorting = Course_content_model::get_next_sorting_number((int)$course_content_group_data['course_id']);
                }
                
                $course_content_group->from_array($course_content_group_data, ['title', 'course_id']);
                
                $overlay = $this->input->post('overlay');
                
                if ($course_content_group->save() && $this->lang->save_overlay_array($overlay) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_course_content_group_success_updated', Messages::MESSAGE_TYPE_SUCCESS);
                    redirect(create_internal_url('admin_course_content_groups'));
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_course_content_groups_error_not_updated', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('admin_course_content_groups'));
                }
            } else {
                $this->db->trans_rollback();
                $this->edit($id);
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:admin_course_content_groups_error_not_found_for_edit', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_course_content_groups'));
        }
    }
    
    public function delete($id) {
        $output = new stdClass();
        $output->message = '';
        $output->status = false;
    
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $content_group = new Course_content_group();
        $content_group->get_by_id(intval($id));
        
        if ($content_group->exists()) {
            $course_content = new Course_content_model();
            $course_content->where_related($content_group);
            $course_content->limit(1);
            $course_content->get();
            
            if (!$course_content->exists()) {
                if ($content_group->delete()) {
                    $this->db->trans_commit();
                    $output->status = true;
                    $output->message = $this->lang->line('admin_course_content_groups_delete_success');
                } else {
                    $this->db->trans_rollback();
                    $output->message = $this->lang->line('admin_course_content_groups_delete_error_delete_failed');
                }
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_course_content_groups_delete_error_cant_delete');
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_course_content_groups_delete_error_not_found');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    private function inject_courses()
    {
        $this->parser->assign('courses', Course::get_all_courses_for_form_select());
    }
    
    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
    private function store_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_FILTER_SESSION_NAME, 'course_id');
        }
    }
    
    private function inject_stored_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course_id');
        $this->parser->assign('filter', $filter);
    }
    
}