<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Security model.
 *
 * @property int    $id
 * @property string $updated           date time format YYYY-MM-DD HH:MM:SS
 * @property string $created           date time format YYYY-MM-DD HH:MM:SS
 * @property string $account_type
 * @property string $account_email
 * @property string $login_ip_address
 * @property string $login_browser
 * @property string $login_failed_time date time format YYYY-MM-DD HH:MM:SS
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Security extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $table = 'security';
    
}