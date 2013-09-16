<?php

abstract class abstract_test {
    
    protected $CI = NULL;
    private $test_type = NULL;
    protected $test_subtypes = NULL;
    private $current_test = NULL;
    private $zip_file_path = NULL;
    private $current_test_directory;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->determine_test_type();
        $this->CI->lang->load('tests/' . $this->test_type);
        $this->CI->lang->load('tests_general');
    }
    
    abstract public function get_test_type_name();

    public function get_test_type() {
        return $this->test_type;
    }
    
    public function get_test_subtypes() {
        $output = array();
        if (is_array($this->test_subtypes) && count($this->test_subtypes)) {
            foreach ($this->test_subtypes as $subtype => $config) {
                $output[$subtype] = isset($config['name']) ? $this->CI->lang->text($config['name']) : $subtype;
            }
        }
        return $output;
    }
    
    public function get_current_test_subtype() {
        if (is_array($this->current_test)) {
            return $this->current_test['subtype'];
        }
        return NULL;
    }
    
    public function get_current_test_id() {
        if (is_array($this->current_test)) {
            return (int)$this->current_test['id'];
        }
        return NULL;
    }


    public function get_current_test_configuration() {
        if (is_array($this->current_test)) {
            return $this->current_test['config'];
        }
        return NULL;
    }
    
    public function get_current_test_configuration_value($constant) {
        if (is_array($this->current_test) && is_string($constant) && isset($this->current_test['config'][$constant])) {
            return $this->current_test['config'][$constant];
        }
        return NULL;
    }
    
    public function get_input_zip_file() {
        return $this->zip_file_path;
    }
    
    public function get_current_test_source_directory() {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1900001);
        }
        return 'private/uploads/unit_tests/test_' . $this->get_current_test_id() . '/';
    }

    public function initialize($test_model) {
        if (is_object($test_model) && !($test_model instanceof Test)) {
            throw new TestException($this->CI->lang->line('tests_general_error_cant_initialize_with_non_test_model'), 1000001);
        } elseif (is_integer($test_model)) {
            $test_model_id = $test_model;
            $test_model = new Test();
            $test_model->get_by_id($test_model_id);
        }
        
        if ($test_model->exists()) {
            if ($test_model->type !== $this->test_type) {
                throw new TestException(sprintf($this->CI->lang->line('tests_general_error_test_type_is_not_supported'), get_class($this)), 1000002);
            } elseif (!array_key_exists($test_model->subtype, $this->test_subtypes)) {
                throw new TestException(sprintf($this->CI->lang->line('tests_general_error_test_subtype_is_not_supported'), get_class($this)), 1000003);
            }
            $current_test = array(
                'subtype' => $test_model->subtype,
                'config' => unserialize($test_model->configuration),
                'id' => $test_model->id,
            );
            $this->current_test = $current_test;
        } else {
            throw new TestException($this->CI->lang->line('tests_general_error_test_record_not_exists'), 1000004);
        }
    }
    
    public function run($input_zip_file) {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1100001);
        }
        if (!file_exists($input_zip_file)) {
            throw new TestException($this->CI->lang->line('tests_general_error_input_zip_file_not_found'), 1100002);
        }
        $method_name = $this->test_subtypes[$this->get_current_test_subtype()]['method'];
        if (!method_exists($this, $method_name)) {
            throw new TestException($this->CI->lang->line('tests_general_error_subtype_method_not_found'), 1100003);
        }
        $this->zip_file_path = $input_zip_file;
        return $this->$method_name();
    }
    
    public function get_configure_view() {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1300001);
        }
        if (!isset($this->test_subtypes[$this->get_current_test_subtype()]['configure_view'])) {
            throw new TestException($this->CI->lang->line('tests_general_error_configure_view_not_set'), 1300002);
        }
        return $this->test_subtypes[$this->get_current_test_subtype()]['configure_view'];
    }
    
    public function validate_test_configuration() {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1400001);
        }
        if (isset($this->test_subtypes[$this->get_current_test_subtype()]['configure_validator'])) {
            if (!method_exists($this, $this->test_subtypes[$this->get_current_test_subtype()]['configure_validator'])) {
                throw new TestException($this->CI->lang->line('tests_general_error_configure_validator_not_exists'), 1400002);
            }
            $method_name = $this->test_subtypes[$this->get_current_test_subtype()]['configure_validator'];
            return $this->$method_name();
        }
        return TRUE;
    }
    
    public function handle_uploads(&$new_config) {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1500001);
        }
        $new_config = array();
        if (isset($this->test_subtypes[$this->get_current_test_subtype()]['configure_uploader'])) {
            if (!method_exists($this, $this->test_subtypes[$this->get_current_test_subtype()]['configure_uploader'])) {
                throw new TestException($this->CI->lang->line('tests_general_error_configure_uploader_not_exists'), 1600002);
            }
            $method_name = $this->test_subtypes[$this->get_current_test_subtype()]['configure_uploader'];
            return $this->$method_name($new_config);
        }
        return TRUE;  
    }

    public function prepare_test_configuration($new_config) {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1700001);
        }
        if (isset($this->test_subtypes[$this->get_current_test_subtype()]['configure_before_save'])) {
            if (!method_exists($this, $this->test_subtypes[$this->get_current_test_subtype()]['configure_before_save'])) {
                throw new TestException($this->CI->lang->line('tests_general_error_configure_before_save_not_exists'), 1700002);
            }
            $method_name = $this->test_subtypes[$this->get_current_test_subtype()]['configure_before_save'];
            return $this->$method_name($new_config);
        }
        return $new_config;
    }

    protected function create_directory($app_full_path) {
        $trimmed_full_path = str_replace('\\', DIRECTORY_SEPARATOR, trim($app_full_path, '\\/'));
        $path_segments = explode(DIRECTORY_SEPARATOR, $trimmed_full_path);
        $path_to_create = '';
        $old_path_to_create = $path_to_create;
        foreach ($path_segments as $path_segment) {
            $path_to_create .= ($path_to_create != '' ? DIRECTORY_SEPARATOR : '') . $path_segment;
            if (!file_exists($path_to_create)) {
                if (!mkdir($path_to_create, DIR_READ_MODE)) {
                    throw new TestException(sprintf($this->CI->lang->line('tests_general_error_cant_create_path'), $path_to_create), 1200001);
                }
            }
            $old_path_to_create = $path_to_create;
        }
    }
    
    protected function upload_file($folder, $field_name, $allowed = 'zip', $additional_config = array()) {
        if (array_key_exists('upload_path', $additional_config)) { unset($additional_config['upload_path']); }
        if (array_key_exists('allowed_types', $additional_config)) { unset($additional_config['allowed_types']); }
        $config = array_merge(array(
            'upload_path' => 'private/uploads/unit_tests/test_' . $this->get_current_test_id() . '/' . trim($folder, '\\/') . '/',
            'allowed_types' => $allowed,
            'max_size' => 2048,
        ), $additional_config);
        $this->CI->load->library('upload');
        $this->CI->upload->initialize($config);
        $this->create_directory($config['upload_path']);
        if ($this->CI->upload->do_upload('configuration_test_files_' . $field_name)) {
            return $this->CI->upload->data();
        } else {
            $this->CI->parser->assign('configuration_test_files_' . $field_name . '_error', $this->CI->upload->display_errors());
            return FALSE;
        }
    }
    
    protected function was_file_sent($field_name) {
        if (isset($_FILES['configuration_test_files_' . $field_name]['error'])) {
            if ($_FILES['configuration_test_files_' . $field_name]['error'] != 4) { return TRUE; }
        }
        return FALSE;
    }
    
    protected function make_test_directory() {
        $random_folder = '';
        $tests_folder = 'private/test_to_execute/';
        do {
            $random_folder = 'test_' . $this->get_current_test_id() . '_' . substr(md5(time() . rand(-99999999, 99999999)), rand(0, 19), 12);
        } while (file_exists($tests_folder . $random_folder));
        $this->create_directory($tests_folder . $random_folder);
        $this->current_test_directory = $tests_folder . $random_folder . '/';
        return $tests_folder . $random_folder . '/';
    }
    
    protected function delete_test_directory() {
        if (file_exists($this->current_test_directory)) {
            unlink_recursive($this->current_test_directory, TRUE);
        }
    }
    
    protected function extract_zip_to($zip_file, $subdirectory='') {
        if (file_exists($zip_file)) {
            $this->create_directory(ltrim($this->current_test_directory . $subdirectory, '\\/'));
            $zip = new ZipArchive();
            if ($zip->open($zip_file) === TRUE) {
                $zip->extractTo(ltrim($this->current_test_directory . $subdirectory, '\\/') . '/');
                $zip->close();
            } else {
                throw new TestException(sprintf($this->CI->lang->line('tests_general_error_not_a_zip_file'), $zip_file), 1800002);
            }
        } else {
            throw new TestException(sprintf($this->CI->lang->line('tests_general_error_file_not_found'), $zip_file), 1800001);
        }
    }

    private function determine_test_type() {
        if ($this->test_type === NULL) {
            $class_name = get_class($this);
            $this->test_type = strtolower(substr($class_name, 0, -5));
        }
    }
    
}

class TestException extends Exception {
    
}