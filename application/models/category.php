<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Category model.
 *
 * @property int      $id
 * @property string   $updated   date time format YYYY-MM-DD HH:MM:SS
 * @property string   $created   date time format YYYY-MM-DD HH:MM:SS
 * @property string   $name
 * @property int|null $parent_id entity id of model {@see Category}
 * @property Category $parent
 * @property Category $subcategory
 * @property Task     $task
 *
 * @method DataMapper where_related_parent(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_subcategory(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Category extends DataMapper implements DataMapperExtensionsInterface
{
    
    private static $cached_categories_array = null;
    
    public $table = 'categories';
    
    public $has_one = [
        'parent' => [
            'class'       => 'category',
            'other_field' => 'subcategory',
        ],
    ];
    
    public $has_many = [
        'subcategory' => [
            'class'       => 'category',
            'other_field' => 'parent',
        ],
        'task'        => [
            'join_table' => 'task_category_rel',
        ],
    ];
    
    /**
     * Loads all categories from database and return them in tree structure.
     *
     * @return array tree structure of categories.
     */
    public function get_all_structured(): array
    {
        $categories = new Category();
        $categories->include_related_count('task');
        $categories
            ->order_by('parent_id', 'asc')
            ->order_by_with_constant('name', 'asc')
            ->get();
        return $this->make_structure($categories->all);
    }
    
    /**
     * Creates tree structure from given array of categories.
     *
     * @param array<Category> $categories array of categories.
     * @param int|null        $parent     parent id number or NULL for root line.
     *
     * @return array tree structure.
     */
    private function make_structure($categories, $parent = null): array
    {
        $output = [];
        foreach ($categories as $key => $category) {
            if ($category->parent_id === $parent) {
                $output[]['category'] = $category;
                unset($categories[$key]);
            }
        }
        foreach ($output as $key => $data) {
            $output[$key]['subcategories'] = $this->make_structure($categories, $data['category']->id);
        }
        return $output;
    }
    
    /**
     * For given ID returns list of all IDs (the ID given and all its children).
     * On error, it returns array with ID 0, to be properly used inside WHERE IN sql query.
     *
     * @param int|null $root_id ID of root category.
     *
     * @return array<integer> all IDs.
     */
    public function get_id_list($root_id): array
    {
        if (!is_int($root_id)) {
            return [0];
        }
        $cat_array = [];
        if (!isset(self::$cached_categories_array)) {
            $categories = new Category();
            $query = $categories
                ->select('id, parent_id')
                ->order_by('parent_id', 'asc')
                ->get_raw();
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $cat_array[(int)$row['parent_id']][] = (int)$row['id'];
                }
            }
            self::$cached_categories_array = $cat_array;
        } else {
            $cat_array = self::$cached_categories_array;
        }
        if (count($cat_array) > 0) {
            $output = [(int)$root_id];
            $this->make_id_list($cat_array, (int)$root_id, $output);
            return $output;
        }
        
        return [0];
    }
    
    /**
     * Recursively iterates through category array and builds output list of IDs.
     *
     * @param array<integer>  $cat_array list of IDs, two-dimensional, first dimension is parent and second dimension
     *                                   holds children.
     * @param integer         $parent    parent ID, which children will go to output.
     * @param array<integer> &$output    output of function, array of all IDs.
     */
    private function make_id_list($cat_array, $parent, &$output): void
    {
        if (isset($cat_array[(int)$parent]) && count($cat_array[(int)$parent]) > 0) {
            foreach ($cat_array[(int)$parent] as $child) {
                $output[] = (int)$child;
                $this->make_id_list($cat_array, (int)$child, $output);
            }
        }
    }
}