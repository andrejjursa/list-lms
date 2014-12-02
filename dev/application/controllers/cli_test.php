<?php

class Cli_test extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            show_error('This controller can be called only from CLI!');
            die();
        }
        $this->load->database();
    }
    
    public function index($worker_id = 0) {
        $test_locks_path = rtrim($this->config->item('test_worker_locking_directory'),'/\\') . DIRECTORY_SEPARATOR;
        if (file_exists($test_locks_path . 'worker_' . (int)$worker_id . '_lock.txt')) {
            die();
        }
        $f = fopen($test_locks_path . 'worker_' . (int)$worker_id . '_lock.txt', 'w');
        fclose($f);
        $test_queue = new Test_queue();
        try {
            $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $this->db->trans_begin();
            $test_queue->where('worker', NULL);
            $test_queue->where('status', 0);
            $test_queue->where('version >', 0);
            $test_queue->group_start(' NOT ');
            $test_queue->where('task_set_id', NULL);
            $test_queue->group_end();
            $test_queue->group_start(' NOT ');
            $test_queue->where('student_id', NULL);
            $test_queue->group_end();
            $test_queue->order_by('priority', 'asc');
            $test_queue->order_by('start', 'asc');
            $test_queue->limit(1);
            $sql_query = $test_queue->get_sql();
            $sql_query = rtrim($sql_query, '; ' . "\n\r") . ' FOR UPDATE';
            $test_queue->query($sql_query);
            if ($test_queue->exists()) {
                $test_queue->worker = (int)$worker_id;
                $test_queue->status = 1;
                $test_queue->exec_start = date('Y-m-d H:i:s');
                $test_queue->where('worker', NULL);
                $test_queue->where('status', 0);
                if ($test_queue->save()) {
                    $this->db->trans_commit();
                } else {
                    $this->db->trans_rollback();
                }
            } else {
                $this->db->trans_rollback();
            }
        } catch (Exception $e) {
        }
        if ($test_queue->exists()) {
            $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $this->db->trans_begin();
            $this->lang->reinitialize_for_idiom($test_queue->system_language);
            $this->lang->load('admin/tests');
            $task_set = new Task_set();
            $task_set->include_related('course', 'test_scoring_deadline');
            $task_set->get_by_id($test_queue->task_set_id);
            $student = new Student();
            $student->get_by_id($test_queue->student_id);
            
            $tests = new Test();
            $tests->where_related($test_queue);
            $tests->get_iterated();
            
            if ($task_set->exists() && $student->exists() && $tests->exists()) {
                $version = $test_queue->version;
                $run_evaluation = $task_set->enable_tests_scoring > 0 && $task_set->course_test_scoring_deadline >= date('Y-m-d H:i:s') ? TRUE : FALSE;
                $score_percent = array();
                $score_points = array();
                $bonus_percent = array();
                $bonus_points = array();
                foreach ($tests as $test) {
                    $files = $task_set->get_student_files($student->id, (int)$version);
                    if (isset($files[(int)$version]['filepath']) && file_exists($files[(int)$version]['filepath'])) {
                        $test_object = $this->load->test($test->type);
                        $test_object->initialize($test);
                        $token = '';
                        echo 'Test queue ' . $test_queue->id . ' is running test ' . $test->id . ' ... ' . PHP_EOL;
                        try {
                            $test_output = $test_object->run($files[(int)$version]['filepath'], $run_evaluation && $test->enable_scoring > 0, $student->id, $token);
                            $test_score = $test_object->get_last_test_score();
                        } catch (Exception $e) {
                            $test_output = $e->message;
                            $test_score = 0;
                        }
                        $test_queue->set_join_field($test, 'result_text', $test_output);
                        
                        echo 'Test queue ' . $test_queue->id . ' is done with test ' . $test->id . ' ... ' . PHP_EOL;
                        
                        $this->db->select('*');
                        $task_id = $test->task_id;
                        $this->db->where('task_set_id', $task_set->id);
                        $this->db->where('task_id', (int)$task_id);
                        $query = $this->db->get('task_task_set_rel');
                        if ($query->num_rows() > 0) {
                            $task_rel = $query->row_object();
                            $min = (double)$task_rel->test_min_points;
                            $max = (double)$task_rel->test_max_points;
                            $percent = (double)$test_score / 100.0;
                            $points = (1.0 - $percent) * $min + $percent * $max;
                            if ($task_rel->bonus_task == 0) {
                                $score_percent[$task_id] = isset($score_percent[$task_id]) ? $score_percent[$task_id] + $percent : $percent;
                                $score_points[$task_id] = isset($score_points[$task_id]) ? $score_points[$task_id] + $points : $points;
                                $test_queue->set_join_field($test, 'percent_points', $test_score);
                                $test_queue->set_join_field($test, 'points', $points);
                            } else {
                                $bonus_percent[$task_id] = isset($bonus_percent[$task_id]) ? $bonus_percent[$task_id] + $percent : $percent;
                                $bonus_points[$task_id] = isset($bonus_points[$task_id]) ? $bonus_points[$task_id] + $points : $points;
                                $test_queue->set_join_field($test, 'percent_bonus', $test_score);
                                $test_queue->set_join_field($test, 'bonus', $points);
                            }
                        }
                        $query->free_result();
                    } else {
                        $this->db->trans_rollback();
                        $test_queue->worker = NULL;
                        $test_queue->status = 3;
                        $test_queue->finish = date('Y-m-d H:i:s');
                        $test_queue->save();
                        @unlink($test_locks_path . 'worker_' . (int)$worker_id . '_lock.txt');
                        die();
                    }
                }
                $tests = new Test();
                $tests->where_related('task/task_set', 'id', $task_set->id);
                $tests->where('type', $test_queue->test_type);
                $tests->where('enable_scoring >', 0);
                $tests->group_by('task_id');
                $tests->where('task_task_task_set_rel.bonus_task', 0);
                $tests->get_iterated();
                $test_count = $tests->result_count();
                
                $min_results = $task_set->test_min_needed > $test_count ? $test_count : $task_set->test_min_needed;
                
                $course = new Course();
                $course->where_related_task_set('id', $task_set->id);
                $course->get();
                
                $min_points_limit = -$course->default_points_to_remove;
                
                if ($test_count > 0) {
                    $max_results = $task_set->test_max_allowed < count($score_points) ? $task_set->test_max_allowed : count($score_points);
                    
                    arsort($score_points, SORT_NUMERIC);
                    $i = 0;
                    $total_score = 0;
                    foreach($score_points as $task_id => $points) {
                        if ($i < $max_results) {
                            $total_score += $points;
                            $i++;
                        } else {
                            break;
                        }
                    }
                    
                    $total_score = $total_score < $min_points_limit ? $min_points_limit : $total_score;
                    
                    $total_bonus = array_sum($bonus_points);
                    $total_score += $total_bonus;
                    
                    if (count($score_points) >= $min_results) {
                        $solution = new Solution();
                        $solution->where('task_set_id', $task_set->id);
                        $solution->where('student_id', $student->id);
                        $solution->get();
                        
                        $save_solution = FALSE;
                        $solution_not_considered = FALSE;
                        
                        if ($solution->exists()) {
                            if ($solution->not_considered == 0) {
                                if ($solution->points < $total_score || is_null($solution->points)) {
                                    $solution->points = $total_score;
                                    $solution->comment = '';
                                    $solution->teacher_id = NULL;
                                    $solution->best_version = (int)$version;
                                    $solution->revalidate = 0;
                                    $save_solution = TRUE;
                                }
                            } else {
                                $solution_not_considered = TRUE;
                            }
                        } else {
                            $solution->points = $total_score;
                            $solution->comment = '';
                            $solution->teacher_id = NULL;
                            $solution->best_version = (int)$version;
                            $solution->task_set_id = $task_set->id;
                            $solution->student_id = $student->id;
                            $solution->revalidate = 0;
                            $save_solution = TRUE;
                        }
                        
                        if ($save_solution) {
                            $solution->save();
                            $this->_action_success();
                            $this->output->set_internal_value('student_id', $student->id);
                            $test_queue->points = $total_score - $total_bonus;
                            $test_queue->bonus = $total_bonus;
                        }
                    }
                    $test_queue->worker = NULL;
                    $test_queue->status = 2;
                    $test_queue->finish = date('Y-m-d H:i:s');
                    $test_queue->save();
                    $this->db->trans_commit();
                }
            } else {
                $test_queue->worker = NULL;
                $test_queue->status = 3;
                $test_queue->finish = date('Y-m-d H:i:s');
                $test_queue->save();
            }
            //$test_queue->worker = NULL;
            //$test_queue->status = 0;
            //$test_queue->save();
        }
        @unlink($test_locks_path . 'worker_' . (int)$worker_id . '_lock.txt');
    }
    
}