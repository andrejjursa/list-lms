<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Filters data library.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 */
class Filter
{
    
    const FILTERS_ARRAY = 'list_filters_array';
    const FILTERS_SETTINGS = 'list_filters_settings';
    const FILTER_SETTINGS_COURSE_FIELD = 'course_field';
    const FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE = 'fields_deleted_on_course_change';
    
    protected $CI = null;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    /**
     * This method will store filter data under filter name into session.
     *
     * @param string       $filter_name name of filter.
     * @param array<mixed> $filter_data data of filter.
     */
    public function store_filter($filter_name, $filter_data)
    {
        if (is_array($filter_data) && !empty($filter_data)) {
            $filters = $this->CI->session->userdata(self::FILTERS_ARRAY);
            $filters[$filter_name] = $filter_data;
            $this->CI->session->set_userdata(self::FILTERS_ARRAY, $filters);
        }
    }
    
    /**
     * This method will return data by filter name.
     * If the second and third parameters are set, it will check teacher data for his prefered course and
     * inject this value into filter by specified conditions, only if these filter data are empty.
     *
     * @param string          $filter_name  name of filter.
     * @param integer|Teacher $teacher      teacher id or teacher object.
     * @param string          $course_field field in filter which contain course id in filter.
     *
     * @return array<midex> filter data.
     */
    public function restore_filter($filter_name, $teacher = null, $course_field = null)
    {
        $filters = $this->CI->session->userdata(self::FILTERS_ARRAY);
        $filters = empty($filters) || is_null($filters) || !is_array($filters) ? [] : $filters;
        $filter = array_key_exists($filter_name, $filters) ? $filters[$filter_name] : [];
        if (!is_null($teacher) && !is_null($course_field) && is_string($course_field) && empty($filter)) {
            if (!is_object($teacher) || !$teacher instanceof Teacher) {
                $teacher_id = $teacher;
                $teacher = new Teacher();
                $teacher->get_by_id((int)$teacher_id);
            }
            if ($teacher->exists()) {
                $filter[$course_field] = $teacher->prefered_course_id;
            }
        }
        return $filter;
    }
    
    /**
     * This method will store name of field containing course id inside filter identified by filter name.
     *
     * @param string $filter_name name of filter.
     * @param string $field_name  name of field containing course id.
     */
    public function set_filter_course_name_field($filter_name, $field_name)
    {
        if (is_null($field_name) || (is_string($field_name) && !empty($field_name))) {
            $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
            $filter_settings[$filter_name][self::FILTER_SETTINGS_COURSE_FIELD] = $field_name;
            $this->CI->session->set_userdata(self::FILTERS_SETTINGS, $filter_settings);
        }
    }
    
    /**
     * This method will store array of field names which have to be deleted, when filter is forced to change course id.
     *
     * @param string        $filter_name name of filter.
     * @param array<string> $fields      array of field names.
     */
    public function set_filter_delete_on_course_change($filter_name, $fields)
    {
        if ($this->is_array_of_strings($fields)) {
            $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
            $filter_settings[$filter_name][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE] = $fields;
            $this->CI->session->set_userdata(self::FILTERS_SETTINGS, $filter_settings);
        }
    }
    
    /**
     * This method will return name of field which contain course id.
     *
     * @param string $filter_name name of filter.
     *
     * @return string name of field or NULL if not set.
     */
    public function get_filter_course_name_field($filter_name)
    {
        $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
        if (!isset(
            $filter_settings[$filter_name],
            $filter_settings[$filter_name][self::FILTER_SETTINGS_COURSE_FIELD]
        )) {
            return null;
        }
        return $filter_settings[$filter_name][self::FILTER_SETTINGS_COURSE_FIELD];
    }
    
    /**
     * This method will return name of fields which will be deleted from filter when course id forced to be changed.
     *
     * @param string $filter_name name of filter.
     *
     * @return array<string> array of field names or NULL if not set.
     */
    public function get_filter_delete_on_course_change($filter_name)
    {
        $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
        if (!isset(
            $filter_settings[$filter_name],
            $filter_settings[$filter_name][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE]
        )) {
            return null;
        }
        return $filter_settings[$filter_name][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE];
    }
    
    /**
     * This method will force all filters which have field for course id to set this field to id specified in parameter.
     *
     * @param Course|integer $course course id, existing course object or NULL.
     *
     * @return boolean TRUE on success, FALSE otherwise.
     */
    public function set_all_filters_course($course)
    {
        $course_id = 0;
        if (is_numeric($course) && (int)$course > 0) {
            $course_id = (int)$course;
        } else if (is_object($course) && $course instanceof Course && $course->exists()) {
            $course_id = $course->id;
        } else if (is_null($course)) {
            $course_id = null;
        } else {
            return false;
        }
        
        $filters = $this->CI->session->userdata(self::FILTERS_ARRAY);
        $filter_settings = $this->CI->session->userdata(self::FILTERS_SETTINGS);
        if (!empty($filters)) {
            foreach ($filters as $filter => $data) {
                if (isset($filter_settings[$filter])) {
                    $data = is_array($data) ? $data : [];
                    if (isset($filter_settings[$filter][self::FILTER_SETTINGS_COURSE_FIELD])) {
                        $data[$filter_settings[$filter][self::FILTER_SETTINGS_COURSE_FIELD]] = $course_id;
                    }
                    if (isset($filter_settings[$filter][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE])) {
                        $delete_fields = $filter_settings[$filter][self::FILTER_SETTINGS_FIELDS_DELETED_ON_COURSE_CHANGE];
                        if ($this->is_array_of_strings($delete_fields) && !empty($delete_fields)) {
                            foreach ($delete_fields as $field) {
                                if (array_key_exists($field, $data)) {
                                    unset($data[$field]);
                                }
                            }
                        }
                    }
                    $filters[$filter] = $data;
                }
            }
        }
        $this->CI->session->set_userdata(self::FILTERS_ARRAY, $filters);
        return true;
    }
    
    /**
     * This method will verify if the array passed by parameter is array of strings.
     *
     * @param array<mixed> $array array to verify.
     *
     * @return boolean TRUE, if parameter is array of strings, FALSE otherwise.
     */
    private function is_array_of_strings($array)
    {
        if (!is_array($array)) {
            return false;
        }
        if (empty($array)) {
            return true;
        }
        $result = true;
        foreach ($array as $item) {
            $result = $result && is_string($item);
            if (!$result) {
                break;
            }
        }
        return $result;
    }
    
}