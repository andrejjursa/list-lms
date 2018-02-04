<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Course content controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Course_content extends LIST_Controller {

    const COURSE_CONTENT_MASTER_FILE_STORAGE = APPPATH . '..' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR;
    const REGEXP_PATTERN_DATETIME = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';
    const STORED_FILTER_SESSION_NAME = 'admin_course_content_filter_data';
    
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
        $this->_select_teacher_menu_pagetag('course_content');
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_course_content/list.js');
        $this->parser->add_js_file('admin_course_content/form.js');
        $this->parser->add_js_file('admin_tasks/form.js');
        $this->parser->add_css_file('admin_course_content.css');
        $this->_add_tinymce4();
        $this->_add_prettify();
        $this->_add_mathjax();
        $this->_add_plupload();
        $this->inject_courses();
        $this->inject_languages();
        $this->inject_course_content_groups($this->current_course_id());
        $this->inject_course_content_groups_array();
        $this->inject_prettify_config();
        $this->inject_stored_filter();
        $this->parser->parse('backend/course_content/index.tpl', [
            'temp_upload_dir' => $this->get_upload_folder_name(NULL),
            'is_writable' => $this->check_writable(),
        ]);
    }

    public function new_content_form() {
        $this->inject_courses();
        $this->inject_languages();
        $this->inject_course_content_groups($this->current_course_id());
        $this->inject_prettify_config();
        $this->parser->parse('backend/course_content/new_content_form.tpl', [
            'temp_upload_dir' => $this->get_upload_folder_name(NULL),
            'is_writable' => $this->check_writable(),
        ]);
    }

    public function get_all_content() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $this->inject_stored_filter();
        
        $course_content = new Course_content_model();
        
        if ((int)($filter['course_id'] ?? 0) > 0) {
            $course_content->where_related('course', 'id', (int)$filter['course_id']);
        }

        $course_content->select('*');
        $course_content->include_related('course', 'name');
        $course_content->include_related('course/period', 'name');
        $course_content->include_related('course_content_group', 'title');
    
        $order_by_direction = $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] == 'title') {
            $course_content->order_by('title', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'course') {
            $course_content->order_by_related('course/period', 'sorting', $order_by_direction);
            $course_content->order_by_related_with_constant('course', 'name', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'published') {
            $course_content->order_by('published', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'public') {
            $course_content->order_by('public', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'course_content_group') {
            $course_content->order_by_related_with_overlay('course_content_group', 'title', $order_by_direction);
        }
        
        $course_content->get_paged_iterated($filter['page'] ?? 1, $filter['rows_per_page'] ?? 25);

        $this->lang->init_overlays('course_content', $course_content->all_to_array(), ['title', 'content']);

        $this->parser->parse('backend/course_content/table_content.tpl', ['course_content' => $course_content]);
    }

    public function create() {
        $this->load->library('form_validation');

        $course_content_data = $this->input->post('course_content');

        $this->form_validation->set_rules('course_content[title]', 'lang:admin_course_content_form_field_title', 'required');
        $this->form_validation->set_rules('course_content[course_id]', 'lang:admin_course_content_form_field_course_id', 'required|exists_in_table[courses.id]');
        $this->form_validation->set_rules('course_content[course_content_group_id]', 'lang:admin_course_content_form_field_course_content_group_id','exists_in_table[?course_content_groups.id]|callback__content_group_related_to_course');
        $this->form_validation->set_message('_content_group_related_to_course', $this->lang->line('admin_course_content_form_error_course_content_group_not_related_to_course'));

        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $course_content = new Course_content_model();
            $course_content->from_array($course_content_data, ['title', 'content', 'course_id']);
            $course_content->published = $course_content_data['published'] ? 1 : 0;
            $course_content->public = $course_content_data['public'] ? 1 : 0;
            $course_content->course_content_group_id = (int)$course_content_data['course_content_group_id'] > 0 ? (int)$course_content_data['course_content_group_id'] : NULL;
            $course_content->published_from = preg_match(self::REGEXP_PATTERN_DATETIME, $course_content_data['published_from']) ? $course_content_data['published_from'] : NULL;
            $course_content->published_to = preg_match(self::REGEXP_PATTERN_DATETIME, $course_content_data['published_to']) ? $course_content_data['published_to'] : NULL;
            
            $overlay = $this->input->post('overlay');
            
            if ($course_content->save() && $this->lang->save_overlay_array($overlay, $course_content) && $this->db->trans_status()) {
                if ($this->change_temp_folder_name($course_content_data['folder_name'], $course_content->id)) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_course_content_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_course_content_flash_message_save_fail_folder_rename', Messages::MESSAGE_TYPE_SUCCESS);
                    $this->new_content_form();
                    return;
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_course_content_flash_message_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_course_content/new_content_form'));
        } else {
            $this->new_content_form();
        }
        $this->db->trans_rollback();
    }
    
    public function _content_group_related_to_course($id) {
        if (is_null($id) || empty($id) || (int)$id <= 0) {
            return TRUE;
        }
        
        $post = $this->input->post('course_content');
        
        $course_id = $post['course_id'] ?? NULL;
        
        if (is_null($course_id) || empty($course_id) || (int)$course_id <= 0) {
            return FALSE;
        }
        
        $content_group = new Course_content_group();
        $content_group->where_related('course', 'id', $course_id);
        $content_group->get_by_id((int)$id);
        
        if ($content_group->exists()) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function plupload_file($upload_folder, $language) {
        $this->load->library('plupload');
        $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $upload_folder . DIRECTORY_SEPARATOR . $this->get_subfolder_by_language($language) . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            @mkdir($path, DIR_READ_MODE,true);
            @chmod($path, DIR_READ_MODE);
        }
        $this->plupload->do_upload($path);
    }
    
    public function file_list($upload_folder, $language) {
        
        $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $upload_folder . DIRECTORY_SEPARATOR . $this->get_subfolder_by_language($language) . DIRECTORY_SEPARATOR;
        $files = [];
        if (file_exists($path)) {
            $dir_content = scandir($path);
            foreach ($dir_content as $item) {
                if (is_file($path . $item)) {
                    $ext = pathinfo($path . $item, PATHINFO_EXTENSION);
                    if ($ext !== 'upload_part') {
                        $files[] = $item;
                    }
                }
            }
        }
        $this->parser->parse('backend/course_content/file_list.tpl', [
            'files' => $files,
            'upload_folder' => $upload_folder,
            'language' => $language,
        ]);
    }
    
    public function delete_file($file, $upload_folder, $language) {
        $output = new stdClass();
        $output->status = false;
        $output->message = '';
        $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $upload_folder . DIRECTORY_SEPARATOR . $this->get_subfolder_by_language($language) . DIRECTORY_SEPARATOR . $file;
        if (file_exists($path)) {
            if (@unlink($path)) {
                $output->status = true;
                $output->message = sprintf($this->lang->line('admin_course_content_message_file_delete_success'), $file);
            } else {
                $output->message = sprintf($this->lang->line('admin_course_content_message_file_delete_failed'), $file);
            }
        } else {
            $output->message = sprintf($this->lang->line('admin_course_content_message_file_delete_not_found'), $file);
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function edit($id) {
        $this->_select_teacher_menu_pagetag('course_content');
        
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$id);
        
        $this->inject_courses();
        $this->parser->parse('backend/course_content/edit.tpl', [
            'content' => $course_content
        ]);
    }
    
    public function delete($id) {
    
    }
    
    public function get_course_content_groups($course_id, $selected_id = NULL, $course_content_id = NULL) {
        $this->inject_course_content_groups($course_id);
        
        $course_content = new Course_content_model();
        if (is_int($course_content_id)) {
            $course_content->get_by_id((int)$course_content_id);
        }
        
        $this->parser->assign('course_content', $course_content);
        $this->parser->assign('selected_id', $selected_id);
        
        $this->parser->parse('backend/course_content/course_content_groups_options.tpl');
    }

    private function inject_courses()
    {
        $this->parser->assign('courses', Course::get_all_courses_for_form_select());
    }
    
    private function inject_languages() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
    
    private function inject_course_content_groups($course_id = NULL) {
        $this->parser->assign('course_content_groups', Course_content_group::get_all_groups($course_id));
    }
    
    private function current_teacher_prefered_course() {
        $teacher = new Teacher();
        $teacher->get_by_id((int)$this->usermanager->get_teacher_id());
        
        if (!$teacher->exists()) { return NULL; }
        
        return $teacher->prefered_course_id ?? NULL;
    }
    
    private function current_course_id() {
        $course_id = $this->current_teacher_prefered_course();
        $post = $this->input->post('course_content');
        $post_course_id = $post['course_id'] ?? NULL;
        
        return $post_course_id ?? $course_id;
    }
    
    private function inject_course_content_groups_array() {
        $this->parser->assign('all_course_content_groups', Course_content_group::get_all_groups(null, true));
    }
    
    private function inject_prettify_config() {
        $this->config->load('prettify');
        $prettify = $this->config->item('prettify');
        $highlighters = $prettify['highlighters'];
        $output = array();
        if (is_array($highlighters) && count($highlighters)) {
            foreach ($highlighters as $lang => $config) {
                $output[] = array('lang' => $lang, 'name' => $this->lang->text($config['name']));
            }
        }
        $this->parser->assign('highlighters', $output);
    }
    
    private function get_upload_folder_name($course_content = NULL) {
        if (!$this->check_writable()) {
            return 'non_writable';
        }
        if (is_object($course_content) && $course_content instanceof Course_content_model && $course_content->exists()) {
            return $course_content->id;
        }
        $post = $this->input->post('course_content');
        if (isset($post['folder_name'])
            && file_exists(realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $post['folder_name'])
            && is_dir(realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $post['folder_name'])) {
            return $post['folder_name'];
        }
        $new_created = false;
        $random_temporary_folder_name = '';
        do {
            $random_temporary_folder_name = 'temp_' . date('Y_m_d_H_i_s') . '_' . rand(1000000, 9999999);
            $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $random_temporary_folder_name;
            if (!file_exists($path)) {
                mkdir($path,DIR_READ_MODE, true);
                $new_created = true;
            }
        } while(!$new_created);
        return $random_temporary_folder_name;
    }
    
    private function get_subfolder_by_language($language) {
        $languages = $this->lang->get_list_of_languages();
        if (array_key_exists($language, $languages)) {
            return $language;
        }
        return '';
    }
    
    private function check_writable() {
        return is_writable(realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE));
    }
    
    private function change_temp_folder_name($folder, $id) {
        $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $folder;
        $new_path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $id;
        if (file_exists($path) && is_dir($path) && !file_exists($new_path)) {
            return @rename($path, $new_path);
        }
        return false;
    }
    
    private function store_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_FILTER_SESSION_NAME, 'course_id');
        }
    }
    
    private function inject_stored_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course_id');
        $this->parser->assign('filter', $filter);
    }

}