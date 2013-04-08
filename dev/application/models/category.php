<?php

class Category extends DataMapper {
    
    public $table = 'categories';
    
    public $has_one = array(
        'parent' => array(
            'class' => 'category',
            'other_field' => 'subcategory',
        ),
    );
    
    public $has_many = array(
        'subcategory' => array(
            'class' => 'category',
            'other_field' => 'parent',
        ),
    );
}