<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Tasks controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Tasks extends LIST_Controller
{
    public const STORED_FILTER_SESSION_NAME = 'admin_tasks_filter_data';
    
    public const FILE_LIST_PUBLIC = 1;
    public const FILE_LIST_HIDDEN = 2;
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index(): void
    {
        $this->_select_teacher_menu_pagetag('tasks');
        $this->load->helper('tests');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_tasks/list.js');
        $this->parser->add_js_file('admin_tasks/filter.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->inject_stored_filter();
        $this->inject_courses();
        $this->inject_authors();
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->parser->parse(
            'backend/tasks/index.tpl',
            [
                'structure' => $structure,
                'test_types' => get_all_supported_test_types()
            ]
        );
    }
    
    public function get_all_tasks(): void
    {
        $fields_config = [
            ['name' => 'created', 'caption' => 'lang:common_table_header_created'],
            ['name' => 'updated', 'caption' => 'lang:common_table_header_updated'],
            ['name' => 'name', 'caption' => 'lang:admin_tasks_table_header_name'],
            ['name' => 'categories', 'caption' => 'lang:admin_tasks_table_header_categories'],
            ['name' => 'task_sets', 'caption' => 'lang:admin_tasks_table_header_task_sets'],
            ['name' => 'test_count', 'caption' => 'lang:admin_tasks_table_header_test_count'],
            ['name' => 'author', 'caption' => 'lang:admin_tasks_table_header_author'],
        ];
        
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        
        $tasks = new Task();
        $tasks->include_related('author', 'fullname');
        $tasks->include_related_count('test');
        if (isset($filter['categories']['clauses']) && count($filter['categories']['clauses']) > 0) {
            $tasks->add_categories_filter($filter['categories']['clauses']);
        }
        if ((isset($filter['name']) && trim($filter['name']) !== '')
            || (isset($filter['text']) && trim($filter['text']) !== '')
        ) {
            $tasks->group_start();
            if (isset($filter['name']) && trim($filter['name']) !== '') {
                $tasks->or_like_with_overlay('name', trim($filter['name']));
            }
            if (isset($filter['text']) && trim($filter['text']) !== '') {
                $tasks->or_like_with_overlay('text', trim($filter['text']), 'both', true);
            }
            $tasks->group_end();
        }
        if (isset($filter['tests'])) {
            $tests = $tasks->test;
            $tests->select_func('COUNT', '@id', 'tests_count');
            $tests->where_related('task', 'id', '${parent}.id');
            if ($filter['tests'] === 'have') {
                if (isset($filter['test_types'])
                    && is_array($filter['test_types'])
                    && count($filter['test_types']) > 0
                ) {
                    $tests->where_in('type', $filter['test_types']);
                }
                $tasks->where_subquery('0 < ', $tests);
            } else if ($filter['tests'] === 'donthave') {
                $tasks->where_subquery('0 = ', $tests);
            }
        }
        if (isset($filter['author'])) {
            if (trim($filter['author']) !== '') {
                if ($filter['author'] == '0') {
                    $tasks->where('author_id', null);
                } else {
                    $tasks->where('author_id', (int)$filter['author']);
                }
            }
        }
        if (isset($filter['time'], $filter['time_days'])) {
            if (is_numeric($filter['time_days']) && $filter['time_days'] >= 1 && $filter['time'] !== 'disable') {
                $days = $filter['time_days'] - 1;
                $day_min = date(
                    'Y-m-d H:i:s',
                    strtotime(
                        date('Y-m-d') . ' 00:00:00' . (
                            $days === 1
                                ? ' -1 day'
                                : ($days > 1 ? ' -' . $days . ' days' : '')
                        )
                    )
                );
                if ($filter['time'] === 'created') {
                    $tasks->where('created >=', $day_min);
                } else if ($filter['time'] === 'updated') {
                    $tasks->where('updated >=', $day_min);
                }
            }
        }
        $tasks->include_related_count('task_set');
        $order_by_direction = $filter['order_by_direction'] === 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] === 'created') {
            $tasks->order_by('created', $order_by_direction);
        } else if ($filter['order_by_field'] === 'updated') {
            $tasks->order_by('updated', $order_by_direction);
        } else if ($filter['order_by_field'] === 'name') {
            $tasks->order_by_with_overlay('name', $order_by_direction);
        } else if ($filter['order_by_field'] === 'task_sets') {
            $tasks->order_by('task_set_count', $order_by_direction);
        } else if ($filter['order_by_field'] === 'test_count') {
            $tasks->order_by('test_count', $order_by_direction);
        } else if ($filter['order_by_field'] === 'author') {
            $tasks->order_by_related_as_fullname('author', 'fullname', $order_by_direction);
        }
        $tasks->get_paged_iterated(
            isset($filter['page']) ? (int)$filter['page'] : 1,
            isset($filter['rows_per_page']) ? (int)$filter['rows_per_page'] : 25
        );
        $this->lang->init_overlays('tasks', $tasks->all_to_array(), ['name']);
        $this->parser->parse(
            'backend/tasks/all_tasks.tpl',
            [
                'tasks' => $tasks,
                'fields_config' => $fields_config
            ]
        );
    }
    
    public function new_task(): void
    {
        $this->_select_teacher_menu_pagetag('tasks');
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->inject_prettify_config();
        $this->_add_tinymce4();
        $this->parser->add_js_file('admin_tasks/form.js');
        $this->parser->add_js_file('admin_tasks/form_new.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->parser->parse('backend/tasks/new_task.tpl', ['structure' => $structure]);
    }
    
    public function create(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task[name]', 'lang:admin_tasks_form_field_name', 'required');
        $this->form_validation->set_rules('task[text]', 'lang:admin_tasks_form_field_text', 'required');
        $this->form_validation->set_rules(
            'task[categories][]',
            'lang:admin_tasks_form_field_categories',
            'required'
        );
        
        if ($this->form_validation->run()) {
            $task_data = $this->input->post('task');
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $categories = new Category();
            $categories->where_in('id', $task_data['categories']);
            $categories->get();
            
            $task = new Task();
            $task->from_array($task_data, ['name']);
            $task->text = remove_base_url($task_data['text']);
            $task->author_id = $this->usermanager->get_teacher_id();
            if ($task->save($categories->all) && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message(
                    'lang:admin_tasks_flash_message_save_successful',
                    Messages::MESSAGE_TYPE_SUCCESS
                );
                $this->_action_success();
                if ($this->input->post('submit_and_go_to_list') !== null) {
                    redirect(create_internal_url('admin_tasks'));
                } else {
                    redirect(create_internal_url('admin_tasks/edit/task_id/' . $task->id));
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_tasks_flash_message_save_failed',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            
            redirect(create_internal_url('admin_tasks'));
        } else {
            $this->new_task();
        }
    }
    
    public function edit(): void
    {
        $this->_select_teacher_menu_pagetag('tasks');
        $this->_load_teacher_langfile('tests');
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? (int)$url['task_id'] : (int)$this->input->post('task_id');
        $task = new Task();
        $task->include_related_count('task_set');
        $task->get_by_id($task_id);
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->_add_tinymce4();
        $this->_add_plupload();
        $this->inject_prettify_config();
        $this->inject_teachers();
        $this->parser->add_js_file('admin_tasks/form.js');
        $this->parser->add_js_file('admin_tasks/form_edit.js');
        $this->parser->add_js_file('admin_tests/all_tests_list.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->parser->add_css_file('admin_tests.css');
        $this->inject_languages();
        $this->lang->load_all_overlays('tasks', $task_id);
        $this->parser->parse('backend/tasks/edit.tpl', [
            'task'      => $task,
            'structure' => $structure,
        ]);
    }
    
    public function update(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task[name]', 'lang:admin_tasks_form_field_name', 'required');
        $this->form_validation->set_rules('task[text]', 'lang:admin_tasks_form_field_text', 'required');
        $this->form_validation->set_rules(
            'task[categories][]',
            'lang:admin_tasks_form_field_categories',
            'required'
        );
        $this->form_validation->set_rules('task_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            $task_id = $this->input->post('task_id');
            $task = new Task();
            $task->get_by_id($task_id);
            if ($task->exists()) {
                $task_data = $this->input->post('task');
                $overlay = $this->input->post('overlay');
                $task->from_array($task_data, ['name', 'internal_comment']);
                $task->text = remove_base_url($task_data['text']);
                
                $author = new Teacher();
                if ((int)$task_data['author_id'] > 0) {
                    $author->get_by_id((int)$task_data['author_id']);
                }
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                $categories = new Category();
                $categories->where_in('id', $task_data['categories']);
                $categories->get();
                
                $task->category->get();
                $task->delete($task->category->all);
                
                if ($task->save(['category' => $categories->all, 'author' => $author])
                    && $this->lang->save_overlay_array(remove_base_url_from_overlay_array($overlay, 'text'))
                    && $this->db->trans_status()
                ) {
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_tasks_flash_message_save_successful',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_tasks_flash_message_save_failed',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
                
            } else {
                $this->messages->add_message(
                    'lang:admin_tasks_error_message_task_not_found',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_tasks'));
        } else {
            $this->edit();
        }
    }
    
    public function delete(): void
    {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? (int)$url['task_id'] : 0;
        if ($task_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task = new Task();
            $task->get_by_id($task_id);
            $task->delete();
            $this->lang->delete_overlays('tasks', (int)$task_id);
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->output->set_output(json_encode(true));
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(false));
            }
        } else {
            $this->output->set_output(json_encode(false));
        }
    }
    
    public function preview(): void
    {
        $this->_add_mathjax();
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? (int)$url['task_id'] : 0;
        $task = new Task();
        $task->get_by_id($task_id);
        $this->inject_files($task_id);
        $this->parser->add_css_file('admin_tasks.css');
        $this->_add_prettify();
        $this->parser->add_js_file('admin_tasks/preview.js');
        $this->parser->parse('backend/tasks/preview.tpl', ['task' => $task]);
    }
    
    public function clone_task($task_id): void
    {
        $result = new stdClass();
        $result->result = false;
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $old_task = new Task();
        $old_task->get_by_id((int)$task_id);
        if ($old_task->exists()) {
            /** @var Task $new_task */
            $new_task = $old_task->get_copy();
            if ($new_task->save()) {
                $this->lang->clone_overlays('tasks', $old_task->id, $new_task->id);
                $from = 'private/uploads/task_files/task_' . $old_task->id;
                $continue = true;
                if (file_exists($from)) {
                    $to = 'private/uploads/task_files/task_' . $new_task->id;
                    if (!clone_directory($from, $to)) {
                        unlink_recursive($to, true);
                        $this->db->trans_rollback();
                        $result->message = $this->lang->line('admin_tasks_error_message_files_not_cloned');
                        $continue = false;
                    }
                }
                if ($continue) {
                    $old_categories = new Category();
                    $old_categories->where_related($old_task);
                    $old_categories->get();
                    if ($old_categories->result_count()) {
                        foreach ($old_categories->all as $old_category) {
                            $old_category->save($new_task);
                        }
                    }
                    $old_tests = new Test();
                    $old_tests->where_related($old_task);
                    $old_tests->get();
                    if ($old_tests->result_count()) {
                        foreach ($old_tests->all as $old_test) {
                            $new_test = $old_test->get_copy();
                            if ($new_test->save($new_task)) {
                                $this->lang->clone_overlays('tests', $old_test->id, $new_test->id);
                                $from = 'private/uploads/unit_tests/test_' . $old_test->id;
                                $to = 'private/uploads/unit_tests/test_' . $new_test->id;
                                clone_directory($from, $to);
                            }
                        }
                    }
                    $this->db->trans_commit();
                    $result->result = true;
                    $result->message = $this->lang->line('admin_tasks_success_message_task_cloned');
                }
            } else {
                $this->db->trans_rollback();
                $result->message = $this->lang->line('admin_tasks_error_message_clone_dont_saved');
            }
        } else {
            $this->db->trans_rollback();
            $result->message = $this->lang->line('admin_tasks_error_message_task_not_found');
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }
    
    public function plupload_file($task_id): void
    {
        $this->load->library('plupload');
        $path = 'private/uploads/task_files/task_' . (int)$task_id . '/';
        if (!file_exists($path)) {
            @mkdir($path);
            @chmod($path, DIR_READ_MODE);
        }
        $this->plupload->do_upload($path);
    }
    
    public function plupload_hidden_file($task_id): void
    {
        $this->load->library('plupload');
        $path_base = 'private/uploads/task_files/task_' . (int)$task_id . '/';
        if (!file_exists($path_base)) {
            @mkdir($path_base);
            @chmod($path_base, DIR_READ_MODE);
        }
        $path = 'private/uploads/task_files/task_' . (int)$task_id . '/hidden/';
        if (!file_exists($path)) {
            @mkdir($path);
            @chmod($path, DIR_READ_MODE);
        }
        $this->plupload->do_upload($path);
    }
    
    public function get_task_files($task_id): void
    {
        $this->inject_files($task_id);
        $this->parser->parse('backend/tasks/edit_files_list.tpl', ['task_id' => (int)$task_id]);
    }
    
    public function get_hidden_task_files($task_id): void
    {
        $this->inject_files($task_id, self::FILE_LIST_HIDDEN);
        $this->parser->parse('backend/tasks/edit_private_files_list.tpl', ['task_id' => (int)$task_id]);
    }
    
    public function delete_file($task_id, $file): void
    {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . (int)$task_id . '/' . $filename;
        $this->output->set_content_type('application/json');
        if (file_exists($filepath)) {
            @unlink($filepath);
            $this->output->set_output(json_encode(true));
        } else {
            $this->output->set_output(json_encode(false));
        }
    }
    
    public function delete_hidden_file($task_id, $file): void
    {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . (int)$task_id . '/hidden/' . $filename;
        $this->output->set_content_type('application/json');
        if (file_exists($filepath)) {
            @unlink($filepath);
            $this->output->set_output(json_encode(true));
        } else {
            $this->output->set_output(json_encode(false));
        }
    }
    
    public function add_to_task_set(): void
    {
        $this->_add_mathjax();
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? (int)$url['task_id'] : ((int)$this->input->post('task_id'));
        $task = new Task();
        $task->get_by_id($task_id);
        $task_set = new Task_set();
        $task_set_id = (int)$this->input->post('task_set_id');
        if ($task_set_id === 0) {
            $task_set->get_as_open();
        } else {
            $task_set->get_by_id($task_set_id);
        }
        $this->_add_prettify();
        $this->parser->add_js_file('admin_tasks/add_to_task_set.js');
        $this->parser->parse('backend/tasks/add_to_task_set.tpl', [
            'task'     => $task,
            'task_set' => $task_set,
        ]);
    }
    
    public function insert_to_task_set(): void
    {
        $this->load->library('form_validation');
        
        $task_set_id = (int)$this->input->post('task_set_id');
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $task_set = new Task_set();
        $task_set->get_by_id($task_set_id);
        
        $this->form_validation->set_rules('task_id', 'task_id', 'required');
        $this->form_validation->set_rules('task_set_id', 'task_set_id', 'required');
        if ($task_set->exists()) {
            if ($task_set->content_type === 'task_set') {
                $this->form_validation->set_rules(
                    'points_total',
                    'lang:admin_tasks_add_to_task_set_form_field_points_total',
                    'required|number|greater_than_equal[0]'
                );
                $this->form_validation->set_rules(
                    'test_max_points',
                    'lang:admin_tasks_add_to_task_set_form_field_test_max_points',
                    'required|number|greater_than_equal[0]'
                );
                $this->form_validation->set_rules(
                    'test_min_points',
                    'lang:admin_tasks_add_to_task_set_form_field_test_min_points',
                    'required|number|less_than_field_or_equal[test_max_points]'
                );
            } else {
                $this->form_validation->set_rules(
                    'max_projects_selections',
                    'lang:admin_tasks_add_to_task_set_form_field_max_projects_selections',
                    'required|integer|greater_than[0]'
                );
            }
        }
        
        if ($this->form_validation->run()) {
            $task_id = (int)$this->input->post('task_id');
            $points_total = (float)$this->input->post('points_total');
            $test_max_points = (float)$this->input->post('test_max_points');
            $test_min_points = (float)$this->input->post('test_min_points');
            $bonus_task = (int)(bool)(int)$this->input->post('bonus_task');
            $max_projects_selections = (int)$this->input->post('max_projects_selections');
            $internal_comment = $this->input->post('internal_comment');
            $task = new Task();
            $task->get_by_id($task_id);
            if (!$task->exists()) {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_tasks_error_message_task_not_found',
                    Messages::MESSAGE_TYPE_ERROR
                );
            } else if (!$task_set->exists()) {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_tasks_add_to_task_set_nothing_opened',
                    Messages::MESSAGE_TYPE_ERROR
                );
            } else if ($task_set->is_related_to($task)) {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_tasks_add_to_task_set_already_related',
                    Messages::MESSAGE_TYPE_ERROR
                );
            } else {
                $related_task = $task_set
                    ->task
                    ->include_join_fields()
                    ->order_by('join_sorting', 'desc')
                    ->limit(1)->get();
                $new_sorting = $related_task->exists() ? (int)$related_task->join_sorting + 1 : 1;
                $task_set->save($task);
                $task_set->set_join_field($task, 'points_total', $points_total);
                $task_set->set_join_field($task, 'test_max_points', $test_max_points);
                $task_set->set_join_field($task, 'test_min_points', $test_min_points);
                $task_set->set_join_field($task, 'sorting', $new_sorting);
                $task_set->set_join_field($task, 'bonus_task', $bonus_task);
                $task_set->set_join_field($task, 'max_projects_selections', $max_projects_selections);
                $task_set->set_join_field($task, 'internal_comment', $internal_comment);
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_tasks_add_to_task_set_save_success',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_tasks_add_to_task_set_save_failed',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
            }
            redirect(create_internal_url('admin_tasks/add_to_task_set/task_id/' . $task_id));
        } else {
            $this->db->trans_rollback();
            $this->add_to_task_set();
        }
    }
    
    public function get_metainfo_open_task_set(): void
    {
        $this->_initialize_open_task_set();
        $this->parser->parse('partials/backend_general/open_task_set.tpl');
    }
    
    private function store_filter($filter): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $new_filter['categories']['clauses'] = $filter['categories']['clauses'] ?? [];
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter(): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
        $this->parser->assign('filter', $filter);
    }
    
    private function inject_languages(): void
    {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
    private function inject_files($task_id, $source = self::FILE_LIST_PUBLIC): void
    {
        $task = new Task();
        $task->get_by_id($task_id);
        $files = [];
        if ($source === self::FILE_LIST_PUBLIC) {
            $files = $task->get_task_files();
        } else if ($source === self::FILE_LIST_HIDDEN) {
            $files = $task->get_task_hidden_files();
        }
        $this->parser->assign('files', $files);
    }
    
    private function inject_prettify_config(): void
    {
        $this->config->load('prettify');
        $prettify = $this->config->item('prettify');
        $highlighters = $prettify['highlighters'];
        $output = [];
        if (is_array($highlighters) && count($highlighters)) {
            foreach ($highlighters as $lang => $config) {
                $output[] = ['lang' => $lang, 'name' => $this->lang->text($config['name'])];
            }
        }
        $this->parser->assign('highlighters', $output);
    }
    
    public function inject_teachers(): void
    {
        $teachers = new Teacher();
        $teachers->order_by_as_fullname('fullname', 'asc');
        $teachers->get_iterated();
        
        $data = [null => ''];
        
        foreach ($teachers as $teacher) {
            $data[$teacher->id] = $teacher->fullname . ' (' . $teacher->email . ')';
        }
        
        $this->parser->assign('teachers', $data);
    }
    
    private function inject_courses(): void
    {
        $courses = new Course();
        $courses->include_related('period', 'name');
        $courses->order_by_related('period', 'sorting', 'ASC');
        $courses->order_by_with_constant('name', 'ASC');
        $courses->get_iterated();
        
        $this->parser->assign('courses', $courses);
    }
    
    private function inject_authors(): void
    {
        $teachers = new Teacher();
        $teachers->order_by_as_fullname('fullname');
        $teachers->get_iterated();
        
        $data = [
            0 => $this->lang->line('admin_tasks_authors_list_unknown_author'),
        ];
        
        foreach ($teachers as $teacher) {
            $data[$teacher->id] = $teacher->fullname . ' (' . $teacher->email . ')';
        }
        
        $this->parser->assign('authors', $data);
    }
    
}