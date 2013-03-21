<?php

class Course extends DataMapper {
    
    public $has_one = array(
        'period'
    );
    
}