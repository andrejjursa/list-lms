<?php

/**
 * CI Smarty
 *
 * Smarty templating for Codeigniter
 *
 * @method assign($tpl_var, $value = null, $nocache = false)
 *
 * @package   CI Smarty
 * @author    Dwayne Charrington
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   2.0
 * @copyright Copyright (c) 2012 Dwayne Charrington and Github contributors
 * @link      http://ilikekillnerds.com
 */
class LIST_Parser extends CI_Parser
{
    
    protected $CI;
    
    protected $_module = '';
    protected $_template_locations = [];
    
    // Current theme location
    protected $_current_path = null;
    
    // The name of the theme in use
    protected $_theme_name = '';
    
    protected $css_files = [];
    protected $js_files = [];
    
    public function __construct()
    {
        // Codeigniter instance and other required libraries/files
        $this->CI =& get_instance();
        $this->CI->load->library('smarty');
        $this->CI->load->helper('parser');
        
        // Detect if we have a current module
        $this->_module = $this->current_module();
        
        // What controllers or methods are in use
        $this->_controller = $this->CI->router->fetch_class();
        $this->_method = $this->CI->router->fetch_method();
        
        // If we don't have a theme name stored
        if ($this->_theme_name == '') {
            $this->set_theme(config_item('theme_name'));
        }
        
        // Update theme paths
        $this->_update_theme_paths();
        
        $this->CI->smarty->registerPlugin('modifier', 'php_strip_tags', 'strip_tags');
        
        $this->CI->smarty->caching_type = 'mysql';
    }
    
    /**
     * Call
     * able to call native Smarty methods
     *
     * @returns mixed
     */
    public function __call($method, $params = [])
    {
        if (!method_exists($this, $method)) {
            return call_user_func_array([$this->CI->smarty, $method], $params);
        }
    }
    
    /**
     * Set Theme
     *
     * Set the theme to use
     *
     * @access public
     *
     * @param $name
     */
    public function set_theme($name): void
    {
        // Store the theme name
        $this->_theme_name = trim($name);
        
        // Our themes can have a functions.php file just like Wordpress
        $functions_file = config_item('theme_path') . $this->_theme_name . '/functions.php';
        
        // In case we have a theme in the application directory
        $functions_file2 = APPPATH . "themes/" . $this->_theme_name . '/functions.php';
        
        // If we have a functions file, include it
        if (file_exists($functions_file)) {
            include_once($functions_file);
        } else if (file_exists($functions_file2)) {
            include_once($functions_file2);
        }
        
        // Update theme paths
        $this->_update_theme_paths();
    }
    
    /**
     * Get Theme
     *
     * Does what the function name implies: gets the name of
     * the currently in use theme.
     *
     * @return string
     */
    public function get_theme(): string
    {
        return $this->_theme_name ?? '';
    }
    
    /**
     * Current Module
     *
     * Just a fancier way of getting the current module
     * if we have support for modules
     *
     * @access public
     * @return string
     */
    public function current_module(): string
    {
        // Modular Separation / Modular Extensions has been detected
        if (method_exists($this->CI->router, 'fetch_module')) {
            $module = $this->CI->router->fetch_module();
            return (!empty($module)) ? $module : '';
        }
        
        return '';
    }
    
    /**
     * Parse
     *
     * Parses a template using Smarty 3 engine
     *
     * @access public
     *
     * @param $template
     * @param $data
     * @param $return
     * @param $caching
     * @param $cache_id
     * @param $theme
     *
     * @return string|bool
     */
    public function parse($template, $data = [], $return = false, $caching = false, $cache_id = '', $theme = '')
    {
        // If we don't want caching, disable it
        if ($caching === false) {
            $this->CI->smarty->disable_caching();
        } else if ($caching === true) {
            $this->CI->smarty->enable_caching();
        } else if (is_int($caching)) {
            $this->CI->smarty->setCaching($caching);
        }
        
        // If no file extension dot has been found default to defined extension for view extensions
        if (!stripos($template, '.')) {
            $template = $template . "." . $this->CI->smarty->template_ext;
        }
        
        // Are we overriding the theme on a per load view basis?
        if ($theme !== '') {
            $this->set_theme($theme);
        }
        
        // Get the location of our view, where the hell is it?
        // But only if we're not accessing a smart resource
        if (!stripos($template, ':')) {
            $template = $this->_find_view($template);
        }
        
        // If we have variables to assign, lets assign them
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $this->CI->smarty->assign($key, $val);
            }
        }
        
        $this->CI->smarty->assign('list_internal_css_files', $this->css_files);
        $this->CI->smarty->assign('list_internal_js_files', $this->js_files);
        
        if (ENVIRONMENT === 'development') {
            $this->CI->smarty->clearCompiledTemplate();
        }
        
        // Load our template into our string for judgement
        $template_string = $this->CI->smarty->fetch($template, $cache_id);
        
        // If we're returning the templates contents, we're displaying the template
        if ($return === false) {
            $this->CI->output->append_output($template_string);
            return true;
        }
        
        // We're returning the contents, fo' shizzle
        return $template_string;
    }
    
    /**
     * Add CSS file to list of files attached to template
     *
     * @access public
     *
     * @param       $file
     * @param array $attributes
     */
    public function add_css_file($file, $attributes = []): void
    {
        include_once APPPATH . 'third_party/Smarty/plugins/modifier.add_file_version.php';
        $defaults = [
            'media' => 'screen',
            'rel'   => 'stylesheet',
            'type'  => 'text/css',
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        $file = smarty_modifier_add_file_version(base_url("/public/css/" . $file));
        
        $html = '<link rel="' . $attributes['rel'] . '" type="' . $attributes['type'] . '" href="' . $file . '" '
            . ($attributes['media'] ? 'media="' . $attributes['media'] . '" ' : '') . '/>';
        
        $this->css_files[] = [
            'html'       => $html,
            'attributes' => $attributes,
            'file'       => $file,
        ];
    }
    
    /**
     * Add JS file to list of files attached to template
     *
     * @access public
     *
     * @param       $file
     * @param array $attributes
     */
    public function add_js_file($file, $attributes = []): void
    {
        include_once APPPATH . 'third_party/Smarty/plugins/modifier.add_file_version.php';
        $defaults = [
            'type' => 'text/javascript',
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        $file = smarty_modifier_add_file_version(base_url("/public/js/" . $file));
        
        $html = '<script type="' . $attributes['type'] . '" src="' . $file . '"></script>';
        
        $this->js_files[] = [
            'html'       => $html,
            'attributes' => $attributes,
            'file'       => $file,
        ];
    }
    
    public function add_js($content, $attributes = []): void
    {
        $defaults = [
            'type' => 'text/javascript',
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        $html = '<script type="' . $attributes['type'] . '">' . PHP_EOL . trim($content) . PHP_EOL . '</script>';
        
        $this->js_files[] = [
            'html'       => $html,
            'attributes' => null,
            'file'       => null,
        ];
    }
    
    /**
     * Clear the list of CSS files attached to template
     */
    public function clear_css_files(): void
    {
        $this->css_files = [];
    }
    
    /**
     * Clear the list of JS files attached to template
     */
    public function clear_js_files(): void
    {
        $this->js_files = [];
    }
    
    /**
     * CSS
     *
     * An asset function that returns a CSS stylesheet
     *
     * @access public
     *
     * @param       $file
     * @param array $attributes
     *
     * @return string
     */
    public function css($file, $attributes = []): string
    {
        $defaults = [
            'media' => 'screen',
            'rel'   => 'stylesheet',
            'type'  => 'text/css',
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        return '<link rel="' . $attributes['rel'] . '" type="' . $attributes['type'] . '" href="' .
            base_url(config_item('theme_path') . $this->get_theme() . "/css/" . $file)
            . '" media="' . $attributes['media'] . '">';
    }
    
    /**
     * JS
     *
     * An asset function that returns a script embed tag
     *
     * @access public
     *
     * @param       $file
     * @param array $attributes
     *
     * @return string
     */
    public function js($file, $attributes = []): string
    {
        $defaults = [
            'type' => 'text/javascript',
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        return '<script type="' . $attributes['type'] . '" src="' . base_url(config_item('theme_path')
                . $this->get_theme() . "/js/" . $file) . '"></script>';
    }
    
    /**
     * IMG
     *
     * An asset function that returns an image tag
     *
     * @access public
     *
     * @param       $file
     * @param array $attributes
     *
     * @return string
     */
    public function img($file, $attributes = []): string
    {
        $defaults = [
            'alt'   => '',
            'title' => '',
        ];
        
        $attributes = array_merge($defaults, $attributes);
        
        return '<img src ="' . base_url(config_item('theme_path') . $this->get_theme() . "/img/" . $file)
            . '" alt="' . $attributes['alt'] . '" title="' . $attributes['title'] . '" />';
    }
    
    /**
     * Theme URL
     *
     * A web friendly URL for determining the current
     * theme root location.
     *
     * @access public
     *
     * @param $location
     *
     * @return string
     */
    public function theme_url($location = ''): string
    {
        // The path to return
        $return = base_url(config_item('theme_path') . $this->get_theme()) . "/";
        
        // If we want to add something to the end of the theme URL
        if ($location !== '') {
            $return = $return . $location;
        }
        
        return trim($return);
    }
    
    /**
     * Finds location of view file and return it with this path.
     *
     * @access public
     *
     * @param $file
     *
     * @return string The path and file found
     */
    public function find_view($file): ?string
    {
        $current_path = $this->_current_path;
        $path = $this->_find_view($file);
        $this->_current_path = $current_path;
        return $path;
    }
    
    public function setCacheLifetimeForTemplateObject($file, $cache_lifetime): void
    {
        $path = $this->find_view($file);
        if (count($this->CI->smarty->template_objects) > 0) {
            foreach ($this->CI->smarty->template_objects as $cache_object) {
                if ($cache_object->template_resource == $path) {
                    $cache_object->cache_lifetime = $cache_lifetime;
                }
            }
        }
    }
    
    /**
     * Find View
     *
     * Searches through module and view folders looking for your view, sir.
     *
     * @access protected
     *
     * @param $file
     *
     * @return string The path and file found
     */
    protected function _find_view($file): ?string
    {
        // We have no path by default
        $path = null;
        
        // Iterate over our saved locations and find the file
        foreach ($this->_template_locations as $location) {
            if (file_exists($location . $file)) {
                // Store the file to load
                $path = $location . $file;
                
                $this->_current_path = $location;
                
                // Stop the loop, we found our file
                break;
            }
        }
        
        // Return the path
        return $path;
    }
    
    /**
     * Add Paths
     *
     * Traverses all added template locations and adds them
     * to Smarty so we can extend and include view files
     * correctly from a slew of different locations including
     * modules if we support them.
     *
     * @access protected
     */
    protected function _add_paths(): void
    {
        // Iterate over our saved locations and find the file
        foreach ($this->_template_locations as $location) {
            $this->CI->smarty->addTemplateDir($location);
        }
    }
    
    /**
     * Update Theme Paths
     *
     * Adds in the required locations for themes
     *
     * @access protected
     */
    protected function _update_theme_paths(): void
    {
        // Store a whole heap of template locations
        $this->_template_locations = [
            config_item('theme_path') . $this->_theme_name . '/views/modules/' . $this->_module . '/layouts/',
            config_item('theme_path') . $this->_theme_name . '/views/modules/' . $this->_module . '/',
            config_item('theme_path') . $this->_theme_name . '/views/layouts/',
            config_item('theme_path') . $this->_theme_name . '/views/',
            APPPATH . 'modules/' . $this->_module . '/views/layouts/',
            APPPATH . 'modules/' . $this->_module . '/views/',
            APPPATH . 'views/layouts/',
            APPPATH . 'views/',
        ];
        
        // Will add paths into Smarty for "smarter" inheritance and inclusion
        $this->_add_paths();
    }
    
    /**
     * String Parse
     *
     * Parses a string using Smarty 3
     *
     * @param string  $template
     * @param array   $data
     * @param boolean $return
     * @param mixed   $is_include
     */
    public function string_parse($template, $data = [], $return = false, $is_include = false)
    {
        $this->CI->smarty->assign($data);
        $caching = $this->CI->smarty->caching;
        $this->CI->smarty->caching = false;
        $output = $this->CI->smarty->fetch('string:' . $template);
        $this->CI->smarty->caching = $caching;
        return $output;
    }
    
    /**
     * Parse String
     *
     * Parses a string using Smarty 3. Never understood why there
     * was two identical functions in Codeigniter that did the same.
     *
     * @param string  $template
     * @param array   $data
     * @param boolean $return
     * @param mixed   $is_include
     */
    public function parse_string($template, $data = [], $return = false, $is_include = false)
    {
        return $this->string_parse($template, $data, $return, $is_include);
    }
    
}
