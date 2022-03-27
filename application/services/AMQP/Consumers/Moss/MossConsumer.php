<?php

namespace Application\Services\AMQP\Consumers\Moss;

use Application\Services\AMQP\AbstractConsumer;
use Application\Services\AMQP\Messages\Moss\StartComparisonMessage;
use PhpAmqpLib\Message\AMQPMessage;

class MossConsumer extends AbstractConsumer
{
    protected const CONSUMER_TAG = 'moss_consumer';
    
    public function processMessage(AMQPMessage $message): void
    {
        $applicationMessage = $this->getMessageReconstruction()->reconstructMessage($message);
        
        if ($applicationMessage instanceof StartComparisonMessage) {
            $message->ack();
            return;
        }
        
        $message->nack(false);
    }
}