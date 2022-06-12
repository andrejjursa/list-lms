<?php

namespace Application\Services\AMQP\Consumers;

use Application\Services\AMQP\AbstractConsumer;
use Application\Services\AMQP\Messages\TestMessage;
use PhpAmqpLib\Message\AMQPMessage;

class TestConsumer extends AbstractConsumer
{
    protected const CONSUMER_TAG = 'test_consumer';
    
    public function processMessage(AMQPMessage $message): void
    {
        $listMessage = $this->getMessageReconstruction()->reconstructMessage($message);
        
        print_r($listMessage);
        
        if ($listMessage instanceof TestMessage) {
            $message->ack();
        }
    }
}