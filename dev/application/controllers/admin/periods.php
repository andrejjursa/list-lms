<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Periods controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Periods extends MY_Controller  {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('periods');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_periods/list.js');
        $this->parser->add_js_file('admin_periods/form.js');
        $this->parser->add_css_file('admin_periods.css');
        $this->parser->parse('backend/periods/index.tpl');
    }
    
    public function ajax_periods_list() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->include_related_count('course');
        $periods->get_iterated();
        $this->parser->parse('backend/periods/ajax_periods_list.tpl', array('periods' => $periods));
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('period[name]', 'lang:admin_periods_form_field_name', 'required|is_unique[periods.name]');
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $period_top = new Period();
            $period_top->order_by('sorting', 'DESC')->limit(1)->get();
            
            $sorting = 1;
            if ($period_top->exists()) { $sorting += intval($period_top->sorting); }
            
            $period_data = $this->input->post('period');
            $period = new Period();
            $period->from_array($period_data, array('name'));
            $period->sorting = $sorting;
            
            if ($period->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_periods_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_periods_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
            }
            
            redirect(create_internal_url('admin_periods/new_period_form'));
        } else {
            $this->new_period_form();
        }
    }
    
    public function new_period_form() {
        $this->parser->parse('backend/periods/new_period_form.tpl');
    }
    
    public function edit() {
        $this->_select_teacher_menu_pagetag('periods');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_periods/form.js');
        $uri = $this->uri->ruri_to_assoc(3);
        $period_id = isset($uri['period_id']) ? intval($uri['period_id']) : 0;
        $period = new Period();
        $period->get_where(array('id' => $period_id));
        $this->parser->parse('backend/periods/edit.tpl', array('period' => $period));
    }
    
    public function update() {
        $this->load->library('form_validation');
        
        $period_id = intval($this->input->post('period_id'));
        
        $this->form_validation->set_rules('period_id', 'id', 'required');
        $this->form_validation->set_rules('period[name]', 'lang:admin_periods_form_field_name', 'required|callback__is_unique_name_not_in[' . $period_id . ']');
        $this->form_validation->set_message('_is_unique_name_not_in', $this->lang->line('admin_periods_form_error_message_is_unique_name_not_in'));
        
        if ($this->form_validation->run()) {
            $period = new Period();
            $period->get_by_id($period_id);
            if ($period->exists()) {
                $period_data = $this->input->post('period');
                $period->from_array($period_data, array('name'));
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($period->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_periods_flash_message_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_periods_flash_message_save_failed', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->messages->add_message('lang:admin_periods_error_period_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_periods/index'));
        } else {
            $this->edit();
        }
    }
    
    public function _is_unique_name_not_in($str, $id) {
        $period = new Period();
        $period->where('name', $str);
        $period->where('id !=', intval($id));
        $period->get();
        return (bool)!$period->exists();
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $uri = $this->uri->ruri_to_assoc(3);
        if (isset($uri['period_id'])) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $period = new Period();
            $period->get_by_id($uri['period_id']);
            $period->delete();
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
    
    public function move_up() {
        $this->output->set_content_type('application/json');
        $uri = $this->uri->ruri_to_assoc(3);
        if (isset($uri['period_id'])) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $period = new Period();
            $period->get_by_id($uri['period_id']);
            $period->move_up();
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
    
    public function move_down() {
        $this->output->set_content_type('application/json');
        $uri = $this->uri->ruri_to_assoc(3);
        if (isset($uri['period_id'])) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $period = new Period();
            $period->get_by_id($uri['period_id']);
            $period->move_down();
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