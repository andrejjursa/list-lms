<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Courses controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Courses extends LIST_Controller {
    
    const REGEXP_PATTERN_DATETYME = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';
    const STORED_FILTER_SESSION_NAME = 'admin_courses_filter_data';
    
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
        $this->_select_teacher_menu_pagetag('courses');
        
        $this->inject_periods();
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_courses/list.js');
        $this->parser->add_js_file('admin_courses/form.js');
        $this->parser->add_css_file('admin_courses.css');
        $this->_add_tinymce4();
        
        $this->inject_stored_filter();
        $this->parser->parse('backend/courses/index.tpl');
    }
    
    public function get_table_content() {
        $fields_config = array(
            array('name' => 'created', 'caption' => 'lang:common_table_header_created'),
            array('name' => 'updated', 'caption' => 'lang:common_table_header_updated'),
            array('name' => 'name', 'caption' => 'lang:admin_courses_table_header_course_name'),
            array('name' => 'description', 'caption' => 'lang:admin_courses_table_header_course_description'),
            array('name' => 'period', 'caption' => 'lang:admin_courses_table_header_course_period'),
            array('name' => 'groups', 'caption' => 'lang:admin_courses_table_header_course_groups'),
            array('name' => 'task_set_types', 'caption' => 'lang:admin_courses_table_header_course_task_set_types'),
            array('name' => 'task_set_count', 'caption' => 'lang:admin_courses_table_header_course_task_set_count'),
            array('name' => 'capacity', 'caption' => 'lang:admin_courses_table_header_course_capacity'),
        );
        
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        
        $courses = new Course();
        $courses->include_related_count('group');
        $courses->include_related_count('task_set_type');
        $courses->include_related_count('task_set');
        $courses->include_related('period', 'name', TRUE);
        $order_by_direction = $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] == 'name') {
            $courses->order_by_with_constant('name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'period') {
            $courses->order_by_related('period', 'sorting', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'created') {
            $courses->order_by('created', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'updated') {
            $courses->order_by('updated', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'groups') {
            $courses->order_by('group_count', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'task_set_types') {
            $courses->order_by('task_set_type_count', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'task_set_count') {
            $courses->order_by('task_set_count', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'capacity') {
            $courses->order_by('capacity', $order_by_direction);
        }
        $courses->get_iterated();
        $this->lang->init_overlays('courses', $courses->all_to_array(), array('description'));
        $this->parser->parse('backend/courses/table_content.tpl', array('courses' => $courses, 'fields_config' => $fields_config));
    }
    
    public function create() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('course[name]', 'lang:admin_courses_form_field_name', 'required');
        $this->form_validation->set_rules('course[period_id]', 'lang:admin_courses_form_field_period', 'required');
        $this->form_validation->set_rules('course[capacity]', 'lang:admin_courses_form_field_capacity', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('course[default_points_to_remove]', 'lang:admin_courses_form_field_default_points_to_remove', 'required|numeric|greater_than[0]');
        
        if ($this->form_validation->run()) {
            $course = new Course();
            $course_data = $this->input->post('course');
            $course->from_array($course_data, array('name', 'period_id', 'capacity', 'default_points_to_remove'));
            $course->allow_subscription_to = preg_match(self::REGEXP_PATTERN_DATETYME, $course_data['allow_subscription_to']) ? $course_data['allow_subscription_to'] : NULL;
            $course->groups_change_deadline = preg_match(self::REGEXP_PATTERN_DATETYME, $course_data['groups_change_deadline']) ? $course_data['groups_change_deadline'] : NULL;
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            if ($course->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_courses_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_courses_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_courses/new_course_form'));
        } else {
            $this->new_course_form();
        }
    }
    
    public function new_course_form() {
        $this->inject_periods();
        $this->parser->parse('backend/courses/new_course_form.tpl');
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        if ($course_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $course = new Course();
            $course->get_by_id($course_id);
            $course->delete();
            $this->lang->delete_overlays('courses', intval($course_id));
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->output->set_output(json_encode(TRUE));    
                $this->_action_success();
                $this->output->set_internal_value('course_id', $course_id);
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(FALSE));                
            }
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('courses');
        
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_courses/list.js');
        $this->parser->add_js_file('admin_courses/form.js');
        $this->parser->add_js_file('admin_courses/edit.js');
        
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        $course = new Course();
        $course->get_by_id($course_id);
        $this->inject_periods();
        $this->inject_languages();
        $this->_add_tinymce4();
        $this->lang->load_all_overlays('courses', $course_id);
        
        $this->parser->parse('backend/courses/edit.tpl', array('course' => $course));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('course_id', 'id', 'required');
        $this->form_validation->set_rules('course[name]', 'lang:admin_courses_form_field_name', 'required');
        $this->form_validation->set_rules('course[period_id]', 'lang:admin_courses_form_field_period', 'required');
        $this->form_validation->set_rules('course[capacity]', 'lang:admin_courses_form_field_capacity', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('course[default_points_to_remove]', 'lang:admin_courses_form_field_default_points_to_remove', 'required|numeric|greater_than[0]');
        
        if ($this->form_validation->run()) {
            $course_id = intval($this->input->post('course_id'));
            $course = new Course();
            $course->get_by_id($course_id);
            if ($course->exists()) {
                $course_data = $this->input->post('course');
                $course->from_array($course_data, array('name', 'period_id', 'capacity', 'default_points_to_remove'));
                $course->description = remove_base_url($course_data['description']);
                $course->allow_subscription_to = preg_match(self::REGEXP_PATTERN_DATETYME, $course_data['allow_subscription_to']) ? $course_data['allow_subscription_to'] : NULL;
                $course->groups_change_deadline = preg_match(self::REGEXP_PATTERN_DATETYME, $course_data['groups_change_deadline']) ? $course_data['groups_change_deadline'] : NULL;
                
                $overlay = $this->input->post('overlay');
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($course->save() && $this->lang->save_overlay_array(remove_base_url_from_overlay_array($overlay, 'description')) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_courses_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                    $this->_action_success();
                    $this->output->set_internal_value('course_id', $course->id);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_courses_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_courses_error_course_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_courses/index'));
        } else {
            $this->edit();
        }
    }
    
    public function mail_to_course($course_id) {
        $course = new Course();
        $course->include_related('period', 'name');
        $course->get_by_id((int)$course_id);
        
        if ($course->exists()) {
            $groups = new Group();
            $groups->where_related_course('id', $course->id);
            $groups->order_by_with_constant('name', 'asc');
            $groups->get_iterated();
            
            $groups_students = array();
            
            foreach ($groups as $group) {
                $groups_students[$group->id] = array(
                    'name' => $group->name,
                    'students' => array(),
                );
            }
            
            $groups_students[0] = array(
                'name' => 'lang:admin_courses_mail_to_course_group_name_unassigned_students',
                'students' => array(),
            );
            
            $participants = new Participant();
            $participants->where('allowed', 1);
            $participants->include_related('student');
            $participants->where_related_course('id', $course->id);
            $participants->order_by_related_as_fullname('student', 'fullname', 'asc');
            $participants->get_iterated();
            
            foreach($participants as $participant) {
                $groups_students[(int)$participant->group_id]['students'][(int)$participant->student_id] = array(
                    'fullname' => $participant->student_fullname,
                    'email' => $participant->student_email,
                );
            }
            
            $this->parser->assign('groups_students', $groups_students);
        }
        
        $this->_add_tinymce4();
        
        $this->parser->add_js_file('admin_courses/mail_form.js');
        $this->parser->add_css_file('admin_courses.css');
        $this->parser->parse('backend/courses/mail_to_course.tpl', array('course' => $course));
    }
    
    public function send_mail_to_course($course_id) {
        $course = new Course();
        $course->get_by_id($course_id);
        if ($course->exists()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('course_mail[subject]', 'lang:admin_courses_mail_to_course_form_field_subject', 'required');
            $this->form_validation->set_rules('course_mail[body]', 'lang:admin_courses_mail_to_course_form_field_body', 'required_no_html');
            $this->form_validation->set_rules('course_mail[from]', 'lang:admin_courses_mail_to_course_form_field_from', 'required');
            $this->form_validation->set_rules('course_mail[student][]', 'lang:admin_courses_mail_to_course_form_field_students', 'required');
            if ($this->form_validation->run()) {
                $data = $this->input->post('course_mail');
                $students = new Student();
                $students->where_related('participant/course', 'id', $course->id);
                $students->where_related('participant', 'allowed', 1);
                $students->where_in('id', $data['student']);
                $students->get();
                if ($students->exists()) {
                    $from = NULL;
                    $from_name = '';
                    $teacher = new Teacher();
                    $teacher->get_by_id($this->usermanager->get_teacher_id());
                    if ($data['from'] == 'me') {
                        $from = $teacher->email;
                        $from_name = $teacher->fullname;
                    }
                    $sender_copy = isset($data['sender_copy']) && $data['sender_copy'] == 1 ? TRUE : FALSE;
                    $sender_email = $teacher->email;
                    if ($this->_send_multiple_emails($students, $data['subject'], '{$data.body|add_base_url}', array('data' => $data), $from, $from_name, $sender_copy, $sender_email)) {
                        $this->messages->add_message('lang:admin_courses_mail_to_course_success_sent', Messages::MESSAGE_TYPE_SUCCESS);
                    } else {
                        $this->messages->add_message('lang:admin_courses_mail_to_course_error_send_failed', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->messages->add_message('lang:admin_courses_mail_to_course_error_no_students_selected', Messages::MESSAGE_TYPE_ERROR);
                }
                redirect(create_internal_url('admin_courses/mail_to_course/' . $course_id));
            } else {
                $this->mail_to_course($course_id);
            }
        } else {
            $this->messages->add_message('lang:admin_courses_mail_to_course_error_course_not_found', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_courses/mail_to_course/' . $course_id));
        }
    }

    public function task_set_types() {
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        $course = new Course();
        $course->get_by_id($course_id);
        $this->inject_unused_task_set_types($course_id);
        $this->parser->add_js_file('admin_courses/list.js');
        $this->parser->add_css_file('admin_courses.css');
        $this->parser->parse('backend/courses/task_set_types.tpl', array('course' => $course));    
    }
    
    public function get_task_set_types() {
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        $course = new Course();
        $course->get_by_id($course_id);
        $course->task_set_type->order_by_with_constant('name', 'asc')->include_join_fields()->get_iterated();
        $this->parser->parse('backend/courses/task_set_types_content.tpl', array('task_set_types' => $course->task_set_type, 'course' => $course));
    }
    
    public function get_task_set_type_form() {
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        $this->inject_unused_task_set_types($course_id);
        $this->parser->parse('backend/courses/add_task_set_type_form.tpl');
    }
    
    public function add_task_set_type() {
        $this->load->library('form_validation');
        
        $url = $this->uri->ruri_to_assoc(3);
        $course_id = isset($url['course_id']) ? intval($url['course_id']) : 0;
        
        $this->form_validation->set_rules('task_set_type[id]', 'lang:admin_courses_form_field_task_set_type_name', 'required');
        $this->form_validation->set_rules('task_set_type[join_upload_solution]', 'lang:admin_courses_form_field_upload_solution', 'required');
        
        if ($this->form_validation->run()) {
            $task_set_type_data = $this->input->post('task_set_type');
            $course = new Course();
            $course->get_by_id($course_id);
            $task_set_type = new Task_set_type();
            $task_set_type->get_by_id(intval($task_set_type_data['id']));
            if ($course->exists() && $task_set_type->exists()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                $course->save($task_set_type);
                $course->set_join_field($task_set_type, 'upload_solution', intval($task_set_type_data['join_upload_solution']));
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_courses_flash_message_task_set_type_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_courses_flash_message_task_set_type_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_courses_flash_message_task_set_type_save_failed', Messages::MESSAGE_TYPE_ERROR);    
            }
            redirect(create_internal_url('admin_courses/get_task_set_type_form/course_id/' . $course_id));
        } else {
            $this->get_task_set_type_form();    
        }    
    }
    
    public function save_task_set_type() {
        $this->output->set_content_type('application/json');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('upload_solution', 'upload_solution', 'required');
        $this->form_validation->set_rules('task_set_type_id', 'task_set_type_id', 'required');
        $this->form_validation->set_rules('course_id', 'course_id', 'required');
        
        if ($this->form_validation->run()) {
            $course_id = intval($this->input->post('course_id'));
            $task_set_type_id = intval($this->input->post('task_set_type_id'));
            $upload_solution = intval($this->input->post('upload_solution'));
            
            $course = new Course();
            $course->get_by_id($course_id);
            
            $task_set_type = new Task_set_type();
            $task_set_type->get_by_id($task_set_type_id);
            
            if ($course->exists() && $task_set_type->exists()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                $task_set_type->set_join_field($course, 'upload_solution', $upload_solution);
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->output->set_output(json_encode(TRUE));
                    $this->_action_success();
                    return;    
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
        $this->output->set_output(json_encode(FALSE));        
    }
    
    public function delete_task_set_type() {
        $this->output->set_content_type('application/json');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_set_type_id', 'task_set_type_id', 'required');
        $this->form_validation->set_rules('course_id', 'course_id', 'required');
        
        if ($this->form_validation->run()) {
            $course_id = intval($this->input->post('course_id'));
            $task_set_type_id = intval($this->input->post('task_set_type_id'));
            
            $course = new Course();
            $course->get_by_id($course_id);
            
            $task_set_type = new Task_set_type();
            $task_set_type->get_by_id($task_set_type_id); 
            
            if ($course->exists() && $task_set_type->exists()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                $course->delete($task_set_type);
                
                $task_sets = new Task_set();
                $task_sets->where_related_course('id', $course_id)->get_iterated();
                foreach ($task_sets as $task_set) {
                    $task_set->delete($task_set_type);
                }
                
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->output->set_output(json_encode(TRUE));
                    return;    
                } else {
                    $this->db->trans_rollback();
                }
            }   
        }
        $this->output->set_output(json_encode(FALSE));    
    }
    
    public function download_solutions($course_id) {
        $course = new Course();
        $course->get_by_id((int)$course_id);
        if ($course->exists()) {
            $course->download_all_solutions();
        } else {
            $this->messages->add_message('lang:admin_courses_message_cant_download_solutions', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_courses'));
        }
    }

    private function inject_periods() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $query = $periods->get_raw();
        $data = array(
            NULL => '',
        );
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $data[(int) $row->id] = $row->name;
        }}
        $this->parser->assign('periods', $data);
        $query->free_result();
    }
    
    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
    private function inject_unused_task_set_types($course_id) {
        $course = new Course();
        $course->get_by_id(intval($course_id));
        $course->task_set_type->get();
        $course_task_set_types = $course->task_set_type->all_to_single_array('id');
        $task_set_types = new Task_set_type();
        $task_set_types->where_not_in('id', count($course_task_set_types) > 0 ? $course_task_set_types : array( 0 ));
        $query = $task_set_types->order_by('name', 'asc')->get_raw();
        $data = array(
            NULL => '',
        );
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $data[(int) $row->id] = $row->name;    
        }}
        $this->parser->assign('task_set_types', $data);
        $query->free_result();
    }
    
    private function store_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
        $this->parser->assign('filter', $filter);
    }
    
}