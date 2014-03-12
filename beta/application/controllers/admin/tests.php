<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tests controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Tests extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile('tests');
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        if ($this->router->method != 'run_test_for_task' && $this->router->method != 'request_token' && $this->router->method != 'evaluate_test_result') {
            $this->usermanager->teacher_login_protected_redirect();
        }
    }
    
    public function new_test_form($task_id) {
        $this->load->helper('tests');
        $tests = get_all_supported_test_types_and_subtypes();
        $task = new Task();
        $task->get_by_id($task_id);
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_tests/new_test_form.js');
        $this->parser->parse('backend/tests/new_test_form.tpl', array(
            'test_types' => $tests['types'],
            'test_subtypes' => $tests['subtypes'],
            'task' => $task,
        ));
    }
    
    public function prepare_new_test($task_id) {
        $task = new Task();
        $task->get_by_id($task_id);
        if (!$task->exists()) {
            $this->new_test_form($task_id);
        } else {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('test[name]', 'lang:admin_tests_test_form_field_name', 'required');
            $this->form_validation->set_rules('test[type]', 'lang:admin_tests_test_form_field_type', 'required');
            $this->form_validation->set_rules('test[subtype]', 'lang:admin_tests_test_form_field_subtype', 'required');
            
            if ($this->form_validation->run()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $test_data = $this->input->post('test');
                $test = new Test();
                $test->name = trim($test_data['name']);
                $test->type = $test_data['type'];
                $test->subtype = $test_data['subtype'];
                $test->enabled = 0;
                $test->configuration = serialize(array());
                if ($test->save($task) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    @mkdir('private/uploads/unit_tests/test_' . $test->id, DIR_READ_MODE);
                    $this->messages->add_message('lang:admin_tests_flash_message_new_test_saved', Messages::MESSAGE_TYPE_SUCCESS);
                    $this->_action_success();
                    redirect(create_internal_url('admin_tests/configure_test/' . $test->id));
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_tests_flash_message_new_test_failed', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('admin_tests/new_test_form/' . $task->id));
                }
            } else {
                $this->new_test_form($task_id);
            }
        }
    }
    
    public function configure_test($test_id) {
        $test = new Test();
        $test->include_related('task');
        $test->get_by_id(intval($test_id));
        $error_message = NULL;
        $test_config_view = '';
        $configuration = array();
        if ($test->exists()) {
            $configuration = @unserialize($test->configuration);
            if ($configuration === FALSE) { $configuration = array(); }
            $test_class = $this->load->test($test->type);
            try {
                $test_class->initialize($test);
                $test_config_view = $test_class->get_configure_view();
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
            $this->lang->load_all_overlays('tests', $test->id);
        } else {
            $error_message = 'lang:admin_tests_error_cant_find_test';
        }
        $this->_add_tinymce4();
        $this->parser->add_js_file('admin_tests/configure_test.js');
        $this->parser->parse('backend/tests/configure_test.tpl', array(
            'test' => $test,
            'test_config_view' => $test_config_view,
            'configuration' => $configuration,
            'error_message' => $error_message,
            'languages' => $this->lang->get_list_of_languages(),
        ));
    }
    
    public function save_test_configuration($test_id) {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $test = new Test();
        $test->get_by_id(intval($test_id));
        if ($test->exists()) {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('test[name]', 'lang:admin_tests_test_form_field_name', 'required');
            $this->form_validation->set_rules('test[timeout]', 'lang:admin_tests_test_form_field_timeout', 'required|greater_than_equal[100]');
            
            $valid = TRUE;
            
            $test_object = $this->load->test($test->type);
            try {
                $test_object->initialize($test);
                $valid = $test_object->validate_test_configuration();
            } catch (TestException $e) {
                $this->db->trans_rollback();
                $this->messages->add_message($e->getMessage(), Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('admin_tests/configure_test/' . $test_id));
                die();
            }            
            if ($this->form_validation->run() && $valid) {
                $test_data = $this->input->post('test');
                $test->name = $test_data['name'];
                $test->timeout = (int)$test_data['timeout'];
                $test->enabled = isset($test_data['enabled']) ? 1 : 0;
                $test->enable_scoring = isset($test_data['enable_scoring']) ? 1 : 0;
                $test->instructions = isset($test_data['instructions']) ? remove_base_url($test_data['instructions']) : '';
                $can_save = TRUE;
                try {
                    $config_data = is_array($this->input->post('configuration')) ? $this->input->post('configuration') : array();
                    $upload_data = array();
                    $can_save = $test_object->handle_uploads($upload_data);
                    $config_data = array_merge($config_data, $upload_data);
                    $test->configuration = serialize($test_object->prepare_test_configuration($config_data));
                } catch (TestException $e) {
                    $this->db->trans_rollback();
                    $this->messages->add_message($e->getMessage(), Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('admin_tests/configure_test/' . $test_id));
                    die();
                }
                if ($can_save) {
                    $overlay = $this->input->post('overlay');
                    if ($test->save() && $this->lang->save_overlay_array(remove_base_url_from_overlay_array($overlay, 'instructions')) && $this->db->trans_status()) {
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:admin_tests_flash_message_configuration_saved', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->_action_success();
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:admin_tests_flash_message_configuration_save_failed', Messages::MESSAGE_TYPE_SUCCESS);
                    }
                    redirect(create_internal_url('admin_tests/configure_test/' . $test_id));
                } else {
                    $this->db->trans_rollback();
                    $this->configure_test($test_id);
                }
            } else {
                $this->db->trans_rollback();
                $this->configure_test($test_id);
            }
        } else {
            $this->db->trans_rollback();
            redirect(create_internal_url('admin_tests/configure_test/' . $test_id));
        }
    }

    public function all_tests($task_id) {
        $this->load->helper('tests');
        $tests_data = get_all_supported_test_types_and_subtypes();
        $task = new Task();
        $task->get_by_id($task_id);
        $tests = new Test();
        if ($task->exists()) {
            $tests->where_related($task);
            $tests->order_by('type', 'asc');
            $tests->order_by('subtype', 'asc');
            $tests->get_iterated();
        }
        $this->parser->parse('backend/tests/all_tests.tpl', array(
            'test_types' => $tests_data['types'],
            'test_subtypes' => $tests_data['subtypes'],
            'task' => $task,
            'tests' => $tests,
        ));
    }
    
    public function delete_test($test_id) {
        $output = new stdClass();
        $output->result = FALSE;
        $output->message = $this->lang->line('admin_tests_error_cant_find_test');
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $test = new Test();
        $test->get_by_id(intval($test_id));
        if ($test->exists()) {
            $test->delete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $output->result = TRUE;
                $output->message = $this->lang->line('admin_tests_delete_test_success');
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_tests_error_cant_delete_test');
            }
        } else {
            $this->db->trans_rollback();
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function prepare_execution($test_id) {
        $test = new Test();
        $test->include_related('task');
        $test->get_by_id(intval($test_id));
        $this->parser->parse('backend/tests/prepare_execution.tpl', array('test' => $test));
    }
    
    public function run_testing_execution($test_id) {
        $test = new Test();
        $test->include_related('task');
        $test->get_by_id(intval($test_id));
        $file_name = '';
        if ($test->exists()) {
            $path = 'private/test_to_execute/testing_execution/';
            if (!file_exists($path)) {
                @mkdir($path, DIR_READ_MODE);
            }
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'zip';
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if ($this->upload->do_upload('source_codes')) {
                $data = $this->upload->data();
                $file_name = $data['file_name'];
            } else {
                $this->parser->assign('source_codes_error', $this->upload->display_errors());
                $this->prepare_execution($test_id);
                return;
            }
        }
        $this->parser->add_js_file('admin_tests/run_testing_execution.js');
        $this->parser->parse('backend/tests/run_testing_execution.tpl', array('test' => $test, 'file_name' => $file_name));
    }
    
    public function after_testing_execution($source_file) {
        $source_file_decoded = decode_from_url($source_file);
        $path = 'private/test_to_execute/testing_execution/' . $source_file_decoded;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public function run_test_for_task($test_id, $task_set_id, $student_id, $version, $token = '') {
        $task_set = new Task_set();
        $task_set->include_related('course', 'test_scoring_deadline');
        $task_set->get_by_id(intval($task_set_id));
        $student = new Student();
        $student->get_by_id(intval($student_id));
        $output = new stdClass();
        $output->text = $this->lang->line('admin_tests_error_message_failed_to_run_student_test');
        $output->code = 1;
        if ($task_set->exists() && $student->exists()) {
            $files = $task_set->get_student_files($student->id, (int)$version);
            if (isset($files[(int)$version]['filepath'])) {
                $run_evaluation = $task_set->enable_tests_scoring > 0 && $task_set->course_test_scoring_deadline >= date('Y-m-d H:i:s') ? TRUE : FALSE;
                $this->run_single_test($test_id, encode_for_url($files[(int)$version]['filepath']), $run_evaluation, $token, $student->id);
            } else {
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode($output));
            }
        } else {
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($output));
        }
    }

    public function run_single_test($test_id, $source_file, $evaluation = FALSE, $student_id = NULL, $token = '') {
        $output = new stdClass();
        $output->text = '';
        $output->code = 0;
        try {
            $test = new Test();
            $test->get_by_id(intval($test_id));
            if ($test->exists()) {
                $test_object = $this->load->test($test->type);
                $test_object->initialize($test);
                $output->text = $test_object->run(decode_from_url($source_file), (bool)(int)$evaluation && strlen($token) > 0, $student_id, $token);
            }
        } catch (Exception $e) {
            $output->text = $e->getMessage();
            $output->code = $e->getCode();
        } 
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function download_test_file($test_id, $file_name_encrypted) {
        $path = 'private/uploads/unit_tests/test_' . (int)$test_id . '/' . decode_from_url($file_name_encrypted);
        if (file_exists($path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $path);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename='.basename($path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush();
            $f = fopen($path, 'r');
            while (!feof($f)) {
                echo fread($f, 1024);
            }
            fclose($f);
            exit;
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    public function request_token() {
        $this->load->model('test_score');
        
        $this->output->set_content_type('application/json');
        
        $this->test_score->delete_old_scores();
        
        $token = $this->test_score->request_token();
        
        $this->output->set_output(json_encode($token));
    }
    
    public function evaluate_test_result($task_set_id, $student_id, $version, $test_type, $token) {
        $task_set = new Task_set();
        $task_set->include_related('course', 'test_scoring_deadline');
        $task_set->get_by_id((int)$task_set_id);
        
        $student = new Student();
        $student->get_by_id((int)$student_id);
        
        $output = new stdClass();
        $output->result = FALSE;
        $output->message = '';
        $output->points_new = 0;
        $output->points_before = 0;
        
        $this->load->model('test_score');
        
        if ($task_set->exists() && $student->exists()) {
            if ($task_set->course_test_scoring_deadline >= date('Y-m-d H:i:s') && $task_set->enable_tests_scoring > 0) {
                $results = $this->test_score->get_data_for_student($student->id, $token, $test_type);
                $this->_transaction_isolation();
                $this->db->trans_start();
                
                $tests = new Test();
                $tests->where_related('task/task_set', 'id', $task_set->id);
                $tests->where('type', $test_type);
                $tests->where('enable_scoring >', 0);
                $tests->group_by('task_id');
                $tests->where('task_task_task_set_rel.bonus_task', 0);
                $tests->get_iterated();
                //$output->debug = $tests->check_last_query(array('', ''), TRUE);
                $test_count = $tests->result_count();
                
                $min_results = $task_set->test_min_needed > $test_count ? $test_count : $task_set->test_min_needed;
                
                $course = new Course();
                $course->where_related_task_set('id', $task_set->id);
                $course->get();
                
                $min_points_limit = -$course->default_points_to_remove;
                                
                if ($test_count > 0) {
                    $total_score = 0;
                    $score_array = array();
                    $bonus_tasks_array = array();
                    $score_percentage = array();
                    $bonus_tasks_percentage = array();
                    if (count($results)) {
                        foreach($results as $task_id => $score) {
                            $this->db->select('*');
                            $this->db->where('task_set_id', $task_set->id);
                            $this->db->where('task_id', (int)$task_id);
                            $query = $this->db->get('task_task_set_rel');

                            if ($query->num_rows() > 0) {
                                $task_rel = $query->row_object();
                                $min = $task_rel->test_min_points;
                                $max = $task_rel->test_max_points;
                                $diff = abs($max - $min);
                                $score_percent = (double)$score / 100;
                                $sub_score = round(10 * ($min + $diff * $score_percent)) / 10;
                                if ($task_rel->bonus_task == 0) {
                                    $score_array[$task_id] = $sub_score;
                                    $score_percentage[$task_id] = $score;
                                } else {
                                    $bonus_tasks_array[$task_id] = $sub_score;
                                    $bonus_tasks_percentage[$task_id] = $score;
                                }
                            }

                            $query->free_result();
                        }
                    }
                    
                    $max_results = $task_set->test_max_allowed < count($score_array) ? $task_set->test_max_allowed : count($score_array);

                    arsort($score_array, SORT_NUMERIC);
                    $i = 0;
                    foreach($score_array as $task_id => $points) {
                        if ($i < $max_results) {
                            $total_score += $points;
                            $i++;
                        } else {
                            break;
                        }
                    }

                    $total_score = $total_score < $min_points_limit ? $min_points_limit : $total_score;

                    arsort($bonus_tasks_array, SORT_NUMERIC);
                    
                    $total_score += array_sum($bonus_tasks_array);
                        
                    if (count($score_array) >= $min_results) {
                        $tasks = new Task();
                        $tasks->where_related_task_set('id', $task_set_id);
                        $tasks->order_by('`task_task_set_rel`.`sorting`', 'asc');
                        $tasks->get_iterated();
                        //$output->debug = $tasks->check_last_query(array('', ''), TRUE);
                        
                        $output->evaluation = $this->parser->parse('backend/tests/evaluation_table.tpl', array(
                            'tasks' => $tasks,
                            'real_points' => $score_array,
                            'bonus_points' => $bonus_tasks_array,
                            'real_percentage' => $score_percentage,
                            'bonus_percentage' => $bonus_tasks_percentage,
                            'max_results' => $max_results,
                        ), TRUE);
                        
                        $solution = new Solution();
                        $solution->where('task_set_id', $task_set->id);
                        $solution->where('student_id', $student->id);
                        $solution->get();

                        $save_solution = FALSE;

                        $output->points_new = $total_score;

                        if ($solution->exists()) {
                            $output->points_before = $solution->points;
                            if ($solution->points < $total_score || is_null($solution->points)) {
                                $solution->points = $total_score;
                                $solution->comment = '';
                                $solution->teacher_id = NULL;
                                $solution->best_version = (int)$version;
                                $solution->revalidate = 0;
                                $save_solution = TRUE;
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
                            $output->result = TRUE;
                            $this->_action_success();
                        } else {
                            $output->message = sprintf($this->lang->line('admin_tests_test_result_nothing_to_update'), $output->points_new, $output->points_before);
                        }
                    } else {
                        $output->message = sprintf($this->lang->line('admin_tests_test_result_minimum_number_of_test_not_selected'), $min_results);
                    }
                } else {
                    $output->message = $this->lang->line('admin_tests_test_result_no_evaluationg_tests');
                }
                $this->db->trans_complete();
            } else {
                $output->message = $this->lang->line('admin_tests_test_result_disabled');
            }
        } else {
            $output->message = $this->lang->line('admin_tests_test_result_input_error');
        }
        
        $this->test_score->delete_token($token);
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
}