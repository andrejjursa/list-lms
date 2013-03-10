<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Translationseditor extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $languages = $this->lang->get_list_of_languages();
        $translations = $this->translations->get_all_for_editing();
        $this->parser->add_js_file('translations_editor_api.js');
        $this->parser->parse('backend/translationseditor/index.tpl', array('languages' => $languages, 'translations' => $translations));
    }
    
    public function ajax_save() {
        $this->output->set_content_type('application/json');
        $translation = $this->input->post('translation');
        if (count($translation) == 1) {
            $output = FALSE;
            $constant = key($translation);
            if (count($translation[$constant]) > 0) { foreach ($translation[$constant] as $idiom => $text) {
                $output = $output || $this->translations->save_translation($constant, $idiom, $text);
            }}
            $this->output->set_output(json_encode($output));
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
}