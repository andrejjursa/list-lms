<?php

/**
 * This extended CI_Form_validation library provides some new and better validation functions.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 */
class LIST_Form_validation extends CI_Form_validation
{
    
    /**
     * Validates entry string or array if it is empty (without html tags).
     *
     * @param string|array $str string to evaluate.
     *
     * @return boolean validation result.
     */
    public function required_no_html($str)
    {
        if (!is_array($str)) {
            $striped = preg_replace(
                '/[\x00-\x1F\x80-\xFF]/',
                '',
                str_replace('&nbsp;', ' ', html_entity_decode(strip_tags($str)))
            );
            return (trim($striped) == '') ? false : true;
        }
        
        return $this->required($str);
    }
    
    /**
     * Better version of standard matches method, this one will check if field is array and find appropriate value of
     * this field.
     *
     * @param string $str   current value of form field.
     * @param string $field form field name, can be array.
     *
     * @return boolean validation result.
     */
    public function matches($str, $field)
    {
        if (strpos($field, '[') !== false) {
            $fld = $this->get_post_array_value($field);
            if ($fld === null) {
                return false;
            }
            if ($str == $fld) {
                return true;
            }
            return false;
        }
        
        return parent::matches($str, $field);
    }
    
    /**
     * Optional version of min_length().
     *
     * @param string  $str    string to evauluate, it must be empty or it must have at least $length characters.
     * @param integer $length minimum number of characters in string $str.
     *
     * @return boolean TRUE, if conditions are satisfied, FALSE otherwise.
     */
    public function min_length_optional($str, $length)
    {
        if (empty($str)) {
            return true;
        }
        
        return $this->min_length($str, $length);
    }
    
    /**
     * Optional version of max_length().
     *
     * @param string  $str    string to evauluate, it must be empty or it must not have more than $length characters.
     * @param integer $length maximum number of characters in string $str.
     *
     * @return boolean TRUE, if conditions are satisfied, FALSE otherwise.
     */
    public function max_length_optional($str, $length)
    {
        if (empty($str)) {
            return true;
        }
        
        return $this->max_length($str, $length);
    }
    
    /**
     * Tests if string value exists in database table.
     *
     * @param string $str   input string to evaluate.
     * @param string $table comma separated definition of table (first part), column (second part), least occurrence
     *                      (third part) and most often occurrence (fourth part). Start table with question mark for
     *                      test if value is set to int (if not, test is true).
     *
     * @return boolean TRUE, if condition is satisfied, FALSE othewise.
     */
    public function exists_in_table($str, $table)
    {
        if (mb_strpos($table, '?') === 0) {
            if (!is_int($str) || (int)$str <= 0) {
                return true;
            }
            $table = mb_substr($table, 1);
        }
        $table_def = explode('.', $table);
        $CI =& get_instance();
        if (count($table_def) === 2) {
            return $CI->db->from($table_def[0])->where($table_def[1], $str)->count_all_results() >= 1;
        }
        
        if (count($table_def) === 3) {
            return $CI->db->from($table_def[0])->where($table_def[1], $str)->count_all_results() >= (int)$table_def[2];
        }
        
        if (count($table_def) === 4) {
            $count = $CI->db->from($table_def[0])->where($table_def[1], $str)->count_all_results();
            return $count >= (int)$table_def[2] && $count <= (int)$table_def[3];
        }
        
        return false;
    }
    
    /**
     * Evaluate text if contains numeric value with floating point.
     *
     * @param string $str string to evaluate.
     *
     * @return boolean TRUE, if string is floating point value, FALSE otherwise.
     */
    public function floatpoint($str)
    {
        if ($str == '') {
            return true;
        }
        $pattern = '/^-{0,1}(0|[1-9]{1}[0-9]*)(\.[0-9]+){0,1}$/';
        if (preg_match($pattern, $str)) {
            return true;
        }
        return false;
    }
    
    /**
     * Test if string is number and is greater or equal to given minimum.
     *
     * @param string $str string to evaluate.
     * @param double $min minimum value.
     *
     * @return boolean TRUE on success.
     */
    public function greater_than_equal($str, $min)
    {
        if (!is_numeric($str)) {
            return false;
        }
        return $str >= $min;
    }
    
    /**
     * Test if string is number and is less or equal to given maximum.
     *
     * @param string $str string to evaluate.
     * @param double $max maximum value.
     *
     * @return boolean TRUE on success.
     */
    public function less_than_equal($str, $max)
    {
        if (!is_numeric($str)) {
            return false;
        }
        return $str <= $max;
    }
    
    /**
     * Test if string is number and is less or equal to given field.
     *
     * @param string $str   string to evaluate.
     * @param string $field POST field as written in html input element name attribute.
     *
     * @return boolean TRUE on success.
     */
    public function less_than_field_or_equal($str, $field)
    {
        if (!is_numeric($str)) {
            return false;
        }
        $max = $this->_reduce_array($_POST, $this->get_keys($field));
        return $str <= $max;
    }
    
    /**
     * Test if string is number and is greater or equal to given field.
     *
     * @param string $str   string to evaluate.
     * @param string $field POST field as written in html input element name attribute.
     *
     * @return boolean TRUE on success.
     */
    public function greater_than_field_or_equal($str, $field)
    {
        if (!is_numeric($str)) {
            return false;
        }
        $max = $this->_reduce_array($_POST, $this->get_keys($field));
        return $str >= $max;
    }
    
    /**
     * Special case of email address check, can be zero or more valid e-mail adresses.
     *
     * @param string $str string to evaluate.
     *
     * @return boolean TRUE if empty or contains only comma separated list of email addresses.
     */
    public function zero_or_more_valid_emails($str)
    {
        if (trim($str) == '') {
            return true;
        }
        
        return $this->valid_emails($str);
    }
    
    /**
     * Test if string is valid date-time string.
     *
     * @param string $str string to evaluate.
     *
     * @return boolean TRUE if condition is satisfied.
     */
    public function datetime($str)
    {
        if ($str == '') {
            return true;
        }
        $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/';
        return (bool)preg_match($pattern, $str);
    }
    
    public function prep_for_form($data = '')
    {
        return $data;
    }
    
    
    /**
     * Returns keys from field.
     *
     * @param string $field POST field as written in html input element name attribute.
     *
     * @return array<string> array of keys to the $_POST superglobal.
     */
    private function get_keys($field)
    {
        if (strpos($field, '[') !== false and preg_match_all('/\[(.*?)\]/', $field, $matches)) {
            // Note: Due to a bug in current() that affects some versions
            // of PHP we can not pass function call directly into it
            $x = explode('[', $field);
            $indexes[] = current($x);
            
            for ($i = 0, $iMax = count($matches['0']); $i < $iMax; $i++) {
                if ($matches['1'][$i] != '') {
                    $indexes[] = $matches['1'][$i];
                }
            }
            return $indexes;
        }
        
        return [$field];
    }
    
    /**
     * Returns value from post array.
     *
     * @param $array_config configuration of array keys.
     *
     * @return NULL if not found, mixed otherwise.
     */
    private function get_post_array_value($array_config)
    {
        $path = explode('[', str_replace(']', '', $array_config));
        $fld = isset($_POST[$path[0]]) ? $_POST[$path[0]] : null;
        if ($fld === null) {
            return null;
        }
        if (count($path) > 1) {
            for ($i = 1, $iMax = count($path); $i < $iMax; $i++) {
                $segment = $path[$i];
                if (!isset($fld[$segment])) {
                    return null;
                }
                $fld = $fld[$segment];
            }
        }
        return $fld;
    }
}