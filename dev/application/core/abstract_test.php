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
    
    /**
     * This method should return real name of test instance. I.e.: Test for java language
     * @return string name of test instance.
     */
    abstract public function get_test_type_name();

    /**
     * Return type of this test.
     * @return string test type.
     */
    public function get_test_type() {
        return $this->test_type;
    }
    
    /**
     * Return array of all possible subtypes in format 'subtype' => 'subtype_name'.
     * Will iterate $this->test_subtypes, which have to be array of subtypes, where subtype is in key.
     * If $this->test_subtypes[subtype]['name'] is set, it will be used as subtype name.
     * @return array<string> array of all possible subtypes with names.
     */
    public function get_test_subtypes() {
        $output = array();
        if (is_array($this->test_subtypes) && count($this->test_subtypes)) {
            foreach ($this->test_subtypes as $subtype => $config) {
                $output[$subtype] = isset($config['name']) ? $this->CI->lang->text($config['name']) : $subtype;
            }
        }
        return $output;
    }
    
    /**
     * Return subtype of initialized test object.
     * Subtype is obtained from Test model after initialization.
     * Object can be initialized with initialize() method.
     * @return string current subtype or NULL if not initialized.
     */
    public function get_current_test_subtype() {
        if (is_array($this->current_test)) {
            return $this->current_test['subtype'];
        }
        return NULL;
    }
    
    /**
     * Return id of initialized test object.
     * Id is obtained from Test model after initialization.
     * Object can be initialized with initialize() method.
     * @return integer current test id or NULL if not initialized.
     */
    public function get_current_test_id() {
        if (is_array($this->current_test)) {
            return (int)$this->current_test['id'];
        }
        return NULL;
    }


    /**
     * Return configuration array of initialized test object.
     * Configuration array is unserialized from Test model after initialization.
     * Object can be initialized with initialize() method.
     * @return array<mixed> current configuration array or NULL if not initialized.
     */
    public function get_current_test_configuration() {
        if (is_array($this->current_test)) {
            return $this->current_test['config'];
        }
        return NULL;
    }
    
    /**
     * Returns value from configuration array of initialized test object.
     * Configuration array is unserialized from Test model after initialization.
     * Object can be initialized with initialize() method.
     * @param string $constant name of constant from array.
     * @return mixed value of constant or NULL if not initialized.
     */
    public function get_current_test_configuration_value($constant) {
        if (is_array($this->current_test) && is_string($constant) && isset($this->current_test['config'][$constant])) {
            return $this->current_test['config'][$constant];
        }
        return NULL;
    }
    
    /**
     * Return path to zip file with source code to test, which is set by run() method.
     * @return string path and file name of zip file with source code.
     */
    public function get_input_zip_file() {
        return $this->zip_file_path;
    }
    
    /**
     * Returns path to directory, where source files from student and test will be copied and run.
     * @return string path to directory.
     * @throws TestException can be thrown if test object is not initialized.
     */
    public function get_current_test_source_directory() {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1900001);
        }
        return 'private/uploads/unit_tests/test_' . $this->get_current_test_id() . '/';
    }

    /**
     * This method will initialize this instance of test object with Test model, or id of test model.
     * @param DataMapper|integer $test_model Test model or id of test record.
     * @throws TestException can be thrown if $test_model is not Test object, test record not exists or test type or subtype is not supported.
     */
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
    
    /**
     * Runs initialized test. It will accept the input zip file (with path) and run the method defined in $this->test_subtypes[current_subtype]['method'].
     * @param string $input_zip_file path to zip file with source code to be tested.
     * @return string result of test in text/html or plain/text form.
     * @throws TestException can be thrown if test object is not initialized, source file is not found or run method is not found.
     */
    public function run($input_zip_file) {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1100001);
        }
        if (!file_exists($input_zip_file)) {
            throw new TestException($this->CI->lang->line('tests_general_error_input_zip_file_not_found'), 1100002);
        }
        $method_name = @$this->test_subtypes[$this->get_current_test_subtype()]['method'];
        if (!method_exists($this, $method_name)) {
            throw new TestException($this->CI->lang->line('tests_general_error_subtype_method_not_found'), 1100003);
        }
        $this->zip_file_path = $input_zip_file;
        return $this->$method_name();
    }
    
    /**
     * Returns path to view file with template for configuration dialog.
     * This view file must be defined in $this->test_subtypes[current_subtype]['configure_view'].
     * @return string path to view template.
     * @throws TestException can be thrown if test object is not initialized or view file is not configured.
     */
    public function get_configure_view() {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1300001);
        }
        if (!isset($this->test_subtypes[$this->get_current_test_subtype()]['configure_view'])) {
            throw new TestException($this->CI->lang->line('tests_general_error_configure_view_not_set'), 1300002);
        }
        return $this->test_subtypes[$this->get_current_test_subtype()]['configure_view'];
    }
    
    /**
     * This method will be run in configuration save process to set validators or validate post input and return validation result.
     * Will use method defined in $this->test_subtypes[current_subtype]['configure_validator'].
     * @return boolean if this method returns FALSE, save action will be stoped and configuration form will be displayed again.
     * @throws TestException can be thrown if test object is not initialized or validator method is not found.
     */
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
    
    /**
     * This method will be call during configuration save process to handle file uploads.
     * Will use method defined in $this->test_subtypes[current_subtype]['configure_uploader'].
     * @param array<mixed> $new_config part of configuration array to be saved (output argument).
     * @return boolean TRUE, if all uploads are successful, FALSE othewise (on FALSE, prevent configuration to be saved).
     * @throws TestException can be thrown if test object is not initialized or uploader method is not found.
     */
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

    /**
     * This method will be call during configuration save process to make final changes to configuration array.
     * Will use method defined in $this->test_subtypes[current_subtype]['configure_before_save'].
     * @param array<mixed> $new_config configuration from configuration form.
     * @return array<mixed> updated configuration.
     * @throws TestException can be thrown if test object is not initialized or particular method not found.
     */
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

    /**
     * Recursively create directory structure.
     * @param string $directory_path directory to create.
     * @throws TestException can be thrown if parent directory is not writable.
     */
    protected function create_directory($directory_path) {
        $trimmed_directory_path = str_replace('\\', DIRECTORY_SEPARATOR, trim($directory_path, '\\/'));
        $path_segments = explode(DIRECTORY_SEPARATOR, $trimmed_directory_path);
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
    
    /**
     * Proccess uploaded file.
     * @param string $folder name of directory inside test working directory where the file will be uploaded.
     * @param string $field_name name of configuration constant and form input (in the form of configuration_test_files_&lt;$field_name&gt;.
     * @param string $allowed allowed file types, pipe separated list of extensions, like: txt|zip
     * @param array<mixed> $additional_config optional additional configuration (see CodeIgniter manual for upload library).
     * @return boolean|array<mixed> return FALSE if upload failed, or uploaded file data on success (see CodeIgniter manual for upload library, specificaly $this->upload->data() method).
     */
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
    
    /**
     * Will check if file upload from desired form input was realized or not.
     * @param string $field_name name of configuration constant and form input (in the form of configuration_test_files_&lt;$field_name&gt;.
     * @return boolean TRUE, if file was upload (or simply error level reported by browser is no 4), FALSE otherwise.
     */
    protected function was_file_sent($field_name) {
        if (isset($_FILES['configuration_test_files_' . $field_name]['error'])) {
            if ($_FILES['configuration_test_files_' . $field_name]['error'] != 4) { return TRUE; }
        }
        return FALSE;
    }
    
    /**
     * Creates working directory for current test. It is either return and set to $this->current_test_directory (for internal use).
     * @return string working directory for current test.
     */
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
    
    /**
     * Recursive delete current test work directory with everything inside.
     */
    protected function delete_test_directory() {
        if (file_exists($this->current_test_directory)) {
            unlink_recursive($this->current_test_directory, TRUE);
        }
    }
    
    /**
     * Will extract zip file to desired subdirectory of current test working directory.
     * @param string $zip_file zip file (with path).
     * @param string $subdirectory subdirectory where to extract files, default is empty.
     * @throws TestException can be thrown if zip file is not found or input file is not valid zip file (can't be open by ZipArchive).
     */
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

    /**
     * Run by constructor, determines type of test from class name.
     * Test class must be named as &lt;type_of_test&gt;_test.
     */
    private function determine_test_type() {
        if ($this->test_type === NULL) {
            $class_name = get_class($this);
            $this->test_type = strtolower(substr($class_name, 0, -5));
        }
    }
    
}

class TestException extends Exception {
    
}