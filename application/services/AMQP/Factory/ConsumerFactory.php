<?php

namespace Application\Services\AMQP\Factory;

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Consumers\TestConsumer;
use Application\Services\AMQP\Exchanges\DirectExchange;
use Application\Services\AMQP\Queues\TestQueue;

class ConsumerFactory
{
    /** @var Connection */
    protected $connection;
    
    /** @var DirectExchange */
    protected $directExchange;
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->directExchange = new DirectExchange();
    }
    
    public function getTestConsumer(): TestConsumer
    {
        return new TestConsumer($this->connection, new TestQueue(), $this->directExchange);
    }
}