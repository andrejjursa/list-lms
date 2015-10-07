<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Abstract test class.
 * @package LIST_Tests
 * @author Andrej Jursa
 */ 
abstract class abstract_test {
    
    protected $CI = NULL;
    private $test_type = NULL;
    protected $test_subtypes = NULL;
    private $current_test = NULL;
    private $zip_file_path = NULL;
    private $current_test_directory;
    private $last_test_score = 0;
    private $encryption_phrase = '';
    private $test_scoring = NULL;
    private $last_exit_code = 0;

    const TEST_OUTPUT_FILE = '__list_output.txt';
    const TEST_SCORING_FILE = '__list_score.txt';
    
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
     * Returns last test score.
     * @return int test score;
     */
    public function get_last_test_score() {
        $score_total = 0;
        if (is_array($this->test_scoring) && count($this->test_scoring)) {
            foreach ($this->test_scoring as $test_scoring_object) {
                $score_total += (double)$test_scoring_object->current;
            }
            if ($score_total > 100) { $score_total = 100; }
            elseif ($score_total < 0) { $score_total = 0; }
        }
        return $score_total;
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
     * Safely returns current sandbox type.
     * @return string sandbox type.
     */
    public function get_sandbox_type()
    {
        $sandbox_type = strtolower($this->CI->config->item('test_sandbox'));
        $allowed_types = array('implicit', 'docker');
        return in_array($sandbox_type, $allowed_types) ? $sandbox_type : 'implicit';
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
                'enable_scoring' => (int)$test_model->enable_scoring > 0 ? TRUE : FALSE,
                'task_id' => $test_model->task_id,
                'timeout' => $test_model->timeout,
            );
            $this->current_test = $current_test;
        } else {
            throw new TestException($this->CI->lang->line('tests_general_error_test_record_not_exists'), 1000004);
        }
    }
    
    /**
     * Runs initialized test. It will accept the input zip file (with path) and run the method defined in $this->test_subtypes[current_subtype]['method'].
     * @param string $input_zip_file path to zip file with source code to be tested.
     * @param boolean $save_score enables or disables saving score into score database table.
     * @param string $score_token unique identification token for batch test set.
     * @param int|Student $score_student id or initialized student model.
     * @return string result of test in text/html or plain/text form.
     * @throws TestException can be thrown if test object is not initialized, source file is not found or run method is not found.
     */
    public function run($input_zip_file, $save_score = FALSE, $score_token = '', $score_student = NULL) {
        $this->test_scoring = NULL;
        $this->last_exit_code = 0;
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
        return $this->$method_name($save_score, $score_token, $score_student);
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
     * Returns path to js file or NULL if not set.
     * This path must be defined in $this->test_subtypes[current_subtype]['configure_js'].
     * @return string|NULL path to js file or NULL if not set.
     * @throws TestException can be thrown if test object is not initialized.
     */
    public function get_configure_js() {
        if (is_null($this->get_current_test_subtype())) {
            throw new TestException($this->CI->lang->line('tests_general_error_test_not_initialized'), 1310001);
        }
        return isset($this->test_subtypes[$this->get_current_test_subtype()]['configure_js']) ? $this->test_subtypes[$this->get_current_test_subtype()]['configure_js'] : NULL;
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
     * Call this function to get last test scoring array.
     * @return string|null serialized array of scoring objects or null on error.
     */
    public function get_last_test_scoring() {
        if (!is_null($this->test_scoring)) {
            return serialize($this->test_scoring);
        }
        return $this->test_scoring;
    }

    /**
     * This function will return last exit code for last test run.
     * @return int last exit code
     */
    public function get_last_exit_code() {
        return $this->last_exit_code;
    }

    /**
     * Call this function from test to set last exit code.
     * @param int $exit_code exit code to remember.
     */
    protected function set_last_exit_code($exit_code) {
        $this->last_exit_code = $exit_code;
    }

    /**
     * Call this function to construct evaluation table data for I/O test scoring.
     * @param $percents current value achieved.
     * @param $maximum maximum value.
     * @param $name name of test.
     */
    protected function construct_io_test_result($percents, $maximum, $name='lang:tasks_test_result_score_name_io_test') {
        $score = new stdClass();
        $score->name = $name;
        $score->current = (double)$percents;
        $score->maximum = (double)$maximum;
        $score->maximum = $score->maximum > 100 ? 100 : ($score->maximum < 0 ? 0 : $score->maximum);
        $score->current = $score->current > $score->maximum ? $score->maximum : ($score->current < 0 ? 0 : $score->current);
        $this->test_scoring = array(
            $score
        );
    }

    /**
     * Decode scoring and return it as decoded JSON array.
     * @param $score encrypted scoring string.
     * @return mixed decoded scoring array.
     * @throws Exception on error like MD5 checksum does not match the score or there is wrong format of scoring output text.
     */
    protected function decode_scoring($score) {
        $lines = explode("\n", $score);
        if (count($lines)) { foreach ($lines as $key => $value) {
            $lines[$key] = base64_decode(trim($value));
        }}
        $this->test_scoring = NULL;
        if (count($lines) == 2) {
            if (mb_strlen($lines[0]) == 32) {
                $md5 = $this->decode_scoring_single_line($lines[0]);
                $json = $this->decode_scoring_single_line($lines[1], 32);
                if (md5($json) == $md5) {
                    $this->test_scoring = json_decode($json);
                    return $this->test_scoring;
                }
                throw new Exception('MD5 checksum does not match!');
            }
        }
        throw new Exception('Wrong output format!');
    }

    /**
     * Decode single line of encrypted scoring.
     * @param $text scoring text to decode.
     * @param int $offset offset in encryption phrase.
     * @return string doceded scoring text.
     */
    private function decode_scoring_single_line($text, $offset = 0) {
        $output = '';

        for ($i = 0; $i < mb_strlen($text); $i++) {
            $output .= chr(ord(mb_substr($text, $i, 1)) ^ ord(mb_substr($this->encryption_phrase, $i + $offset % mb_strlen($this->encryption_phrase))));
        }

        return $output;
    }

    /**
     * @param $path path to directory with test files extracted.
     * @return null|string encryption phrase as string or NULL on error.
     */
    protected function create_encryption_phrase($path) {
        if (file_exists($path) && is_dir($path)) {
            $filepath = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . '__list_encrypt_phrase.txt';
            $lexicon = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.?!\\/\'"^&*(){}[];,%$#@~';
            $phrase = '';
            for ($i = 0; $i < 8192; $i++) {
                $char_index = rand(0, strlen($lexicon) - 1);
                $phrase .= $lexicon[$char_index];
            }
            $f = NULL;
            try {
                $f = fopen($filepath, 'w');
                fwrite($f, $phrase);
                fclose($f);
                $this->encryption_phrase = $phrase;
                return $phrase;
            } catch (Exception $e) {
                if ($f !== NULL && file_exists($filepath)) {
                    fclose($f);
                }
                $this->encryption_phrase = NULL;
                return NULL;
            }
        } else {
            $this->encryption_phrase = NULL;
            return NULL;
        }
    }
    
    /**
     * Return path of directory with test execution scripts.
     * @return string path to directory.
     */
    protected function get_test_scripts_directory() {
        $path = rtrim(getcwd(), '\\/') . '/';
        
        $path .= 'test_scripts/';
        
        if (file_exists($path . ENVIRONMENT . '/execute_test')) {
            $path .= ENVIRONMENT . '/';
        }
        
        return $path;
    }
    
    /**
     * Return content of test output file.
     * @param string $output_file name of output file (with path inside working directory).
     * @return string content of output file.
     */
    protected function read_output_file($output_file) {
        $output = '';
        if (file_exists($this->current_test_directory . $output_file)) {
            $f = fopen($this->current_test_directory . $output_file, 'r');
            while (!feof(($f))) {
                $output .= fread($f, 1024);
            }
            fclose($f);
        }
        return $output;
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
     * @param string $allowed allowed file types, pipe separated list of extensions, like: txt|zip.
     * @param array<mixed> $additional_config optional additional configuration (see CodeIgniter manual for upload library).
     * @param boolean $single_file_handle set to TRUE means that additional file types will be handled as single file and ZIPed.
     * @param string $single_file_allowed file types for single file, pipe separated list of extensions, like: txt|zip.
     * @param string $single_file_target_zip_filename target zip file name.
     * @param string $single_file_additional_config additional upload configuration.
     * @return boolean|array<mixed> return FALSE if upload failed, or uploaded file data on success (see CodeIgniter manual for upload library, specificaly $this->upload->data() method).
     */
    protected function upload_file($folder, $field_name, $allowed = 'zip', $additional_config = array(), $single_file_handle = FALSE, $single_file_allowed = '', $single_file_target_zip_filename = '', $single_file_additional_config = array()) {
        if ($single_file_handle) {
            $this->CI->load->library('upload');
            $mimes_zip = $this->CI->upload->mimes_types('zip');
            $mimes_zip = is_array($mimes_zip) ? $mimes_zip : array($mimes_zip);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_location = $_FILES['configuration_test_files_' . $field_name]['tmp_name'];
            $file_mime_type = finfo_file($finfo, $file_location);
            finfo_close($finfo);
            if (!in_array($file_mime_type, $mimes_zip)) {
                if (array_key_exists('upload_path', $single_file_additional_config)) { unset($single_file_additional_config['upload_path']); }
                if (array_key_exists('allowed_types', $single_file_additional_config)) { unset($single_file_additional_config['allowed_types']); }
                if (array_key_exists('encrypt_name', $single_file_additional_config)) { unset($single_file_additional_config['encrypt_name']); }
                $config = array_merge(array(
                    'upload_path' => 'private/uploads/unit_tests/test_' . $this->get_current_test_id() . '/' . trim($folder, '\\/') . '/',
                    'allowed_types' => $single_file_allowed,
                    'max_size' => 2048,
                    'encrypt_name' => TRUE,
                ), $single_file_additional_config);
                $this->CI->load->library('upload');
                $this->CI->upload->initialize($config);
                $this->create_directory($config['upload_path']);
                if ($this->CI->upload->do_upload('configuration_test_files_' . $field_name)) {
                    $data = $this->CI->upload->data();
                    if ($this->zip_plain_file_to_archive($single_file_target_zip_filename, $config['upload_path'], $data['file_name'], $data['orig_name'])) {
                        $data['file_name'] = $single_file_target_zip_filename;
                        return $data;
                    } else {
                        $this->CI->parser->assign('configuration_test_files_' . $field_name . '_error', $this->lang->line('tests_general_error_single_file_zip_error'));
                        return FALSE;
                    }
                } else {
                    $this->CI->parser->assign('configuration_test_files_' . $field_name . '_error', $this->CI->upload->display_errors('', '') . ' (' . $file_mime_type . ')');
                    return FALSE;
                }
            }
        }
        
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
            $this->CI->parser->assign('configuration_test_files_' . $field_name . '_error', $this->CI->upload->display_errors('', ''));
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
    
    protected function copy_file_to($file, $subdirectory='') {
        if (file_exists($file)) {
            $this->create_directory(ltrim($this->current_test_directory . $subdirectory, '\\/'));
            if (!@copy($file, ltrim($this->current_test_directory . $subdirectory, '\\/') . '/' . basename($file))) {
                throw new TestException(sprintf($this->CI->lang->line('tests_general_error_file_copy_error'), basename($file), ltrim($this->current_test_directory . $subdirectory, '\\/') . '/'), 1800003);
            }
        } else {
            throw new TestException(sprintf($this->CI->lang->line('tests_general_error_file_not_found'), $file), 1800001);
        }
    }
    
    /**
     * Adds information about score into database.
     * @param int $score score value from 0 to 100, or more for bonus points.
     * @param int|Student $student_id student id or student model.
     * @param string $token string token.
     * @return void returns nothing.
     */
    protected function save_test_result($score, $student_id, $token) {
        if (!$this->current_test['enable_scoring']) { 
            $this->last_test_score = 0;
            return;           
        }
        $this->last_test_score = $score;
        
        /*
        $this->CI->load->model('test_score');
        
        if (is_object($student_id) && $student_id instanceOf DataMapper) {
            $student_id = (int)$student_id->id;
        }
        
        $this->CI->test_score->set_score_for_task($student_id, $this->current_test['task_id'], $token, $score, $this->test_type);*/
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
    
    /**
     * Add not zip file into zip archive.
     * @param string $zip_name zip archive name.
     * @param string $path path to file and zip archive.
     * @param string $file_to_zip name of file to add to zip archive.
     * @param string $original_file_name original name of file.
     * @return boolean TRUE, if file is added to zip archive.
     */
    private function zip_plain_file_to_archive($zip_name, $path, $file_to_zip, $original_file_name) {
        $clear_path = rtrim($path, '/\\') . '/';
        if (file_exists($clear_path . $file_to_zip)) {
            $rand_zip_name = '';
            do {
                $rand_zip_name = 'temp_' . rand(1000, 9999) . '_' . $zip_name;
            } while (file_exists($clear_path . $rand_zip_name));
            
            $zip = new ZipArchive();
            if ($zip->open($clear_path . $rand_zip_name, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($clear_path . $file_to_zip, $original_file_name);
                $zip->close();
                @unlink($clear_path . $file_to_zip);
                if (file_exists($clear_path . $zip_name)) {
                    @unlink($clear_path . $zip_name);
                }
                rename($clear_path . $rand_zip_name, $clear_path . $zip_name);
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Returns test timeout.
     * @return int timeout.
     */
    protected function get_test_timeout() {
        $timeout = (int)$this->current_test['timeout'];
        return $timeout >= 100 ? $timeout : 100;
    }
    
    /**
     * Truncate test output to given number of lines. Only lines with text will be count.
     * @param string $text test output text.
     * @param integer $lines maximum number of lines.
     * @return string truncated text.
     */
    protected function truncate_lines($text, $lines = 0) {
        if (is_null($lines) || !is_numeric($lines) || $lines <= 0) { return $text; }
        $text_array = explode("\n", $text);
        if (count($text_array) <= $lines) { return $text; }
        
        $output = '';
        
        $lns = 0;
        
        foreach ($text_array as $line) {
            if (trim(strip_tags($line)) !== '') {
                $lns++;
            }
            $output .= $line . "\n";
            if ($lns == $lines) {
                if (strpos($line, '</pre') === FALSE) {
                    $output .= '</pre>';
                }
                break;
            }
        }
        
        return rtrim($output);
    }
    
    /**
     * Converts html special chars and protect &lt;br /&gt; tags.
     * @param string $output test output.
     * @return string converted output.
     */
    protected function encode_output($output) {
        $find = array(
            '/\&lt\;br[\s]*[\/]?\&gt\;/i',
            '/\&lt\;pre[\s]*\&gt\;/i',
            '/\&lt\;\/pre[\s]*\&gt\;/i',
        );
        $replace = array(
            '<br />',
            '<pre>',
            '</pre>',
        );
        return preg_replace($find, $replace, htmlspecialchars($output));
    }
}

class TestException extends Exception {
    
}
