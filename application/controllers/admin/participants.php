<?php

/**
 * Participants controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Participants extends LIST_Controller
{
    
    public const STORED_FILTER_SESSION_NAME = 'admin_participants_filter_data';
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_load_teacher_langfile('students');
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index(): void
    {
        $this->_select_teacher_menu_pagetag('participants');
        $this->inject_stored_filter();
        $this->inject_courses();
        //$this->inject_students();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_participants/list.js');
        $this->parser->add_js_file('admin_participants/form.js');
        $this->parser->add_css_file('admin_participants.css');
        $this->parser->parse('backend/participants/index.tpl');
    }
    
    public function table_content(): void
    {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $participants = new Participant();
        $participants->include_related('student', 'fullname');
        $participants->include_related('student', 'email');
        $participants->include_related('course', 'name');
        $participants->include_related('course/period', 'name');
        $participants->include_related('group', 'name');
        if (isset($filter['student_fullname']) && trim($filter['student_fullname']) !== '') {
            $participants->like_related('student', 'fullname', trim($filter['student_fullname']));
        }
        if (isset($filter['course']) && (int)$filter['course'] > 0) {
            $participants->where_related('course', 'id', (int)$filter['course']);
        }
        if (isset($filter['group']) && (int)$filter['group'] > 0) {
            $participants->where_related('group', 'id', (int)$filter['group']);
        }
        if (isset($filter['group_set'])) {
            if ($filter['group_set'] === 'none') {
                $participants->where('group_id', null);
            } else if ($filter['group_set'] === 'assigned') {
                $participants->group_start(' NOT', 'AND');
                $participants->where('group_id', null);
                $participants->group_end();
            }
        }
        $order_by_direction = $filter['order_by_direction'] === 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] === 'student') {
            $participants->order_by_related_as_fullname('student', 'fullname', $order_by_direction);
            $participants->order_by_related('student', 'email', $order_by_direction);
        } else if ($filter['order_by_field'] === 'course') {
            $participants->order_by_related('course/period', 'sorting', $order_by_direction);
            $participants->order_by_related_with_constant('course', 'name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'group') {
            $participants->order_by_related_with_constant('group', 'name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'status') {
            $participants->order_by('allowed', $order_by_direction);
        }
        $participants->get_paged_iterated(
            isset($filter['page']) ? (int)$filter['page'] : 1,
            isset($filter['rows_per_page']) ? (int)$filter['rows_per_page'] : 25
        );
        $this->parser->parse('backend/participants/table_content.tpl', ['participants' => $participants]);
    }
    
    public function get_groups_from_course($course_id, $selected_id = null): void
    {
        $groups = new Group();
        $groups->select('id, name');
        $groups->where_related_course('id', $course_id);
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        $options = [
            '' => '',
        ];
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $this->parser->parse(
            'backend/participants/groups_from_course.tpl',
            [
                'groups'   => $options,
                'selected' => $selected_id,
            ]
        );
    }
    
    public function approve_participation(): void
    {
        $this->output->set_content_type('application/json');
        $output = [
            'status'  => false,
            'message' => $this->lang->line('admin_participants_message_participant_not_found'),
        ];
        $url = $this->uri->ruri_to_assoc(3);
        $participant_id = isset($url['participant_id']) ? (int)$url['participant_id'] : 0;
        if ($participant_id > 0) {
            $process_ok = true;
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $participant = new Participant();
            $participant->get_by_id($participant_id);
            $participant->allowed = 1;
            $participant->save();
            
            $student = $participant->student->get();
            if (!$participant->exists() || !$student->exists()) {
                $output['message'] = $this->lang->line('admin_participants_message_participant_not_found');
                $process_ok = false;
            }
            
            $course = $participant->course->get();
            if ($process_ok) {
                if (!$course->exists()) {
                    $output['message'] = $this->lang->line('admin_participants_message_course_not_set');
                    $process_ok = false;
                }
                if ($course->participant->where('allowed', 1)->count() >= (int)$course->capacity) {
                    $output['message'] = $this->lang->line('admin_participants_message_course_is_full');
                    $process_ok = false;
                }
            }
            
            if ($process_ok) {
                $group = $participant->group->get();
                if ($group->exists() && !$group->is_related_to($course)) {
                    $output['message'] = $this->lang->line(
                        'admin_participants_message_student_group_not_belongs_to_course'
                    );
                    $process_ok = false;
                }
            }
            
            if ($process_ok) {
                $group = new Group();
                $rooms = $group->room;
                $rooms->select_min('capacity');
                $rooms->where('group_id', '${parent}.id', false);
                $group->select_subquery($rooms, 'group_capacity');
                $group->include_related_count('participant');
                $group->where_related_participant('allowed', 1);
                $group->get_by_id((int)$participant->group_id);
                if ($group->exists() && (int)$group->participant_count >= (int)$group->group_capacity) {
                    $output['message'] = $this->lang->line('admin_participants_message_group_is_full');
                    $process_ok = false;
                }
            }
            
            $output['status'] = $process_ok;
            if ($output['status']) {
                $this->db->trans_commit();
                $output['message'] = $this->lang->line('admin_participants_message_participant_approwed');
                $this->_action_success();
                $this->output->set_internal_value('student_id', $student->id);
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_output(json_encode($output));
    }
    
    public function disapprove_participation(): void
    {
        $this->output->set_content_type('application/json');
        $output = [
            'status'  => false,
            'message' => $this->lang->line('admin_participants_message_participant_not_found'),
        ];
        $url = $this->uri->ruri_to_assoc(3);
        $participant_id = isset($url['participant_id']) ? (int)$url['participant_id'] : 0;
        if ($participant_id > 0) {
            $process_ok = true;
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $participant = new Participant();
            $participant->get_by_id($participant_id);
            $student_id = $participant->student_id;
            
            if (!$participant->exists()) {
                $output['message'] = $this->lang->line('admin_participants_message_participant_not_found');
                $process_ok = false;
            }
            
            if ($process_ok) {
                if ($participant->allowed == 0) {
                    $participant->delete();
                    $output['message'] = $this->lang->line('admin_participants_message_participant_disapprowed');
                } else {
                    $output['message'] = $this->lang->line(
                        'admin_participants_message_participant_cant_be_disapprowed'
                    );
                    $process_ok = false;
                }
            }
            
            $output['status'] = $process_ok;
            if ($output['status']) {
                $this->db->trans_commit();
                $this->_action_success();
                $this->output->set_internal_value('student_id', $student_id);
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_output(json_encode($output));
    }
    
    public function delete_participation(): void
    {
        $this->output->set_content_type('application/json');
        $output = [
            'status'  => false,
            'message' => $this->lang->line('admin_participants_message_participant_not_found'),
        ];
        $url = $this->uri->ruri_to_assoc(3);
        $participant_id = isset($url['participant_id']) ? (int)$url['participant_id'] : 0;
        if ($participant_id > 0) {
            $process_ok = true;
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $participant = new Participant();
            $participant->get_by_id($participant_id);
            
            $student = $participant->student->get();
            $course = $participant->course->get();
            
            if (!$participant->exists()) {
                $output['message'] = $this->lang->line('admin_participants_message_participant_not_found');
                $process_ok = false;
            }
            
            if ($process_ok) {
                if ($participant->allowed != 0) {
                    $participant->delete();
                    if ($student->is_related_to('active_course', $course->id)) {
                        $student->delete($course, 'active_course');
                    }
                    $output['message'] = $this->lang->line('admin_participants_message_participant_deleted');
                } else {
                    $output['message'] = $this->lang->line(
                        'admin_participants_message_participant_cant_be_deleted'
                    );
                    $process_ok = false;
                }
            }
            
            $output['status'] = $process_ok;
            if ($output['status']) {
                $this->db->trans_commit();
                $this->_action_success();
                $this->output->set_internal_value('student_id', $student->id);
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_output(json_encode($output));
    }
    
    public function add_participant_form(): void
    {
        $this->inject_courses();
        $this->parser->parse('backend/participants/add_participant_form.tpl');
    }
    
    public function add_participant(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'participant[course]',
            'lang:admin_participants_form_field_course',
            'required'
        );
        $this->form_validation->set_rules(
            'participant[students][]',
            'lang:admin_participants_form_field_students',
            'required'
        );
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $process_ok = true;
            
            $participant_data = $this->input->post('participant');
            
            $course = new Course();
            $course->get_by_id((int)$participant_data['course']);
            $group = new Group();
            $group->get_by_id((int)@$participant_data['group']);
            
            if (!$course->exists()) {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_participants_message_course_not_exists',
                    Messages::MESSAGE_TYPE_ERROR
                );
                $process_ok = false;
            }
            
            if ($process_ok && $course->exists()) {
                if ($group->exists() && !$group->is_related_to($course)) {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_participants_message_group_not_belongs_to_course',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                    $process_ok = false;
                }
            }
            
            $disapproved = 0;
            $added = 0;
            if ($process_ok) {
                foreach ($participant_data['students'] as $student_id) {
                    $student = new Student();
                    $student->where_related('participant/course', 'id', $course->id);
                    $student->get_by_id($student_id);
                    
                    if ($student->exists()) {
                        continue;
                    }
                    
                    $student->get_by_id($student_id);
                    
                    $participant = new Participant();
                    $participant->allowed = (int)@$participant_data['allowed'];
                    $participant->save([$student, $course, $group]);
                    $added++;
                    
                    if ($participant->allowed === 1) {
                        $disallowed_participant = false;
                        if ($course->participant->where('allowed', 1)->count() > (int)$course->capacity) {
                            $disallowed_participant = true;
                        }
                        
                        if ($group->exists()) {
                            $group_for_test = new Group();
                            $rooms = $group_for_test->room;
                            $rooms->select_min('capacity');
                            $rooms->where('group_id', '${parent}.id', false);
                            $group_for_test->select_subquery($rooms, 'group_capacity');
                            $group_for_test->include_related_count('participant');
                            $group_for_test->where_related_participant('allowed', 1);
                            $group_for_test->get_by_id((int)$group->id);
                            if ($group_for_test->exists()) {
                                if ((int)$group_for_test->participant_count > (int)$group_for_test->group_capacity) {
                                    $disallowed_participant = true;
                                }
                            }
                        }
                        
                        if ($disallowed_participant) {
                            $participant->allowed = 0;
                            $participant->save();
                            $disapproved++;
                        }
                    }
                }
            }
            
            if ($this->db->trans_status() && $process_ok) {
                $this->db->trans_commit();
                $info_approved = (int)@$participant_data['allowed'] === 1 ? $added - $disapproved : 0;
                $info_disapproved = (int)@$participant_data['allowed'] === 1 ? $disapproved : $added;
                $message = sprintf(
                    $this->lang->line('admin_participants_message_addition_successfull'),
                    $info_approved,
                    $info_disapproved
                );
                $this->messages->add_message($message, Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_participants_messages_error_in_addition_transaction',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            
            redirect(create_internal_url('admin_participants/add_participant_form'));
        } else {
            $this->add_participant_form();
        }
    }
    
    public function get_all_students(): void
    {
        $this->output->set_content_type('application/json');
        $students = new Student();
        $students->order_by('fullname', 'asc');
        $students->or_like('fullname', trim($this->input->get('term')));
        $students->or_like('email', trim($this->input->get('term')));
        $students->get_iterated();
        $data = [];
        foreach ($students as $student) {
            $data[] = ['value' => $student->fullname . ' (' . $student->email . ')', 'id' => $student->id];
        }
        $this->output->set_output(json_encode($data));
    }
    
    public function change_group($participant_id): void
    {
        $group_id = $this->input->post('group_id');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $participant = new Participant();
        $participant->get_by_id($participant_id);
        
        $group = new Group();
        $group->get_by_id($group_id);
        
        $course = $participant->course->get();
        if ($group->exists()) {
            if ($group->is_related_to($course)) {
                $participant->save($group);
            }
        } else {
            $current_group = $participant->group->get();
            $participant->delete($current_group);
        }
        
        $is_ok = true;
        
        if ($group->exists()) {
            if ($participant->allowed == 1) {
                $group_for_test = new Group();
                $rooms = $group_for_test->room;
                $rooms->select_min('capacity');
                $rooms->where('group_id', '${parent}.id', false);
                $group_for_test->select_subquery($rooms, 'group_capacity');
                $group_for_test->include_related_count('participant');
                $group_for_test->where_related_participant('allowed', 1);
                $group_for_test->get_by_id((int)$participant->group_id);
                if ($group_for_test->exists()) {
                    if ((int)$group_for_test->participant_count > (int)$group_for_test->group_capacity) {
                        $is_ok = false;
                    }
                }
            }
        }
        
        if ($is_ok && $this->db->trans_status()) {
            $this->db->trans_commit();
            $this->_action_success();
            $this->output->set_internal_value('student_id', $participant->student_id);
            $this->output->set_internal_value('course_id', $participant->course_id);
        } else {
            $this->db->trans_rollback();
        }
        
        $participant->include_related('group', 'name');
        $participant->get_by_id($participant_id);
        
        $this->parser->parse('backend/participants/group_column.tpl', ['participant' => $participant]);
    }
    
    private function store_filter($filter): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_FILTER_SESSION_NAME, 'course');
            $this->filter->set_filter_delete_on_course_change(self::STORED_FILTER_SESSION_NAME, ['group']);
        }
    }
    
    private function inject_stored_filter(): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_FILTER_SESSION_NAME,
            $this->usermanager->get_teacher_id(),
            'course'
        );
        $this->parser->assign('filter', $filter);
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
}
