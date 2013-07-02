<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This extended CI_Email library provides new functions to handle emails with smarty templates.
 * @package LIST_Libraries
 * @author Andrej Jursa
 */
class LIST_Email extends CI_Email {
    
    /**
     * @var CodeIgniter CodeIgniter object.
     */
    protected $CI;
    /**
     * @var array<string> default e-mail addresses.
     */
    protected $default_addresses;

    /**
     * Extended constructor, which will first load default e-mail configuration from config file and then merge it with provided custom configuration.
     * It will also load all default addresses to internal variable.
     * @param array<mixed> $config configuration options.
     */
    public function __construct($config = array()) {
        $this->CI =& get_instance();
        
        $config_default = $this->CI->config->item('email');
        $config = array_merge($config_default, $config);
        
        parent::__construct($config);
        
        $this->default_addresses = $this->CI->config->item('email_address');
    }
    
    /**
     * Set mail from parameter to the one stored in config.php file.
     * @return LIST_Email this object for method chaining.
     * @throws Exception when there is error in configuration (ie. missing/null array indexes or invalid e-mail address). 
     */
    public function from_system() {
        $this->CI->load->library('form_validation');
        if (isset($this->default_addresses['system']['name']) && isset($this->default_addresses['system']['email']) && $this->CI->form_validation->valid_email($this->default_addresses['system']['email'])) {
            $this->from($this->default_addresses['system']['email'], $this->default_addresses['system']['name']);
        } else {
            throw new Exception('Invalid configuration of default e-mail address for "system".');
        }
        return $this;
    }
    
    /**
     * Set mail reply to parameter to the one stored in config.php file.
     * @return LIST_Email this object for method chaining.
     * @throws Exception when there is error in configuration (ie. missing/null array indexes or invalid e-mail address). 
     */
    public function reply_to_system() {
        $this->CI->load->library('form_validation');
        if (isset($this->default_addresses['system']['name']) && isset($this->default_addresses['system']['email']) && $this->CI->form_validation->valid_email($this->default_addresses['system']['email'])) {
            $this->reply_to($this->default_addresses['system']['email'], $this->default_addresses['system']['name']);
        } else {
            throw new Exception('Invalid configuration of default e-mail address for "system".');
        }
        return $this;
    }

    /**
     * Will construct and set html message by provided smarty template code or smarty template file.
     * @param string $template can be template code or template file (file must be prepended by file: prefix, ie. 'file:emails/new_user.tpl').
     * @param array<mixed> $tpl_data template data.
     * @return LIST_Email this object for method chaining.
     */
    public function build_message_body($template, $tpl_data = array()) {
        $message = '';
        if (strtolower(substr($template, 0, 5)) == 'file:') {
            $message = $this->CI->parser->parse(substr($template, 5), $tpl_data, TRUE, FALSE, '');
        } else {
            $message = $this->CI->parser->string_parse($template, $tpl_data);
        }
        $this->message($message);
        return $this;
    }
}

?>
