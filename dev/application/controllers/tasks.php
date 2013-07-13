<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->_initialize_student_menu();
    }

    public function index() {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_select_student_menu_pagetag('tasks');
        
        $task_set = $this->get_task_sets($course, $group, $student);
        if ($course->exists()) {
            $task_set_types = $course->task_set_type->order_by_with_constant('name', 'asc')->get_iterated();
            $this->parser->assign('task_set_types', $task_set_types);
            
            $task_sets = $this->filter_valid_task_sets($task_set);
            $this->lang->init_overlays('task_sets', $task_sets, array('name'));
            $this->parser->assign('task_sets', $task_sets);
            
            $points = $this->compute_points($task_sets, $student);
            $this->parser->assign('points', $points);
        }
        
        $this->parser->add_css_file('frontend_tasks.css');
        
        $this->parser->parse('frontend\tasks\index.tpl', array('course' => $course));
    }
    
    public function task($task_set_id = NULL) {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_select_student_menu_pagetag('tasks');
        
        $task_set = $this->get_task_set_by_id($course, $group, $student, $task_set_id);
        if ($course->exists()) {
            $task_sets = $this->filter_valid_task_sets($task_set);
            $this->lang->init_overlays('task_sets', $task_sets, array('name'));
            $filtered_task_set = count($task_sets) == 1 ? $task_sets[0] : new Task_set();
            if ($filtered_task_set->exists()) {
                $this->parser->assign('task_set', $filtered_task_set);
                $this->parser->assign('task_set_can_upload', $this->can_upload_file($filtered_task_set, $course));
                $this->parser->assign('solution_files', $filtered_task_set->get_student_files($student->id));
                $this->parser->assign('max_filesize', compute_size_with_unit(intval($this->config->item('maximum_solition_filesize') * 1024)));
            } else {
                $this->messages->add_message('lang:tasks_task_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('tasks/index'));
            }
        }
                
        $this->parser->add_css_file('frontend_tasks.css');
        $this->parser->add_js_file('tasks\task.js');
        
        $this->parser->parse('frontend\tasks\task.tpl', array('course' => $course));
    }
    
    public function upload_solution($task_set_id = 0) {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = $this->get_task_set_by_id($course, $group, $student, $task_set_id);
        $task_sets = $this->filter_valid_task_sets($task_set);
        $filtered_task_set = count($task_sets) == 1 ? $task_sets[0] : new Task_set();
        if ($filtered_task_set->id == intval($task_set_id) && $this->can_upload_file($filtered_task_set, $course)) {
            $config['upload_path'] = 'private/uploads/solutions/task_set_' . intval($task_set_id) . '/';
            $config['allowed_types'] = 'zip';
            $config['max_size'] = intval($this->config->item('maximum_solition_filesize'));
            $config['file_name'] = $student->id . '_' . $this->normalize_student_name($student) . '_' . substr(md5(time() . rand(-500000, 500000)), 0, 4) . '_' . $filtered_task_set->get_student_file_next_version($student->id) . '.zip';
            @mkdir($config['upload_path']);
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('file')) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $solution = new Solution();
                $solution->where('task_set_id', $filtered_task_set->id);
                $solution->where('student_id', $student->id);
                $solution->get();
                if ($solution->exists()) {
                    $solution->revalidate = 1;
                    $solution->save();
                } else {
                    $solution = new Solution();
                    $solution->revalidate = 1;
                    $solution->save(array(
                        'student' => $student, 
                        'task_set' => $filtered_task_set,
                    ));
                }
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:tasks_task_solution_uploaded', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    @unlink($config['upload_path'] . $config['file_name']);
                    $this->messages->add_message('lang:tasks_task_solution_canceled_due_db_error', Messages::MESSAGE_TYPE_ERROR);
                }
                redirect(create_internal_url('tasks/task/' . intval($task_set_id)));
            } else {
                $this->parser->assign('file_error_message', $this->upload->display_errors('', ''));
                $this->task($task_set_id);
            }
        } else {
            $this->messages->add_message('lang:tasks_task_error_cant_upload_solution', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('tasks/task/' . intval($task_set_id)));
        }
    }
    
    public function download_solution($task_set_id, $file) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        if ($task_set->exists()) {
            $filename = decode_from_url($file);
            $file_info = $task_set->get_specific_file_info($filename);
            if ($file_info !== FALSE) {
                $filename = $file_info['file_name'] . '_' . $file_info['version'] . '.zip';
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.$filename);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_info['filepath']));
                ob_clean();
                flush();
                readfile($file_info['filepath']);
                exit;
            }
        }
        $this->output->set_status_header(404, 'Not found');
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
    
    public function show_comments($task_id) {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_id));
        
        if ($task_set->exists()) {
            $comments = new Comment();
            $comments->where_related_task_set($task_set);
            $comments->where('reply_at_id', NULL);
            $comments->get();
        }
        
        $this->parser->parse('frontend/tasks/show_comments.tpl', array('comments' =>  $comments));
    }

    private function normalize_student_name($student) {
        $normalized = normalize($student->fullname);
        $output = '';
        for($i = 0; $i < mb_strlen($normalized); $i++) {
            $char = mb_substr($normalized, $i, 1);
            if (preg_match('/^[a-zA-Z]$/', $char)) {
                $output .= $char;
            }
        }
        return $output;
    }
        
    private function get_task_sets(&$course, &$group, &$student) {
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related('active_for_student', 'id', $student->id);
        $course->where_related('participant', 'student_id', $student->id);
        $course->where_related('participant', 'allowed', 1);
        $course->include_related('period', 'name');
        $course->get();
        
        $task_set = new Task_set();
        $group = new Group();
        
        if ($course->exists()) {
            $group->where_related_participant('student_id', $student->id);
            $group->where_related_participant('course_id', $course->id);
            $group->get();

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
        }
        
        return $task_set;
    }
    
    private function get_task_set_by_id(&$course, &$group, &$student, $task_set_id) {
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $group = new Group();
        
        $task_set = new Task_set();
        $task_set->where('published', 1);
        $task_set->get_by_id($task_set_id);
        
        if ($task_set->exists()) {
            $course->where_related_participant('student_id', $student->id);
            $course->where_related_participant('allowed', 1);
            $course->get_by_id($task_set->course_id);
            if (!$course->exists()) {
                return new Task_set();
            }
            
            if (!is_null($task_set->group_id)) {
                $group->where_related_participant('student_id', $student->id);
                $group->where_related_participant('course_id', $course->id);
                $group->get_by_id($task_set->group_id);
                if (!$group->exists()) {
                    return new Task_set();
                }
            }
        }
        
        return $task_set;
    }

    private function can_upload_file($task_set, $course) {
        if ($task_set->exists() && $course->exists()) {
            $task_set_type = $course->task_set_type->where('id', $task_set->task_set_type_id)->include_join_fields()->get();
            if ($task_set_type->exists() && $task_set_type->join_upload_solution == 1) {
                if (is_null($task_set->upload_end_time)) { return TRUE; }
                if (strtotime($task_set->upload_end_time) > time()) { return TRUE; }
            }
        }
        return FALSE;
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
    
    private function compute_points($i_task_sets, Student $student) {
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