<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Restrictions controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Restrictions extends LIST_Controller  {
    
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
        $this->_select_teacher_menu_pagetag('restrictions');
        
        $this->parser->add_js_file('admin_restrictions/list.js');
        $this->parser->add_js_file('admin_restrictions/form.js');
        $this->parser->add_css_file('admin_restrictions.css');
        
        $this->parser->parse('backend/restrictions/index.tpl');
    }
    
    public function restrictions_list() {
        $restrictions = new Restriction();
        $restrictions->order_by('start_time', 'desc');
        $restrictions->get_iterated();
        
        $this->parser->parse('backend/restrictions/restrictions_list.tpl', array('restrictions' => $restrictions));
    }
    
    public function new_restriction_form() {
        $this->parser->parse('backend/restrictions/new_restriction_form.tpl');
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('restriction[ip_addresses]', 'lang:admin_restrictions_form_field_ip_addresses', 'required|callback__ip_addresses_validation');
        $this->form_validation->set_rules('restriction[start_time]', 'lang:admin_restrictions_form_field_start_time', 'required|datetime');
        $this->form_validation->set_rules('restriction[end_time]', 'lang:admin_restrictions_form_field_end_time', 'required|datetime|callback__time_compare');
        $this->form_validation->set_message('_ip_addresses_validation', $this->lang->line('admin_restrictions_form_validation_message_ip_addresses'));
        $this->form_validation->set_message('_time_compare', $this->lang->line('admin_restrictions_form_validation_message_time_compare'));
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $restriction = new Restriction();
            $restriction->from_array($this->input->post('restriction'));
            if ($restriction->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message($this->lang->line('admin_restrictions_flash_message_creation_success'), Messages::MESSAGE_TYPE_SUCCESS);
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message($this->lang->line('admin_restrictions_flash_message_creation_failed'), Messages::MESSAGE_TYPE_ERROR);
            }
            
            redirect(create_internal_url('admin_restrictions/new_restriction_form'));
        } else {
            $this->new_restriction_form();
        }
    }
    
    public function _ip_addresses_validation($string) {
        $this->load->helper('ip_address');
        if (preg_match('/^[0-9\*\.\ ]+$/', $string)) {
            $parts = explode(',', $string);
            foreach ($parts as $part) {
                $part = trim($part);
                if (!check_valid_ip_address($part) && !check_valid_ip_range($part) && !check_valid_ip_wildcard($part)) {
                    return FALSE;
                }
            }
            return TRUE;
        }
        return FALSE;
    }
    
    public function _time_compare($string) {
        $restriction = $this->input->post('restriction');
        if ($restriction['start_time'] <= $restriction['end_time']) { 
            return TRUE;
        }
        return FALSE;
    }
    
    public function delete($restriction_id) {
        $output = new stdClass();
        $output->status = FALSE;
        $output->message = '';
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $restriction = new Restriction();
        $restriction->get_by_id((int)$restriction_id);
        
        if ($restriction->exists()) {
            $restriction->delete();
            $this->db->trans_commit();
            $output->status = TRUE;
            $output->message = $this->lang->line('admin_restrictions_messages_restriction_delete_successfully');
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_restrictions_messages_restriction_deletion_failed');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function edit($restriction_id) {
        $this->_select_teacher_menu_pagetag('restrictions');
        
        $this->parser->add_js_file('admin_restrictions/form.js');
        
        $restriction = new Restriction();
        $restriction->get_by_id((int)$restriction_id);
        
        $this->parser->parse('backend/restrictions/edit.tpl', array('restriction' => $restriction));
    }
    
    public function update($restriction_id) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('restriction[ip_addresses]', 'lang:admin_restrictions_form_field_ip_addresses', 'required|callback__ip_addresses_validation');
        $this->form_validation->set_rules('restriction[start_time]', 'lang:admin_restrictions_form_field_start_time', 'required|datetime');
        $this->form_validation->set_rules('restriction[end_time]', 'lang:admin_restrictions_form_field_end_time', 'required|datetime|callback__time_compare');
        $this->form_validation->set_message('_ip_addresses_validation', $this->lang->line('admin_restrictions_form_validation_message_ip_addresses'));
        $this->form_validation->set_message('_time_compare', $this->lang->line('admin_restrictions_form_validation_message_time_compare'));
        
        if ($this->form_validation->run()) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            $restriction = new Restriction();
            $restriction->get_by_id((int)$restriction_id);
            
            if ($restriction->exists()) {
                $restriction->from_array($this->input->post('restriction'));
                if ($restriction->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message($this->lang->line('admin_restrictions_flash_messages_update_successful'), Messages::MESSAGE_TYPE_SUCCESS);
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message($this->lang->line('admin_restrictions_flash_messages_update_failed'), Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message($this->lang->line('admin_restrictions_error_message_restriction_not_found'), Messages::MESSAGE_TYPE_ERROR);
            }
            
            redirect(create_internal_url('admin_restrictions'));
        } else {
            $this->edit($restriction_id);
        }
    }
    
    public function clear_old() {
        $output = new stdClass();
        $output->status = FALSE;
        $output->message = '';
        
        $count = 0;
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $time = date('Y-m-d H:i:s');
        
        $restrictions = new Restriction();
        $restrictions->where('end_time <', $time);
        $restrictions->where('start_time <', $time);
        $restrictions->get_iterated();
        
        foreach ($restrictions as $restriction) {
            if ($restriction->delete()) {
                $count++;
            }
        }
        
        if ($count > 0) {
            $this->db->trans_commit();
            $output->status = TRUE;
            $output->message = sprintf($this->lang->line('admin_restrictions_message_old_deleted'), $count);
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_restrictions_message_nothing_old_deleted');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
}