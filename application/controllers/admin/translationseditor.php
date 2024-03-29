<?php

/**
 * Translations editor controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Translationseditor extends LIST_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index(): void
    {
        $this->_select_teacher_menu_pagetag('translations_editor');
        $languages = $this->lang->get_list_of_languages();
        $translations = $this->translations->get_all_for_editing();
        $this->parser->add_js_file('admin_translationseditor/list.js');
        $this->parser->add_css_file('admin_translationseditor.css');
        $this->parser->parse(
            'backend/translationseditor/index.tpl',
            [
                'languages'    => $languages,
                'translations' => $translations,
            ]
        );
    }
    
    public function reload_table(): void
    {
        $languages = $this->lang->get_list_of_languages();
        $translations = $this->translations->get_all_for_editing();
        $this->parser->parse(
            'backend/translationseditor/table_body.tpl',
            [
                'languages'    => $languages,
                'translations' => $translations,
            ]
        );
    }
    
    public function ajax_save(): void
    {
        $this->output->set_content_type('application/json');
        $translation = $this->input->post('translation');
        if (count($translation) === 1) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $constant = key($translation);
            if (count($translation[$constant]) > 0) {
                foreach ($translation[$constant] as $idiom => $text) {
                    $this->translations->save_translation($constant, $idiom, $text);
                }
            }
            $output = false;
            if ($this->db->trans_status()) {
                $output = true;
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
            $new_row = $this->translations->get_constant_for_editing($constant);
            $languages = $this->lang->get_list_of_languages();
            $_POST = [];
            $row = $this->parser->parse(
                'backend/translationseditor/table_row.tpl',
                [
                    'languages'   => $languages,
                    'translation' => $new_row[$constant],
                    'constant'    => $constant,
                ],
                true
            );
            $this->output->set_output(json_encode(['result' => $output, 'row' => $row]));
        } else {
            $this->output->set_output(json_encode(['result' => false]));
        }
    }
    
    public function ajax_delete($constant): void
    {
        $this->output->set_content_type('application/json');
        $this->_transaction_isolation();
        $this->db->trans_begin();
        $this->translations->delete_translations($constant);
        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $this->output->set_output(json_encode(true));
        } else {
            $this->db->trans_rollback();
            $this->output->set_output(json_encode(false));
        }
    }
    
    public function new_constant(): void
    {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->add_js_file('admin_translationseditor/form.js');
        $this->parser->parse('backend/translationseditor/new_constant.tpl', ['languages' => $languages]);
    }
    
    public function save_new_constant()
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'translation[constant]',
            'lang:admin_translationseditor_new_constant_field_constant',
            'required|callback__valid_constant|callback__free_constant'
        );
        $this->form_validation->set_message(
            '_valid_constant',
            $this->lang->line('admin_translationseditor_new_constant_form_error_valid_constant')
        );
        $this->form_validation->set_message(
            '_free_constant',
            $this->lang->line('admin_translationseditor_new_constant_form_error_free_constant')
        );
        
        if ($this->form_validation->run()) {
            $translation = $this->input->post('translation');
            $constant = $translation['constant'];
            $this->_transaction_isolation();
            $this->db->trans_begin();
            if (count($translation['text']) > 0) {
                foreach ($translation['text'] as $idiom => $text) {
                    $this->translations->save_translation($constant, $idiom, $text);
                }
            }
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message(
                    $this->lang->line('admin_translationseditor_new_constant_message_added'),
                    Messages::MESSAGE_TYPE_SUCCESS
                );
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    $this->lang->line('admin_translationseditor_new_constant_message_save_failed'),
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_translationseditor/new_constant'));
        } else {
            $this->new_constant();
        }
    }
    
    public function _valid_constant($str): bool
    {
        return (bool)preg_match(Translations::CONSTANT_VALIDATION_REGEXP, $str);
    }
    
    public function _free_constant($str): bool
    {
        return $this->translations->is_constant_free($str);
    }
    
    public function translations_json(): void
    {
        $this->output->set_content_type('application/json');
        $data = $this->translations->get_all_for_idiom($this->lang->get_current_idiom());
        $this->output->set_output(json_encode($data));
    }
    
}