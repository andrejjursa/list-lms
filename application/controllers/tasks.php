<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Tasks controller for frontend.
 *
 * @package LIST_FE_Controllers
 * @author  Andrej Jursa
 */
class Tasks extends LIST_Controller
{
    
    protected $filter_next_task_set_publication_min_cache_lifetime;
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
    }
    
    public function index(): void
    {
        $this->usermanager->student_login_protected_redirect();
        $this->load->helper('task_sets');
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('tasks');
        $this->parser->add_css_file('frontend_tasks.css');
        $this->parser->add_js_file('tasks/list.js');
        $data = $this->input->post();
        $showAllTaskSets = false;
        if (is_array($data) && array_key_exists('show_all_task_sets', $data) && (int)$data['show_all_task_sets'] === 1) {
            $showAllTaskSets = true;
        }
        $this->parser->assign('showAllTaskSets', $showAllTaskSets);
        $cache_id = $this->usermanager->get_student_cache_id() . '|' . ($showAllTaskSets ? 'show_all' : 'show_future');
        if ($this->_is_cache_enabled()) {
            $this->smarty->caching = Smarty::CACHING_LIFETIME_SAVED;
        }
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/tasks/index.tpl'), $cache_id)) {
            $task_set = $this->get_task_sets($course, $group, $student);
            if ($course->exists()) {
                $task_set_types = $course->task_set_type->order_by_with_constant('name', 'asc')->get_iterated();
                $this->parser->assign('task_set_types', $task_set_types);
                
                $task_sets = $this->filter_valid_task_sets($task_set);
                if ($this->_is_cache_enabled() && $this->filter_next_task_set_publication_min_cache_lifetime > 0 && $this->filter_next_task_set_publication_min_cache_lifetime <= $this->smarty->cache_lifetime) {
                    $this->smarty->setCacheLifetime($this->filter_next_task_set_publication_min_cache_lifetime + 1);
                    $this->parser->setCacheLifetimeForTemplateObject('frontend/tasks/index.tpl', $this->filter_next_task_set_publication_min_cache_lifetime + 1);
                }
                $this->lang->init_overlays('task_sets', $task_sets, ['name']);
                $this->parser->assign('task_sets', $task_sets);
                
                $points = $this->compute_points($task_sets, $student);
                $this->parser->assign('points', $points);
            }
            $this->parser->assign(['course' => $course]);
            
            $projects = new Task_set();
            if ($course->exists()) {
                $projects->where('content_type', 'project');
                $projects->include_related('solution');
                $projects->add_join_condition('`solutions`.`student_id` = ?', [$this->usermanager->get_student_id()]);
                $projects->include_related('course');
                $projects->include_related('course/period');
                $projects->where('published', 1);
                $projects->group_start();
                $projects->where('publish_start_time <=', date('Y-m-d H:i:s'));
                $projects->or_where('publish_start_time', null);
                $projects->group_end();
                $projects->where_related('course', $course);
                $projects->order_by('publish_start_time');
                $projects->get_iterated();
            }
            $this->parser->assign('projects', $projects);
        }
        $this->parser->parse('frontend/tasks/index.tpl', [], false, $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : false, $cache_id);
    }
    
    public function task($task_set_id_url = null): void
    {
        $this->_add_mathjax();
        $task_set_id = url_get_id($task_set_id_url);
        $this->usermanager->student_login_protected_redirect();
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('tasks');
        $this->parser->add_css_file('frontend_tasks.css');
        $this->parser->add_js_file('tasks/task.js');
        $this->_add_prettify();
        $this->_add_scrollTo();
        $this->_add_jquery_countdown();
        $this->parser->assign('max_filesize', compute_size_with_unit((int)($this->config->item('maximum_solition_filesize') * 1024)));
        $cache_id = $this->usermanager->get_student_cache_id('task_set_' . $task_set_id);
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/tasks/task.tpl'), $cache_id)) {
            $task_set = $this->get_task_set_by_id($course, $group, $student, $task_set_id);
            if ($course->exists()) {
                $task_sets = $this->filter_valid_task_sets($task_set);
                $this->lang->init_overlays('task_sets', $task_sets, ['name']);
                $filtered_task_set = count($task_sets) === 1 ? $task_sets[0] : new Task_set();
                if ($filtered_task_set->exists()) {
                    $this->load->helper('tests');
                    $test_types_subtypes = get_all_supported_test_types_and_subtypes();
                    $this->lang->init_overlays('task_sets', $filtered_task_set, ['name', 'instructions']);
                    $solution_versions = new Solution_version();
                    $solution_versions->where_related('solution/task_set', 'id', $task_set_id);
                    $solution_versions->where_related('solution', 'student_id', $this->usermanager->get_student_id());
                    $query = $solution_versions->get_raw();
                    $versions_metadata = [];
                    if ($query->num_rows()) {
                        foreach ($query->result() as $row) {
                            $versions_metadata[$row->version] = clone $row;
                        }
                    }
                    $query->free_result();
                    $this->parser->assign('task_set', $filtered_task_set);
                    $this->parser->assign('task_set_can_upload', $this->can_upload_file($filtered_task_set, $course));
                    $this->parser->assign('solution_files', $filtered_task_set->get_student_files($student->id));
                    $this->parser->assign('test_types', $test_types_subtypes['types']);
                    $this->parser->assign('test_subtypes', $test_types_subtypes['subtypes']);
                    $this->parser->assign('versions_metadata', $versions_metadata);
                } else {
                    $this->messages->add_message('lang:tasks_task_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('tasks/index'));
                }
            }
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse('frontend/tasks/task.tpl', [], false, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function test_result($test_queue_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        $this->parser->add_css_file('frontend_tasks.css');
        
        $test_queue = new Test_queue();
        $test_queue->where_related('student', 'id', $this->usermanager->get_student_id());
        $test_queue->include_related('task_set');
        $test_queue->include_related('task_set/course');
        $test_queue->include_related('task_set/course/period');
        $test_queue->get_by_id((int)$test_queue_id);
        
        $tasks = new Task();
        if ($test_queue->exists()) {
            $tasks->distinct();
            $tasks->where_related('task_set', 'id', $test_queue->task_set_id);
            $tasks->order_by('task_task_set_rel.sorting', 'asc');
            $tasks->get_iterated();
            
            $tests = $test_queue->test->include_join_fields()->order_by('id', 'asc')->get_iterated();
            
            $tests_per_task = [];
            
            $overlays_tests = [];
            
            foreach ($tests as $test) {
                $test_line = [
                    'id'               => $test->id,
                    'name'             => $test->name,
                    'task_id'          => $test->task_id,
                    'result'           => $test->join_result,
                    'result_text'      => $test->join_result_text,
                    'percent_points'   => $test->join_percent_points,
                    'percent_bonus'    => $test->join_percent_bonus,
                    'points'           => $test->join_points,
                    'bonus'            => $test->join_bonus,
                    'evaluation_table' => is_null($test->join_evaluation_table) ? [] : unserialize($test->join_evaluation_table),
                ];
                $overlays_tests[] = $test->id;
                
                $tests_per_task[$test->task_id][] = $test_line;
            }
            
            $this->lang->init_overlays('tests', $overlays_tests, ['name']);
            
            $this->parser->assign('tests_per_task', $tests_per_task);
        }
        
        $this->load->helper('tests');
        
        $test_types = get_all_supported_test_types();
        
        $this->parser->parse('frontend/tasks/test_result.tpl', [
            'test_queue' => $test_queue,
            'tasks'      => $tasks,
            'test_types' => $test_types,
        ]);
    }
    
    public function reset_task_cache($task_set_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        $this->_action_success();
        $this->output->set_internal_value('task_set_id', $task_set_id);
    }
    
    public function upload_solution($task_set_id = 0): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = $this->get_task_set_by_id($course, $group, $student, $task_set_id);
        $task_sets = $this->filter_valid_task_sets($task_set);
        $filtered_task_set = count($task_sets) === 1 ? $task_sets[0] : new Task_set();
        if ($filtered_task_set->id === (int)$task_set_id && $this->can_upload_file($filtered_task_set, $course)) {
            $allowed_file_types_array = trim($filtered_task_set->allowed_file_types) !== '' ? array_map('trim', explode(',', $filtered_task_set->allowed_file_types)) : [];
            $config['upload_path'] = 'private/uploads/solutions/task_set_' . (int)$task_set_id . '/';
            $config['allowed_types'] = 'zip' . (count($allowed_file_types_array) ? '|' . implode('|', $allowed_file_types_array) : '');
            $config['max_size'] = (int)$this->config->item('maximum_solition_filesize');
            $current_version = $filtered_task_set->get_student_file_next_version($student->id);
            $config['file_name'] = $student->id . '_' . $this->normalize_student_name($student) . '_' . substr(md5(time() . rand(-500000, 500000)), 0, 4) . '_' . $current_version . '.zip';
            @mkdir($config['upload_path'], DIR_READ_MODE, true);
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
                $mimes = $this->upload->mimes_types('zip');
                if ((is_string($mimes) && $upload_data['file_type'] !== $mimes) || (is_array($mimes) && !in_array($upload_data['file_type'], $mimes, true))) {
                    if (!$this->zip_plain_file_to_archive($upload_data['full_path'], $upload_data['client_name'], $upload_data['file_path'])) {
                        $this->messages->add_message('lang:tasks_task_error_cant_zip_file', Messages::MESSAGE_TYPE_ERROR);
                        redirect(create_internal_url('tasks/task/' . intval($task_set_id)));
                        die();
                    }
                }
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $solution = new Solution();
                $solution->where('task_set_id', $filtered_task_set->id);
                $solution->where('student_id', $student->id);
                $solution->get();
                $revalidate = 1;
                if ($filtered_task_set->enable_tests_scoring == 1 && $filtered_task_set->allowed_test_types !== '' && $course->test_scoring_deadline >= date('Y-m-d H:i:s')) {
                    $test_types = explode(',', $filtered_task_set->allowed_test_types);
                    $tests = new Test();
                    $tests->where_related('task/task_set', 'id', $filtered_task_set->id);
                    $tests->where('enabled', 1);
                    $tests->where('enable_scoring', 1);
                    $tests->where_in('type', $test_types);
                    $revalidate = $tests->count() > 0 ? 0 : 1;
                }
                if ($solution->exists()) {
                    $solution->ip_address = $_SERVER["REMOTE_ADDR"];
                    $solution->revalidate = $revalidate;
                    $solution->save();
                } else {
                    $solution = new Solution();
                    $solution->ip_address = $_SERVER["REMOTE_ADDR"];
                    $solution->revalidate = $revalidate;
                    $solution->save([
                        'student'  => $student,
                        'task_set' => $filtered_task_set,
                    ]);
                }
                $solution_version = new Solution_version();
                $solution_version->ip_address = $_SERVER["REMOTE_ADDR"];
                $solution_version->version = $current_version;
                
                $comment = $this->input->post('comment');
                if (trim($comment) !== '') {
                    $solution_version->comment = trim($comment);
                }
                
                $solution_version->save($solution);
                if ($this->db->trans_status()) {
                    $log = new Log();
                    $log->add_student_solution_upload_log(sprintf($this->lang->line('tasks_task_solution_upload_log_message'), $config['file_name']), $student, $solution->id);
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:tasks_task_solution_uploaded', Messages::MESSAGE_TYPE_SUCCESS);
                    $this->_action_success();
                    $this->output->set_internal_value('task_set_id', $solution->task_set_id);
                } else {
                    $this->db->trans_rollback();
                    @unlink($config['upload_path'] . $config['file_name']);
                    $this->messages->add_message('lang:tasks_task_solution_canceled_due_db_error', Messages::MESSAGE_TYPE_ERROR);
                }
                redirect(create_internal_url('tasks/task/' . (int)$task_set_id));
            } else {
                $this->parser->assign('file_error_message', $this->upload->display_errors('', ''));
                $this->task($task_set_id);
            }
        } else {
            $this->messages->add_message('lang:tasks_task_error_cant_upload_solution', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('tasks/task/' . (int)$task_set_id));
        }
    }
    
    public function download_solution($task_set_id, $file): void
    {
        if (($this->usermanager->is_student_session_valid() && !Restriction::check_restriction_for_ip_address())
            || $this->usermanager->is_teacher_session_valid()) {
            $task_set = new Task_set();
            $task_set->get_by_id((int)$task_set_id);
            if ($task_set->exists()) {
                $filename = decode_from_url($file);
                $file_info = $task_set->get_specific_file_info($filename);
                if ($file_info !== false) {
                    $allow_download = true;
                    if (!$this->usermanager->is_teacher_session_valid()) {
                        $solution_version = new Solution_version();
                        $solution_version->where('version', $file_info['version']);
                        $solution_version->where_related('solution/task_set', 'id', $task_set_id);
                        $solution_version->get();
                        if ($solution_version->exists()) {
                            if ((bool)$solution_version->download_lock) {
                                $allow_download = false;
                            }
                        }
                    }
                    if ($allow_download) {
                        $log = new Log();
                        if (!$this->usermanager->is_teacher_session_valid()) {
                            $log->add_student_solution_download_log($this->lang->line('tasks_log_message_student_solution_download'), $this->usermanager->get_student_id(), $filename, $task_set->id);
                        }
                        $filename = $file_info['file_name'] . '_' . $file_info['version'] . '.zip';
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime_type = finfo_file($finfo, $file_info['filepath']);
                        finfo_close($finfo);
                        header('Content-Description: File Transfer');
                        header('Content-Type: ' . $mime_type);
                        header('Content-Disposition: attachment; filename=' . $filename);
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($file_info['filepath']));
                        ob_clean();
                        flush();
                        $f = fopen($file_info['filepath'], 'r');
                        while (!feof($f)) {
                            echo fread($f, 1024);
                        }
                        fclose($f);
                        exit;
                    }
    
                    $this->parser->parse('frontend/tasks/download_solution.tpl', ['version_download_disabled' => true]);
                } else {
                    $this->output->set_status_header(404, 'Not found');
                }
            } else {
                $this->output->set_status_header(404, 'Not found');
            }
        } else {
            $this->parser->parse('frontend/tasks/download_solution.tpl');
        }
    }
    
    public function download_file($task_id, $file): void
    {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . (int)$task_id . '/' . $filename;
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename=' . basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            $f = fopen($filepath, 'r');
            while (!feof($f)) {
                echo fread($f, 1024);
            }
            fclose($f);
            exit;
        }
    
        $this->output->set_status_header(404, 'Not found');
    }
    
    public function download_hidden_file($task_id, $file): void
    {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . (int)$task_id . '/hidden/' . $filename;
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename=' . basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            $f = fopen($filepath, 'r');
            while (!feof($f)) {
                echo fread($f, 1024);
            }
            fclose($f);
            exit;
        }
    
        $this->output->set_status_header(404, 'Not found');
    }
    
    public function show_comments($task_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_id);
        $comments = [];
        
        if ($task_set->exists() && (bool)$task_set->comments_enabled) {
            $comments = Comment::get_comments_for_task_set($task_set);
        }
        
        $this->parser->parse('frontend/tasks/show_comments.tpl', ['comments' => $comments, 'task_set' => $task_set]);
    }
    
    public function subscribe_to_task_comments($task_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_id);
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        if ($task_set->exists() && $student->exists() && $student->save(['comment_subscription' => $task_set])) {
            $this->db->trans_commit();
            $this->messages->add_message('lang:tasks_comments_message_subscription_successful', Messages::MESSAGE_TYPE_SUCCESS);
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:tasks_comments_message_subscription_error', Messages::MESSAGE_TYPE_ERROR);
        }
        redirect(create_internal_url('tasks/show_comments/' . $task_id));
    }
    
    public function unsubscribe_to_task_comments($task_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_id);
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        if ($task_set->exists() && $student->exists() && $student->is_related_to('comment_subscription', $task_set->id)) {
            $student->delete_comment_subscription($task_set);
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:tasks_comments_message_subscription_cancel_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:tasks_comments_message_subscription_cancel_error', Messages::MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:tasks_comments_message_subscription_cancel_error', Messages::MESSAGE_TYPE_ERROR);
        }
        redirect(create_internal_url('tasks/show_comments/' . $task_id));
    }
    
    public function reply_at_comment($task_id, $comment_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_id);
        $comment = new Comment();
        if ($task_set->exists() && (bool)$task_set->comments_enabled) {
            $comment->include_related('student', '*', true, true);
            $comment->include_related('teacher', '*', true, true);
            $comment->get_by_id((int)$comment_id);
        }
        
        $this->parser->add_css_file('frontend_tasks.css');
        $this->parser->parse('frontend/tasks/reply_at_comment.tpl', ['task_set' => $task_set, 'comment' => $comment]);
    }
    
    public function post_comment($task_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $this->create_comment();
        
        redirect(create_internal_url('tasks/show_comments/' . $task_id));
    }
    
    public function post_comment_reply($task_id, $comment_id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $this->create_comment();
        
        redirect(create_internal_url('tasks/reply_at_comment/' . $task_id . '/' . $comment_id));
    }
    
    public function solution_version_comment($id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $solution_version = new Solution_version();
        $solution_version->include_related('solution/task_set', ['id', 'name']);
        $solution_version->include_related('solution/student', ['fullname']);
        $solution_version->get_by_id((int)$id);
        
        $this->parser->assign('solution_version', $solution_version);
        $this->parser->parse('frontend/tasks/solution_version_comment.tpl');
    }
    
    public function save_solution_version_comment($id): void
    {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $solution_version = new Solution_version();
        $solution_version->get_by_id((int)$id);
        
        if ($solution_version->exists()) {
            $comment = $this->input->post('comment');
            if (trim($comment) === '') {
                $solution_version->comment = null;
            } else {
                $solution_version->comment = trim($comment);
            }
            $solution_version->save();
            $this->db->trans_commit();
            $this->messages->add_message('lang:tasks_solution_version_comment_updated', Messages::MESSAGE_TYPE_SUCCESS);
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:tasks_solution_version_not_found', Messages::MESSAGE_TYPE_ERROR);
        }
        redirect(create_internal_url('tasks/solution_version_comment/' . (int)$id));
    }
    
    private function create_comment(): bool
    {
        $post_data = $this->input->post('comment');
        if (array_key_exists('text', $post_data) && array_key_exists('task_set_id', $post_data) && array_key_exists('reply_at_id', $post_data)) {
            $task_set = new Task_set();
            $task_set->get_by_id((int)$post_data['task_set_id']);
            $student = new Student();
            $student->get_by_id($this->usermanager->get_student_id());
            if ($task_set->exists() && $student->exists() && (bool)$task_set->comments_enabled) {
                if (trim(strip_tags($post_data['text'])) !== '') {
                    $text = strip_tags($post_data['text'], '<a><strong><em><span>');
                    $comment = new Comment();
                    $comment->text = $text;
                    $comment->approved = (bool)$task_set->comments_moderated ? 0 : 1;
                    $comment->reply_at_id = empty($post_data['reply_at_id']) ? null : (int)$post_data['reply_at_id'];
                    
                    $this->_transaction_isolation();
                    $this->db->trans_begin();
                    if ($comment->save([$task_set, $student])) {
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:tasks_comments_message_comment_post_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        if ((bool)$comment->approved) {
                            $all_students = $task_set->comment_subscriber_student;
                            $all_students->where('id !=', $this->usermanager->get_student_id());
                            $all_students->get();
                            $this->_send_multiple_emails($all_students, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_student.tpl', ['task_set' => $task_set, 'student' => $student, 'comment' => $comment]);
                            $task_set_related_teachers = new Teacher();
                            if (!is_null($task_set->group_id)) {
                                $task_set_related_teachers->where_related('room/group', 'id', $task_set->group_id);
                            } else {
                                $task_set_related_teachers->where_related('room/group/course', 'id', $task_set->course_id);
                            }
                            $task_set_related_teachers->group_by('id');
                            $all_teachers = new Teacher();
                            $all_teachers->where_related('comment_subscription', 'id', $task_set->id);
                            $all_teachers->union($task_set_related_teachers, false, '', null, null, 'id');
                            $all_teachers->check_last_query();
                            $this->_send_multiple_emails($all_teachers, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_teacher.tpl', ['task_set' => $task_set, 'student' => $student, 'comment' => $comment]);
                        }
                        return true;
                    }
    
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:tasks_comments_message_comment_post_error_save', Messages::MESSAGE_TYPE_ERROR);
                    return false;
                }
    
                $this->messages->add_message('lang:tasks_comments_message_comment_post_error_empty', Messages::MESSAGE_TYPE_ERROR);
                return false;
            }
    
            $this->messages->add_message('lang:tasks_comments_message_not_found_or_disabled', Messages::MESSAGE_TYPE_ERROR);
            return false;
        }
    
        $this->messages->add_message('lang:tasks_comments_message_comment_post_error_data', Messages::MESSAGE_TYPE_ERROR);
        return false;
    }
    
    private function normalize_student_name($student): string
    {
        $normalized = normalize($student->fullname);
        $output = '';
        for ($i = 0, $iMax = mb_strlen($normalized); $i < $iMax; $i++) {
            $char = mb_substr($normalized, $i, 1);
            if (preg_match('/^[a-zA-Z]$/', $char)) {
                $output .= $char;
            }
        }
        return $output;
    }
    
    private function get_task_sets(&$course, &$group, &$student): Task_set
    {
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related('active_for_student', 'id', $student->id);
        $course->where_related('participant', 'student_id', $student->id);
        $course->where_related('participant', 'allowed', 1);
        $course->include_related('period', 'name');
        $course->get();
        
        $task_set = new Task_set();
        $task_set2 = new Task_set();
        $group = new Group();
        
        if ($course->exists()) {
            $group->where_related_participant('student_id', $student->id);
            $group->where_related_participant('course_id', $course->id);
            $group->get();
            
            $task_set->select('`task_sets`.*, `rooms`.`time_day` AS `pb_time_day`, `rooms`.`time_begin` AS `pb_time_begin`, `rooms`.`id` AS `pb_room_id`, `task_sets`.`publish_start_time` AS `pb_publish_start_time`, `task_sets`.`upload_end_time` AS `pb_upload_end_time`');
            $task_set->where('published', 1);
            $task_set->where_related_course($course);
            $task_set->include_related('solution');
            $task_set->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set->include_related('solution/teacher', 'fullname');
            $task_set->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
            $task_set->group_start();
            $task_set->or_where('group_id', null);
            $task_set->or_where('group_id', $group->id);
            $task_set->group_end();
            $task_set->include_related('room', '*', true, true);
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->include_related('task_set_type');
            $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` WHERE `course_task_set_type_rel`.`course_id` = `${parent}`.`course_id` AND `course_task_set_type_rel`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            $task_set->where('content_type', 'task_set');
            
            $task_set2->select('`task_sets`.*, `task_set_permission_rooms`.`time_day` AS `pb_time_day`, `task_set_permission_rooms`.`time_begin` AS `pb_time_begin`, `task_set_permission_rooms`.`id` AS `pb_room_id`, `task_set_permissions`.`publish_start_time` AS `pb_publish_start_time`, `task_set_permissions`.`upload_end_time` AS `pb_upload_end_time`');
            $task_set2->where('published', 1);
            $task_set2->where_related_course($course);
            $task_set2->where_related('task_set_permission', 'group_id', $group->id);
            $task_set2->where_related('task_set_permission', 'enabled', 1);
            $task_set2->include_related('solution');
            $task_set2->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set2->include_related('solution/teacher', 'fullname');
            $task_set2->include_related('task_set_permission/room', '*', 'room', true);
            $task_set2->include_related_count('task', 'total_tasks');
            $task_set2->include_related('task_set_type');
            $task_set2->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set2->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` WHERE `course_task_set_type_rel`.`course_id` = `${parent}`.`course_id` AND `course_task_set_type_rel`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            $task_set2->where('content_type', 'task_set');
            
            $task_set3 = new Task_set();
            $task_set3->select('`task_sets`.*, NULL AS `pb_time_day`, NULL AS `pb_time_begin`, NULL AS `pb_room_id`, NULL AS `pb_publish_start_time`, "0000-00-00 00:00:00" AS `pb_upload_end_time`', false);
            $task_set3->where('published', 1);
            $task_set3->where_related_course($course);
            $task_set3->include_related('solution');
            $task_set3->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set3->include_related('solution/teacher', 'fullname');
            $task_set3->where_related('solution', 'student_id', $student->id);
            $task_set3->include_related('room', '*', true, true);
            $task_set3->include_related_count('task', 'total_tasks');
            $task_set3->include_related('task_set_type');
            $task_set3->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set3->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` WHERE `course_task_set_type_rel`.`course_id` = `${parent}`.`course_id` AND `course_task_set_type_rel`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            $task_set3->where('content_type', 'task_set');
            
            $sorting = $task_set2->union_order_by_overlay('task_set_type_name', 'task_set_types', 'name', 'task_set_type_id', 'asc');
            $sorting .= ', `sorting` ASC';
            $sorting .= ', `pb_publish_start_time` ASC, `pb_upload_end_time` ASC';
            $sorting .= ', ' . $task_set2->union_order_by_constant('name', 'asc');
            
            $task_set2->union([$task_set, $task_set3], false, $sorting, null, null, 'id');
        }
        
        return $task_set2;
    }
    
    private function get_task_set_by_id(&$course, &$group, &$student, $task_set_id): Task_set
    {
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related('active_for_student', 'id', $student->id);
        $course->where_related('participant', 'student_id', $student->id);
        $course->where_related('participant', 'allowed', 1);
        $course->include_related('period', 'name');
        $course->get();
        
        $task_set = new Task_set();
        $task_set2 = new Task_set();
        $group = new Group();
        
        if ($course->exists()) {
            $group->where_related_participant('student_id', $student->id);
            $group->where_related_participant('course_id', $course->id);
            $group->get();
            
            $task_set->select('`task_sets`.*, `rooms`.`time_day` AS `pb_time_day`, `rooms`.`time_begin` AS `pb_time_begin`, `rooms`.`id` AS `pb_room_id`, `task_sets`.`publish_start_time` AS `pb_publish_start_time`, `task_sets`.`upload_end_time` AS `pb_upload_end_time`');
            $task_set->where('published', 1);
            $task_set->where_related_course($course);
            $task_set->include_related('solution');
            $task_set->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
            $task_set->group_start();
            $task_set->or_where('group_id', null);
            $task_set->or_where('group_id', $group->id);
            $task_set->group_end();
            $task_set->include_related('room', '*', true, true);
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->include_related('task_set_type');
            $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set->where('id', $task_set_id);
            $task_set->include_related('course', 'test_scoring_deadline');
            $task_set->where('content_type', 'task_set');
            
            $task_set2->select('`task_sets`.*, `task_set_permission_rooms`.`time_day` AS `pb_time_day`, `task_set_permission_rooms`.`time_begin` AS `pb_time_begin`, `task_set_permission_rooms`.`id` AS `pb_room_id`, `task_set_permissions`.`publish_start_time` AS `pb_publish_start_time`, `task_set_permissions`.`upload_end_time` AS `pb_upload_end_time`');
            $task_set2->where('published', 1);
            $task_set2->where_related_course($course);
            $task_set2->where_related('task_set_permission', 'group_id', $group->id);
            $task_set2->where_related('task_set_permission', 'enabled', 1);
            $task_set2->include_related('solution');
            $task_set2->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set2->include_related('task_set_permission/room', '*', 'room', true);
            $task_set2->include_related_count('task', 'total_tasks');
            $task_set2->include_related('task_set_type');
            $task_set2->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set2->where('id', $task_set_id);
            $task_set2->include_related('course', 'test_scoring_deadline');
            $task_set2->where('content_type', 'task_set');
            
            $task_set3 = new Task_set();
            $task_set3->select('`task_sets`.*, NULL AS `pb_time_day`, NULL AS `pb_time_begin`, NULL AS `pb_room_id`, NULL AS `pb_publish_start_time`, "0000-00-00 00:00:00" AS `pb_upload_end_time`', false);
            $task_set3->where('published', 1);
            $task_set3->where_related_course($course);
            $task_set3->include_related('solution');
            $task_set3->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set3->where_related('solution', 'student_id', $student->id);
            $task_set3->include_related('room', '*', true, true);
            $task_set3->include_related_count('task', 'total_tasks');
            $task_set3->include_related('task_set_type');
            $task_set3->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set3->where('id', $task_set_id);
            $task_set3->include_related('course', 'test_scoring_deadline');
            $task_set3->where('content_type', 'task_set');
            
            $task_set2->union([$task_set, $task_set3], false, '', 1, 0, 'id');
        }
        
        return $task_set2;
    }
    
    private function can_upload_file($task_set, $course): bool
    {
        if ($task_set->exists() && $course->exists()) {
            $task_set_type = $course->task_set_type->where('id', $task_set->task_set_type_id)->include_join_fields()->get();
            if ($task_set_type->exists() && $task_set_type->join_upload_solution === 1) {
                if (is_null($task_set->pb_upload_end_time)) {
                    return true;
                }
                if (strtotime($task_set->pb_upload_end_time) > time()) {
                    return true;
                }
            }
        }
        return false;
    }
    
    private function filter_valid_task_sets(Task_set $task_sets): array
    {
        $output = [];
        
        $days = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        $minimum_next_time = date('U') + $this->smarty->cache_lifetime * 2;
        
        foreach ($task_sets->all as $task_set) {
            $add = true;
            if (is_null($task_set->solution_id)) {
                if (!is_null($task_set->pb_publish_start_time)) {
                    if (!is_null($task_set->pb_room_id)) {
                        if (strtotime($task_set->pb_publish_start_time) > time()) {
                            $add = false;
                            if (strtotime($task_set->pb_publish_start_time) < $minimum_next_time) {
                                $minimum_next_time = strtotime($task_set->pb_publish_start_time);
                            }
                        } else {
                            $current_day = (int)strftime('%w', strtotime($task_set->pb_publish_start_time));
                            $current_day = $current_day > 0 ? $current_day : 7;
                            if ($task_set->pb_time_day === $current_day) {
                                [$year, $month, $day] = explode(',', strftime('%Y,%m,%d', strtotime($task_set->pb_publish_start_time)));
                                $time = mktime(0, 0, 0, (int)$month, (int)$day, (int)$year) + (int)$task_set->pb_time_begin;
                            } else {
                                $time = strtotime('next ' . $days[$task_set->pb_time_day], strtotime($task_set->pb_publish_start_time)) + (int)$task_set->pb_time_begin;
                            }
                            if ($time > time()) {
                                $add = false;
                                if ($time < $minimum_next_time) {
                                    $minimum_next_time = $time;
                                }
                            }
                        }
                    } else {
                        if (strtotime($task_set->pb_publish_start_time) > time()) {
                            $add = false;
                            if (strtotime($task_set->pb_publish_start_time) < $minimum_next_time) {
                                $minimum_next_time = strtotime($task_set->pb_publish_start_time);
                            }
                        }
                    }
                }
            }
            if ($add) {
                $output[] = $task_set;
            }
        }
        
        $this->filter_next_task_set_publication_min_cache_lifetime = abs($minimum_next_time - date('U'));
        
        return $output;
    }
    
    private function compute_points($i_task_sets, Student $student): array
    {
        $task_sets = is_array($i_task_sets) ? $i_task_sets : (is_object($i_task_sets) && $i_task_sets instanceof Task_set ? $i_task_sets->all : []);
        
        $ids = [0];
        
        if (count($task_sets) > 0) {
            foreach ($task_sets as $task_set) {
                $ids[] = $task_set->id;
            }
        }
        
        $solutions = $student->solution->where_in_related('task_set', 'id', $ids)->get_iterated();
        
        $points = [];
        
        foreach ($solutions as $solution) {
            $points[$solution->task_set_id] = [
                'points'     => $solution->points + $solution->tests_points,
                'considered' => !(bool)$solution->not_considered,
            ];
        }
        
        $output = [
            'total' => 0,
            'max'   => 0,
        ];
        
        if (count($task_sets) > 0) {
            foreach ($task_sets as $task_set) {
                $output['total'] += ((isset($points[$task_set->id]) && $points[$task_set->id]['considered']) ? $points[$task_set->id]['points'] : 0);
                $output['max'] += (!is_null($task_set->points_override) ? $task_set->points_override : $task_set->total_points);
                $output[$task_set->task_set_type_id]['total'] = (isset($output[$task_set->task_set_type_id]['total']) ? $output[$task_set->task_set_type_id]['total'] : 0) + (isset($points[$task_set->id]) && $points[$task_set->id]['considered'] ? $points[$task_set->id]['points'] : 0);
                $output[$task_set->task_set_type_id]['max'] = (isset($output[$task_set->task_set_type_id]['max']) ? $output[$task_set->task_set_type_id]['max'] : 0) + (!is_null($task_set->points_override) ? $task_set->points_override : $task_set->total_points);
            }
        }
        
        return $output;
    }
    
    private function zip_plain_file_to_archive($archive_name, $original_file_name, $file_path): bool
    {
        if (file_exists($archive_name)) {
            rename($archive_name, rtrim($file_path, '/\\') . '/' . $original_file_name);
            $zip = new ZipArchive();
            if ($zip->open($archive_name, ZipArchive::CREATE) === true) {
                $zip->addFile(rtrim($file_path, '/\\') . '/' . $original_file_name, $original_file_name);
                $zip->close();
                @unlink(rtrim($file_path, '/\\') . '/' . $original_file_name);
                return true;
            }
    
            @unlink(rtrim($file_path, '/\\') . '/' . $original_file_name);
            return false;
        }
        return false;
    }
    
}
