<?php

require_once APPPATH . 'core/abstract_admin_widget.php';

/**
 * Dashboard controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Dashboard extends LIST_Controller
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
        $this->_select_teacher_menu_pagetag('dashboard');
        
        $this->load->helper('widget');
        
        $widget_types_list = get_admin_widget_list();
        
        $this->parser->assign('widget_types_list', $widget_types_list);
        
        $this->parser->add_js_file('admin_dashboard/widgets.js');
        $this->parser->add_css_file('admin_dashboard.css');
        $this->_add_tinymce4();
        $this->_add_prettify();
        $this->parser->add_js_file('formula/edit.js');
        
        if (count($widget_types_list)) {
            foreach ($widget_types_list as $widget_type => $widget_type_name) {
                if (file_exists('public/css/admin_widgets/' . strtolower($widget_type) . '.css')) {
                    $this->parser->add_css_file('admin_widgets/' . strtolower($widget_type) . '.css');
                }
            }
        }
        
        $widgets = new Admin_widget();
        $widgets->where_related_teacher('id', $this->usermanager->get_teacher_id());
        $widgets->order_by('column', 'asc');
        $widgets->order_by('position', 'asc');
        $widgets->get_iterated();
        
        $widget_list = [];
        
        foreach ($widgets as $widget) {
            $widget_list[$widget->column][] = $widget->id;
        }
        
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        
        $this->parser->assign('columns', $teacher->widget_columns);
        
        $this->parser->assign('widget_list', $widget_list);
        
        $this->parser->parse('backend/dashboard/index.tpl');
    }
    
    public function add_widget(): void
    {
        $output = new stdClass();
        $output->message = '';
        $output->status = false;
        $output->new_id = 0;
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $widget_type = $this->input->post('widget_type');
        $widget_column = $this->input->post('widget_column');
        $output->column = (int)$widget_column;
        if ($widget_type !== '') {
            $last_widget = new Admin_widget();
            $last_widget->where_related('teacher', 'id', $this->usermanager->get_teacher_id());
            $last_widget->where('column', (int)$widget_column);
            $last_widget->limit(1);
            $last_widget->order_by('position', 'desc');
            $last_widget->get();
            $position = 1;
            if ($last_widget->exists()) {
                $position += $last_widget->position;
            }
            try {
                $wgt = $this->load->admin_widget($widget_type, 0, []);
                $widget = new Admin_widget();
                $widget->teacher_id = $this->usermanager->get_teacher_id();
                $widget->widget_type = $widget_type;
                $widget->widget_config = serialize($wgt->defaultConfiguration());
                $widget->position = $position;
                $widget->column = (int)$widget_column;
                $widget->save();
                $this->db->trans_commit();
                $output->message = $this->lang->line('admin_dashboard_message_widget_created');
                $output->status = true;
                $output->new_id = (int)$widget->id;
            } catch (Exception $e) {
                $this->db->trans_rollback();
                $output->message = $this->lang->line('admin_dashboard_message_widget_creation_failed');
            }
        } else {
            $this->db->trans_rollback();
            $output->message = $this->lang->line('admin_dashboard_message_widget_creation_failed');
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function set_columns(): void
    {
        $this->_transaction_isolation();
        $this->db->trans_begin();
        
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        
        $teacher->widget_columns = (int)$this->input->post('widget_columns');
        
        if ($teacher->widget_columns >= 1 && $teacher->widget_columns <= 4) {
            if ($teacher->save()) {
                $widget = new Admin_widget();
                $widget->where_related('teacher', $teacher);
                $widget->where('column >', $teacher->widget_columns);
                $widget->limit(1);
                $widget->get();
                
                if ($widget->exists()) {
                    $widget->where('column', $teacher->widget_columns);
                    $widget->where_related('teacher', $teacher);
                    $widget->limit(1);
                    $widget->order_by('position', 'desc');
                    $widget->get();
                    
                    $position = 1;
                    if ($widget->exists()) {
                        $position += $widget->position;
                    }
                    
                    $widget->select('id');
                    $widget->where('column >', $teacher->widget_columns);
                    $widget->where_related('teacher', $teacher);
                    $widget->order_by('column', 'asc');
                    $widget->order_by('position', 'asc');
                    $widget->get();
                    
                    $updates = true;
                    
                    foreach ($widget->all as $widget_to_update) {
                        $widget_to_update->column = $teacher->widget_columns;
                        $widget_to_update->position = $position;
                        if (!$widget_to_update->save()) {
                            $updates = false;
                        } else {
                            $position++;
                        }
                    }
                    if ($updates) {
                        $this->db->trans_commit();
                        $this->messages->add_message(
                            $this->lang->line('admin_dashboard_message_columns_saved'),
                            Messages::MESSAGE_TYPE_SUCCESS
                        );
                    } else {
                        $this->db->trans_rollback();
                        $this->messages->add_message(
                            $this->lang->line('admin_dashboard_message_columns_save_fail'),
                            Messages::MESSAGE_TYPE_ERROR
                        );
                    }
                } else {
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        $this->lang->line('admin_dashboard_message_columns_saved'),
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    $this->lang->line('admin_dashboard_message_columns_save_fail'),
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
        } else {
            $this->db->trans_rollback();
            $this->messages->add_message(
                $this->lang->line('admin_dashboard_message_columns_save_fail_count'),
                Messages::MESSAGE_TYPE_ERROR
            );
        }
        
        redirect(create_internal_url('admin_dashboard'));
    }
    
}