<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Wire\AMQPTable;

interface MessageInterface
{
    public function getBody(): string;
    
    public function getContentType(): string;
    
    public function getContentEncoding(): ?string;
    
    /** @return null|array|AMQPTable */
    public function getApplicationHeaders();
    
    public function getDeliveryMode(): int;
    
    public function getPriority(): ?int;
    
    public function __construct();
}