<?php

/**
 * This class adds join helper methods for datamapper.
 * 
 * @author Andrej Jursa
 * @package LIST_DMZ_Extensions
 */

class DMZ_Joins {
    
    /**
     * Adds custom condition to last build join clause. You can use question mark as wildcard character for value pased by $replace array.
     * @param DataMapper $object model object which will be using this function.
     * @param string $condition SQL condition added to last join clause.
     * @param array<mixed> $replace array of values replaced for corresponding question marks (by position in condition string).
     * @return DataMapper returns object for method chaining.
     */
    public function add_join_condition($object, $condition, $replace = array()) {
        if (count($object->db->ar_join) > 0 && trim($condition) != '') {
            $build_condition = $condition;
            if (is_array($replace) && count($replace) > 0) {
                foreach ($replace as $data) {
                    $build_condition = $this->find_and_replace_position($build_condition, $data, $object);
                }
            }
            $object->db->ar_join[count($object->db->ar_join) - 1] .= ' AND ' . $build_condition;
        } 
        
        return $object;
    }
    
    /**
     * Replaces first found question mark from the left by $replace value.
     * @param string $query SQL query to operate at.
     * @param mixed $replace value which will be replaced into position of first found question mark.
     * @param DataMapper $object model object.
     * @return string altered SQL query.
     */
    private function find_and_replace_position($query, $replace, $object) {
        $quote = FALSE;
        $quote_type = '';
        $last_char = '';
        $char = '';
        for ($pos = 0; $pos < mb_strlen($query); $pos++) {
            $last_char = $char;
            $char = mb_substr($query, $pos, 1);
            if ($char == '?' && $quote == FALSE) {
                $tmp_query = $query;
                $query = mb_substr($tmp_query, 0, $pos) . $object->db->escape($replace) . mb_substr($tmp_query, $pos + 1);
                return $query;
            } elseif (($char == '\'' || $char == '"') && $last_char != '\\') {
                if ($quote && $quote_type == $char) {
                    $quote = FALSE;
                } elseif (!$quote) {
                    $quote_type = $char;
                    $quote = TRUE;
                }
            }
        }
        
        return $query;
    }
    
}