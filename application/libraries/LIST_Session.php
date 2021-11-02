<?php

/**
 * Extended LIST_Session library.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 */
class LIST_Session extends CI_Session
{
    
    public function sess_update()
    {
        if (!$this->CI->input->is_ajax_request()) {
            parent::sess_update();
        }
    }
    
}