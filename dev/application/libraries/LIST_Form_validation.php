<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This extended CI_Form_validation library provides some new and better validation functions.
 * @package LIST_Libraries
 * @author Andrej Jursa
 */
class LIST_Form_validation extends CI_Form_validation {
    
    /**
     * Validates entry string or array if it is empty (without html tags).
     * @param string|array $str string to evaluate.
     * @return boolean validation result.
     */
    public function required_no_html($str) {
        if (!is_array($str)) {
            $striped = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', str_replace('&nbsp;', ' ', html_entity_decode(strip_tags($str))));
            return (trim($striped) == '') ? FALSE : TRUE;
        } else {
            return $this->required($str);
        }
    }

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
    
    /**
     * Tests if string value exists in database table.
     * @param string $str input string to evaluate.
     * @param string $table comma separated definition of table (first part), column (second part), least occurrence (third part) and most often occurrence (fourth part).
     * @return boolean TRUE, if condition is satisfied, FALSE othewise.
     */
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
    
    /**
     * Evaluate text if contains numeric value with floating point.
     * @param string $str string to evaluate.
     * @return boolean TRUE, if string is floating point value, FALSE otherwise.
     */
    public function floatpoint($str) {
        if ($str == '') { return TRUE; }
        $pattern = '/^-{0,1}(0|[1-9]{1}[0-9]*)(\.[0-9]+){0,1}$/';
        if (preg_match($pattern, $str)) {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Test if string is number and is greater or equal to given minimum.
     * @param string $str string to evaluate.
     * @param double $min minimum value.
     * @return boolean TRUE on success.
     */
    public function greater_than_equal($str, $min) {
        if (!is_numeric($str)) {
            return FALSE;
        }
        return $str >= $min;
    }
    
    /**
     * Test if string is number and is less or equal to given maximum.
     * @param string $str string to evaluate.
     * @param double $max maximum value.
     * @return boolean TRUE on success.
     */
    public function less_than_equal($str, $max) {
        if (!is_numeric($str)) {
            return FALSE;
        }
        return $str <= $max;
    }
    
    /**
     * Test if string is number and is less or equal to given field.
     * @param string $str string to evaluate.
     * @param string $field POST field as written in html input element name attribute.
     * @return boolean TRUE on success.
     */
    public function less_than_field_or_equal($str, $field) {
        if (!is_numeric($str)) {
            return FALSE;
        }
        $max = $this->_reduce_array($_POST, $this->get_keys($field));
        return $str <= $max;
    }
    
    /**
     * Test if string is number and is greater or equal to given field.
     * @param string $str string to evaluate.
     * @param string $field POST field as written in html input element name attribute.
     * @return boolean TRUE on success.
     */
    public function greater_than_field_or_equal($str, $field) {
        if (!is_numeric($str)) {
            return FALSE;
        }
        $max = $this->_reduce_array($_POST, $this->get_keys($field));
        return $str >= $max;
    }
    
    /**
     * Returns keys from field.
     * @param string $field POST field as written in html input element name attribute.
     * @return array<string> array of keys to the $_POST superglobal.
     */
    private function get_keys($field) {
        if (strpos($field, '[') !== FALSE AND preg_match_all('/\[(.*?)\]/', $field, $matches))
        {
            // Note: Due to a bug in current() that affects some versions
            // of PHP we can not pass function call directly into it
            $x = explode('[', $field);
            $indexes[] = current($x);

            for ($i = 0; $i < count($matches['0']); $i++)
            {
                if ($matches['1'][$i] != '')
                {
                    $indexes[] = $matches['1'][$i];
                }
            }
            return $indexes;
        } else {
            return array($field);
        }
    }
}