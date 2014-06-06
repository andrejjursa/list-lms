<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Logs controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Logs extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_logs_filter_data';
    
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
        $this->_select_teacher_menu_pagetag('logs');
        $this->parser->add_js_file('admin_logs/list.js');
        $this->parser->add_css_file('admin_logs.css');
        $this->inject_stored_filter();
        $this->parser->parse('backend/logs/index.tpl');
    }
    
    public function all_logs() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $logs = new Log();
        $logs->include_related('student');
        $logs->include_related('teacher');
        if (isset($filter['type']) && $filter['type'] > 0) {
            $logs->where('log_type', $filter['type']);
        }
        $logs->order_by('created', 'desc');
        $logs->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->parser->parse('backend/logs/all_logs.tpl', array('logs' => $logs));
    }
    
    public function details($log_id) {
        $log = new Log();
        $log->include_related('student');
        $log->include_related('teacher');
        $log->get_by_id($log_id);
        
        if ($log->exists()) {
            if ($log->log_type == Log::LOG_TYPE_STUDENT_SOLUTION_UPLOAD || $log->log_type == Log::LOG_TYPE_TEACHER_SOLUTION_UPLOAD) {
                $solution = new Solution();
                $solution->include_related('student');
                $solution->include_related('task_set');
                $solution->include_related('task_set/course');
                $solution->include_related('task_set/course/period');
                $solution->get_by_id((int)$log->affected_row_primary_id);
                $this->parser->assign('solution', $solution);
            } elseif ($log->log_type == Log::LOG_TYPE_STUDENT_SOLUTION_DOWNLOAD) {
                $additional_data = unserialize($log->additional_data);
                $task_set = new Task_set();
                $task_set->include_related('course');
                $task_set->include_related('course/period');
                $task_set->get_by_id(@$additional_data['task_set_id']);
                $this->parser->assign(array(
                    'task_set' => $task_set,
                    'filename' => @$additional_data['solution_file'],
                ));
            }
        }
        
        $this->parser->add_css_file('admin_logs.css');
        $this->parser->parse('backend/logs/details.tpl', array('log' => $log));
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