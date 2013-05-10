<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This extended CI_Form_validation library provides some new and better validation functions.
 * @package LIST_Libraries
 * @author Andrej Jursa
 */
class LIST_Form_validation extends CI_Form_validation {
    
    /**
     * Better version of standard matches method, this one will check if field is array and find appropriate value of this field.
     * @param string $str current value of form field.
     * @param string $field form field name, can be array.
     * @return boolean validation result.
     */
    public function matches($str, $field) {
        if (strpos($field, '[') !== FALSE) {
            $path = explode('[', str_replace(']', '', $field));
            $fld = isset($_POST[$path[0]]) ? $_POST[$path[0]] : FALSE;
            if ($fld === FALSE) { return FALSE; }
            if (count($path) > 1) {
                for ($i=1;$i<count($path);$i++) {
                    $segment = $path[$i];
                    if (!isset($fld[$segment])) {
                        return FALSE;
                    }
                    $fld = $fld[$segment];
                }
            }
            if ($str == $fld) { return TRUE; }
            return FALSE;
        } else {
            return parent::matches($str, $field);
        }
    }
    
    /**
     * Optional version of min_length().
     * @param string $str string to evauluate, it must be empty or it must have at least $length characters.
     * @param integer $length minimum number of characters in string $str.
     * @return boolean TRUE, if conditions are satisfied, FALSE otherwise.
     */
    public function min_length_optional($str, $length) {
        if (empty($str)) { return TRUE; }
        
        return $this->min_length($str, $length);
    }
    
    /**
     * Optional version of max_length().
     * @param string $str string to evauluate, it must be empty or it must not have more than $length characters.
     * @param integer $length maximum number of characters in string $str.
     * @return boolean TRUE, if conditions are satisfied, FALSE otherwise.
     */
    public function max_length_optional($str, $length) {
        if (empty($str)) { return TRUE; }
        
        return $this->max_length($str, $length);
    }
    
    public function exists_in_table($str, $table) {
        $table_def = explode('.', $table);
        $CI =& get_instance();
        if (count($table_def) == 2) {
            return $CI->db->from($table_def[0])->where($table_def[1], $str)->count_all_results() >= 1;
        } else if (count($table_def) == 3) {
            return $CI->db->from($table_def[0])->where($table_def[1], $str)->count_all_results() >= intval($table_def[2]);
        } else if (count($table_def) == 4) {
            $count = $CI->db->from($table_def[0])->where($table_def[1], $str)->count_all_results();
            return $count >= intval($table_def[2]) && $count <= intval($table_def[3]);
        }
        return FALSE;
    }
}