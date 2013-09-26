<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Solutions controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Solutions extends LIST_Controller {
    
    const STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME = 'admin_solutions_filter_data_task_set_selection';
    const STORED_VALUATION_TABLES_FILTER_SESSION_NAME = 'admin_solutions_filter_data_valuation_tables';
    const STORED_SOLUTION_SELECTION_FILTER_SESSION_NAME = 'admin_solutions_filter_data_solution_list';
    
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
        $this->_select_teacher_menu_pagetag('solutions');
        $this->inject_stored_task_set_selection_filter();
        $this->inject_courses();
        $this->inject_all_task_set_types();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/index.tpl');
    }
    
    public function batch_valuation($task_set_id) {
        $this->_select_teacher_menu_pagetag('solutions');
        $task_set = new Task_set();
        $task_set->include_related('course', 'name', TRUE);
        $task_set->include_related('course/period', 'name', TRUE);
        $task_set->include_related('group', 'name', TRUE);
        $task_set->get_by_id($task_set_id);
        $this->parser->add_js_file('admin_solutions/batch_valuation_list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->_add_prettify();
        $this->parser->parse('backend/solutions/batch_valuation.tpl', array('task_set' => $task_set));
    }
    
    public function batch_valuation_list($task_set_id) {
        $this->inject_batch_valuation($task_set_id);
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $this->parser->parse('backend/solutions/batch_valuation_list.tpl', array('task_set' => $task_set));
    }
    
    public function batch_save_solutions($task_set_id) {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $data = $this->input->post('batch_valuation');
            $saved_count = 0;
            $save_status = TRUE;
            if (is_array($data) && count($data) > 0) { foreach ($data as $student_id => $solution_data) {
                $student = new Student();
                $student->get_by_id($student_id);
                $task_set_check = new Task_set();
                $task_set_check->where_related('course/participant/student', 'id', intval($student_id));
                $task_set_check->where_related('course/participant', 'allowed', 1);
                $task_set_check->group_start();
                    $task_set_check->or_where('group_id', NULL);
                    $task_set_check->or_where('`course_participants`.`group_id` = `task_sets`.`group_id`');
                    $task_set_check->or_where_related('solution', 'student_id', $student->id);
                $task_set_check->group_end();
                $task_set_check->get_by_id($task_set_id);
                if ($student->exists() && $task_set_check->exists() && array_key_exists('points', $solution_data) && is_numeric($solution_data['points'])) {
                    $solution = new Solution();
                    $solution->where_related_student('id', $student->id);
                    $solution->where_related_task_set('id', $task_set->id);
                    $solution->get();
                    if ($solution->points != floatval($solution_data['points']) || $solution->not_considered != intval(@$solution_data['not_considered'])) {
                        $solution->teacher_id = $this->usermanager->get_teacher_id();
                        $solution->points = floatval($solution_data['points']);
                        $solution->revalidate = 0;
                        $solution->not_considered = intval(@$solution_data['not_considered']);
                        $save_status = $save_status & $solution->save(array($task_set, $student));
                        $saved_count++;
                    }
                }
            }}
            if ($this->db->trans_status() && $save_status && $saved_count > 0) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_solutions_batch_valuation_success_message_save_ok', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_solutions_batch_valuation_error_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:admin_solutions_batch_valuation_error_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
        }
        redirect(create_internal_url('admin_solutions/batch_valuation_list/' . $task_set_id));
    }

    public function remove_points($task_set_id = NULL) {
        $this->output->set_content_type('application/json');
        $result = new stdClass();
        $result->result = FALSE;
        $result->message = '';
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('points', 'lang:admin_solutions_remove_points_form_field_points', 'required|numeric|greater_than[0]');
        if ($this->form_validation->run()) {
            $points_to_remove = floatval($this->input->post('points'));
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->select('*');
            $task_set->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` ctst WHERE `ctst`.`course_id` = `${parent}`.`course_id` AND `ctst`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            $task_set->include_related('course', '*', TRUE, TRUE);
            $task_set->include_related('course/period', 'name');
            $task_set->include_related('group', '*', TRUE, TRUE);
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                if ($task_set->join_upload_solution == 1) {
                    if (!is_null($task_set->upload_end_time)) {
                        $timestamp_end = strtotime($task_set->upload_end_time);
                        if(time() > $timestamp_end) {
                            $participants = new Participant();
                            $participants->select('*');
                            $participants->select_subquery('(SELECT `solutions`.`id` FROM `solutions` WHERE `solutions`.`task_set_id` = ' . $task_set->id . ' AND `solutions`.`student_id` = `${parent}`.`student_id`)', 'solution_id');
                            $participants->where_related_course($task_set->course);
                            if ($task_set->group->exists() && !is_null($task_set->group->id)) {
                                $participants->where_related_group($task_set->group);
                            }
                            $participants->where('allowed', 1);
                            $participants->get_iterated();
                            $notify_students = array(0);
                            foreach ($participants as $participant) {
                                if (is_null($participant->solution_id) && !is_null($participant->student_id)) {
                                    $solution = new Solution();
                                    $solution->task_set_id = $task_set->id;
                                    $solution->student_id = $participant->student_id;
                                    $solution->teacher_id = $this->usermanager->get_teacher_id();
                                    $solution->points = - $points_to_remove;
                                    $solution->revalidate = 0;
                                    if ($solution->save()) {
                                        $notify_students[] = $participant->student_id;
                                    }
                                }
                            }
                            if ($this->db->trans_status()) {
                                $this->db->trans_commit();
                                $result->result = TRUE;
                                $result->message = sprintf($this->lang->line('admin_solutions_remove_points_success'), count($notify_students) - 1);

                                $students = new Student();
                                $students->where_in('id', $notify_students);
                                $students->get();
                                $result->mail_sent = $this->_send_multiple_emails($students, 'lang:admin_solutions_remove_points_notification_subject', 'file:emails/backend/solutions/remove_points_notify.tpl', array('task_set' => $task_set, 'points_to_remove' => $points_to_remove));
                            } else {
                                $this->db->trans_rollback();
                                $result->message = $this->lang->line('admin_solutions_remove_points_error_unknown');
                            }
                        } else {
                            $this->db->trans_rollback();
                            $result->message = $this->lang->line('admin_solutions_remove_points_error_task_set_upload_limit_not_reached');
                        }
                    } else {
                        $this->db->trans_rollback();
                        $result->message = $this->lang->line('admin_solutions_remove_points_error_task_set_upload_not_limited');
                    }
                } else {
                    $this->db->trans_rollback();
                    $result->message = $this->lang->line('admin_solutions_remove_points_error_task_set_solution_uploading_disabled');
                }
            } else {
                $this->db->trans_rollback();
                $result->message = $this->lang->line('admin_solutions_list_task_set_not_found');
            }
        } else {
            $result->message = $this->form_validation->error_string();
        }
        $this->output->set_output(json_encode($result));
    }
    
    public function solutions_list($task_set_id = NULL) {
        $this->_select_teacher_menu_pagetag('solutions');
        $task_set = new Task_set();
        $task_set->select('`task_sets`.*');
        $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'task_set_total_points');
        $task_set->include_related('course', 'name');
        $task_set->include_related('course/period', 'name');
        $task_set->include_related('group', 'name');
        $task_set->get_by_id($task_set_id);
        
        $this->inject_students($task_set_id);
        $this->inject_task_set_possible_groups($task_set_id);
        $this->inject_stored_solution_list_filter($task_set_id);
        $this->parser->add_js_file('admin_solutions/solutions_list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/solutions_list.tpl', array('task_set' => $task_set));
    }
        
    public function create_solution($task_set_id) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('solution[student_id]', 'lang:admin_solutions_list_form_field_student', 'required|exists_in_table[students.id.1.1]');
        $this->form_validation->set_rules('solution[points]', 'lang:admin_solutions_list_form_field_points', 'required|floatpoint');
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $solution_data = $this->input->post('solution');
            $task_set = new Task_set();
            $task_set->where_related('course/participant/student', 'id', intval($solution_data['student_id']));
            $task_set->where_related('course/participant', 'allowed', 1);
            $task_set->group_start();
                $task_set->or_where('group_id', NULL);
                $task_set->or_where('`course_participants`.`group_id` = `task_sets`.`group_id`');
            $task_set->group_end();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                $teacher = new Teacher();
                $teacher->get_by_id($this->usermanager->get_teacher_id());
                
                $solution = new Solution();
                $solution->from_array($solution_data, array('student_id', 'points', 'comment'));
                $solution->revalidate = 0;
                $solution->save(array($teacher, $task_set));
                
                $solution->where('task_set_id', $task_set_id);
                $solution->where('student_id', intval($solution_data['student_id']));
                if ($solution->count() == 1) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_solutions_list_new_solution_created', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_solutions_list_new_solution_error_solution_exists', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_solutions_list_new_solution_error_student_not_in_course_or_group', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_solutions/new_solution_form/' . intval($task_set_id)));
        } else {
            $this->new_solution_form($task_set_id);
        }
    }
    
    public function new_solution_form($task_set_id) {
        $task_set = new Task_set();
        $task_set->select('`task_sets`.*');
        $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'task_set_total_points');
        $task_set->get_by_id($task_set_id);
        $this->inject_students($task_set_id);
        $this->parser->parse('backend/solutions/new_solution_form.tpl', array('task_set' => $task_set));
    }
    
    public function display_tasks_list($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $tasks = $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get();
            $this->lang->init_overlays('tasks', $tasks, array('name', 'text'));
            $this->parser->assign('tasks', $tasks);
            $this->parser->assign('task_set', $task_set);
        }
        $this->parser->parse('backend/solutions/display_tasks_list.tpl');
    }

    public function valuation($task_set_id, $solution_id) {
        $solution = new Solution();
        $solution->select('`solutions`.*');
        $solution->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `task_sets`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'task_set_total_points');
        $solution->include_related('task_set', '*', TRUE, TRUE);
        $solution->include_related('task_set/course', 'name');
        $solution->include_related('task_set/course/period', 'name');
        $solution->include_related('task_set/group', 'name');
        $solution->include_related('student', array('fullname', 'email'));
        $solution->include_related('teacher', array('fullname', 'email'));
        $solution->where('student_id IS NOT NULL');
        $solution->where('task_set_id', $task_set_id);
        $solution->get_by_id($solution_id);
        
        $this->load->helper('tests');
        $test_types_subtypes = get_all_supported_test_types_and_subtypes();
        
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/valuation.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->_add_prettify();
        $this->parser->parse('backend/solutions/valuation.tpl', array(
            'solution' => $solution,
            'test_types' => $test_types_subtypes['types'],
            'test_subtypes' => $test_types_subtypes['subtypes'],
            'add_url' => $this->uri->assoc_to_uri($this->uri->ruri_to_assoc(5)),
        ));
    }
    
    public function get_next_solution($task_set_id, $solution_id) {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $output = new stdClass();
        $output->have_next = FALSE;
        $output->next_id = NULL;
        $output->error_message = '';
        $current_solution = new Solution();
        $current_solution->where('task_set_id', $task_set_id);
        $current_solution->get_by_id($solution_id);
        if ($current_solution->revalidate == 0) {
            $additional = $this->uri->ruri_to_assoc(5);
            $solution = new Solution();
            $solution->where('task_set_id', $task_set_id);
            $solution->where('revalidate', 1);
            if (isset($additional['group_id'])) {
                $solution->where_related('student/participant/group', 'id', (int)$additional['group_id']);
            }
            $solution->limit(1);
            $solution->get();
            if ($solution->exists()) {
                $output->have_next = TRUE;
                $output->next_id = $solution->id;
            } else {
                $output->error_message = $this->lang->line('admin_solutions_valuation_next_solution_message_no_more_solution_to_valuate');
            }
        } else {
            $output->error_message = $this->lang->line('admin_solutions_valuation_next_solution_message_this_solution_is_not_valuated');
        }
        $this->db->trans_rollback();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function update_valuation($task_set_id, $solution_id) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('solution[points]', 'lang:admin_solutions_valuation_form_field_points', 'required|floatpoint');
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $solution = new Solution();
            $solution->where('task_set_id', intval($task_set_id));
            $solution->get_by_id($solution_id);
            if ($solution->exists()) {
                $solution_data = $this->input->post('solution');
                if ($solution->comment != $solution_data['comment'] || $solution->points != floatval($solution_data['points']) || $solution->not_considered != intval($solution_data['not_considered'])) {
                    $solution->teacher_id = $this->usermanager->get_teacher_id();
                }
                $solution->from_array($solution_data, array('points', 'comment', 'not_considered'));
                $solution->revalidate = 0;
                if ($solution->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_solutions_valuation_solution_saved', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_solutions_valuation_solution_not_saved', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_solutions_valuation_solution_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_solutions/valuation/' . $task_set_id . '/' . $solution_id . '/' . $this->uri->assoc_to_uri($this->uri->ruri_to_assoc(5))));
        } else {
            $this->valuation($task_set_id, $solution_id);
        }
    }
    
    public function get_student_file_content($task_set_id, $solution_id, $solution_file) {
        $task_set = new Task_set();
        $task_set->where_related('solution', 'id', $solution_id);
        $task_set->include_related('solution/student', 'id');
        $task_set->get_by_id($task_set_id);
        $files = array();
        if ($task_set->exists()) {
            $file_name = decode_from_url($solution_file);
            $files = $task_set->get_student_file_content($file_name);
        }
        $this->parser->parse('backend/solutions/list_of_file_content.tpl', array('files' => $files));
    }
    
    public function show_file_content($task_set_id, $solution_id, $solution_file, $zip_index) {
        $this->output->set_content_type('text/plain');
        $task_set = new Task_set();
        $task_set->where_related('solution', 'id', $solution_id);
        $task_set->include_related('solution/student', 'id');
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $file_name = decode_from_url($solution_file);
            $output = $task_set->extract_student_file_by_index($file_name, $zip_index);
            if ($output !== FALSE) {
                $this->output->set_output('<pre class="prettyprint linenums lang-' . $output['extension'] . '">' . htmlspecialchars($output['content']) . '</pre>');
            } else {
                $this->output->set_output($this->lang->line('admin_solutions_valuation_file_content_error_cant_read_file'));
            }
        } else {
            $this->output->set_output($this->lang->line('admin_solutions_valuation_file_content_error_task_set_not_found'));
        }
    }

    public function get_task_set_list() {
        $filter = $this->input->post('filter');
        $this->store_task_set_selection_filter($filter);
        $task_sets = new Task_set();
        $task_sets->select('`task_sets`.*, `course_course_task_set_type_rel`.`upload_solution` AS `join_upload_solution`');
        $task_sets->include_related_count('solution');
        $task_sets->include_related_count('task');
        $task_sets->include_related('course', array('name', 'default_points_to_remove'));
        $task_sets->include_related('course/period', 'name');
        $task_sets->include_related('group', 'name');
        $task_sets->include_related('task_set_type', 'name');
        $task_sets->include_related('course/task_set_type');
        $task_sets->where('(`course_task_set_types`.`id` = `task_sets`.`task_set_type_id`)');
        if (isset($filter['course']) && intval($filter['course']) > 0) {
            $task_sets->where_related_course('id', intval($filter['course']));
        }
        if (isset($filter['group']) && $filter['group'] == 'NULL') {
            $task_sets->where_related_group('id', NULL);
        } else if (isset($filter['group']) && intval($filter['group']) > 0) {
            $task_sets->where_related_group('id', intval($filter['group']));
        }
        if (isset($filter['task_set_type']) && intval($filter['task_set_type']) > 0) {
            $task_sets->where_related_task_set_type('id', intval($filter['task_set_type']));
        }
        $order_by_direction = $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] == 'course') {
            $task_sets->order_by_related('course/period', 'sorting', $order_by_direction);
            $task_sets->order_by_related_with_constant('course', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'task_set_name') {
            $task_sets->order_by_with_overlay('name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'course_group') {
            $task_sets->order_by_related_with_constant('group', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'task_set_type') {
            $task_sets->order_by_related_with_constant('task_set_type', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'solution_count') {
            $task_sets->order_by('solution_count', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'task_count') {
            $task_sets->order_by('task_count', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'upload_end_time') {
            $task_sets->order_by('upload_end_time', $order_by_direction);
        }
        $task_sets->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), array('name'));
        $this->parser->parse('backend/solutions/task_set_list.tpl', array('task_sets' => $task_sets));
    }
    
    public function get_solutions_list_for_task_set($task_set_id) {
        $filter = $this->input->post('filter');
        $this->store_solution_list_filter($filter, $task_set_id);
        
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $solutions = new Solution();
        if ($task_set->exists()) {
            $solutions->where_related($task_set);
            $solutions->include_related('student');
            $solutions->include_related('teacher');
            $solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
            $solutions->include_related('student/participant/group');
            if (isset($filter['group']) && (int)$filter['group'] > 0) {
                $solutions->where_related('student/participant/group', 'id', (int)$filter['group']);
            }
            $solutions->where_related('student/participant/course', 'id', $task_set->course_id);
            $solutions->get_iterated();
        }
        
        $this->parser->parse('backend/solutions/solutions_list_table_content.tpl', array('task_set' => $task_set, 'solutions' => $solutions));
    }
    
    public function get_groups_from_course($course_id, $selected_id = NULL) {
        $groups = new Group();
        $groups->select('id, name');
        $groups->where_related_course('id', $course_id);
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        $options = array(
            '' => '',
            'NULL' => $this->lang->line('admin_solutions_group_no_group'),
        );
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $this->parser->parse('backend/solutions/groups_from_course.tpl', array('groups' => $options, 'selected' => $selected_id));
    }
    
    public function valuation_tables() {
        $this->_select_teacher_menu_pagetag('valuation_tables');
        $this->inject_stored_valuation_tables_filter();
        $this->inject_courses();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/valuation_tables.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/valuation_tables.tpl');
    }
    
    public function get_valuation_table() {
        $filter = $this->input->post('filter');
        $this->store_valuation_tables_filter($filter);
        $this->inject_stored_valuation_tables_filter();
        
        $course = new Course();
        $course->include_related('period', 'name');
        $course->get_by_id(intval(@$filter['course']));
        
        $group = new Group();
        $group->get_by_id(@$filter['group']);
        
        if ($course->exists()) {
            $task_set_types = new Task_set_type();
            $task_set_types->where_related_course($course);
            $task_set_types->order_by_with_constant('name', 'asc');
            $task_set_types->get_iterated();
                        
            $students = new Student();
            $students->include_related('participant/group', 'id');
            $students->where_related('participant/course', 'id', $course->id);
            if ($group->exists()) {
                $students->where_related('participant/group', 'id', $group->id);
            }
            $students->where_related('participant', 'allowed', 1);
            if ($filter['order_by_field'] == 'students') {
                $students->order_by('fullname', $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc');
            }
            $students->get_iterated();
            
            $student_ids = array(0);
            
            $points_table = array();
            
            foreach($students as $student) { 
                $student_ids[] = $student->id;
                $points_table[$student->id]['student']['fullname'] = $student->fullname;
                $points_table[$student->id]['student']['email'] = $student->email;
                $points_table[$student->id]['student']['group'] = $student->participant_group_id;
            }
            
            $task_sets = new Task_set();
            $task_sets->select('*');
            $task_sets->select_subquery('(SELECT SUM(`points_total`) FROM `task_task_set_rel` WHERE `task_task_set_rel`.`task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'counted_points_sum');
            $task_sets->include_related('group', 'name');
            $task_sets->where_related('course', 'id', $course->id);
            if ($group->exists()) {
                $task_sets->group_start();
                    $task_sets->or_where_related('group', 'id', $group->id);
                    $task_sets->or_where_related('group', 'id', NULL);
                $task_sets->group_end();
            }
            $task_sets->where('published', 1);
            $task_sets->order_by_related_with_constant('task_set_type', 'name', 'asc');
            $task_sets->order_by_with_overlay('name', 'asc');
            $task_sets->get_iterated();
            
            $task_set_ids = array(0);
            $task_set_types_points_max = array();
            $header = array();
            
            foreach ($task_set_types as $task_set_type) {
                $header[$task_set_type->id] = array(
                    'name' => $task_set_type->name,
                    'task_sets' => array(),
                );
            }
            
            foreach($task_sets as $task_set) { 
                $task_set_ids[] = $task_set->id;
                $points = floatval(!is_null($task_set->points_override) ? $task_set->points_override : $task_set->counted_points_sum);
                $task_set_types_points_max[$task_set->task_set_type_id] = isset($task_set_types_points_max[$task_set->task_set_type_id]) ? $task_set_types_points_max[$task_set->task_set_type_id] + $points : $points;
                $header[$task_set->task_set_type_id]['task_sets'][$task_set->id] = array(
                    'name' => $task_set->name,
                    'group_name' => $task_set->group_name,
                    'group_id' => $task_set->group_id,
                    'points' => $points,
                );
            }
            $total_points = array_sum($task_set_types_points_max);
            
            $solutions = new Solution();
            $solutions->include_related('task_set/task_set_type', 'id');
            $solutions->where_in_related('task_set', 'id', $task_set_ids);
            $solutions->where_in_related('student', 'id', $student_ids);
            $solutions->order_by_related('student', 'fullname', 'asc');
            $solutions->order_by_related('student', 'email', 'asc');
            $solutions->order_by_related_with_constant('task_set/task_set_type', 'name', 'asc');
            $solutions->order_by_related_with_overlay('task_set', 'name', 'asc');
            $solutions->get_iterated();
            
            foreach ($solutions as $solution) {
                $points_table[$solution->student_id]['points'][$solution->task_set_task_set_type_id][$solution->task_set_id] = array(
                    'points' => $solution->points,
                    'revalidate' => $solution->revalidate,
                    'not_considered' => $solution->not_considered,
                );
                if (!(bool)$solution->not_considered) {
                    $points_table[$solution->student_id]['points'][$solution->task_set_task_set_type_id]['total'] = isset($points_table[$solution->student_id]['points'][$solution->task_set_task_set_type_id]['total']) ? $points_table[$solution->student_id]['points'][$solution->task_set_task_set_type_id]['total'] + floatval($solution->points) : floatval($solution->points);
                    $points_table[$solution->student_id]['points']['total'] = isset($points_table[$solution->student_id]['points']['total']) ? $points_table[$solution->student_id]['points']['total'] + floatval($solution->points) : floatval($solution->points);
                }
            }
            
            if (count($points_table) > 0) {
                if (substr($filter['order_by_field'], 0, 14) == 'task_set_type_') {
                    $task_set_type_id = intval(substr($filter['order_by_field'], 14));
                    $order_array = array();
                    foreach ($points_table as $key => $line) {
                        $order_array[$key] = @$line['points'][$task_set_type_id]['total'];
                    }
                    $direction = $filter['order_by_direction'] == 'desc' ? SORT_DESC : SORT_ASC;
                    array_multisort($order_array, $direction, SORT_NUMERIC, $points_table);
                } elseif ($filter['order_by_field'] == 'total') {
                    $order_array = array();
                    foreach ($points_table as $key => $line) {
                        $order_array[$key] = @$line['points']['total'];
                    }
                    $direction = $filter['order_by_direction'] == 'desc' ? SORT_DESC : SORT_ASC;
                    array_multisort($order_array, $direction, SORT_NUMERIC, $points_table);
                } elseif (substr($filter['order_by_field'], 0, 9) == 'task_set_') {
                    list($task_set_type_id, $task_set_id) = explode('_', substr($filter['order_by_field'], 9));
                    $order_array = array();
                    $category_order_array = array();
                    foreach ($points_table as $key => $line) {
                        if (isset($line['points'][$task_set_type_id]) && array_key_exists($task_set_id, $line['points'][$task_set_type_id])) {
                            if (is_null(@$line['points'][$task_set_type_id][$task_set_id]['points'])) {
                                $category_order_array[$key] = 2;
                            } else {
                                if (@$line['points'][$task_set_type_id][$task_set_id]['not_considered']) {
                                    $category_order_array[$key] = 3;
                                } else {
                                    $category_order_array[$key] = 4;
                                }
                            }
                        } else {
                            if (!is_null(@$header[$task_set_type_id]['task_sets'][$task_set_id]['group_id']) && $line['student']['group'] != @$header[$task_set_type_id]['task_sets'][$task_set_id]['group_id']) {
                                $category_order_array[$key] = 1;
                            } else {
                                $category_order_array[$key] = 0;
                            }
                        }
                        $order_array[$key] = @$line['points'][$task_set_type_id][$task_set_id]['points'];
                    }
                    $direction = $filter['order_by_direction'] == 'desc' ? SORT_DESC : SORT_ASC;
                    array_multisort($category_order_array, $direction, SORT_NUMERIC, $order_array, $direction, SORT_NUMERIC, $points_table);
                }
            }
            
            $this->parser->assign('task_set_types_points_max', $task_set_types_points_max);
            $this->parser->assign('total_points', $total_points);
            $this->parser->assign('header', $header);
            $this->parser->assign('points_table', $points_table);
        }
        
        $this->parser->parse('backend/solutions/valuation_table_content.tpl', array('course' => $course, 'group' => $group));
    }

    private function store_task_set_selection_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME, 'course');
            $this->filter->set_filter_delete_on_course_change(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME, array('group'));
        }
    }
    
    private function inject_stored_task_set_selection_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course');
        $this->parser->assign('filter', $filter);
    }
    
    private function store_valuation_tables_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $new_filter['simple'] = isset($filter['simple']) && $filter['simple'] == 1 ? 1 : 0;
            $this->filter->store_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME, 'course');
            $this->filter->set_filter_delete_on_course_change(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME, array('group'));
        }
    }
    
    private function inject_stored_valuation_tables_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course');
        $this->parser->assign('filter', $filter);
    }

    private function store_solution_list_filter($filter, $task_set_id) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_SOLUTION_SELECTION_FILTER_SESSION_NAME . '_' . $task_set_id);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME . '_' . $task_set_id, $new_filter);
        }
    }
    
    private function inject_stored_solution_list_filter($task_set_id) {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME . '_' . $task_set_id);
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
    
    private function inject_students($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $data = array('' => '');
        if ($task_set->exists()) {
            $students = new Student();
            $students->where_related('participant', 'allowed', 1);
            $students->where_related('participant/course/task_set', 'id', intval($task_set_id));
            if (!is_null($task_set->group_id)) {
                $students->where_related('participant/group', 'id', intval($task_set->group_id));
            }
            $students->where('students.id = `students`.`id` AND NOT EXISTS (SELECT * FROM `solutions` WHERE `solutions`.`student_id` = `students`.`id` AND `solutions`.`task_set_id` = ' . intval($task_set_id) . ')');
            $students->group_by('id');
            $students->order_by('fullname', 'asc');
            $students->order_by('email', 'asc');
            $students->get_iterated();

            foreach ($students as $student) {
                $data[$student->id] = $student->fullname . ' (' . $student->email . ')';
            }
        }
        $this->parser->assign('students', $data);
    }
    
    private function inject_batch_valuation($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $solutions = new Solution();
        $solutions->where_related($task_set);
        $solutions->group_by('student_id');
        $solutions->get_iterated();
        
        $additional_student_ids = array( 0 );
        foreach ($solutions as $solution) { $additional_student_ids[] = $solution->student_id; }
        
        $data = array();
        if ($task_set->exists()) {
            $students = new Student();
            $students->where_related('participant', 'allowed', 1);
            $students->where_related('participant/course/task_set', 'id', intval($task_set_id));
            if (!is_null($task_set->group_id)) {
                $students->where_related('participant/group', 'id', intval($task_set->group_id));
            }
            $students->include_related('solution');
            $students->add_join_condition('`solutions`.`task_set_id` = ?', array($task_set->id));
            $students->or_where_in('id', $additional_student_ids);
            $students->order_by_as_fullname('fullname', 'asc');
            $students->order_by('email', 'asc');
            $students->get_iterated();

            foreach ($students as $student) {
                $data[$student->id] = clone $student;
            }
        }
        $this->parser->assign('batch_valuation_students', $data);
    }
    
    private function inject_all_task_set_types() {
        $task_set_types = new Task_set_type();
        $task_set_types->order_by_with_constant('name', 'asc');
        $task_set_types->get_iterated();
        
        $data = array('' => '');
        foreach($task_set_types as $task_set_type) {
            $data[$task_set_type->id] = $task_set_type->name;
        }
        
        $this->parser->assign('task_set_types', $data);
    }
    
    public function inject_task_set_possible_groups($task_set_id) {
        $task_sets = new Task_set();
        $task_sets->where('group_id', NULL);
        $task_sets->include_related('course/group');
        $task_sets->order_by_related_with_constant('course/group', 'name', 'asc');
        $task_sets->where('id', (int)$task_set_id);
        $task_sets->get_iterated();
        
        $data = array('' => '');
        foreach ($task_sets as $task_set) {
            $data[$task_set->course_group_id] = $task_set->course_group_name;
        }
        
        $this->parser->assign('possible_groups', $data);
    }
}