<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Restriction model.
 *
 * @property int         $id
 * @property string      $updated     date time format YYYY-MM-DD HH:MM:SS
 * @property string      $created     date time format YYYY-MM-DD HH:MM:SS
 * @property string|null $ip_address
 * @property string      $start_time  date time format YYYY-MM-DD HH:MM:SS
 * @property string      $end_time    date time format YYYY-MM-DD HH:MM:SS
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Restriction extends DataMapper implements DataMapperExtensionsInterface
{
    
    public static function check_restriction_for_ip_address($ip_address = null): bool
    {
        $ci =& get_instance();
        $ci->load->helper('ip_address');
        $ip_to_check = $ip_address;
        if (is_null($ip_address)) {
            $ip_to_check = getenv('REMOTE_ADDR');
        }
        
        if (check_valid_ip_address($ip_to_check)) {
            $time = date('Y-m-d H:i:s');
            $restriction = new Restriction();
            $restriction->where('start_time <=', $time);
            $restriction->where('end_time >=', $time);
            $restriction->get_iterated();
            foreach ($restriction as $restriction_record) {
                $ip_addresses = explode(',', $restriction_record->ip_addresses);
                if (count($ip_addresses)) {
                    foreach ($ip_addresses as $matching_pattern) {
                        if (trim($matching_pattern) !== ''
                            && match_ip_address_agains(trim($matching_pattern), $ip_to_check)
                        ) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    
}