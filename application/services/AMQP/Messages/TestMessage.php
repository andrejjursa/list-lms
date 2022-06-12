<?php

namespace Application\Services\AMQP\Messages;

use Application\Services\AMQP\MessageInterface;
use PhpAmqpLib\Message\AMQPMessage;

class TestMessage implements MessageInterface
{
    /** @var string */
    private $message = '';
    
    public function __construct()
    {
    }
    
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    
    public function getBody(): string
    {
        return json_encode(['message' => $this->message]);
    }
    
    public function getContentType(): string
    {
        return 'application/json';
    }
    
    public function getContentEncoding(): ?string
    {
        return null;
    }
    
    /**
     * @inheritDoc
     */
    public function getApplicationHeaders()
    {
        return null;
    }
    
    public function getDeliveryMode(): int
    {
        return AMQPMessage::DELIVERY_MODE_PERSISTENT;
    }
    
    public function getPriority(): ?int
    {
        return null;
    }
}