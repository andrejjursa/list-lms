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
    
    public function get_all_structured() {
        $categories = new Category();
        $categories->order_by('parent_id', 'asc')->order_by('name', 'asc')->get();
        return $this->make_structure($categories->all);
    }
    
    private function make_structure($categories, $parent = NULL) {
        $output = array();
        foreach ($categories as $key => $category) {
            if ($category->parent_id == $parent) {
                $output[]['category'] = $category;
                unset($categories[$key]);
            }
        }
        foreach ($output as $key => $data) {
            $output[$key]['subcategories'] = $this->make_structure($categories, $data['category']->id);
        }
        return $output;
    }
}