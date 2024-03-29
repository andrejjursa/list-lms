<?php

/**
 * This class adds possibility to use UNION [ALL] sql clausule.
 *
 * @package LIST_DMZ_Extensions
 * @author  Andrej Jursa
 */

class DMZ_Unions
{
    
    public function union(
        $object,
        $next_object,
        $all = false,
        $order = '',
        $limit = null,
        $offset = null,
        $group_by = null
    ) {
        if (!is_array($next_object)) {
            $super_select = '(' . $object->get_sql() . ') UNION'
                . ($all === true ? ' ALL' : '') . ' (' . $next_object->get_sql() . ')';
        } else {
            $partials = [];
            foreach ($next_object as $n_object) {
                $partials[] = '(' . $n_object->get_sql() . ')';
            }
            $super_select = '(' . $object->get_sql() . ') UNION' . ($all === true ? ' ALL' : '') . ' '
                . implode(' UNION' . ($all === true ? ' ALL' : '') . ' ', $partials);
        }
        if (!is_null($group_by)) {
            $super_select = 'SELECT `table_grouped_by_' . $group_by . '`.* FROM (' . $super_select
                . ') `table_grouped_by_' . $group_by . '` GROUP BY `table_grouped_by_' . $group_by
                . '`.' . $object->db->protect_identifiers($group_by);
        }
        if ($order != '') {
            $super_select .= ' ORDER BY ' . $order;
        }
        if (!is_null($limit) && is_int($limit)) {
            $super_select .= ' LIMIT ' . $limit;
            if (!is_null($offset) && is_int($offset)) {
                $super_select .= ' OFFSET ' . $offset;
            }
        }
        $object->query($this->fixQuery($super_select));
    }
    
    public function union_iterated(
        $object,
        $next_object,
        $all = false,
        $order = '',
        $limit = null,
        $offset = null,
        $group_by = null
    ) {
        if (!is_array($next_object)) {
            $super_select = '(' . $object->get_sql() . ') UNION'
                . ($all === true ? ' ALL' : '') . ' (' . $next_object->get_sql() . ')';
        } else {
            $partials = [];
            foreach ($next_object as $n_object) {
                $partials[] = '(' . $n_object->get_sql() . ')';
            }
            $super_select = '(' . $object->get_sql() . ') UNION' . ($all === true ? ' ALL' : '') . ' '
                . implode(' UNION' . ($all === true ? ' ALL' : '') . ' ', $partials);
        }
        if (!is_null($group_by)) {
            $super_select = 'SELECT `table_grouped_by_' . $group_by . '`.* FROM (' . $super_select
                . ') `table_grouped_by_' . $group_by . '` GROUP BY `table_grouped_by_' . $group_by
                . '`.' . $object->db->protect_identifiers($group_by);
        }
        if ($order != '') {
            $super_select .= ' ORDER BY ' . $order;
        }
        if (!is_null($limit) && is_int($limit)) {
            $super_select .= ' LIMIT ' . $limit;
            if (!is_null($offset) && is_int($offset)) {
                $super_select .= ' OFFSET ' . $offset;
            }
        }
        $CI =& get_instance();
        $query = $CI->db->query($this->fixQuery($super_select));
        $object->_dm_dataset_iterator = new DM_DatasetIterator($object, $query);
    }
    
    public function union_order_by_constant(
        $object,
        $column,
        $direction = 'asc',
        $lang_idiom = null,
        $constant_prefix = 'user_custom_'
    ) {
        $CI =& get_instance();
        if (is_null($lang_idiom)) {
            $lang_idiom = $CI->lang->get_current_idiom();
        }
        
        $subquery = '(SELECT `text` AS `sorting_text`
FROM `translations`
WHERE `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND CONCAT("lang:", "'
            . $object->db->escape_str($constant_prefix) . '", `constant`) = '
            . $object->db->protect_identifiers($object->db->escape_str($column)) . '
UNION
SELECT ' . $object->db->protect_identifiers($object->db->escape_str($column))
            . ' AS `sorting_text` LIMIT 1) ' . (strtolower($direction) === 'asc' ? 'ASC' : 'DESC');
        
        return $subquery;
    }
    
    public function union_order_by_overlay(
        $object,
        $column,
        $target_table,
        $target_column,
        $target_table_id_field,
        $direction = 'asc',
        $lang_idiom = null
    ) {
        $CI =& get_instance();
        if (is_null($lang_idiom)) {
            $lang_idiom = $CI->lang->get_current_idiom();
        }
        
        $subquery = '(SELECT `text` AS `sorting_text`
FROM `lang_overlays`
WHERE `table` = "' . $object->db->escape_str($target_table) . '" AND `column` = "'
            . $object->db->escape_str($target_column) . '" AND `table_id` = '
            . $object->db->protect_identifiers($target_table_id_field)
            . ' AND `idiom` = "' . $object->db->escape_str($lang_idiom) . '" AND `text` != ""
UNION
SELECT ' . $object->db->protect_identifiers($column) . ' AS `sorting_text`
LIMIT 1) ' . (strtolower($direction) === 'asc' ? 'ASC' : 'DESC');
        
        return $subquery;
    }
    
    private function fixQuery($query)
    {
        $changed = preg_replace(
            '/FROM[ \n\r\t]+\([ \n\r\t]*(`[a-z0-9\_]+`([ \n\r\t]*\,[ \n\r\t]*`[a-z0-9\_]+`)*)[ \n\r\t]*\)/im',
            'FROM ${1}',
            $query
        );
        return $changed;
    }
    
}