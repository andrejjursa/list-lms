<?php

/**
 * Task model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Task extends DataMapper {
    
    public $has_many = array(
        'category' => array(
            'join_table' => 'task_category_rel',
        ),
        'task_set' => array(
            'join_table' => 'task_task_set_rel',
        ),
    );
    
    public function add_categories_filter($filter) {
        if (count($filter) > 0) {
            $clause = 0;
            foreach ($filter as $cats) {
                if (count($cats) > 0) {
                    $this->db->join($this->has_many['category']['join_table'] . ' `categories_' . $clause . '`', 'tasks.id = categories_' . $clause . '.task_id', 'left');
                    $this->group_start();
                    foreach ($cats as $cat) {
                        $category = new Category();
                        $id_list = $category->get_id_list(intval($cat));
                        $this->or_where('categories_' . $clause . '`.`category_id` IN (' . implode(', ', $id_list) . ')');
                    }
                    $this->group_end();
                    $clause++;
                }
            }
        }
        $this->group_by('id');
    }
}