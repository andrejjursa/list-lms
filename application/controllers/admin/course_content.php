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
    const SORTING_STORED_FILTER_SESSION_NAME = 'admin_course_content_sorting_filter_data';
    
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
        $this->inject_stored_listing_filter();
        $this->parser->parse('backend/course_content/index.tpl', [
            'is_writable' => $this->check_writable(),
        ]);
    }

    public function new_content_form() {
        $this->inject_courses();
        $this->inject_languages();
        $this->inject_course_content_groups($this->current_course_id());
        $this->inject_prettify_config();
        $this->parser->parse('backend/course_content/new_content_form.tpl', [
            'is_writable' => $this->check_writable(),
        ]);
    }

    public function get_all_content() {
        $filter = $this->input->post('filter');
        $this->store_listing_filter($filter);
        $this->inject_stored_listing_filter();
        
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
        } elseif ($filter['order_by_field'] == 'published_from') {
            $course_content->order_by('published_from', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'published_to') {
            $course_content->order_by('published_to', $order_by_direction);
        }
        
        $course_content->get_paged_iterated($filter['page'] ?? 1, $filter['rows_per_page'] ?? 25);

        $this->lang->init_overlays('course_content', $course_content->all_to_array(), ['title', 'content']);
        
        $this->inject_languages();

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
            $files_visibility = str_replace('\\"', '"', $course_content_data['files_visibility'] ?? '{}');
            $course_content = new Course_content_model();
            $course_content->from_array($course_content_data, ['title', 'content', 'course_id']);
            $course_content->published = ($course_content_data['published'] ?? false) ? 1 : 0;
            $course_content->public = ($course_content_data['public'] ?? false) ? 1 : 0;
            $course_content->course_content_group_id = (int)$course_content_data['course_content_group_id'] > 0 ? (int)$course_content_data['course_content_group_id'] : NULL;
            $course_content->published_from = preg_match(self::REGEXP_PATTERN_DATETIME, $course_content_data['published_from']) ? $course_content_data['published_from'] : NULL;
            $course_content->published_to = preg_match(self::REGEXP_PATTERN_DATETIME, $course_content_data['published_to']) ? $course_content_data['published_to'] : NULL;
            $course_content->files_visibility = !Course_content_model::isJson($files_visibility) ? '{}' : $files_visibility;
            
            $overlay = $this->input->post('overlay');
            
            if ($course_content->save() && $this->replace_temp_folder_name_in_texts($course_content_data['folder_name'], $course_content, $overlay) && $this->lang->save_overlay_array($overlay, $course_content) && $this->db->trans_status()) {
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
            $this->db->trans_rollback();
            $this->new_content_form();
        }
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
    
    public function file_list($language = '', $upload_folder = '') {
        $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $upload_folder . DIRECTORY_SEPARATOR . $this->get_subfolder_by_language($language) . DIRECTORY_SEPARATOR;
        $files = [];
        if (file_exists($path) && $upload_folder !== '') {
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
    
    public function request_temporary_directory() {
        $output = new stdClass();
        $output->directory = $this->get_upload_folder_name();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function edit($id) {
        $this->_select_teacher_menu_pagetag('course_content');
    
        $this->parser->add_js_file('jquery.activeform.js');
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
        
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$id);
        
        $this->parser->parse('backend/course_content/edit.tpl', [
            'content' => $course_content
        ]);
    }
    
    public function update() {
        $course_content_id = $this->input->post('course_content_id');
    
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$course_content_id);
        
        if ($course_content->exists()) {
            $this->load->library('form_validation');
    
            $course_content_data = $this->input->post('course_content');
    
            $this->form_validation->set_rules('course_content[title]', 'lang:admin_course_content_form_field_title', 'required');
            $this->form_validation->set_rules('course_content[course_id]', 'lang:admin_course_content_form_field_course_id', 'required|exists_in_table[courses.id]');
            $this->form_validation->set_rules('course_content[course_content_group_id]', 'lang:admin_course_content_form_field_course_content_group_id','exists_in_table[?course_content_groups.id]|callback__content_group_related_to_course');
            $this->form_validation->set_message('_content_group_related_to_course', $this->lang->line('admin_course_content_form_error_course_content_group_not_related_to_course'));
            
            if ($this->form_validation->run()) {
                if ($course_content->course_id != (int)$course_content_data['course_id'] || $course_content->course_content_group_id != (int)$course_content_data['course_content_group_id']) {
                    $course_content->sorting = Course_content_model::get_next_sorting_number((int)$course_content_data['course_id'], $course_content_data['course_content_group_id'] ? (int)$course_content_data['course_content_group_id'] : NULL);
                }
                
                $files_visibility = str_replace('\\"', '"', $course_content_data['files_visibility'] ?? '{}');
                $course_content->from_array($course_content_data, ['title', 'content', 'course_id']);
                $course_content->published = ($course_content_data['published'] ?? false) ? 1 : 0;
                $course_content->public = ($course_content_data['public'] ?? false) ? 1 : 0;
                $course_content->course_content_group_id = (int)$course_content_data['course_content_group_id'] > 0 ? (int)$course_content_data['course_content_group_id'] : NULL;
                $course_content->published_from = preg_match(self::REGEXP_PATTERN_DATETIME, $course_content_data['published_from']) ? $course_content_data['published_from'] : NULL;
                $course_content->published_to = preg_match(self::REGEXP_PATTERN_DATETIME, $course_content_data['published_to']) ? $course_content_data['published_to'] : NULL;
                $course_content->files_visibility = !Course_content_model::isJson($files_visibility) ? '{}' : $files_visibility;
                
                $overlay = $this->input->post('overlay');
    
                if ($course_content->save() && $this->lang->save_overlay_array($overlay) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->_action_success();
                    $this->messages->add_message('lang:admin_course_content_success_updated', Messages::MESSAGE_TYPE_SUCCESS);
                    redirect(create_internal_url('admin_course_content'));
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_course_content_error_not_updated', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('admin_course_content'));
                }
                
            } else {
                $this->db->trans_rollback();
                $this->edit($course_content_id);
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message('lang:admin_course_content_error_course_content_not_found', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_course_content'));
        }
    }
    
    public function delete($id) {
        $output = new stdClass();
        $output->status = false;
        $output->message = '';
    
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$id);
        
        if ($course_content->exists()) {
            $this->lang->delete_overlays('course_content', $course_content->id);
            if ($course_content->delete()) {
                $this->db->trans_commit();
                $this->_action_success();
                $output->message = $this->lang->line('admin_course_content_success_course_content_deleted');
                $output->status = true;
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_course_content_error_course_content_cant_be_deleted');
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_course_content_error_course_content_not_found');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
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
    
    public function change_publication_status($course_content_id = null) {
        $output = new stdClass();
        $output->message = '';
        $output->status = FALSE;
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$course_content_id);
        
        if ($course_content->exists()) {
            $course_content->published = 1 - (int)$course_content->published;
            $course_content->save();
            $this->db->trans_commit();
            $output->message = sprintf($this->lang->line('admin_course_content_publication_status_switched'), $this->lang->get_overlay_with_default('course_content', $course_content->id, 'title', $course_content->title));
            $output->status = TRUE;
            $this->_action_success();
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_course_content_error_course_content_not_found');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function change_public_status($course_content_id = null) {
        $output = new stdClass();
        $output->message = '';
        $output->status = FALSE;
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $course_content = new Course_content_model();
        $course_content->get_by_id((int)$course_content_id);
        
        if ($course_content->exists()) {
            $course_content->public = 1 - (int)$course_content->public;
            $course_content->save();
            $this->db->trans_commit();
            $output->message = sprintf($this->lang->line('admin_course_content_public_status_switched'), $this->lang->get_overlay_with_default('course_content', $course_content->id, 'title', $course_content->title));
            $output->status = TRUE;
            $this->_action_success();
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_course_content_error_course_content_not_found');
        }
    
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function sorting() {
        $this->_select_teacher_menu_pagetag('course_content_sorting');
        
        $this->inject_stored_sorting_filter();
        $this->inject_courses();
        
        $this->parser->add_js_file('admin_course_content/sorting/list.js');
        $this->parser->add_css_file('admin_course_content.css');
        
        $this->parser->parse('backend/course_content/sorting.tpl');
    }
    
    public function get_all_course_content_sorting() {
        $filter = $this->input->post('filter');
        $this->store_sorting_filter($filter);
        $this->inject_stored_sorting_filter();
        
        $this->_transaction_isolation();
        $this->db->trans_start();
        
        $course = new Course();
        $course->get_by_id((int)($filter['course'] ?? 0));
        
        if ($course->exists()) {
            $query1 = new Course_content_model();
            $query1->select('id, title, sorting');
            $query1->select_func('', ['content'], 'type');
            $query1->select_func('', [0], 'content_count');
            $query1->where_related('course_content_group', 'id', null);
            $query1->where_related($course);
    
            $content_counter = new Course_content_model();
            $content_counter->select_func('COUNT', '@id', 'content_count');
            $content_counter->where_related($course);
            $content_counter->where_related('course_content_group', 'id', '${parent}.id');
            
    
            $query2 = new Course_content_group();
            $query2->select('id, title, sorting');
            $query2->select_func('', 'group', 'type');
            $query2->select_subquery($content_counter, 'content_count');
            $query2->where_related($course);
            
            $query1->union_iterated($query2, true, 'sorting ASC');
            
            $grouped_content = new Course_content_model();
            $grouped_content->select('id, title, sorting, course_content_group_id');
            $grouped_content->not_group_start();
            $grouped_content->where_related('course_content_group', 'id', null);
            $grouped_content->group_end();
            $grouped_content->where_related($course);
            $grouped_content->order_by_related('course_content_group', 'sorting', 'asc');
            $grouped_content->order_by('sorting', 'asc');
            $grouped_content->get_iterated();
    
            $this->parser->assign('top_level', $query1);
            $this->parser->assign('grouped_content', $grouped_content);
        }
        
        $this->db->trans_complete();
        
        $this->parser->assign('course', $course);
        
        $this->parser->parse('backend/course_content/sorting_list.tpl');
    }
    
    public function update_sorting() {
        $group_id = $this->input->post('group_id') ?? null;
        $course_id = $this->input->post('course_id') ?? null;
        $order = $this->input->post('order') ?? [];
        
        $output = new stdClass();
        $output->status = false;
        $output->message = '';
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $course = new Course();
        $course->get_by_id((int)$course_id);
        
        if (!empty($order) && is_array($order) && count($order) > 1) {
            if ($course->exists()) {
                if (!empty($group_id)) {
                    $output = $this->sort_in_group($output, $course, $group_id, $order);
                } else {
                    $output = $this->sort_top_level($output, $course, $order);
                }
                if ($output->status) {
                    $this->db->trans_commit();
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                }
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_course_content_sorting_course_not_found');
            }
        } else {
            $output->message = $this->lang->line('admin_course_content_sorting_nothing_to_sort');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    private function sort_in_group($output, $course, $group_id, $order) {
        $course_content_group = new Course_content_group();
        $course_content_group->where_related($course);
        $course_content_group->get_by_id((int)$group_id);
    
        $output->status = false;
        
        if ($course_content_group->exists()) {
            $all_content = new Course_content_model();
            $all_content->select('id');
            $all_content->where_related($course_content_group);
            $all_content->where_related($course);
            $all_content->order_by('sorting', 'asc');
            $all_content->get_iterated();
            
            $position = 0;
    
            $sorted = [];
            $sorted_order = [];
            for ($i=0; $i<count($order);$i++) {
                $item = $order[$i];
                
                if ($item['type'] == 'group') {
                    $output->message = $this->lang->line('admin_course_content_sorting_order_inconsistent');
                    return $output;
                }
    
                $sorted[] = [ 'id' => (int)$item['id'], 'sorting' => $position++ ];
                $sorted_order[] = (int)$item['id'];
            }
            
            $new_position = count($order);
            
            foreach ($all_content as $content) {
                if (!in_array($content->id, $sorted_order)) {
                    $sorted[] = [
                        'id' => $content->id,
                        'sorting' => $new_position++
                    ];
                }
            }
            
            foreach ($sorted as $to_sort) {
                $this->db->where('id', $to_sort['id']);
                $this->db->set('updated', 'updated', false);
                $this->db->set('sorting', $to_sort['sorting']);
                if (!$this->db->update($all_content->table)) {
                    $output->message = $this->lang->line('admin_course_content_sorting_failed_to_update_sorting');
                    return $output;
                }
            }
            
            $output->message = $this->lang->line('admin_course_content_sorting_successful');
            $output->status = true;
        } else {
            $output->message = $this->lang->line('admin_course_content_sorting_group_not_found');
        }
    
        return $output;
    }
    
    private function sort_top_level($output, $course, $order) {
        $output->status = false;
        
        $all_content = new Course_content_model();
        $all_content->select('id');
        $all_content->where_related('course_content_group', 'id', null);
        $all_content->where_related($course);
        $all_content->order_by('sorting', 'asc');
        $all_content->get_iterated();
        
        $all_groups = new Course_content_group();
        $all_groups->select('id');
        $all_groups->where_related($course);
        $all_groups->order_by('sorting', 'asc');
        $all_groups->get_iterated();
        
        $position = 0;
        
        $sorted = [];
        $sorted_groups = [];
        $sorted_content = [];
        
        for ($i=0; $i<count($order);$i++) {
            $item = $order[$i];
            
            $type = 'group';
            if ($item['type'] == 'content') { $type = 'content'; }
            
            $sorted[] = [ 'id' => (int)$item['id'], 'type' => $type, 'sorting' => $position++ ];
            if ($type == 'content') {
                $sorted_content[] = (int)$item['id'];
            } else {
                $sorted_groups[] = (int)$item['id'];
            }
        }
        
        $new_position = count($order);
    
        foreach ($all_content as $content) {
            if (!in_array($content->id, $sorted_content)) {
                $sorted[] = [
                    'id' => $content->id,
                    'sorting' => $new_position++,
                    'type' => 'content'
                ];
            }
        }
    
        foreach ($all_groups as $group) {
            if (!in_array($group->id, $sorted_groups)) {
                $sorted[] = [
                    'id' => $group->id,
                    'sorting' => $new_position++,
                    'type' => 'group'
                ];
            }
        }
    
        foreach ($sorted as $to_sort) {
            $this->db->where('id', $to_sort['id']);
            $this->db->set('updated', 'updated', false);
            $this->db->set('sorting', $to_sort['sorting']);
            if ($to_sort['type'] == 'content') {
                if (!$this->db->update($all_content->table)) {
                    $output->message = $this->lang->line('admin_course_content_sorting_failed_to_update_sorting');
                    return $output;
                }
            } else {
                if (!$this->db->update($all_groups->table)) {
                    $output->message = $this->lang->line('admin_course_content_sorting_failed_to_update_sorting');
                    return $output;
                }
            }
        }
    
        $output->message = $this->lang->line('admin_course_content_sorting_successful');
        $output->status = true;
        
        return $output;
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
    
    private function get_upload_folder_name() {
        if (!$this->check_writable()) {
            return 'non_writable';
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
        if (empty($folder)) {
            return true;
        }
        $path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $folder;
        $new_path = realpath(self::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $id;
        if (!file_exists($path) || !is_dir($path)) {
            return true;
        }
        if (file_exists($path) && is_dir($path) && !file_exists($new_path)) {
            return @rename($path, $new_path);
        }
        return false;
    }
    
    private function store_listing_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::STORED_FILTER_SESSION_NAME, 'course_id');
        }
    }
    
    private function inject_stored_listing_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course_id');
        $this->parser->assign('filter', $filter);
    }
    
    private function store_sorting_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::SORTING_STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::SORTING_STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(self::SORTING_STORED_FILTER_SESSION_NAME, 'course_id');
        }
    }
    
    private function inject_stored_sorting_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::SORTING_STORED_FILTER_SESSION_NAME, $this->usermanager->get_teacher_id(), 'course_id');
        $this->parser->assign('filter', $filter);
    }
    
    private function replace_temp_folder_name_in_texts($temp_name, &$course_content, &$overlay) {
        if (empty($temp_name)) {
            return true;
        }
        
        $course_content->content = str_replace($temp_name, $course_content->id, $course_content->content);
        $course_content->title = str_replace($temp_name, $course_content->id, $course_content->title);
        
        if (count($overlay)) {
            foreach ($overlay as $language => $tables) {
                if (count($tables)) {
                    foreach ($tables as $table => $ids) {
                        if (count($ids)) {
                            foreach ($ids as $id => $columns) {
                                if (count($columns)) {
                                    foreach ($columns as $column => $value) {
                                        $overlay[$language][$table][$id][$column] = str_replace($temp_name, $course_content->id, $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $course_content->save();
    }

}