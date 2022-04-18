<?php

namespace Application\Services\AMQP\Queues\Moss;

use Application\Services\AMQP\QueueInterface;
use PhpAmqpLib\Wire\AMQPTable;

class ComparisonQueue implements QueueInterface
{
    
    public function getName(): string
    {
        return 'moss_comparison';
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
    
    public function getArguments()
    {
        return [];
    }
    
    public function getTicket(): ?int
    {
        return null;
    }
    
    public function getRoutingKey(): string
    {
        return 'moss_comparison';
    }
}