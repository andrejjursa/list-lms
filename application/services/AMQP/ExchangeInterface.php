<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;

interface ExchangeInterface
{
    public function getExchange(): string;
    
    /**
     * @return string {@see AMQPExchangeType}
     */
    public function getType(): string;
    
    public function isPassive(): bool;
    
    public function isDurable(): bool;
    
    public function isAutoDelete(): bool;
    
    public function isInternal(): bool;
    
    public function isNoWait(): bool;
    
    /** @return array|AMQPTable */
    public function getArguments();
    
    public function getTicket(): ?int;
}