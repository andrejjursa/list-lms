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
    
    /**
     * Add special filtering of tasks by categories.
     * @param array<mixed> $filter two dimensional array of category IDs, it is represented as logic formula in conjunctive normal form, where first dimension is clause and second is disjunct inside clause.
     */
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
                            'size' => $this->get_file_size($path . $file),
                        );
                    }
                }
            }
        }
        return $files;
    }
    
    /**
     * Compute file capacity and return value with unit.
     * @param string $filename path and file name.
     * @return string capacity of file in bytes, KiB, MiB or GiB.
     */
    private function get_file_size($filename) {
        $size_bytes = @filesize($filename);
        if ($size_bytes === FALSE || $size_bytes == 0) {
            return '0 B';
        }
        $size = $size_bytes;
        $unit = 'B';
        if ($size > 1023) {
            $size /= 1024;
            $unit = 'KiB';
        }
        if ($size > 1023) {
            $size /= 1024;
            $unit = 'MiB';
        }
        if ($size > 1023) {
            $size /= 1024;
            $unit = 'GiB';
        }
        return number_format($size, 2, '.', ' ') . ' ' . $unit;
    }
}