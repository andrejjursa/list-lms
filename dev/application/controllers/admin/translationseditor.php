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
        $this->parser->add_css_file('admin_translationseditor.css');
        $this->parser->parse('backend/translationseditor/index.tpl', array('languages' => $languages, 'translations' => $translations));
    }
    
    public function reload_table() {
        $languages = $this->lang->get_list_of_languages();
        $translations = $this->translations->get_all_for_editing();
        $this->parser->parse('backend/translationseditor/table_body.tpl', array('languages' => $languages, 'translations' => $translations));
    }
    
    public function ajax_save() {
        $this->output->set_content_type('application/json');
        $translation = $this->input->post('translation');
        if (count($translation) == 1) {
            $output = FALSE;
            $constant = key($translation);
            if (count($translation[$constant]) > 0) { foreach ($translation[$constant] as $idiom => $text) {
                $result = $this->translations->save_translation($constant, $idiom, $text);
                $output = $output || $result;
            }}
            $new_row = $this->translations->get_constant_for_editing($constant);
            $languages = $this->lang->get_list_of_languages();
            $_POST = array();
            $row = $this->parser->parse('backend/translationseditor/table_row.tpl', array('languages' => $languages, 'translation' => $new_row[$constant], 'constant' => $constant), TRUE);
            $this->output->set_output(json_encode(array('result' => $output, 'row' => $row)));
        } else {
            $this->output->set_output(json_encode(array('result' => FALSE)));
        }
    }
    
    public function ajax_delete($constant) {
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($this->translations->delete_translations($constant)));
    }
    
    public function new_constant() {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->parse('backend/translationseditor/new_constant.tpl', array('languages' => $languages));
    }
    
    public function save_new_constant() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('translation[constant]', 'lang:admin_translationseditor_new_constant_field_constant', 'required|callback__valid_constant|callback__free_constant');
        $this->form_validation->set_message('_valid_constant', $this->lang->line('admin_translationseditor_new_constant_form_error_valid_constant'));
        $this->form_validation->set_message('_free_constant', $this->lang->line('admin_translationseditor_new_constant_form_error_free_constant'));
        
        if ($this->form_validation->run()) {
            $translation = $this->input->post('translation');
            $constant = $translation['constant'];
            if (count($translation['text']) > 0) { foreach ($translation['text'] as $idiom => $text) {
                $this->translations->save_translation($constant, $idiom, $text);
            }}
        } else {
            $this->new_constant();
        }
    }
    
    public function _valid_constant($str) {
        return (bool)preg_match(Translations::CONSTANT_VALIDATION_REGEXP, $str);
    }
    
    public function _free_constant($str) {
        return $this->translations->is_constant_free($str);
    }
    
}