<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dashboard controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Dashboard extends LIST_Controller {
    
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
        $this->_select_teacher_menu_pagetag('dashboard');
        
        $this->load->helper('widget');
        
        $widget_types_list = get_admin_widget_list();
        
        $this->parser->assign('widget_types_list', $widget_types_list);
        
        $this->parser->add_js_file('admin_dashboard/widgets.js');
        
        $widgets = new Admin_widget();
        $widgets->where_related_teacher('id', $this->usermanager->get_teacher_id());
        $widgets->get_iterated();
        
        $widget_list = array();
        
        foreach ($widgets as $widget) {
            $widget_list[] = $widget->id;
        }
        
        $this->parser->assign('widget_list', $widget_list);
        
        $this->parser->parse('backend/dashboard/index.tpl');
    }
    
    public function add_widget() {
        $widget_type = $this->input->post('widget_type');
        if ($widget_type !== '') {
            $widget = new Admin_widget();
            $widget->teacher_id = $this->usermanager->get_teacher_id();
            $widget->widget_type = $widget_type;
            $widget->widget_config = serialize(array());
            $widget->save();
            $this->messages->add_message($this->lang->line('admin_dashboard_message_widget_created'), Messages::MESSAGE_TYPE_SUCCESS);
        } else {
            $this->messages->add_message($this->lang->line('admin_dashboard_message_widget_creation_failed'), Messages::MESSAGE_TYPE_ERROR);
        }
        redirect(create_internal_url('admin_dashboard'));
    }
    
}