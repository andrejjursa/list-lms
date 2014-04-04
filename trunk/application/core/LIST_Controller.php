<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Overriden controller class with few more useful methods.
 * @package LIST_Core
 * @author Andrej Jursa
 */ 
class LIST_Controller extends CI_Controller {
    
    const TRANSACTION_ISOLATION_REPEATABLE_READ = 'REPEATABLE READ';
    const TRANSACTION_ISOLATION_READ_COMMITTED = 'READ COMMITTED';
    const TRANSACTION_ISOLATION_READ_UNCOMMITTED = 'READ UNCOMMITTED';
    const TRANSACTION_ISOLATION_SERIALIZABLE = 'SERIALIZABLE';
    
    const TRANSACTION_AREA_GLOBAL = 'GLOBAL';
    const TRANSACTION_AREA_SESSION = 'SESSION';
    
    /**
     * Main constructor, initialise controller.
     * Database will be connected, libraries for usermanager and messages will be loaded and translations model will be loaded.
     * All user data will be send to smarty template.
     */
    public function __construct() {
        parent::__construct();
        $this->load->config('list');
        $this->load->config('lockdown');
        if ($this->config->item('system_lockdown') === TRUE) {
            if (!$this->input->is_ajax_request()) {
                redirect(create_internal_url('maintenance', TRUE));
            }
            die();
        }
        if ($this->input->is_cli_request()) {
            echo 'Error: You can\'t call this controller from CLI!';
            die();
        }
        $this->load->database();
        $this->load->library('usermanager');
        $this->load->library('messages');
        $this->load->model('translations');
        $this->usermanager->set_student_data_to_smarty();
        $this->usermanager->set_teacher_data_to_smarty();
    }
    
    /**
     * Perform initialisation of language for specific language idiom.
     * Language idiom must exist in system/application languages.
     * @param string $lang_idiom language idiom.
     */
    protected function _init_specific_language($lang_idiom) {
        $languages = $this->lang->get_list_of_languages();
        if (array_key_exists($lang_idiom, $languages) && $this->lang->get_current_idiom() != $lang_idiom) {
            $this->lang->reinitialize_for_idiom($lang_idiom);
            $translations = $this->translations->get_translations_for_idiom($lang_idiom);
            $this->lang->add_custom_translations($translations);
            $this->_init_lang_js_messages();
        }
    }

        /**
     * Perform initialisation of student language settings.
     * @param Student|integer $student, for which language have to be set, accept Student model or integer with student id, default is NULL (use currently loged in student).
     */
    protected function _init_language_for_student($student = NULL) {
        if (is_null($student)) {
            $this->lang->reinitialize_for_idiom($this->usermanager->get_student_language());
            $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
            $this->lang->add_custom_translations($translations);
            $this->_init_lang_js_messages();
        } else {
            if (!(is_object($student) && $student instanceof Student) && !(is_numeric($student) && intval($student) > 0)) {
                $this->_init_language_for_student();
            } else {
                if (!is_object($student)) {
                    $student = new Student($student);
                }
                if ($student->exists()) {
                    if ($this->lang->get_current_idiom() != $student->language) {
                        $this->lang->reinitialize_for_idiom($student->language);
                        $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
                        $this->lang->add_custom_translations($translations);
                        $this->_init_lang_js_messages();
                    }
                } else {
                    $this->_init_language_for_student();
                }
            }
        }
    }
    
    /**
     * Load student type language file.
     * @param string $filename name of file to be loaded or NULL to load file with name of derived controller.
     */
    protected function _load_student_langfile($filename = NULL) {
        if (is_null($filename)) {
            $this->lang->load(strtolower(get_class($this)));
        } else {
            $this->lang->load($filename);
        }
    }
    
    /**
     * Perform initialisation of teacher language settings.
     * @param Teacher|integer $teacher, for which language have to be set, accept Teacher model or integer with teacher id, default is NULL (use currently loged in teacher).
     */
    protected function _init_language_for_teacher($teacher = NULL) {
        if (is_null($teacher)) {
            $this->lang->reinitialize_for_idiom($this->usermanager->get_teacher_language());
            $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
            $this->lang->add_custom_translations($translations);
            $this->_init_lang_js_messages();
            $this->_init_teacher_quick_langmenu();
        } else {
            if (!(is_object($teacher) && $teacher instanceof Teacher) && !(is_numeric($teacher) && intval($teacher) > 0)) {
                $this->_init_language_for_teacher();
            } else {
                if (!is_object($teacher)) {
                    $teacher = new Teacher($teacher);
                }
                if ($teacher->exists()) {
                    if ($this->lang->get_current_idiom() != $teacher->language) {
                        $this->lang->reinitialize_for_idiom($teacher->language);
                        $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
                        $this->lang->add_custom_translations($translations);
                        $this->_init_lang_js_messages();
                        $this->_init_teacher_quick_langmenu();
                    }
                } else {
                    $this->_init_language_for_teacher();
                }
            }
        }
    }
    
    /**
     * Load teacher type language file.
     * @param string $filename name of file to be loaded or NULL to load file with name of derived controller.
     */
    protected function _load_teacher_langfile($filename = NULL) {
        if (is_null($filename)) {
            $this->lang->load('admin/' . strtolower(get_class($this)));
        } else {
            $this->lang->load('admin/' . $filename);
        }
    }
    
    /**
     * Loads and inject teacher menu configuration to template.
     * Smarty template variable $list_adminmenu will be created.
     */
    protected function _initialize_teacher_menu() {
        $this->config->load('adminmenu');
        $this->parser->assign('list_adminmenu', $this->config->item('adminmenu'));
        $this->_load_teacher_langfile('adminmenu');
    }
    
    /**
     * Loads and inject open task set to template.
     * Smarty template variable $list_open_task_set will be created.
     */
    protected function _initialize_open_task_set() {
        $task_set = new Task_set();
        $task_set->get_as_open();
        $this->parser->assign('list_open_task_set', $task_set);
    }
    
    /**
     * Set the active menu item in teacher menu.
     * Smarty template variable $list_adminmenu_current will be created.
     * @param string $tag page tag to be set as active item in menu.
     */
    protected function _select_teacher_menu_pagetag($tag = '') {
        $this->parser->assign('list_adminmenu_current', $tag);
    }
    
    /**
     * Set the database transaction isolation level.
     * @param string $level transaction isolation level, one of TRANSACTION_ISOLATION_* of MY_Controller class.
     * @param string $area area of where isolation is aplied, one of TRANSACTION_AREA_* of MY_Controller class.
     */
    protected function _transaction_isolation($level = self::TRANSACTION_ISOLATION_SERIALIZABLE, $area = self::TRANSACTION_AREA_SESSION) {
        $this->db->query('SET ' . $area . ' TRANSACTION ISOLATION LEVEL ' . $level . ';');
    }
    
    /**
     * Add language messages.js file to page headers.
     */
    private function _init_lang_js_messages() {
        $path = 'public/js/language/' . $this->lang->get_current_idiom() . '/messages.js';
        $this->parser->assign('list_lang_js_messages', $path);
    }
    
    /**
     * This method adds tinymce editor to template.
     */
    protected function _add_tinymce() {
        $this->parser->add_js_file('tinymce/jquery.tinymce.js');
        $this->parser->add_js_file('tinymce_fix.js');
        $this->parser->add_css_file('tinymce/common.css');
    }
    
    protected function _add_tinymce4() {
        $this->parser->add_js_file('tinymce4/jquery.tinymce.min.js');
        $this->parser->add_js_file('tinymce4/tinymce.min.js');
    }

    protected function _add_dataTables() {
        $this->parser->add_js_file('jquery.dataTables.min.js');
        $this->parser->add_css_file('jquery.dataTables.css');
    }

    /**
     * This method adds plupload to template and load plupload library.
     */
    protected function _add_plupload() {
        $this->load->library('plupload');
        $this->parser->add_js_file('plupload.js');
        $this->parser->add_js_file('plupload.html5.js');
        $this->parser->add_js_file('plupload.flash.js');
        $this->parser->add_js_file('plupload.silverlight.js');
        $this->parser->add_js_file('jquery.ui.plupload.js');
        if (strlen($this->lang->line('plupload_i18n_langfile')) > 0) {
            $this->parser->add_js_file('i18n/' . $this->lang->line('plupload_i18n_langfile'));
        }
        $this->parser->add_css_file('jquery.ui.plupload.css');
    }
    
    /**
     * This method add jquery scrollTo plugin to template.
     */
    protected function _add_scrollTo() {
        $this->parser->add_js_file('jquery.scrollTo-1.4.3.1-min.js');
    }
    
    /**
     * This method add google prettify syntax highlighter to template.
     */
    protected function _add_prettify() {
        $this->parser->add_js_file('prettify/prettify.js');
        $this->parser->add_css_file('prettify.css');
    }
    
    /**
     * This method add Jcrop to template.
     */
    protected function _add_jCrop() {
        $this->parser->add_js_file('jquery.Jcrop.js');
        $this->parser->add_css_file('jquery.Jcrop.css');
    }
    
    /**
     * This method add jQuery countdown plugin to template.
     */
    protected function _add_jquery_countdown() {
        $this->parser->add_js_file('jquery.plugin.min.js');
        $this->parser->add_js_file('jquery.countdown.js');
        $localisation_file = $this->lang->line('common_jquery_countdown_localisation_file');
        if (!is_null($localisation_file) && trim($localisation_file) !== '') {
            $this->parser->add_js_file(trim($localisation_file));
        }
    }

    /**
     * Injects all possible languages to smarty parser.
     */
    private function _init_teacher_quick_langmenu() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('list_quicklang_menu', $languages);
    }
    
    protected function _init_teacher_quick_prefered_course_menu() {
        $menu = array();
        
        $courses = new Course();
        $courses->include_related('period', 'name');
        $courses->order_by_related('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name', 'asc');
        $courses->get_iterated();
        
        foreach($courses as $course) {
            $menu[$course->period_name][$course->id] = $course->name;
        }
        
        $teacher = new Teacher();
        $teacher->get_by_id($this->usermanager->get_teacher_id());
        $current_course_name = $this->lang->line('admin_teachers_no_prefered_course');
        $current_course_id = NULL;
        if ($teacher->exists()) {
            $prefered_course = $teacher->prefered_course->get();
            if ($prefered_course->exists()) {
                $current_course_name = $this->lang->text($prefered_course->name);
                $current_course_id = $prefered_course->id;
            }
        }
        $this->parser->assign('list_teacher_prefered_course_name', $current_course_name);
        $this->parser->assign('list_teacher_prefered_course_id', $current_course_id);
        $this->parser->assign('list_teacher_prefered_course_menu', $menu);
    }
    
    /**
     * Loads and injects page navigation for student's frontend.
     * Also loads all courses, in which current student is participating.
     * Smarty template variable $list_pagemenu will be created.
     */
    protected function _initialize_student_menu() {
        $this->config->load('pagemenu');
        $this->parser->assign('list_pagemenu', $this->config->item('pagemenu'));
        $this->_load_student_langfile('pagemenu');
        if ($this->usermanager->is_student_session_valid()) {
            $courses = new Course();
            $courses->where_related('participant', 'student_id', $this->usermanager->get_student_id());
            $courses->where_related('participant', 'allowed', 1);
            $courses->include_related('period', 'name');
            $courses->order_by_related_with_constant('period', 'sorting', 'asc');
            $courses->order_by_with_constant('name', 'asc');
            $courses->get_iterated();
            $this->parser->assign('list_student_courses', $courses);
        }
    }
    
    /**
     * Set the active menu item in student menu.
     * Smarty template variable list_pagemenu_current will be created.
     * @param string $tag page tag to be set as active item in menu.
     */
    protected function _select_student_menu_pagetag($tag = '') {
        $this->parser->assign('list_pagemenu_current', $tag);
    }
    
    /**
     * Sends message to all students or teachers. Do not use get_iterated() to execute select query!
     * @param Student|Teacher $recipients list of students or teachers.
     * @param string $subject email subject (accepts lang: prefix).
     * @param string $template template body or file:path/to/template.tpl.
     * @param string $template_variables array of template variables.
     * @param string $from email addres of sender or NULL to use system address.
     * @param string $from_name name of sender.
     * @param boolean $sender_copy enable sending of copy to sender email address.
     * @param string $sender_email sender email address.
     * @return boolean TRUE, if all emails are sent, or FALSE if all or some emails failed to be send.
     */
    protected function _send_multiple_emails($recipients, $subject, $template, $template_variables = array(), $from = NULL, $from_name = '', $sender_copy = FALSE, $sender_email = '') {
        if ($recipients instanceof Teacher || $recipients instanceof Student) {
            $email_by_languages = array();
            if ($recipients->exists()) { foreach ($recipients->all as $recipient) {
                $email_by_languages[$recipient->language][] = $recipient;
            }}
            if (count($email_by_languages) == 0) { return FALSE; }
            $this->load->library('email');
            if (is_null($from)) {
                $this->email->from_system();
                $this->email->reply_to_system();
            } else {
                $this->email->from($from, $from_name);
                $this->email->reply_to($from, $from_name);
            }
            $result = TRUE;
            $lang_clone = clone $this->lang;
            set_time_limit(0);
            foreach($email_by_languages as $language => $subrecipients) {
                if (count($subrecipients) == 0) { continue; }
                $this->_init_specific_language($language);
                $this->email->build_message_body($template, $template_variables);
                $email_subject = 'LIST' . ($subject ? ' - ' . $this->lang->text($subject) : '');
                if ($this->config->item('email_multirecipient_batch_mode')) {
                    $to_list = array();
                    foreach ($subrecipients as $recipient) {
                        $to_list[] = $recipient->email;
                    }
                    if ($sender_copy === TRUE) {
                        $to_list[] = $sender_email;
                    }
                    $this->email->to($to_list);
                    $this->email->subject($email_subject);
                    $partial_result = $this->email->send();
                    $result = $result && $partial_result;
                } else {
                    foreach($subrecipients as $recipient) {
                        $this->email->to($recipient->email);
                        $this->email->subject($email_subject);
                        $partial_result = $this->email->send();
                        $result = $result && $partial_result;
                    }
                    if ($sender_copy === TRUE) {
                        $this->email->to($sender_email);
                        $this->email->subject($email_subject);
                        $partial_result = $this->email->send();
                        $result = $result && $partial_result;
                    }
                }
            }
            $this->lang = $lang_clone;
            set_time_limit((int)ini_get('max_execution_time'));
            return $result;
        } else {
            return FALSE;
        }
    }
    
    protected function _is_cache_enabled() {
        return $this->config->item('enable_hooks');
    }
    
    protected function _action_success() {
        $this->output->set_internal_value(LIST_Output::IV_ACTION_RESULT, TRUE);
    }
}