<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tests controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Tests extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function new_test_form($task_id) {
        $this->load->helper('tests');
        $tests = get_all_supported_test_types_and_subtypes();
        $task = new Task();
        $task->get_by_id($task_id);
        $this->parser->add_js_file('jquery.activeform.js');
        $this->parser->add_js_file('admin_tests/new_test_form.js');
        $this->parser->parse('backend/tests/new_test_form.tpl', array(
            'test_types' => $tests['types'],
            'test_subtypes' => $tests['subtypes'],
            'task' => $task,
        ));
    }
    
    public function prepare_new_test($task_id) {
        $task = new Task();
        $task->get_by_id($task_id);
        if (!$task->exists()) {
            $this->new_test_form($task_id);
        } else {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('test[name]', 'lang:admin_tests_test_form_field_name', 'required');
            $this->form_validation->set_rules('test[type]', 'lang:admin_tests_test_form_field_type', 'required');
            $this->form_validation->set_rules('test[subtype]', 'lang:admin_tests_test_form_field_subtype', 'required');
            
            if ($this->form_validation->run()) {
                $this->_transaction_isolation();
                $this->db->trans_begin();
                $test_data = $this->input->post('test');
                $test = new Test();
                $test->name = trim($test_data['name']);
                $test->type = $test_data['type'];
                $test->subtype = $test_data['subtype'];
                $test->enabled = 0;
                $test->configuration = serialize(array());
                if ($test->save($task) && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    @mkdir('private/uploads/unit_tests/test_' . $test->id, DIR_READ_MODE);
                    $this->messages->add_message('lang:admin_tests_flash_message_new_test_saved', Messages::MESSAGE_TYPE_SUCCESS);
                    redirect(create_internal_url('admin_tests/configure_test/' . $test->id));
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_tests_flash_message_new_test_failed', Messages::MESSAGE_TYPE_ERROR);
                    redirect(create_internal_url('admin_tests/new_test_form/' . $task->id));
                }
            } else {
                $this->new_test_form($task_id);
            }
        }
    }
    
    public function configure_test($test_id) {
        $test = new Test();
        $test->include_related('task');
        $test->get_by_id($test_id);
        $error_message = NULL;
        $test_config_view = '';
        $configuration = array();
        if ($test->exists()) {
            $configuration = @unserialize($test->configuration);
            if ($configuration === FALSE) { $configuration = array(); }
            $test_class = $this->load->test($test->type);
            try {
                $test_class->initialize($test);
                $test_config_view = $test_class->get_configure_view();
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
        } else {
            $error_message = 'lang:admin_tasks_error_cant_find_test';
        }
        $this->parser->parse('backend/tests/configure_test.tpl', array(
            'test' => $test,
            'test_config_view' => $test_config_view,
            'confuguration' => $configuration,
            'error_message' => $error_message,
        ));
    }
    
    public function all_tests($task_id) {
        $this->load->helper('tests');
        $tests_data = get_all_supported_test_types_and_subtypes();
        $task = new Task();
        $task->get_by_id($task_id);
        $tests = new Test();
        if ($task->exists()) {
            $tests->where_related($task);
            $tests->order_by('type', 'asc');
            $tests->order_by('subtype', 'asc');
            $tests->get_iterated();
        }
        $this->parser->parse('backend/tests/all_tests.tpl', array(
            'test_types' => $tests_data['types'],
            'test_subtypes' => $tests_data['subtypes'],
            'task' => $task,
            'tests' => $tests,
        ));
    }
}