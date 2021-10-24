<?php

/**
 * Projects controller for frontend.
 *
 * @package LIST_FE_Controllers
 * @author  Andrej Jursa
 */
class Projects extends LIST_Controller
{
    
    protected $filter_next_task_set_publication_min_cache_lifetime;
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        if ($this->router->method !== 'overview') {
            $this->usermanager->student_login_protected_redirect();
        }
    }
    
    public function index(): void
    {
        $cache_id = $this->usermanager->get_student_cache_id();
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('projects');
        $this->parser->add_css_file('frontend_projects.css');
        $this->parser->add_js_file('projects/list.js');
        
        if ($this->_is_cache_enabled()) {
            $this->smarty->caching = Smarty::CACHING_LIFETIME_SAVED;
        }
        if (!$this->_is_cache_enabled()
            || !$this->parser->isCached($this->parser->find_view('frontend/projects/index.tpl'), $cache_id)
        ) {
            $projects_all = $this->get_task_sets($course, $student);
            
            $projects = $this->filter_valid_task_sets($projects_all);
            if ($course->exists()) {
                if ($this->_is_cache_enabled() && $this->filter_next_task_set_publication_min_cache_lifetime > 0
                    && $this->filter_next_task_set_publication_min_cache_lifetime <= $this->smarty->cache_lifetime
                ) {
                    $this->smarty->setCacheLifetime($this->filter_next_task_set_publication_min_cache_lifetime + 1);
                    $this->parser->setCacheLifetimeForTemplateObject(
                        'frontend/projects/index.tpl',
                        $this->filter_next_task_set_publication_min_cache_lifetime + 1
                    );
                }
                
                $this->lang->init_overlays('task_sets', $projects, ['name']);
                $this->parser->assign('projects', $projects);
            }
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse(
            'frontend/projects/index.tpl',
            [],
            false,
            $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : false,
            $cache_id
        );
    }
    
    public function overview($task_set_id_url = null, $language = null): void
    {
        $task_set_id = url_get_id($task_set_id_url);
        $this->parser->add_css_file('frontend_projects.css');
        if (!is_null($language)) {
            $this->_init_specific_language($language);
        }
        if ($this->_is_cache_enabled()) {
            $this->smarty->caching = Smarty::CACHING_LIFETIME_SAVED;
        }
        $cache_id = 'project_' . $task_set_id . '|lang_' . $this->lang->get_current_idiom();
        if (!$this->_is_cache_enabled()
            || !$this->parser->isCached($this->parser->find_view('frontend/projects/overview.tpl'), $cache_id)
        ) {
            $project_all = $this->get_task_set_overview($task_set_id, $course);
            $project = $this->filter_valid_task_sets($project_all);
            if ($course->exists()) {
                if ($this->_is_cache_enabled() && $this->filter_next_task_set_publication_min_cache_lifetime > 0
                    && $this->filter_next_task_set_publication_min_cache_lifetime <= $this->smarty->cache_lifetime
                ) {
                    $this->smarty->setCacheLifetime($this->filter_next_task_set_publication_min_cache_lifetime + 1);
                    $this->parser->setCacheLifetimeForTemplateObject(
                        'frontend/projects/overview.tpl',
                        $this->filter_next_task_set_publication_min_cache_lifetime + 1
                    );
                }
                $this->lang->init_overlays('task_sets', $project, ['name', 'instructions']);
                $project = count($project) === 1 ? $project[0] : new Task_set();
                $this->parser->assign('project', $project);
                $tasks = $project->task;
                $tasks->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc');
                $tasks->get_iterated();
                $this->parser->assign('tasks', $tasks);
            }
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse(
            'frontend/projects/overview.tpl',
            [],
            false,
            $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : false,
            $cache_id
        );
    }
    
    public function selection($task_set_id_url = null): void
    {
        $task_set_id = url_get_id($task_set_id_url);
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('projects');
        $this->parser->add_css_file('frontend_projects.css');
        
        $cache_id = $this->usermanager->get_student_cache_id('task_set_' . $task_set_id);
        if (!$this->_is_cache_enabled()
            || !$this->parser->isCached($this->parser->find_view('frontend/projects/selection.tpl'), $cache_id)
        ) {
            $project_all = $this->get_task_set($task_set_id, $course, $student);
            $project = $this->filter_valid_task_sets($project_all);
            if ($course->exists()) {
                $this->lang->init_overlays('task_sets', $project, ['name', 'instructions']);
                $project = count($project) === 1 ? $project[0] : new Task_set();
                $this->parser->assign('project', $project);
                $tasks = $project->task;
                $tasks->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc');
                $tasks->get_iterated();
                $this->parser->assign('tasks', $tasks);
            }
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse(
            'frontend/projects/selection.tpl',
            [],
            false,
            $this->_is_cache_enabled(),
            $cache_id
        );
    }
    
    public function task($task_set_id_url = null, $task_id_url = null): void
    {
        $this->_add_mathjax();
        $task_set_id = url_get_id($task_set_id_url);
        $task_id = url_get_id($task_id_url);
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('projects');
        $this->parser->add_css_file('frontend_projects.css');
        $this->parser->add_js_file('projects/task.js');
        $this->_add_prettify();
        $this->_add_jquery_countdown();
        $this->parser->assign(
            'max_filesize',
            compute_size_with_unit((int)($this->config->item('maximum_solition_filesize') * 1024))
        );
        
        $cache_id = $this->usermanager->get_student_cache_id('task_set_' . $task_set_id . '|task_' . $task_id);
        if (!$this->_is_cache_enabled()
            || !$this->parser->isCached($this->parser->find_view('frontend/projects/task.tpl'), $cache_id)
        ) {
            $project_all = $this->get_task_set($task_set_id, $course, $student);
            $project = $this->filter_valid_task_sets($project_all);
            if ($course->exists()) {
                $this->lang->init_overlays('task_sets', $project, ['name']);
                $project = count($project) === 1 ? $project[0] : new Task_set();
                $this->parser->assign('project', $project);
                $task = $project->task;
                $task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc');
                $task->get_by_id($task_id);
                $project_selection = new Project_selection();
                $project_selection->where('task_set_id', $project->id);
                $project_selection->where('student_id', $this->usermanager->get_student_id());
                $project_selection->where('task_id', $task->id);
                $project_selection->get();
                $students = new Student();
                $students->where_related('project_selection', 'task_set_id', $project->id);
                $students->where_related('project_selection', 'task_id', $task->id);
                $students->get_iterated();
                $solution_versions = new Solution_version();
                $solution_versions->where_related('solution/task_set', 'id', $task_set_id);
                $solution_versions->where_related(
                    'solution',
                    'student_id',
                    $this->usermanager->get_student_id()
                );
                $query = $solution_versions->get_raw();
                $versions_metadata = [];
                if ($query->num_rows()) {
                    foreach ($query->result() as $row) {
                        $versions_metadata[$row->version] = clone $row;
                    }
                }
                $query->free_result();
                $this->parser->assign('task', $task);
                $this->parser->assign('students', $students);
                $this->parser->assign('project_selection', $project_selection);
                $this->parser->assign('solution_files', $project->get_student_files($student->id));
                $this->parser->assign('versions_metadata', $versions_metadata);
            }
            $this->parser->assign(['course' => $course]);
        }
        $this->parser->parse(
            'frontend/projects/task.tpl',
            [],
            false,
            $this->_is_cache_enabled(),
            $cache_id
        );
    }
    
    public function select_project($task_set_id_url = null, $task_id_url = null): void
    {
        $task_set_id = url_get_id($task_set_id_url);
        $task_id = url_get_id($task_id_url);
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related('active_for_student', 'id', $student->id);
        $course->where_related('participant', 'student_id', $student->id);
        $course->where_related('participant', 'allowed', 1);
        $course->include_related('period', 'name');
        $course->get();
        
        if ($course->exists()) {
            $task_set = new Task_set();
            $task_set->where_related('course', $course);
            $task_set->where('published', 1);
            $task_set->get_by_id($task_set_id);
            
            if ($task_set->exists()) {
                if ($task_set->get_student_files_count($student->id) === 0
                    && date('Y-m-d H:i:s') <= $task_set->project_selection_deadline
                ) {
                    /** @var Task $task */
                    $task = $task_set->task->include_join_fields()->get_by_id($task_id);
                    
                    if ($task->exists()) {
                        $project_selection = new Project_selection();
                        $project_selection->where_related('student', $student);
                        $project_selection->where_related('task_set', $task_set);
                        $project_selection->get();
                        
                        $can_continue = true;
                        
                        if ($project_selection->exists()) {
                            if ($project_selection->task_id !== $task->id) {
                                $project_selection->task_id = $task->id;
                            } else {
                                $can_continue = false;
                            }
                        } else {
                            $project_selection->task_id = $task->id;
                            $project_selection->task_set_id = $task_set->id;
                            $project_selection->student_id = $student->id;
                        }
                        
                        if ($can_continue) {
                            $project_selection_count = new Project_selection();
                            $project_selection_count->where_related('task_set', $task_set);
                            $project_selection_count->where_related('task', $task);
                            $count = $project_selection_count->count();
                            
                            if ($task->join_max_projects_selections > $count) {
                                if ($project_selection->save()) {
                                    $this->db->trans_commit();
                                    $this->_action_success();
                                    $this->output->set_internal_value('task_set_id', $task_set->id);
                                    $this->messages->add_message($this->lang->line(
                                        'projects_selection_success'),
                                        Messages::MESSAGE_TYPE_SUCCESS
                                    );
                                    redirect(
                                        create_internal_url(
                                        'projects/task/' . $task_set_id_url . '/' . $task_id_url
                                        )
                                    );
                                } else {
                                    $this->db->trans_rollback();
                                    $this->messages->add_message(
                                        $this->lang->line('projects_selection_error_save_failed'),
                                        Messages::MESSAGE_TYPE_ERROR
                                    );
                                    redirect(
                                        create_internal_url(
                                            'projects/task/' . $task_set_id_url . '/' . $task_id_url
                                        )
                                    );
                                }
                            } else {
                                $this->db->trans_rollback();
                                $this->messages->add_message(
                                    $this->lang->line('projects_selection_error_no_free_space'),
                                    Messages::MESSAGE_TYPE_ERROR
                                );
                                redirect(
                                    create_internal_url(
                                        'projects/task/' . $task_set_id_url . '/' . $task_id_url
                                    )
                                );
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->messages->add_message(
                                $this->lang->line('projects_selection_error_already_selected'),
                                Messages::MESSAGE_TYPE_ERROR
                            );
                            redirect(
                                create_internal_url(
                                    'projects/task/' . $task_set_id_url . '/' . $task_id_url
                                )
                            );
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message(
                            $this->lang->line('projects_selection_error_task_not_found'),
                            Messages::MESSAGE_TYPE_ERROR
                        );
                        redirect(create_internal_url('projects/selection/' . $task_set_id_url));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        $this->lang->line('projects_selection_error_selection_disabled'),
                        Messages::MESSAGE_TYPE_ERROR
                    );
                    redirect(create_internal_url('projects/selection/' . $task_set_id_url));
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    $this->lang->line('projects_selection_error_task_set_not_found'),
                    Messages::MESSAGE_TYPE_ERROR
                );
                redirect(create_internal_url('projects'));
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message(
                $this->lang->line('projects_selection_error_no_active_course'),
                Messages::MESSAGE_TYPE_ERROR
            );
            redirect(create_internal_url('projects'));
        }
    }
    
    public function upload_solution($task_set_id_url, $task_id_url): void
    {
        $task_set_id = url_get_id($task_set_id_url);
        $task_id = url_get_id($task_id_url);
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $date = date('Y-m-d H:i:s');
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course = new Course();
        $course->where_related('active_for_student', 'id', $student->id);
        $course->where_related('participant', 'student_id', $student->id);
        $course->where_related('participant', 'allowed', 1);
        $course->include_related('period', 'name');
        $course->get();
        
        $task_set = new Task_set();
        $task_set->where_related($course);
        $task_set->where('published', 1);
        $task_set->group_start();
        $task_set->where('publish_start_time <=', $date);
        $task_set->or_where('publish_start_time', null);
        $task_set->group_end();
        $task_set->get_by_id($task_set_id);
        
        $task = $task_set->task->include_join_fields()->get_by_id($task_id);
        
        $project_selection = new Project_selection();
        $project_selection->where_related($student);
        $project_selection->where_related($task_set);
        $project_selection->where_related($task);
        $project_selection->get();
        
        if ($student->exists() && $course->exists() && $task_set->exists()
            && $task->exists() && $project_selection->exists()
        ) {
            if ($date <= $task_set->upload_end_time) {
                $config['upload_path'] = 'private/uploads/solutions/task_set_' . (int)$task_set_id . '/';
                $config['allowed_types'] = 'zip';
                $config['max_size'] = (int)($this->config->item('maximum_solition_filesize'));
                $current_version = $task_set->get_student_file_next_version($student->id);
                $config['file_name'] = $student->id . '_' . $this->normalize_student_name($student) . '_'
                    . substr(md5(time() . rand(-500000, 500000)), 0, 4)
                    . '_' . $current_version . '.zip';
                @mkdir($config['upload_path'], DIR_READ_MODE);
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('file')) {
                    $solution = new Solution();
                    $solution->where('task_set_id', $task_set->id);
                    $solution->where('student_id', $student->id);
                    $solution->get();
                    $revalidate = 1;
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
                            'task_set' => $task_set,
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
                        $log->add_student_solution_upload_log(
                            sprintf(
                                $this->lang->line('projects_task_solution_upload_log_message'),
                                $config['file_name']
                            ),
                            $student,
                            $solution->id
                        );
                        $this->db->trans_commit();
                        $this->messages->add_message(
                            'lang:projects_task_solution_uploaded',
                            Messages::MESSAGE_TYPE_SUCCESS
                        );
                        $this->_action_success();
                        $this->output->set_internal_value('task_set_id', $solution->task_set_id);
                        $this->output->set_internal_value('task_id', $task->id);
                    } else {
                        $this->db->trans_rollback();
                        @unlink($config['upload_path'] . $config['file_name']);
                        $this->messages->add_message(
                            'lang:projects_task_solution_canceled_due_db_error',
                            Messages::MESSAGE_TYPE_ERROR
                        );
                    }
                    redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
                } else {
                    $this->db->trans_rollback();
                    $this->parser->assign(
                        'file_error_message',
                        $this->upload->display_errors('', '')
                    );
                    $this->task($task_set_id_url, $task_id_url);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:projects_task_solution_upload_time_error',
                    Messages::MESSAGE_TYPE_ERROR
                );
                redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message(
                'lang:projects_task_solution_database_data_wrong_error',
                Messages::MESSAGE_TYPE_ERROR
            );
            redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
        }
    }
    
    public function reset_task_cache($task_set_id, $task_id): void
    {
        $this->_action_success();
        $this->output->set_internal_value('task_set_id', $task_set_id);
        $this->output->set_internal_value('task_id', $task_id);
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
    
    private function get_task_sets(&$course, &$student): Task_set
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
        
        if ($course->exists()) {
            $task_set->where('published', 1);
            $task_set->where_related_course($course);
            $task_set->include_related('solution');
            $task_set->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set->include_related('solution/teacher', 'fullname');
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->where('content_type', 'project');
            $task_set->include_related('project_selection');
            $task_set->add_join_condition(
                '`project_selections`.`student_id` = ? '
                . 'AND `project_selections`.`task_set_id` = `task_sets`.`id`',
                [$student->id]
            );
            $task_set->include_related('project_selection/task');
            $task_set->order_by('publish_start_time', 'asc');
            $task_set->order_by('upload_end_time', 'asc');
            $task_set->order_by_with_overlay('name', 'asc');
            $task_set->get();
        }
        
        return $task_set;
    }
    
    private function get_task_set($task_set_id, &$course, &$student)
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
        
        if ($course->exists()) {
            $task_set->where('published', 1);
            $task_set->where_related_course($course);
            $task_set->include_related('solution');
            $task_set->add_join_condition('`solutions`.`student_id` = ?', [$student->id]);
            $task_set->include_related('solution/teacher', 'fullname');
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->where('content_type', 'project');
            $task_set->include_related('project_selection');
            $task_set->add_join_condition(
                '`project_selections`.`student_id` = ? '
                . 'AND `project_selections`.`task_set_id` = `task_sets`.`id`',
                [$student->id]
            );
            $task_set->include_related('project_selection/task');
            $task_set->order_by('publish_start_time', 'asc');
            $task_set->order_by('upload_end_time', 'asc');
            $task_set->order_by_with_overlay('name', 'asc');
            $task_set->get_by_id((int)$task_set_id);
        }
        
        return $task_set;
    }
    
    private function get_task_set_overview($task_set_id, &$course): Task_set
    {
        $task_set = new Task_set();
        $task_set->where('published', 1);
        $task_set->include_related_count('task', 'total_tasks');
        $task_set->where('content_type', 'project');
        $task_set->order_by('publish_start_time', 'asc');
        $task_set->order_by('upload_end_time', 'asc');
        $task_set->order_by_with_overlay('name', 'asc');
        $task_set->get_by_id((int)$task_set_id);
        
        $course = new Course();
        $course->where_related('task_set', 'id', (int)$task_set->id);
        $course->include_related('period');
        $course->get();
        
        return $task_set;
    }
    
    private function filter_valid_task_sets(Task_set $task_sets): array
    {
        $output = [];
        
        $days = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        $minimum_next_time = date('U') + $this->smarty->cache_lifetime * 2;
        
        foreach ($task_sets->all as $task_set) {
            $add = true;
            if (is_null($task_set->solution_id)) {
                if (!is_null($task_set->publish_start_time)) {
                    if (strtotime($task_set->publish_start_time) > time()) {
                        $add = false;
                        if (strtotime($task_set->publish_start_time) < $minimum_next_time) {
                            $minimum_next_time = strtotime($task_set->publish_start_time);
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
    
}
