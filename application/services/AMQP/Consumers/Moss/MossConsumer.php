<?php

namespace Application\Services\AMQP\Consumers\Moss;

use Application\Services\AMQP\AbstractConsumer;
use Application\Services\AMQP\Connection;
use Application\Services\AMQP\ExchangeInterface;
use Application\Services\AMQP\Messages\Moss\StartComparisonMessage;
use Application\Services\AMQP\QueueInterface;
use Application\Services\Moss\Service\MossExecutionService;
use PhpAmqpLib\Message\AMQPMessage;

class MossConsumer extends AbstractConsumer
{
    protected const CONSUMER_TAG = 'moss_consumer';
    
    /**
     * @var MossExecutionService
     */
    protected $mossExecutionService;
    
    public function __construct(
        Connection $connection,
        QueueInterface $queue,
        ExchangeInterface $exchange,
        MossExecutionService $mossExecutionService
    ) {
        parent::__construct($connection, $queue, $exchange);
        $this->mossExecutionService = $mossExecutionService;
    }
    
    
    public function processMessage(AMQPMessage $message): void
    {
        $applicationMessage = $this->getMessageReconstruction()->reconstructMessage($message);
        
        if (!($applicationMessage instanceof StartComparisonMessage)) {
            $message->nack(false);
            return;
        }
        
        try {
            $this->mossExecutionService->execute($applicationMessage);
            $message->ack();
        } catch (\Throwable $exception) {
            $message->nack(false);
        }
    }
}