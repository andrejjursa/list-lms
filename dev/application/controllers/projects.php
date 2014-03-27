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
        if ($this->_is_cache_enabled()) {
            $this->smarty->caching = Smarty::CACHING_LIFETIME_SAVED;
        }
        if (!$this->_is_cache_enabled() || !$this->parser->isCached($this->parser->find_view('frontend/projects/index.tpl'), $cache_id)) {
            $this->_initialize_student_menu();

            $this->_select_student_menu_pagetag('projects');
            
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
            
            $this->parser->add_css_file('frontend_projects.css');
            $this->parser->add_js_file('projects/list.js');
            $this->parser->assign(array('course' => $course));
        }
        $this->parser->parse('frontend/projects/index.tpl', array(), FALSE, $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : FALSE, $cache_id);
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