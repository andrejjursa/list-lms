<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Solution_version model.
 *
 * @property int         $id
 * @property string      $updated     date time format YYYY-MM-DD HH:MM:SS
 * @property string      $created     date time format YYYY-MM-DD HH:MM:SS
 * @property int|null    $solution_id entity id of model {@see Solution}
 * @property int         $version
 * @property bool        $download_lock
 * @property string      $ip_address
 * @property string|null $comment
 * @property Solution    $solution
 *
 * @method DataMapper where_related_solution(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Solution_version extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'solution',
    ];
    
}