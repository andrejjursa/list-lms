<?php

/**
 * This class adds posibility to set ordering of table for column using L.I.S.T. translations methods (constants and overlays).
 * 
 * @author Andrej Jursa
 * @package LIST_DMZ_Extensions
 */

class DMZ_Translations {
    
    /**
     * Add sorting of table on table level (not for deep relation), for column which is translated by language constant.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $direction sorting direction (asc | desc).
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @param string $constant_prefix prefix of user constant, default is 'user_custom_'.
     * @return DataMapper returns object for method chaining.
     */
    public function order_by_with_constant($object, $column, $direction = 'asc', $lang_idiom = NULL, $constant_prefix = 'user_custom_') {
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $subquery = '(SELECT `text` AS `sorting_text`
FROM `translations`
WHERE `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND CONCAT("lang:", "' . $object->db->escape_str($constant_prefix) . '", `constant`) = ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($object->db->escape_str($column)) . '
UNION
SELECT ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($object->db->escape_str($column)) . ' AS `sorting_text` LIMIT 1) ' . (strtolower($direction) == 'asc' ? 'ASC' : 'DESC');
        
        $object->db->ar_orderby[] = $subquery;
        return $object;
    }
    
    /**
     * Add sorting of table on table level (not for deep relation), for column which is translated by language overlay.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $direction sorting direction (asc | desc).
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @return DataMapper returns object for method chaining.
     */
    public function order_by_with_overlay($object, $column, $direction = 'asc', $lang_idiom = NULL) {
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $subquery = '(SELECT `text` AS `sorting_text`
FROM `lang_overlays`
WHERE `table` = "' . $object->db->escape_str($object->table) . '" AND `column` = "' . $object->db->escape_str($column) . '" AND `table_id` = ' . $object->db->protect_identifiers($object->table) . '.`id` AND `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND `text` != ""
UNION
SELECT ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . ' AS `sorting_text`
LIMIT 1)' . (strtolower($direction) == 'asc' ? 'ASC' : 'DESC');
        
        $object->db->ar_orderby[] = $subquery;
        return $object;
    }
    
    /**
     * Add sorting of table deepest level in related table field.
     * @param DataMapper $object model object which will be using this function.
     * @param string $related slash separated list of deeply related models.
     * @param string $column column name in table.
     * @param string $direction sorting direction (asc | desc).
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @param string $constant_prefix prefix of user constant, default is 'user_custom_'.
     * @return DataMapper returns object for method chaining.
     */
    public function order_by_related_with_constant($object, $related, $column, $direction = 'asc', $lang_idiom = NULL, $constant_prefix = 'user_custom_') {
        if (!is_string($related) || trim($related) == '') { return $this->order_by_with_constant($object, $column, $direction, $lang_idiom, $constant_prefix); }
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
                
        $related_table = $object->_add_related_table($related, $object);
        
        $subquery = '(SELECT `text` AS `sorting_text`
FROM `translations`
WHERE `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND CONCAT("lang:", "' . $object->db->escape_str($constant_prefix) . '", `constant`) = ' . $object->db->protect_identifiers($related_table) . '.' . $object->db->protect_identifiers($object->db->escape_str($column)) . '
UNION
SELECT ' . $object->db->protect_identifiers($related_table) . '.' . $object->db->protect_identifiers($object->db->escape_str($column)) . ' AS `sorting_text`
LIMIT 1) ' . (strtolower($direction) == 'asc' ? 'ASC' : 'DESC');
        
        $object->db->ar_orderby[] = $subquery;
        
        return $object;
    }
    
    /**
     * Add sorting of table deepest level in related table field.
     * @param DataMapper $object model object which will be using this function.
     * @param string $related slash separated list of deeply related models.
     * @param string $column column name in table.
     * @param string $direction sorting direction (asc | desc).
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @return DataMapper returns object for method chaining.
     */
    public function order_by_related_with_overlay($object, $related, $column, $direction = 'asc', $lang_idiom = NULL) {
        if (!is_string($related) || trim($related) == '') { return $this->order_by_with_overlay($object, $column, $direction, $lang_idiom); }
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $related_table = $object->_add_related_table($related, $object);
        
        $rfs = explode('/', $related);
        $final_relation_model = array_pop($rfs);
        $final_relation_model = strtoupper($final_relation_model[0]) . strtolower(substr($final_relation_model, 1));
        $final_model = new $final_relation_model();
        
        $subquery = '(SELECT `text` AS `sorting_text`
FROM `lang_overlays`
WHERE `table` = "' . $object->db->escape_str($final_model->table) . '" AND `column` = "' . $object->db->escape_str($column) . '" AND `table_id` = ' . $object->db->protect_identifiers($related_table) . '.`id` AND `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND `text` != ""
UNION
SELECT ' . $object->db->protect_identifiers($related_table) . '.' . $object->db->protect_identifiers($column) . ' AS `sorting_text`
LIMIT 1)' . (strtolower($direction) == 'asc' ? 'ASC' : 'DESC');
        
        $object->db->ar_orderby[] = $subquery;
        return $object;
    }
    
    /**
     * Add like condition to where clause for column which can contain language constant.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $value search text.
     * @param string $wrap wraping constant, can be 'both', 'before', 'after'.
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @param string $constant_prefix prefix of user constant, default is 'user_custom_'.
     * @return DataMapper returns object for method chaining.
     */
    public function like_with_constant($object, $column, $value, $wrap = 'both', $lang_idiom = NULL, $constant_prefix = 'user_custom_') {
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $like = $wrap == 'before' ? '%' . $object->db->escape_like_str($value) : ($wrap == 'after' ? $object->db->escape_like_str($value) . '%' : '%' . $object->db->escape_like_str($value) . '%');
        
        $subquery = '(SELECT `text` AS `like_text`
FROM `translations`
WHERE `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND CONCAT("lang:", "' . $object->db->escape_str($constant_prefix) . '", `constant`) = ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . '
UNION
SELECT ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . ' AS `like_text`
LIMIT 1) LIKE "' . $like . '"';
        
        $object->where($subquery);
        
        return $object;
    }
    
    /**
     * Add like condition to where clause, with OR operator, for column which can contain language constant.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $value search text.
     * @param string $wrap wraping constant, can be 'both', 'before', 'after'.
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @param string $constant_prefix prefix of user constant, default is 'user_custom_'.
     * @return DataMapper returns object for method chaining.
     */
    public function or_like_with_constant($object, $column, $value, $wrap = 'both', $lang_idiom = NULL, $constant_prefix = 'user_custom_') {
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $like = $wrap == 'before' ? '%' . $object->db->escape_like_str($value) : ($wrap == 'after' ? $object->db->escape_like_str($value) . '%' : '%' . $object->db->escape_like_str($value) . '%');
        
        $subquery = '(SELECT `text` AS `like_text`
FROM `translations`
WHERE `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND CONCAT("lang:", "' . $object->db->escape_str($constant_prefix) . '", `constant`) = ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . '
UNION
SELECT ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . ' AS `like_text`
LIMIT 1) LIKE "' . $like . '"';
        
        $object->or_where($subquery);
        
        return $object;
    }
    
    /**
     * Add like condition to where clause for column which can be translated with language overlay.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $value search text.
     * @param string $wrap wraping constant, can be 'both', 'before', 'after'.
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @return DataMapper returns object for method chaining.
     */
    public function like_with_overlay($object, $column, $value, $wrap = 'both', $lang_idiom = NULL) {
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $like = $wrap == 'before' ? '%' . $object->db->escape_like_str($value) : ($wrap == 'after' ? $object->db->escape_like_str($value) . '%' : '%' . $object->db->escape_like_str($value) . '%');
        
        $subquery = '(SELECT `text` AS `like_text`
FROM `lang_overlays`
WHERE `table` = "' . $object->db->escape_str($object->table) . '" AND `table_id` = ' . $object->db->protect_identifiers($object->table) . '.`id` AND `column` = "' . $object->db->escape_str($column) . '" AND `idiom` = "' . $object->db->escape_str($lang_idiom) . '"
UNION
SELECT ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . ' AS `like_text`
LIMIT 1) LIKE "' . $like . '"';
        
        $object->where($subquery);
        
        return $object;
    }
    
    /**
     * Add like condition to where clause, with OR operator, for column which can be translated with language overlay.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $value search text.
     * @param string $wrap wraping constant, can be 'both', 'before', 'after'.
     * @param string $lang_idiom language idiom, default is NULL = current language idion set in language object.
     * @return DataMapper returns object for method chaining.
     */
    public function or_like_with_overlay($object, $column, $value, $wrap = 'both', $lang_idiom = NULL) {
        $CI =& get_instance();
        if (is_null($lang_idiom)) { $lang_idiom = $CI->lang->get_current_idiom(); }
        
        $like = $wrap == 'before' ? '%' . $object->db->escape_like_str($value) : ($wrap == 'after' ? $object->db->escape_like_str($value) . '%' : '%' . $object->db->escape_like_str($value) . '%');
        
        $subquery = '(SELECT `text` AS `like_text`
FROM `lang_overlays`
WHERE `table` = "' . $object->db->escape_str($object->table) . '" AND `table_id` = ' . $object->db->protect_identifiers($object->table) . '.`id` AND `column` = "' . $object->db->escape_str($column) . '" AND `idiom` = "' . $object->db->escape_str($lang_idiom) . '"
UNION
SELECT ' . $object->db->protect_identifiers($object->table) . '.' . $object->db->protect_identifiers($column) . ' AS `like_text`
LIMIT 1) LIKE "' . $like . '"';
        
        $object->or_where($subquery);
        
        return $object;
    }
    
}

/* End of file translations.php */
/* Location: ./application/extensions/translations.php */