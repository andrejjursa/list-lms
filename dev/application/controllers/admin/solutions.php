<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Solutions controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Solutions extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_solutions_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('solutions');
        $this->inject_stored_filter();
        $this->inject_courses();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/list.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/index.tpl');
    }
    
    public function solutions_list($task_set_id = NULL) {
        $this->_select_teacher_menu_pagetag('solutions');
        $task_set = new Task_set();
        $task_set->select('`task_sets`.*');
        $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id`)', 'task_set_total_points');
        $task_set->include_related('course', 'name');
        $task_set->include_related('course/period', 'name');
        $task_set->include_related('group', 'name');
        $task_set->get_by_id($task_set_id);
        
        $this->inject_students($task_set_id);
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
                
                $solution->where($task_set);
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
        $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id`)', 'task_set_total_points');
        $task_set->get_by_id($task_set_id);
        $this->inject_students($task_set_id);
        $this->parser->parse('backend/solutions/new_solution_form.tpl', array('task_set' => $task_set));
    }
    
    public function valuation($task_set_id, $solution_id) {
        $solution = new Solution();
        $solution->select('`solutions`.*');
        $solution->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `task_sets`.`id`)', 'task_set_total_points');
        $solution->include_related('task_set', '*', TRUE, TRUE);
        $solution->include_related('task_set/course', 'name');
        $solution->include_related('task_set/course/period', 'name');
        $solution->include_related('task_set/group', 'name');
        $solution->include_related('student', array('fullname', 'email'));
        $solution->include_related('teacher', array('fullname', 'email'));
        $solution->where('student_id IS NOT NULL');
        $solution->where('task_set_id', $task_set_id);
        $solution->get_by_id($solution_id);
        
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_solutions/valuation.js');
        $this->parser->add_css_file('admin_solutions.css');
        $this->parser->parse('backend/solutions/valuation.tpl', array('solution' => $solution));
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
                $solution->from_array($solution_data, array('points', 'comment'));
                if (is_null($solution->teacher_id)) { $solution->teacher_id = $this->usermanager->get_teacher_id(); }
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
            redirect(create_internal_url('admin_solutions/valuation/' . $task_set_id . '/' . $solution_id));
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
                $this->config->load('geshi');
                $highlight_extensions = $this->config->item('file_extension_highlight');
                if (isset($highlight_extensions[$output['extension']])) {
                    include(APPPATH . 'third_party/geshi/geshi.php');
                    $geshi = new GeSHi($output['content'], $highlight_extensions[$output['extension']]);
                    $geshi->set_header_type(GESHI_HEADER_PRE_VALID);
                    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
                    $this->output->set_output($geshi->parse_code());
                } else {
                    $this->output->set_output('<pre>' . htmlspecialchars($output['content']) . '</pre>');
                }
            } else {
                $this->output->set_output($this->lang->line('admin_solutions_valuation_file_content_error_cant_read_file'));
            }
        } else {
            $this->output->set_output($this->lang->line('admin_solutions_valuation_file_content_error_task_set_not_found'));
        }
    }

    public function get_task_set_list() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $task_sets = new Task_set();
        $task_sets->select('`task_sets`.*, `course_course_task_set_type_rel`.`upload_solution` AS `join_upload_solution`');
        $task_sets->include_related_count('solution');
        $task_sets->include_related_count('task');
        $task_sets->include_related('course', 'name');
        $task_sets->include_related('course/period', 'name');
        $task_sets->include_related('group', 'name');
        $task_sets->include_related('task_set_type', 'name');
        $task_sets->include_related('course/task_set_type');
        $task_sets->where('(`course_task_set_types`.`id` = `task_sets`.`task_set_type_id`)');
        if (isset($filter['course']) && intval($filter['course']) > 0) {
            $task_sets->where_related_course('id', intval($filter['course']));
        }
        if (isset($filter['group']) && intval($filter['group']) > 0) {
            $task_sets->where_related_group('id', intval($filter['group']));
        }
        $task_sets->order_by_related('course/period', 'sorting', 'asc');
        $task_sets->order_by_related_with_constant('course', 'name', 'asc');
        $task_sets->order_by_with_overlay('name', 'asc');
        $task_sets->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), array('name'));
        $this->parser->parse('backend/solutions/task_set_list', array('task_sets' => $task_sets));
    }
    
    public function get_solutions_list_for_task_set($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $solutions = new Solution();
        if ($task_set->exists()) {
            $solutions->where_related($task_set);
            $solutions->include_related('student');
            $solutions->include_related('teacher');
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
            '' => ''
        );
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $this->parser->parse('backend/solutions/groups_from_course.tpl', array('groups' => $options, 'selected' => $selected_id));
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
}