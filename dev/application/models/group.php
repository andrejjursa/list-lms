<?php

class Group extends DataMapper {
    
    public $has_one = array(
        'course'
    );
    
    public $has_many = array(
        'room'
    );
}