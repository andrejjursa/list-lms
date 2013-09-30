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
        
    }
    
    private function inject_course_group_rooms() {
        $rooms = new Room();
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
    
    private function inject_course_task_set_types() {
        $task_set_types = new Task_set_type();
        $task_set_types->include_related('course', '*', TRUE, TRUE);
        $task_set_types->order_by_related('course', 'id', 'true');
        $task_set_types->order_by_with_constant('name', 'asc');
        $task_set_types->get_iterated();
        
        $data = array();
        foreach ($task_set_types as $task_set_type) {
            if ($task_set_type->course->exists()) {
                $data[$task_set_type->course->id][] = array(
                    'value' => $task_set_type->id,
                    'text' => $this->lang->text($task_set_type->name),
                );
            }
        }
        
        $this->parser->assign('all_task_set_types', $data);
    }
    
}