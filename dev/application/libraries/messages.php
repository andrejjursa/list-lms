<?php

class Messages {
    
    /**
     * var object $CI CodeIgniter.
     */
    protected $CI = null;
    
    const MESSAGE_TYPE_DEFAULT = 'default';
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR = 'error';
    
    const FLASH_MESSAGES_NAME = 'flash_messages';
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->flash_messages_to_smarty();        
    }
    
    public function add_message($message, $type = self::MESSAGE_TYPE_DEFAULT) {
        $messages = $this->read_messages();
        array_push($messages, array('message' => $message, 'type' => $type));
        $this->CI->session->set_flashdata(self::FLASH_MESSAGES_NAME, $messages);
    }
    
    public function keep_messages() {
        $this->CI->session->keep_flashdata(self::FLASH_MESSAGES_NAME);
    }
    
    public function read_messages() {
        $messages = $this->CI->session->flashdata(self::FLASH_MESSAGES_NAME);
        $messages = is_array($messages) ? $messages : array();
        return $messages;
    }
    
    public function flash_messages_to_smarty() {
        $this->CI->load->library('parser');
        $this->CI->parser->assign('list_flash_messages', $this->read_messages());
    }
}