<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Help controller for both frontend and backend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Help extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        if ($this->usermanager->is_teacher_session_valid()) {
            $this->_init_language_for_teacher();
        } elseif ($this->usermanager->is_student_session_valid()) {
            $this->_init_language_for_student();
        } else if ($this->router->method != 'login_error') {
            redirect(create_internal_url('help/login_error'));
        }
        $this->_load_student_langfile();
    }
    
    public function show($controller, $topic, $idiom = NULL) {
        $file_path = $controller . '/' . $topic . '.html';
        $path = APPPATH . 'manual/' . (is_null($idiom) ? $this->lang->get_current_idiom() : $idiom) . '/' . $file_path;
        if (file_exists($path)) {
            $help_content = file_get_contents($path);
            $this->parser->parse('frontend/help/show.tpl', array('help_content' => $help_content));
        } else {
            $options = array();
            $dirs = scandir(APPPATH . 'manual');
            foreach ($dirs as $dir) { if ($dir !== '.' && $dir !== '..' && is_dir(APPPATH . 'manual/' . $dir)) {
                if (file_exists(APPPATH . 'manual/' . $dir . '/' . $file_path)) {
                    $options[$dir] = create_internal_url('help/show/' . $controller . '/' . $topic . '/' . $dir);
                }
            }}
            $this->parser->parse('frontend/help/show_error.tpl', array('options' => $options));
        }
    }
    
    public function login_error() {
        $this->parser->parse('frontend/help/login_error.tpl');
    }
}