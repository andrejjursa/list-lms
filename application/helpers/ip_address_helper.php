<?php

/**
 * IP address helper functions.
 *
 * @package LIST_Helpers
 * @author  Andrej Jursa
 */


function match_ip_address_agains($matching_pattern, $ip_address = null): bool
{
    $ip_to_check = $ip_address;
    if (is_null($ip_address)) {
        $ip_to_check = getenv('REMOTE_ADDR');
    }
    
    if (check_valid_ip_address($ip_to_check)) {
        if (check_valid_ip_address($matching_pattern)) {
            if ($matching_pattern === $ip_to_check) {
                return true;
            }
        } else {
            $range = ip_address_wildcard_to_range_array($matching_pattern);
            if (is_null($range)) {
                $range = ip_address_range_to_array($matching_pattern);
            }
            if (!is_null($range)) {
                $ip_to_check_long = list_ip_2_number($ip_to_check);
                if (list_ip_2_number($range['bottom']) <= $ip_to_check_long
                    && $ip_to_check_long <= list_ip_2_number($range['top'])
                ) {
                    return true;
                }
            }
        }
    }
    
    return false;
}

function ip_address_wildcard_to_range_array($wildcard): ?array
{
    if (check_valid_ip_wildcard($wildcard)) {
        return [
            'bottom' => str_replace('*', 0, $wildcard),
            'top'    => str_replace('*', 255, $wildcard),
        ];
    }
    return null;
}

function ip_address_range_to_array($ip_range): ?array
{
    if (check_valid_ip_range($ip_range)) {
        $parts = explode(':', $ip_range);
        return [
            'bottom' => str_replace('*', 0, $parts[0]),
            'top'    => str_replace('*', 255, $parts[1]),
        ];
    }
    return null;
}

function check_valid_ip_address($ip_address): bool
{
    if (preg_match('/^[0-9\.]+$/', $ip_address)) {
        $parts = explode('.', $ip_address);
        if (count($parts) === 4) {
            foreach ($parts as $part) {
                if ($part === '' || $part > 255) {
                    return false;
                }
            }
            return true;
        }
    }
    return false;
}

function check_valid_ip_wildcard($ip_wildcard): ?bool
{
    if (preg_match('/^[0-9\.\*]+$/', $ip_wildcard)) {
        $parts = explode('.', $ip_wildcard);
        if (count($parts) === 4) {
            $wildcard_found = false;
            foreach ($parts as $part) {
                if ($part === '*') {
                    $wildcard_found = true;
                } else if (preg_match('/^\d{1,3}$/', $part)) {
                    if ($wildcard_found || $part > 255) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            return $wildcard_found;
        }
    }
    return false;
}

function check_valid_ip_range($ip_range): bool
{
    $parts = explode(':', $ip_range);
    return (count($parts) === 2) && check_valid_ip_address($parts[0]) && check_valid_ip_address($parts[1])
        && list_ip_2_number($parts[0]) <= list_ip_2_number($parts[1]);
}

function list_ip_2_number($ip_address): int
{
    if (check_valid_ip_address($ip_address)) {
        $number = 0;
        $parts = explode('.', $ip_address);
        for ($i = 3; $i >= 0; $i--) {
            $number += $parts[$i] * (255 ** (3 - $i));
        }
        return $number;
    }
    return -1;
}