<?php

namespace Application\Services\AMQP\Messages\Moss;

use Application\Services\AMQP\MessageInterface;
use PhpAmqpLib\Message\AMQPMessage;

class StartComparisonMessage implements MessageInterface
{
    /** @var int */
    private $parallelMossComparisonID;
    
    public function getBody(): string
    {
        return json_encode(
            [
                'parallelMossComparisonID' => $this->parallelMossComparisonID,
            ]
        );
    }
    
    public function getContentType(): string
    {
        return 'application/json';
    }
    
    public function getContentEncoding(): ?string
    {
        return null;
    }
    
    public function getApplicationHeaders()
    {
        return null;
    }
    
    public function getDeliveryMode(): int
    {
        return AMQPMessage::DELIVERY_MODE_NON_PERSISTENT;
    }
    
    public function getPriority(): ?int
    {
        return null;
    }
    
    public function __construct()
    {
    }
    
    /**
     * @return int
     */
    public function getParallelMossComparisonID(): int
    {
        return $this->parallelMossComparisonID;
    }
    
    /**
     * @param int $parallelMossComparisonID
     */
    public function setParallelMossComparisonID(int $parallelMossComparisonID): void
    {
        $this->parallelMossComparisonID = $parallelMossComparisonID;
    }
}