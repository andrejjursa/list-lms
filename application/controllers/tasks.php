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
    }

    public function index() {
        $cache_id = $this->usermanager->get_student_cache_id();
        if (!$this->_is_cache_enabled() || !$this->parser->isCached('frontend/tasks/index.tpl', $cache_id)) {
            $this->_initialize_student_menu();
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
            $this->parser->add_js_file('tasks/list.js');
            $this->parser->assign(array('course' => $course));
        }
        $this->parser->parse('frontend/tasks/index.tpl', array(), FALSE, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function task($task_set_id = NULL) {
        $cache_id = $this->usermanager->get_student_cache_id('task_set_' . $task_set_id);
        if (!$this->_is_cache_enabled() || !$this->parser->isCached('frontend/tasks/task.tpl', $cache_id)) {
            $this->_initialize_student_menu();
            $this->usermanager->student_login_protected_redirect();

            $this->_select_student_menu_pagetag('tasks');

            $task_set = $this->get_task_set_by_id($course, $group, $student, $task_set_id);
            if ($course->exists()) {
                $task_sets = $this->filter_valid_task_sets($task_set);
                $this->lang->init_overlays('task_sets', $task_sets, array('name'));
                $filtered_task_set = count($task_sets) == 1 ? $task_sets[0] : new Task_set();
                if ($filtered_task_set->exists()) {
                    $this->load->helper('tests');
                    $test_types_subtypes = get_all_supported_test_types_and_subtypes();
                    $this->lang->init_overlays('task_sets', $filtered_task_set, array('name', 'instructions'));
                    $this->parser->assign('task_set', $filtered_task_set);
                    $this->parser->assign('task_set_can_upload', $this->can_upload_file($filtered_task_set, $course));
                    $this->parser->assign('solution_files', $filtered_task_set->get_student_files($student->id));
                    $this->parser->assign('max_filesize', compute_size_with_unit(intval($this->config->item('maximum_solition_filesize') * 1024)));
                    $this->parser->assign('test_types', $test_types_subtypes['types']);
                    $this->parser->assign('test_subtypes', $test_types_subtypes['subtypes']);
                } else {
                    $this->messages->add_message('lang:tasks_task_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('tasks/index'));
                }
            }

            $this->parser->add_css_file('frontend_tasks.css');
            $this->parser->add_js_file('tasks/task.js');
            $this->_add_prettify();
            $this->_add_scrollTo();
            $this->parser->assign(array('course' => $course));
        }
        $this->parser->parse('frontend/tasks/task.tpl', array(), FALSE, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function upload_solution($task_set_id = 0) {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = $this->get_task_set_by_id($course, $group, $student, $task_set_id);
        $task_sets = $this->filter_valid_task_sets($task_set);
        $filtered_task_set = count($task_sets) == 1 ? $task_sets[0] : new Task_set();
        if ($filtered_task_set->id == intval($task_set_id) && $this->can_upload_file($filtered_task_set, $course)) {
            $allowed_file_types_array = trim($filtered_task_set->allowed_file_types) != '' ? array_map('trim', explode(',', $filtered_task_set->allowed_file_types)) : array();
            $config['upload_path'] = 'private/uploads/solutions/task_set_' . intval($task_set_id) . '/';
            $config['allowed_types'] = 'zip' . (count($allowed_file_types_array) ? '|' . implode('|', $allowed_file_types_array) : '');
            $config['max_size'] = intval($this->config->item('maximum_solition_filesize'));
            $config['file_name'] = $student->id . '_' . $this->normalize_student_name($student) . '_' . substr(md5(time() . rand(-500000, 500000)), 0, 4) . '_' . $filtered_task_set->get_student_file_next_version($student->id) . '.zip';
            @mkdir($config['upload_path'], DIR_READ_MODE);
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
                $mimes = $this->upload->mimes_types('zip');
                if ((is_array($mimes) && !in_array($upload_data['file_type'], $mimes)) || (is_string($mimes) && $upload_data['file_type'] != $mimes)) {
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
                    $this->_action_success();
                    $this->output->set_internal_value('task_set_id', $solution->task_set_id);
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
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file_info['filepath']);
                finfo_close($finfo);
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $mime_type);
                header('Content-Disposition: attachment; filename='.$filename);
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
        }
        $this->output->set_status_header(404, 'Not found');
    }

    public function download_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/' . $filename;
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename='.basename($filepath));
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
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    public function download_hidden_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/hidden/' . $filename;
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename='.basename($filepath));
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
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    public function show_comments($task_id) {
        $this->usermanager->student_login_protected_redirect();
        
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_id));
        $comments = array();
        
        if ($task_set->exists() && (bool)$task_set->comments_enabled) {
            $comments = Comment::get_comments_for_task_set($task_set);
        }
        
        $this->parser->parse('frontend/tasks/show_comments.tpl', array('comments' =>  $comments, 'task_set' => $task_set));
    }
    
    public function subscribe_to_task_comments($task_id) {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_id));
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        if ($task_set->exists() && $student->exists() && $student->save(array('comment_subscription' => $task_set))) {
            $this->db->trans_commit();
            $this->messages->add_message('lang:tasks_comments_message_subscription_successful', Messages::MESSAGE_TYPE_SUCCESS);
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:tasks_comments_message_subscription_error', Messages::MESSAGE_TYPE_ERROR);
        }
        redirect(create_internal_url('tasks/show_comments/' . $task_id));
    }
    
    public function unsubscribe_to_task_comments($task_id) {
        $this->usermanager->student_login_protected_redirect();
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_id));
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
    
    public function reply_at_comment($task_id, $comment_id) {
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
        $this->parser->parse('frontend/tasks/reply_at_comment.tpl', array('task_set' => $task_set, 'comment' => $comment));
    }

    public function post_comment($task_id) {
        $this->usermanager->student_login_protected_redirect();
        
        $this->create_comment();
        
        redirect(create_internal_url('tasks/show_comments/' . $task_id));
    }
    
    public function post_comment_reply($task_id, $comment_id) {
        $this->usermanager->student_login_protected_redirect();
        
        $this->create_comment();
        
        redirect(create_internal_url('tasks/reply_at_comment/' . $task_id . '/' . $comment_id));
    }

    private function create_comment() {
        $post_data = $this->input->post('comment');
        if (array_key_exists('text', $post_data) && array_key_exists('task_set_id', $post_data) && array_key_exists('reply_at_id', $post_data)) {
            $task_set = new Task_set();
            $task_set->get_by_id(intval($post_data['task_set_id']));
            $student = new Student();
            $student->get_by_id($this->usermanager->get_student_id());
            if ($task_set->exists() && $student->exists() && (bool)$task_set->comments_enabled) {
                if (trim(strip_tags($post_data['text'])) != '') {
                    $text = strip_tags($post_data['text'], '<a><strong><em><span>');
                    $comment = new Comment();
                    $comment->text = $text;
                    $comment->approved = (bool)$task_set->comments_moderated ? 0 : 1;
                    $comment->reply_at_id = empty($post_data['reply_at_id']) ? NULL : intval($post_data['reply_at_id']);
                    
                    $this->_transaction_isolation();
                    $this->db->trans_begin();
                    if ($comment->save(array($task_set, $student))) {
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:tasks_comments_message_comment_post_success_save', Messages::MESSAGE_TYPE_SUCCESS);
                        if ((bool)$comment->approved) {
                            $all_students = $task_set->comment_subscriber_student;
                            $all_students->where('id !=', $this->usermanager->get_student_id());
                            $all_students->get();
                            $this->_send_multiple_emails($all_students, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_student.tpl', array('task_set' => $task_set, 'student' => $student, 'comment' => $comment));
                            $task_set_related_teachers = new Teacher();
                            if (!is_null($task_set->group_id)) {
                                $task_set_related_teachers->where_related('room/group', 'id', $task_set->group_id);
                            } else {
                                $task_set_related_teachers->where_related('room/group/course', 'id', $task_set->course_id);
                            }
                            $task_set_related_teachers->group_by('id');
                            $all_teachers = new Teacher();
                            $all_teachers->where_related('comment_subscription', 'id', $task_set->id);
                            $all_teachers->union($task_set_related_teachers, FALSE, '', NULL, NULL, 'id');
                            $all_teachers->check_last_query();
                            $this->_send_multiple_emails($all_teachers, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_teacher.tpl', array('task_set' => $task_set, 'student' => $student, 'comment' => $comment));
                        }
                        return TRUE;
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:tasks_comments_message_comment_post_error_save', Messages::MESSAGE_TYPE_ERROR);
                        return FALSE;
                    }
                } else {
                    $this->messages->add_message('lang:tasks_comments_message_comment_post_error_empty', Messages::MESSAGE_TYPE_ERROR);
                    return FALSE;
                }
            } else {
                $this->messages->add_message('lang:tasks_comments_message_not_found_or_disabled', Messages::MESSAGE_TYPE_ERROR);
                return FALSE;
            }
        } else {
            $this->messages->add_message('lang:tasks_comments_message_comment_post_error_data', Messages::MESSAGE_TYPE_ERROR);
            return FALSE;
        }
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
            $task_set->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set->include_related('solution/teacher', 'fullname');
            $task_set->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
            $task_set->group_start();
                $task_set->or_where('group_id', NULL);
                $task_set->or_where('group_id', $group->id);
            $task_set->group_end();
            $task_set->include_related('room', '*', TRUE, TRUE);
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->include_related('task_set_type');
            $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` WHERE `course_task_set_type_rel`.`course_id` = `${parent}`.`course_id` AND `course_task_set_type_rel`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            
            $task_set2->select('`task_sets`.*, `task_set_permission_rooms`.`time_day` AS `pb_time_day`, `task_set_permission_rooms`.`time_begin` AS `pb_time_begin`, `task_set_permission_rooms`.`id` AS `pb_room_id`, `task_set_permissions`.`publish_start_time` AS `pb_publish_start_time`, `task_set_permissions`.`upload_end_time` AS `pb_upload_end_time`');
            $task_set2->where('published', 1);
            $task_set2->where_related_course($course);
            $task_set2->where_related('task_set_permission', 'group_id', $group->id);
            $task_set2->where_related('task_set_permission', 'enabled', 1);
            $task_set2->include_related('solution');
            $task_set2->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set2->include_related('solution/teacher', 'fullname');
            $task_set2->include_related('task_set_permission/room', '*', 'room', TRUE);
            $task_set2->include_related_count('task', 'total_tasks');
            $task_set2->include_related('task_set_type');
            $task_set2->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set2->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` WHERE `course_task_set_type_rel`.`course_id` = `${parent}`.`course_id` AND `course_task_set_type_rel`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            
            $task_set3 = new Task_set();
            $task_set3->select('`task_sets`.*, NULL AS `pb_time_day`, NULL AS `pb_time_begin`, NULL AS `pb_room_id`, NULL AS `pb_publish_start_time`, "0000-00-00 00:00:00" AS `pb_upload_end_time`', FALSE);
            $task_set3->where('published', 1);
            $task_set3->where_related_course($course);
            $task_set3->include_related('solution');
            $task_set3->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set3->include_related('solution/teacher', 'fullname');
            $task_set3->where_related('solution', 'student_id', $student->id);
            $task_set3->include_related('room', '*', TRUE, TRUE);
            $task_set3->include_related_count('task', 'total_tasks');
            $task_set3->include_related('task_set_type');
            $task_set3->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set3->select_subquery('(SELECT `upload_solution` FROM `course_task_set_type_rel` WHERE `course_task_set_type_rel`.`course_id` = `${parent}`.`course_id` AND `course_task_set_type_rel`.`task_set_type_id` = `${parent}`.`task_set_type_id`)', 'join_upload_solution');
            
            $sorting = $task_set2->union_order_by_overlay('task_set_type_name', 'task_set_types', 'name', 'task_set_type_id', 'asc');
            $sorting .= ', `pb_publish_start_time` ASC, `pb_upload_end_time` ASC';
            $sorting .= ', ' . $task_set2->union_order_by_constant('name', 'asc');
            
            $task_set2->union(array($task_set, $task_set3), FALSE, $sorting, NULL, NULL, 'id');
        }
        
        return $task_set2;
    }
    
    private function get_task_set_by_id(&$course, &$group, &$student, $task_set_id) {
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
            $task_set->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
            $task_set->group_start();
                $task_set->or_where('group_id', NULL);
                $task_set->or_where('group_id', $group->id);
            $task_set->group_end();
            $task_set->include_related('room', '*', TRUE, TRUE);
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->include_related('task_set_type');
            $task_set->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set->where('id', $task_set_id);
            
            $task_set2->select('`task_sets`.*, `task_set_permission_rooms`.`time_day` AS `pb_time_day`, `task_set_permission_rooms`.`time_begin` AS `pb_time_begin`, `task_set_permission_rooms`.`id` AS `pb_room_id`, `task_set_permissions`.`publish_start_time` AS `pb_publish_start_time`, `task_set_permissions`.`upload_end_time` AS `pb_upload_end_time`');
            $task_set2->where('published', 1);
            $task_set2->where_related_course($course);
            $task_set2->where_related('task_set_permission', 'group_id', $group->id);
            $task_set2->where_related('task_set_permission', 'enabled', 1);
            $task_set2->include_related('solution');
            $task_set2->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set2->include_related('task_set_permission/room', '*', 'room', TRUE);
            $task_set2->include_related_count('task', 'total_tasks');
            $task_set2->include_related('task_set_type');
            $task_set2->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set2->where('id', $task_set_id);
            
            $task_set3 = new Task_set();
            $task_set3->select('`task_sets`.*, NULL AS `pb_time_day`, NULL AS `pb_time_begin`, NULL AS `pb_room_id`, NULL AS `pb_publish_start_time`, "0000-00-00 00:00:00" AS `pb_upload_end_time`', FALSE);
            $task_set3->where('published', 1);
            $task_set3->where_related_course($course);
            $task_set3->include_related('solution');
            $task_set3->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set3->where_related('solution', 'student_id', $student->id);
            $task_set3->include_related('room', '*', TRUE, TRUE);
            $task_set3->include_related_count('task', 'total_tasks');
            $task_set3->include_related('task_set_type');
            $task_set3->select_subquery('(SELECT SUM(`points_total`) AS `points` FROM `task_task_set_rel` WHERE `task_set_id` = `${parent}`.`id` AND `task_task_set_rel`.`bonus_task` = 0)', 'total_points');
            $task_set3->where('id', $task_set_id);
            
            $task_set2->union(array($task_set, $task_set3), FALSE, '', 1, 0, 'id');
        }
        
        return $task_set2;
    }

    private function can_upload_file($task_set, $course) {
        if ($task_set->exists() && $course->exists()) {
            $task_set_type = $course->task_set_type->where('id', $task_set->task_set_type_id)->include_join_fields()->get();
            if ($task_set_type->exists() && $task_set_type->join_upload_solution == 1) {
                if (is_null($task_set->pb_upload_end_time)) { return TRUE; }
                if (strtotime($task_set->pb_upload_end_time) > time()) { return TRUE; }
            }
        }
        return FALSE;
    }
    
    private function filter_valid_task_sets(Task_set $task_sets) {
        $output = array();
        
        $days = array(1=> 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                
        foreach($task_sets->all as $task_set) {
            $add = TRUE;
            if (is_null($task_set->solution_id)) {
                if (!is_null($task_set->pb_publish_start_time)) {
                    if (!is_null($task_set->pb_room_id)) {
                        if (strtotime($task_set->pb_publish_start_time) > time()) {
                            $add = FALSE;
                        } else {
                            $current_day = intval(strftime('%w', strtotime($task_set->pb_publish_start_time)));
                            $current_day = $current_day > 0 ? $current_day : 7;
                            if ($task_set->pb_time_day == $current_day) {
                                list($year, $month, $day) = explode(',', strftime('%Y,%m,%d', strtotime($task_set->pb_publish_start_time)));
                                $time = mktime(0, 0, 0, intval($month), intval($day), intval($year)) + intval($task_set->pb_time_begin);
                            } else {
                                $time = strtotime('next ' . $days[$task_set->pb_time_day], strtotime($task_set->pb_publish_start_time)) + intval($task_set->pb_time_begin);
                            }
                            if ($time > time()) {
                                $add = FALSE;
                            }
                        }
                    } else {
                        if (strtotime($task_set->pb_publish_start_time) > time()) {
                            $add = FALSE;
                        }
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
            $points[$solution->task_set_id] = array(
                'points' => $solution->points,
                'considered' => !(bool)$solution->not_considered,
            );
        }
        
        $output = array(
            'total' => 0,
            'max' => 0,
        );
        
        if (count($task_sets) > 0) { foreach($task_sets as $task_set) {
            $output['total'] += ((isset($points[$task_set->id]) && $points[$task_set->id]['considered']) ? $points[$task_set->id]['points'] : 0);
            $output['max'] += (!is_null($task_set->points_override) ? $task_set->points_override : $task_set->total_points);
            $output[$task_set->task_set_type_id]['total'] = (isset($output[$task_set->task_set_type_id]['total']) ? $output[$task_set->task_set_type_id]['total'] : 0) + (isset($points[$task_set->id]) && $points[$task_set->id]['considered'] ? $points[$task_set->id]['points'] : 0);
            $output[$task_set->task_set_type_id]['max'] = (isset($output[$task_set->task_set_type_id]['max']) ? $output[$task_set->task_set_type_id]['max'] : 0) + (!is_null($task_set->points_override) ? $task_set->points_override : $task_set->total_points);
        }}
        
        return $output;
    }
    
    private function zip_plain_file_to_archive($archive_name, $original_file_name, $file_path) {
        if (file_exists($archive_name)) {
            rename($archive_name, rtrim($file_path, '/\\') . '/' . $original_file_name);
            $zip = new ZipArchive();
            if ($zip->open($archive_name, ZipArchive::CREATE) === TRUE) {
                $zip->addFile(rtrim($file_path, '/\\') . '/' . $original_file_name, $original_file_name);
                $zip->close();
                @unlink(rtrim($file_path, '/\\') . '/' . $original_file_name);
                return TRUE;
            } else {
                @unlink(rtrim($file_path, '/\\') . '/' . $original_file_name);
                return FALSE;
            }
        }
        return FALSE;
    }
        
}