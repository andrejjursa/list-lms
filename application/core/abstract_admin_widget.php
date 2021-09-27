<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Abstract admin widget class.
 * @package LIST_Tests
 * @author Andrej Jursa
 *
 * @property LIST_Lang $lang
 * @property LIST_Parser $parser
 * @property DataMapper $db
 * @property LIST_Loader $load
 * @property LIST_Form_validation $form_validation
 * @property Changelog $changelog
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
    
    public function defaultConfiguration(): array
    {
        return array();
    }
    
    protected function defaultParserVars(): void
    {
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
    * @param string $key
    * @access private
    */
   function __get(string $key)
   {
        $CI =& get_instance();
        return $CI->$key;
   }
}
