<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->usermanager->student_login_protected_redirect();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->_initialize_student_menu();
    }

    public function index() {
        $this->_select_student_menu_pagetag('tasks');
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related('active_for_student', 'id', $student->id);
        $course->where_related('participant', 'student_id', $student->id);
        $course->where_related('participant', 'allowed', 1);
        $course->include_related('period', 'name');
        $course->get();
        
        if ($course->exists()) {
            $group = new Group();
            $group->where_related_participant('student_id', $student->id);
            $group->where_related_participant('course_id', $course->id);
            $group->get();
            $this->parser->assign('group', $group);

            $task_set_types = $course->task_set_type->order_by_with_constant('name', 'asc')->get_iterated();
            $this->parser->assign('task_set_types', $task_set_types);
            
            $task_set = new Task_set();
            $task_set->where('published', 1);
            $task_set->where_related_course($course);
            $task_set->group_start();
                $task_set->or_where('group_id', NULL);
                $task_set->or_where('group_id', $group->id);
            $task_set->group_end();
            $task_set->include_related('room', '*', TRUE, TRUE);
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id`)', 'total_points');
            $task_set->order_by_related_with_constant('task_set_type', 'name', 'asc');
            $task_set->order_by_with_overlay('name', 'asc');
            $task_set->get();
            $task_sets = $this->filter_valid_task_sets($task_set);
            $this->lang->init_overlays('task_sets', $task_sets, array('name'));
            $this->parser->assign('task_sets', $task_sets);
            
            $points = $this->compute_points($task_sets, $student);
            $this->parser->assign('points', $points);
        }
        
        $this->parser->add_css_file('frontend_tasks.css');
        
        $this->parser->parse('frontend\tasks\index.tpl', array('course' => $course));
    }
    
    public function download_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/' . $filename;
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            readfile($filepath);
            exit;
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    public function download_hidden_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/hidden/' . $filename;
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            readfile($filepath);
            exit;
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    private function filter_valid_task_sets(Task_set $task_sets) {
        $output = array();
        
        $days = array(1=> 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        
        foreach($task_sets->all as $task_set) {
            $add = TRUE;
            if (!is_null($task_set->publish_start_time)) {
                if (!is_null($task_set->room->id)) {
                    if (strtotime($task_set->publish_start_time) > time()) {
                        $add = FALSE;
                    } else {
                        $current_day = intval(strftime('%w', strtotime($task_set->publish_start_time)));
                        $current_day > 0 ? $current_day : 7;
                        if ($task_set->room->time_day == $current_day) {
                            list($year, $month, $day) = explode(',', strftime('%Y,%m,%d', strtotime($task_set->publish_start_time)));
                            $time = mktime(0, 0, 0, intval($month), intval($day), intval($year)) + intval($task_set->room->time_begin);
                        } else {
                            $time = strtotime('next ' . $days[$task_set->room->time_day], strtotime($task_set->publish_start_time)) + intval($task_set->room->time_begin);
                        }
                        if ($time > time()) {
                            $add = FALSE;
                        }
                    }
                } else {
                    if (strtotime($task_set->publish_start_time) > time()) {
                        $add = FALSE;
                    }
                }
            }
            if ($add) {
                $output[] = $task_set;
            }
        }
        
        return $output;
    }
    
    public function compute_points($i_task_sets, Student $student) {
        $task_sets = is_array($i_task_sets) ? $i_task_sets : (is_object($i_task_sets) && $i_task_sets instanceof Task_set ? $i_task_sets->all : array());
        
        $ids = array(0);
        
        if (count($task_sets) > 0) { foreach($task_sets as $task_set) {
            $ids[] = $task_set->id;
        }}
        
        $solutions = $student->solution->where_in_related('task_set', 'id', $ids)->get_iterated();
        
        $points = array();
        
        foreach ($solutions as $solution) {
            $points[$solution->task_set_id] = $solution->points;
        }
        
        $output = array(
            'total' => 0,
            'max' => 0,
        );
        
        if (count($task_sets) > 0) { foreach($task_sets as $task_set) {
            $output['total'] += (isset($points[$task_set->id]) ? $points[$task_set->id] : 0);
            $output['max'] += $task_set->total_points;
            $output[$task_set->task_set_type_id]['total'] = (isset($output[$task_set->task_set_type_id]['total']) ? $output[$task_set->task_set_type_id]['total'] : 0) + (isset($points[$task_set->id]) ? $points[$task_set->id] : 0);
            $output[$task_set->task_set_type_id]['max'] = (isset($output[$task_set->task_set_type_id]['max']) ? $output[$task_set->task_set_type_id]['max'] : 0) + $task_set->total_points;
        }}
        
        return $output;
    }
    
}