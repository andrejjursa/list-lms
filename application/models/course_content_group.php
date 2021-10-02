<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Course content groups model.
 *
 * @property int                  $id
 * @property string               $updated   date time format YYYY-MM-DD HH:MM:SS
 * @property string               $created   date time format YYYY-MM-DD HH:MM:SS
 * @property string               $title
 * @property int|null             $course_id entity id of model {@see Course}
 * @property int                  $sorting
 * @property Course               $course
 * @property Course_content_model $course_content_model
 *
 * @method DataMapper where_related_course(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_course_content_model(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Course_content_group extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $table = 'course_content_groups';
    
    public $has_one = ['course'];
    
    public $has_many = ['course_content_model'];
    
    public static function get_all_groups($course_id = null, $valueText = false): array
    {
        $groups = new Course_content_group();
        $groups->select('*');
        $groups->order_by('sorting', 'asc');
        if (!is_int($course_id) && !$valueText) {
            $groups->where_related('course', 'id', (int)$course_id);
        }
        $groups->get_iterated();
        
        $output = $valueText ? [] : [null => ''];
        
        $ci =& get_instance();
        
        foreach ($groups as $group) {
            if ($valueText) {
                $output[$group->course_id][] = [
                    'value' => $group->id,
                    'text'  => $ci->lang->get_overlay_with_default('course_content_groups', $group->id, 'title', $group->title),
                ];
            } else {
                $output[$group->id] = $ci->lang->get_overlay_with_default('course_content_groups', $group->id, 'title', $group->title);
            }
        }
        
        return $output;
    }
    
    public static function get_next_sorting_number($course_id)
    {
        return Course_content_model::get_next_sorting_number($course_id);
    }
    
    public function isNew(): bool
    {
        if (($this->stored->id ?? null) === null) {
            return false;
        }
        
        if (($this->stored->created ?? null) === null) {
            return false;
        }
        
        if (date('Y-m-d H:i:s', strtotime('-5 minutes')) <= date('Y-m-d H:i:s', strtotime($this->stored->created))) {
            return true;
        }
        
        return false;
    }
    
}