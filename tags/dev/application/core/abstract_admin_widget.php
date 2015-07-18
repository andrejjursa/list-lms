<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Abstract admin widget class.
 * @package LIST_Tests
 * @author Andrej Jursa
 */ 
abstract class abstract_admin_widget {
    
    protected $config;
    private $id;
    
    public function __construct($id, $config = array()) {
        $this->config = $config;
        $this->id = $id;
        $this->lang->load('widgets/admin/' . strtolower(get_class($this)));
        $this->defaultParserVars();
    }
    
    public function getWidgetId() { return $this->id; }
    
    public function defaultConfiguration() {
        return array();
    }
    
    protected function defaultParserVars() {
        $this->parser->assign('widget_id', $this->id);
    }

    abstract public function render();
    
    abstract public function getContentTypeName();

    abstract public function preConfigureForm();
    
    abstract public function validateConfiguration($configuration);
    
    abstract public function mergeConfiguration($old_configuration, $new_configuration);

    /**
    * __get
    *
    * Allows models to access CI's loaded classes using the same
    * syntax as controllers.
    *
    * @param	string
    * @access private
    */
   function __get($key)
   {
        $CI =& get_instance();
        return $CI->$key;
   }
}
