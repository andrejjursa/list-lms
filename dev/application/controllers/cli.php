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
        if (is_null($migration)) {
            $this->migration->latest();
            if ($this->migration->error_string()) {
                echo 'Error occured:' . "\n\n";
                echo $this->migration->error_string();
            } else {
                echo 'SUCCESS!';
            }
        } elseif (is_numeric($migration) && intval($migration) > 0) {
            $this->migration->version(intval($migration));
            if ($this->migration->error_string()) {
                echo 'Error occured:' . "\n\n";
                echo $this->migration->error_string();
            } else {
                echo 'SUCCESS!';
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
            echo ' - param 2: teacher e-mail' . "\n";
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
    
}