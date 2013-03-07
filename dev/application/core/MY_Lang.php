<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Lang extends CI_Lang {
    
    protected $lang_idiom = '';
    
    public function __construct() {
        parent::__construct();
        $this->load_default_lang_idiom();
    }
    
    public function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
        if ($idiom == '') {
            $idiom = $this->lang_idiom;
        }  
        return parent::load($langfile, $idiom, $return, $add_suffix, $alt_path); 
    }
    
    public function reinitialize_for_idiom($idiom) {
        $this->lang_idiom = $idiom;
        $this->language = array();
        $this->is_loaded = array();
    }
    
    protected function load_default_lang_idiom() {
        $deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
        $this->lang_idiom = ($deft_lang == '') ? 'english' : $deft_lang;
    }
    
}