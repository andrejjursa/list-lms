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
        $this->load->database();
        $this->load->library('usermanager');
        $this->load->library('messages');
        $this->load->model('translations');
        $this->usermanager->set_student_data_to_smarty();
        $this->usermanager->set_teacher_data_to_smarty();
    }
    
    /**
     * Perform initialisation of student language settings.
     */
    protected function _init_language_for_student() {
        $this->lang->reinitialize_for_idiom($this->usermanager->get_student_language());
        $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
        $this->lang->add_custom_translations($translations);
        $this->_init_lang_js_messages();
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
     */
    protected function _init_language_for_teacher() {
        $this->lang->reinitialize_for_idiom($this->usermanager->get_teacher_language());
        $translations = $this->translations->get_translations_for_idiom($this->lang->get_current_idiom());
        $this->lang->add_custom_translations($translations);
        $this->_init_lang_js_messages();
        $this->_init_teacher_quick_langmenu();
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
        $this->parser->add_css_file('tinymce/common.css');
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
     * Injects all possible languages to smarty parser.
     */
    private function _init_teacher_quick_langmenu() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('list_quicklang_menu', $languages);
    }
    
    /**
     * TODO
     */
    protected function _initialize_student_menu() {
        
    }
    
    /**
     * TODO
     * @param string $tag
     */
    protected function _select_student_menu_pagetag($tag = '') {
        
    }
}