<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Task sets controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Task_sets extends LIST_Controller {
	
    const STORED_FILTER_SESSION_NAME = 'admin_task_sets_filter_data';
    const REGEXP_PATTERN_DATETYME = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }

    public function index() {
        $this->_select_teacher_menu_pagetag('task_sets');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_task_sets/list.js');
        $this->parser->add_js_file('admin_task_sets/form.js');
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
    
    public function get_task_set_groups($course_id, $selected_id = NULL, $task_set_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $query = $course->group->order_by('name', 'asc')->get_raw();
        
        $groups = array('' => '');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $groups[$row->id] = $row->name;
        }}
        
        $query->free_result();
        
        $task_set = new Task_set();
        $task_set->where_related_course('id', $course_id)->get_by_id($task_set_id);
        
        $this->parser->parse('backend/task_sets/task_set_groups_options.tpl', array('groups' => $groups, 'task_set' => $task_set, 'selected_id' => $selected_id));
    }
    
    public function get_task_set_group_rooms($course_id, $group_id, $selected_id = NULL, $task_set_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $course->group->get_by_id($group_id);
        $query = $course->group->room->order_by('name', 'asc')->get_raw();
        
        $rooms = array('' => '');
        
        $days = get_days();
        include (APPPATH . 'third_party/Smarty/plugins/modifier.is_time.php');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $rooms[$row->id] = $this->lang->text($row->name) . ' (' . $days[$row->time_day] . ': ' . smarty_modifier_is_time($row->time_begin) . ' - ' . smarty_modifier_is_time($row->time_end) . ')';
        }}
        
        $query->free_result();
        
        $task_set = new Task_set();
        $task_set->where_related_course('id', $course_id)->get_by_id($task_set_id);
        
        $this->parser->parse('backend/task_sets/task_set_group_rooms_options.tpl', array('rooms' => $rooms, 'task_set' => $task_set, 'selected_id' => $selected_id));
    }
    
    public function get_all_task_sets() {
        $fields_config = array(
            array('name' => 'created', 'caption' => 'lang:common_table_header_created'),
            array('name' => 'updated', 'caption' => 'lang:common_table_header_updated'),
            array('name' => 'name', 'caption' => 'lang:admin_task_sets_table_header_name'),
            array('name' => 'course', 'caption' => 'lang:admin_task_sets_table_header_course'),
            array('name' => 'group', 'caption' => 'lang:admin_task_sets_table_header_group'),
            array('name' => 'task_set_type', 'caption' => 'lang:admin_task_sets_table_header_task_set_type'),
            array('name' => 'tasks', 'caption' => 'lang:admin_task_sets_table_header_tasks'),
            array('name' => 'published', 'caption' => 'lang:admin_task_sets_table_header_published'),
        );
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        $task_sets = new Task_set();
        $task_sets->order_by_with_overlay('name', 'asc');
        $task_sets->include_related('course', 'name', TRUE);
        $task_sets->include_related('course/period', 'name', TRUE);
        $task_sets->include_related('group', 'name', TRUE);
        $task_sets->include_related('task_set_type', 'name', TRUE);
        $task_sets->include_related_count('task');
        $task_sets->include_related_count('comment');
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
        if (isset($filter['name']) && trim($filter['name']) != '') {
            $name_value = trim($filter['name']);
            $task_sets->like_with_overlay('name', $name_value);
        }
        $task_sets->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), array('name'));
        $opened_task_set = new Task_set();
        $opened_task_set->get_as_open();
        $this->parser->parse('backend/task_sets/table_content.tpl', array('task_sets' => $task_sets, 'opened_task_set' => $opened_task_set, 'fields_config' => $fields_config));
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
            $task_set->from_array($task_set_data, array('name', 'course_id', 'task_set_type_id', 'published'));
            $task_set->group_id = intval($task_set_data['group_id']) > 0 ? intval($task_set_data['group_id']) : NULL;
            $task_set->room_id = intval($task_set_data['room_id']) > 0 ? intval($task_set_data['room_id']) : NULL;
            $task_set->publish_start_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['publish_start_time']) ? $task_set_data['publish_start_time'] : NULL;
            $task_set->upload_end_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['upload_end_time']) ? $task_set_data['upload_end_time'] : NULL;
            $task_set->comments_enabled = isset($task_set_data['comments_enabled']) ? (bool)intval($task_set_data['comments_enabled']) : FALSE;
            $task_set->comments_moderated = isset($task_set_data['comments_moderated']) ? (bool)intval($task_set_data['comments_moderated']) : FALSE;
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
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : intval($this->input->post('task_set_id'));
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $this->_add_tinymce();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_task_sets/edit.js');
        $this->parser->add_js_file('admin_task_sets/form.js');
        $this->parser->add_css_file('admin_task_sets.css');
        $this->inject_courses();
        $this->inject_languages();
        $this->parser->parse('backend/task_sets/edit.tpl', array('task_set' => $task_set));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
        $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
        $this->form_validation->set_rules('task_set[task_set_type_id]', 'lang:admin_task_sets_form_field_task_set_type_id', 'required|exists_in_table[task_set_types.id]');
        
        $task_set_id = intval($this->input->post('task_set_id'));
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $tasks = $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get();
        $tasks_join_fields_data = $this->input->post('task_join_field');
        if ($tasks->exists()) { foreach ($tasks->all as $task) {
            if (isset($tasks_join_fields_data[$task->id])) {
                if (!isset($tasks_join_fields_data[$task->id]['delete'])) {
                    $this->form_validation->set_rules('task_join_field[' . intval($task->id) . '][points_total]', 'lang:admin_task_sets_form_field_task_points_total', 'required|number|greater_than_equal[0]');
                }
            }
        }}
        
        if ($this->form_validation->run()) {    
            if ($task_set->exists()) {
                $task_set_data = $this->input->post('task_set');
                $task_set->from_array($task_set_data, array('name', 'course_id', 'task_set_type_id', 'published', 'instructions'));
                $task_set->group_id = intval($task_set_data['group_id']) > 0 ? intval($task_set_data['group_id']) : NULL;
                $task_set->room_id = intval($task_set_data['room_id']) > 0 ? intval($task_set_data['room_id']) : NULL;
                $task_set->publish_start_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['publish_start_time']) ? $task_set_data['publish_start_time'] : NULL;
                $task_set->upload_end_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['upload_end_time']) ? $task_set_data['upload_end_time'] : NULL;
                $task_set->comments_enabled = isset($task_set_data['comments_enabled']) ? (bool)intval($task_set_data['comments_enabled']) : FALSE;
                $task_set->comments_moderated = isset($task_set_data['comments_moderated']) ? (bool)intval($task_set_data['comments_moderated']) : FALSE;
                
                $overlay = $this->input->post('overlay');
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($tasks->exists()) {
                    $tasks_sorting = array_flip(explode(',', $this->input->post('tasks_sorting')));
                    foreach($tasks->all as $task) {
                        if (isset($tasks_join_fields_data[$task->id])) {
                            if (!isset($tasks_join_fields_data[$task->id]['delete'])) {
                                $task->set_join_field($task_set, 'sorting', $tasks_sorting[$task->id] + 1);
                                $task->set_join_field($task_set, 'points_total', floatval($tasks_join_fields_data[$task->id]['points_total']));
                            } else {
                                $task_set->delete($task);
                            }
                        }
                    }
                }
                
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
    
    public function open() {
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : 0;
        if ($task_set_id !== 0) {
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                $task_set->set_as_open();
            }
        }
        $this->_initialize_open_task_set();
        $this->parser->parse('partials/backend_general/open_task_set.tpl');
    }
    
    public function comments($task_set_id) {
        $this->_select_teacher_menu_pagetag('task_sets');
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        if ($task_set->exists()) {
            if ((bool)$task_set->comments_enabled) {
                $this->_add_scrollTo();
                $this->parser->add_js_file('admin_task_sets/comments_list.js');
                $this->parser->add_css_file('admin_task_sets.css');
                $this->parser->parse('backend/task_sets/comments.tpl', array('task_set' => $task_set));
            } else {
                $this->messages->add_message('lang:admin_task_sets_comments_error_comments_disabled', Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('admin_task_sets'));
            }
        } else {
            $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_task_sets'));
        }
    }
    
    public function all_comments($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $comments = array();
        if ($task_set->exists() && (bool)$task_set->comments_enabled) {
            $comments = Comment::get_comments_for_task_set($task_set);
        }
        $this->parser->parse('backend/task_sets/all_comments.tpl', array('task_set' => $task_set, 'comments' => $comments));
    }
    
    public function new_comment_form($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $this->parser->parse('backend/task_sets/new_comment_form.tpl', array('task_set' => $task_set));
    }
    
    public function my_comments_settings($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        if ($teacher->exists() && $task_set->exists()) {
            $this->parser->assign('subscribed', $teacher->is_related_to('comment_subscription', $task_set->id));
        }
        $this->parser->parse('backend/task_sets/my_comments_settings.tpl', array('task_set' => $task_set, 'teacher' => $teacher));
    }
    
    public function comments_unsubscribe($task_set_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        if ($teacher->exists() && $task_set->exists() && $teacher->is_related_to('comment_subscription', $task_set->id)) {
            $teacher->delete_comment_subscription($task_set);
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_task_sets_comments_my_settings_unsubscribe_success');
                $output->result = TRUE;
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_sets_comments_my_settings_unsubscribe_error');
                $output->result = FALSE;
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_my_settings_unsubscribe_error');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function comments_subscribe($task_set_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        if ($teacher->exists() && $task_set->exists() && $teacher->save(array('comment_subscription' => $task_set))) {
            $this->db->trans_commit();
            $output->message = $this->lang->line('admin_task_sets_comments_my_settings_subscribe_success');
            $output->result = TRUE;
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_my_settings_subscribe_error');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function delete_comment($task_set_id, $comment_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $comment = new Comment();
        $comment->get_by_id($comment_id);
        if ($comment->exists() && $task_set->exists() && $comment->is_related_to($task_set)) {
            $comment->delete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_task_sets_comments_success_delete_comment');
                $output->result = TRUE;
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_sets_comments_error_delete_comment');
                $output->result = FALSE;
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_error_delete_comment');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function approve_comment($task_set_id, $comment_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $comment = new Comment();
        $comment->include_related('student', '*', TRUE, TRUE);
        $comment->include_related('teacher', '*', TRUE, TRUE);
        $comment->get_by_id($comment_id);
        if ($comment->exists() && $task_set->exists() && $comment->is_related_to($task_set) && !is_null($comment->student->id) && is_null($comment->teacher->id) && !(bool)$comment->approved) {
            $comment->approved = 1;
            if ($comment->save()) {
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_task_sets_comments_success_approve_comment');
                $output->result = TRUE;
                
                $this->_load_student_langfile('tasks');
                $all_students = $task_set->comment_subscriber_student;
                $all_students->where('id !=', $comment->student->id);
                $all_students->get();
                $this->_send_multiple_emails($all_students, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_student.tpl', array('task_set' => $task_set, 'student' => $comment->student, 'comment' => $comment));
                $all_teachers = $task_set->comment_subscriber_teacher;
                $all_teachers->where('id !=', $this->usermanager->get_teacher_id());
                $all_teachers->get();
                $this->_send_multiple_emails($all_teachers, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_teacher.tpl', array('task_set' => $task_set, 'student' => $comment->student, 'comment' => $comment));
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_sets_comments_error_approve_comment');
                $output->result = FALSE;
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_error_approve_comment');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }

    public function post_comment($task_set_id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('comment[text]', 'lang:admin_task_sets_comments_form_field_text', 'required_no_html');
        if ($this->form_validation->run()) {
            $this->add_comment($task_set_id);
            redirect(create_internal_url('admin_task_sets/new_comment_form/' . $task_set_id));
        } else {
            $this->new_comment_form($task_set_id);
        }
    }
    
    public function reply_at_comment($task_set_id, $reply_at_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $comment = new Comment();
        $comment->include_related('teacher', '*', TRUE, TRUE);
        $comment->include_related('student', '*', TRUE, TRUE);
        $comment->get_by_id($reply_at_id);
        $this->parser->add_css_file('admin_task_sets.css');
        $this->parser->parse('backend/task_sets/reply_at_comment.tpl', array('task_set' => $task_set, 'comment' => $comment));
    }
    
    public function post_comment_reply($task_set_id, $reply_at_id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('comment[text]', 'lang:admin_task_sets_comments_form_field_text', 'required_no_html');
        if ($this->form_validation->run()) {
            $this->add_comment($task_set_id, $reply_at_id);
            redirect(create_internal_url('admin_task_sets/reply_at_comment/' . $task_set_id . '/' . $reply_at_id));
        } else {
            $this->reply_at_comment($task_set_id, $reply_at_id);
        }
    }

    private function add_comment($task_set_id, $reply_at_id = NULL) {
        $comment_data = $this->input->post('comment');
        if (isset($comment_data['task_set_id']) && $comment_data['task_set_id'] == $task_set_id) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                if ((bool)$task_set->comments_enabled) {
                    $save_array = array();
                    $save_array['task_set'] = $task_set;
                    if (isset($comment_data['reply_at_id']) && $comment_data['reply_at_id'] == $reply_at_id) {
                        $reply_at = new Comment();
                        $reply_at->get_by_id($reply_at_id);
                        if ($reply_at->exists()) {
                            if ($reply_at->task_set_id == $task_set_id) {
                                $save_array['reply_at'] = $reply_at;
                            } else {
                                $this->db->trans_rollback();
                                $this->messages->add_message('lang:admin_task_sets_comments_error_reply_at_comment_from_different_task_set', Messages::MESSAGE_TYPE_ERROR);
                                return;
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->messages->add_message('lang:admin_task_sets_comments_error_reply_at_comment_not_exists', Messages::MESSAGE_TYPE_ERROR);
                            return;
                        }
                    }
                    $teacher = new Teacher();
                    $teacher->get_by_id($this->usermanager->get_teacher_id());
                    $save_array['teacher'] = $teacher;
                    
                    $comment = new Comment();
                    $comment->text = strip_tags($comment_data['text'], '<a><strong><em><span>');
                    $comment->approved = 1;
                    if ($comment->save($save_array)) {
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:admin_task_sets_comments_save_successfully', Messages::MESSAGE_TYPE_SUCCESS);
                        
                        $all_students = $task_set->comment_subscriber_student;
                        $all_students->get();
                        $this->_send_multiple_emails($all_students, 'lang:admin_task_sets_comments_email_subject_new_post', 'file:emails/backend/comments/new_comment_student.tpl', array('task_set' => $task_set, 'teacher' => $teacher, 'comment' => $comment));
                        $all_teachers = $task_set->comment_subscriber_teacher;
                        $all_teachers->where('id !=', $this->usermanager->get_teacher_id());
                        $all_teachers->get();
                        $this->_send_multiple_emails($all_teachers, 'lang:admin_task_sets_comments_email_subject_new_post', 'file:emails/backend/comments/new_comment_teacher.tpl', array('task_set' => $task_set, 'teacher' => $teacher, 'comment' => $comment));
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:admin_task_sets_comments_error_save_failed', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_task_sets_comments_error_comments_disabled', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
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