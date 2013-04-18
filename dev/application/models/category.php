<?php

/**
 * Category model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Category extends DataMapper {
    
    private static $cached_categories_array = NULL;
    
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
        'task' => array(
            'join_table' => 'task_category_rel',
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
    
    public function get_id_list($root_id) {
        if (!is_integer($root_id)) { return array( 0 ); }
        $cat_array = array();
        if (!isset(self::$cached_categories_array)) {
            $categories = new Category();
            $query = $categories->select('id, parent_id')->order_by('parent_id', 'asc')->get_raw();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $cat_array[intval($row['parent_id'])][] = intval($row['id']);
                }
            }
            self::$cached_categories_array = $cat_array;
        } else {
            $cat_array = self::$cached_categories_array;
        }
        if (count($cat_array) > 0) {
            $output = array( intval($root_id) );
            $this->make_id_list($cat_array, intval($root_id), $output);
            return $output;
        } else {
            return array( 0 );
        }
    }
    
    private function make_id_list($cat_array, $parent, &$output) {
        if (isset($cat_array[intval($parent)]) && count($cat_array[intval($parent)]) > 0) {
            foreach ($cat_array[intval($parent)] as $child) {
                $output[] = intval($child);
                $this->make_id_list($cat_array, intval($child), $output);
            }
        }
    }
}