<?php

class Group extends DataMapper {
    
    public $has_one = array(
        'course'
    );
    
}