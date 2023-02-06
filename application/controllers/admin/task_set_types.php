<?php

/**
 * Task_set_types controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Task_set_types extends LIST_Controller
{
    
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
        $this->_select_teacher_menu_pagetag('task_set_types');
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_task_set_types/list.js');
        $this->parser->add_js_file('admin_task_set_types/form.js');
        $this->parser->add_css_file('admin_task_set_types.css');
        $this->parser->parse('backend/task_set_types/index.tpl');
    }
    
    public function get_table_content(): void
    {
        $task_set_types = new Task_set_type();
        $task_set_types->include_related_count('task_set');
        $task_set_types->include_related_count('course');
        $task_set_types->order_by_with_constant('name', 'asc')->get_iterated();
        $this->parser->parse(
            'backend/task_set_types/table_content.tpl',
            [
                'task_set_types' => $task_set_types,
            ]
        );
    }
    
    public function new_task_set_type_form(): void
    {
        $this->parser->parse('backend/task_set_types/new_task_set_type_form.tpl');
    }

    private function identifier_is_unique($identifier, $task_set_type_id = null): bool {
        $task_set_types = new Task_set_type();
        return $task_set_types->where(
            array('identifier' => $identifier, 'id !=' => strval($task_set_type_id))
        )
        ->count() == 0;
    }
    
    public function create(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'task_set_type[name]',
            'lang:admin_task_set_types_form_field_name',
            'required'
        );
        
        $this->form_validation->set_rules(
            'task_set_type[identifier]',
            'lang:admin_task_set_types_form_field_identifier',
            'required|min_length[2]|alpha_dash|is_unique[task_set_types.identifier]'
        );

        if ($this->form_validation->run()) {
            $task_set_type_data = $this->input->post('task_set_type');
            $task_set_type = new Task_set_type();
            $task_set_type->from_array($task_set_type_data, ['name']);
            $task_set_type->from_array($task_set_type_data, ['identifier']);
    
            $this->_transaction_isolation();
            $this->db->trans_begin();
            
            if ($task_set_type->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message(
                    'lang:admin_task_set_types_flash_message_save_successful',
                    Messages::MESSAGE_TYPE_SUCCESS
                );
                $this->_action_success();
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_task_set_types_flash_message_save_failed',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            
            redirect(create_internal_url('admin_task_set_types/new_task_set_type_form'));
        } else {
            $this->new_task_set_type_form();
        }
    }
    
    public function edit(): void
    {
        $this->_select_teacher_menu_pagetag('task_set_types');
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_type_id = isset($url['task_set_type_id']) ? (int)$url['task_set_type_id'] : 0;
        $this->parser->add_js_file('translation_selector.js');
        $this->parser->add_js_file('admin_task_set_types/form.js');
        $task_set_type = new Task_set_type();
        $task_set_type->get_by_id($task_set_type_id);
        $this->parser->parse('backend/task_set_types/edit.tpl', ['task_set_type' => $task_set_type]);
    }
    
    public function update(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'task_set_type[name]',
            'lang:admin_task_set_types_form_field_name',
            'required'
        );
        $this->form_validation->set_rules('task_set_type_id', 'id', 'required');
        
        $task_set_type_data = $this->input->post('task_set_type');
        $task_set_type_id = (int)$this->input->post('task_set_type_id');
    
        $task_set_types = new Task_set_type();
        $is_unique = $task_set_types->where(
                array('identifier' => $task_set_type_data['identifier'], 'id !=' => strval($task_set_type_id))
            )->count() == 0;
        
        $identifier_rules = 'required|min_length[2]|alpha_dash';
        if (!$is_unique)
            $identifier_rules .= '|is_unique[task_set_types.identifier]';
        
        $this->form_validation->set_rules(
            'task_set_type[identifier]',
            'lang:admin_task_set_types_form_field_identifier',
            $identifier_rules
        );
        
        if ($this->form_validation->run()) {
            $task_set_type_id = (int)$this->input->post('task_set_type_id');
            $task_set_type = new Task_set_type();
            $task_set_type->get_by_id($task_set_type_id);
            
            $prev_identifier = $task_set_type->stored->identifier;
            
            if ($task_set_type->exists()) {
                $task_set_type->from_array($task_set_type_data, ['name']);
                $task_set_type->from_array($task_set_type_data, ['identifier']);
                
                $this->_transaction_isolation();
                $this->db->trans_begin();
                
                if ($task_set_type->save() && $this->db->trans_status()) {
                    $this->update_in_virtual($prev_identifier, $task_set_type->identifier);
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_task_set_types_flash_message_save_successful',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                    $this->_action_success();
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_task_set_types_flash_message_save_failed',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
            } else {
                $this->messages->add_message(
                    'lang:admin_task_set_types_error_task_set_type_not_found',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_task_set_types/index'));
        } else {
            $this->edit();
        }
    }
    
    private function update_in_virtual($previous, $new) {
        if (empty($previous))
            return;
        $courses = new Course();
        $courses->include_related('period', 'name');
        $courses->order_by_related('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name');
        $courses->get_iterated();
        
        foreach ($courses as $course_raw) {
            $course = new Course();
            $course->get_by_id($course_raw->id);
            
            
            $virtual = $course->task_set_type
                ->include_join_fields()
                ->where('virtual', 1)
                ->get();
            foreach($virtual as $type){
                $task_set_type = new Task_set_type();
                $task_set_type->get_by_id($type->id);
                
                $course->set_join_field(
                    $task_set_type,
                    'formula',
                    str_replace('~'.$previous, '~'.$new, $type->join_formula)
                );
            }
        }
    }
    
    public function delete(): void
    {
        $this->output->set_content_type('application/json');
        $url = $this->uri->ruri_to_assoc(3);
        $task_set_type_id = isset($url['task_set_type_id']) ? (int)$url['task_set_type_id'] : 0;
        if ($task_set_type_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $task_set_type = new Task_set_type();
            $task_set_type->get_by_id($task_set_type_id);
            $task_set_type->delete();
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
    
}