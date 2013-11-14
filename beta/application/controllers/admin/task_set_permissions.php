<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include(APPPATH . 'controllers/admin/task_sets.php');

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
    
    public function create_permission($task_set_id) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_set_permission[group_id]', 'lang:admin_task_sets_form_field_group_id', 'required');
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id((int)$task_set_id);
            if ($task_set->exists() && !is_null($task_set->course_id)) {
                $task_set_permission_data = $this->input->post('task_set_permission');
                $task_set_permission = new Task_set_permission();
                $task_set_permission->enabled = isset($task_set_permission_data['enabled']) ? 1 : 0;
                $task_set_permission->group_id = $task_set_permission_data['group_id'];
                $task_set_permission->room_id = intval($task_set_permission_data['room_id']) > 0 ? intval($task_set_permission_data['room_id']) : NULL;
                $task_set_permission->publish_start_time = preg_match(Task_sets::REGEXP_PATTERN_DATETYME, $task_set_permission_data['publish_start_time']) ? $task_set_permission_data['publish_start_time'] : NULL;
                $task_set_permission->upload_end_time = preg_match(Task_sets::REGEXP_PATTERN_DATETYME, $task_set_permission_data['upload_end_time']) ? $task_set_permission_data['upload_end_time'] : NULL;
                if ($task_set_permission->save($task_set)) {
                    $task_set_permissions = new Task_set_permission();
                    $task_set_permissions->where_related($task_set);
                    $task_set_permissions->where('group_id', $task_set_permission->group_id);
                    if ($task_set_permissions->count() > 1) {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_save_for_the_same_group', Messages::MESSAGE_TYPE_ERROR);
                    } else {
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:admin_task_set_permissions_success_message_saved', Messages::MESSAGE_TYPE_SUCCESS);
                        $this->_action_success();
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_save', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_find_task_set_or_course', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_task_set_permissions/new_permission/' . (int)$task_set_id));
        } else {
            $this->new_permission($task_set_id);
        }
    }

    public function edit_permission($task_set_id, $task_set_permission_id) {
        $task_set = new Task_set();
        $task_set->include_related('course');
        $task_set->include_related('course/period');
        $task_set->get_by_id((int)$task_set_id);
        $task_set_permission = new Task_set_permission();
        $task_set_permission->get_by_id((int)$task_set_permission_id);
        $this->inject_course_groups($task_set->course_id);
        $this->inject_course_group_rooms($task_set->course_id);
        $this->parser->add_js_file('admin_task_set_permissions/form.js');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->parse('backend/task_set_permissions/edit_permission.tpl', array('task_set' => $task_set, 'task_set_permission' => $task_set_permission));
    }
    
    public function update_permission($task_set_id, $task_set_permission_id) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_set_permission[group_id]', 'lang:admin_task_sets_form_field_group_id', 'required');
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id((int)$task_set_id);
            $task_set_permission = new Task_set_permission();
            $task_set_permission->get_by_id((int)$task_set_permission_id);
            if ($task_set->exists() && !is_null($task_set->course_id)) {
                if ($task_set_permission->exists() && $task_set_permission->is_related_to($task_set)) {
                    $task_set_permission_data = $this->input->post('task_set_permission');
                    $task_set_permission->enabled = isset($task_set_permission_data['enabled']) ? 1 : 0;
                    $task_set_permission->group_id = $task_set_permission_data['group_id'];
                    $task_set_permission->room_id = intval($task_set_permission_data['room_id']) > 0 ? intval($task_set_permission_data['room_id']) : NULL;
                    $task_set_permission->publish_start_time = preg_match(Task_sets::REGEXP_PATTERN_DATETYME, $task_set_permission_data['publish_start_time']) ? $task_set_permission_data['publish_start_time'] : NULL;
                    $task_set_permission->upload_end_time = preg_match(Task_sets::REGEXP_PATTERN_DATETYME, $task_set_permission_data['upload_end_time']) ? $task_set_permission_data['upload_end_time'] : NULL;
                    if ($task_set_permission->save()) {
                        $task_set_permissions = new Task_set_permission();
                        $task_set_permissions->where_related($task_set);
                        $task_set_permissions->where('group_id', $task_set_permission->group_id);
                        if ($task_set_permissions->count() > 1) {
                            $this->db->trans_rollback();
                            $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_save_for_the_same_group', Messages::MESSAGE_TYPE_ERROR);
                        } else {
                            $this->db->trans_commit();
                            $this->messages->add_message('lang:admin_task_set_permissions_success_message_saved', Messages::MESSAGE_TYPE_SUCCESS);
                            $this->_action_success();
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_save', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_find_task_set_permission_or_is_not_related_to_task_set', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_set_permissions_error_message_cant_find_task_set_or_course', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_task_set_permissions/edit_permission/' . (int)$task_set_id) . '/' . (int)$task_set_permission_id);
        } else {
            $this->edit_permission($task_set_id, $task_set_permission_id);
        }
    }
    
    public function delete_permission($task_set_id, $task_set_permission_id) {
        $output = new stdClass();
        $output->result = FALSE;
        $output->message = '';
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_set_id);
        $task_set_permission = new Task_set_permission();
        $task_set_permission->get_by_id((int)$task_set_permission_id);
        if ($task_set->exists() && !is_null($task_set->course_id)) {
            if ($task_set_permission->exists() && $task_set_permission->is_related_to($task_set)) {
                $task_set_permission->delete();
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $output->result = TRUE;
                    $output->message = $this->lang->line('admin_task_set_permissions_success_message_deleted');
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $output->message = $this->lang->line('admin_task_set_permissions_error_message_delete_error');
                }
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_set_permissions_error_message_cant_find_task_set_permission_or_is_not_related_to_task_set');
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('lang:admin_task_set_permissions_error_message_cant_find_task_set_or_course');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
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