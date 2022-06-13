<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

trait ChannelTrait
{
    /** @var Connection */
    protected $connection;
    
    /** @var QueueInterface */
    protected $queue;
    
    /** @var ExchangeInterface */
    protected $exchange;
    
    /**
     * @return AbstractChannel|AMQPChannel
     */
    protected function getChannel()
    {
        $channel = $this->connection->getConnection()->channel();
    
        $this->declareExchange($channel);
    
        $channel->queue_declare(
            $this->queue->getName(),
            $this->queue->isPassive(),
            $this->queue->isDurable(),
            $this->queue->isExclusive(),
            $this->queue->isAutoDelete(),
            $this->queue->isNoWait(),
            $this->queue->getArguments(),
            $this->queue->getTicket()
        );
        
        $channel->queue_bind(
            $this->queue->getName(),
            $this->exchange->getExchange(),
            $this->queue->getRoutingKey()
        );
        
        return $channel;
    }
    
    protected function getChannelForDelayQueue(int $milliseconds, string &$routingKey = '')
    {
        $channel = $this->connection->getConnection()->channel();
        
        $this->declareExchange($channel);
        
        $queueName = $this->queue->getName() . '.delay.' . $milliseconds;
        $routingKey = $queueName . '.rk';
        
        $arguments = new AMQPTable();
        $arguments->set('x-message-ttl', $milliseconds);
        $arguments->set('x-dead-letter-exchange', $this->exchange->getExchange());
        $arguments->set('x-dead-letter-routing-key', $this->queue->getRoutingKey());
        
        $channel->queue_declare(
            $queueName,
            false,
            true,
            false,
            false,
            false,
            $arguments,
            $this->queue->getTicket()
        );
        
        $channel->queue_bind(
            $queueName,
            $this->exchange->getExchange(),
            $routingKey
        );
        
        return $channel;
    }
    
    /**
     * @param $channel
     *
     * @return void
     */
    protected function declareExchange($channel): void
    {
        $channel->exchange_declare(
            $this->exchange->getExchange(),
            $this->exchange->getType(),
            $this->exchange->isPassive(),
            $this->exchange->isDurable(),
            $this->exchange->isAutoDelete(),
            $this->exchange->isInternal(),
            $this->exchange->isNoWait(),
            $this->exchange->getArguments(),
            $this->exchange->getTicket()
        );
    }
}