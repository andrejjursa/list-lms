<?php

/**
 * Category model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
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
    
    /**
     * Loads all categories from database and return them in tree structure.
     * @return array<mixed> tree structure of categories.
     */
    public function get_all_structured() {
        $categories = new Category();
        $categories->order_by('parent_id', 'asc')->order_by('name', 'asc')->get();
        return $this->make_structure($categories->all);
    }
    
    /**
     * Creates tree structure from given array of categories.
     * @param array<Category> $categories array of categories.
     * @param integer $parent parent id number or NULL for root line.
     * @return array<mixed> tree structure.
     */
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