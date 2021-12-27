<?php

namespace Application\Services\AMQP\Factory;

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Exchanges\DirectExchange;
use Application\Services\AMQP\Publisher;
use Application\Services\AMQP\Queues\TestQueue;

class PublisherFactory
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
    
    public function getTestQueuePublisher(): Publisher
    {
        return new Publisher(
            $this->connection,
            new TestQueue(),
            $this->directExchange
        );
    }
}