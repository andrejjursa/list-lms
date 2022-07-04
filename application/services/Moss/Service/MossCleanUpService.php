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
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockExpiredException;
use Symfony\Component\Lock\LockFactory;
use Throwable;

class MossCleanUpService
{
    private const RESPONSE_HTTP_NOT_FOUND = 404;
    private const LOCK_TIMEOUT = 1800;
    
    /** @var ClientInterface */
    private $client;
    
    /** @var LockFactory */
    private $lockFactory;
    
    /** @var CI_DB_active_record|DataMapper */
    private $db;
    
    public function __construct(ClientInterface $client, LockFactory $lockFactory)
    {
        $this->client = $client;
        $CI =& get_instance();
        $CI->load->database();
        $this->db = $CI->db;
        $this->lockFactory = $lockFactory;
    }
    
    /**
     * @return array{errors: array<array{id: int, reason: string}>, deleted: int[]}
     * @throws LockAcquiringException
     * @throws LockExpiredException
     * @throws Throwable
     */
    public function cleanUpComparisons(): array
    {
        $lock = $this->lockFactory->createLock('mossCleanUpComparisonsLock', self::LOCK_TIMEOUT);
        
        if (!$lock->acquire()) {
            throw new LockAcquiringException('Can\'t obtain lock.');
        }
        
        try {
            $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $this->db->trans_begin();
        } catch (Throwable $ex) {
            $lock->release();
            throw $ex;
        }
    
        try {
            $comparisons = new Parallel_moss_comparison();
            $comparisons->limit(1000);
            $comparisons->get_iterated();
    
            $output = [
                'errors'  => [],
                'deleted' => [],
            ];
    
            $currentTime = new DateTimeImmutable();
            $deleteIds = [];
    
            /** @var Parallel_moss_comparison $comparison */
            foreach ($comparisons as $comparison) {
                if ($lock->isExpired()) {
                    throw new LockExpiredException('Lock expired in the process.');
                }
                $lock->refresh();
                try {
                    $finishDateTime = new DateTimeImmutable($comparison->processing_finish);
                    $diffFinish = $finishDateTime->diff($currentTime);
                    $startDateTime = new DateTimeImmutable($comparison->processing_start);
                    $diffStart = $startDateTime->diff($currentTime);
                } catch (Exception $e) {
                    $output['errors'][] = [
                        'id'     => $comparison->id,
                        'reason' => $e->getMessage(),
                    ];
                    continue;
                }
                if (in_array(
                    $comparison->status,
                    [Parallel_moss_comparison::STATUS_PROCESSING, Parallel_moss_comparison::STATUS_RESTART],
                    true
                    ) && $diffStart->days >= 1
                ) {
                    $comparison->status = Parallel_moss_comparison::STATUS_FAILED;
                    $comparison->failure_message = 'Force stop of crashed comparison job.';
                    $comparison->processing_finish = $currentTime->format('Y-m-d H:i:s');
                    $comparison->save();
                    $output['errors'][] = [
                        'id'     => $comparison->id,
                        'reason' => $comparison->failure_message,
                    ];
                    continue;
                }
                if ($diffFinish->days < 2) {
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
                            'id'     => $comparison->id,
                            'reason' => $e->getMessage(),
                        ];
                    }
                } else if ($comparison->status === Parallel_moss_comparison::STATUS_FAILED) {
                    $deleteIds[] = $comparison->id;
                    $output['deleted'][] = $comparison->id;
                }
            }
    
            if ($lock->isExpired()) {
                throw new LockExpiredException('Lock expired before comparisons deletion.');
            }
            $lock->refresh();
    
            if (count($deleteIds) > 0) {
                $comparisons = new Parallel_moss_comparison();
                $comparisons->where_in('id', $deleteIds);
                $comparisons->get();
                $comparisons->delete_all();
            }
    
            $this->db->trans_commit();
    
            return $output;
        } catch (Throwable $ex) {
            $this->db->trans_rollback();
            throw new \RuntimeException('Process failed!', 0, $ex);
        } finally {
            $lock->release();
        }
    }
}