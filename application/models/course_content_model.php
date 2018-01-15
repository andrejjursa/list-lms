<?php

/**
 * Course content model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course_content_model extends DataMapper {

    public $table = 'course_content';

    public $has_one = array('course');

}