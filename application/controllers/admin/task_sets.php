<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Task sets controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Task_sets extends LIST_Controller {
	
    const STORED_FILTER_SESSION_NAME = 'admin_task_sets_filter_data';
    const REGEXP_PATTERN_DATETYME = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';

    const STORED_SORTING_FILTER_SESSION_NAME = 'admin_task_sets_sorting_filter_data';

    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }

    public function index() {
        $this->_select_teacher_menu_pagetag('task_sets');
        $this->load->helper('task_sets');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_task_sets/list.js');
        $this->parser->add_js_file('admin_task_sets/form.js');
        $this->parser->add_css_file('admin_task_sets.css');
        $this->inject_courses();
        $this->inject_stored_task_sets_filter();
        $this->inject_task_set_types();
        $this->inject_test_types();
        $this->inject_course_groups();
        $this->inject_course_group_rooms();
        $this->inject_course_task_set_types();
        $this->parser->parse('backend/task_sets/index.tpl');
    }

    public function sorting() {
        $this->_select_teacher_menu_pagetag('task_sets_sorting');
        $this->parser->add_css_file('admin_task_sets.css');
        $this->parser->add_js_file('admin_task_sets/sorting/list.js');
        $this->inject_courses();
        $this->inject_stored_sorting_filter();
        $this->parser->parse('backend/task_sets/sorting.tpl');
    }

    public function get_all_task_sets_sorting() {
        $filter = $this->input->post('filter');
        $this->store_sorting_filter($filter);
        $this->inject_stored_sorting_filter();

        $course = new Course();
        $course->get_by_id(isset($filter['course']) ? (int)$filter['course'] : 0);

        if ($course->exists()) {
            $all_task_set_type_ids = array();
            $all_task_set_ids = array();

            $task_set_types = new Task_set_type();
            $task_set_types->where_related($course);
            $task_set_types->get_iterated();

            $all_task_sets = array();

            foreach ($task_set_types as $task_set_type) {
                $all_task_sets[$task_set_type->id] = array(
                    'name' => $task_set_type->name,
                    'items' => array(),
                );
                $all_task_set_type_ids[] = $task_set_type->id;
            }

            $task_sets = new Task_set();
            $task_sets->where_related($course);
            $task_sets->where('content_type', 'task_set');
            $task_sets->order_by('sorting', 'asc');
            $task_sets->get_iterated();

            foreach ($task_sets as $task_set) {
                if (isset($all_task_sets[$task_set->task_set_type_id])) {
                    $all_task_sets[$task_set->task_set_type_id]['items'][] = array(
                        'id' => $task_set->id,
                        'name' => $task_set->name,
                    );
                    $all_task_set_ids[] = $task_set->id;
                }
            }

            $this->lang->init_overlays('task_set_types', $all_task_set_type_ids, array('name'));
            $this->lang->init_overlays('task_sets', $all_task_set_ids, array('name'));

            $this->parser->assign('all_task_sets', $all_task_sets);
        }

        $this->parser->assign('course', $course);

        $this->parser->parse('backend/task_sets/all_task_sets_sorting.tpl');
    }

    public function update_sorting() {
        $output = new stdClass();
        $output->status = false;
        $output->message = '';
        $output->content = '';

        $task_set_type_id = $this->input->post('task_set_type_id');
        $course_id = $this->input->post('course_id');
        $order = $this->input->post('order');


        $this->_transaction_isolation();
        $this->db->trans_begin();

        $course = new Course((int)$course_id);
        $task_set_type = new Task_set_type((int)$task_set_type_id);

        if ($course->exists() && $task_set_type->exists()) {
            $task_sets = new Task_set();
            $task_sets->where_related_course($course);
            $task_sets->where_related_task_set_type($task_set_type);
            $task_sets->where('content_type', 'task_set');

            $task_sets->hide_updated_field();
            $task_sets->update('sorting', count($order) + 1);
            $task_sets->show_updated_field();

            $sort_order = 1;
            foreach ($order as $task_set_id) {
                $task_set = new Task_set();
                $task_set->where_related_course($course);
                $task_set->where_related_task_set_type($task_set_type);
                $task_set->where('id', $task_set_id);
                $task_set->hide_updated_field();
                $task_set->update('sorting', $sort_order++);
                $task_set->show_updated_field();
            }

            $task_sets = new Task_set();
            $task_sets->select(array('id, name'));
            $task_sets->where_related_course($course);
            $task_sets->where_related_task_set_type($task_set_type);
            $task_sets->where('content_type', 'task_set');
            $task_sets->order_by('sorting', 'asc');
            $task_sets->get_iterated();

            $this->db->trans_commit();
            $output->status = true;
            $output->message = $this->lang->line('admin_task_sets_sorting_update_successful');
            $task_set_type_name = $this->lang->text($this->lang->get_overlay_with_default('task_set_types', $task_set_type_id, 'name', $task_set_type->name));
            $course_name = $this->lang->text($this->lang->get_overlay_with_default('courses', $course_id, 'name', $course->name));
            $output->message = sprintf($output->message, $task_set_type_name, $course_name);

            $items = array();

            foreach ($task_sets as $task_set) {
                $items[] = array(
                    'id' => $task_set->id,
                    'name' => $task_set->name,
                );
            }

            $output->content = $this->parser->parse('backend/task_sets/partial/single_type_task_sets_sorting.tpl', array(
                'task_set_type_id' => $task_set_type_id,
                'course' => $course,
                'task_set_type_data' => array(
                    'items' => $items,
                ),
            ), true);

            $this->_action_success();
        } else{
            $this->db->trans_rollback();
            $output->status = false;
            $output->message = $this->lang->line('admin_task_sets_sorting_error_course_or_task_set_type_not_found');
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }

    public function new_task_set_form() {
        $this->inject_courses();
        $this->inject_test_types();
        $url_params = $this->uri->ruri_to_assoc(3);
        $this->parser->parse('backend/task_sets/new_task_set_form.tpl', array('url_params' => $url_params));
    }
    
    public function get_task_set_types($course_id, $selected_id = NULL, $task_set_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $query = $course->task_set_type->order_by('name', 'asc')->get_raw();
        
        $task_set_types = array('' => '');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $task_set_types[$row->id] = $row->name;
        }}
        
        $query->free_result();
        
        $task_set = new Task_set();
        $task_set->where_related_course('id', $course_id)->get_by_id($task_set_id);
        
        $this->parser->parse('backend/task_sets/task_set_types_options.tpl', array('task_set_types' => $task_set_types, 'task_set' => $task_set, 'selected_id' => $selected_id));
    }
    
    public function get_task_set_groups($course_id, $selected_id = NULL, $task_set_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $query = $course->group->order_by('name', 'asc')->get_raw();
        
        $groups = array('' => '');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $groups[$row->id] = $row->name;
        }}
        
        $query->free_result();
        
        $task_set = new Task_set();
        $task_set->where_related_course('id', $course_id)->get_by_id($task_set_id);
        
        $this->parser->parse('backend/task_sets/task_set_groups_options.tpl', array('groups' => $groups, 'task_set' => $task_set, 'selected_id' => $selected_id));
    }
    
    public function get_course_groups($course_id, $selected_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $query = $course->group->order_by('name', 'asc')->get_raw();
        
        $groups = array('' => '', 'none' => 'lang:admin_task_sets_filter_option_without_group');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $groups[$row->id] = $row->name;
        }}
        
        $query->free_result();
        
        $this->parser->parse('backend/task_sets/task_set_groups_filter_options.tpl', array('groups' => $groups, 'selected_id' => $selected_id));
    }


    public function get_task_set_group_rooms($course_id, $group_id, $selected_id = NULL, $task_set_id = NULL) {
        $course = new Course();
        $course->get_by_id($course_id);
        $course->group->get_by_id($group_id);
        $query = $course->group->room->order_by('name', 'asc')->get_raw();
        
        $rooms = array('' => '');
        
        $days = get_days();
        include (APPPATH . 'third_party/Smarty/plugins/modifier.is_time.php');
        
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $rooms[$row->id] = $this->lang->text($row->name) . ' (' . $days[$row->time_day] . ': ' . smarty_modifier_is_time($row->time_begin) . ' - ' . smarty_modifier_is_time($row->time_end) . ')';
        }}
        
        $query->free_result();
        
        $task_set = new Task_set();
        $task_set->where_related_course('id', $course_id)->get_by_id($task_set_id);
        
        $this->parser->parse('backend/task_sets/task_set_group_rooms_options.tpl', array('rooms' => $rooms, 'task_set' => $task_set, 'selected_id' => $selected_id));
    }
    
    public function get_all_task_sets() {
        $fields_config = array(
            array('name' => 'created', 'caption' => 'lang:common_table_header_created'),
            array('name' => 'updated', 'caption' => 'lang:common_table_header_updated'),
            array('name' => 'name', 'caption' => 'lang:admin_task_sets_table_header_name'),
            array('name' => 'content_type', 'caption' => 'lang:admin_task_sets_table_header_content_type'),
            array('name' => 'course', 'caption' => 'lang:admin_task_sets_table_header_course'),
            array('name' => 'group', 'caption' => 'lang:admin_task_sets_table_header_group'),
            array('name' => 'task_set_type', 'caption' => 'lang:admin_task_sets_table_header_task_set_type'),
            array('name' => 'tasks', 'caption' => 'lang:admin_task_sets_table_header_tasks'),
            array('name' => 'published', 'caption' => 'lang:admin_task_sets_table_header_published'),
            array('name' => 'publish_start_time', 'caption' => 'lang:admin_task_sets_table_header_publish_start_time'),
            array('name' => 'upload_end_time', 'caption' => 'lang:admin_task_sets_table_header_upload_end_time'),
            array('name' => 'project_selection_deadline', 'caption' => 'lang:admin_task_sets_table_header_project_selection_deadline'),
        );
        $filter = $this->input->post('filter');
        if (!array_key_exists('hide_old', $filter)) {
            $filter['hide_old'] = 0;
        }
        $this->store_task_sets_filter($filter);
        $this->inject_stored_task_sets_filter();
        $task_sets = new Task_set();
        $task_sets->select('*');
        $task_sets->select_subquery('(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = ${parent}.`id` AND `tsp`.`enabled` = 1)', 'task_set_permission_count');
        //$task_sets->include_related_count('task_set_permission');
        //$task_sets->add_join_condition('`task_set_permissions`.`enabled` = 1');
        $task_sets->include_related('course', 'name', TRUE);
        $task_sets->include_related('course/period', 'name', TRUE);
        $task_sets->include_related('group', 'name', TRUE);
        $task_sets->include_related('task_set_type', 'name', TRUE);
        $task_sets->include_related_count('task');
        $task_sets->include_related_count('comment');
        if (isset($filter['course']) && intval($filter['course']) > 0) {
            $task_sets->where_related_course('id', intval($filter['course']));
            if (isset($filter['group']) && $filter['group'] == 'none') {
                $task_sets->where_related_group('id', NULL);
                $task_sets->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
            } else if (isset($filter['group']) && intval($filter['group']) > 0) {
                $task_sets->group_start();
                    $task_sets->or_group_start();
                        $task_sets->where_related_group('id', intval($filter['group']));
                        $task_sets->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
                    $task_sets->group_end();
                    $task_sets->or_group_start();
                        $task_sets->where_related('task_set_permission/group', 'id', intval($filter['group']));
                        $task_sets->where_related('task_set_permission', 'enabled', 1);
                    $task_sets->group_end();
                $task_sets->group_end();
            }
        }
        if (isset($filter['hide_old']) && boolval($filter['hide_old'])) {
            $old = date('Y-m-d H:i:s', strtotime('now -2 weeks'));
            $task_sets->group_start();
                $task_sets->group_start();
                    $task_sets->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
                    $task_sets->group_start();
                        $task_sets->where('upload_end_time', null);
                        $task_sets->or_where('upload_end_time >', $old);
                    $task_sets->group_end();
                $task_sets->group_end();
                $task_sets->or_group_start();
                    $task_sets->where('content_type !=', 'project');
                    $task_sets->where_subquery('0 <', '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
                    $task_sets->group_start();
                        $task_sets->where_subquery('0 <', '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1 AND `tsp`.`upload_end_time` IS NULL)');
                        $task_sets->or_where_subquery('0 <', '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1 AND `tsp`.`upload_end_time` > \'' . $old . '\')');
                    $task_sets->group_end();
                $task_sets->group_end();
            $task_sets->group_end();
        }
        if (isset($filter['task_set_type']) && intval($filter['task_set_type']) > 0) {
            $task_sets->where_related_task_set_type('id', intval($filter['task_set_type']));
        }
        if (isset($filter['tasks']) && is_numeric($filter['tasks']) && intval($filter['tasks']) == 0) {
            $task_sets->where_has_no_tasks();
        } else if (isset($filter['tasks']) && is_numeric($filter['tasks']) && intval($filter['tasks']) == 1) {
            $task_sets->where_has_tasks();
        }
        if (isset($filter['name']) && trim($filter['name']) != '') {
            $name_value = trim($filter['name']);
            $task_sets->like_with_overlay('name', $name_value);
        }
        $order_by_direction = $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] == 'created') {
            $task_sets->order_by('created', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'updated') {
            $task_sets->order_by('updated', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'name') {
            $task_sets->order_by_with_overlay('name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'course') {
            $task_sets->order_by_related('course/period', 'sorting', $order_by_direction);
            $task_sets->order_by_related_with_constant('course', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'group') {
            $task_sets->order_by_related_with_constant('group', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'task_set_type') {
            $task_sets->order_by_related_with_constant('task_set_type', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'tasks') {
            $task_sets->order_by('task_count', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'published') {
            $task_sets->order_by('published', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'upload_end_time') {
            $task_sets->order_by('upload_end_time', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'publish_start_time') {
            $task_sets->order_by('publish_start_time', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'project_selection_deadline') {
            $task_sets->order_by('project_selection_deadline', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'content_type') {
            $task_sets->order_by('content_type', $order_by_direction);
        }
        $task_sets->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('task_sets', $task_sets->all_to_array(), array('name'));
        $opened_task_set = new Task_set();
        $opened_task_set->get_as_open();
        $this->parser->parse('backend/task_sets/table_content.tpl', array('task_sets' => $task_sets, 'opened_task_set' => $opened_task_set, 'fields_config' => $fields_config));
    }

    public function create() {
        $this->load->library('form_validation');
        
        $task_set_data = $this->input->post('task_set');

        $this->form_validation->set_rules('task_set[content_type]', 'lang:admin_task_sets_form_field_content_type', 'required');
        
        if (isset($task_set_data['content_type']) && $task_set_data['content_type'] == 'task_set') {
            $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
            $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
            $this->form_validation->set_rules('task_set[task_set_type_id]', 'lang:admin_task_sets_form_field_task_set_type_id', 'required|exists_in_table[task_set_types.id]');
            $this->form_validation->set_rules('task_set[points_override]', 'lang:admin_task_sets_form_field_points_override', 'greater_than_equal[0]');
            if (isset($task_set_data['enable_tests_scoring'])) {
                $this->form_validation->set_rules('task_set[test_min_needed]', 'lang:admin_task_sets_form_field_test_min_needed', 'greater_than_equal[0]');
                $this->form_validation->set_rules('task_set[test_max_allowed]', 'lang:admin_task_sets_form_field_test_max_allowed', 'greater_than_field_or_equal[task_set[test_min_needed]]');
            }
            $this->form_validation->set_rules('task_set[deadline_notification_emails_handler]', 'lang:admin_task_sets_form_field_deadline_notification_emails_handler', 'required');
            if (isset($task_set_data['deadline_notification_emails_handler']) && $task_set_data['deadline_notification_emails_handler'] == 2) {
                $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'required|valid_emails');
            } else {
                $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'zero_or_more_valid_emails');
            }
            $this->form_validation->set_rules('task_set[publish_start_time]', 'lang:admin_task_sets_form_field_publish_start_time', 'datetime');
            $this->form_validation->set_rules('task_set[upload_end_time]', 'lang:admin_task_sets_form_field_upload_end_time', 'datetime');
        } else {
            $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
            $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
            $this->form_validation->set_rules('task_set[points_override]', 'lang:admin_task_sets_form_field_points_override', 'required|numeric|greater_than_equal[0]');
            $this->form_validation->set_rules('task_set[deadline_notification_emails_handler]', 'lang:admin_task_sets_form_field_deadline_notification_emails_handler', 'required');
            if (isset($task_set_data['deadline_notification_emails_handler']) && $task_set_data['deadline_notification_emails_handler'] == 2) {
                $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'required|valid_emails');
            } else {
                $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'zero_or_more_valid_emails');
            }
            $this->form_validation->set_rules('task_set[publish_start_time]', 'lang:admin_task_sets_form_field_publish_start_time', 'datetime');
            $this->form_validation->set_rules('task_set[upload_end_time]', 'lang:admin_task_sets_form_field_upload_end_time', 'required|datetime');
            $this->form_validation->set_rules('task_set[project_selection_deadline]', 'lang:admin_task_sets_form_field_project_selection_deadline', 'required|datetime');
        }
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $task_set = new Task_set();
            $task_set->from_array($task_set_data, array('content_type', 'name', 'course_id', 'task_set_type_id', 'published', 'allowed_file_types', 'internal_comment', 'test_priority'));
            $task_set->group_id = intval($task_set_data['group_id']) > 0 ? intval($task_set_data['group_id']) : NULL;
            $task_set->room_id = intval($task_set_data['room_id']) > 0 ? intval($task_set_data['room_id']) : NULL;
            $task_set->publish_start_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['publish_start_time']) ? $task_set_data['publish_start_time'] : NULL;
            $task_set->upload_end_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['upload_end_time']) ? $task_set_data['upload_end_time'] : NULL;
            $task_set->project_selection_deadline = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['project_selection_deadline']) ? $task_set_data['project_selection_deadline'] : NULL;
            $task_set->comments_enabled = isset($task_set_data['comments_enabled']) ? (bool)intval($task_set_data['comments_enabled']) : FALSE;
            $task_set->comments_moderated = isset($task_set_data['comments_moderated']) ? (bool)intval($task_set_data['comments_moderated']) : FALSE;
            $task_set->points_override = isset($task_set_data['points_override_enabled']) && (bool)$task_set_data['points_override_enabled'] ? floatval($task_set_data['points_override']) : NULL;
            $task_set->allowed_test_types = isset($task_set_data['allowed_test_types']) && is_array($task_set_data['allowed_test_types']) ? implode(',', $task_set_data['allowed_test_types']) : '';
            $task_set->enable_tests_scoring = isset($task_set_data['enable_tests_scoring']) ? 1 : 0;
            $task_set->deadline_notification_emails = $task_set_data['deadline_notification_emails'];
            $task_set->deadline_notification_emails_handler = $task_set_data['deadline_notification_emails_handler'];
            $task_set->deadline_notified = is_null($task_set->upload_end_time) ? 1 : 0;
            if ($task_set->enable_tests_scoring == 1) {
                $task_set->test_min_needed = isset($task_set_data['test_min_needed']) ? intval($task_set_data['test_min_needed']) : 0;
                $task_set->test_max_allowed = isset($task_set_data['test_max_allowed']) ? intval($task_set_data['test_max_allowed']) : 0;
            }
            if ($task_set->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_task_sets_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
                if ($this->input->post('open_task_set') == 'true') {
                    redirect(create_internal_url('admin_task_sets/new_task_set_form/force_open_task_set_id/' . (int)$task_set->id));
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_sets_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_task_sets/new_task_set_form'));
        } else {
            $this->new_task_set_form();
        }
        $this->db->trans_rollback();
    }
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('task_sets');
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : intval($this->input->post('task_set_id'));
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $ps_data = array();
        $nps_data = array();
        
        if ($task_set->exists() && $task_set->content_type == 'project') {
            $project_selections = new Project_selection();
            $project_selections->select('*');
            $project_selections->include_related('student', array('fullname', 'email'));
            $project_selections->where_related($task_set);
            $project_selections->include_related('task', 'name');
            $project_selections->include_related('task/task_set', 'id');
            $project_selections->where_related('task/task_set', 'id', $task_set->id);
            $project_selections->order_by('task_task_task_set_rel.sorting', 'ASC');
            $project_selections->order_by_related_as_fullname('student', 'fullname', 'asc');
            $project_selections->get();
            
            if ($project_selections->exists()) { foreach ($project_selections->all as $project_selection) {
                $ps_data[$project_selection->task_id][] = $project_selection;
            }}
            
            $project_selections->select_func('COUNT', '@id', 'count');
            $project_selections->where('task_set_id', 'participant_course_task_sets.id', false);
            $project_selections->where_related('student', 'id', '${parent}.id');
            
            $students = new Student();
            $students->where_related('participant/course/task_set', $task_set);
            $students->where_related('participant', 'allowed', 1);
            $students->where_subquery(0, $project_selections);
            $students->order_by_as_fullname('fullname', 'asc');
            $students->get();
            
            if ($students->exists()) { $nps_data = $students->all; }
        }
        
        $this->_add_tinymce4();
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_task_sets/edit.js');
        $this->parser->add_js_file('admin_task_sets/form.js');
        $this->parser->add_css_file('admin_task_sets.css');
        $this->inject_courses();
        $this->inject_languages();
        $this->inject_test_types();
        $this->inject_course_groups();
        $this->inject_course_group_rooms();
        $this->inject_course_task_set_types();
        $this->parser->parse('backend/task_sets/edit.tpl', array('task_set' => $task_set, 'project_selections' => $ps_data, 'not_project_selections' => $nps_data));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $task_set_data = $this->input->post('task_set');
                
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set_id = intval($this->input->post('task_set_id'));
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        if ($task_set->exists()) {
            if (isset($task_set_data['content_type']) && $task_set_data['content_type'] == 'task_set') {
                $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
                $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
                $this->form_validation->set_rules('task_set[task_set_type_id]', 'lang:admin_task_sets_form_field_task_set_type_id', 'required|exists_in_table[task_set_types.id]');
                $this->form_validation->set_rules('task_set[points_override]', 'lang:admin_task_sets_form_field_points_override', 'greater_than_equal[0]');
                if (isset($task_set_data['enable_tests_scoring'])) {
                    $this->form_validation->set_rules('task_set[test_min_needed]', 'lang:admin_task_sets_form_field_test_min_needed', 'greater_than_equal[0]');
                    $this->form_validation->set_rules('task_set[test_max_allowed]', 'lang:admin_task_sets_form_field_test_max_allowed', 'greater_than_field_or_equal[task_set[test_min_needed]]');
                }
                $this->form_validation->set_rules('task_set[deadline_notification_emails_handler]', 'lang:admin_task_sets_form_field_deadline_notification_emails_handler', 'required');
                if (isset($task_set_data['deadline_notification_emails_handler']) && $task_set_data['deadline_notification_emails_handler'] == 2) {
                    $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'required|valid_emails');
                } else {
                    $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'zero_or_more_valid_emails');
                }
                $this->form_validation->set_rules('task_set[publish_start_time]', 'lang:admin_task_sets_form_field_publish_start_time', 'datetime');
                $this->form_validation->set_rules('task_set[upload_end_time]', 'lang:admin_task_sets_form_field_upload_end_time', 'datetime');
            } else {
                $this->form_validation->set_rules('task_set[name]', 'lang:admin_task_sets_form_field_name', 'required');
                $this->form_validation->set_rules('task_set[course_id]', 'lang:admin_task_sets_form_field_course_id', 'required|exists_in_table[courses.id]');
                $this->form_validation->set_rules('task_set[points_override]', 'lang:admin_task_sets_form_field_points_override', 'required|numeric|greater_than_equal[0]');
                $this->form_validation->set_rules('task_set[deadline_notification_emails_handler]', 'lang:admin_task_sets_form_field_deadline_notification_emails_handler', 'required');
                if (isset($task_set_data['deadline_notification_emails_handler']) && $task_set_data['deadline_notification_emails_handler'] == 2) {
                    $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'required|valid_emails');
                } else {
                    $this->form_validation->set_rules('task_set[deadline_notification_emails]', 'lang:admin_task_sets_form_field_deadline_notification_emails', 'zero_or_more_valid_emails');
                }
                $this->form_validation->set_rules('task_set[publish_start_time]', 'lang:admin_task_sets_form_field_publish_start_time', 'datetime');
                $this->form_validation->set_rules('task_set[upload_end_time]', 'lang:admin_task_sets_form_field_upload_end_time', 'required|datetime');
                $this->form_validation->set_rules('task_set[project_selection_deadline]', 'lang:admin_task_sets_form_field_project_selection_deadline', 'required|datetime');
            }

            $tasks = $task_set->task->include_join_fields()->order_by('`task_task_set_rel`.`sorting`', 'asc')->get();
            $tasks_join_fields_data = $this->input->post('task_join_field');
            if ($tasks->exists()) { foreach ($tasks->all as $task) {
                if (isset($tasks_join_fields_data[$task->id])) {
                    if (!isset($tasks_join_fields_data[$task->id]['delete'])) {
                        if ($task_set->content_type == 'task_set') {
                            $this->form_validation->set_rules('task_join_field[' . intval($task->id) . '][points_total]', 'lang:admin_task_sets_form_field_task_points_total', 'required|number|greater_than_equal[0]');
                            $this->form_validation->set_rules('task_join_field[' . intval($task->id) . '][test_max_points]', 'lang:admin_task_sets_form_field_task_test_max_points', 'required|number|greater_than_equal[0]');
                            $this->form_validation->set_rules('task_join_field[' . intval($task->id) . '][test_min_points]', 'lang:admin_task_sets_form_field_task_test_min_points', 'required|number|less_than_field_or_equal[task_join_field[' . intval($task->id) . '][test_max_points]]');
                        } else {
                            $this->form_validation->set_rules('task_join_field[' . intval($task->id) . '][max_projects_selections]', 'lang:admin_task_sets_form_field_task_max_projects_selections', 'required|integer|greater_than[0]');
                        }
                    }
                }
            }}
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_task_sets'));
            die();
        }
        
        if ($this->form_validation->run()) {   
            $task_set_upload_end_time = $task_set->upload_end_time;
            $task_set->from_array($task_set_data, array('name', 'course_id', 'task_set_type_id', 'published', 'allowed_file_types', 'internal_comment', 'test_priority'));
            $task_set->instructions = remove_base_url($task_set_data['instructions']);
            $task_set->group_id = intval($task_set_data['group_id']) > 0 ? intval($task_set_data['group_id']) : NULL;
            $task_set->room_id = intval($task_set_data['room_id']) > 0 ? intval($task_set_data['room_id']) : NULL;
            $task_set->publish_start_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['publish_start_time']) ? $task_set_data['publish_start_time'] : NULL;
            $task_set->upload_end_time = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['upload_end_time']) ? $task_set_data['upload_end_time'] : NULL;
            $task_set->project_selection_deadline = preg_match(self::REGEXP_PATTERN_DATETYME, $task_set_data['project_selection_deadline']) ? $task_set_data['project_selection_deadline'] : NULL;
            $task_set->comments_enabled = isset($task_set_data['comments_enabled']) ? (bool)intval($task_set_data['comments_enabled']) : FALSE;
            $task_set->comments_moderated = isset($task_set_data['comments_moderated']) ? (bool)intval($task_set_data['comments_moderated']) : FALSE;
            $task_set->points_override = isset($task_set_data['points_override_enabled']) && (bool)$task_set_data['points_override_enabled'] ? floatval($task_set_data['points_override']) : NULL;
            $task_set->allowed_test_types = isset($task_set_data['allowed_test_types']) && is_array($task_set_data['allowed_test_types']) ? implode(',', $task_set_data['allowed_test_types']) : '';
            $task_set->enable_tests_scoring = isset($task_set_data['enable_tests_scoring']) ? 1 : 0;
            $task_set->deadline_notification_emails = $task_set_data['deadline_notification_emails'];
            $task_set->deadline_notification_emails_handler = $task_set_data['deadline_notification_emails_handler'];
            if ($task_set->upload_end_time !== $task_set_upload_end_time) {
                $task_set->deadline_notified = 0;
            }
            if ($task_set->enable_tests_scoring == 1) {
                $task_set->test_min_needed = isset($task_set_data['test_min_needed']) ? intval($task_set_data['test_min_needed']) : 0;
                $task_set->test_max_allowed = isset($task_set_data['test_max_allowed']) ? intval($task_set_data['test_max_allowed']) : 0;
            }

            $overlay = $this->input->post('overlay');

            if ($tasks->exists()) {
                $tasks_sorting = array_flip(explode(',', $this->input->post('tasks_sorting')));
                foreach($tasks->all as $task) {
                    if (isset($tasks_join_fields_data[$task->id])) {
                        if (!isset($tasks_join_fields_data[$task->id]['delete'])) {
                            $task->set_join_field($task_set, 'sorting', $tasks_sorting[$task->id] + 1);
                            $task->set_join_field($task_set, 'points_total', floatval(@$tasks_join_fields_data[$task->id]['points_total']));
                            $task->set_join_field($task_set, 'test_min_points', floatval(@$tasks_join_fields_data[$task->id]['test_min_points']));
                            $task->set_join_field($task_set, 'test_max_points', floatval(@$tasks_join_fields_data[$task->id]['test_max_points']));
                            $task->set_join_field($task_set, 'bonus_task', (int)(bool)@$tasks_join_fields_data[$task->id]['bonus_task']);
                            $task->set_join_field($task_set, 'internal_comment', @$tasks_join_fields_data[$task->id]['internal_comment']);
                            $task->set_join_field($task_set, 'max_projects_selections', @$tasks_join_fields_data[$task->id]['max_projects_selections']);
                        } else {
                            $task_set->delete($task);
                        }
                    }
                }
            }

            if ($task_set->save() && $this->lang->save_overlay_array(remove_base_url_from_overlay_array($overlay, 'instructions')) && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_task_sets_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_sets_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_task_sets'));
        } else {
            $this->db->trans_rollback();
            $this->edit();
        }
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : 0;
        if ($task_set_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            
            $task_set_permissions = $task_set->task_set_permission->get();
            $task_set_permissions->delete_all();
            
            $task_set->delete();
            $this->lang->delete_overlays('task_sets', intval($task_set_id));
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->output->set_output(json_encode(TRUE)); 
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(FALSE));                
            }
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }

    public function change_publication_status($task_set_id = null) {
        $output = new stdClass();
        $output->message = '';
        $output->status = FALSE;

        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_set_id);

        if ($task_set->exists()) {
            $task_set->published = 1 - (int)$task_set->published;
            $task_set->save();
            $this->db->trans_commit();
            $output->message = sprintf($this->lang->line('admin_task_sets_publication_status_switched'), $this->lang->get_overlay_with_default('task_sets', $task_set->id, 'name', $task_set->name));
            $output->status = TRUE;
            $this->_action_success();
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_error_task_set_not_found');
        }

        $this->output->set_output(json_encode($output));
    }
    
    public function preview($task_set_id = null) {
        $this->_add_mathjax();
        $task_set = new Task_set();
        $task_set->get_by_id((int)$task_set_id);

        $this->_add_mathjax();
        $this->_add_prettify();
        $this->parser->add_js_file('admin_task_sets/preview.js');

        $this->parser->parse('backend/task_sets/preview.tpl', array('task_set' => $task_set));
    }

    public function open() {
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : 0;
        if ($task_set_id !== 0) {
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                $task_set->set_as_open();
            }
        }
        $this->_initialize_open_task_set();
        $this->parser->parse('partials/backend_general/open_task_set.tpl');
    }
    
    public function clone_task_set() {
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_id = isset($url['task_set_id']) ? intval($url['task_set_id']) : 0;
        $result = new stdClass();
        $result->result = FALSE;
        $result->message = $this->lang->line('admin_task_sets_error_task_set_not_found');
        if ($task_set_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                $new_task_set = $task_set->get_copy();
                $new_task_set->published = 0;
                if ($new_task_set->save()) {
                    $this->lang->clone_overlays('task_sets', $task_set->id, $new_task_set->id);
                    $tasks = new Task();
                    $tasks->include_join_fields();
                    $tasks->where_related_task_set($task_set);
                    $tasks->get_iterated();
                    foreach ($tasks as $task) {
                        $new_task_set->save_task($task);
                        $task->set_join_field($new_task_set, 'sorting', $task->join_sorting);
                        $task->set_join_field($new_task_set, 'points_total', $task->join_points_total);
                        $task->set_join_field($new_task_set, 'test_min_points', $task->join_test_min_points);
                        $task->set_join_field($new_task_set, 'test_max_points', $task->join_test_max_points);
                        $task->set_join_field($new_task_set, 'bonus_task', $task->join_bonus_task);
                        $task->set_join_field($new_task_set, 'max_projects_selections', $task->join_max_projects_selections);
                    }
                    $task_set_permissions = new Task_set_permission();
                    $task_set_permissions->where_related($task_set);
                    $task_set_permissions->get_iterated();
                    foreach ($task_set_permissions as $task_set_permission) {
                        $new_task_set_permission = $task_set_permission->get_copy();
                        $new_task_set_permission->save($new_task_set);
                    }
                    if ($this->db->trans_status()) {
                        $this->db->trans_commit();
                        $result->result = TRUE;
                        $result->message = $this->lang->line('admin_task_sets_success_task_set_cloned');
                        $this->_action_success();
                    } else {
                        $this->db->trans_rollback();
                        $result->message = $this->lang->line('admin_task_sets_error_task_set_cant_be_cloned');
                    }
                } else {
                    $this->db->trans_rollback();
                    $result->message = $this->lang->line('admin_task_sets_error_task_set_cant_be_cloned');
                }
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function comments($task_set_id) {
        $this->_select_teacher_menu_pagetag('task_sets');
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        if ($task_set->exists()) {
            if ((bool)$task_set->comments_enabled) {
                $this->_add_scrollTo();
                $this->parser->add_js_file('admin_task_sets/comments_list.js');
                $this->parser->add_css_file('admin_task_sets.css');
                $this->parser->parse('backend/task_sets/comments.tpl', array('task_set' => $task_set));
            } else {
                $this->messages->add_message('lang:admin_task_sets_comments_error_comments_disabled', Messages::MESSAGE_TYPE_ERROR);
                redirect(create_internal_url('admin_task_sets'));
            }
        } else {
            $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_task_sets'));
        }
    }
    
    public function all_comments($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $comments = array();
        if ($task_set->exists() && (bool)$task_set->comments_enabled) {
            $comments = Comment::get_comments_for_task_set($task_set);
        }
        $this->parser->parse('backend/task_sets/all_comments.tpl', array('task_set' => $task_set, 'comments' => $comments));
    }
    
    public function new_comment_form($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $this->parser->parse('backend/task_sets/new_comment_form.tpl', array('task_set' => $task_set));
    }
    
    public function my_comments_settings($task_set_id) {
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        if ($teacher->exists() && $task_set->exists()) {
            $this->parser->assign('subscribed', $teacher->is_related_to('comment_subscription', $task_set->id));
        }
        $this->parser->parse('backend/task_sets/my_comments_settings.tpl', array('task_set' => $task_set, 'teacher' => $teacher));
    }
    
    public function comments_unsubscribe($task_set_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        if ($teacher->exists() && $task_set->exists() && $teacher->is_related_to('comment_subscription', $task_set->id)) {
            $teacher->delete_comment_subscription($task_set);
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_task_sets_comments_my_settings_unsubscribe_success');
                $output->result = TRUE;
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_sets_comments_my_settings_unsubscribe_error');
                $output->result = FALSE;
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_my_settings_unsubscribe_error');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function comments_subscribe($task_set_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        if ($teacher->exists() && $task_set->exists() && $teacher->save(array('comment_subscription' => $task_set))) {
            $this->db->trans_commit();
            $output->message = $this->lang->line('admin_task_sets_comments_my_settings_subscribe_success');
            $output->result = TRUE;
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_my_settings_subscribe_error');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function delete_comment($task_set_id, $comment_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $comment = new Comment();
        $comment->get_by_id($comment_id);
        if ($comment->exists() && $task_set->exists() && $comment->is_related_to($task_set)) {
            $comment->delete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_task_sets_comments_success_delete_comment');
                $output->result = TRUE;
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_sets_comments_error_delete_comment');
                $output->result = FALSE;
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_error_delete_comment');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function approve_comment($task_set_id, $comment_id) {
        $output = new stdClass();
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $comment = new Comment();
        $comment->include_related('student', '*', TRUE, TRUE);
        $comment->include_related('teacher', '*', TRUE, TRUE);
        $comment->get_by_id($comment_id);
        if ($comment->exists() && $task_set->exists() && $comment->is_related_to($task_set) && !is_null($comment->student->id) && is_null($comment->teacher->id) && !(bool)$comment->approved) {
            $comment->approved = 1;
            if ($comment->save()) {
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_task_sets_comments_success_approve_comment');
                $output->result = TRUE;
                
                $this->_load_student_langfile('tasks');
                $all_students = $task_set->comment_subscriber_student;
                $all_students->where('id !=', $comment->student->id);
                $all_students->get();
                $this->_send_multiple_emails($all_students, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_student.tpl', array('task_set' => $task_set, 'student' => $comment->student, 'comment' => $comment));
                $all_teachers = $task_set->comment_subscriber_teacher;
                $all_teachers->where('id !=', $this->usermanager->get_teacher_id());
                $all_teachers->get();
                $this->_send_multiple_emails($all_teachers, 'lang:tasks_comments_email_subject_new_post', 'file:emails/frontend/comments/new_comment_teacher.tpl', array('task_set' => $task_set, 'student' => $comment->student, 'comment' => $comment));
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_task_sets_comments_error_approve_comment');
                $output->result = FALSE;
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_task_sets_comments_error_approve_comment');
            $output->result = FALSE;
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }

    public function post_comment($task_set_id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('comment[text]', 'lang:admin_task_sets_comments_form_field_text', 'required_no_html');
        if ($this->form_validation->run()) {
            $this->add_comment($task_set_id);
            redirect(create_internal_url('admin_task_sets/new_comment_form/' . $task_set_id));
        } else {
            $this->new_comment_form($task_set_id);
        }
    }
    
    public function reply_at_comment($task_set_id, $reply_at_id) {
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        $comment = new Comment();
        $comment->include_related('teacher', '*', TRUE, TRUE);
        $comment->include_related('student', '*', TRUE, TRUE);
        $comment->get_by_id($reply_at_id);
        $this->parser->add_css_file('admin_task_sets.css');
        $this->parser->parse('backend/task_sets/reply_at_comment.tpl', array('task_set' => $task_set, 'comment' => $comment));
    }
    
    public function post_comment_reply($task_set_id, $reply_at_id) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('comment[text]', 'lang:admin_task_sets_comments_form_field_text', 'required_no_html');
        if ($this->form_validation->run()) {
            $this->add_comment($task_set_id, $reply_at_id);
            redirect(create_internal_url('admin_task_sets/reply_at_comment/' . $task_set_id . '/' . $reply_at_id));
        } else {
            $this->reply_at_comment($task_set_id, $reply_at_id);
        }
    }
    
    public function select_project($task_set_id, $task_id, $student_id) {
        $output = new stdClass();
        $output->message = '';
        $output->status = FALSE;
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->where('content_type', 'project');
        $task_set->get_by_id((int)$task_set_id);
        
        $task = new Task();
        $task->get_by_id((int)$task_id);
        
        $student = new Student();
        $student->get_by_id((int)$student_id);
        
        $course = new Course();
        $course->where_related_task_set($task_set);
        $course->get();
        
        $participant = new Participant();
        $participant->where_related_course($course);
        $participant->where_related($student);
        $participant->where('allowed', 1);
        $participant->get();
        
        $project_selection = new Project_selection();
        $project_selection->where_related_student($student);
        $project_selection->where_related_task_set($task_set);
        $project_selection->get();
        
        if ($task_set->exists() && $task->exists() && $task_set->is_related_to($task) && $student->exists() && $course->exists() && $participant->exists()) {
            if ($task_set->get_student_files_count($student->id) == 0) {
                $all_project_selections = new Project_selection();
                $all_project_selections->where_related_task_set($task_set);
                $all_project_selections->where_related_task($task);
                $currently_selected = $all_project_selections->count();

                $jf_task = $task_set->task->include_join_fields()->get_by_id($task_id);
                $maximum_selections = (int)$jf_task->join_max_projects_selections;

                if ($project_selection->exists()) {
                    if (!$project_selection->is_related_to($task)) {
                        if ($currently_selected < $maximum_selections) {
                            $project_selection->save($task);
                            $output->status = TRUE;
                            $output->message = $this->lang->line('admin_task_sets_project_selection_success');
                        } else {
                            $output->message = $this->lang->line('admin_task_sets_project_selection_no_room');
                        }
                    } else {
                        $output->message = $this->lang->line('admin_task_sets_project_selection_already_selected');
                    }
                } else {
                    if ($currently_selected < $maximum_selections) {
                        $project_selection->save(array(
                            'student' => $student,
                            'task_set' => $task_set,
                            'task' => $task,
                        ));
                        $output->status = TRUE;
                        $output->message = $this->lang->line('admin_task_sets_project_selection_success');
                    } else {
                        $output->message = $this->lang->line('admin_task_sets_project_selection_no_room');
                    }
                }
            } else {
                $output->message = $this->lang->line('admin_task_sets_project_selection_already_submited_solutions');
            }
        } else {
            $output->message = $this->lang->line('admin_task_sets_project_selection_cant_find_data');
        }
        
        if ($output->status) {
            $this->db->trans_commit();
            $this->_action_success();
        } else {
            $this->db->trans_rollback();
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }

    private function add_comment($task_set_id, $reply_at_id = NULL) {
        $comment_data = $this->input->post('comment');
        if (isset($comment_data['task_set_id']) && $comment_data['task_set_id'] == $task_set_id) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if ($task_set->exists()) {
                if ((bool)$task_set->comments_enabled) {
                    $save_array = array();
                    $save_array['task_set'] = $task_set;
                    if (isset($comment_data['reply_at_id']) && $comment_data['reply_at_id'] == $reply_at_id) {
                        $reply_at = new Comment();
                        $reply_at->get_by_id($reply_at_id);
                        if ($reply_at->exists()) {
                            if ($reply_at->task_set_id == $task_set_id) {
                                $save_array['reply_at'] = $reply_at;
                            } else {
                                $this->db->trans_rollback();
                                $this->messages->add_message('lang:admin_task_sets_comments_error_reply_at_comment_from_different_task_set', Messages::MESSAGE_TYPE_ERROR);
                                return;
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->messages->add_message('lang:admin_task_sets_comments_error_reply_at_comment_not_exists', Messages::MESSAGE_TYPE_ERROR);
                            return;
                        }
                    }
                    $teacher = new Teacher();
                    $teacher->get_by_id($this->usermanager->get_teacher_id());
                    $save_array['teacher'] = $teacher;
                    
                    $comment = new Comment();
                    $comment->text = strip_tags($comment_data['text'], '<a><strong><em><span>');
                    $comment->approved = 1;
                    if ($comment->save($save_array)) {
                        $this->db->trans_commit();
                        $this->messages->add_message('lang:admin_task_sets_comments_save_successfully', Messages::MESSAGE_TYPE_SUCCESS);
                        
                        $all_students = $task_set->comment_subscriber_student;
                        $all_students->get();
                        $this->_send_multiple_emails($all_students, 'lang:admin_task_sets_comments_email_subject_new_post', 'file:emails/backend/comments/new_comment_student.tpl', array('task_set' => $task_set, 'teacher' => $teacher, 'comment' => $comment));
                        $all_teachers = $task_set->comment_subscriber_teacher;
                        $all_teachers->where('id !=', $this->usermanager->get_teacher_id());
                        $all_teachers->get();
                        $this->_send_multiple_emails($all_teachers, 'lang:admin_task_sets_comments_email_subject_new_post', 'file:emails/backend/comments/new_comment_teacher.tpl', array('task_set' => $task_set, 'teacher' => $teacher, 'comment' => $comment));
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message('lang:admin_task_sets_comments_error_save_failed', Messages::MESSAGE_TYPE_ERROR);
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_task_sets_comments_error_comments_disabled', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
        } else {
            $this->messages->add_message('lang:admin_task_sets_error_task_set_not_found', Messages::MESSAGE_TYPE_ERROR);
        }
    }

    private function inject_courses() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = array( NULL => '' );
        if ($periods->exists()) { foreach ($periods as $period) {
            $period->course->order_by_with_constant('name', 'asc')->get_iterated();
            if ($period->course->exists() > 0) { foreach ($period->course as $course) {
                $data[$period->name][$course->id] = $course->name;
            }}
        }}
        $this->parser->assign('courses', $data);
    }
    
    private function inject_task_set_types() {
        $task_set_types = new Task_set_type();
        $task_set_types->order_by('name', 'asc');
        $task_set_types->get_iterated();
        $data = array( NULL => '' );
        if ($task_set_types->exists()) { foreach ($task_set_types as $task_set_type) {
            $data[$task_set_type->id] = $task_set_type->name;
        }}
        $this->parser->assign('task_set_types', $data);
    }
    
    private function store_task_sets_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_FILTER_SESSION_NAME, 'course');
        }
    }
    
    private function inject_stored_task_sets_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course');
        $this->parser->assign('filter', $filter);
    }

    private function store_sorting_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_SORTING_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_SORTING_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_SORTING_FILTER_SESSION_NAME, 'course');
        }
    }

    private function inject_stored_sorting_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_SORTING_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course');
        $this->parser->assign('filter', $filter);
    }

    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
    private function inject_test_types() {
        $this->load->helper('tests');
        
        $this->parser->assign('test_types', get_all_supported_test_types());
    }
    
    private function inject_course_groups() {
        $groups = new Group();
        $groups->include_related('course', '*', TRUE, TRUE);
        $groups->order_by_related('course', 'id', 'asc');
        $groups->order_by_with_constant('name', 'asc');
        $groups->get_iterated();
        
        $data = array();
        foreach ($groups as $group) {
            if ($group->course->exists()) {
                $data[$group->course->id][] = array(
                    'value' => $group->id,
                    'text' => $this->lang->text($group->name),
                );
            }
        }
        
        $this->parser->assign('all_groups', $data);
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