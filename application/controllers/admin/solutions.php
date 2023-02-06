<?php
/**
 *  Probably should not be here, but I don't know where to properly load the classes
 */
include_once "application/services/Formula/NodeFactory.php";

use Application\Services\DependencyInjection\ContainerFactory;
use Application\Services\Formula\FormulaService;

/**
 * Solutions controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Solutions extends LIST_Controller
{
    
    public const STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME = 'admin_solutions_filter_data_task_set_selection';
    public const STORED_VALUATION_TABLES_FILTER_SESSION_NAME = 'admin_solutions_filter_data_valuation_tables';
    public const STORED_SOLUTION_SELECTION_FILTER_SESSION_NAME = 'admin_solutions_filter_data_solution_list';
    public const STORED_BATCH_VALUATION_FILTER_SESSION_NAME = 'admin_solutions_filter_data_batch_valuation';
    
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
        $this->_select_teacher_menu_pagetag('solutions');
        $this->load->helper('task_sets');
        $this->inject_stored_task_set_selection_filter();
        $this->inject_courses();
        $this->inject_all_task_set_types();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/index.tpl');
    }
    
    public function batch_valuation($task_set_id): void
    {
        $this->_add_mathjax();
        $this->_select_teacher_menu_pagetag('solutions');
        $task_set = new Task_set();
        $task_set->include_related('course', 'name', true);
        $task_set->include_related('course/period', 'name', true);
        $task_set->include_related('group', 'name', true);
        $task_set->get_by_id($task_set_id);
        $this->inject_task_set_possible_groups($task_set_id);
        $this->inject_batch_valuation_filter((int)$task_set_id);
        $this->_add_highcharts();
        $this->parser->add_js_file('admin_solutions/histogram.js');
        $this->parser->add_js_file('admin_solutions/batch_valuation_list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->_add_prettify();
        $this->parser->parse('backend/solutions/batch_valuation.tpl', ['task_set' => $task_set]);
    }
    
    public function batch_valuation_list($task_set_id): void
    {
        $this->inject_batch_valuation($task_set_id);
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $this->parser->parse('backend/solutions/batch_valuation_list.tpl', ['task_set' => $task_set]);
    }
    
    public function batch_save_solutions($task_set_id): void
    {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $data = $this->input->post('batch_valuation');
            $saved_count = 0;
            $save_status = true;
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $student_id => $solution_data) {
                    $student = new Student();
                    $student->get_by_id($student_id);
                    $task_set_check = new Task_set();
                    $task_set_check->where_related('course/participant/student', 'id', (int)$student_id);
                    $task_set_check->where_related('course/participant', 'allowed', 1);
                    $task_set_check->group_start();
                    $task_set_check->or_group_start();
                    $task_set_check->group_start();
                    $task_set_check->or_where('group_id', null);
                    $task_set_check->or_where('`course_participants`.`group_id` = `task_sets`.`group_id`');
                    $task_set_check->group_end();
                    $task_set_check->where_subquery(
                        0,
                        '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                        . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
                    );
                    $task_set_check->group_end();
                    $task_set_check->or_where_related(
                        'task_set_permission',
                        '`group_id` = `course_participants`.`group_id`'
                    );
                    $task_set_check->or_where_related('solution', 'student_id', $student->id);
                    $task_set_check->group_end();
                    $task_set_check->get_by_id($task_set_id);
                    if ($student->exists() && $task_set_check->exists()
                        && array_key_exists('points', $solution_data) && is_numeric($solution_data['points'])
                    ) {
                        $solution = new Solution();
                        $solution->where_related_student('id', $student->id);
                        $solution->where_related_task_set('id', $task_set->id);
                        $solution->get();
                        if (is_null($solution->points) || (float)$solution->points !== (float)$solution_data['points']
                            || $solution->not_considered != (int)@$solution_data['not_considered']
                        ) {
                            $solution->teacher_id = $this->usermanager->get_teacher_id();
                            $solution->points = (float)$solution_data['points'];
                            $solution->revalidate = 0;
                            $solution->not_considered = (int)@$solution_data['not_considered'];
                            $save_status = $save_status & $solution->save([$task_set, $student]);
                            $saved_count++;
                        }
                    }
                }
            }
            if ($this->db->trans_status() && $save_status && $saved_count > 0) {
                $this->db->trans_commit();
                $this->messages->add_message(
                    'lang:admin_solutions_batch_valuation_success_message_save_ok',
                    Messages::MESSAGE_TYPE_SUCCESS
                );
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_solutions_batch_valuation_error_message_save_failed',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message(
                'lang:admin_solutions_batch_valuation_error_message_save_failed',
                Messages::MESSAGE_TYPE_ERROR
            );
        }
        redirect(create_internal_url('admin_solutions/batch_valuation_list/' . $task_set_id));
    }
    
    /**
     * @throws Exception
     */
    public function remove_points($task_set_id = null): void
    {
        $this->output->set_content_type('application/json');
        $result = new stdClass();
        $result->result = false;
        $result->message = '';
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules(
            'points',
            'lang:admin_solutions_remove_points_form_field_points',
            'required|numeric|greater_than[0]'
        );
        if ($this->form_validation->run()) {
            $points_to_remove = (float)$this->input->post('points');
            $task_set = new Task_set();
            $task_set->select('*');
            $task_set->select_subquery(
                '(SELECT `upload_solution` FROM `course_task_set_type_rel` ctst '
                . 'WHERE `ctst`.`course_id` = `${parent}`.`course_id` '
                . 'AND `ctst`.`task_set_type_id` = `${parent}`.`task_set_type_id`)',
                'join_upload_solution'
            );
            $task_set->include_related_count('task_set_permission');
            $task_set->add_join_condition('`task_set_permissions`.`enabled` = 1');
            $task_set->include_related('course', '*', true, true);
            $task_set->include_related('course/period', 'name');
            $task_set->include_related('group', '*', true, true);
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                if ($task_set->join_upload_solution == 1) {
                    $notify_students = [];
                    $students = null;
                    $error_code = 0;
                    if ($task_set->task_set_permission_count === 0) {
                        if ($this->remove_points_iteration(
                            $task_set,
                            $points_to_remove,
                            $task_set->id,
                            $task_set->course->id,
                            $task_set->group->id,
                            $error_code,
                            $students
                        )) {
                            $notify_students[] = $students;
                        }
                    } else {
                        $task_set_permissions = $task_set->task_set_permissions;
                        $task_set_permissions->where('enabled', 1);
                        $task_set_permissions->include_related('group', '*', true, true);
                        $task_set_permissions->get_iterated();
                        foreach ($task_set_permissions as $task_set_permission) {
                            if ($this->remove_points_iteration(
                                $task_set_permission,
                                $points_to_remove,
                                $task_set->id,
                                $task_set->course->id,
                                $task_set_permission->group_id,
                                $error_code,
                                $students
                            )) {
                                $notify_students[] = $students;
                                $error_code = 0;
                            }
                        }
                    }
                    if ($error_code === 0 || count($notify_students) > 0) {
                        $student_count = 0;
                        foreach ($notify_students as $notify_student_group) {
                            $student_count += $notify_student_group->result_count();
                            $result->mail_sent = $this->_send_multiple_emails(
                                $notify_student_group,
                                'lang:admin_solutions_remove_points_notification_subject',
                                'file:emails/backend/solutions/remove_points_notify.tpl',
                                ['task_set' => $task_set, 'points_to_remove' => $points_to_remove]
                            );
                        }
                        $result->result = true;
                        $result->message = sprintf(
                            $this->lang->line('admin_solutions_remove_points_success'),
                            $student_count
                        );
                        $this->_action_success();
                    } else {
                        $result->message = $this->lang->line('admin_solutions_remove_points_error_some_problem');
                    }
                } else {
                    $this->db->trans_rollback();
                    $result->message = $this->lang->line(
                        'admin_solutions_remove_points_error_task_set_solution_uploading_disabled'
                    );
                }
            } else {
                $this->db->trans_rollback();
                $result->message = $this->lang->line(
                    'admin_solutions_list_task_set_not_found'
                );
            }
        } else {
            $result->message = $this->form_validation->error_string();
        }
        $this->output->set_output(json_encode($result));
    }
    
    private function remove_points_iteration(
        $task_set,
        $points_to_remove,
        $task_set_id,
        $task_set_course_id,
        $task_set_group_id,
        &$error_code = 0,
        &$students = null
    ): bool
    {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if (!is_null($task_set->upload_end_time)) {
            $timestamp_end = strtotime($task_set->upload_end_time);
            if (time() > $timestamp_end) {
                $participants = new Participant();
                $participants->select('*');
                $participants->select_subquery(
                    '(SELECT `solutions`.`id` FROM `solutions` '
                    . 'WHERE `solutions`.`task_set_id` = ' . $task_set_id
                    . ' AND `solutions`.`student_id` = `${parent}`.`student_id`)',
                    'solution_id'
                );
                $participants->where_related_course('id', $task_set_course_id);
                if ($task_set->group->exists() && !is_null($task_set_group_id)) {
                    $participants->where_related_group('id', $task_set_group_id);
                }
                $participants->where('allowed', 1);
                $participants->get_iterated();
                $notify_students = [0];
                foreach ($participants as $participant) {
                    if (is_null($participant->solution_id) && !is_null($participant->student_id)) {
                        $solution = new Solution();
                        $solution->task_set_id = $task_set_id;
                        $solution->student_id = $participant->student_id;
                        $solution->teacher_id = $this->usermanager->get_teacher_id();
                        $solution->points = -$points_to_remove;
                        $solution->revalidate = 0;
                        if ($solution->save()) {
                            $notify_students[] = $participant->student_id;
                        }
                    }
                }
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    
                    $students = new Student();
                    $students->where_in('id', $notify_students);
                    $students->get();
                    /*$result->mail_sent = $this->_send_multiple_emails(
                        $students,
                        'lang:admin_solutions_remove_points_notification_subject',
                        'file:emails/backend/solutions/remove_points_notify.tpl',
                        ['task_set' => $task_set, 'points_to_remove' => $points_to_remove]
                    );*/
                    return true;
                }
                
                $this->db->trans_rollback();
                //$result->message = $this->lang->line('admin_solutions_remove_points_error_unknown');
                $error_code = 1;
                return false;
            }
            
            $this->db->trans_rollback();
            /*$result->message = $this->lang->line(
                'admin_solutions_remove_points_error_task_set_upload_limit_not_reached'
            );*/
            $error_code = 2;
            return false;
        }
        
        $this->db->trans_rollback();
        //$result->message = $this->lang->line('admin_solutions_remove_points_error_task_set_upload_not_limited');
        $error_code = 3;
        return false;
    }
    
    public function solutions_list($task_set_id = null): void
    {
        $this->_select_teacher_menu_pagetag('solutions');
        $task_set = new Task_set();
        $task_set->select('`task_sets`.*');
        $task_set->select_subquery(
            '(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` '
            . 'WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)',
            'task_set_total_points'
        );
        $task_set->include_related_count('task_set_permission');
        $task_set->add_join_condition('`task_set_permissions`.`enabled` = 1');
        $task_set->include_related('course', 'name');
        $task_set->include_related('course/period', 'name');
        $task_set->include_related('group', 'name');
        
        $task_set->get_by_id($task_set_id);
        
        if ($task_set->exists() && $task_set->content_type === 'project') {
            $this->inject_task_set_authors($task_set->id);
        }
        
        $this->inject_students($task_set_id);
        $this->inject_task_set_possible_groups($task_set_id);
        $this->inject_stored_solution_list_filter($task_set_id);
        $this->parser->add_js_file('admin_solutions/histogram.js');
        $this->parser->add_js_file('admin_solutions/solutions_list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->_add_highcharts();
        $this->parser->parse('backend/solutions/solutions_list.tpl', ['task_set' => $task_set]);
    }
    
    
    public function create_solution($task_set_id): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'solution[student_id]',
            'lang:admin_solutions_list_form_field_student',
            'required|exists_in_table[students.id.1.1]'
        );
        $this->form_validation->set_rules(
            'solution[points]',
            'lang:admin_solutions_list_form_field_points',
            'floatpoint'
        );
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $solution_data = $this->input->post('solution');
            $task_set = new Task_set();
            $task_set->where_related('course/participant/student', 'id', (int)$solution_data['student_id']);
            $task_set->where_related('course/participant', 'allowed', 1);
            $task_set->group_start();
            $task_set->or_group_start();
            $task_set->group_start();
            $task_set->or_where('group_id', null);
            $task_set->or_where('`course_participants`.`group_id` = `task_sets`.`group_id`');
            $task_set->group_end();
            $task_set->where_subquery(
                0,
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
            );
            $task_set->group_end();
            $task_set->or_where_related(
                'task_set_permission',
                '`group_id` = `course_participants`.`group_id`'
            );
            $task_set->group_end();
            $task_set->get_by_id($task_set_id);
            $created_solution_id = null;
            if ($task_set->exists()) {
                $teacher = new Teacher();
                $teacher->get_by_id($this->usermanager->get_teacher_id());
                
                $solution = new Solution();
                $solution->from_array($solution_data, ['student_id', 'comment']);
                if (trim($solution_data['points']) !== '') {
                    $solution->points = (float)$solution_data['points'];
                } else {
                    $solution->points = null;
                }
                $solution->revalidate = 0;
                $solution->save([$teacher, $task_set]);
                
                $solution->where('task_set_id', $task_set_id);
                $solution->where('student_id', (int)$solution_data['student_id']);
                if ($solution->count() === 1) {
                    $created_solution_id = $solution->id;
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_solutions_list_new_solution_created',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                    $this->_action_success();
                    $this->output->set_internal_value('student_id', $solution->student_id);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_solutions_list_new_solution_error_solution_exists',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_solutions_list_new_solution_error_student_not_in_course_or_group',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(
                create_internal_url(
                    'admin_solutions/new_solution_form/' . (int)$task_set_id . ($created_solution_id !== null
                        ? '/' . (int)$created_solution_id
                        : '')
                )
            );
        } else {
            $this->new_solution_form($task_set_id);
        }
    }
    
    public function new_solution_form($task_set_id, $last_created_solution_id = null): void
    {
        $task_set = new Task_set();
        $task_set->select('`task_sets`.*');
        $task_set->select_subquery(
            '(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` '
            . 'WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)',
            'task_set_total_points'
        );
        $task_set->get_by_id($task_set_id);
        $this->inject_students($task_set_id);
        $this->parser->parse(
            'backend/solutions/new_solution_form.tpl',
            [
                'task_set'                 => $task_set,
                'last_created_solution_id' => $last_created_solution_id,
            ]
        );
    }
    
    public function display_tasks_list($task_set_id): void
    {
        $this->_add_mathjax();
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $tasks = $task_set
                ->task
                ->include_join_fields()
                ->order_by('`task_task_set_rel`.`sorting`', 'asc')
                ->get();
            $this->lang->init_overlays('tasks', $tasks, ['name', 'text']);
            $this->parser->assign('tasks', $tasks);
            $this->parser->assign('task_set', $task_set);
        }
        $this->parser->parse('backend/solutions/display_tasks_list.tpl');
    }
    
    public function valuation($task_set_id, $solution_id): void
    {
        $this->_add_mathjax();
        $solution = new Solution();
        $solution->select('`solutions`.*');
        $solution->select_subquery(
            '(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` '
            . 'WHERE `task_set_id` = `task_sets`.`id` AND `task_task_set_rel`.`bonus_task` = 0)',
            'task_set_total_points'
        );
        $solution->include_related('task_set', '*', true, true);
        $solution->include_related('task_set/course', 'name');
        $solution->include_related('task_set/course/period', 'name');
        $solution->include_related('task_set/group', 'name');
        $solution->include_related('student', ['fullname', 'email']);
        $solution->include_related('teacher', ['fullname', 'email']);
        $solution->where('student_id IS NOT NULL');
        $solution->where('task_set_id', $task_set_id);
        $solution->get_by_id($solution_id);
        
        $group = new Group();
        $group->where_related('participant', 'student_id', $solution->student_id);
        $group->where_related('participant/course/task_set', 'id', $task_set_id);
        $group->get();
        
        $project_selection = new Project_selection();
        $project_selection->select(
            '`project_selections`.*, `task_task_task_set_rel`.`internal_comment` AS `task_join_internal_comment`'
        );
        $project_selection->include_related('task', '*', true, true);
        $project_selection->include_related('task/task_set', ['id', 'name']);
        $project_selection->where('task_set_id', $solution->task_set_id);
        $project_selection->where('student_id', $solution->student_id);
        $project_selection->where('task_task_sets.id', $solution->task_set_id);
        $project_selection->get();
        
        $this->load->helper('tests');
        $test_types_subtypes = get_all_supported_test_types_and_subtypes();
        
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/valuation.js');
        $this->parser->add_css_file('admin_solutions.css', ['media' => '']);
        $this->_add_prettify();
        $this->parser->parse('backend/solutions/valuation.tpl', [
            'solution'          => $solution,
            'group'             => $group,
            'test_types'        => $test_types_subtypes['types'],
            'test_subtypes'     => $test_types_subtypes['subtypes'],
            'project_selection' => $project_selection,
            'add_url'           => $this->uri->assoc_to_uri($this->uri->ruri_to_assoc(5)),
        ]);
    }
    
    public function get_next_solution($task_set_id, $solution_id): void
    {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $output = new stdClass();
        $output->have_next = false;
        $output->next_id = null;
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
                $output->have_next = true;
                $output->next_id = $solution->id;
            } else {
                $output->error_message = $this->lang->line(
                    'admin_solutions_valuation_next_solution_message_no_more_solution_to_valuate'
                );
            }
        } else {
            $output->error_message = $this->lang->line(
                'admin_solutions_valuation_next_solution_message_this_solution_is_not_valuated'
            );
        }
        $this->db->trans_rollback();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function _tests_points_value_check($value): bool
    {
        if ($value === '') {
            return true;
        }
        
        return $this->form_validation->floatpoint($value);
    }
    
    public function update_valuation($task_set_id, $solution_id): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'solution[points]',
            'lang:admin_solutions_valuation_form_field_points',
            'trim|callback__tests_points_value_check'
        );
        $this->form_validation->set_rules(
            'solution[tests_points]',
            'lang:admin_solutions_valuation_form_field_tests_points',
            'trim|callback__tests_points_value_check'
        );
        $this->form_validation->set_message(
            '_tests_points_value_check',
            $this->lang->line('admin_solutions_valuation_form_field_tests_points_value_check_error')
        );
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $solution = new Solution();
            $solution->where('task_set_id', (int)$task_set_id);
            $solution->get_by_id($solution_id);
            if ($solution->exists()) {
                $solution_data = $this->input->post('solution');
                if ($solution->comment !== $solution_data['comment']
                    || (float)$solution->points !== (float)$solution_data['points']
                    || $solution->not_considered != (int)$solution_data['not_considered']
                ) {
                    $solution->teacher_id = $this->usermanager->get_teacher_id();
                }
                $solution->from_array($solution_data, ['comment', 'not_considered', 'disable_evaluation_by_tests']);
                $solution->points = isset($solution_data['points']) && $solution_data['points'] !== ''
                    ? $solution_data['points']
                    : null;
                $solution->tests_points = isset($solution_data['tests_points']) && $solution_data['tests_points'] !== ''
                    ? $solution_data['tests_points']
                    : null;
                $solution->revalidate = 0;
                if ($solution->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_solutions_valuation_solution_saved',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                    $this->_action_success();
                    $this->output->set_internal_value('student_id', $solution->student_id);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_solutions_valuation_solution_not_saved',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_solutions_valuation_solution_not_found',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url(
                    'admin_solutions/valuation/' . $task_set_id . '/' . $solution_id
                    . '/' . $this->uri->assoc_to_uri($this->uri->ruri_to_assoc(5)))
            );
        } else {
            $this->valuation($task_set_id, $solution_id);
        }
    }
    
    public function get_student_file_content($task_set_id, $solution_id, $solution_file): void
    {
        $task_set = new Task_set();
        $task_set->where_related('solution', 'id', $solution_id);
        $task_set->include_related('solution/student', 'id');
        $task_set->get_by_id($task_set_id);
        $files = [];
        if ($task_set->exists()) {
            $file_name = decode_from_url($solution_file);
            $files = $task_set->get_student_file_content($file_name);
        }
        $this->parser->parse('backend/solutions/list_of_file_content.tpl', ['files' => $files]);
    }
    
    public function get_solution_version_metadata($task_set_id, $solution_id, $solution_file): void
    {
        $task_set = new Task_set();
        $task_set->where_related('solution', 'id', $solution_id);
        $task_set->include_related('solution/student', 'id');
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $file_name = decode_from_url($solution_file);
            $file_info = $task_set->get_specific_file_info($file_name);
            $this->parser->assign('file_last_modified', $file_info['last_modified']);
            $solution_version = new Solution_version();
            $solution_version->where('version', $file_info['version']);
            $solution_version->where_related('solution', 'id', $solution_id);
            $solution_version->get();
            if (!$solution_version->exists()) {
                $solution_version = new Solution_version();
                $solution_version->version = (int)$file_info['version'];
                $solution_version->solution_id = (int)$solution_id;
                $solution_version->save();
            }
            $this->parser->assign('solution_version', $solution_version);
            $this->parser->assign('task_set', $task_set);
        }
        $this->parser->parse('backend/solutions/version_metadata.tpl');
    }
    
    public function get_solution_version_comment($task_set_id, $solution_id, $solution_file): void
    {
        $task_set = new Task_set();
        $task_set->where_related('solution', 'id', $solution_id);
        $task_set->include_related('solution/student', 'id');
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $file_name = decode_from_url($solution_file);
            $file_info = $task_set->get_specific_file_info($file_name);
            $solution_version = new Solution_version();
            $solution_version->where('version', $file_info['version']);
            $solution_version->where_related('solution', 'id', $solution_id);
            $solution_version->include_related('solution/student', ['fullname']);
            $solution_version->get();
            $this->parser->assign('solution_version', $solution_version);
            $this->parser->assign('task_set', $task_set);
        }
        $this->parser->parse('backend/solutions/version_comment.tpl');
    }
    
    public function solution_version_switch_download_lock($solution_version_id): void
    {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $output = new stdClass();
        $output->status = false;
        $output->value = false;
        $output->message = $this->lang->line(
            'admin_solutions_valuation_version_metadata_download_lock_error_cant_found'
        );
        
        $solution_version = new Solution_version();
        $solution_version->include_related('solution');
        $solution_version->get_by_id((int)$solution_version_id);
        
        if ($solution_version->exists()) {
            $output->value = !($solution_version->download_lock == 0);
            $solution_version->download_lock = $solution_version->download_lock == 0 ? 1 : 0;
            if ($solution_version->save()) {
                $output->value = !($solution_version->download_lock == 0);
                $output->status = true;
                if ($output->value) {
                    $output->message = $this->lang->line(
                        'admin_solutions_valuation_version_metadata_download_lock_enabled'
                    );
                } else {
                    $output->message = $this->lang->line(
                        'admin_solutions_valuation_version_metadata_download_lock_disabled'
                    );
                }
                $this->db->trans_commit();
                $this->_action_success();
                $this->output->set_internal_value('student_id', (int)$solution_version->solution_student_id);
            } else {
                $output->message = $this->lang->line(
                    'admin_solutions_valuation_version_metadata_download_lock_error_cant_save'
                );
                $this->db->trans_rollback();
            }
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function show_file_content($task_set_id, $solution_id, $solution_file, $zip_index): void
    {
        $this->output->set_content_type('text/plain');
        $task_set = new Task_set();
        $task_set->where_related('solution', 'id', $solution_id);
        $task_set->include_related('solution/student', 'id');
        $task_set->get_by_id($task_set_id);
        if ($task_set->exists()) {
            $file_name = decode_from_url($solution_file);
            $output = $task_set->extract_student_file_by_index($file_name, $zip_index);
            if ($output !== false) {
                $this->output->set_output(
                    '<div class="codepreview_container"><pre class="codepreview prettyprint linenums lang-'
                    . strtolower($output['extension']) . '">' . htmlspecialchars($output['content']) . '</pre></div>'
                );
            } else {
                $this->output->set_output(
                    $this->lang->line('admin_solutions_valuation_file_content_error_cant_read_file')
                );
            }
        } else {
            $this->output->set_output(
                $this->lang->line('admin_solutions_valuation_file_content_error_task_set_not_found')
            );
        }
    }
    
    public function get_task_set_list(): void
    {
        $filter = $this->input->post('filter');
        if (!array_key_exists('hide_old', $filter)) {
            $filter['hide_old'] = 0;
        }
        $this->store_task_set_selection_filter($filter);
        
        $this->db->query(
            'CREATE TEMPORARY TABLE course_task_set_type_rel_override AS ( '
            . 'SELECT ctstr.course_id, ctstr.task_set_type_id, ctstr.upload_solution '
            . 'FROM course_task_set_type_rel ctstr ) '
            . 'UNION ( SELECT cs.id as course_id, 0 AS task_set_type_id, 1 AS upload_solution '
            . 'FROM (SELECT id FROM courses) cs )'
        );
        
        $solutions = new Solution();
        $solutions->select_func('COUNT', 'id', 'cnt');
        $solutions->where_related('student/participant/course', 'id', '${parent}.course_id');
        $solutions->where_related('task_set', 'id', '${parent}.id');
        
        $task_sets = new Task_set();
        /*$task_sets->select(
            '`task_sets`.*, `course_course_task_set_type_rel`.`upload_solution` AS `join_upload_solution`'
        );*/
        $task_sets->select('`task_sets`.*');
        $task_sets->select_subquery(
            '(SELECT `sq_ctst`.`upload_solution` FROM course_task_set_type_rel_override AS `sq_ctst` '
            . 'WHERE `sq_ctst`.`course_id` = `${parent}`.`course_id` '
            . 'AND `sq_ctst`.`task_set_type_id` = `${parent}`.`task_set_type_id`)',
            'join_upload_solution'
        );
        //$task_sets->include_related_count('task_set_permission');
        //$task_sets->add_join_condition('`task_set_permissions`.`enabled` = 1');
        $task_sets->select('*');
        $task_sets->select_subquery(
            '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
            . 'WHERE `tsp`.`task_set_id` = ${parent}.`id` AND `tsp`.`enabled` = 1)',
            'task_set_permission_count'
        );
        //$task_sets->include_related_count('solution');
        $task_sets->select_subquery($solutions, 'solution_count');
        $task_sets->include_related_count('task');
        $task_sets->include_related('course', ['name', 'default_points_to_remove', 'id']);
        $task_sets->include_related('course/period', 'name');
        $task_sets->include_related('group', 'name');
        $task_sets->include_related('task_set_type', 'name');
        //$task_sets->where_related('solution/student/participant/course', 'id', 'courses.id');
        /*$task_sets->include_related('course/task_set_type');
        $task_sets->where('(`course_task_set_types`.`id` = `task_sets`.`task_set_type_id`)');
        $task_sets->where(
            '((`course_course_task_set_type_rel`.`task_set_type_id` = `task_sets`.`task_set_type_id` '
            . 'AND `task_sets`.`task_set_type_id` != 0) OR `task_sets`.`task_set_type_id` = 0)'
        );*/
        $task_sets->where('content_type', $filter['content_type'] ?? 'task_set');
        if (isset($filter['course']) && (int)$filter['course'] > 0) {
            $task_sets->where_related_course('id', (int)$filter['course']);
        }
        if (isset($filter['group']) && $filter['group'] == 'NULL') {
            $task_sets->where_related_group('id', null);
            $task_sets->where_subquery(
                0,
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
            );
        } else if (isset($filter['group']) && (int)$filter['group'] > 0) {
            $task_sets->group_start();
            $task_sets->or_group_start();
            $task_sets->where_related_group('id', (int)$filter['group']);
            $task_sets->where_subquery(
                0,
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
            );
            $task_sets->group_end();
            $task_sets->or_group_start();
            $task_sets->where_related('task_set_permission/group', 'id', (int)$filter['group']);
            $task_sets->where_related('task_set_permission', 'enabled', 1);
            $task_sets->group_end();
            $task_sets->group_end();
        }
        if (isset($filter['hide_old']) && (bool)$filter['hide_old']) {
            $old = date('Y-m-d H:i:s', strtotime('now -2 weeks'));
            $task_sets->group_start();
            $task_sets->group_start();
            $task_sets->where_subquery(
                0,
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
            );
            $task_sets->group_start();
            $task_sets->where('upload_end_time', null);
            $task_sets->or_where('upload_end_time >', $old);
            $task_sets->group_end();
            $task_sets->group_end();
            $task_sets->or_group_start();
            $task_sets->where('content_type !=', 'project');
            $task_sets->where_subquery(
                '0 <',
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
            );
            $task_sets->group_start();
            $task_sets->where_subquery(
                '0 <',
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1 '
                . 'AND `tsp`.`upload_end_time` IS NULL)'
            );
            $task_sets->or_where_subquery(
                '0 <',
                '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1 '
                . 'AND `tsp`.`upload_end_time` > \'' . $old . '\')'
            );
            $task_sets->group_end();
            $task_sets->group_end();
            $task_sets->group_end();
        }
        if (isset($filter['content_type'], $filter['task_set_type']) && $filter['content_type'] === 'task_set'
            && (int)$filter['task_set_type'] > 0
        ) {
            $task_sets->where_related_task_set_type('id', (int)$filter['task_set_type']);
        }
        $order_by_direction = $filter['order_by_direction'] === 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] === 'course') {
            $task_sets->order_by_related('course/period', 'sorting', $order_by_direction);
            $task_sets->order_by_related_with_constant('course', 'name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'task_set_name') {
            $task_sets->order_by_with_overlay('name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'course_group') {
            $task_sets->order_by_related_with_constant('group', 'name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'task_set_type') {
            $task_sets->order_by_related_with_constant('task_set_type', 'name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'solution_count') {
            $task_sets->order_by('solution_count', $order_by_direction);
        } else if ($filter['order_by_field'] === 'task_count') {
            $task_sets->order_by('task_count', $order_by_direction);
        } else if ($filter['order_by_field'] === 'upload_end_time') {
            $task_sets->order_by('upload_end_time', $order_by_direction);
        } else if ($filter['order_by_field'] === 'content_type') {
            $task_sets->order_by('content_type', $order_by_direction);
        }
        $task_sets->get_paged_iterated(
            isset($filter['page']) ? (int)$filter['page'] : 1,
            isset($filter['rows_per_page']) ? (int)$filter['rows_per_page'] : 25
        );
        //$task_sets->check_last_query();
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), ['name']);
        $this->parser->parse('backend/solutions/task_set_list.tpl', ['task_sets' => $task_sets]);
    }
    
    public function get_solutions_list_for_task_set($task_set_id): void
    {
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
            if ($task_set->content_type === 'project' && isset($filter['author']) && $filter['author'] !== 'all'
                && $filter['author'] !== ''
            ) {
                $solutions->where_related(
                    'student/project_selection/task_set',
                    'id',
                    $task_set->id
                );
                $solutions->where_related(
                    'student/project_selection/task/author',
                    'id',
                    (int)$filter['author']
                );
                $solutions->group_by('id');
            }
            $solutions->get_iterated();
        }
        
        $this->parser->parse(
            'backend/solutions/solutions_list_table_content.tpl',
            [
                'task_set'  => $task_set,
                'solutions' => $solutions,
            ]
        );
    }
    
    public function get_points_overview($task_set_id = null): void
    {
        $filter = $this->input->post('filter');
        
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $data = [];
        
        if ($task_set->exists()) {
            $solutions = new Solution();
            
            $solutions->where_related($task_set);
            //$solutions->include_related('student');
            //$solutions->include_related('teacher');
            //$solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
            //$solutions->include_related('student/participant/group');
            if (isset($filter['group']) && (int)$filter['group'] > 0) {
                $solutions->where_related('student/participant/group', 'id', (int)$filter['group']);
            }
            $solutions->where_related('student/participant/course', 'id', $task_set->course_id);
            if ($task_set->content_type === 'project' && isset($filter['author'])
                && $filter['author'] !== 'all' && $filter['author'] !== ''
            ) {
                $solutions->where_related(
                    'student/project_selection/task_set',
                    'id',
                    $task_set->id
                );
                $solutions->where_related(
                    'student/project_selection/task/author',
                    'id',
                    (int)$filter['author']
                );
                $solutions->group_by('id');
            }
            $solutions->group_start();
            $solutions->group_start('NOT');
            $solutions->where('points', null);
            $solutions->group_end();
            $solutions->group_start('NOT', 'OR ');
            $solutions->where('tests_points', null);
            $solutions->group_end();
            $solutions->group_end();
            $solutions->where('not_considered', 0);
            $solutions->get_iterated();
            
            foreach ($solutions as $solution) {
                $points_total = (string)((double)$solution->points + (double)$solution->tests_points);
                if (!isset($data[$points_total])) {
                    $data[$points_total] = 1;
                } else {
                    $data[$points_total]++;
                }
            }
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data, JSON_FORCE_OBJECT));
    }
    
    public function get_groups_from_course($course_id, $selected_id = null): void
    {
        $groups = new Group();
        $groups->select('id, name');
        $groups->where_related_course('id', $course_id);
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        $options = [
            ''     => '',
            'NULL' => $this->lang->line('admin_solutions_group_no_group'),
        ];
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $this->parser->parse(
            'backend/solutions/groups_from_course.tpl',
            [
                'groups'   => $options,
                'selected' => $selected_id,
            ]
        );
    }
    
    public function valuation_tables(): void
    {
        $this->_select_teacher_menu_pagetag('valuation_tables');
        $this->inject_stored_valuation_tables_filter();
        $this->inject_courses();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/valuation_tables.js');
        $this->parser->add_css_file('dataTables.fixedColumns.css');
        $this->parser->add_css_file('dataTables.colVis.css');
        $this->parser->add_css_file('admin_solutions.css');
        $this->_add_dataTables();
        $this->parser->add_js_file('jquery.DataTables.FixedColumns.js');
        $this->parser->add_js_file('jquery-migrate-1.2.1.min.js');
        $this->parser->add_js_file('dataTables.colVis.min.js');
        $this->parser->parse('backend/solutions/valuation_tables.tpl');
    }
    
    public function get_valuation_table(): void
    {
        $filter = $this->input->post('filter');
        $this->store_valuation_tables_filter($filter);
        $this->inject_stored_valuation_tables_filter();

        $table_data = $this->get_valuation_table_data((int)@$filter['course'], @$filter['group'], (bool)@$filter['simple']);
        $this->add_virtual_task_set_types_data($table_data, (int)@$filter['course']);
        
        $this->parser->assign('table_data', $table_data);
        
        $course = new Course();
        $course->include_related('period');
        $course->get_by_id((int)@$filter['course']);
        
        $group = new Group();
        $group->get_by_id(@$filter['group']);
        
        $this->parser->parse(
            'backend/solutions/valuation_table_content.tpl',
            [
                'course' => $course,
                'group'  => $group,
            ]
        );
    }
    
    public function student_solution_upload($solution_id): void
    {
        $solution = new Solution();
        $solution->include_related('student', ['fullname', 'email']);
        $solution->include_related('task_set', 'name');
        $solution->include_related('task_set/course', 'name');
        $solution->include_related('task_set/course/period', 'name');
        $solution->join_related('student/participant');
        $solution->add_join_condition('`student_participants`.`course_id` = `task_sets`.`course_id`');
        $solution->include_related('student/participant/group', ['name', 'id'], 'group');
        $solution->get_by_id((int)$solution_id);
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->add_js_file('admin_solutions/upload.js');
        $this->parser->parse('backend/solutions/student_solution_upload.tpl', ['solution' => $solution]);
    }
    
    public function do_upload_student_solution($solution_id): void
    {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $solution = new Solution();
        $solution->include_related('task_set', '*', true, true);
        $solution->include_related('student');
        $solution->get_by_id((int)$solution_id);
        if ($solution->exists()) {
            if ($solution->task_set->exists()) {
                $allowed_file_types_array = trim($solution->task_set->allowed_file_types) !== ''
                    ? array_map('trim', explode(',', $solution->task_set->allowed_file_types))
                    : [];
                $config['upload_path'] = 'private/uploads/solutions/task_set_' . (int)$solution->task_set->id . '/';
                $config['allowed_types'] = 'zip' . (count($allowed_file_types_array)
                        ? '|' . implode('|', $allowed_file_types_array)
                        : '');
                $config['max_size'] = (int)$this->config->item('maximum_solition_filesize');
                $config['file_name'] =
                    $solution->student_id . '_' . $this->normalize_student_name($solution->student_fullname)
                    . '_' . substr(md5(time() . rand(-500000, 500000)), 0, 4) . '_'
                    . $solution->task_set->get_student_file_next_version($solution->student_id) . '.zip';
                @mkdir($config['upload_path'], DIR_READ_MODE);
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('upload')) {
                    $upload_data = $this->upload->data();
                    $mimes = $this->upload->mimes_types('zip');
                    if ((is_array($mimes) && !in_array($upload_data['file_type'], $mimes, true))
                        || (is_string($mimes) && $upload_data['file_type'] !== $mimes)
                    ) {
                        if (!$this->zip_plain_file_to_archive(
                            $upload_data['full_path'],
                            $upload_data['client_name'],
                            $upload_data['file_path']
                        )) {
                            $this->messages->add_message(
                                'lang:admin_solutions_upload_cant_zip_file',
                                Messages::MESSAGE_TYPE_ERROR
                            );
                            redirect(create_internal_url(
                                    'admin_solutions/student_solution_upload/' . (int)$solution_id)
                            );
                            die();
                        }
                    }
                    $solution->revalidate = 1;
                    $solution->save();
                    if ($this->db->trans_status()) {
                        $log = new Log();
                        $log->add_teacher_solution_upload_log(
                            sprintf(
                                $this->lang->line('admin_solutions_upload_log_message'),
                                $config['file_name']
                            ),
                            $this->usermanager->get_teacher_id(),
                            $solution->student_id,
                            $solution->id
                        );
                        $this->db->trans_commit();
                        $this->messages->add_message(
                            'lang:admin_solutions_upload_success',
                            Messages::MESSAGE_TYPE_SUCCESS
                        );
                        $this->_action_success();
                        $this->output->set_internal_value('student_id', $solution->student_id);
                    } else {
                        $this->db->trans_rollback();
                        @unlink($config['upload_path'] . $config['file_name']);
                        $this->messages->add_message(
                            'lang:admin_solutions_upload_failed',
                            Messages::MESSAGE_TYPE_ERROR
                        );
                    }
                    redirect(create_internal_url(
                            'admin_solutions/student_solution_upload/' . (int)$solution_id)
                    );
                } else {
                    $this->db->trans_rollback();
                    $this->parser->assign(
                        'file_error_message',
                        $this->upload->display_errors('', '')
                    );
                    $this->student_solution_upload($solution_id);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_solutions_upload_task_set_not_found',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
        } else {
            $this->db->trans_rollback();
            $this->student_solution_upload($solution_id);
        }
    }
    
    public function download_solutions($task_set_id): void
    {
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_set_id);
        if ($task_set->exists()) {
            $task_set->download_all_solutions();
        } else {
            $this->messages->add_message(
                'lang:admin_solutions_solutions_download_unknown_task_set',
                Messages::MESSAGE_TYPE_ERROR
            );
            redirect(create_internal_url('admin_solutions'));
        }
    }
    
    private function zip_plain_file_to_archive($archive_name, $original_file_name, $file_path)
    {
        if (file_exists($archive_name)) {
            rename($archive_name, rtrim($file_path, '/\\') . '/' . $original_file_name);
            $zip = new ZipArchive();
            if ($zip->open($archive_name, ZipArchive::CREATE) === true) {
                $zip->addFile(
                    rtrim($file_path, '/\\') . '/' . $original_file_name,
                    $original_file_name
                );
                $zip->close();
                @unlink(rtrim($file_path, '/\\') . '/' . $original_file_name);
                return true;
            }
            
            @unlink(rtrim($file_path, '/\\') . '/' . $original_file_name);
            return false;
        }
        return false;
    }
    
    private function normalize_student_name($student_fullname): string
    {
        $normalized = normalize($student_fullname);
        $output = '';
        for ($i = 0, $iMax = mb_strlen($normalized); $i < $iMax; $i++) {
            $char = mb_substr($normalized, $i, 1);
            if (preg_match('/^[a-zA-Z]$/', $char)) {
                $output .= $char;
            }
        }
        return $output;
    }
    
    private function store_task_set_selection_filter($filter): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(
                self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME,
                'course'
            );
            $this->filter->set_filter_delete_on_course_change(
                self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME,
                ['group']
            );
        }
    }
    
    private function inject_stored_task_set_selection_filter(): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_TASK_SET_SELECTION_FILTER_SESSION_NAME,
            $this->usermanager->get_teacher_id(),
            'course'
        );
        $this->parser->assign('filter', $filter);
    }
    
    private function store_valuation_tables_filter($filter)
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $new_filter['simple'] = isset($filter['simple']) && $filter['simple'] === 1 ? 1 : 0;
            $this->filter->store_filter(self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(
                self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME,
                'course'
            );
            $this->filter->set_filter_delete_on_course_change(
                self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME,
                ['group']
            );
        }
    }
    
    private function inject_stored_valuation_tables_filter(): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME,
            $this->usermanager->get_teacher_id(),
            'course'
        );
        $this->parser->assign('filter', $filter);
    }
    
    private function store_solution_list_filter($filter, $task_set_id): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(
                self::STORED_SOLUTION_SELECTION_FILTER_SESSION_NAME . '_' . $task_set_id
            );
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(
                self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME . '_' . $task_set_id, $new_filter
            );
        }
    }
    
    private function inject_stored_solution_list_filter($task_set_id): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_VALUATION_TABLES_FILTER_SESSION_NAME . '_' . $task_set_id
        );
        $this->parser->assign('filter', $filter);
    }
    
    private function store_batch_valuation_filter($filter, $task_set_id): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(
                self::STORED_BATCH_VALUATION_FILTER_SESSION_NAME . '_' . $task_set_id
            );
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(
                self::STORED_BATCH_VALUATION_FILTER_SESSION_NAME . '_' . $task_set_id, $new_filter
            );
        }
    }
    
    private function inject_batch_valuation_filter($task_set_id): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_BATCH_VALUATION_FILTER_SESSION_NAME . '_' . $task_set_id
        );
        $this->parser->assign('filter', $filter);
    }
    
    private function get_batch_valuation_filter($task_set_id): array
    {
        $this->load->library('filter');
        return $this->filter->restore_filter(
            self::STORED_BATCH_VALUATION_FILTER_SESSION_NAME . '_' . $task_set_id
        );
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
    
    private function inject_students($task_set_id): void
    {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $task_set_permissions = new Task_set_permission();
        $task_set_permissions->where_related($task_set);
        $task_set_permissions->where('enabled', 1);
        $task_set_permissions->get_iterated();
        
        $data = ['' => ''];
        if ($task_set->exists()) {
            $students = new Student();
            $students->where_related('participant', 'allowed', 1);
            $students->where_related('participant/course/task_set', 'id', (int)$task_set_id);
            if ($task_set_permissions->result_count() === 0) {
                if (!is_null($task_set->group_id)) {
                    $students->where_related('participant/group', 'id', (int)$task_set->group_id);
                }
            } else {
                $group_ids = [];
                foreach ($task_set_permissions as $task_set_permission) {
                    $group_ids[] = (int)$task_set_permission->group_id;
                }
                $students->where_in_related('participant/group', 'id', $group_ids);
            }
            $students->include_related('participant/group', 'name', 'group');
            $students->where(
                'students.id = `students`.`id` AND NOT EXISTS (SELECT * FROM `solutions` '
                . 'WHERE `solutions`.`student_id` = `students`.`id` '
                . 'AND `solutions`.`task_set_id` = ' . (int)$task_set_id . ')'
            );
            $students->group_by('id');
            $students->order_by_related_with_constant('participant/group', 'name', 'asc');
            $students->order_by_as_fullname('fullname', 'asc');
            $students->order_by('email', 'asc');
            $students->get_iterated();
            
            foreach ($students as $student) {
                $data[is_null($student->group_name)
                    ? 'lang:admin_solutions_student_selection_not_in_group'
                    : $student->group_name][$student->id] = $student->fullname . ' (' . $student->email . ')';
            }
        }
        $this->parser->assign('students', $data);
    }
    
    private function inject_batch_valuation($task_set_id): void
    {
        $filter = $this->input->post('filter');
        $filter = is_array($filter) && count($filter) > 0
            ? $filter
            : $this->get_batch_valuation_filter((int)$task_set_id);
        $this->store_batch_valuation_filter($filter, (int)$task_set_id);
        
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $task_set_permissions = new Task_set_permission();
        $task_set_permissions->where_related($task_set);
        $task_set_permissions->where('enabled', 1);
        $task_set_permissions->get_iterated();
        
        $solutions = new Solution();
        $solutions->where_related($task_set);
        $solutions->group_by('student_id');
        if (isset($filter['group']) && (int)$filter['group'] > 0) {
            $solutions->where_related('student/participant/group', 'id', (int)$filter['group']);
        }
        $solutions->get_iterated();
        
        $additional_student_ids = [0];
        foreach ($solutions as $solution) {
            $additional_student_ids[] = $solution->student_id;
        }
        
        $data = [];
        if ($task_set->exists()) {
            $students = new Student();
            $students->where_related('participant', 'allowed', 1);
            $students->where_related('participant/course/task_set', 'id', (int)$task_set_id);
            if ($task_set_permissions->result_count() === 0) {
                if (!is_null($task_set->group_id)) {
                    $students->where_related('participant/group', 'id', (int)$task_set->group_id);
                }
            } else {
                $group_ids = [];
                foreach ($task_set_permissions as $task_set_permission) {
                    $group_ids[] = (int)$task_set_permission->group_id;
                }
                $students->where_in_related('participant/group', 'id', $group_ids);
            }
            $students->include_related('solution');
            $students->add_join_condition('`solutions`.`task_set_id` = ?', [$task_set->id]);
            $students->order_by_as_fullname('fullname', 'asc');
            $students->order_by('email', 'asc');
            if (isset($filter['group']) && (int)$filter['group'] > 0) {
                $students->where_related('participant/group', 'id', (int)$filter['group']);
            }
            $students->or_where_in('id', $additional_student_ids);
            $students->get_iterated();
            
            foreach ($students as $student) {
                $data[$student->id] = clone $student;
            }
        }
        $this->parser->assign('batch_valuation_students', $data);
    }
    
    private function inject_all_task_set_types(): void
    {
        $task_set_types = new Task_set_type();
        $task_set_types->order_by_with_constant('name', 'asc');
        $task_set_types->get_iterated();
        
        $data = ['' => ''];
        foreach ($task_set_types as $task_set_type) {
            $data[$task_set_type->id] = $task_set_type->name;
        }
        
        $this->parser->assign('task_set_types', $data);
    }
    
    public function inject_task_set_possible_groups($task_set_id): void
    {
        $task_sets = new Task_set();
        $task_sets->where('group_id', null);
        $task_sets->include_related('course/group');
        $task_sets->where('id', (int)$task_set_id);
        $task_sets->where_subquery(
            0,
            '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
            . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
        );
        
        $task_sets2 = new Task_set();
        $task_sets2->where('id', (int)$task_set_id);
        $task_sets2->include_related('task_set_permission/group', '*', 'course_group');
        $task_sets2->where_related('task_set_permission', 'enabled', 1);
        $task_sets2->group_start(' NOT ');
        $task_sets2->or_where_subquery(
            0,
            '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
            . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
        );
        $task_sets2->or_where_subquery(
            1,
            '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
            . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
        );
        $task_sets2->group_end();
        
        $task_sets->union_iterated(
            $task_sets2,
            false,
            $task_sets->union_order_by_constant('course_group_name', 'asc')
        );
        
        $data = ['' => ''];
        foreach ($task_sets as $task_set) {
            $data[$task_set->course_group_id] = $task_set->course_group_name;
        }
        
        $this->parser->assign('possible_groups', $data);
    }
    
    private function inject_task_set_authors($task_set_id): void
    {
        $teachers = new Teacher();
        $teachers->where_related('task/task_set', 'id', (int)$task_set_id);
        $teachers->order_by_as_fullname('fullname');
        $teachers->get_iterated();
        
        $authors = [];
        foreach ($teachers as $teacher) {
            $authors[$teacher->id] = $teacher->fullname;
        }
        
        $this->parser->assign('authors', $authors);
    }

    /**
     * @param table_data an array with all students' points data from non-virtual task set types and task sets used in the valuation table.
     * @param max determines whether the resulting array should contain maximum points ​​or total points.
     * @return array where the key is the student's id and value is an array with task set types ids as keys and student's total/max points as values ([student_id => [type_id => total_points], ...]).
     * Extracts necessary data for formula evaluation from the given points array.
     */
    private function extract_evaluation_data($table_data, $max=false): array
    {
        $course_id = $this->input->post('filter')['course'];
        $course = new Course();
        $course->get_by_id($course_id);
        $course->task_set_type->get();
        $task_set_types = $course->task_set_type
            ->include_join_fields()
            ->where('virtual', 0)
            ->get();
        $types = array_map(function ($type) {
            return $type->id;
        }, $task_set_types->all);
        
        $evaluation_data = [];
        foreach ($table_data['content'] as $student_data) {
            $student_id = $student_data['id'];
            $evaluation_data[$student_id] = [];
            
            foreach($types as $type_id){
                $evaluation_data[$student_id][$type_id] = 0;
            }
            
            foreach ($student_data['task_sets_points'] as $index=>$points_data) {
                if ($points_data['type'] == 'task_set_type' || $points_data['type'] == 'task_set_type_completed') {
                    $task_set_type_id = $table_data['header']['content_type_task_set']['items'][$index]['id'];
                    $evaluation_data[$student_id][$task_set_type_id] = ($max) ? $points_data['max_points'] : $points_data['points'] ;
                }
            }
        }
        return $evaluation_data;
    }

    /**
     * @param int $course_id id of course which we are interested in.
     * @return Task_set_type collection in the course which are marked as virtual.
     * Finds and returns all virtual task set types along with their join fields from the given course.
     */
    private function get_virtual_task_set_types($course_id) : Task_set_type
    {
        $course = new Course();
        $course->get_by_id($course_id);
        $course->task_set_type
            ->include_join_fields()
            ->where('virtual', 1)
            ->get();

        return $course->task_set_type;
    }

    /**
     * @param array $table_data a reference to an array with all students' points data from non-virtual task set types and task sets used in the valuation table.
     * @param int   $course_id  id of course which we are interested in.
     * Adds virtual task set types points data to the given points array and increases total points by the appropriate amount in the points array.
     */
    private function add_virtual_task_set_types_data(&$table_data, $course_id) : void {
        $virtual_types = $this->get_virtual_task_set_types($course_id);
        $evaluation_data = $this->extract_evaluation_data($table_data);
        $max_evaluation_data = $this->extract_evaluation_data($table_data, true);

        foreach ($virtual_types as $virtual_type) {
            $table_data['header']['content_type_task_set']['items'][] = [
                'type'  => 'task_set_type',
                'id'    => $virtual_type->id,
                'name'  => $this->lang->text($virtual_type->name),
                'title' => '',
            ];
        }
        
        $container = ContainerFactory::getContainer();
        /** @var FormulaService $formulaService */
        $formulaService = $container->get(FormulaService::class);
        $formula_evaluation_data = $formulaService->evaluate_formulas($evaluation_data, $virtual_types);
        $formula_max_evaluation_data = $formulaService->evaluate_formulas($max_evaluation_data, $virtual_types);

        foreach($table_data['content'] as $index=>$student_data){
            $student_formula_eval_data = $formula_evaluation_data[$student_data['id']];

            foreach ($virtual_types as $virtual_type) {
                $virtual_type_id = $virtual_type->id;
                $points = null;
                $rounded_points = 0;
                
                if (array_key_exists($virtual_type_id, $student_formula_eval_data)) {
                    $points = $student_formula_eval_data[$virtual_type_id];
                    $rounded_points = round($points, 2);
                    $min_points = $virtual_type->join_min_points;
                    $minReached = false;
    
                    if ($min_points != null) {
                        if ($virtual_type->join_min_points_in_percentage == 1) {
                            $student_formula_max_eval_data = $formula_max_evaluation_data[$student_data['id']];
                            $max_points = $student_formula_max_eval_data[$virtual_type_id];
                            $minReached = $points / $max_points * 100 >= $min_points;
                        } else {
                            $minReached = $points >= $min_points;
                        }
                    }
                }
                if ($points === null){
                    $table_data['content'][$index]['task_sets_points'][] = [
                        'type'   => 'task_set_type',
                        'points' => 'err',
                        'flag'   => 'ok',
                    ];
                } else {
                    $table_data['content'][$index]['task_sets_points'][] = [
                        'type'   => ($minReached) 
                            ? 'task_set_type_completed' 
                            : 'task_set_type',
                        'points' => $rounded_points,
                        'flag'   => 'ok',
                    ];
                    $table_data['content'][$index]['task_sets_points_total'] += $rounded_points;
                }
                if ($virtual_type->join_include_in_total == 1) {
                    $table_data['content'][$index]['total_points'] += $rounded_points;
                }
            }
        }
    }
    
    private function get_valuation_table_data($course_id, $group_id = null, $condensed = false): array
    {
        $table_data = [
            'header'  => [],
            'content' => [],
        ];
        
        $course = new Course();
        $course->get_by_id((int)$course_id);
        
        $group = new Group();
        $group->get_by_id((int)$group_id);
        
        if ($course->exists()) {
            $students = new Student();
            $students->select('id, fullname, email');
            $students->include_related('participant/group', ['id', 'name']);
            $students->where_related('participant/course', 'id', $course->id);
            $students->where_related('participant', 'allowed', 1);
            $students->order_by_as_fullname('fullname');
            if ($group->exists()) {
                $students->where_related('participant/group', 'id', (int)$group_id);
            }
            $students->get_iterated();
            
            $task_sets_out_of_group_ids = [0];
            $task_sets_data = [];
            $task_sets_ids = [];
            $projects_ids = [];
            
            if ($group->exists()) {
                $students_filter = new Student();
                $students_filter->select('id');
                $students_filter->where_related('participant/course', 'id', $course->id);
                $students_filter->where_related('participant', 'allowed', 1);
                $students_filter->where_related('participant/group', 'id', (int)$group->id);
                
                $solutions_filter = new Solution();
                $solutions_filter->select('id');
                $solutions_filter->where_in_subquery('student_id', $students_filter);
                
                $task_sets_out_of_group = new Task_set();
                $task_sets_out_of_group->select('id');
                $task_sets_out_of_group->where_in_subquery('id', $solutions_filter);
                $task_sets_out_of_group->where('published', 1);
                $task_sets_out_of_group->get();
                $task_sets_out_of_group_ids = $task_sets_out_of_group->all_to_single_array('id');
                $task_sets_out_of_group_ids[] = 0;
            }
            
            $content_type_task_set = new Task_set();
            $content_type_task_set->select('id, name, content_type, group_id, task_set_type_id');
            $content_type_task_set->include_related('task_set_type', 'name');
            $content_type_task_set->include_related('group', 'name');
            $content_type_task_set->where('content_type', 'task_set');
            $content_type_task_set->where('published', 1);
            $content_type_task_set->where_related_course($course);
            $content_type_task_set->order_by_related_with_constant(
                'task_set_type',
                'name',
                'asc'
            );
            $content_type_task_set->order_by('task_set_type_id', 'asc');
            $content_type_task_set->order_by('sorting', 'asc');
            $content_type_task_set->order_by('publish_start_time', 'asc');
            if ($group->exists()) {
                $content_type_task_set->group_start();
                $content_type_task_set->group_start('', 'OR ');
                $content_type_task_set->group_start();
                $content_type_task_set->or_where('group_id', null);
                $content_type_task_set->or_where('group_id', (int)$group_id);
                $content_type_task_set->group_end();
                $content_type_task_set->where_subquery(
                    0,
                    '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp '
                    . 'WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)'
                );
                $content_type_task_set->group_end();
                $content_type_task_set->group_start('', 'OR ');
                $content_type_task_set->where_related('task_set_permission', 'group_id', (int)$group_id);
                $content_type_task_set->where_related('task_set_permission', 'enabled', 1);
                $content_type_task_set->group_end();
                $content_type_task_set->or_where_in('id', $task_sets_out_of_group_ids);
                $content_type_task_set->group_end();
            }
            $content_type_task_set->select_subquery(
                '(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` ' .
                'WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)',
                'total_points'
            );
            $content_type_task_set->get();
            $header_items = [];
            if ($content_type_task_set->result_count() > 0) {
                $last_task_set_type_id = null;
                foreach ($content_type_task_set->all as $task_set) {
                    $permissions = new Task_set_permission();
                    $permissions->select('id, group_id');
                    $permissions->include_related('group', 'name');
                    $permissions->where_related_task_set($task_set);
                    $permissions->where('enabled', 1);
                    $permissions->get_iterated();
                    if ($permissions->result_count() > 0) {
                        $group_ids = [];
                        $group_names = [];
                        foreach ($permissions as $permission) {
                            $group_ids[] = $permission->group_id;
                            $group_names[] = $this->lang->text($permission->group_name);
                        }
                        $task_sets_data[$task_set->id] = [
                            'group_id'   => $group_ids,
                            'group_name' => $group_names,
                        ];
                    } else {
                        $task_sets_data[$task_set->id] = [
                            'group_id'   => [$task_set->group_id],
                            'group_name' => $this->lang->text($task_set->group_name),
                        ];
                    }
                    if ($task_set->task_set_type_id !== $last_task_set_type_id) {
                        $last_task_set_type_id = $task_set->task_set_type_id;
                        $header_items[] = [
                            'type'  => 'task_set_type',
                            'id'    => $task_set->task_set_type_id,
                            'name'  => $this->lang->text($task_set->task_set_type_name),
                            'title' => '',
                        ];
                    }
                    if (!$condensed) {
                        $header_items[] = [
                            'type'  => 'task_set',
                            'id'    => $task_set->id,
                            'name'  => $this->lang->get_overlay_with_default(
                                'task_sets',
                                $task_set->id,
                                'name',
                                $task_set->name
                            ),
                            'title' => is_array($task_sets_data[$task_set->id]['group_name'])
                                ? implode(', ', $task_sets_data[$task_set->id]['group_name'])
                                : $task_sets_data[$task_set->id]['group_name'],
                        ];
                    }
                    $task_sets_ids[] = $task_set->id;
                }
            }
            $table_data['header']['content_type_task_set'] = [
                'content_type_name' => $this->lang->line(
                    'admin_solutions_valuation_tables_header_content_type_task_sets'
                ),
                'items'             => $header_items,
            ];
            
            $content_type_project = new Task_set();
            $content_type_project->where('content_type', 'project');
            $content_type_project->where('published', 1);
            $content_type_project->where_related_course($course);
            $content_type_project->order_by_related_with_constant(
                'task_set_type',
                'name',
                'asc'
            );
            $content_type_project->order_by('publish_start_time', 'asc');
            $content_type_project->get();
            $header_items = [];
            if ($content_type_project->result_count() > 0) {
                foreach ($content_type_project->all as $project) {
                    if (!$condensed) {
                        $header_items[] = [
                            'type'  => 'task_set',
                            'id'    => $project->id,
                            'name'  => $this->lang->get_overlay_with_default(
                                'task_sets',
                                $project->id,
                                'name',
                                $project->name
                            ),
                            'title' => '',
                        ];
                    }
                    $projects_ids[] = $project->id;
                }
                
            }
            $table_data['header']['content_type_project'] = [
                'content_type_name' => $this->lang->line(
                    'admin_solutions_valuation_tables_header_content_type_project'
                ),
                'items'             => $header_items,
            ];
            
            foreach ($students as $student) {
                $student_line = [
                    'fullname'               => $student->fullname,
                    'email'                  => $student->email,
                    'id'                     => $student->id,
                    'total_points'           => 0,
                    'task_sets_points'       => [],
                    'task_sets_points_total' => 0,
                    'projects_points'        => [],
                    'projects_points_total'  => 0,
                ];
                
                $solutions_data = [];
                
                if ($content_type_task_set->result_count() > 0 || $content_type_project->result_count() > 0) {
                    $solutions = new Solution();
                    $solutions->select('id');
                    $solutions->select('task_set_id, points, tests_points, not_considered, revalidate');
                    $solutions->where_related_student($student);
                    $solutions->group_start();
                    if (count($task_sets_ids) > 0) {
                        $solutions->or_where_in('task_set_id', $task_sets_ids);
                    }
                    if (count($projects_ids) > 0) {
                        $solutions->or_where_in('task_set_id', $projects_ids);
                    }
                    $solutions->group_end();
                    $solutions->get_iterated();
                    foreach ($solutions as $solution) {
                        $solutions_data[$solution->task_set_id] = [
                            'points'         => is_null($solution->points) && is_null($solution->tests_points)
                                ? null
                                : ($solution->points + $solution->tests_points),
                            'not_considered' => $solution->not_considered,
                            'revalidate'     => $solution->revalidate,
                            'solution_id'    => $solution->id,
                            'task_set_id'    => $solution->task_set_id,
                        ];
                    }
                }
                
                $task_sets_points_array = [];
                if ($content_type_task_set->result_count() > 0) {
                    $task_sets_points = 0;
                    $last_task_set_type_id = null;
                    $last_task_set_type_key = null;
                    
                    $max_points = 0;
                    foreach ($content_type_task_set->all as $task_set) {
                        if ($last_task_set_type_id !== $task_set->task_set_type_id) {
                            if ($last_task_set_type_id !== null) {
                                $query = $this->db->query("SELECT course_task_set_type_rel.min_points AS 'min_points'," .
                                    "course_task_set_type_rel.min_points_in_percentage AS 'percentage' " .
                                    "FROM course_task_set_type_rel WHERE course_task_set_type_rel.course_id=" . $course->id .
                                    " AND course_task_set_type_rel.task_set_type_id=" . $last_task_set_type_id);
                                $row = $query->first_row('array');
                                if (isset($row) && isset($row['min_points']) && trim($row['min_points']) != '' &&
                                    isset($row['percentage']) && trim($row['percentage']) != '') {
                                    $min_points = $row['min_points'];
                                    if ($row['percentage'] == 1) {
                                      $min_points = $min_points * $max_points / 100;
                                    }
                                    if ($task_sets_points_array[$last_task_set_type_key]['points'] >= $min_points) {
                                        $task_sets_points_array[$last_task_set_type_key]['type'] = 'task_set_type_completed';
                                    }
                                }
                            }
                            $last_task_set_type_id = $task_set->task_set_type_id;
                            $task_sets_points_array[] = [
                                'type'   => 'task_set_type',
                                'points' => 0,
                                'flag'   => 'ok',
                            ];
                            $last_task_set_type_key = count($task_sets_points_array) - 1;
                            $max_points = 0;
                        }
                        $max_points += $task_set->total_points;
                        $task_sets_points_array[$last_task_set_type_key]['max_points'] = $max_points;
                        $points = 0;
                        
                        $points_included_in_total_score = 0;
                        $included = true;
                        $db_query = $this->db->query("select course_task_set_type_rel.include_in_total as 'included' " .
                            "from course_task_set_type_rel where course_task_set_type_rel.course_id=" . $course->id .
                            " and course_task_set_type_rel.task_set_type_id=" . $last_task_set_type_id);
                        $result = $db_query->first_row('array');
                        if (isset($result) && isset($result['included']) && trim($result['included']) != ''){
                            if($result['included'] == 0) {
                                $included = false;
                            }
                        }
                        
                        if (isset($solutions_data[$task_set->id])) {
                            if ($solutions_data[$task_set->id]['not_considered']) {
                                if (!$condensed) {
                                    $task_sets_points_array[] = [
                                        'type'        => 'task_set',
                                        'points'      => '*',
                                        'flag'        => 'notConsidered',
                                        'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                        'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                    ];
                                }
                            } else {
                                if (is_null($solutions_data[$task_set->id]['points'])) {
                                    if (!$condensed) {
                                        $task_sets_points_array[] = [
                                            'type'        => 'task_set',
                                            'points'      => '!',
                                            'flag'        => 'revalidate',
                                            'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                            'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                        ];
                                    }
                                } else if ($solutions_data[$task_set->id]['revalidate']) {
                                    if (!$condensed) {
                                        $task_sets_points_array[] = [
                                            'type'        => 'task_set',
                                            'points'      => $solutions_data[$task_set->id]['points'],
                                            'flag'        => 'revalidate',
                                            'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                            'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                        ];
                                    }
                                    $points = (float)$solutions_data[$task_set->id]['points'];
                                    if($included){
                                        $points_included_in_total_score = (float)$solutions_data[$task_set->id]['points'];
                                    }
                                } else {
                                    if (!$condensed) {
                                        $task_sets_points_array[] = [
                                            'type'        => 'task_set',
                                            'points'      => $solutions_data[$task_set->id]['points'],
                                            'flag'        => 'ok',
                                            'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                            'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                        ];
                                    }
                                    $points = (float)$solutions_data[$task_set->id]['points'];
                                    if($included){
                                        $points_included_in_total_score = (float)$solutions_data[$task_set->id]['points'];
                                    }
                                }
                            }
                        } else if (!$condensed) {
                            if (!is_null($task_sets_data[$task_set->id]['group_id'][0])
                                && !in_array(
                                    $student->participant_group_id,
                                    $task_sets_data[$task_set->id]['group_id'],
                                    true
                                )
                            ) {
                                $task_sets_points_array[] = [
                                    'type'   => 'task_set',
                                    'points' => '-',
                                    'flag'   => 'notInGroup',
                                ];
                            } else {
                                $task_sets_points_array[] = [
                                    'type'   => 'task_set',
                                    'points' => 'x',
                                    'flag'   => 'notSubmitted',
                                ];
                            }
                        }
                        $task_sets_points += $points;
                        $task_sets_points_array[$last_task_set_type_key]['points'] += $points;
                        $student_line['total_points'] += $points_included_in_total_score;
                        $student_line['task_sets_points_total'] = $task_sets_points;
                    }
                    $query = $this->db->query("SELECT course_task_set_type_rel.min_points AS 'min_points'," .
                        "course_task_set_type_rel.min_points_in_percentage AS 'percentage' " .
                        "FROM course_task_set_type_rel where course_task_set_type_rel.course_id=" . $course->id .
                        " AND course_task_set_type_rel.task_set_type_id=" . $last_task_set_type_id);
                    $row = $query->first_row('array');
                    if (isset($row) && isset($row['min_points']) && trim($row['min_points']) != ''
                        && isset($row['percentage']) && trim($row['percentage']) != '') {
                        $min_points = $row['min_points'];
                        if ($row['percentage'] == 1) {
                            $min_points = $min_points * $max_points /100;
                        }
                        if ($task_sets_points_array[$last_task_set_type_key]['points'] >= $min_points) {
                            $task_sets_points_array[$last_task_set_type_key]['type'] = 'task_set_type_completed';
                        }
                        $task_sets_points_array[$last_task_set_type_key]['max_points'] = $max_points;
                    }
                }
                $student_line['task_sets_points'] = $task_sets_points_array;
                
                $task_sets_points_array = [];
                if ($content_type_project->result_count() > 0) {
                    $task_sets_points = 0;
                    foreach ($content_type_project as $task_set) {
                        $points = 0;
                        if (isset($solutions_data[$task_set->id])) {
                            if ($solutions_data[$task_set->id]['not_considered']) {
                                if (!$condensed) {
                                    $task_sets_points_array[] = [
                                        'type'        => 'task_set',
                                        'points'      => '*',
                                        'flag'        => 'notConsidered',
                                        'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                        'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                    ];
                                }
                            } else if (is_null($solutions_data[$task_set->id]['points'])) {
                                if (!$condensed) {
                                    $task_sets_points_array[] = [
                                        'type'        => 'task_set',
                                        'points'      => '!',
                                        'flag'        => 'revalidate',
                                        'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                        'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                    ];
                                }
                            } else if ($solutions_data[$task_set->id]['revalidate']) {
                                if (!$condensed) {
                                    $task_sets_points_array[] = [
                                        'type'        => 'task_set',
                                        'points'      => $solutions_data[$task_set->id]['points'],
                                        'flag'        => 'revalidate',
                                        'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                        'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                    ];
                                }
                                $points = (float)$solutions_data[$task_set->id]['points'];
                            } else {
                                if (!$condensed) {
                                    $task_sets_points_array[] = [
                                        'type'        => 'task_set',
                                        'points'      => $solutions_data[$task_set->id]['points'],
                                        'flag'        => 'ok',
                                        'task_set_id' => $solutions_data[$task_set->id]['task_set_id'],
                                        'solution_id' => $solutions_data[$task_set->id]['solution_id'],
                                    ];
                                }
                                $points = (float)$solutions_data[$task_set->id]['points'];
                            }
                        } else {
                            if (!$condensed) {
                                $task_sets_points_array[] = [
                                    'type'   => 'task_set',
                                    'points' => 'x',
                                    'flag'   => 'notSubmitted',
                                ];
                            }
                        }
                        $task_sets_points += $points;
                        $student_line['total_points'] += $points;
                        $student_line['projects_points_total'] = $task_sets_points;
                    }
                }
                $student_line['projects_points'] = $task_sets_points_array;
                
                $table_data['content'][] = $student_line;
            }
        }
        
        //echo '<pre>' . htmlspecialchars(print_r($table_data, true)) . '</pre>';
        //die();
        
        return $table_data;
    }
}
