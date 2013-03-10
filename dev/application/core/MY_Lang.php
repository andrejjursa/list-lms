<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Lang extends CI_Lang {
    
    protected $lang_idiom = '';
    
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
    
    protected function load_default_lang_idiom() {
        $deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
        $this->lang_idiom = ($deft_lang == '') ? 'english' : $deft_lang;
    }
    
}