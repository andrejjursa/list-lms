<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/abstract_admin_widget.php';

/**
 * Widget controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Widget extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function showWidget($widget_id) {
        $widget = new Admin_widget();
        $widget->where_related('teacher', 'id', $this->usermanager->get_teacher_id());
        $widget->get_by_id((int)$widget_id);
        
        if ($widget->exists()) {
            $widget_class = $this->load->admin_widget($widget->widget_type, $widget->id, unserialize($widget->widget_config));
            $widget_class->render();
        } else {
            throw new Exception('Can\'t find admin widget with id <strong>' . $widget_id . '</strong>.');
        }
    }
    
    public function configure($widget_id) {
        $widget = new Admin_widget();
        $widget->where_related('teacher', 'id', $this->usermanager->get_teacher_id());
        $widget->get_by_id((int)$widget_id);
        
        $this->parser->assign('widget_type', $widget->widget_type);
        $this->parser->assign('widget_config', unserialize($widget->widget_config));
        $this->parser->assign('widget_id', $widget->id);
        
        $widget_class = NULL;
        if ($widget->exists()) {
            try {
                $widget_class = $this->load->admin_widget($widget->widget_type, $widget->id, unserialize($widget->widget_config));
                $this->parser->assign('widget_type_name', $widget_class->getContentTypeName());
                $widget_class->preConfigureForm();
            } catch (Exception $e) {
                $this->parser->assign('no_widget_found', true);
            }
        }
        
        $this->parser->parse('backend/widget/configure.tpl');
    }
    
    public function save_configuration($widget_id) {
        $widget = new Admin_widget();
        $widget->where_related('teacher', 'id', $this->usermanager->get_teacher_id());
        $widget->get_by_id((int)$widget_id);
        
        if ($widget->exists()) {
            try {
                $widget_class = $this->load->admin_widget($widget->widget_type, $widget->id, unserialize($widget->widget_config));
                $data = $this->input->post('configure');
                if ($widget_class->validateConfiguration($data)) {
                    $data_to_save = $widget_class->mergeConfiguration(unserialize($widget->widget_config), $data);
                    $widget->widget_config = serialize($data_to_save);
                    if ($widget->save()) {
                        $this->messages->add_message($this->lang->line('admin_widget_configure_message_save_success'), Messages::MESSAGE_TYPE_SUCCESS);
                        redirect(create_internal_url('admin_widget/configure/' . $widget_id));
                    } else {
                        $this->messages->add_message($this->lang->line('admin_widget_configure_message_save_error'), Messages::MESSAGE_TYPE_ERROR);
                        redirect(create_internal_url('admin_widget/configure/' . $widget_id));
                    }
                } else {
                    $this->configure($widget_id);
                }
            } catch (Exception $e) {
                $this->configure($widget_id);
            }
        } else {
            $this->configure($widget_id);
        }
    }
    
    public function delete($widget_id) {
        $output = new stdClass();
        $output->message = '';
        $output->status = FALSE;
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $widget = new Admin_widget();
        $widget->where_related('teacher', 'id', $this->usermanager->get_teacher_id());
        $widget->get_by_id((int)$widget_id);
        
        if ($widget->exists()) {
            if ($widget->delete()) {
                $this->db->trans_commit();
                $output->status = TRUE;
                $output->message = $this->lang->line('admin_widget_delete_widget_message_success');
            } else {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_widget_delete_widget_message_failed');
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_widget_delete_widget_message_not_found');
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function sort() {
        $columns = $this->input->post('column');
        
        $this->_transaction_isolation();
        $this->db->trans_start();
        
        if (count($columns)) { foreach ($columns as $column => $widget_ids) {
            $position = 1;
            if (count($widget_ids)) { foreach ($widget_ids as $widget_id) {
                $widget = new Admin_widget();
                $widget->where_related('teacher', 'id', $this->usermanager->get_teacher_id());
                $widget->get_by_id((int)$widget_id);
                if ($widget->exists()) {
                    $widget->column = $column;
                    $widget->position = $position;
                    $widget->save();
                    $position++;
                }
            }}
        }}
        
        $this->db->trans_complete();
    }
    
}
