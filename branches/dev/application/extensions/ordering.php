<?php

/**
 * This class adds additional ordering methods for datamapper.
 * 
 * @author Andrej Jursa
 * @package LIST_DMZ_Extensions
 */

class DMZ_Ordering {
    
    /**
     * Handle column as fullname (first name <space> last name) and create order by clause for this column by last name.
     * @param DataMapper $object model object which will be using this function.
     * @param string $column column name in table.
     * @param string $direction sorting direction (asc | desc).
     * @return DataMapper returns object for method chaining.
     */
    public function order_by_as_fullname($object, $column, $direction = 'asc') {
        $object->db->ar_orderby[] = 'SUBSTRING_INDEX(`' . $column . '`, \' \', -1) ' . (strtolower($direction) == 'asc' ? 'ASC' : 'DESC');
                
        return $object;
    }
    
    /**
     * Add sorting of table deepest level in related table field.
     * @param DataMapper $object model object which will be using this function.
     * @param string $related slash separated list of deeply related models.
     * @param string $column column name in table.
     * @param string $direction sorting direction (asc | desc).
     * @return DataMapper returns object for method chaining.
     */
    public function order_by_related_as_fullname($object, $related, $column, $direction = 'asc') {
        $related_table = $object->_add_related_table($related, $object);
                
        $object->db->ar_orderby[] = 'SUBSTRING_INDEX(' .  $object->db->protect_identifiers($related_table). '.`' . $column . '`, \' \', -1) ' . (strtolower($direction) == 'asc' ? 'ASC' : 'DESC');
                
        return $object;
    }
    
}