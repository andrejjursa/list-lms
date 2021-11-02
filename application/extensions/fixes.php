<?php

class DMZ_Fixes
{
    
    /**
     * This will fix the problematic order_by_subquery ignoring order direction.
     *
     * @param $object    object to apply at.
     * @param $subquery  a subquery.
     * @param $direction a direction (desc, asc, asc by default).
     */
    public function order_by_subquery_fixed($object, $subquery, $direction)
    {
        $object->order_by_subquery($subquery, $direction);
        
        $last_order_by = $object->db->ar_orderby[count($object->db->ar_orderby) - 1];
        
        $real_direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
        
        if (mb_stripos($last_order_by, 'ASC', -4) !== false) {
            $new_order_by = mb_substr($last_order_by, 0, mb_stripos($last_order_by, 'ASC', -4));
            $new_order_by .= $real_direction;
        }
        
        $object->db->ar_orderby[count($object->db->ar_orderby) - 1] = $new_order_by;
    }
    
}