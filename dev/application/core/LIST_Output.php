<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Turn Smarty debug compatible whith Ci-Smarty parse fetch method
 *
 * Responsible for sending debug Smarty final output to browser (Smarty_Internal_Debug::display_debug) using debug console (pop-window)
 *(tks for Redn0x - http://www.smarty.net/docs/en/chapter.debugging.console.tpl)
 *
 * @package       CodeIgniter
 * @subpackage    Core
 * @hacked-by     octaaugusto	  
*/


class LIST_Output extends CI_Output {
    	
        const IV_ACTION_RESULT = 'action_result';
    
	public function _display($output = '')
	{
		parent::_display($output);
		// If Smarty is active - NOTE: $this->output->enable_profiler(TRUE) active Smarty debug to simplify
		if (class_exists('CI_Controller') && class_exists('Smarty_Internal_Debug') && (config_item('smarty_debug') || $this->enable_profiler))
		{
			$CI =& get_instance();
			Smarty_Internal_Debug::display_debug( $CI->smarty);
		}
	}
        
        public function set_internal_value($name, $value) {
            $CI =& get_instance();
            $CI->internal_values[$name] = $value;
        }
        
        public function get_internal_value($name) {
            $CI =& get_instance();
            if (isset($CI->internal_values[$name])) {
                return $CI->internal_values[$name];
            }
            return NULL;
        }
}
// END MY_Output Class

/* End of file MY_Output.php */
/* Location: ./application/core/MY_Output.php */