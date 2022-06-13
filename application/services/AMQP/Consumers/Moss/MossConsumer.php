<?php

namespace Application\Services\AMQP\Consumers\Moss;

use Application\Services\AMQP\AbstractConsumer;
use Application\Services\AMQP\Connection;
use Application\Services\AMQP\ExchangeInterface;
use Application\Services\AMQP\Factory\PublisherFactory;
use Application\Services\AMQP\Messages\Moss\StartComparisonMessage;
use Application\Services\AMQP\QueueInterface;
use Application\Services\Moss\Service\MossExecutionService;
use CI_Controller;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Lock\LockFactory;

class MossConsumer extends AbstractConsumer
{
    protected const CONSUMER_TAG = 'moss_consumer';
    
    protected const NO_MOSS_ID_MESSAGE_DELAY = 60000; // milliseconds
    protected const NO_LOCK_ACQUIRED_MESSAGE_DELAY = 10000; // milliseconds
    
    /**
     * @var MossExecutionService
     */
    protected $mossExecutionService;
    
    /**
     * @var PublisherFactory
     */
    protected $publisherFactory;
    
    /** @var LockFactory */
    protected $lockFactory;
    
    /**
     * @var CI_Controller
     */
    protected $CI;
    
    public function __construct(
        Connection $connection,
        QueueInterface $queue,
        ExchangeInterface $exchange,
        MossExecutionService $mossExecutionService,
        PublisherFactory $publisherFactory,
        LockFactory $lockFactory
    ) {
        parent::__construct($connection, $queue, $exchange);
        $this->mossExecutionService = $mossExecutionService;
        $this->publisherFactory = $publisherFactory;
        $this->lockFactory = $lockFactory;
        $this->CI =& get_instance();
        $this->CI->config->load('moss');
    }
    
    
    public function processMessage(AMQPMessage $message): void
    {
        $applicationMessage = $this->getMessageReconstruction()->reconstructMessage($message);
        
        if (!($applicationMessage instanceof StartComparisonMessage)) {
            $message->nack(false);
            return;
        }
        
        if (!$this->isMossUserIdSet()) {
            $publisher = $this->publisherFactory->getComparisonQueuePublisher();
            $publisher->publishMessageWithDelay($applicationMessage, self::NO_MOSS_ID_MESSAGE_DELAY);
            $message->nack(false);
            return;
        }
    
        $lock = $this->lockFactory->createLock(
            sprintf(
                'moss_comparison_id_%d_lock',
                $applicationMessage->getParallelMossComparisonID()
            )
        );
        
        if (!$lock->acquire()) {
            $publisher = $this->publisherFactory->getComparisonQueuePublisher();
            $publisher->publishMessageWithDelay($applicationMessage, self::NO_LOCK_ACQUIRED_MESSAGE_DELAY);
            $message->nack(false);
            return;
        }
    
        try {
            $this->mossExecutionService->execute($applicationMessage);
            $message->ack();
        } catch (\Throwable $exception) {
            $message->nack(false);
        } finally {
            $lock->release();
        }
    }
    
    private function isMossUserIdSet(): bool
    {
        $this->CI->load->config('moss');
        return preg_match(
            '/^\d+$/',
            $this->CI->config->item('moss_user_id')
        ) && (int)$this->CI->config->item('moss_user_id') > 0;
    }
}