<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_tasks_filter_data';
    
    const FILELIST_PUBLIC = 1;
    const FILELIST_HIDDEN = 2;
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('tasks');
        $this->parser->add_js_file('admin_tasks/list.js');
        $this->parser->add_js_file('admin_tasks/filter.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->inject_stored_filter();
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->parser->parse('backend/tasks/index.tpl', array('structure' => $structure));
    }
    
    public function get_all_tasks() {
        $tasks = new Task();
        $tasks->order_by_with_overlay('name', 'asc');
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        if (isset($filter['categories']['clauses']) && count($filter['categories']['clauses']) > 0) {
            $tasks->add_categories_filter($filter['categories']['clauses']);
        }
        if ((isset($filter['name']) && trim($filter['name']) != '') || (isset($filter['text']) && trim($filter['text']) != '')) {
            $tasks->group_start();
            if (isset($filter['name']) && trim($filter['name']) != '') {
                $tasks->or_like_with_overlay('name', trim($filter['name']));
            }
            if (isset($filter['text']) && trim($filter['text']) != '') {
                $tasks->or_like_with_overlay('text', trim($filter['text']), 'both', TRUE);
            }
            $tasks->group_end();
        }
        $tasks->include_related_count('task_set');
        $tasks->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->lang->init_overlays('tasks', $tasks->all_to_array(), array('name'));
        $this->parser->parse('backend/tasks/all_tasks.tpl', array('tasks' => $tasks));
    }
    
    public function new_task() {
        $this->_select_teacher_menu_pagetag('tasks');
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->_add_tinymce();
        $this->parser->add_js_file('admin_tasks/form.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->parser->parse('backend/tasks/new_task.tpl', array('structure' => $structure));
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task[name]', 'lang:admin_tasks_form_field_name', 'required');
        $this->form_validation->set_rules('task[text]', 'lang:admin_tasks_form_field_text', 'required');
        $this->form_validation->set_rules('task[categories][]', 'lang:admin_tasks_form_field_categories', 'required');
        
        if ($this->form_validation->run()) {
            $task_data = $this->input->post('task');
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $categories = new Category();
            $categories->where_in('id', $task_data['categories']);
            $categories->get();
            
            $task = new Task();
            $task->from_array($task_data, array('name', 'text'));
            if ($task->save($categories->all) && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_tasks_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                redirect(create_internal_url('admin_tasks/edit/task_id/' . $task->id));
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_tasks_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            
            redirect(create_internal_url('admin_tasks'));
        } else {
            $this->new_task();
        }
    }
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('tasks');
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? intval($url['task_id']) : intval($this->input->post('task_id'));
        $task = new Task();
        $task->include_related_count('task_set');
        $task->get_by_id($task_id);
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->_add_tinymce();
        $this->_add_plupload();
        $this->parser->add_js_file('admin_tasks/form.js');
        $this->parser->add_js_file('admin_tasks/form_edit.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->inject_languages();
        $this->lang->load_all_overlays('tasks', $task_id);
        $this->parser->parse('backend/tasks/edit.tpl', array('task' => $task, 'structure' => $structure));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task[name]', 'lang:admin_tasks_form_field_name', 'required');
        $this->form_validation->set_rules('task[text]', 'lang:admin_tasks_form_field_text', 'required');
        $this->form_validation->set_rules('task[categories][]', 'lang:admin_tasks_form_field_categories', 'required');
        $this->form_validation->set_rules('task_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            $task_id = $this->input->post('task_id');
            $task = new Task();
            $task->get_by_id($task_id);
            if ($task->exists()) {
                $task_data = $this->input->post('task');
                $overlay = $this->input->post('overlay');
                $task->from_array($task_data, array('name', 'text'));
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                $categories = new Category();
                $categories->where_in('id', $task_data['categories']);
                $categories->get();
                
                $task->category->get();
                $task->delete($task->category->all);
                
                if ($task->save($categories->all) && $this->lang->save_overlay_array($overlay) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_tasks_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_tasks_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
                
            } else {
                $this->messages->add_message('lang:admin_tasks_error_message_task_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_tasks'));
        } else {
            $this->edit();
        }
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? intval($url['task_id']) : 0;
        if ($task_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task = new Task();
            $task->get_by_id($task_id);
            $task->delete();
            $this->lang->delete_overlays('tasks', intval($task_id));
            if ($this->db->trans_status()) {
                $files_dir = 'public/uploads/task_files/task_' . intval($task_id) . '/';
                if (file_exists($files_dir)) {
                    unlink_recursive($files_dir, TRUE);
                }
                $this->db->trans_commit();
                $this->output->set_output(json_encode(TRUE));    
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(FALSE));                
            }
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    public function preview() {
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? intval($url['task_id']) : 0;
        $task = new Task();
        $task->get_by_id($task_id);
        $this->inject_files($task_id);
        $this->parser->add_css_file('admin_tasks.css');
        $this->parser->parse('backend/tasks/preview.tpl', array('task' => $task));
    }
    
    public function plupload_file($task_id) {
        $this->load->library('plupload');
        $path = 'private/uploads/task_files/task_' . intval($task_id) . '/';
        if (!file_exists($path)) {
            @mkdir($path);
            @chmod($path, 0644);
        }
        $this->plupload->do_upload($path);
    }
    
    public function plupload_hidden_file($task_id) {
        $this->load->library('plupload');
        $path_base = 'private/uploads/task_files/task_' . intval($task_id) . '/';
        if (!file_exists($path_base)) {
            @mkdir($path_base);
            @chmod($path_base, 0644);
        }
        $path = 'private/uploads/task_files/task_' . intval($task_id) . '/hidden/';
        if (!file_exists($path)) {
            @mkdir($path);
            @chmod($path, 0644);
        }
        $this->plupload->do_upload($path);
    }
    
    public function get_task_files($task_id) {
        $this->inject_files($task_id);
        $this->parser->parse('backend/tasks/edit_files_list.tpl', array('task_id' => intval($task_id)));
    }
    
    public function get_hidden_task_files($task_id) {
        $this->inject_files($task_id, self::FILELIST_HIDDEN);
        $this->parser->parse('backend/tasks/edit_private_files_list.tpl', array('task_id' => intval($task_id)));
    }
    
    public function delete_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/' . $filename;
        $this->output->set_content_type('application/json');
        if (file_exists($filepath)) {
            @unlink($filepath);
            $this->output->set_output(json_encode(TRUE));
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    public function delete_hidden_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/hidden/' . $filename;
        $this->output->set_content_type('application/json');
        if (file_exists($filepath)) {
            @unlink($filepath);
            $this->output->set_output(json_encode(TRUE));
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    public function add_to_task_set() {
        $url = $this->uri->ruri_to_assoc(3);
        $task_id = isset($url['task_id']) ? intval($url['task_id']) : (intval($this->input->post('task_id')));
        $task = new Task();
        $task->get_by_id($task_id);
        $task_set = new Task_set();
        $task_set_id = intval($this->input->post('task_id'));
        if ($task_set_id == 0) {
            $task_set->get_as_open();
        } else {
            $task_set->get_by_id($task_set_id);
        }
        $this->parser->parse('backend/tasks/add_to_task_set.tpl', array(
            'task' => $task,
            'task_set' => $task_set,
        ));
    }
    
    public function insert_to_task_set() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('task_id', 'task_id', 'required');
        $this->form_validation->set_rules('task_set_id', 'task_set_id', 'required');
        $this->form_validation->set_rules('points_total', 'lang:admin_tasks_add_to_task_set_form_field_points_total', 'required|number|greater_than[0]');
        
        if ($this->form_validation->run()) {
            $task_id = intval($this->input->post('task_id'));
            $task_set_id = intval($this->input->post('task_set_id'));
            $points_total = floatval($this->input->post('points_total'));
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task = new Task();
            $task->get_by_id($task_id);
            $task_set = new Task_set();
            $task_set->get_by_id($task_set_id);
            if (!$task->exists()) {
                $this->messages->add_message('lang:admin_tasks_error_message_task_not_found', Messages::MESSAGE_TYPE_ERROR);
            } elseif (!$task_set->exists()) {
                $this->messages->add_message('lang:admin_tasks_add_to_task_set_nothing_opened', Messages::MESSAGE_TYPE_ERROR);
            } elseif ($task_set->is_related_to($task)) {
                $this->messages->add_message('lang:admin_tasks_add_to_task_set_already_related', Messages::MESSAGE_TYPE_ERROR);
            } else {
                $related_task = $task_set->task->include_join_fields()->order_by('join_sorting', 'desc')->limit(1)->get();
                $new_sorting = $related_task->exists() ? intval($related_task->join_sorting) + 1 : 1; 
                $task_set->save($task);
                $task_set->set_join_field($task, 'points_total', $points_total);
                $task_set->set_join_field($task, 'sorting', $new_sorting);
                if ($this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_tasks_add_to_task_set_save_success', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_tasks_add_to_task_set_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
            }
            redirect(create_internal_url('admin_tasks/add_to_task_set/task_id/' . $task_id));
        } else {
            $this->add_to_task_set();
        }
    }
    
    public function get_metainfo_open_task_set() {
        $this->_initialize_open_task_set();
        $this->parser->parse('partials/backend_general/open_task_set.tpl');
    }

    private function store_filter($filter) {
        if (is_array($filter)) {
            $old_filter = $this->session->userdata(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $new_filter['categories']['clauses'] = isset($filter['categories']['clauses']) ? $filter['categories']['clauses'] : array();
            $this->session->set_userdata(self::STORED_FILTER_SESSION_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter() {
        $filter = $this->session->userdata(self::STORED_FILTER_SESSION_NAME);
        $this->parser->assign('filter', $filter);
    }
    
    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
    private function inject_files($task_id, $source = self::FILELIST_PUBLIC) {
        $path = 'private/uploads/task_files/task_' . intval($task_id) . '/';
        if ($source == self::FILELIST_HIDDEN) { $path .= 'hidden/'; } 
        $files = array();
        if (file_exists($path)) {
            $files_in_dir = scandir($path);
            foreach ($files_in_dir as $file) {
                if (is_file($path . $file)) {
                    $ext = strrpos($path . $file, '.');
                    if (substr($path . $file, $ext) !== 'upload_part') {
                        $files[] = array(
                            'file' => $file,
                            'filepath' => $path . $file,
                            'size' => $this->get_file_size($path . $file),
                        );
                    }
                }
            }
        }
        $this->parser->assign('files', $files);
    }
    
    private function get_file_size($filename) {
        $size_bytes = @filesize($filename);
        if ($size_bytes === FALSE || $size_bytes == 0) {
            return '0 B';
        }
        $size = $size_bytes;
        $unit = 'B';
        if ($size > 1023) {
            $size /= 1024;
            $unit = 'KiB';
        }
        if ($size > 1023) {
            $size /= 1024;
            $unit = 'MiB';
        }
        if ($size > 1023) {
            $size /= 1024;
            $unit = 'GiB';
        }
        return number_format($size, 2, '.', ' ') . ' ' . $unit;
    }
}