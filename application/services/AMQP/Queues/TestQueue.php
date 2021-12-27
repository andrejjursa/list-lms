<?php

namespace Application\Services\AMQP\Queues;

use PhpAmqpLib\Wire\AMQPTable;

class TestQueue implements \Application\Services\AMQP\QueueInterface
{
    
    public function getName(): string
    {
        return 'test';
    }
    
    public function isPassive(): bool
    {
        return false;
    }
    
    public function isDurable(): bool
    {
        return true;
    }
    
    public function isExclusive(): bool
    {
        return false;
    }
    
    public function isAutoDelete(): bool
    {
        return false;
    }
    
    public function isNoWait(): bool
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return [];
    }
    
    public function getTicket(): ?int
    {
        return null;
    }
}