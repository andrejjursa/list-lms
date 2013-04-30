<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends MY_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_tasks_filter_data';
    
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
        $this->parser->add_js_file('tasks_api.js');
        $this->parser->add_js_file('tasks/filter.js');
        $this->parser->add_css_file('admin_tasks.css');
        $this->inject_stored_filter();
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->parser->parse('backend/tasks/index.tpl', array('structure' => $structure));
    }
    
    public function get_all_tasks() {
        $tasks = new Task();
        $tasks->order_by('name', 'asc');
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        if (isset($filter['categories']['clauses']) && count($filter['categories']['clauses']) > 0) {
            $tasks->add_categories_filter($filter['categories']['clauses']);
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
        $this->parser->add_js_file('tasks/form.js');
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
        $task_id = isset($url['task_id']) ? intval($url['task_id']) : 0;
        $task = new Task();
        $task->get_by_id($task_id);
        $category = new Category();
        $structure = $category->get_all_structured();
        $this->_add_tinymce();
        $this->_add_uploadify();
        $this->parser->add_js_file('tasks/form.js');
        $this->parser->add_js_file('tasks/form_edit.js');
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
        $this->parser->parse('backend/tasks/preview.tpl', array('task' => $task));
    }
    
    public function add_files($task_id) {
        $task = new Task();
        $task->get_by_id(intval($task_id));
        if (!$task->exists()) {
            $this->output->set_output($this->lang->line('admin_tasks_error_message_task_not_found'));
            return;
        }
        $path = 'public/uploads/task_files/task_' . intval($task_id) . '/';
        if (!file_exists($path)) {
            @mkdir($path);
            @chmod($path, 0644);
        }
        $config['upload_path'] = $path;
        $config['allowed_types'] = '*';
        $config['overwrite'] = FALSE;
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file_upload')) {
            $this->output->set_output('ok');
        } else {
            $this->output->set_output(sprintf($this->lang->line('admin_tasks_file_upload_message_upload_error'), $this->upload->display_errors('<p>', '</p>')));
        }
    }
    
    public function get_task_files($task_id) {
        $this->inject_files($task_id);
        $this->parser->parse('backend/tasks/edit_files_list.tpl', array('task_id' => intval($task_id)));
    }

    public function download_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'public/uploads/task_files/task_' . intval($task_id) . '/' . $filename;
        $this->output->set_header('Content-Type: application/octet-stream');
        $this->output->set_header('Content-Transfer-Encoding: Binary');
        $this->output->set_header('Content-disposition: attachment; filename="' . $filename . '"');
        if (file_exists($filepath)) {
            $file_content = file_get_contents($filepath);
            $this->output->set_output($file_content);
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    public function delete_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'public/uploads/task_files/task_' . intval($task_id) . '/' . $filename;
        $this->output->set_content_type('application/json');
        if (file_exists($filepath)) {
            @unlink($filepath);
            $this->output->set_output(json_encode(TRUE));
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
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
    
    private function inject_files($task_id) {
        $path = 'public/uploads/task_files/task_' . intval($task_id) . '/';
        $files = array();
        if (file_exists($path)) {
            $files_in_dir = scandir($path);
            foreach ($files_in_dir as $file) {
                if (is_file($path . $file)) {
                    $files[] = array(
                        'file' => $file,
                        'filepath' => $path . $file,
                    );
                }
            }
        }
        $this->parser->assign('files', $files);
    }
}