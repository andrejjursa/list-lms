<?php

if (!class_exists('simple_html_dom_node')) {
    include_once APPPATH . 'third_party/simplehtmldom/simple_html_dom.php';
}

/**
 * This extended CI_Email library provides new functions to handle emails with smarty templates.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 */
class LIST_Email extends CI_Email
{
    
    /**
     * @var CodeIgniter CodeIgniter object.
     */
    protected $CI;
    /**
     * @var array<string> default e-mail addresses.
     */
    protected $default_addresses;
    
    /**
     * Extended constructor, which will first load default e-mail configuration from config file and then merge it with
     * provided custom configuration. It will also load all default addresses to internal variable.
     *
     * @param array $config configuration options.
     */
    public function __construct($config = [])
    {
        $this->CI =& get_instance();
        
        $config_default = $this->CI->config->item('email');
        $config = array_merge($config_default, $config);
        
        parent::__construct($config);
        
        $this->default_addresses = $this->CI->config->item('email_address');
    }
    
    /**
     * Set mail from parameter to the one stored in config.php file.
     *
     * @return LIST_Email this object for method chaining.
     * @throws Exception when there is error in configuration (ie. missing/null array indexes or invalid e-mail
     *                   address).
     */
    public function from_system(): LIST_Email
    {
        $this->CI->load->library('form_validation');
        if (isset($this->default_addresses['system']['name'], $this->default_addresses['system']['email'])
            && $this->CI->form_validation->valid_email($this->default_addresses['system']['email'])
        ) {
            $this->from($this->default_addresses['system']['email'], $this->default_addresses['system']['name']);
        } else {
            throw new Exception('Invalid configuration of default e-mail address for "system".');
        }
        return $this;
    }
    
    /**
     * Set mail reply to parameter to the one stored in config.php file.
     *
     * @return LIST_Email this object for method chaining.
     * @throws Exception when there is error in configuration (ie. missing/null array indexes or invalid e-mail
     *                   address).
     */
    public function reply_to_system(): LIST_Email
    {
        $this->CI->load->library('form_validation');
        if (isset($this->default_addresses['system']['name'], $this->default_addresses['system']['email'])
            && $this->CI->form_validation->valid_email($this->default_addresses['system']['email'])
        ) {
            $this->reply_to($this->default_addresses['system']['email'], $this->default_addresses['system']['name']);
        } else {
            throw new Exception('Invalid configuration of default e-mail address for "system".');
        }
        return $this;
    }
    
    /**
     * Will construct and set html message by provided smarty template code or smarty template file.
     *
     * @param string $template can be template code or template file (file must be prepended by file: prefix, ie.
     *                         'file:emails/new_user.tpl').
     * @param array  $tpl_data template data.
     *
     * @return LIST_Email this object for method chaining.
     */
    public function build_message_body($template, $tpl_data = []): LIST_Email
    {
        $message = '';
        if (stripos($template, 'file:') === 0) {
            $message = $this->CI->parser->parse(substr($template, 5), $tpl_data, true, false, '');
        } else {
            $message = $this->CI->parser->string_parse($template, $tpl_data);
        }
        $this->message($message);
        return $this;
    }
    
    protected function _get_alt_message(): string
    {
        $generate_alt_message = empty($this->alt_message);
        if ($generate_alt_message) {
            $protect_body = $this->_body;
            
            $html = str_get_html(
                $this->_body,
                true,
                true,
                DEFAULT_TARGET_CHARSET,
                false
            );
            foreach ($html->find('a') as $a_tag) {
                if ($a_tag->plaintext !== $a_tag->href) {
                    $a_tag_clone = clone $a_tag;
                    $a_tag_clone->innertext = $a_tag_clone->href;
                    $new_a_tag = $a_tag->innertext . '[' . $a_tag_clone . ']';
                    $a_tag->outertext = $new_a_tag;
                    unset($a_tag_clone);
                }
            }
            
            $this->_body = $html->__toString();
        }
        
        $output = parent::_get_alt_message();
        
        if ($generate_alt_message) {
            $output = html_entity_decode($output);
            $this->_body = $protect_body;
        }
        
        return $output;
    }
}
