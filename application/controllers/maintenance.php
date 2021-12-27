<?php

/**
 * Controller for maintenance messages.
 *
 * @property LIST_Loader $load
 * @property LIST_Parser $parser
 *
 * @package LIST_MAINTENANCE_Controllers
 * @author  Andrej Jursa
 */
class Maintenance extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('parser');
    }
    
    public function index(): void
    {
        $this->parser->parse('general/maintenance.tpl');
    }
    
}