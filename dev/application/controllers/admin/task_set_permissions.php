<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Room_set_permissions controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Task_set_permissions extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_load_teacher_langfile('task_sets');
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_set_id);
        $task_set_permissions = new Task_set_permission();
        if ($task_set->exists()) {
            $task_set_permissions->include_related('group');
            $task_set_permissions->include_related('room');
            $task_set_permissions->where_related($task_set);
            $task_set_permissions->order_by('publish_start_time', 'asc');
            $task_set_permissions->get_iterated();
        }
        
        $this->parser->parse('backend/task_set_permissions/index.tpl', array(
            'task_set' => $task_set,
            'task_set_permissions' => $task_set_permissions,
        ));
    }
    
    public function new_permission($task_set_id) {
        $task_set = new Task_set();
        $task_set->include_related('course');
        $task_set->include_related('course/period');
        $task_set->get_by_id((int)$task_set_id);
        $this->inject_course_groups($task_set->course_id);
        $this->inject_course_group_rooms($task_set->course_id);
        $this->parser->add_js_file('admin_task_set_permissions/form.js');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->parse('backend/task_set_permissions/new_permission.tpl', array('task_set' => $task_set));
    }
    
    private function inject_course_groups($course_id) {
        $groups = new Group();
        $groups->where_related('course', 'id', (int)$course_id);
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        
        $data = array('' => '');
        foreach ($groups as $group) {
            $data[$group->id] = $this->lang->text($group->name);
        }
        
        $this->parser->assign('groups', $data);
    }
    
    private function inject_course_group_rooms($course_id) {
        $rooms = new Room();
        $rooms->where_related('group/course', 'id', (int)$course_id);
        $rooms->include_related('group', '*', TRUE, TRUE);
        $rooms->order_by_related('group', 'id', 'asc');
        $rooms->order_by('time_day', 'asc')->order_by('time_begin', 'asc');
        $rooms->order_by_with_constant('name', 'asc');
        $rooms->get_iterated();
        
        $days = get_days();
        
        $data = array();
        foreach ($rooms as $room) {
            if ($room->group->exists()) {
                $data[$room->group->id][] = array(
                    'value' => $room->id,
                    'text' => $this->lang->text($room->name) . ' (' . $days[$room->time_day] . ': ' . is_time($room->time_begin) . ' - ' . is_time($room->time_end) . ')',
                );
            }
        }
        
        $this->parser->assign('all_rooms', $data);
    }
    
}