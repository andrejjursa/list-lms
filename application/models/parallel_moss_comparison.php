<?php

use Application\Interfaces\DataMapperExtensionsInterface;
use Application\MetaClass\Database\PagedMetaClass;
use Application\ModelTraits\JSONFieldTrait;

/**
 * Parallel MOSS comparison model.
 *
 * @property int            $id
 * @property string         $updated           date time format YYYY-MM-DD HH:MM:SS
 * @property string         $created           date time format YYYY-MM-DD HH:MM:SS
 * @property string         $status
 * @property null|int       $teacher_id
 * @property null|Teacher   $teacher
 * @property array          $configuration
 * @property null|string    $processing_start  date time format YYYY-MM-DD HH:MM:SS
 * @property null|string    $processing_finish date time format YYYY-MM-DD HH:MM:SS
 * @property null|string    $result_link
 * @property PagedMetaClass $paged
 *
 * @method DataMapper where_related_teacher(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Parallel_moss_comparison extends DataMapper implements DataMapperExtensionsInterface
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_FAILED = 'failed';
    
    use JSONFieldTrait;
    
    public $has_one = [
        'teacher',
    ];
    
    public $validation = [
        'configuration' => [
            'label'     => 'configuration',
            'rules'     => ['jsonEncode'],
            'get_rules' => ['jsonDecode'],
        ],
    ];
}