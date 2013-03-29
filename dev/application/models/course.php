<?php

class Course extends DataMapper {
    
    public $has_one = array(
        'period'
    );
    
    public $has_many = array(
        'group'
    );
    
}