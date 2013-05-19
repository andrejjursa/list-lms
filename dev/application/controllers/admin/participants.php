<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Participants controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Participants extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_participants_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('participants');
        $this->inject_stored_filter();
        $this->inject_courses();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_participants/list.js');
        $this->parser->add_css_file('admin_participants.css');
        $this->parser->parse('backend/participants/index.tpl');
    }
    
    public function table_content() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $participants = new Participant();
        $participants->include_related('student', 'fullname');
        $participants->include_related('student', 'email');
        $participants->include_related('course', 'name');
        $participants->include_related('course/period', 'name');
        $participants->include_related('group', 'name');
        if (isset($filter['student_fullname']) && trim($filter['student_fullname']) != '') {
            $participants->like_related('student', 'fullname', trim($filter['student_fullname']));
        }
        if (isset($filter['course']) && intval($filter['course']) > 0) {
            $participants->where_related('course', 'id', intval($filter['course']));
        }
        if (isset($filter['group']) && intval($filter['group']) > 0) {
            $participants->where_related('group', 'id', intval($filter['group']));
        }
        if (isset($filter['group_set'])) {
            if ($filter['group_set'] == 'none') {
                $participants->where('group_id', NULL);
            } else if ($filter['group_set'] == 'assigned') {
                $participants->group_start(' NOT', 'AND');
                $participants->where('group_id', NULL);
                $participants->group_end();
            }
        }
        $participants->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->parser->parse('backend/participants/table_content.tpl', array('participants' => $participants));
    }
    
    public function get_groups_from_course($course_id, $selected_id = NULL) {
        $groups = new Group();
        $groups->select('id, name');
        $groups->where_related_course('id', $course_id);
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        $options = array(
            '' => ''
        );
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $this->parser->parse('backend/participants/groups_from_course.tpl', array('groups' => $options, 'selected' => $selected_id));
    }
    
    public function approve_participation() {
        $this->output->set_content_type('application/json');
        $output = array(
            'status' => FALSE,
            'message' => $this->lang->line('admin_participants_message_participant_not_found'),
        );
        $url = $this->uri->ruri_to_assoc(3);
        $participant_id = isset($url['participant_id']) ? intval($url['participant_id']) : 0;
        if ($participant_id > 0) {
            $process_ok = TRUE;
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $participant = new Participant();
            $participant->get_by_id($participant_id);
            $participant->allowed = 1;
            $participant->save();
                 
            $student = $participant->student->get();
            if (!$participant->exists() || !$student->exists()) {
                $output['message'] = $this->lang->line('admin_participants_message_participant_not_found');
                $process_ok = FALSE;
            }
            
            if ($process_ok) {
                $course = $participant->course->get();
                if (!$course->exists()) {
                    $output['message'] = $this->lang->line('admin_participants_message_course_not_set');
                    $process_ok = FALSE;
                }
                if ($course->participant->where('allowed', 1)->count() > intval($course->capacity)) {
                    $output['message'] = $this->lang->line('admin_participants_message_course_is_full');
                    $process_ok = FALSE;
                }
            }
            
            if ($process_ok) {
                $group = new Group();
                $rooms = $group->room;
                $rooms->select_min('capacity');
                $rooms->where('group_id', '${parent}.id', FALSE);
                $group->select_subquery($rooms, 'group_capacity');
                $group->include_related_count('participant');
                $group->where_related_participant('allowed', 1);
                $group->get_by_id(intval($participant->group_id));
                if ($group->exists()) {
                    if (intval($group->participant_count) > intval($group->group_capacity)) {
                        $output['message'] = $this->lang->line('admin_participants_message_group_is_full');
                        $process_ok = FALSE;
                    }
                }
            }
            
            $output['status'] = $process_ok;
            if ($output['status']) {
                $this->db->trans_commit();
                $output['message'] = $this->lang->line('admin_participants_message_participant_approwed');
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_output(json_encode($output));
    }

    public function disapprove_participation() {
        $this->output->set_content_type('application/json');
        $output = array(
            'status' => FALSE,
            'message' => $this->lang->line('admin_participants_message_participant_not_found'),
        );
        $url = $this->uri->ruri_to_assoc(3);
        $participant_id = isset($url['participant_id']) ? intval($url['participant_id']) : 0;
        if ($participant_id > 0) {
            $process_ok = TRUE;
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $participant = new Participant();
            $participant->get_by_id($participant_id);
            
            if (!$participant->exists()) {
                $output['message'] = $this->lang->line('admin_participants_message_participant_not_found');
                $process_ok = FALSE;
            }
            
            if ($process_ok) {
                if ($participant->allowed == 0) {
                    $participant->delete();
                    $output['message'] = $this->lang->line('admin_participants_message_participant_disapprowed');
                } else {
                    $output['message'] = $this->lang->line('admin_participants_message_participant_cant_be_disapprowed');
                    $process_ok = FALSE;
                }
            }
            
            $output['status'] = $process_ok;
            if ($output['status']) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_output(json_encode($output));
    }
    
    public function delete_participation() {
        $this->output->set_content_type('application/json');
        $output = array(
            'status' => FALSE,
            'message' => $this->lang->line('admin_participants_message_participant_not_found'),
        );
        $url = $this->uri->ruri_to_assoc(3);
        $participant_id = isset($url['participant_id']) ? intval($url['participant_id']) : 0;
        if ($participant_id > 0) {
            $process_ok = TRUE;
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $participant = new Participant();
            $participant->get_by_id($participant_id);
            
            if (!$participant->exists()) {
                $output['message'] = $this->lang->line('admin_participants_message_participant_not_found');
                $process_ok = FALSE;
            }
            
            if ($process_ok) {
                if ($participant->allowed != 0) {
                    $participant->delete();
                    $output['message'] = $this->lang->line('admin_participants_message_participant_deleted');
                } else {
                    $output['message'] = $this->lang->line('admin_participants_message_participant_cant_be_deleted');
                    $process_ok = FALSE;
                }
            }
            
            $output['status'] = $process_ok;
            if ($output['status']) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_output(json_encode($output));
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
}