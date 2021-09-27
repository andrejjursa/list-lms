<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Solution_version model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Solution_version extends DataMapper implements DataMapperExtensionsInterface {
    
    public $has_one = array(
        'solution',
    );
    
}