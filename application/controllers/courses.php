<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Courses controller for frontend.
 *
 * @package LIST_FE_Controllers
 * @author  Andrej Jursa
 */
class Courses extends LIST_Controller
{
    
    public const LIST_OF_COURSES_FILTER_NAME = 'frontend_courses_list';
    
    public function __construct()
    {
        parent::__construct();
        if ($this->router->method !== 'show_details' && $this->router->method !== 'show_description') {
            $this->usermanager->student_login_protected_redirect();
        }
        $this->_init_language_for_student();
        $this->_load_student_langfile();
    }
    
    public function index(): void
    {
        $period_id = $this->input->post('period_id');
        $filter = $this->inject_stored_filter();
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('courses');
        $this->parser->add_css_file('frontend_courses.css');
        $this->parser->add_js_file('courses/selection.js');
        
        $cache_id = $this->usermanager->get_student_cache_id('period_' . ($period_id ?: @$filter['period_id']));
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/courses/index.tpl'), $cache_id)) {
            $periods = new Period();
            if ((int)$period_id === 0) {
                if (isset($filter['period_id'])) {
                    $periods->where('id', $filter['period_id']);
                } else {
                    $periods->limit(1);
                }
            } else {
                $periods->where('id', $period_id);
            }
            $periods->order_by('sorting', 'asc')->get();
            $filter['period_id'] = $periods->id;
            $this->store_filter($filter);
            $this->inject_period_options();
            $this->parser->assign(['periods' => $periods]);
        }
        $this->parser->parse('frontend/courses/index.tpl', [], false, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function signup_to_course($course_id): void
    {
        $this->output->set_content_type('application/json');
        
        $output = new stdClass();
        $output->status = false;
        $output->message = '';
        $output->content = '';
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where('hide_in_lists', 0);
        $course->get_by_id($course_id);
        
        if ($course->exists()) {
            if ($course->is_subscription_allowed()) {
                if ($student->participant->where_related($course)->count() === 0) {
                    if ((bool)$course->auto_accept_students === true) {
                        $participants = new Participant();
                        $participants->where_related_course($course);
                        $participants->where('allowed', 1);
                        $participants_count = $participants->count();
                        if ($participants_count >= (int)$course->capacity) {
                            $output->message = $this->lang->line('courses_message_course_is_full');
                            $output->status = false;
                            $this->db->trans_rollback();
                        } else {
                            $participant = new Participant();
                            $participant->allowed = 1;
                            $participant->save([$student, $course]);
                            $this->db->trans_commit();
                            $output->message = sprintf($this->lang->line('courses_message_signed_up_for_course_approved'), $this->lang->text($course->name));
                            $this->parser->assign('course', $course);
                            $output->content = $this->parser->parse('frontend/courses/single_course.tpl', [], true);
                            $output->status = true;
                            $this->_action_success();
                        }
                    } else {
                        $participant = new Participant();
                        $participant->allowed = 0;
                        $participant->save([$student, $course]);
                        $this->db->trans_commit();
                        $output->message = sprintf($this->lang->line('courses_message_signed_up_for_course'), $this->lang->text($course->name));
                        $this->parser->assign('course', $course);
                        $output->content = $this->parser->parse('frontend/courses/single_course.tpl', [], true);
                        $output->status = true;
                        $this->_action_success();
                    }
                } else {
                    $output->message = $this->lang->line('courses_message_already_in_course_or_waiting_for_approwal');
                    $this->db->trans_rollback();
                }
            } else {
                $output->message = $this->lang->line('courses_message_subscription_disallowed');
                $this->db->trans_rollback();
            }
        } else {
            $output->message = $this->lang->line('courses_message_course_not_found');
            $this->db->trans_rollback();
        }
        
        $this->output->set_output(json_encode($output));
    }
    
    public function activate_course($course_id): void
    {
        $this->_initialize_student_menu();
        $this->output->set_content_type('application/json');
        
        $output = new stdClass();
        $output->status = false;
        $output->message = '';
        $output->content = '';
        $output->metainfo = '';
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->get_by_id($course_id);
        
        if ($course->exists()) {
            $participant = $student->participant->where_related($course)->where('allowed', 1)->get();
            if ($participant->exists()) {
                $student->save($course, 'active_course');
                $this->db->trans_commit();
                $this->usermanager->set_student_data_to_smarty();
                $period = $course->period->get();
                $output->content = $this->parser->parse('frontend/courses/period_courses.tpl', ['period' => $period], true);
                $output->metainfo = $this->parser->parse('partials/frontend_general/selected_course.tpl', [], true);
                $output->status = true;
                $output->message = sprintf($this->lang->line('courses_message_switched_to_course'), $this->lang->text($course->name) . ' / ' . $this->lang->text($period->name));
                $this->_action_success();
            } else {
                $output->message = $this->lang->line('courses_message_cant_switch_to_unsigned_course');
                $this->db->trans_rollback();
            }
        } else {
            $output->message = $this->lang->line('courses_message_course_not_found');
            $this->db->trans_rollback();
        }
        
        $this->output->set_output(json_encode($output));
    }
    
    public function show_details($course_id, $lang = null): void
    {
        $this->parser->add_css_file('frontend_courses.css');
        if (!is_null($lang)) {
            $this->_init_specific_language($lang);
        }
        $cache_id = 'course_' . $course_id . '|lang_' . $this->lang->get_current_idiom();
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/courses/course_details.tpl'), $cache_id)) {
            $course = new Course();
            $course->include_related('period');
            $course->get_by_id($course_id);
            smarty_inject_days();
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse('frontend/courses/course_details.tpl', [], false, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function show_description($course_id, $lang = null): void
    {
        $this->parser->add_css_file('frontend_courses.css');
        if (!is_null($lang)) {
            $this->_init_specific_language($lang);
        }
        $cache_id = 'course_' . $course_id . '|lang_' . $this->lang->get_current_idiom();
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/courses/show_description.tpl'), $cache_id)) {
            $course = new Course();
            $course->include_related('period');
            $course->get_by_id((int)$course_id);
            smarty_inject_days();
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse('frontend/courses/show_description.tpl', [], false, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function course_description(): void
    {
        $this->parser->add_css_file('frontend_courses.css');
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('course_description');
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course_id = $student->active_course_id ?? 'none';
        $cache_id = 'student_' . $student->id . '|course_' . $course_id . '|lang_' . $this->lang->get_current_idiom();
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/courses/course_description.tpl'), $cache_id)) {
            $course = new Course();
            $course->include_related('period');
            $course->get_by_id((int)$course_id);
            smarty_inject_days();
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse('frontend/courses/course_description.tpl', [], false, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function quick_course_change($course_id, $current_url): void
    {
        $this->activate_course($course_id);
        $output = $this->output->get_output();
        $output_object = json_decode($output);
        $this->messages->add_message($output_object->message, $output_object->status ? Messages::MESSAGE_TYPE_SUCCESS : Messages::MESSAGE_TYPE_ERROR);
        
        $decoded_current_url = decode_from_url($current_url);
        redirect($decoded_current_url);
    }
    
    private function inject_period_options(): void
    {
        $periods = new Period();
        $periods->order_by('sorting', 'asc')->get_iterated();
        
        $data = [];
        
        foreach ($periods as $period) {
            $data[$period->id] = $period->name;
        }
        
        $this->parser->assign('period_options', $data);
    }
    
    private function store_filter($filter): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::LIST_OF_COURSES_FILTER_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::LIST_OF_COURSES_FILTER_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter(): array
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::LIST_OF_COURSES_FILTER_NAME);
        $this->parser->assign('filter', $filter);
        return $filter;
    }
}