<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    
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
    
}