<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Categories controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Categories extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('categories');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('categories_api.js');
        $this->parser->add_js_file('categories/form.js');
        $this->parser->add_css_file('admin_categories.css');
        $categories = new Category();
        $structure = $categories->get_all_structured();
        $this->parser->parse('backend/categories/index.tpl', array('structure' => $structure));
    }
    
    public function tree_structure() {
        $categories = new Category();
        $structure = $categories->get_all_structured();
        $this->parser->parse('backend/categories/tree_structure.tpl', array('structure' => $structure));
    }
    
    public function new_category_form() {
        $categories = new Category();
        $structure = $categories->get_all_structured();
        $this->parser->parse('backend/categories/new_category_form.tpl', array('structure' => $structure));
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('category[name]', 'lang:admin_categories_form_field_category_name', 'required');
        $this->form_validation->set_rules('category[parent_id]', 'lang:admin_categories_form_field_parent_category', 'required');
        
        if ($this->form_validation->run()) {
            $category_data = $this->input->post('category');
            $category = new Category();
            $category->name = $category_data['name'];
            
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $cansave = TRUE;
            if ($category_data['parent_id'] == 'root') {
                $category->parent_id = NULL;
            } else {
                $parent = new Category();
                $parent->get_by_id(intval($category_data['parent_id']));
                if (!$parent->exists()) {
                    $cansave = FALSE;
                } else {
                    $category->parent_id = intval($category_data['parent_id']);
                }
            }
            
            if ($cansave && $category->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_categories_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_categories_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            
            redirect(create_internal_url('admin_categories/new_category_form'));
        } else {
            $this->new_category_form();
        }
    }
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('categories');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('categories/form.js');
        $uri = $this->uri->ruri_to_assoc(3);
        $category_id = isset($uri['category_id']) ? intval($uri['category_id']) : 0;
        $category = new Category();
        $category->get_by_id($category_id);
        $categories = new Category();
        $structure = $categories->get_all_structured();
        $this->parser->parse('backend/categories/edit.tpl', array('category' => $category, 'structure' => $structure));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('category[name]', 'lang:admin_categories_form_field_category_name', 'required');
        $this->form_validation->set_rules('category[parent_id]', 'lang:admin_categories_form_field_parent_category', 'required');
        $this->form_validation->set_rules('category_id', 'id', 'required');
        
        if ($this->form_validation->run()) {
            $categori_id = $this->input->post('category_id');
            $category = new Category();
            $category->get_by_id($categori_id);
            if ($category->exists()) {
                $category_data = $this->input->post('category');
                $category->name = $category_data['name'];
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                $cansave = TRUE;
                if ($category_data['parent_id'] == 'root') {
                    $category->parent_id = NULL;
                } else {
                    $parent = new Category();
                    $parent->get_by_id(intval($category_data['parent_id']));
                    if (!$parent->exists()) {
                        $cansave = FALSE;
                    } else {
                        $category->parent_id = intval($category_data['parent_id']);
                    }
                }
                
                if ($cansave && $category->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_categories_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_categories_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_categories_error_category_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_categories/index'));
        } else {
            $this->edit();
        }
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $category_id = isset($url['category_id']) ? intval($url['category_id']) : 0;
        if ($category_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $category = new Category();
            $category->get_by_id($category_id);
            $category->delete();
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
}