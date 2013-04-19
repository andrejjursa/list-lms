<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends MY_Controller {
    
    public function index() {
        $this->load->library('migration');
        $this->migration->version(8);
        show_error($this->migration->error_string());
    }
    
}