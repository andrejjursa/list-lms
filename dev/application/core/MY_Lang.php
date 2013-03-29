<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Lang extends CI_Lang {
    
    protected $lang_idiom = '';
    protected $lang_overlays = array();
    
    public function __construct() {
        parent::__construct();
        $this->load_default_lang_idiom();
    }
    
    public function get_current_idiom() {
        return $this->lang_idiom;
    }
    
    public function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
        if ($idiom == '') {
            $idiom = $this->lang_idiom;
        }  
        return parent::load($langfile, $idiom, $return, $add_suffix, $alt_path); 
    }
    
    public function reinitialize_for_idiom($idiom) {
        if (is_string($idiom) && $this->lang_idiom != $idiom) {
            $this->lang_idiom = $idiom;
            $old_loaded = $this->is_loaded;
            $this->language = array();
            $this->is_loaded = array();
            if (count($old_loaded) > 0) { foreach($old_loaded as $load_file) {
                if (preg_match('/^(?P<langfile>.+)_lang\.php$/i', $load_file, $matches)) {
                    $this->load($matches['langfile']);
                }
            }}
            return TRUE;
        }
        return FALSE;
    }
    
    public function get_list_of_languages() {
        $languages = scandir(APPPATH . 'language');
        $langs = array();
        if (count($languages) > 0) { foreach($languages as $language) {
            if (file_exists(APPPATH . 'language/' . $language . '/config.php')) {
                include(APPPATH . 'language/' . $language . '/config.php');
                if (isset($lang_config['idiom']) && isset($lang_config['title'])) {
                    $langs[$lang_config['idiom']] = $lang_config['title'];
                }
            }
        }}
        return $langs;
    }
    
    public function add_custom_translations($translations) {
        $this->language = array_merge($translations, $this->language);
    }
    
    public function text($text, $default = '') {
        $output = '';
        if (strtolower(substr($text, 0, 5)) == 'lang:') {
            $line = substr($text, 5);
            $output = $this->line($line);
        } else {
            $output = $text;
        }
        if (!$output) {
            $output = $default;
        }
        return $output;
    }
    
    public function load_all_overlays($table, $table_id = NULL) {
        $this->load_overlays($table, $table_id);
    }
    
    public function get_overlay($table, $table_id, $column, $idiom = NULL) {
        return $this->replace_null_overlay_text($this->get_overlay_if_exists($table, $table_id, $column, is_null($idiom) ? $this->lang_idiom : $idiom));
    }
    
    public function save_overlay($table, $table_id, $column, $idiom, $text) {
        $CI =& get_instance();
        
        $update = !is_null($this->get_overlay_if_exists($table, $table_id, $column, $idiom));
        
        if ($update) {
            $CI->db->set('table', $table);
            $CI->db->set('table_id', intval($table_id));
            $CI->db->set('column', $column);
            $CI->db->set('idiom', $idiom);
            $CI->db->set('text', $text);
            $CI->db->where('table', $table);
            $CI->db->where('table_id', intval($table_id));
            $CI->db->where('column', $column);
            $CI->db->where('idiom', $idiom);
            $CI->db->update('lang_overlays');
        } else {
            $CI->db->set('table', $table);
            $CI->db->set('table_id', intval($table_id));
            $CI->db->set('column', $column);
            $CI->db->set('idiom', $idiom);
            $CI->db->set('text', $text);
            $CI->db->insert('lang_overlays');
        }
        
        if ($update || $CI->db->affected_rows() > 0) {
            $this->lang_overlays[$idiom][$table][intval($table_id)][$column] = $text;
            return TRUE;
        }
        return FALSE;
    }
    
    public function save_overlay_array($array) {
        $all_ok = TRUE;
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $idiom => $tables) {
                if (is_array($tables) && count($tables) > 0) {
                    foreach ($tables as $table => $table_content) {
                        if (is_array($table_content) && count($table_content) > 0) {
                            foreach ($table_content as $table_id => $columns) {
                                if (is_array($columns) && count($columns) > 0) {
                                    foreach ($columns as $column => $text) {
                                        if (is_string($text)) {
                                            $output = $this->save_overlay($table, $table_id, $column, $idiom, $text);
                                            $all_ok = $output && $all_ok;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $all_ok;
    }
    
    public function init_overlays($table, $rows, $fields) {
        $this->load_overlays($table);
        if (count($rows) > 0 && count($fields) > 0) {
            foreach ($rows as $row) {
                foreach ($fields as $field) {
                    if (!$this->overlay_exists($table, $row['id'], $field, $this->lang_idiom)) {
                        $this->no_more_load_overlay($table, $row['id'], $field, $this->lang_idiom);
                    }
                }
            }
        }
    }
    
    protected function replace_null_overlay_text($text) {
        if (is_null($text)) { return $text; }
        return $text;
    }
    
    protected function get_overlay_if_exists($table, $table_id, $column, $idiom) {
        if ($this->overlay_exists($table, $table_id, $column, $idiom)) {
            return $this->lang_overlays[$idiom][$table][intval($table_id)][$column];
        } else {
            $this->load_overlays($table, $table_id, $column);
            if (!$this->overlay_exists($table, $table_id, $column, $idiom)) {
                $this->no_more_load_overlay($table, $table_id, $column, $idiom);
            }
            return $this->lang_overlays[$idiom][$table][intval($table_id)][$column];
        }
    }
    
    protected function overlay_exists($table, $table_id, $column, $idiom) {
        if (isset($this->lang_overlays[$idiom][$table][intval($table_id)])) {
            $columns = $this->lang_overlays[$idiom][$table][intval($table_id)];
            return array_key_exists($column, $columns);
        }
        return FALSE; 
    }
    
    protected function no_more_load_overlay($table, $table_id, $column, $idiom) {
        $this->lang_overlays[$idiom][$table][intval($table_id)][$column] = NULL;
    }
    
    protected function load_overlays($table, $table_id = NULL, $column = NULL) {
        $CI =& get_instance();
        $CI->db->from('lang_overlays')->where('table', $table);
        if (!is_null($table_id)) {
            $CI->db->where('table_id', intval($table_id));
        }
        if (!is_null($column)) {
            $CI->db->where('column', intval($column));
        }
        $query = $CI->db->get();
        if ($query->num_rows()) { foreach ($query->result() as $row) {
            $this->lang_overlays[$row->idiom][$row->table][intval($row->table_id)][$row->column] = $row->text;
        }}
        $query->free_result();
    }
    
    protected function load_default_lang_idiom() {
        $deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
        $this->lang_idiom = ($deft_lang == '') ? 'english' : $deft_lang;
    }
    
}