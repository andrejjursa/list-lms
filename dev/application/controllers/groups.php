<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Groups controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Groups extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->usermanager->student_login_protected_redirect();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
    }
    
    public function index() {
        $cache_id = $this->usermanager->get_student_cache_id();
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/groups/index.tpl'), $cache_id)) {
            $this->_initialize_student_menu();
            $this->_select_student_menu_pagetag('groups');
            $student = new Student();
            $student->get_by_id($this->usermanager->get_student_id());

            $course = new Course();
            $course->where_related_active_for_student($student);
            $course->where_related('participant/student', $student);
            $course->where_related_participant('allowed', 1);
            $course->get();

            $can_change_group = FALSE;

            if ($course->exists()) {
                if (is_null($course->groups_change_deadline) || date('U', strtotime($course->groups_change_deadline)) >= time()) { $can_change_group = TRUE; }
            }

            smarty_inject_days();
            $this->parser->add_css_file('frontend_groups.css');
            $this->parser->assign(array('course' => $course, 'can_change_group' => $can_change_group));
        }
        $this->parser->parse('frontend/groups/index.tpl', array(), FALSE, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function select_group() {
        $group_id = $this->input->post('group_id');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $group = new Group();
        $group->get_by_id($group_id);
        
        if ($group->exists()) {
            $course = $group->course->get();
            
            if (is_null($course->groups_change_deadline) || date('U', strtotime($course->groups_change_deadline)) >= time()) {

                $student = new Student();
                $student->get_by_id($this->usermanager->get_student_id());

                if ($student->is_related_to('active_course', $course->id)) {
                    $participant = new Participant();
                    $participant->where_related($student);
                    $participant->where_related($course);
                    $participant->where('allowed', 1);
                    $participant->get();
                    if ($participant->exists()) {
                        if (!$participant->is_related_to($group)) {
                            $participant->save($group);
                            $participant->where_related($course);
                            $participant->where_related($group);
                            $participant->where('allowed', 1);
                            $participants_count = $participant->count();
                            $room = new Room();
                            $room->where_related($group)->order_by('capacity', 'asc')->limit(1)->get();
                            if ($participants_count > intval($room->capacity)) {
                                $this->db->trans_rollback();
                                $this->messages->add_message('lang:groups_message_group_is_full', Messages::MESSAGE_TYPE_ERROR);
                            } else {
                                $this->db->trans_commit();
                                $this->messages->add_message(sprintf($this->lang->line('groups_message_group_changed'), $this->lang->text($group->name)), Messages::MESSAGE_TYPE_SUCCESS);
                                $this->_action_success();
                                $this->output->set_internal_value('course_id', $participant->course_id);
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->messages->add_message('lang:groups_message_you_are_in_group', Messages::MESSAGE_TYPE_ERROR);
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:groups_message_cant_found_participant_record', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:groups_message_cant_change_group_of_inactive_course', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:groups_message_groups_switching_disabled', Messages::MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:groups_message_group_not_found', Messages::MESSAGE_TYPE_ERROR);
        }
        
        redirect(create_internal_url('groups'));
    }
    
}