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
    
    protected const NO_MOSS_ID_MESSAGE_DELAY = 120000; // milliseconds
    protected const NO_LOCK_ACQUIRED_MESSAGE_DELAY = 20000; // milliseconds
    
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

    /**
     * append line to log /var/log/listmoss/listmoss.log
     *
     * @param string $msg
     * @example appendlog("contacting moss server...");
     */
    public function appendlog($msg): void
    {
        $logdir="/var/log/listmoss";
        $logfile="listmoss.log";
        if (!is_dir($logdir)) return;
        $logf=fopen($logdir . "/" . $logfile, "a+");
        if (!$logf) return;
        fwrite($logf, strftime("%F %T") . $msg . "\n");
        fclose($logf);
    }
    
    public function processMessage(AMQPMessage $message): void
    {
        $applicationMessage = $this->getMessageReconstruction()->reconstructMessage($message);
        $this->appendLog(" moss processMessage($message)...");
        
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
        $this->appendLog(" moss processMessage() to createlock...");
    
        $lock = $this->lockFactory->createLock(
            sprintf(
                'moss_comparison_id_%d_lock',
                $applicationMessage->getParallelMossComparisonID()
            )
        );
        
        $this->appendLog(" moss processMessage() to acquirelock...");

        if (!$lock->acquire()) {
            $publisher = $this->publisherFactory->getComparisonQueuePublisher();
            $publisher->publishMessageWithDelay($applicationMessage, self::NO_LOCK_ACQUIRED_MESSAGE_DELAY);
            $message->nack(false);
            return;
        }
    
        try {
            $this->appendLog(" moss processMessage() to execute...");
            $result = $this->mossExecutionService->execute($applicationMessage);
            $this->appendLog(" moss processMessage() execute result: '$result'.");

            $message->ack();

            if (!$result && $this->mossExecutionService->getStatus() === \Parallel_moss_comparison::STATUS_RESTART) {
                $timeout = MossExecutionService::RESTARTS_DELAYS[$this->mossExecutionService->getRestarts()] ?? 3600;
                $timeout *= 1000;
                $publisher = $this->publisherFactory->getComparisonQueuePublisher();
                if ($timeout > 0) {
                    $publisher->publishMessageWithDelay($applicationMessage, $timeout);
                } else {
                    $publisher->publishMessage($applicationMessage);
                }
            }
            if ((bool)$this->CI->config->item('moss_stop_on_message') === true) {
                $this->stopConsumer();
            }
        } catch (\Throwable $exception) {
            $this->appendLog(" moss processMessage() exception $exception\n");
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
