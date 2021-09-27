<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Course content groups model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course_content_group extends DataMapper implements DataMapperExtensionsInterface {
    
    public $table = 'course_content_groups';
    
    public $has_one = ['course'];
    
    public $has_many = ['course_content_model'];
    
    public static function get_all_groups($course_id = NULL, $valueText = FALSE) {
        $groups = new Course_content_group();
        $groups->select('*');
        $groups->order_by('sorting', 'asc');
        if (!is_int($course_id) && !$valueText) {
            $groups->where_related('course', 'id', (int)$course_id);
        }
        $groups->get_iterated();
        
        $output = $valueText ? [] : [NULL => ''];
        
        $ci =& get_instance();
        
        foreach ($groups as $group) {
            if ($valueText) {
                $output[$group->course_id][] = [
                    'value' => $group->id,
                    'text' => $ci->lang->get_overlay_with_default('course_content_groups', $group->id, 'title', $group->title),
                ];
            } else {
                $output[$group->id] = $ci->lang->get_overlay_with_default('course_content_groups', $group->id, 'title', $group->title);
            }
        }
        
        return $output;
    }
    
    public static function get_next_sorting_number($course_id) {
        return Course_content_model::get_next_sorting_number($course_id);
    }
    
    public function isNew() {
        if (($this->stored->id ?? NULL) === NULL) {
            return FALSE;
        }
        
        if (($this->stored->created ?? NULL) === NULL) {
            return false;
        }
        
        if (date('Y-m-d H:i:s', strtotime('-5 minutes')) <= date('Y-m-d H:i:s', strtotime($this->stored->created))) {
            return true;
        }
        
        return false;
    }
    
}