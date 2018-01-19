<?php

/**
 * Course content model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course_content_model extends DataMapper {

    public $table = 'course_content';

    public $has_one = ['course', 'course_content_group'];
    
    public static function get_next_sorting_number($course_id, $content_group_id = NULL) {
        if (!is_int($course_id)) {
            if ($course_id instanceof Course && $course_id->exists() && !is_null($course_id->id)) {
                $course_id = (int)$course_id->id;
            } else {
                throw new InvalidArgumentException('Argument $course_id must be integer or preloaded Course.');
            }
        }
        $group_id = NULL;
        if (!is_null($content_group_id)) {
            if (is_int($content_group_id)) {
                $group_id = (int)$content_group_id;
            } elseif ($content_group_id instanceof Course_content_group && $content_group_id->exists() && !is_null($content_group_id->id)) {
                $group_id = (int)$content_group_id->id;
            } else {
                throw new InvalidArgumentException('Argument $content_group_id must be NULL, integer of preloaded Course_content_group.');
            }
        }
        
        if (!is_null($group_id)) {
            $course_content = new Course_content_model();
            $course_content->select('');
            $course_content->select_func('', ['MAX' => '@sorting', '+', 1], 'new_sorting');
            $course_content->where_related('course_content_group', 'id', $group_id);
            $course_content->where_related('course', 'id', $course_id);
            $course_content->where_related('course_content_group/course', 'id', $course_id);
            $course_content->get();
            
            return (int)$course_content->new_sorting;
        } else {
            $course_content = new Course_content_model();
            $course_content->select('');
            $course_content->select_func('', ['MAX' => '@sorting', '+', 1], 'new_sorting');
            $course_content->where('course_content_group_id', NULL);
            $course_content->where_related('course', 'id', $course_id);
            $course_content->get();
            
            $course_content_group = new Course_content_group();
            $course_content_group->select('');
            $course_content_group->select_func('', ['MAX' => '@sorting', '+', 1], 'new_sorting');
            $course_content_group->where_related('course', 'id', $course_id);
            $course_content_group->get();
            
            return max((int)$course_content->new_sorting, (int)$course_content_group->new_sorting);
        }
        
    }

}