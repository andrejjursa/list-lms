<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include_once APPPATH . 'controllers/admin/tests.php';

/**
 * Fetests controller for frontend.
 *
 * @package LIST_FE_Controllers
 * @author  Andrej Jursa
 */
class Fetests extends Tests
{
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_student();
    }
    
}