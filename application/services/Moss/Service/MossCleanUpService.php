<?php

namespace Application\Services\Moss\Service;

use CI_DB_active_record;
use DataMapper;
use DateTimeImmutable;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Parallel_moss_comparison;

class MossCleanUpService
{
    private const RESPONSE_HTTP_NOT_FOUND = 404;
    
    /** @var ClientInterface */
    private $client;
    
    /** @var CI_DB_active_record|DataMapper */
    private $db;
    
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $CI =& get_instance();
        $CI->load->database();
        $this->db = $CI->db;
    }
    
    /**
     * @return array{errors: array<array{id: int, reason: string}>, deleted: int[]}
     */
    public function cleanUpComparisons(): array
    {
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_begin();
        
        $comparisons = new Parallel_moss_comparison();
        $comparisons->get_iterated();
        
        $output = [
            'errors' => [],
            'deleted' => [],
        ];
    
        $currentTime = new DateTimeImmutable();
        $deleteIds = [];
    
        /** @var Parallel_moss_comparison $comparison */
        foreach ($comparisons as $comparison) {
            try {
                $finishDateTime = new DateTimeImmutable($comparison->processing_finish);
                $diff = $finishDateTime->diff($currentTime);
                $startDateTime = new DateTimeImmutable($comparison->processing_start);
                $diffStart = $startDateTime->diff($currentTime);
            } catch (Exception $e) {
                $output['errors'][] = [
                    'id' => $comparison->id,
                    'reason' => $e->getMessage(),
                ];
                continue;
            }
            if ($comparison->status === Parallel_moss_comparison::STATUS_PROCESSING && $diffStart->days >= 1) {
                $comparison->status = Parallel_moss_comparison::STATUS_FAILED;
                $comparison->failure_message = 'Force stop of crashed comparison job.';
                $comparison->processing_finish = $currentTime->format('Y-m-d H:i:s');
                $comparison->save();
                $output['errors'][] = [
                    'id' => $comparison->id,
                    'reason' => $comparison->failure_message,
                ];
                continue;
            }
            if ($diff->days < 2) {
                continue;
            }
            if ($comparison->status === Parallel_moss_comparison::STATUS_FINISHED) {
                if ($comparison->result_link === null || trim($comparison->result_link) === '') {
                    $deleteIds[] = $comparison->id;
                    $output['deleted'][] = $comparison->id;
                    continue;
                }
                $request = new Request('HEAD', $comparison->result_link);
                try {
                    $response = $this->client->send($request);
                } catch (GuzzleException $e) {
                    if ($e->getResponse()->getStatusCode() === self::RESPONSE_HTTP_NOT_FOUND) {
                        $deleteIds[] = $comparison->id;
                        $output['deleted'][] = $comparison->id;
                        continue;
                    }
                    $output['errors'][] = [
                        'id' => $comparison->id,
                        'reason' => $e->getMessage(),
                    ];
                }
            } elseif ($comparison->status === Parallel_moss_comparison::STATUS_FAILED) {
                $deleteIds[] = $comparison->id;
                $output['deleted'][] = $comparison->id;
            }
        }
        
        if (count($deleteIds) > 0) {
            $comparisons = new Parallel_moss_comparison();
            $comparisons->where_in('id', $deleteIds);
            $comparisons->get();
            $comparisons->delete_all();
        }
    
        $this->db->trans_commit();
        
        return $output;
    }
}