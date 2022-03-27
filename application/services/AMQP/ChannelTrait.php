<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;

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
        
        $channel->queue_bind($this->queue->getName(), $this->exchange->getExchange());
        
        return $channel;
    }
}