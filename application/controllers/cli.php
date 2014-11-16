<?php

/**
 * Controller for CLI requests.
 * @package LIST_CLI_Controllers
 * @author Andrej Jursa
 */
class Cli extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request() && $this->router->method != 'send_deadline_notifications') {
            show_error('This controller can be called only from CLI!');
            die();
        }
    }
    
    public function __destruct() {
        echo "\n";
    }

    public function index() {
        echo 'This is CLI controller for LIST' . "\n\n";
        
        echo 'Available commands:' . "\n";
        echo '  update_database [migration version]' . "\n";
        echo '  new_teacher' . "\n";
        echo '  lamsfet_import - WARNING: do not execute on live installation' . "\n";
        echo '  clear_lockdown' . "\n";
        echo '  generate_encryption_key' . "\n";
        echo '  apply_lockdown' . "\n";
        echo '  fix_broken_link' . "\n";
        echo '  send_deadline_notifications' . "\n";
        echo '  garbage_collector';
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
                $this->_recreate_production_cache();
                echo 'Cache refreshed, ' . $cleared . ' old cache files deleted.';
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
                $this->_recreate_production_cache();
                echo 'Cache refreshed, ' . $cleared . ' old cache files deleted.';
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
        
        echo 'Unlocking LIST ...' . "\n";
        $this->configurator->set_config_array('lockdown', array('system_lockdown' => FALSE));
    }
    
    /**
     * Release system lockdown.
     * @return void
     */
    public function clear_lockdown() {
        $this->config->load('lockdown');
        $this->load->library('configurator');
        echo 'Releasing system lockdown...' . "\n";
        $this->configurator->set_config_array('lockdown', array('system_lockdown' => FALSE));
    }
    
    /**
     * Apply new lockdown.
     */
    public function apply_lockdown() {
        $this->config->load('lockdown');
        $this->load->library('configurator');
        $this->configurator->set_config_array('lockdown', array('system_lockdown' => TRUE));
        echo 'System locked...' . "\n";
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
    
    public function fix_broken_link() {
        $this->load->database();
        $this->load->helper('lamsfet');
        echo 'This script will repair all broken links in tasks.' . "\n\n";
        echo 'Please specify the broken prefix here.' . "\n\n";
        echo 'Example:' . "\n";
        echo 'Your installation is: http://www.domain.com/list/' . "\n";
        echo 'Your links in tasks starts with: list/index.php/....' . "\n";
        echo 'But they should be: index.php/...' . "\n";
        echo 'Then the prefix is: list/' . "\n\n";
        $broken_prefix = $this->_get_cli_user_input('Your broken prefix');
        echo "\n\n";
        $this->apply_lockdown();
        fix_broken_tasks_links($broken_prefix);
        $this->clear_lockdown();
    }
    
    public function send_deadline_notifications($lang_idiom = NULL) {
        $this->load->database();
        
        $this->load->model('translations');
        if (!is_null($lang_idiom)) {
            $this->lang->reinitialize_for_idiom($lang_idiom);
        }
        $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
        $this->lang->add_custom_translations($translations);
        $this->lang->load('cli');
        
        $current_time = Date('Y-m-d H:i:s');
        $one_day_back_time = Date('Y-m-d H:i:s', strtotime('now -1 day'));
        
        $task_sets1 = new Task_set();
        $task_sets1->select('id, name, course_id, group_id AS common_group_id, upload_end_time AS common_upload_end_time, deadline_notified AS common_deadline_notified, deadline_notification_emails AS common_deadline_notification_emails, deadline_notification_emails_handler AS common_deadline_notification_emails_handler');
        $task_sets1->select('null AS `task_set_permission_id`', FALSE);
        $task_sets1->where('deadline_notified', 0);
        $task_sets1->where('deadline_notification_emails_handler >', 0);
        $task_sets1->group_start();
            $task_sets1->not_group_start();
                $task_sets1->where('upload_end_time', NULL);
            $task_sets1->group_end();
            $task_sets1->where('upload_end_time <', $current_time);
            $task_sets1->where('upload_end_time >=', $one_day_back_time);
        $task_sets1->group_end();
        $task_sets1->where_subquery(0, '(SELECT COUNT(`tsp`.`id`) AS `count` FROM `task_set_permissions` tsp WHERE `tsp`.`task_set_id` = `task_sets`.`id` AND `tsp`.`enabled` = 1)');
        $task_sets1->where('published', 1);
        
        $task_sets2 = new Task_set();
        $task_sets2->select('id, name, course_id');
        $task_sets2->include_related('task_set_permission', 'group_id', 'common');
        $task_sets2->include_related('task_set_permission', 'upload_end_time', 'common');
        $task_sets2->include_related('task_set_permission', 'deadline_notified', 'common');
        $task_sets2->include_related('task_set_permission', 'deadline_notification_emails', 'common');
        $task_sets2->include_related('task_set_permission', 'deadline_notification_emails_handler', 'common');
        $task_sets2->include_related('task_set_permission', 'id');
        $task_sets2->where_related('task_set_permission', 'enabled', 1);
        $task_sets2->where_related('task_set_permission', 'deadline_notified', 0);
        $task_sets2->where_related('task_set_permission', 'deadline_notification_emails_handler >', 0);
        $task_sets2->group_start();
            $task_sets2->not_group_start();
                $task_sets2->where_related('task_set_permission', 'upload_end_time', NULL);
            $task_sets2->group_end();
            $task_sets2->where_related('task_set_permission', 'upload_end_time <', $current_time);
            $task_sets2->where_related('task_set_permission', 'upload_end_time >=', $one_day_back_time);
        $task_sets2->group_end();
        $task_sets2->where('published', 1);
        
        $task_sets1->union_iterated($task_sets2, TRUE);
        
        $this->load->library('email');
        
        $sent_notifications = 0;
        
        foreach ($task_sets1 as $task_set) {
            if ($task_set->common_deadline_notification_emails_handler > 0) {
                $emails = trim($task_set->common_deadline_notification_emails) != '' ? explode(',', $task_set->common_deadline_notification_emails) : array();
                array_walk($emails, function(&$email, $key) {
                    $email = trim($email);
                });
                if ($task_set->common_deadline_notification_emails_handler == 1) {
                    $groups = new Group();
                    $groups->where_related('course', 'id', $task_set->course_id);
                    $groups->include_related('room/teacher', '*');
                    $groups->group_start('NOT');
                        $groups->where_related('room', 'id', null);
                        $groups->or_where_related('room/teacher', 'id', null);
                    $groups->group_end();
                    $groups->group_by_related('room/teacher', 'email');
                    if (!is_null($task_set->common_group_id)) {
                        $groups->where('id', $task_set->common_group_id);
                    }
                    $groups->get_iterated();
                    
                    foreach($groups as $teacher) {
                        if (trim($teacher->room_teacher_email) != '') {
                            $email = trim($teacher->room_teacher_email);
                            if (!in_array($email, $emails)) { $emails[] = $email; }
                        }
                    }
                }
                
                $group = new Group();
                if (!is_null($task_set->common_group_id)) {
                    $group->get_by_id((int)$task_set->common_group_id);
                }
                
                if (count($emails)) {
                    $this->email->from_system();
                    $this->email->reply_to_system();
                    
                    $this->email->build_message_body('file:emails/cli/deadline_notification.tpl', array('task_set' => $task_set, 'group' => $group));
                    
                    if ($this->config->item('email_multirecipient_batch_mode')) {
                        $this->email->to($emails);
                        $this->email->subject('LIST: ' . $this->lang->line('cli_deadline_notification_subject'));
                        $this->email->send();
                    } else {
                        foreach ($emails as $email) {
                            $this->email->to($email);
                            $this->email->subject('TEST');
                            $this->email->send();
                        }
                    }
                    
                    $sent_notifications++;
                    
                    if (!is_null($task_set->task_set_permission_id)) {
                        $task_set_permission = new Task_set_permission();
                        $task_set_permission->get_by_id($task_set->task_set_permission_id);
                        if ($task_set_permission->exists()) {
                            $task_set_permission->deadline_notified = 1;
                            $task_set_permission->save();
                        }
                    } else {
                        $task_set_update = new Task_set();
                        $task_set_update->get_by_id($task_set->id);
                        if ($task_set_update->exists()) {
                            $task_set_update->deadline_notified = 1;
                            $task_set_update->save();
                        }
                    }
                }
            }
        }
        
        echo "Process finished, {$sent_notifications} notifications were sent ...\n";
    }
    
    public function garbage_collector() {
        echo 'Running garbage collector script ...' . "\n";
        
        $this->load->helper('application');
        
        $current_time = time();
        
        // ----------- COMPARATOR WORKING DIRECTORIES --------------------------
        
        $path_to_comparator_files = 'public/comparator/';
        $time_for_comparator_folders_to_remain_untouched = 21600;
        
        echo ' Clearing old Java comparator working directories:' . "\n";
        
        $dirs = scandir($path_to_comparator_files);
        $deleted = 0;
        $total_dirs = 0;
        
        if (is_array($dirs) && count($dirs) > 0) {
            foreach($dirs as $dir) {
                if (is_dir($path_to_comparator_files . $dir) && $dir != '.' && $dir != '..') {
                    $total_dirs++;
                    echo '  ' . $dir;
                    $dir_mod_time = filemtime($path_to_comparator_files . $dir);
                    if ($current_time - $dir_mod_time >= $time_for_comparator_folders_to_remain_untouched) {
                        $deleted++;
                        unlink_recursive($path_to_comparator_files . $dir, TRUE);
                        echo ':  OLD - deleting' . "\n";
                    } else {
                        echo ':  SAFE' . "\n";
                    }
                }
            }
        } 
        if ($total_dirs == 0) {
            echo '  No directories ...' . "\n";
        }
        echo ' Done, ' . $deleted . ' from ' . $total_dirs . ' directories deleted.' . "\n";
        
        // ----------- MOSS WORKING DIRECTORIES --------------------------------
        
        $path_to_moss_files = 'private/moss/';
        $time_for_moss_folders_to_remain_untouched = 21600;
        
        echo ' Clearing old MOSS comparator working directories:' . "\n";
        
        $dirs = scandir($path_to_moss_files);
        $deleted = 0;
        $total_dirs = 0;
        
        if (is_array($dirs) && count($dirs) > 0) {
            foreach($dirs as $dir) {
                if (is_dir($path_to_moss_files . $dir) && $dir != '.' && $dir != '..') {
                    $total_dirs++;
                    echo '  ' . $dir;
                    $dir_mod_time = filemtime($path_to_moss_files . $dir);
                    if ($current_time - $dir_mod_time >= $time_for_moss_folders_to_remain_untouched) {
                        $deleted++;
                        unlink_recursive($path_to_moss_files . $dir, TRUE);
                        echo ':  OLD - deleting' . "\n";
                    } else {
                        echo ':  SAFE' . "\n";
                    }
                }
            }
        } 
        if ($total_dirs == 0) {
            echo '  No directories ...' . "\n";
        }
        echo ' Done, ' . $deleted . ' from ' . $total_dirs . ' directories deleted.' . "\n";
        
        // ----------- EXTRACTED SOLUTIONS DIRECTORIES -------------------------
        
        $path_to_extracted_solutions = 'private/extracted_solutions/';
        $time_for_extracted_solutions_to_remain_untouched = 1800;
        
        echo ' Clearing old extracted solutions working directories:' . "\n";
        
        $dirs = scandir($path_to_extracted_solutions);
        $deleted = 0;
        $total_dirs = 0;
        
        if (is_array($dirs) && count($dirs) > 0) {
            foreach($dirs as $dir) {
                if (is_dir($path_to_extracted_solutions . $dir) && $dir != '.' && $dir != '..') {
                    $total_dirs++;
                    echo '  ' . $dir;
                    $dir_mod_time = filemtime($path_to_extracted_solutions . $dir);
                    if ($current_time - $dir_mod_time >= $time_for_extracted_solutions_to_remain_untouched) {
                        $deleted++;
                        unlink_recursive($path_to_extracted_solutions . $dir, TRUE);
                        echo ':  OLD - deleting' . "\n";
                    } else {
                        echo ':  SAFE' . "\n";
                    }
                }
            }
        } 
        if ($total_dirs == 0) {
            echo '  No directories ...' . "\n";
        }
        echo ' Done, ' . $deleted . ' from ' . $total_dirs . ' directories deleted.' . "\n";
        
        // ----------- TEST TO EXECUTE DIRECTORIES -----------------------------
        
        $path_to_test_to_execute = 'private/test_to_execute/';
        $time_for_test_to_execute_to_remain_untouched = 3600;
        
        echo ' Clearing old test to execute working directories:' . "\n";
        
        $dirs = scandir($path_to_test_to_execute);
        $deleted = 0;
        $total_dirs = 0;
        
        if (is_array($dirs) && count($dirs) > 0) {
            foreach($dirs as $dir) {
                if (is_dir($path_to_test_to_execute . $dir) && $dir != '.' && $dir != '..') {
                    $total_dirs++;
                    echo '  ' . $dir;
                    $dir_mod_time = filemtime($path_to_test_to_execute . $dir);
                    if ($current_time - $dir_mod_time >= $time_for_test_to_execute_to_remain_untouched) {
                        $deleted++;
                        unlink_recursive($path_to_test_to_execute . $dir, TRUE);
                        echo ':  OLD - deleting' . "\n";
                    } else {
                        echo ':  SAFE' . "\n";
                    }
                }
            }
        } 
        if ($total_dirs == 0) {
            echo '  No directories ...' . "\n";
        }
        echo ' Done, ' . $deleted . ' from ' . $total_dirs . ' directories deleted.' . "\n";
        
        // ----------- TEST TO EXECUTE DIRECTORIES -----------------------------
        
        $total_number = 0;
        echo ' Clearing unfinished uploads of task files:' . "\n";
        $deleted = $this->find_and_delete_old_upload_part('private/uploads/task_files/', '', 172800, $current_time, $total_number);
        if ($total_number == 0) {
            echo '  No files ...' . "\n";
        }
        echo ' Done, ' . $deleted . ' from ' . $total_number . ' files deleted.' . "\n";
        
        echo 'Done ...' . "\n";
    }
    
    private function find_and_delete_old_upload_part($path_base, $path_add, $max_time, $current_time, &$count_of_parts) {
        $deleted = 0;
        
        $files = scandir($path_base . $path_add);
        if (is_array($files) && count($files) > 0) {
            foreach($files as $file) {
                if (is_dir($path_base . $path_add . $file) && $file != '.' && $file != '..') {
                    $deleted = $deleted + $this->find_and_delete_old_upload_part($path_base, $path_add . $file . '/', $max_time, $current_time, $count_of_parts);
                } else if (is_file($path_base . $path_add . $file)) {
                    $ext_pos = strrpos($file, '.');
                    if ($ext_pos !== FALSE) {
                        $ext = substr($file, $ext_pos + 1);
                        if ($ext == 'upload_part') {
                            $count_of_parts++;
                            echo '  ' . $path_add . $file;
                            $filemtime = filemtime($path_base . $path_add . $file);
                            if ($current_time - $filemtime >= $max_time) {
                                echo ':  OLD - deleting' . "\n";
                                $deleted++;
                                @unlink($path_base . $path_add . $file);
                            } else {
                                echo ':  SAFE' . "\n";
                            }
                        }
                    }
                }
            }
        }
        
        
        return $deleted;
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
    
    /**
     * Create new cache files for DataMapper if production cache is enabled.
     */
    private function _recreate_production_cache() {
        $result = $this->db->get('migrations', 1);
        if ($result->num_rows() == 1) {
            if ($result->row()->version != $this->_get_last_migration_version()) {
                echo '  Current migration version isn\'t the latest one.' . "\n" . '  Cache will be rebuild on first hit.' . "\n";
                return;
            }
        } else {
            echo '  Migrations database table doe\'s not exists. Cache can\'t be recreated.' . "\n";
            return;
        }
        
        $this->config->load('datamapper', TRUE);
        $production_cache = $this->config->item('production_cache', 'datamapper');
        if (!empty($production_cache) && file_exists($production_cache) && is_dir($production_cache)) {
            include APPPATH . '../system/core/Model.php';
            $path = APPPATH . 'models/';
            if (file_exists($path)) {
                $files = scandir($path);
                foreach ($files as $file) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    $class_name = basename($file, '.' . $ext);
                    $class_name = strtoupper(substr($class_name, 0, 1)) . strtolower(substr($class_name, 1));
                    if (strtolower($ext) == 'php') {
                        include $path . $file;
                        if (class_exists($class_name) && in_array('DataMapper', class_parents($class_name))) {
                            echo '  DataMapper model ' . $class_name . ' cached again ...' . "\n";
                            $model = new $class_name();
                            $model->limit(1)->get_iterated();
                        }
                    }
                }
            }
        }
    }
    
    private function _get_last_migration_version() {
        $last = -1;
        $dir = scandir(APPPATH . 'migrations');
        foreach ($dir as $file) {
            if (is_file(APPPATH . 'migrations/' . $file)) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (strtolower($ext) == 'php') {
                    $filename = basename($file, '.' . $ext);
                    $matches = array();
                    if (preg_match('/^(?P<version>[0-9]+)\_/', $filename, $matches)) {
                        $version = (int)$matches['version'];
                        $last = max(array($last, $version));
                    }
                }
            }
        }
        return $last;
    }
}
