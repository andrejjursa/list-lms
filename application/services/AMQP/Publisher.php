<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Publisher
{
    use ChannelTrait;
    
    /**
     * @param Connection        $connection
     * @param QueueInterface    $queue
     * @param ExchangeInterface $exchange
     */
    public function __construct(Connection $connection, QueueInterface $queue, ExchangeInterface $exchange)
    {
        $this->connection = $connection;
        $this->queue = $queue;
        $this->exchange = $exchange;
    }
    
    public function publishMessage(MessageInterface $message): void
    {
        $channel = $this->getChannel();
        
        $channel->basic_publish(
            $this->constructMessage($message),
            $this->exchange->getExchange(),
            $this->queue->getRoutingKey()
        );
    }
    
    public function publishMessageWithDelay(MessageInterface $message, int $milliseconds): void
    {
        $routingKey = '';
        $channel = $this->getChannelForDelayQueue($milliseconds, $routingKey);
        $channel->basic_publish(
            $this->constructMessage($message),
            $this->exchange->getExchange(),
            $routingKey
        );
    }
    
    protected function constructMessage(MessageInterface $message): AMQPMessage
    {
        $arguments = [
            'content_type'  => $message->getContentType(),
            'delivery_mode' => $message->getDeliveryMode(),
        ];
        
        $applicationHeaders = $message->getApplicationHeaders();
        
        if ($applicationHeaders === null || is_array($applicationHeaders)) {
            $applicationHeaders['x_message_class'] = get_class($message);
            $applicationHeaders = new AMQPTable($applicationHeaders);
        } else {
            /** @var AMQPTable $applicationHeaders */
            $applicationHeaders->set('x_message_class', get_class($message));
        }
        $arguments['application_headers'] = $applicationHeaders;
        
        if (($contentEncoding = $message->getContentEncoding()) !== null) {
            $arguments['content_encoding'] = $contentEncoding;
        }
        
        if (($priority = $message->getPriority()) !== null) {
            $arguments['priority'] = $priority;
        }
        
        return new AMQPMessage(
            $message->getBody(),
            $arguments
        );
    }
}