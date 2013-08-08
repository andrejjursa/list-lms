<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Filters data library.
 * @package LIST_Libraries
 * @author Andrej Jursa
 */
class Filter {
    
    const FILTERS_ARRAY = 'list_filters_array';
    const FILTERS_SETTINGS = 'list_filters_settings';
    const FILTER_SETTINGS_COURSE_FIELD = 'course_field';
    const FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE = 'fields_deleted_on_course_change';
    
    protected $CI = NULL;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    public function store_filter($filter_name, $filter_data) {
        if (is_array($filter_data) && !empty($filter_data)) {
            $filters = $this->CI->session->userdata(self::FILTERS_ARRAY);
            $filters[$filter_name] = $filter_data;
            $this->CI->session->set_userdata(self::FILTERS_ARRAY, $filters);
        }
    }
    
    public function restore_filter($filter_name) {
        $filters = $this->CI->session->userdata(self::FILTERS_ARRAY);
        $filters = empty($filters) || is_null($filters) || !is_array($filters) ? array() : $filters; 
        return array_key_exists($filter_name, $filters) ? $filters[$filter_name] : array();
    }

    public function set_filter_course_name_field($filter_name, $field_name) {
        if (is_null($field_name) || (is_string($field_name) && !empty($field_name))) {
            $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
            $filter_settings[$filter_name][self::FILTER_SETTINGS_COURSE_FIELD] = $field_name;
            $this->CI->session->set_userdata(self::FILTERS_SETTINGS, $filter_settings);
        }
    }
    
    public function set_filter_delete_on_course_change($filter_name, $fields) {
        if ($this->is_array_of_strings($fields)) {
            $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
            $filter_settings[$filter_name][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE] = $fields;
            $this->CI->session->set_userdata(self::FILTERS_SETTINGS, $filter_settings);
        }
    }
    
    public function get_filter_course_name_field($filter_name) {
        $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
        if (!isset($filter_settings[$filter_name]) || !isset($filter_settings[$filter_name][self::FILTER_SETTINGS_COURSE_FIELD])) { return NULL; }
        return $filter_settings[$filter_name][self::FILTER_SETTINGS_COURSE_FIELD];
    }
    
    public function get_filter_delete_on_course_change($filter_name) {
        $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
        if (!isset($filter_settings[$filter_name]) || !isset($filter_settings[$filter_name][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE])) { return NULL; }
        return $filter_settings[$filter_name][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE];
    }
    
    public function set_all_filters_course($course) {
        $course_id = 0;
        if (is_numeric($course) && intval($course) > 0) {
            $course_id = intval($course);
        } elseif (is_object($course) && $course instanceof Course && $course->exists()) {
            $course_id = $course->id;
        } elseif (is_null($course)) {
            $course_id = NULL;
        } else {
            return FALSE;
        }
        
        $filters = $this->CI->session->userdata(self::FILTERS_ARRAY);
        $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
        if (!empty($filters)) { foreach($filters as $filter => $data) {
            if (isset($filter_settings[$filter])) {
                $data = is_array($data) ? $data : array();
                if (isset($filter_settings[$filter][self::FILTER_SETTINGS_COURSE_FIELD])) {
                    $data[$filter_settings[$filter][self::FILTER_SETTINGS_COURSE_FIELD]] = $course_id;
                }
                if (isset($filter_settings[$filter][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE])) {
                    $delete_fields = $filter_settings[$filter][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE];
                    if ($this->is_array_of_strings($delete_fields) && !empty($delete_fields)) { foreach ($delete_fields as $field) {
                        if (array_key_exists($field, $data)) { unset($data[$field]); }
                    }}
                }
                $filters[$filter] = $data;
            }
        }}
        $this->CI->session->set_userdata(self::FILTERS_ARRAY, $filters);
        return TRUE;
    }
    
    private function is_array_of_strings($array) {
        if (!is_array($array)) { return FALSE; }
        if (empty($array)) { return TRUE; }
        $result = TRUE;
        foreach($array as $item) {
            $result = $result && is_string($item);
            if (!$result) { break; }
        }
        return $result;
    }
    
}