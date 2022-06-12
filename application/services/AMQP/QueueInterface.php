<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Wire\AMQPTable;

interface QueueInterface
{
    public function getName(): string;
    
    public function isPassive(): bool;
    
    public function isDurable(): bool;
    
    public function isExclusive(): bool;
    
    public function isAutoDelete(): bool;
    
    public function isNoWait(): bool;
    
    /** @return array|AMQPTable */
    public function getArguments();
    
    public function getTicket(): ?int;
    
    public function getRoutingKey(): string;
}