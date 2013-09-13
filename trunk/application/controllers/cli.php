<?php

/**
 * Controller for CLI requests.
 * @package LIST_CLI_Controllers
 * @author Andrej Jursa
 */
class Cli extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            show_error('This controller can be called only from CLI!');
            die();
        }
    }

    public function index() {
        echo 'This is CLI controller for LIST' . "\n\n";
        
        echo 'Available commands:' . "\n";
        echo '  update_database [migration version]' . "\n";
        echo '  new_teacher' . "\n";
        echo '  lamsfet_import - WARNING: do not execute on live installation' . "\n";
        echo '  clear_lockdown' . "\n";
        echo '  generate_encryption_key' . "\n";
        echo '  apply_lockdown';
    }

    /**
     * Performs an migration update from console.
     * @param integer|null $migration migration number or null to update to the latest migration.
     */
    public function update_database($migration = null) {
        $this->output->set_content_type('text/plain');
        $this->load->database();
        $this->load->library('migration');
        $cleared = $this->_clear_production_cache();
        if (is_null($migration)) {
            $this->migration->latest();
            if ($this->migration->error_string()) {
                echo 'Error occured:' . "\n\n";
                echo $this->migration->error_string();
            } else {
                echo 'SUCCESS!' . "\n";
                echo 'Cache cleared, ' . $cleared . ' files deleted.';
            }
        } elseif (is_numeric($migration) && intval($migration) > 0) {
            $answer = $this->_get_cli_user_input('Do you realy want to update database to version ' . $migration . '? (yes)');
            if ($answer !== 'yes') {
                echo 'Database structure update canceled.';
                return;
            }
            $this->migration->version(intval($migration));
            if ($this->migration->error_string()) {
                echo 'Error occured:' . "\n\n";
                echo $this->migration->error_string();
            } else {
                echo 'SUCCESS!' . "\n";
                echo 'Cache cleared, ' . $cleared . ' files deleted.';
            }
        } else {
            echo 'Can\'t execute command!';
        }
    }
    
    /**
     * Creates new teacher from console.
     */
    public function new_teacher() {
        $this->load->database();
        $languages = $this->lang->get_list_of_languages();
        
        echo 'Create new teacher' . "\n\n";
        $name = $this->_get_cli_user_input('Teacher full name');
        $email = $this->_get_cli_user_input('Teacher e-mail');
        $password = $this->_get_cli_user_input('Teacher password');
        if (count($languages)) {
            echo 'Available languages:';
            foreach($languages as $language_key => $language_value) {
                echo "\n" . '  (' . $language_key . ') for ' . normalize($language_value) . '';
            }
        }
        $language = $this->_get_cli_user_input("\n" . 'Select teacher language');
        
        $this->load->library('form_validation');
            
        if (!$this->form_validation->required($name) || !$this->form_validation->required($email) || !$this->form_validation->required($password) || !$this->form_validation->required($language)) {
            echo 'ERROR: Some parameter(s) is(are) missing.' . "\n";
        } elseif (!$this->form_validation->valid_email($email)) {
            echo 'ERROR: E-mail is invalid.' . "\n";
        } elseif (!$this->form_validation->is_unique($email, 'teachers.email')) {
            echo 'ERROR: E-mail must be unique.' . "\n";
        } elseif (!$this->form_validation->min_length($password, 6)) {
            echo 'ERROR: Password must have at least 6 characters.' . "\n";
        } elseif (!$this->form_validation->max_length($password, 20)) {
            echo 'ERROR: Password must not be longer than 20 characters.' . "\n";
        } elseif (!array_key_exists($language, $languages)) {
            echo 'ERROR: Desired language not found.' . "\n";
        } else {
            $teacher = new Teacher();
            $teacher->fullname = $name;
            $teacher->email = $email;
            $teacher->password = sha1($password);
            $teacher->language = $language;

            $this->db->trans_begin();
            $teacher->save();

            $teacher->get_by_email($email);
            if ($teacher->result_count() == 1) {
                $this->db->trans_commit();
                echo "\n" . 'Teacher account created!';
            } else {
                $this->db->trans_rollback();
                echo "\n" . 'Teacher account failed to be created!';
            }
        }
    }
    
    /**
     * Import LaMSfET data into this LIST installation.
     * @return void
     */
    public function lamsfet_import() {
        $this->config->load('lockdown');
        $this->load->library('configurator');
        $lamsfet_db = $this->load->database('lamsfet', TRUE, TRUE);
        $this->load->database();
        $this->config->load('lamsfet');
        $lamsfet_url = $this->config->item('lamsfet_url');
        
        echo 'This script will import some database data and files from LaMSfET at ' . $lamsfet_url . ' (from application/config/lamsfet.php)' . "\n\n";
        echo 'WARNING: THIS SCRIPT WILL TRUNCATE CONTENT TABLES OF LIST AND DELETE ALL TASK FILES, TASK UNIT TEST FILES AND SOLUTION FILES FROM HARD DRIVE!' . "\n\n";
        $answer = $this->_get_cli_user_input('Do you want to execute this import script? (yes)');
        if ($answer != 'yes') { 
            echo 'Import canceled.' . "\n";
            return;
        }
        $this->load->helper('lamsfet');
        
        echo 'Starting LaMSfET data migration to LIST ...' . "\n\n";
        
        echo 'Locking down LIST ...' . "\n\n";
        $this->configurator->set_config_array('lockdown', array('system_lockdown' => TRUE));
        
        $lamsfet_db->reconnect();
        
        $courses = lamsfet_fetch_table('courses', $lamsfet_db);
        $courses_set_types = lamsfet_fetch_table('courses_set_types', $lamsfet_db);
        $courses_terms = lamsfet_fetch_table('courses_terms', $lamsfet_db);
        $excercise_groups = lamsfet_fetch_table('excercise_groups', $lamsfet_db);
        $labels = lamsfet_fetch_table('labels', $lamsfet_db);
        $set_types = lamsfet_fetch_table('set_types', $lamsfet_db);
        $sets = lamsfet_fetch_table('sets', $lamsfet_db);
        $tasks = lamsfet_fetch_table('tasks', $lamsfet_db);
        $tasks_labels = lamsfet_fetch_table('tasks_labels', $lamsfet_db);
        $tasks_in_sets = lamsfet_fetch_table('tasks_in_sets', $lamsfet_db);
        
        echo "\n";
        
        list_import_prepare();
        
        echo "\n";
        
        $this->db->reconnect();
        list_import_lamsfet_set_types($set_types);
        $this->db->reconnect();
        list_import_lamsfet_courses_and_courses_terms($courses_terms, $courses);
        $this->db->reconnect();
        list_import_lamsfet_courses_set_types_relation($courses_set_types, $courses_terms, $set_types);
        $this->db->reconnect();
        list_import_lamsfet_excercise_groups($excercise_groups, $courses_terms);
        $this->db->reconnect();
        list_import_lamsfet_sets($sets, $set_types, $courses_terms, $excercise_groups);
        $this->db->reconnect();
        list_import_lamsfet_labels($labels);
        $this->db->reconnect();
        list_import_lamsfet_tasks($tasks, $lamsfet_url);
        $this->db->reconnect();
        list_import_lamsfet_tasks_labels_relations($tasks, $labels, $tasks_labels);
        $this->db->reconnect();
        list_import_lamsfet_tasks_in_sets_relation($sets, $tasks, $tasks_in_sets);
        
        echo "\n\n ... DONE!\n\n";
        
        echo 'Unlocking LIST ...';
        $this->configurator->set_config_array('lockdown', array('system_lockdown' => FALSE));
    }
    
    /**
     * Release system lockdown.
     * @return void
     */
    public function clear_lockdown() {
        $this->config->load('lockdown');
        $this->load->library('configurator');
        if ($this->config->item('system_lockdown') === TRUE) {
            echo 'LIST is in system lockdown! Are you sure to release this lockdown?' . "\n";
            echo 'If there is maintenance task in progress, releasing this lockdown you can damage system.' . "\n";
            $answer = $this->_get_cli_user_input('Release lockdown? (yes)');
            if ($answer !== 'yes') {
                echo 'Canceled.';
                return;
            }
            echo 'Releasing system lockdown.';
            $this->configurator->set_config_array('lockdown', array('system_lockdown' => FALSE));
        } else {
            echo 'System lockdown is not set. Operation canceled.';
        }
    }
    
    /**
     * Apply new lockdown.
     */
    public function apply_lockdown() {
        $this->config->load('lockdown');
        $this->load->library('configurator');
        $this->configurator->set_config_array('lockdown', array('system_lockdown' => TRUE));
        echo 'System locked...';
    }
    
    /**
     * Generate new encryption key.
     */
    public function generate_encryption_key() {
        $encryption_key_data = '';
        switch (rand(1, 5)) {
            case 1:
                $encryption_key_data = get_current_user() . rand(1, 1000000) . (time() + rand(-3600, 3600)) . ENVIRONMENT . get_include_path() . memory_get_peak_usage() . memory_get_usage();
            break;
            case 2:
                $encryption_key_data = (time() + rand(-3600, 3600)) . get_current_user() . rand(1, 2000000) . memory_get_peak_usage() . get_include_path() . memory_get_usage() . ENVIRONMENT;
            break;
            case 3:
                $encryption_key_data = (time() + rand(-3600, 3600)) . ENVIRONMENT . memory_get_peak_usage() . get_current_user() . get_include_path() . rand(1, 3000000) . memory_get_usage();
            break;
            case 4:
                $encryption_key_data = memory_get_peak_usage() . (time() + rand(-3600, 3600)) . ENVIRONMENT . rand(1, 4000000) . get_include_path() . memory_get_usage() . get_current_user();
            break;
            case 5:
                $encryption_key_data = rand(1, 5000000) . (time() + rand(-3600, 3600)) . get_include_path() . memory_get_peak_usage() . memory_get_usage() . get_current_user() . ENVIRONMENT;
            break;
        }
        $config = array();
        $config['encryption_key'] = md5($encryption_key_data);
        $this->load->library('configurator');
        $this->configurator->set_config_array('config', $config);
        echo 'Encryption key set to: ' . $config['encryption_key'];
    }

    /**
     * Clear production cache for DataMapper if it is enabled.
     * @return integer number of deleted cache files.
     */
    private function _clear_production_cache() {
        $count = 0;
        $this->config->load('datamapper', TRUE);
        $production_cache = $this->config->item('production_cache', 'datamapper');
        if (!empty($production_cache) && file_exists($production_cache) && is_dir($production_cache)) {
            $production_cache = rtrim($production_cache, '/\\') . DIRECTORY_SEPARATOR;
            $dir_content = scandir($production_cache);
            foreach($dir_content as $item) {
                if (is_file($production_cache . $item) && substr($item, -4) == '.php') {
                    if (@unlink($production_cache . $item)) { $count++; }
                }
            }
        }
        return $count;
    }
    
    /**
     * Displays standard input prompt with message and return answer.
     * @param string $msg message.
     * @return string answer.
     */
    private function _get_cli_user_input($msg) {
        fwrite(STDOUT, "$msg: ");
        $varin = trim(fgets(STDIN));
        return $varin;
    }
    
}