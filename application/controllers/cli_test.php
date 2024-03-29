<?php

/**
 * @property CI_Input $input
 * @property LIST_Loader $load
 * @property CI_DB $db
 * @property LIST_Lang $lang
 * @property LIST_Parser $parser
 * @property CI_Config $config
 */
class Cli_test extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            show_error('This controller can be called only from CLI!');
            die();
        }
        $this->load->database();
    }
    
    public function index($worker_id = 0): void
    {
        $test_queue = new Test_queue();
        $execute_tests = false;
        try {
            $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $this->db->trans_begin();
            $test_queue->where('worker', null);
            $test_queue->where('status', 0);
            $test_queue->where('version >', 0);
            $test_queue->group_start(' NOT ');
            $test_queue->where('task_set_id', null);
            $test_queue->group_end();
            $test_queue->group_start(' NOT ');
            $test_queue->where('student_id', null);
            $test_queue->group_end();
            $test_queue->order_by('priority', 'asc');
            $test_queue->order_by('start', 'asc');
            $test_queue->limit(1);
            $sql_query = $test_queue->get_sql();
            $sql_query = rtrim($sql_query, '; ' . "\n\r") . ' FOR UPDATE;';
            $test_queue->query($sql_query);
            if ($test_queue->exists()) {
                $test_queue->worker = (int)$worker_id;
                $test_queue->status = 1;
                $test_queue->exec_start = date('Y-m-d H:i:s');
                $test_queue->where('worker', null);
                $test_queue->where('status', 0);
                if ($test_queue->save()) {
                    $this->db->trans_commit();
                    $execute_tests = true;
                } else {
                    $this->db->trans_rollback();
                }
            } else {
                $this->db->trans_rollback();
            }
        } catch (Exception $e) {
        }
        if ($test_queue->exists() && $execute_tests) {
            
            //$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
            //$this->db->trans_begin();
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
            
            try {
                if ($task_set->exists() && $student->exists() && $tests->exists()) {
                    $version = $test_queue->version;
                    $run_evaluation = $task_set->enable_tests_scoring > 0
                    && $task_set->course_test_scoring_deadline >= date('Y-m-d H:i:s');
                    $score_percent = [];
                    $score_points = [];
                    $bonus_percent = [];
                    $bonus_points = [];
                    $total_tests_count = $tests->result_count();
                    foreach ($tests as $test) {
                        $test_queue->single_test_exec_start = date('Y-m-d H:i:s');
                        $test_queue->save();
                        $files = $task_set->get_student_files($student->id, (int)$version);
                        if (isset($files[(int)$version]['filepath'])
                            && file_exists($files[(int)$version]['filepath'])
                        ) {
                            $test_object = $this->load->test($test->type);
                            $test_object->initialize($test);
                            $token = '';
                            try {
                                $test_output = $test_object->run(
                                    $files[(int)$version]['filepath'],
                                    $run_evaluation && $test->enable_scoring > 0,
                                    $student->id,
                                    $token
                                );
                                $test_score = $test_object->get_last_test_score();
                            } catch (Exception $e) {
                                $test_output = $e->getMessage();
                                $test_score = 0;
                            }
                            $test_queue->set_join_field($test, 'result_text', $test_output);
                            $test_queue->set_join_field($test,
                                'evaluation_table',
                                $test_object->get_last_test_scoring()
                            );
                            $test_queue->set_join_field($test, 'result', $test_object->get_last_exit_code());
                            
                            if ($run_evaluation && $test->enable_scoring > 0) {
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
                                        $test_queue->set_join_field($test, 'percent_points', $test_score);
                                        $test_queue->set_join_field($test, 'points', $points);
                                        $score_percent[$task_id] = isset($score_percent[$task_id])
                                            ? $score_percent[$task_id] + $percent
                                            : $percent;
                                        $percent = (double)$score_percent[$task_id];
                                        $points = (1.0 - $percent) * $min + $percent * $max;
                                        $score_points[$task_id] = $points;
                                    } else {
                                        $test_queue->set_join_field($test, 'percent_bonus', $test_score);
                                        $test_queue->set_join_field($test, 'bonus', $points);
                                        $bonus_percent[$task_id] = isset($bonus_percent[$task_id])
                                            ? $bonus_percent[$task_id] + $percent
                                            : $percent;
                                        $percent = (double)$bonus_percent[$task_id];
                                        $points = (1.0 - $percent) * $min + $percent * $max;
                                        $bonus_points[$task_id] = $points;
                                    }
                                }
                                $query->free_result();
                            }
                        } else {
                            //$this->db->trans_rollback();
                            $test_queue->worker = null;
                            $test_queue->status = 3;
                            $test_queue->finish = date('Y-m-d H:i:s');
                            $test_queue->save();
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
                    
                    if ($test_count > 0 && $run_evaluation) {
                        $max_results = $task_set->test_max_allowed < count($score_points)
                            ? $task_set->test_max_allowed
                            : count($score_points);
                        
                        arsort($score_points, SORT_NUMERIC);
                        $i = 0;
                        $total_score = 0;
                        foreach ($score_points as $task_id => $points) {
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
                            
                            $save_solution = false;
                            $solution_not_considered = false;
                            $solution_disable_evaluation = false;
                            
                            $best_old_score = $min_points_limit;
                            
                            if ($solution->exists()) {
                                if ($solution->not_considered == 0) {
                                    if ($solution->disable_evaluation_by_tests == 0) {
                                        if ($solution->tests_points < $total_score
                                            || is_null($solution->tests_points)
                                        ) {
                                            $solution->tests_points = $total_score;
                                            $solution->teacher_id = null;
                                            $solution->best_version = (int)$version;
                                            $solution->revalidate = 0;
                                            $save_solution = true;
                                        } else {
                                            $best_old_score = $solution->tests_points;
                                        }
                                    } else {
                                        $solution_disable_evaluation = true;
                                    }
                                } else {
                                    $solution_not_considered = true;
                                }
                            } else {
                                $solution->tests_points = $total_score;
                                $solution->comment = '';
                                $solution->teacher_id = null;
                                $solution->best_version = (int)$version;
                                $solution->task_set_id = $task_set->id;
                                $solution->student_id = $student->id;
                                $solution->revalidate = 0;
                                $save_solution = true;
                            }
                            
                            if ($save_solution) {
                                $solution->save();
                                $this->parser->clearCache('frontend/tasks/index.tpl');
                                $test_queue->result_message = $this->lang->line(
                                    'admin_tests_test_result_new_points_added'
                                );
                            } else {
                                if (!$solution_disable_evaluation) {
                                    if (!$solution_not_considered) {
                                        $test_queue->result_message = sprintf(
                                            $this->lang->line('admin_tests_test_result_nothing_to_update'),
                                            $total_score,
                                            $best_old_score
                                        );
                                    } else {
                                        $test_queue->result_message = $this->lang->line(
                                            'admin_tests_test_result_solution_not_considered'
                                        );
                                    }
                                } else {
                                    $test_queue->result_message = $this->lang->line(
                                        'admin_tests_test_result_solution_disable_evaluation'
                                    );
                                }
                            }
                            $test_queue->points = $total_score - $total_bonus;
                            $test_queue->bonus = $total_bonus;
                        } else {
                            $test_queue->result_message = sprintf(
                                $this->lang->line('admin_tests_test_result_minimum_number_of_test_not_selected'),
                                $min_results
                            );
                        }
                        
                        $result_table_tasks = new Task();
                        $result_table_tasks->where_related_task_set('id', $task_set->id);
                        $result_table_tasks->order_by('`task_task_set_rel`.`sorting`', 'asc');
                        $result_table_tasks->get_iterated();
                        $test_queue->result_html = $this->parser->parse('backend/tests/evaluation_table.tpl', [
                            'tasks'            => $result_table_tasks,
                            'real_points'      => $score_points,
                            'bonus_points'     => $bonus_points,
                            'real_percentage'  => $score_percent,
                            'bonus_percentage' => $bonus_percent,
                            'max_results'      => $max_results,
                        ], true);
                        $test_queue->worker = null;
                        $test_queue->status = 2;
                        $test_queue->finish = date('Y-m-d H:i:s');
                        $test_queue->save();
                        //$this->db->trans_commit();
                    } else if ($total_tests_count && !$run_evaluation) {
                        $test_queue->worker = null;
                        $test_queue->status = 2;
                        $test_queue->finish = date('Y-m-d H:i:s');
                        $test_queue->result_message = $this->lang->line(
                            'admin_tests_test_result_testing_finished'
                        );
                        $test_queue->save();
                        //$this->db->trans_commit();
                    } else {
                        //$this->db->trans_rollback();
                        $test_queue->worker = null;
                        $test_queue->status = 3;
                        $test_queue->finish = date('Y-m-d H:i:s');
                        $test_queue->result_message = $this->lang->line(
                            'admin_tests_test_result_no_test_selected'
                        );
                        $test_queue->save();
                    }
                } else {
                    //$this->db->trans_rollback();
                    $test_queue->worker = null;
                    $test_queue->status = 3;
                    $test_queue->finish = date('Y-m-d H:i:s');
                    $test_queue->result_message = $this->lang->line('admin_tests_test_result_configuration_error');
                    $test_queue->save();
                }
            } catch (Exception $e) {
                //$this->db->trans_rollback();
                $test_queue->worker = null;
                $test_queue->status = 3;
                $test_queue->finish = date('Y-m-d H:i:s');
                $test_queue->result_message = $this->lang->line('admin_tests_test_result_execution_error');
                $test_queue->result_html = '<pre>' . $e->getMessage() . '</pre>';
                $test_queue->save();
            }
        }
        
        //@unlink($test_locks_path . 'worker_' . (int)$worker_id . '_lock.txt');
    }
    
    public function aging(): void
    {
        $max_ticks = (int)$this->config->item('test_aging_ticks_to_priority_increase');
        $max_raising = (int)$this->config->item('test_aging_max_tests_to_raise_priority');
        
        $max_lifetime = (int)$this->config->item('test_queue_done_error_lifetime');
        $max_lifetime = $max_lifetime < 60 ? 60 : $max_lifetime;
        
        $old_test_queue = new Test_queue();
        $old_test_queue->where('status >=', 2);
        $old_test_queue->where('finish + INTERVAL ' . $max_lifetime . ' MINUTE < NOW()', null, true);
        $old_test_queue->get();
        $old_test_queue->delete_all();
        
        if ($max_ticks < 10) {
            $max_ticks = 10;
        }
        if ($max_raising <= 0) {
            $max_raising = 1;
        }
        
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        
        $tests_queue = new Test_queue();
        $tests_queue->distinct();
        $tests_queue->select('priority');
        $tests_queue->order_by('priority', 'asc');
        $tests_queue->where('status', 0);
        $tests_queue->where('worker', null);
        $tests_queue->get_iterated();
        
        $priorities = [];
        
        foreach ($tests_queue as $test_queue) {
            $priorities[] = (int)$test_queue->priority;
        }
        
        if (count($priorities) >= 2) {
            unset($priorities[0]);
            foreach ($priorities as $priority) {
                if ($priority <= 1) {
                    continue;
                }
                $tests_with_max_ticks = new Test_queue();
                $tests_with_max_ticks->where('age >=', $max_ticks);
                $tests_with_max_ticks->where('status', 0);
                $tests_with_max_ticks->where('worker', null);
                $tests_with_max_ticks->where('priority', $priority);
                $tests_with_max_ticks->order_by('start', 'asc');
                $tests_with_max_ticks->limit($max_raising);
                $tests_with_max_ticks->get_iterated();
                if ($tests_with_max_ticks->exists()) {
                    foreach ($tests_with_max_ticks as $test_with_max_ticks) {
                        $test_with_max_ticks->priority = $priority - 1;
                        $test_with_max_ticks->age = 0;
                        $test_with_max_ticks->save();
                    }
                    $tests_queue_with_priority = new Test_queue();
                    $tests_queue_with_priority->where('status', 0);
                    $tests_queue_with_priority->where('worker', null);
                    $tests_queue_with_priority->where('priority', $priority);
                    $tests_queue_with_priority->update('age', '0', false);
                    continue;
                }
                
                $tests_queue_with_priority = new Test_queue();
                $tests_queue_with_priority->where('status', 0);
                $tests_queue_with_priority->where('worker', null);
                $tests_queue_with_priority->where('priority', $priority);
                $tests_queue_with_priority->update('age', 'age + 1', false);
            }
        }
        
        $this->db->trans_complete();
    }
    
    public function reset_all(): void
    {
        echo 'Resetting old tests that may be frozen.' . PHP_EOL;
        
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        
        $test_queue = new Test_queue();
        $test_queue->where('status', 1);
        $test_queue->get_iterated();
        
        if ($test_queue->result_count() > 0) {
            echo 'Found ' . $test_queue->result_count() . ' old tests.' . PHP_EOL;
            foreach ($test_queue as $single_test) {
                $tests = $single_test->test->get_iterated();
                foreach ($tests as $test) {
                    $set = [
                        'result'           => 0,
                        'result_text'      => null,
                        'percent_points'   => 0,
                        'percent_bonus'    => 0,
                        'points'           => 0,
                        'bonus'            => 0,
                        'evaluation_table' => null,
                    ];
                    $this->db->set($set);
                    $this->db->where('test_id', $test->id);
                    $this->db->where('test_queue_id', $single_test->id);
                    $this->db->update('test_test_queue_rel');
                }
                $single_test->status = 0;
                $single_test->worker = null;
                $single_test->save();
            }
            echo 'All old tests were reset.' . PHP_EOL;
        } else {
            echo 'Nothing found.' . PHP_EOL;
        }
        
        $this->db->trans_complete();
    }
    
}
