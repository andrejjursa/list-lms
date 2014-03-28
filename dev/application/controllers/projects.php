<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Projects controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Projects extends LIST_Controller {
    
    protected $filter_next_task_set_publication_min_cache_lifetime;
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
        $this->usermanager->student_login_protected_redirect();
    }
    
    public function index() {
        $cache_id = $this->usermanager->get_student_cache_id();
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('projects');
        $this->parser->add_css_file('frontend_projects.css');
        $this->parser->add_js_file('projects/list.js');
        
        if ($this->_is_cache_enabled()) {
            $this->smarty->caching = Smarty::CACHING_LIFETIME_SAVED;
        }
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/projects/index.tpl'), $cache_id)) {
            $projects_all = $this->get_task_sets($course, $student);
            
            $projects = $this->filter_valid_task_sets($projects_all);
            if ($course->exists()) {
                if ($this->_is_cache_enabled() && $this->filter_next_task_set_publication_min_cache_lifetime > 0 && $this->filter_next_task_set_publication_min_cache_lifetime <= $this->smarty->cache_lifetime) {
                    $this->smarty->setCacheLifetime($this->filter_next_task_set_publication_min_cache_lifetime + 1);
                    $this->parser->setCacheLifetimeForTemplateObject('frontend/projects/index.tpl', $this->filter_next_task_set_publication_min_cache_lifetime + 1);
                }

                $this->lang->init_overlays('task_sets', $projects, array('name'));
                $this->parser->assign('projects', $projects);
            }
            $this->parser->assign(array('course' => $course));
        }
        $this->parser->parse('frontend/projects/index.tpl', array(), FALSE, $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : FALSE, $cache_id);
    }
    
    public function selection($task_set_id_url = NULL) {
        $task_set_id = url_get_id($task_set_id_url);
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('projects');
        $this->parser->add_css_file('frontend_projects.css');
        
        $cache_id = $this->usermanager->get_student_cache_id('task_set_' . $task_set_id);
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/projects/selection.tpl'), $cache_id)) {
            $project_all = $this->get_task_set($task_set_id, $course, $student);
            $project = $this->filter_valid_task_sets($project_all);
            if ($course->exists()) {
                $this->lang->init_overlays('task_sets', $project, array('name', 'instructions'));
                $project = count($project) == 1 ? $project[0] : new Task_set();
                $this->parser->assign('project', $project);
                $tasks = $project->task;
                $tasks->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc');
                $tasks->get_iterated();
                $this->parser->assign('tasks', $tasks);
            }
            $this->parser->assign(array('course' => $course));
        }
        $this->parser->parse('frontend/projects/selection.tpl', array(), FALSE, $this->_is_cache_enabled(), $cache_id);
    }
    
    public function task($task_set_id_url = NULL, $task_id_url = NULL) {
        $task_set_id = url_get_id($task_set_id_url);
        $task_id = url_get_id($task_id_url);
        
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('projects');
        $this->parser->add_css_file('frontend_projects.css');
        $this->parser->add_js_file('projects/task.js');
        $this->_add_prettify();
        $this->_add_jquery_countdown();
        $this->parser->assign('max_filesize', compute_size_with_unit(intval($this->config->item('maximum_solition_filesize') * 1024)));
        
        $cache_id = $this->usermanager->get_student_cache_id('task_set_' . $task_set_id . '|task_id_' . $task_id);
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/projects/task.tpl'), $cache_id)) {
            $project_all = $this->get_task_set($task_set_id, $course, $student);
            $project = $this->filter_valid_task_sets($project_all);
            if ($course->exists()) {
                $this->lang->init_overlays('task_sets', $project, array('name'));
                $project = count($project) == 1 ? $project[0] : new Task_set();
                $this->parser->assign('project', $project);
                $task = $project->task;
                $task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc');
                $task->get_by_id($task_id);
                $project_selection = new Project_selection();
                $project_selection->where('task_set_id', $project->id);
                $project_selection->where('student_id', $this->usermanager->get_student_id());
                $project_selection->where('task_id', $task->id);
                $project_selection->get();
                $this->parser->assign('task', $task);
                $this->parser->assign('project_selection', $project_selection);
                $this->parser->assign('solution_files', $project->get_student_files($student->id));
            }
            $this->parser->assign(array('course' => $course));
        }
        $this->parser->parse('frontend/projects/task.tpl', array(), FALSE, $this->_is_cache_enabled(), $cache_id);
    }

    public function select_project($task_set_id_url = NULL, $task_id_url = NULL) {
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
            $task_set->get_by_id($task_set_id);

            if ($task_set->exists()) {
                if ($task_set->get_student_files_count($student->id) == 0 && date('Y-m-d H:i:s') <= $task_set->project_selection_deadline) {
                    $task = $task_set->task->include_join_fields()->get_by_id($task_id);

                    if ($task->exists()) {
                        $project_selection = new Project_selection();
                        $project_selection->where_related('student', $student);
                        $project_selection->where_related('task_set', $task_set);
                        $project_selection->get();

                        $can_continue = TRUE;

                        if ($project_selection->exists()) {
                            if ($project_selection->task_id != $task->id) {
                                $project_selection->task_id = $task->id;
                            } else {
                                $can_continue = FALSE;
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
                                    $this->messages->add_message($this->lang->line('projects_selection_success'), Messages::MESSAGE_TYPE_SUCCESS);
                                    redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
                                } else {
                                    $this->db->trans_rollback();
                                    $this->messages->add_message($this->lang->line('projects_selection_error_save_failed'), Messages::MESSAGE_TYPE_ERROR);
                                    redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
                                }
                            } else {
                                $this->db->trans_rollback();
                                $this->messages->add_message($this->lang->line('projects_selection_error_no_free_space'), Messages::MESSAGE_TYPE_ERROR);
                                redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->messages->add_message($this->lang->line('projects_selection_error_already_selected'), Messages::MESSAGE_TYPE_ERROR);
                            redirect(create_internal_url('projects/task/' . $task_set_id_url . '/' . $task_id_url));
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message($this->lang->line('projects_selection_error_task_not_found'), Messages::MESSAGE_TYPE_ERROR);
                        redirect(create_internal_url('projects/selection/' . $task_set_id_url));
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message($this->lang->line('projects_selection_error_selection_disabled'), Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('projects/selection/' . $task_set_id_url));
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message($this->lang->line('projects_selection_error_task_set_not_found'), Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('projects'));
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message($this->lang->line('projects_selection_error_no_active_course'), Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('projects'));
        }
    }

    private function get_task_sets(&$course, &$student) {
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
            $task_set->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set->include_related('solution/teacher', 'fullname');
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->where('content_type', 'project');
            $task_set->include_related('project_selection');
            $task_set->add_join_condition('`project_selections`.`student_id` = ? AND `project_selections`.`task_set_id` = `task_sets`.`id`', array($student->id));
            $task_set->include_related('project_selection/task');
            $task_set->order_by('publish_start_time', 'asc');
            $task_set->order_by('upload_end_time', 'asc');
            $task_set->order_by_with_overlay('name', 'asc');
            $task_set->get();
        }
        
        return $task_set;
    }
    
    private function get_task_set($task_set_id, &$course, &$student) {
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
            $task_set->add_join_condition('`solutions`.`student_id` = ?', array($student->id));
            $task_set->include_related('solution/teacher', 'fullname');
            $task_set->include_related_count('task', 'total_tasks');
            $task_set->where('content_type', 'project');
            $task_set->include_related('project_selection');
            $task_set->add_join_condition('`project_selections`.`student_id` = ? AND `project_selections`.`task_set_id` = `task_sets`.`id`', array($student->id));
            $task_set->include_related('project_selection/task');
            $task_set->order_by('publish_start_time', 'asc');
            $task_set->order_by('upload_end_time', 'asc');
            $task_set->order_by_with_overlay('name', 'asc');
            $task_set->get_by_id((int)$task_set_id);
        }
        
        return $task_set;
    }
    
    private function filter_valid_task_sets(Task_set $task_sets) {
        $output = array();
        
        $days = array(1=> 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        
        $minimum_next_time = date('U') + $this->smarty->cache_lifetime * 2;
                
        foreach($task_sets->all as $task_set) {
            $add = TRUE;
            if (is_null($task_set->solution_id)) {
                if (!is_null($task_set->publish_start_time)) {
                    if (strtotime($task_set->publish_start_time) > time()) {
                        $add = FALSE;
                        if (strtotime($task_set->publish_start_time) < $minimum_next_time) { $minimum_next_time = strtotime($task_set->publish_start_time); }
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