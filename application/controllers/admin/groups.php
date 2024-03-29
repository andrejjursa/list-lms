<?php

/**
 * Groups controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Groups extends LIST_Controller
{
    
    public const STORED_FILTER_SESSION_NAME = 'admin_groups_filter_data';
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index(): void
    {
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
    
    public function new_group_form(): void
    {
        smarty_inject_days();
        $this->inject_courses();
        $this->parser->parse('backend/groups/new_group_form.tpl');
    }
    
    public function get_table_content(): void
    {
        $fields_config = [
            ['name' => 'created', 'caption' => 'lang:common_table_header_created'],
            ['name' => 'updated', 'caption' => 'lang:common_table_header_updated'],
            ['name' => 'name', 'caption' => 'lang:admin_groups_table_header_group_name'],
            ['name' => 'course', 'caption' => 'lang:admin_groups_table_header_group_course'],
            ['name' => 'rooms', 'caption' => 'lang:admin_groups_table_header_group_rooms'],
            ['name' => 'capacity', 'caption' => 'lang:admin_groups_table_header_group_capacity'],
        ];
        smarty_inject_days();
        $groups = new Group();
        $rooms = $groups->room;
        $rooms->select_min('capacity');
        $rooms->where('group_id', '${parent}.id', false);
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        if (isset($filter['course_id']) && (int)$filter['course_id'] > 0) {
            $groups->where_related_course('id', (int)$filter['course_id']);
        }
        $groups->include_related('course', 'name', true);
        $groups->include_related('course/period', 'name', true);
        $groups->select_subquery($rooms, 'group_capacity');
        $order_by_direction = $filter['order_by_direction'] === 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] === 'course') {
            $groups->order_by_related('course/period', 'sorting', $order_by_direction);
            $groups->order_by_related_with_constant('course', 'name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'created') {
            $groups->order_by('created', $order_by_direction);
        } else if ($filter['order_by_field'] === 'updated') {
            $groups->order_by('updated', $order_by_direction);
        } else if ($filter['order_by_field'] === 'capacity') {
            $groups->order_by('group_capacity', $order_by_direction);
        } else if ($filter['order_by_field'] === 'name') {
            $groups->order_by_with_constant('name', $order_by_direction);
        }
        $groups->get_paged_iterated(
            isset($filter['page']) ? (int)$filter['page'] : 1,
            isset($filter['rows_per_page']) ? (int)$filter['rows_per_page'] : 25
        );
        $this->parser->parse(
            'backend/groups/table_content.tpl',
            [
                'groups'        => $groups,
                'fields_config' => $fields_config,
            ]
        );
    }
    
    public function create(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'group[name]',
            'lang:admin_groups_form_field_group_name',
            'required'
        );
        $this->form_validation->set_rules(
            'group[course_id]',
            'lang:admin_groups_form_field_group_course',
            'required'
        );
        
        if ($this->form_validation->run()) {
            $group = new Group();
            $group_data = $this->input->post('group');
            $group->from_array($group_data, ['name', 'course_id']);
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            if ($group->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message(
                    'lang:admin_groups_flash_message_save_successful',
                    Messages::MESSAGE_TYPE_SUCCESS
                );
                $this->_action_success();
                $this->output->set_internal_value('course_id', $group->course_id);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_groups_flash_message_save_failed',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_groups/new_group_form'));
        } else {
            $this->new_group_form();
        }
    }
    
    public function edit(): void
    {
        $uri = $this->uri->ruri_to_assoc(3);
        $group_id = isset($uri['group_id']) ? (int)$uri['group_id'] : 0;
        $group = new Group();
        $group->get_by_id($group_id);
        
        $this->inject_courses();
        $this->_select_teacher_menu_pagetag('groups');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_groups/form.js');
        $this->parser->add_js_file('admin_groups/edit.js');
        $this->parser->parse('backend/groups/edit.tpl', ['group' => $group]);
    }
    
    public function update(): void
    {
        $this->load->library('form_validation');
        
        $group_id = (int)$this->input->post('group_id');
        
        $this->form_validation->set_rules('group_id', 'id', 'required');
        $this->form_validation->set_rules(
            'group[name]',
            'lang:admin_groups_form_field_group_name',
            'required'
        );
        $this->form_validation->set_rules(
            'group[course_id]',
            'lang:admin_groups_form_field_group_course',
            'required'
        );
        
        if ($this->form_validation->run()) {
            $group = new Group();
            $group->get_by_id($group_id);
            if ($group->exists()) {
                $group_data = $this->input->post('group');
                $group->from_array($group_data, ['name', 'course_id']);
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($group->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_groups_flash_message_save_successful',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                    $this->_action_success();
                    $this->output->set_internal_value('course_id', $group->course_id);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_groups_flash_message_save_failed',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
            } else {
                $this->messages->add_message(
                    'lang:admin_groups_error_no_such_group_message',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_groups/index'));
        } else {
            $this->edit();
        }
    }
    
    public function delete(): void
    {
        $this->output->set_content_type('application/json');
        $uri = $this->uri->ruri_to_assoc(3);
        if (isset($uri['group_id'])) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $group = new Group();
            $group->get_by_id((int)$uri['group_id']);
            $course_id = $group->course_id;
            if ($group->exists()) {
                $group->room->get()->delete_all();
                $group->delete();
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->output->set_output(json_encode(true));
                    $this->_action_success();
                    $this->output->set_internal_value('course_id', $course_id);
                } else {
                    $this->db->trans_rollback();
                    $this->output->set_output(json_encode(false));
                }
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(false));
            }
        } else {
            $this->output->set_output(json_encode(false));
        }
    }
    
    public function group_mail($group_id): void
    {
        $group = new Group();
        $group->include_related('course', 'name');
        $group->include_related('course/period', 'name');
        $group->get_by_id($group_id);
        $students = new Student();
        $students->where_related('participant/group', 'id', $group->id);
        $students->where_related('participant/course', 'id', $group->course_id);
        $students->where_related('participant', 'allowed', 1);
        $students->get_iterated();
        $this->_add_tinymce4();
        $this->parser->add_js_file('admin_groups/group_mail.js');
        $this->parser->parse('backend/groups/group_mail.tpl', ['group' => $group, 'students' => $students]);
    }
    
    public function send_group_mail($group_id): void
    {
        $group = new Group();
        $group->get_by_id($group_id);
        if ($group->exists()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules(
                'group_mail[subject]',
                'lang:admin_groups_group_email_form_field_subject',
                'required'
            );
            $this->form_validation->set_rules(
                'group_mail[body]',
                'lang:admin_groups_group_email_form_field_body',
                'required_no_html'
            );
            $this->form_validation->set_rules(
                'group_mail[from]',
                'lang:admin_groups_group_email_form_field_from',
                'required'
            );
            $this->form_validation->set_rules(
                'group_mail[student][]',
                'lang:admin_groups_group_email_form_field_students',
                'required'
            );
            if ($this->form_validation->run()) {
                $data = $this->input->post('group_mail');
                $students = new Student();
                $students->where_related('participant/group', 'id', $group->id);
                $students->where_related('participant/course', 'id', $group->course_id);
                $students->where_related('participant', 'allowed', 1);
                $students->where_in('id', $data['student']);
                $students->get();
                if ($students->exists()) {
                    $from = null;
                    $from_name = '';
                    $teacher = new Teacher();
                    $teacher->get_by_id($this->usermanager->get_teacher_id());
                    if ($data['from'] === 'me') {
                        $from = $teacher->email;
                        $from_name = $teacher->fullname;
                    }
                    $sender_copy = isset($data['sender_copy']) && $data['sender_copy'] === 1;
                    $sender_email = $teacher->email;
                    if ($this->_send_multiple_emails(
                        $students,
                        $data['subject'],
                        '{$data.body|add_base_url}',
                        ['data' => $data], $from, $from_name, $sender_copy, $sender_email
                    )) {
                        $this->messages->add_message(
                            'lang:admin_groups_group_email_success_sent',
                            Messages::MESSAGE_TYPE_SUCCESS
                        );
                    } else {
                        $this->messages->add_message(
                            'lang:admin_groups_group_email_error_send_failed',
                            Messages::MESSAGE_TYPE_ERROR
                        );
                    }
                } else {
                    $this->messages->add_message(
                        'lang:admin_groups_group_email_error_no_students_selected',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
                redirect(create_internal_url('admin_groups/group_mail/' . $group_id));
            } else {
                $this->group_mail($group_id);
            }
        } else {
            $this->messages->add_message(
                'lang:admin_groups_group_email_error_group_not_found',
                Messages::MESSAGE_TYPE_ERROR
            );
            redirect(create_internal_url('admin_groups/group_mail/' . $group_id));
        }
    }
    
    private function inject_courses(): void
    {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = [null => ''];
        if ($periods->exists()) {
            foreach ($periods as $period) {
                $period->course->order_by_with_constant('name', 'asc')->get_iterated();
                if ($period->course->exists() > 0) {
                    foreach ($period->course as $course) {
                        $data[$period->name][$course->id] = $course->name;
                    }
                }
            }
        }
        $this->parser->assign('courses', $data);
    }
    
    private function store_filter($filter): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(
                self::STORED_FILTER_SESSION_NAME,
                'course_id'
            );
        }
    }
    
    private function inject_stored_filter(): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_FILTER_SESSION_NAME,
            $this->usermanager->get_teacher_id(),
            'course_id'
        );
        $this->parser->assign('filter', $filter);
    }
    
}