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
     * @param string $name teacher full name.
     * @param string $email teacher e-mail.
     * @param string $password teacher password.
     * @param string $language teacher default language.
     */
    public function new_teacher($name = null, $email = null, $password = null, $language = null) {
        $languages = $this->lang->get_list_of_languages();
        if (is_null($name) && is_null($email) && is_null($password) && is_null($language)) {
            echo 'Create new teacher' . "\n\n";
            echo ' - param 1: teacher name' . "\n";
            echo ' - param 2: teacher e-mail (use _AT_ as @)' . "\n";
            echo ' - param 3: teacher password' . "\n";
            echo ' - param 4: teacher language' . "\n";
            if (count($languages)) {
                echo '      can be:';
                foreach($languages as $language_key => $language_value) {
                    echo "\n" . '        ' . $language_key . ' (for ' . normalize($language_value) . ')';
                }
            }
        } else {
            $email = str_replace('_AT_', '@', $email);
            echo 'Creating teacher with:' . "\n";
            echo '  name:     ' . $name . "\n";
            echo '  email:    ' . $email . "\n";
            echo '  password: ' . $password . "\n";
            echo '  language: ' . $language . "\n";
            
            $this->load->library('form_validation');
            $this->load->database();
            
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
    }
    
    public function lamsfet_import() {
        $lamsfet_url = 'http://capek.ii.fmph.uniba.sk/lamsfet/';
        
        echo 'This script will import some database data and files from LaMSfET at ' . $lamsfet_url = 'http://capek.ii.fmph.uniba.sk/lamsfet/' . "\n\n";
        echo 'WARNING: THIS SCRIPT WILL TRUNCATE CONTENT TABLES OF LIST AND DELETE ALL TASK FILES, TASK UNIT TEST FILES AND SOLUTION FILES FROM HARD DRIVE!' . "\n\n";
        $answer = $this->get_cli_user_input('Do you want to execute this import script? (yes)');
        if ($answer != 'yes') { 
            echo 'Import canceled.' . "\n";
            return;
        }
        $this->load->helper('lamsfet');
        
        $lamsfet_db = $this->load->database('lamsfet', TRUE, TRUE);
        echo 'Starting LaMSfET data migration to LIST ...' . "\n\n";
        
        $labels = lamsfet_fetch_table('labels', $lamsfet_db);
        $set_types = lamsfet_fetch_table('set_types', $lamsfet_db);
        $sets = lamsfet_fetch_table('sets', $lamsfet_db);
        $tasks = lamsfet_fetch_table('tasks', $lamsfet_db);
        $tasks_labels = lamsfet_fetch_table('tasks_labels', $lamsfet_db);
        $tasks_in_sets = lamsfet_fetch_table('tasks_in_sets', $lamsfet_db);
        
        $this->load->database();
        
        list_import_prepare();
        
        list_import_lamsfet_labels($labels);
        list_import_lamsfet_tasks($tasks, $lamsfet_url);
        list_import_lamsfet_tasks_labels_relations($tasks, $labels, $tasks_labels);
        list_import_lamsfet_set_types($set_types);
        list_import_lamsfet_sets($sets, $set_types);
        list_import_lamsfet_tasks_in_sets_relation($sets, $tasks, $tasks_in_sets);
        
        echo "\n\n ... DONE!";
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
    
    private function get_cli_user_input($msg) {
        fwrite(STDOUT, "$msg: ");
        $varin = trim(fgets(STDIN));
        return $varin;
    }
    
}