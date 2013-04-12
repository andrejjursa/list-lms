<?php

/**
 * Room model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Room extends DataMapper {
    
    public $has_one = array(
        'group'
    );
    
}