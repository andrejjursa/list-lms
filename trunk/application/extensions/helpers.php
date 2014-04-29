<?php

/**
 * This class adds some helpful functions.
 * 
 * @author Andrej Jursa
 * @package LIST_DMZ_Extensions
 */

class DMZ_Helpers {
    
    public function change_last_having_operator($object, $to, $from = '=') {
        $havings = count($object->db->ar_having);
        if ($havings > 0) {
            $having = $object->db->ar_having[$havings - 1];
            $pos = mb_strrpos($having, ' ' . $from . ' ');
            if ($pos !== FALSE) {
                $before = mb_substr($having, 0, $pos);
                $after = mb_substr($having, $pos + mb_strlen(' ' . $from . ' ') + 1);
                $object->db->ar_having[$havings - 1] = $before . ' ' . $to . ' ' . $after;
            }
        }
    }
    
}