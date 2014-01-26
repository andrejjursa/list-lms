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
        'test',
    );
    
    public $has_one = array(
        'author' => array(
            'class' => 'teacher',
            'other_field' => 'task',
            'join_self_as' => 'task',
            'join_other_as' => 'author',
        ),
    );


    /**
     * Add special filtering of tasks by categories.
     * @param array<mixed> $filter two dimensional array of category IDs, it is represented as logic formula in conjunctive normal form, where first dimension is clause and second is disjunct inside clause.
     */
    public function add_categories_filter($filter) {
        $select_subquery = ' ( SELECT `cat_tasks`.`id` FROM `tasks` `cat_tasks` ';
        $where_part = '';
        if (count($filter) > 0) {
            $clause = 0;
            /*echo '<pre>';
            print_r($filter);
            echo '</pre>';*/
            foreach ($filter as $cats) {
                if (count($cats) > 0) {
                    $where_part .= (!empty($where_part) ? ' AND ' : '') . ' ( ';
                    $or_where_part = '';
                    $category = FALSE;
                    $task_set = FALSE;
                    foreach ($cats as $cat) {
                        if (substr($cat, 0, 9) == 'category:') {
                            if (!$category) {
                                $select_subquery .= ' LEFT OUTER JOIN `task_category_rel` `categories_' . $clause . '` ON `cat_tasks`.`id` = `categories_' . $clause . '`.`task_id` ';
                                $category = TRUE;
                            }
                            $category = new Category();
                            $id_list = $category->get_id_list(intval(substr($cat, 9)));
                            $or_where_part .= (!empty($or_where_part) ? ' OR ' : '') . '`categories_' . $clause . '`.`category_id` IN (' . implode(', ', $id_list) . ') ';
                        } else if (substr($cat, 0, 7) == 'course:') {
                            if (!$task_set) {
                                $select_subquery .= ' LEFT OUTER JOIN `task_task_set_rel` `task_rel_' . $clause . '` ON `cat_tasks`.`id` = `task_rel_' . $clause . '`.`task_id` ';
                                $select_subquery .= ' LEFT OUTER JOIN `task_sets` `task_set_' . $clause . '` ON `task_rel_' . $clause . '`.`task_set_id` = `task_set_' . $clause . '`.`id` ';
                                $task_set = TRUE;
                            }
                            $or_where_part .= (!empty($or_where_part) ? ' OR ' : '') . '`task_set_' . $clause . '`.`course_id` = ' . intval(substr($cat, 7));
                        }
                    }
                    $where_part .= $or_where_part . ' ) ';
                    $clause++;
                }
            }
        }
        $where_part .= (!empty($where_part) ? ' AND ' : '') . ' `tasks`.`id` = `cat_tasks`.`id` ';
        $select_subquery .= ' WHERE ' . $where_part . ' GROUP BY `cat_tasks`.`id` ) ';
        $this->db->ar_where[] = ' EXISTS ' . $select_subquery;
        //echo ' EXISTS ' . $select_subquery;
    }
    
    /**
     * Return list of files for this task.
     * @return array<mixed> list of files for this task.
     */
    public function get_task_files() {
        if (!is_null($this->id)) {
            $path = 'private/uploads/task_files/task_' . intval($this->id) . '/';
            return $this->get_files($path);
        } else {
            return array();
        }
    }
    
    /**
     * Return list of hidden files for this task.
     * @return array<mixed> list of hidden files for this task.
     */
    public function get_task_hidden_files() {
        if (!is_null($this->id)) {
            $path = 'private/uploads/task_files/task_' . intval($this->id) . '/hidden/';
            return $this->get_files($path);
        } else {
            return array();
        }
    }
    
    /**
     * Return list of files from given path.
     * @param string $path where to look for files.
     * @return array<mixed> list of files.
     */
    private function get_files($path) {
        $files = array();
        if (file_exists($path)) {
            $files_in_dir = scandir($path);
            foreach ($files_in_dir as $file) {
                if (is_file($path . $file)) {
                    $ext = strrpos($path . $file, '.');
                    if (substr($path . $file, $ext) !== 'upload_part') {
                        $files[] = array(
                            'file' => $file,
                            'filepath' => $path . $file,
                            'size' => get_file_size($path . $file),
                        );
                    }
                }
            }
        }
        return $files;
    }
    
    /**
     * Deletes relations (if parameters are set) or this object from database.
     * @param DataMapper|string $object related object to delete from relation.
     * @param string $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '') {
        $this_id = $this->id;
        parent::delete($object, $related_field);
        if (empty($object) && !is_array($object) && !empty($this_id)) {
            $tests = new Test();
            $tests->where_related($this);
            $tests->get();
            if ($tests->result_count()) { foreach ($tests->all as $test) {
                $test->delete();
            }}
            $path = 'private/uploads/task_files/task_' . intval($this_id) . '/';
            if (file_exists($path)) {
                unlink_recursive($path, TRUE);
            }
        }
    }
    
}