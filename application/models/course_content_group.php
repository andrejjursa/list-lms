<?php

/**
 * Course content groups model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course_content_group extends DataMapper {
    
    public $table = 'course_content_groups';
    
    public $has_one = ['course'];
    
    public $has_many = ['course_content_model'];
    
    public static function get_all_groups($course_id = NULL) {
        $groups = new Course_content_group();
        $groups->select('*');
        $groups->order_by_with_overlay('title', 'asc');
        if (!is_int($course_id)) {
            $groups->where_related('course', 'id', (int)$course_id);
        }
        $groups->get_iterated();
        
        $output = [NULL => ''];
        
        $ci =& get_instance();
        
        foreach ($groups as $group) {
            $output[$group->id] = $ci->lang->get_overlay_with_default('course_content_groups', $group->id, 'title', $group->title);
        }
        
        return $output;
    }
    
    public static function get_next_sorting_number($course_id) {
        return Course_content_model::get_next_sorting_number($course_id);
    }
    
}