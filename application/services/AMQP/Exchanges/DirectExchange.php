<?php

namespace Application\Services\AMQP\Exchanges;

use Application\Services\AMQP\ExchangeInterface;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class DirectExchange implements ExchangeInterface
{
    
    public function getExchange(): string
    {
        return 'amq.direct';
    }
    
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return AMQPExchangeType::DIRECT;
    }
    
    public function isPassive(): bool
    {
        return true;
    }
    
    public function isDurable(): bool
    {
        return true;
    }
    
    public function isAutoDelete(): bool
    {
        return false;
    }
    
    public function isInternal(): bool
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